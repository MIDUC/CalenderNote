<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryRule extends Model
{
    protected $fillable = [
        'story_id',
        'name',
        'description',
        'trigger_type',
        'trigger_conditions',
        'required_count',
        'target_character_id',
        'relationship_value_change',
        'relationship_type_change',
        'relationship_threshold',
        'has_penalty',
        'penalty_effects',
        'penalty_due_days',
        'is_cumulative',
        'reset_on_complete',
        'is_active',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'trigger_conditions' => 'array',
            'required_count' => 'integer',
            'relationship_value_change' => 'integer',
            'relationship_threshold' => 'integer',
            'has_penalty' => 'boolean',
            'penalty_effects' => 'array',
            'penalty_due_days' => 'integer',
            'is_cumulative' => 'boolean',
            'reset_on_complete' => 'boolean',
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function targetCharacter()
    {
        return $this->belongsTo(StoryCharacter::class, 'target_character_id');
    }

    public function userProgress()
    {
        return $this->hasMany(UserStoryRuleProgress::class, 'story_rule_id');
    }
}

