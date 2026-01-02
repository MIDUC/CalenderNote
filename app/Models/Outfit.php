<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outfit extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'description',
        'category',
        'icon',
        'sprite_url',
        'sprite_config',
        'price',
        'price_type',
        'level_required',
        'stats_bonus',
        'effects',
        'is_premium',
        'is_limited',
        'limited_until',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'sprite_config' => 'array',
            'stats_bonus' => 'array',
            'effects' => 'array',
            'price' => 'integer',
            'price_type' => 'integer',
            'level_required' => 'integer',
            'is_premium' => 'boolean',
            'is_limited' => 'boolean',
            'limited_until' => 'datetime',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_outfits')
            ->withPivot('is_equipped', 'purchased_at')
            ->withTimestamps();
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function isEquippedBy(User $user): bool
    {
        return $user->current_outfit_id === $this->id;
    }
}
