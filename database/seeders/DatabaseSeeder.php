<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => \Illuminate\Support\Facades\Hash::make('admin'),
                'role' => 'admin',
            ]
        );
        
        // Update password if admin exists
        if ($admin->wasRecentlyCreated === false) {
            $admin->password = \Illuminate\Support\Facades\Hash::make('admin');
            $admin->save();
        }

        // Create a test user first to ensure it exists
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'user',
            ]
        );

        // Create additional users
        User::factory(10)->create();

        // Seed in order of dependencies
        $this->call([
            LevelNameSeeder::class,
            ItemSeeder::class,
            DailyLoginRewardSeeder::class,
            GameRateSeeder::class,
            MonsterSeeder::class,
            ExerciseTypeSeeder::class,
            StorySeeder::class,
            StoryCharacterSeeder::class,
            CharacterQuestSeeder::class,
            ConfigSeeder::class,
            TransactionTypeSeeder::class,
            WalletSeeder::class,
            TransactionSeeder::class,
            RecurringTransactionSeeder::class,
            ScheduleSeeder::class,
            ScheduleShareSeeder::class,
            TaskSeeder::class,
            NoteSeeder::class,
        ]);
    }
}
