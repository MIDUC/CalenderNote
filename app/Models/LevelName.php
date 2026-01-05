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

    /**
     * Get level name with tier for a specific level
     * Example: Level 31 -> "Nguyên Anh Kỳ Tầng 1"
     */
    public static function getNameWithTierForLevel(int $level): ?string
    {
        $levelName = static::where('level_min', '<=', $level)
            ->where('level_max', '>=', $level)
            ->first();
        
        if (!$levelName) {
            return null;
        }
        
        // Calculate tier: tier = level - level_min + 1
        $tier = $level - $levelName->level_min + 1;
        
        return $levelName->name . ' Tầng ' . $tier;
    }
}
