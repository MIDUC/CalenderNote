<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
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
            // Income types
            $incomeTypes = [
                ['name' => 'Lương', 'icon' => '💰', 'color' => '#10b981'],
                ['name' => 'Thưởng', 'icon' => '🎁', 'color' => '#3b82f6'],
                ['name' => 'Đầu tư', 'icon' => '📈', 'color' => '#8b5cf6'],
                ['name' => 'Kinh doanh', 'icon' => '💼', 'color' => '#f59e0b'],
            ];

            foreach ($incomeTypes as $type) {
                \App\Models\TransactionType::create([
                    'user_id' => $user->id,
                    'name' => $type['name'],
                    'type' => 'income',
                    'description' => 'Loại thu nhập: ' . $type['name'],
                    'color' => $type['color'],
                    'icon' => $type['icon'],
                    'is_active' => true,
                ]);
            }

            // Expense types
            $expenseTypes = [
                ['name' => 'Ăn uống', 'icon' => '🍔', 'color' => '#ef4444'],
                ['name' => 'Mua sắm', 'icon' => '🛒', 'color' => '#ec4899'],
                ['name' => 'Giao thông', 'icon' => '🚗', 'color' => '#06b6d4'],
                ['name' => 'Giải trí', 'icon' => '🎬', 'color' => '#a855f7'],
                ['name' => 'Y tế', 'icon' => '💊', 'color' => '#14b8a6'],
                ['name' => 'Giáo dục', 'icon' => '📚', 'color' => '#6366f1'],
                ['name' => 'Nhà ở', 'icon' => '🏠', 'color' => '#f97316'],
                ['name' => 'Điện nước', 'icon' => '⚡', 'color' => '#eab308'],
            ];

            foreach ($expenseTypes as $type) {
                \App\Models\TransactionType::create([
                    'user_id' => $user->id,
                    'name' => $type['name'],
                    'type' => 'expense',
                    'description' => 'Loại chi tiêu: ' . $type['name'],
                    'color' => $type['color'],
                    'icon' => $type['icon'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
