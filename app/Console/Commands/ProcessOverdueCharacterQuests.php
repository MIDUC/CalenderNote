<?php

namespace App\Console\Commands;

use App\Models\UserCharacterQuest;
use App\Models\UserCharacterRelationship;
use App\Services\GamificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessOverdueCharacterQuests extends Command
{
    protected $signature = 'character-quests:process-overdue';
    protected $description = 'Process overdue character quests: mark as failed and apply heavy penalties for enemy quests';

    public function handle()
    {
        $today = Carbon::today();
        
        // Find all in-progress quests that are overdue
        $overdueQuests = UserCharacterQuest::where('status', 'in_progress')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->with('characterQuest.character', 'user')
            ->get();
        
        $this->info("Found {$overdueQuests->count()} overdue character quests to process.");
        
        $processedCount = 0;
        
        foreach ($overdueQuests as $userQuest) {
            try {
                // Check if character is enemy
                $relationship = UserCharacterRelationship::where('user_id', $userQuest->user_id)
                    ->where('character_id', $userQuest->characterQuest->character_id)
                    ->first();
                
                $isEnemy = $relationship && $relationship->relationship_type === 'enemy';
                
                // Mark as failed
                $userQuest->status = 'failed';
                $userQuest->failed_at = now();
                $userQuest->save();
                
                // Apply penalties (heavier for enemy quests)
                if ($isEnemy) {
                    // Very heavy penalty for enemy quests
                    $xpPenalty = $userQuest->xp_penalty ?: 250;
                    $currencyPenalty = $userQuest->currency_penalty ?: 300;
                    
                    GamificationService::subtractXP($userQuest->user, $xpPenalty);
                    if ($userQuest->user->currency >= $currencyPenalty) {
                        $userQuest->user->currency -= $currencyPenalty;
                        $userQuest->user->save();
                    }
                    
                    // Make relationship worse
                    if ($relationship) {
                        $relationship->relationship_value = max(-100, $relationship->relationship_value - 20);
                        $relationship->save();
                    }
                    
                    Log::warning('Overdue enemy quest processed with heavy penalty', [
                        'user_quest_id' => $userQuest->id,
                        'user_id' => $userQuest->user_id,
                        'character_id' => $userQuest->characterQuest->character_id,
                        'xp_penalty' => $xpPenalty,
                        'currency_penalty' => $currencyPenalty,
                    ]);
                } else {
                    // Normal penalty for non-enemy quests
                    $xpPenalty = $userQuest->xp_penalty ?: 50;
                    $currencyPenalty = $userQuest->currency_penalty ?: 100;
                    
                    GamificationService::subtractXP($userQuest->user, $xpPenalty);
                    if ($userQuest->user->currency >= $currencyPenalty) {
                        $userQuest->user->currency -= $currencyPenalty;
                        $userQuest->user->save();
                    }
                }
                
                $processedCount++;
            } catch (\Exception $e) {
                Log::error('Error processing overdue character quest', [
                    'user_quest_id' => $userQuest->id,
                    'error' => $e->getMessage(),
                ]);
                
                $this->error("Failed to process quest ID {$userQuest->id}: {$e->getMessage()}");
            }
        }
        
        $this->info("Successfully processed {$processedCount} overdue character quests.");
        
        return Command::SUCCESS;
    }
}

