<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryCharacter extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_id',
        'name',
        'name_en',
        'description',
        'avatar',
        'role',
        'chapter_appearance',
        'attributes',
        'dialogue',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'dialogue' => 'array',
            'chapter_appearance' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function story()
    {
        return $this->belongsTo(Story::class);
    }

    public function quests()
    {
        return $this->hasMany(CharacterQuest::class, 'character_id');
    }

    public function userRelationships()
    {
        return $this->hasMany(UserCharacterRelationship::class, 'character_id');
    }
}

