<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterQuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'character_id',
        'title',
        'description',
        'type',
        'requirements',
        'target_count',
        'xp_reward',
        'currency_reward',
        'item_reward_id',
        'item_reward_quantity',
        'level_required',
        'relationship_effects',
        'relationship_value_change',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'target_count' => 'integer',
            'xp_reward' => 'integer',
            'currency_reward' => 'integer',
            'item_reward_quantity' => 'integer',
            'level_required' => 'integer',
            'relationship_effects' => 'array',
            'relationship_value_change' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function character()
    {
        return $this->belongsTo(StoryCharacter::class, 'character_id');
    }

    public function itemReward()
    {
        return $this->belongsTo(Item::class, 'item_reward_id');
    }

    public function userQuests()
    {
        return $this->hasMany(UserCharacterQuest::class, 'character_quest_id');
    }
}

