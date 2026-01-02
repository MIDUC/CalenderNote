<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_type_id',
        'target_amount',
        'frequency',
        'days_of_week',
        'start_date',
        'end_date',
        'is_active',
        'current_streak',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'days_of_week' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'current_streak' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exerciseType()
    {
        return $this->belongsTo(ExerciseType::class);
    }
}
