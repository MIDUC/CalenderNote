<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
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
            $transactionTypes = \App\Models\TransactionType::where('user_id', $user->id)->get();
            
            if ($wallets->isEmpty() || $transactionTypes->isEmpty()) {
                continue;
            }

            // Create 50-100 transactions per user
            $count = fake()->numberBetween(50, 100);
            
            for ($i = 0; $i < $count; $i++) {
                $wallet = $wallets->random();
                $transactionType = $transactionTypes->random();
                $amount = fake()->randomFloat(2, 10000, 5000000);
                
                // Calculate balance
                $oldBalance = $wallet->balance;
                $newBalance = $transactionType->type === 'income' 
                    ? $oldBalance + $amount 
                    : $oldBalance - $amount;
                
                \App\Models\Transaction::create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'transaction_type_id' => $transactionType->id,
                    'description' => fake()->sentence(),
                    'amount' => $amount,
                    'date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
                    'recurring_id' => null,
                    'old_balance' => $oldBalance,
                    'new_balance' => $newBalance,
                ]);

                // Update wallet balance
                $wallet->balance = $newBalance;
                $wallet->save();
            }
        }
    }
}
