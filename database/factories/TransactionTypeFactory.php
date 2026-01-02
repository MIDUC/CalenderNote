<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionType>
 */
class TransactionTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['income', 'expense'];
        $type = fake()->randomElement($types);
        
        $incomeNames = ['Lương', 'Thưởng', 'Đầu tư', 'Kinh doanh', 'Quà tặng', 'Khác'];
        $expenseNames = ['Ăn uống', 'Mua sắm', 'Giao thông', 'Giải trí', 'Y tế', 'Giáo dục', 'Nhà ở', 'Điện nước', 'Khác'];
        
        $names = $type === 'income' ? $incomeNames : $expenseNames;
        
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => fake()->randomElement($names),
            'type' => $type,
            'description' => fake()->optional()->sentence(),
            'parent_id' => null,
            'color' => fake()->hexColor(),
            'icon' => fake()->randomElement(['💰', '🍔', '🚗', '🎬', '💊', '📚', '🏠', '⚡', '🎁', '💼']),
            'is_active' => true,
        ];
    }
}
