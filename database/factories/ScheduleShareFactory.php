<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduleShare>
 */
class ScheduleShareFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'schedule_id' => \App\Models\Schedule::factory(),
            'owner_id' => \App\Models\User::factory(),
            'shared_with_user_id' => \App\Models\User::factory(),
            'can_view' => true,
            'can_comment' => fake()->boolean(50),
            'can_edit' => fake()->boolean(30),
        ];
    }
}
