<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'type',
        'requirements',
        'xp_reward',
        'currency_reward',
        'rarity',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'xp_reward' => 'integer',
            'currency_reward' => 'integer',
            'rarity' => 'integer',
            'order' => 'integer',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('progress', 'is_unlocked', 'unlocked_at')
            ->withTimestamps();
    }
}
