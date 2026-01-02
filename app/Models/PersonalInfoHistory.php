<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInfoHistory extends Model
{
    use HasFactory;

    protected $table = 'personal_info_history';

    protected $fillable = [
        'user_id',
        'age',
        'weight',
        'height',
        'gender',
        'bmi',
        'month_year',
        'goals',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'bmi' => 'decimal:1',
            'goals' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
