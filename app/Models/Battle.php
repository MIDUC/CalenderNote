<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    protected $fillable = [
        'user_id',
        'monster_id',
        'status',
        'user_hp',
        'monster_hp',
        'turns',
        'battle_log',
        'xp_gained',
        'currency_gained',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'battle_log' => 'array',
            'user_hp' => 'integer',
            'monster_hp' => 'integer',
            'turns' => 'integer',
            'xp_gained' => 'integer',
            'currency_gained' => 'integer',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function monster()
    {
        return $this->belongsTo(Monster::class);
    }
}
