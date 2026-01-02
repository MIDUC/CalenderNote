<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOutfit extends Model
{
    protected $table = 'user_outfits';

    protected $fillable = [
        'user_id',
        'outfit_id',
        'is_equipped',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'is_equipped' => 'boolean',
            'purchased_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function outfit()
    {
        return $this->belongsTo(Outfit::class);
    }
}
