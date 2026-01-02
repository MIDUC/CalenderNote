<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    protected $fillable = [
        'user_id',
        'achievement_id',
        'progress',
        'is_unlocked',
        'unlocked_at',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'is_unlocked' => 'boolean',
            'unlocked_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }
}
