<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCharacterRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'character_id',
        'relationship_type',
        'relationship_value',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'relationship_value' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function character()
    {
        return $this->belongsTo(StoryCharacter::class, 'character_id');
    }
}

