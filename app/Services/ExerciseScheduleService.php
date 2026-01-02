<?php

namespace App\Services;

use App\Models\User;
use App\Models\ExerciseType;
use App\Models\ExerciseSchedule;
use Carbon\Carbon;

class ExerciseScheduleService
{
    /**
     * Generate exercise schedules based on user's personal info and goals
     */
    public static function generateSchedulesFromPersonalInfo(User $user, array $goals = []): array
    {
        $schedules = [];
        $bmi = self::calculateBMI($user->weight, $user->height);
        
        if (!$bmi) {
            return $schedules;
        }

        $today = Carbon::today();
        $startDate = $today;
        $endDate = $today->copy()->endOfMonth(); // Schedule for current month

        // Determine goals based on BMI
        if (empty($goals)) {
            if ($bmi < 18.5) {
                // Underweight - focus on strength building
                $goals = ['gain_muscle' => true, 'improve_strength' => true];
            } elseif ($bmi >= 25) {
                // Overweight - focus on weight loss and cardio
                $goals = ['lose_weight' => true, 'improve_cardio' => true];
            } else {
                // Normal weight - maintain and improve overall fitness
                $goals = ['maintain' => true, 'improve_fitness' => true];
            }
        }

        // Get exercise types
        $exerciseTypes = ExerciseType::where('is_active', true)->get();

        foreach ($exerciseTypes as $exerciseType) {
            $targetAmount = self::calculateTargetAmount($exerciseType, $bmi, $goals, $user);
            
            if ($targetAmount > 0) {
                // Check if schedule already exists for this exercise type this month
                $existingSchedule = ExerciseSchedule::where('user_id', $user->id)
                    ->where('exercise_type_id', $exerciseType->id)
                    ->where('start_date', '<=', $endDate)
                    ->where(function($query) use ($startDate) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', $startDate);
                    })
                    ->where('is_active', true)
                    ->first();

                if (!$existingSchedule) {
                    $schedule = ExerciseSchedule::create([
                        'user_id' => $user->id,
                        'exercise_type_id' => $exerciseType->id,
                        'target_amount' => $targetAmount,
                        'frequency' => 'daily',
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                    ]);
                    
                    $schedules[] = $schedule->load('exerciseType');
                }
            }
        }

        return $schedules;
    }

    /**
     * Calculate target amount based on BMI, goals, and user level
     */
    private static function calculateTargetAmount(ExerciseType $exerciseType, float $bmi, array $goals, User $user): float
    {
        $targets = $exerciseType->targets ?? [];
        $baseTarget = $targets['level_1'] ?? 10;
        
        // Adjust based on user level
        $levelMultiplier = 1 + (($user->level - 1) * 0.1); // 10% increase per level
        $targetAmount = $baseTarget * $levelMultiplier;

        // Adjust based on BMI and goals
        $exerciseName = mb_strtolower($exerciseType->name);
        
        if (isset($goals['lose_weight']) && $goals['lose_weight']) {
            // Focus on cardio exercises for weight loss
            if (strpos($exerciseName, 'chạy') !== false || strpos($exerciseName, 'running') !== false) {
                $targetAmount *= 1.5; // Increase cardio
            }
        } elseif (isset($goals['gain_muscle']) && $goals['gain_muscle']) {
            // Focus on strength exercises
            if (strpos($exerciseName, 'chống') !== false || 
                strpos($exerciseName, 'gập') !== false || 
                strpos($exerciseName, 'squat') !== false ||
                strpos($exerciseName, 'push') !== false) {
                $targetAmount *= 1.3; // Increase strength exercises
            }
        }

        // Adjust based on BMI
        if ($bmi >= 25) {
            // Overweight - increase all exercises by 20%
            $targetAmount *= 1.2;
        } elseif ($bmi < 18.5) {
            // Underweight - moderate increase
            $targetAmount *= 1.1;
        }

        return round($targetAmount, 1);
    }

    /**
     * Calculate BMI
     */
    private static function calculateBMI($weight, $height)
    {
        if (!$weight || !$height || $height <= 0) {
            return null;
        }
        
        $heightInMeters = $height / 100;
        return round($weight / ($heightInMeters * $heightInMeters), 1);
    }
}

