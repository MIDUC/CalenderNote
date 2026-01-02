<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 1000, 5000000);
        
        return [
            'user_id' => \App\Models\User::factory(),
            'wallet_id' => \App\Models\Wallet::factory(),
            'transaction_type_id' => \App\Models\TransactionType::factory(),
            'description' => fake()->sentence(),
            'amount' => $amount,
            'date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'recurring_id' => null,
            'old_balance' => fake()->randomFloat(2, 0, 10000000),
            'new_balance' => fake()->randomFloat(2, 0, 10000000),
        ];
    }
}
