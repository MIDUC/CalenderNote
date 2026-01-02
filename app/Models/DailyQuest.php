<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyQuest extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'target_count',
        'xp_reward',
        'currency_reward',
        'date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'target_count' => 'integer',
            'xp_reward' => 'integer',
            'currency_reward' => 'integer',
            'date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_daily_quests')
            ->withPivot('progress', 'is_completed', 'is_claimed', 'completed_at')
            ->withTimestamps();
    }
}
