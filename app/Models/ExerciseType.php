<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'icon',
        'description',
        'base_xp',
        'base_currency',
        'targets',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'targets' => 'array',
            'base_xp' => 'integer',
            'base_currency' => 'integer',
            'order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function userExercises()
    {
        return $this->hasMany(UserExercise::class);
    }

    public function schedules()
    {
        return $this->hasMany(ExerciseSchedule::class);
    }
}
