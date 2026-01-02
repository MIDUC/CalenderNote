<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameRate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'value',
        'type',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:4',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Get rate value by key
     */
    public static function getRate(string $key, float $default = 0.0): float
    {
        $rate = static::where('key', $key)
            ->where('is_active', true)
            ->first();
        
        return $rate ? (float) $rate->value : $default;
    }

    /**
     * Get multiple rates at once
     */
    public static function getRates(array $keys): array
    {
        $rates = static::whereIn('key', $keys)
            ->where('is_active', true)
            ->pluck('value', 'key')
            ->toArray();
        
        return $rates;
    }
}

