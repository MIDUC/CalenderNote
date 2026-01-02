<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exercise_type_id',
        'amount',
        'exercise_date',
        'notes',
        'xp_gained',
        'currency_gained',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'exercise_date' => 'date',
            'xp_gained' => 'integer',
            'currency_gained' => 'integer',
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
