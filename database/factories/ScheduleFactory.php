<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $repeatType = fake()->randomElement(['none', 'daily', 'weekly', 'monthly', 'yearly']);
        $hasFixedTime = fake()->boolean(70);
        $startDate = fake()->dateTimeBetween('-1 month', '+2 months');
        
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'type' => fake()->randomElement(['event', 'task', 'reminder']),
            'repeat_type' => $repeatType,
            'days_of_week' => $repeatType === 'weekly' ? json_encode([1, 3, 5]) : null,
            'days_of_month' => $repeatType === 'monthly' ? json_encode([1, 15]) : null,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => fake()->optional()->dateTimeBetween($startDate, '+6 months')->format('Y-m-d'),
            'has_fixed_time' => $hasFixedTime,
            'fixed_time' => $hasFixedTime ? fake()->time() : null,
            'notify_before_minutes' => fake()->optional()->randomElement([5, 10, 15, 30, 60]),
            'notify_times' => fake()->optional()->randomElement([['08:00'], ['12:00'], ['18:00']]),
            'is_active' => true,
            'shareable' => fake()->boolean(30),
            'require_checkin' => fake()->boolean(20),
        ];
    }
}
