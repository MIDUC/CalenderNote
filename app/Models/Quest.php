<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    protected $fillable = [
        'npc_id',
        'title',
        'description',
        'type',
        'requirements',
        'target_count',
        'current_count',
        'xp_reward',
        'currency_reward',
        'item_reward_id',
        'item_reward_quantity',
        'level_required',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'target_count' => 'integer',
            'current_count' => 'integer',
            'xp_reward' => 'integer',
            'currency_reward' => 'integer',
            'item_reward_quantity' => 'integer',
            'level_required' => 'integer',
        ];
    }

    public function npc()
    {
        return $this->belongsTo(NPC::class, 'npc_id');
    }

    public function itemReward()
    {
        return $this->belongsTo(Item::class, 'item_reward_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_quests')
            ->withPivot('progress', 'status', 'completed_at')
            ->withTimestamps();
    }
}
