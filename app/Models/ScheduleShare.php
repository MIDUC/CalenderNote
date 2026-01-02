<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'owner_id',
        'shared_with_user_id',
        'can_view',
        'can_comment',
        'can_edit',
        'created_at',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function sharedWith()
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }
}
