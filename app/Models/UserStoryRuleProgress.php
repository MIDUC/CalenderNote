<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStoryRuleProgress extends Model
{
    protected $table = 'user_story_rule_progress';

    protected $fillable = [
        'user_id',
        'story_rule_id',
        'current_count',
        'relationship_value',
        'penalty_applied',
        'last_triggered_at',
        'penalty_due_at',
    ];

    protected function casts(): array
    {
        return [
            'current_count' => 'integer',
            'relationship_value' => 'integer',
            'penalty_applied' => 'boolean',
            'last_triggered_at' => 'datetime',
            'penalty_due_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function storyRule()
    {
        return $this->belongsTo(StoryRule::class);
    }
}

