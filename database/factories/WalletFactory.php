<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $walletNames = ['Ví chính', 'Ví tiết kiệm', 'Ví đầu tư', 'Ví chi tiêu', 'Ví khẩn cấp'];
        
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => fake()->randomElement($walletNames),
            'transaction_type_id' => null,
            'balance' => fake()->randomFloat(2, 0, 10000000),
            'is_active' => true,
        ];
    }
}
