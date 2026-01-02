<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleShareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = \App\Models\Schedule::where('shareable', true)->get();
        $users = \App\Models\User::all();
        
        if ($schedules->isEmpty() || $users->count() < 2) {
            return;
        }

        foreach ($schedules as $schedule) {
            // Share with 1-3 random users (excluding owner)
            $sharedWithCount = fake()->numberBetween(1, min(3, $users->count() - 1));
            $sharedWithUsers = $users->where('id', '!=', $schedule->user_id)->random($sharedWithCount);
            
            foreach ($sharedWithUsers as $sharedUser) {
                \App\Models\ScheduleShare::firstOrCreate(
                    [
                        'schedule_id' => $schedule->id,
                        'shared_with_user_id' => $sharedUser->id,
                    ],
                    [
                        'owner_id' => $schedule->user_id,
                        'can_view' => true,
                        'can_comment' => fake()->boolean(50),
                        'can_edit' => fake()->boolean(30),
                    ]
                );
            }
        }
    }
}
