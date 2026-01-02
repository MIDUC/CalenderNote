<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLoginReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'xp_reward',
        'currency_reward',
        'item_reward_id',
        'item_reward_quantity',
        'description',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'day' => 'integer',
            'xp_reward' => 'integer',
            'currency_reward' => 'integer',
            'item_reward_quantity' => 'integer',
            'order' => 'integer',
        ];
    }

    public function itemReward()
    {
        return $this->belongsTo(Item::class, 'item_reward_id');
    }
}
