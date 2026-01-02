<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'price',
        'sell_price',
        'effects',
        'icon',
        'rarity',
    ];

    protected function casts(): array
    {
        return [
            'effects' => 'array',
            'price' => 'integer',
            'sell_price' => 'integer',
            'rarity' => 'integer',
        ];
    }

    /**
     * Users who own this item
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
