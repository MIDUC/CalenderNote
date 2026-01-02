<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuest extends Model
{
    protected $fillable = [
        'user_id',
        'quest_id',
        'progress',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }
}
