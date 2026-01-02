<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        
        if ($users->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            // Create 20-40 schedules per user
            $count = fake()->numberBetween(20, 40);
            
            for ($i = 0; $i < $count; $i++) {
                $repeatType = fake()->randomElement(['none', 'daily', 'weekly', 'monthly', 'yearly']);
                $hasFixedTime = fake()->boolean(70);
                $startDate = fake()->dateTimeBetween('-1 month', '+3 months');
                $endDate = fake()->optional(0.4)->dateTimeBetween($startDate, '+6 months');
                
                \App\Models\Schedule::create([
                    'user_id' => $user->id,
                    'title' => fake()->sentence(3),
                    'description' => fake()->optional(0.7)->paragraph(),
                    'type' => fake()->randomElement(['event', 'task', 'reminder']),
                    'repeat_type' => $repeatType,
                    'days_of_week' => $repeatType === 'weekly' ? json_encode([1, 3, 5]) : null,
                    'days_of_month' => $repeatType === 'monthly' ? json_encode([1, 15]) : null,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
                    'has_fixed_time' => $hasFixedTime,
                    'fixed_time' => $hasFixedTime ? fake()->time() : null,
                    'notify_before_minutes' => fake()->optional(0.6)->randomElement([5, 10, 15, 30, 60]),
                    'notify_times' => ($notifyTimes = fake()->optional(0.3)->randomElement([['08:00'], ['12:00'], ['18:00']])) ? json_encode($notifyTimes) : null,
                    'is_active' => true,
                    'shareable' => fake()->boolean(30),
                    'require_checkin' => fake()->boolean(20),
                ]);
            }
        }
    }
}
