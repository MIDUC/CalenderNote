<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecurringTransaction>
 */
class RecurringTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $frequency = fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']);
        $startDate = fake()->dateTimeBetween('-6 months', 'now');
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
        
        return [
            'user_id' => \App\Models\User::factory(),
            'wallet_id' => \App\Models\Wallet::factory(),
            'transaction_type_id' => \App\Models\TransactionType::factory(),
            'amount' => fake()->randomFloat(2, 10000, 2000000),
            'description' => fake()->sentence(),
            'frequency' => $frequency,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => fake()->optional()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'last_executed' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'next_run' => $nextRun,
            'is_active' => true,
        ];
    }
}
