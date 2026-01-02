<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Monster extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'level',
        'hp',
        'max_hp',
        'attack',
        'defense',
        'xp_reward',
        'currency_reward',
        'drop_items',
        'rarity',
        'is_boss',
        'critical_rate',
        'critical_damage_multiplier',
        'dodge_rate',
        'drop_rate_multiplier',
        'rare_drop_rate',
        'encounter_rate',
        'status_effect_resistance',
    ];

    protected function casts(): array
    {
        return [
            'drop_items' => 'array',
            'level' => 'integer',
            'hp' => 'integer',
            'max_hp' => 'integer',
            'attack' => 'integer',
            'defense' => 'integer',
            'xp_reward' => 'integer',
            'currency_reward' => 'integer',
            'rarity' => 'integer',
            'is_boss' => 'boolean',
            'critical_rate' => 'decimal:4',
            'critical_damage_multiplier' => 'decimal:2',
            'dodge_rate' => 'decimal:4',
            'drop_rate_multiplier' => 'decimal:2',
            'rare_drop_rate' => 'decimal:4',
            'encounter_rate' => 'decimal:4',
            'status_effect_resistance' => 'decimal:4',
        ];
    }

    public function battles()
    {
        return $this->hasMany(Battle::class);
    }
}
