<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'schedule_id',
        'user_id',
        'task_date',
        'task_time',
        'status',
        'note',
        'checkin_photo_url',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Quan hệ: Task thuộc về một người dùng (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Quan hệ: Task thuộc về một lịch trình (Schedule)
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}