<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
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
            $schedules = \App\Models\Schedule::where('user_id', $user->id)->get();
            
            // Create 30-60 tasks per user
            $count = fake()->numberBetween(30, 60);
            
            for ($i = 0; $i < $count; $i++) {
                $status = fake()->randomElement(['pending', 'in_progress', 'completed', 'failed']);
                $taskDate = fake()->dateTimeBetween('-1 month', '+1 month');
                $schedule = $schedules->isNotEmpty() && fake()->boolean(60) ? $schedules->random() : null;
                
                // Only set completed_at if task date is in the past and status is completed
                $completedAt = null;
                if ($status === 'completed' && $taskDate <= new \DateTime('now')) {
                    $completedAt = fake()->dateTimeBetween($taskDate, 'now');
                }
                
                \App\Models\Task::create([
                    'schedule_id' => $schedule?->id,
                    'user_id' => $user->id,
                    'title' => fake()->sentence(3),
                    'description' => fake()->optional(0.6)->paragraph(),
                    'task_date' => $taskDate->format('Y-m-d'),
                    'task_time' => fake()->optional(0.7)->time(),
                    'status' => $status,
                    'note' => fake()->optional(0.4)->sentence(),
                    'checkin_photo_url' => $status === 'completed' ? fake()->optional(0.3)->imageUrl() : null,
                    'completed_at' => $completedAt ? $completedAt->format('Y-m-d H:i:s') : null,
                    'require_checkin' => fake()->boolean(30),
                    'fixed_time' => fake()->boolean(40),
                ]);
            }
        }
    }
}
