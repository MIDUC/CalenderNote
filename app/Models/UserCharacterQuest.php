<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCharacterQuest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'character_quest_id',
        'status',
        'progress',
        'accepted_at',
        'due_date',
        'completed_at',
        'failed_at',
        'xp_penalty',
        'currency_penalty',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'accepted_at' => 'date',
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
            'xp_penalty' => 'integer',
            'currency_penalty' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function characterQuest()
    {
        return $this->belongsTo(CharacterQuest::class, 'character_quest_id');
    }
}

