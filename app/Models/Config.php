<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    use HasFactory;

    protected $fillable = [
        'config_key',
        'config_value',
        'description',
        'updated_at',
    ];

    public $timestamps = false; // vì bảng chỉ có updated_at
}
