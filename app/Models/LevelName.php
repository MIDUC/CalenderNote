<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LevelName extends Model
{
    protected $fillable = [
        'level_min',
        'level_max',
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'level_min' => 'integer',
            'level_max' => 'integer',
        ];
    }

    /**
     * Get level name for a specific level
     */
    public static function getNameForLevel(int $level): ?string
    {
        return static::where('level_min', '<=', $level)
            ->where('level_max', '>=', $level)
            ->value('name');
    }
}
