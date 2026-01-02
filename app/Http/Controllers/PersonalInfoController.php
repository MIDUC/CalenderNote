<?php

namespace App\Http\Controllers;

use App\Models\PersonalInfoHistory;
use App\Services\ExerciseScheduleService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PersonalInfoController extends BaseController
{
    /**
     * Get user's personal information
     */
    public function show(Request $request)
    {
        $user = $request->user();
        
        return $this->sendResponse([
            'age' => $user->age,
            'weight' => $user->weight,
            'height' => $user->height,
            'gender' => $user->gender,
            'bmi' => $this->calculateBMI($user->weight, $user->height),
            'story_progress' => $user->story_progress ?? [],
            'current_month_record' => $this->getCurrentMonthRecord($user),
        ], 'Personal information retrieved successfully.');
    }

    /**
     * Get history of personal info updates
     */
    public function history(Request $request)
    {
        $user = $request->user();
        $history = $user->personalInfoHistory()
            ->orderBy('month_year', 'desc')
            ->limit(12) // Last 12 months
            ->get();
        
        return $this->sendResponse($history, 'Personal info history retrieved successfully.');
    }

    /**
     * Update user's personal information
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'age' => 'nullable|integer|min:1|max:150',
            'weight' => 'nullable|numeric|min:1|max:500',
            'height' => 'nullable|numeric|min:50|max:300',
            'gender' => 'nullable|string|in:male,female,other',
            'goals' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $currentMonthYear = Carbon::now()->format('Y-m');
        $bmi = $this->calculateBMI($validated['weight'] ?? $user->weight, $validated['height'] ?? $user->height);
        
        // Check if already updated this month
        $monthRecord = PersonalInfoHistory::where('user_id', $user->id)
            ->where('month_year', $currentMonthYear)
            ->first();
        
        $hasChanges = false;
        if ($monthRecord) {
            // Check if there are significant changes (weight change > 0.5kg or height change > 1cm)
            $weightChanged = isset($validated['weight']) && abs($validated['weight'] - ($monthRecord->weight ?? $user->weight)) > 0.5;
            $heightChanged = isset($validated['height']) && abs($validated['height'] - ($monthRecord->height ?? $user->height)) > 1;
            
            if ($weightChanged || $heightChanged) {
                $hasChanges = true;
            }
        } else {
            // First update this month
            $hasChanges = true;
        }
        
        // Update user info
        $user->fill($validated);
        $user->save();
        
        // Save to history if this is a new month or significant changes
        if ($hasChanges || !$monthRecord) {
            PersonalInfoHistory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'month_year' => $currentMonthYear,
                ],
                [
                    'age' => $user->age,
                    'weight' => $user->weight,
                    'height' => $user->height,
                    'gender' => $user->gender,
                    'bmi' => $bmi,
                    'goals' => $validated['goals'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]
            );
            
            // Generate exercise schedules based on new info
            $goals = $validated['goals'] ?? [];
            $schedules = ExerciseScheduleService::generateSchedulesFromPersonalInfo($user, $goals);
            
            return $this->sendResponse([
                'age' => $user->age,
                'weight' => $user->weight,
                'height' => $user->height,
                'gender' => $user->gender,
                'bmi' => $bmi,
                'schedules_created' => count($schedules),
                'schedules' => $schedules,
                'message' => count($schedules) > 0 
                    ? 'Đã cập nhật thông tin và tạo ' . count($schedules) . ' nhiệm vụ luyện tập mới!'
                    : 'Đã cập nhật thông tin thành công!',
            ], 'Personal information updated successfully.');
        }
        
        return $this->sendResponse([
            'age' => $user->age,
            'weight' => $user->weight,
            'height' => $user->height,
            'gender' => $user->gender,
            'bmi' => $bmi,
            'message' => 'Thông tin đã được cập nhật trong tháng này.',
        ], 'Personal information updated successfully.');
    }

    /**
     * Get current month record
     */
    private function getCurrentMonthRecord($user)
    {
        $currentMonthYear = Carbon::now()->format('Y-m');
        return PersonalInfoHistory::where('user_id', $user->id)
            ->where('month_year', $currentMonthYear)
            ->first();
    }

    /**
     * Calculate BMI (Body Mass Index)
     */
    private function calculateBMI($weight, $height)
    {
        if (!$weight || !$height || $height <= 0) {
            return null;
        }
        
        // BMI = weight (kg) / (height (m))^2
        $heightInMeters = $height / 100;
        return round($weight / ($heightInMeters * $heightInMeters), 1);
    }
}
