<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'description',
        'source',
        'genre',
        'cover_image',
        'total_chapters',
        'unlock_level',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_chapters' => 'integer',
            'unlock_level' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function characters()
    {
        return $this->hasMany(StoryCharacter::class);
    }
}

