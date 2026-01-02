<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecurringTransactionSeeder extends Seeder
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
            $wallets = \App\Models\Wallet::where('user_id', $user->id)->get();
            $transactionTypes = \App\Models\TransactionType::where('user_id', $user->id)->where('type', 'expense')->get();
            
            if ($wallets->isEmpty() || $transactionTypes->isEmpty()) {
                continue;
            }

            // Create 5-10 recurring transactions per user
            $count = fake()->numberBetween(5, 10);
            
            for ($i = 0; $i < $count; $i++) {
                $wallet = $wallets->random();
                $transactionType = $transactionTypes->random();
                $frequency = fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']);
                $startDate = fake()->dateTimeBetween('-3 months', 'now');
                $nextRun = clone $startDate;
                
                switch ($frequency) {
                    case 'daily':
                        $nextRun->modify('+1 day');
                        break;
                    case 'weekly':
                        $nextRun->modify('+1 week');
                        break;
                    case 'monthly':
                        $nextRun->modify('+1 month');
                        break;
                    case 'yearly':
                        $nextRun->modify('+1 year');
                        break;
                }
                
                $endDate = fake()->optional(0.3)->dateTimeBetween('now', '+1 year');
                $lastExecuted = fake()->optional(0.5)->dateTimeBetween($startDate, 'now');
                
                \App\Models\RecurringTransaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'transaction_type_id' => $transactionType->id,
                    'amount' => fake()->randomFloat(2, 50000, 2000000),
                    'description' => fake()->sentence(),
                    'frequency' => $frequency,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate ? $endDate->format('Y-m-d') : null,
                    'last_executed' => $lastExecuted ? $lastExecuted->format('Y-m-d H:i:s') : null,
                    'next_run' => $nextRun->format('Y-m-d H:i:s'),
                    'is_active' => true,
                ]);
            }
        }
    }
}
