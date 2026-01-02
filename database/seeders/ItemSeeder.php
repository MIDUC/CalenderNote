<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Rarity 1 - Thường (Common)
            [
                'name' => 'Bánh mì hồi phục',
                'description' => 'Hồi phục 50 HP khi chiến đấu',
                'type' => 'consumable',
                'price' => 10,
                'sell_price' => 5,
                'effects' => ['hp_restore' => 50],
                'icon' => '🍞',
                'rarity' => 1,
            ],
            [
                'name' => 'Linh thạch hạ phẩm',
                'description' => 'Linh thạch chất lượng thấp, dùng để tu luyện',
                'type' => 'material',
                'price' => 5,
                'sell_price' => 2,
                'effects' => ['exp_bonus' => 10],
                'icon' => '💎',
                'rarity' => 1,
            ],
            [
                'name' => 'Kiếm gỗ',
                'description' => 'Kiếm tập luyện bằng gỗ',
                'type' => 'equipment',
                'price' => 20,
                'sell_price' => 10,
                'effects' => ['slot' => 'weapon', 'attack' => 5],
                'icon' => '🗡️',
                'rarity' => 1,
            ],
            
            // Rarity 2 - Hiếm (Uncommon)
            [
                'name' => 'Thuốc hồi máu',
                'description' => 'Hồi phục 100 HP khi chiến đấu',
                'type' => 'consumable',
                'price' => 25,
                'sell_price' => 12,
                'effects' => ['hp_restore' => 100],
                'icon' => '🧪',
                'rarity' => 2,
            ],
            [
                'name' => 'Kiếm sắt',
                'description' => 'Tăng 10 điểm tấn công',
                'type' => 'equipment',
                'price' => 100,
                'sell_price' => 50,
                'effects' => ['slot' => 'weapon', 'attack' => 10],
                'icon' => '⚔️',
                'rarity' => 2,
            ],
            [
                'name' => 'Áo giáp da',
                'description' => 'Tăng 10 điểm phòng thủ và 20 Max HP',
                'type' => 'equipment',
                'price' => 100,
                'sell_price' => 50,
                'effects' => ['slot' => 'armor', 'defense' => 10, 'max_hp' => 20],
                'icon' => '🛡️',
                'rarity' => 2,
            ],
            [
                'name' => 'Linh thạch trung phẩm',
                'description' => 'Linh thạch chất lượng trung bình',
                'type' => 'material',
                'price' => 50,
                'sell_price' => 25,
                'effects' => ['exp_bonus' => 30],
                'icon' => '💠',
                'rarity' => 2,
            ],
            
            // Rarity 3 - Cực hiếm (Rare)
            [
                'name' => 'Nhẫn sức mạnh',
                'description' => 'Tăng 5 điểm tấn công và phòng thủ',
                'type' => 'equipment',
                'price' => 150,
                'sell_price' => 75,
                'effects' => ['slot' => 'accessory', 'attack' => 5, 'defense' => 5],
                'icon' => '💍',
                'rarity' => 3,
            ],
            [
                'name' => 'Viên ngọc kinh nghiệm',
                'description' => 'Tăng 50 XP ngay lập tức',
                'type' => 'consumable',
                'price' => 50,
                'sell_price' => 25,
                'effects' => ['exp_bonus' => 50],
                'icon' => '💎',
                'rarity' => 3,
            ],
            [
                'name' => 'Kiếm Linh',
                'description' => 'Kiếm được rèn từ linh khí, tăng 20 tấn công',
                'type' => 'equipment',
                'price' => 300,
                'sell_price' => 150,
                'effects' => ['slot' => 'weapon', 'attack' => 20],
                'icon' => '⚔️',
                'rarity' => 3,
            ],
            [
                'name' => 'Linh thạch thượng phẩm',
                'description' => 'Linh thạch chất lượng cao',
                'type' => 'material',
                'price' => 200,
                'sell_price' => 100,
                'effects' => ['exp_bonus' => 100],
                'icon' => '💠',
                'rarity' => 3,
            ],
            
            // Rarity 4 - Thần (Epic)
            [
                'name' => 'Kiếm Thần',
                'description' => 'Kiếm thần thoại, tăng 50 tấn công và 20 phòng thủ',
                'type' => 'equipment',
                'price' => 1000,
                'sell_price' => 500,
                'effects' => ['slot' => 'weapon', 'attack' => 50, 'defense' => 20],
                'icon' => '🗡️',
                'rarity' => 4,
            ],
            [
                'name' => 'Áo Giáp Thần',
                'description' => 'Áo giáp thần thoại, tăng 30 phòng thủ và 100 Max HP',
                'type' => 'equipment',
                'price' => 1000,
                'sell_price' => 500,
                'effects' => ['slot' => 'armor', 'defense' => 30, 'max_hp' => 100],
                'icon' => '🛡️',
                'rarity' => 4,
            ],
            [
                'name' => 'Đan Dược Thần',
                'description' => 'Đan dược thần kỳ, hồi phục toàn bộ HP và tăng 200 XP',
                'type' => 'consumable',
                'price' => 500,
                'sell_price' => 250,
                'effects' => ['hp_restore' => 9999, 'exp_bonus' => 200],
                'icon' => '🧪',
                'rarity' => 4,
            ],
            
            // Rarity 5 - Truyền thuyết (Legendary)
            [
                'name' => 'Kiếm Truyền Thuyết',
                'description' => 'Kiếm truyền thuyết, tăng 100 tấn công, 50 phòng thủ và 200 Max HP',
                'type' => 'equipment',
                'price' => 5000,
                'sell_price' => 2500,
                'effects' => ['slot' => 'weapon', 'attack' => 100, 'defense' => 50, 'max_hp' => 200],
                'icon' => '⚔️',
                'rarity' => 5,
            ],
            [
                'name' => 'Linh Thạch Cực Phẩm',
                'description' => 'Linh thạch chất lượng tối cao, tăng 500 XP',
                'type' => 'material',
                'price' => 2000,
                'sell_price' => 1000,
                'effects' => ['exp_bonus' => 500],
                'icon' => '💎',
                'rarity' => 5,
            ],
            [
                'name' => 'Thần Dược Bất Tử',
                'description' => 'Thần dược có thể hồi sinh, hồi phục toàn bộ và tăng vĩnh viễn 50 tấn công',
                'type' => 'special',
                'price' => 10000,
                'sell_price' => 5000,
                'effects' => ['hp_restore' => 99999, 'attack' => 50, 'permanent' => true],
                'icon' => '🌟',
                'rarity' => 5,
            ],
        ];

        foreach ($items as $item) {
            $existing = Item::where('name', $item['name'])->first();
            if ($existing) {
                $existing->update($item);
            } else {
                Item::create($item);
            }
        }
    }
}
