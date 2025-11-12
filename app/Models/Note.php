<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'status',
        'note_date',
        'created_at',
        'updated_at',
    ];

    /**
     * User sở hữu note
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope để lấy note theo trạng thái
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }
}