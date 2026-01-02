<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NPC extends Model
{
    protected $table = 'npcs';
    
    protected $fillable = [
        'name',
        'description',
        'icon',
        'type',
        'dialogue',
        'level_required',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'dialogue' => 'array',
            'level_required' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function quests()
    {
        return $this->hasMany(Quest::class, 'npc_id');
    }
}
