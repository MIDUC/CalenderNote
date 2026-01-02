<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NoteSeeder extends Seeder
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
            // Create 20-40 notes per user
            $count = fake()->numberBetween(20, 40);
            
            for ($i = 0; $i < $count; $i++) {
                \App\Models\Note::create([
                    'user_id' => $user->id,
                    'title' => fake()->sentence(3),
                    'content' => fake()->paragraph(),
                    'status' => fake()->randomElement(['new', 'done']),
                    'note_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                ]);
            }
        }
    }
}
