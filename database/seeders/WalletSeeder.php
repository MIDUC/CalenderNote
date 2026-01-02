<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
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
            $walletNames = ['Ví chính', 'Ví tiết kiệm', 'Ví đầu tư'];
            
            foreach ($walletNames as $name) {
                \App\Models\Wallet::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'transaction_type_id' => null,
                    'balance' => fake()->randomFloat(2, 1000000, 50000000),
                    'is_active' => true,
                ]);
            }
        }
    }
}
