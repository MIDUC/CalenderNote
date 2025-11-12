<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'repeat_type',
        'days_of_week',
        'days_of_month',
        'start_date',
        'end_date',
        'has_fixed_time',
        'fixed_time',
        'notify_before_minutes',
        'notify_times',
        'is_active',
        'shareable',
        'require_checkin',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function shares()
    {
        return $this->hasMany(ScheduleShare::class);
    }
}