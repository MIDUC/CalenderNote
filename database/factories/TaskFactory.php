<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'in_progress', 'completed', 'failed']);
        $taskDate = fake()->dateTimeBetween('-1 month', '+1 month');
        
        return [
            'schedule_id' => fake()->optional()->randomElement([\App\Models\Schedule::factory(), null]),
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'task_date' => $taskDate->format('Y-m-d'),
            'task_time' => fake()->optional()->time(),
            'status' => $status,
            'note' => fake()->optional()->sentence(),
            'checkin_photo_url' => $status === 'completed' ? fake()->optional()->imageUrl() : null,
            'completed_at' => $status === 'completed' ? fake()->dateTimeBetween($taskDate, 'now') : null,
            'require_checkin' => fake()->boolean(30),
            'fixed_time' => fake()->boolean(40),
        ];
    }
}
