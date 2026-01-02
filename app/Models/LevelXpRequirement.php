<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelXpRequirement extends Model
{
    protected $fillable = [
        'level',
        'xp_required',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'xp_required' => 'integer',
        ];
    }

    /**
     * Get XP required for a specific level
     */
    public static function getXPForLevel(int $level): int
    {
        $requirement = static::where('level', $level)->first();
        
        if ($requirement) {
            return $requirement->xp_required;
        }
        
        // Fallback to default formula if not found
        $baseXP = 100;
        return (int) ($baseXP * pow($level, 1.5));
    }

    /**
     * Get total XP needed to reach a level (sum of all previous levels)
     */
    public static function getTotalXPForLevel(int $level): int
    {
        if ($level <= 1) return 0;
        
        $total = 0;
        for ($i = 1; $i < $level; $i++) {
            $total += self::getXPForLevel($i);
        }
        
        return $total;
    }
}

