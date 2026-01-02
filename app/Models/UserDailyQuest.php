<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDailyQuest extends Model
{
    protected $fillable = [
        'user_id',
        'daily_quest_id',
        'progress',
        'is_completed',
        'is_claimed',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'is_completed' => 'boolean',
            'is_claimed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dailyQuest()
    {
        return $this->belongsTo(DailyQuest::class);
    }
}
