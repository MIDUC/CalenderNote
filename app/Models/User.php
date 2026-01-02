<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'exp',
        'level',
        'currency',
        'level_name',
        'hp',
        'max_hp',
        'attack',
        'defense',
        'current_streak',
        'longest_streak',
        'last_task_date',
        'last_login_date',
        'login_streak',
        'last_reward_claimed_date',
        'current_reward_day',
        'age',
        'weight',
        'height',
        'gender',
        'story_progress',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'exp' => 'integer',
            'level' => 'integer',
            'currency' => 'integer',
            'hp' => 'integer',
            'max_hp' => 'integer',
            'attack' => 'integer',
            'defense' => 'integer',
            'current_streak' => 'integer',
            'longest_streak' => 'integer',
            'last_task_date' => 'date',
            'last_login_date' => 'date',
            'login_streak' => 'integer',
            'last_reward_claimed_date' => 'date',
            'current_reward_day' => 'integer',
            'age' => 'integer',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'story_progress' => 'array',
        ];
    }

    /**
     * Get user's items
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'user_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    /**
     * Get user's quests
     */
    public function quests()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('user_quests') || !\Illuminate\Support\Facades\Schema::hasTable('quests')) {
            // Return empty relationship if tables don't exist
            return $this->belongsToMany(Quest::class, 'user_quests')
                ->whereRaw('1 = 0'); // Always return empty
        }
        
        return $this->belongsToMany(Quest::class, 'user_quests')
            ->withPivot('progress', 'status', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Get user's achievements
     */
    public function achievements()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('user_achievements') || !\Illuminate\Support\Facades\Schema::hasTable('achievements')) {
            // Return empty relationship if tables don't exist
            return $this->belongsToMany(Achievement::class, 'user_achievements')
                ->whereRaw('1 = 0'); // Always return empty
        }
        
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('progress', 'is_unlocked', 'unlocked_at')
            ->withTimestamps();
    }

    /**
     * Get user's daily quests
     */
    public function dailyQuests()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('user_daily_quests') || !\Illuminate\Support\Facades\Schema::hasTable('daily_quests')) {
            // Return empty relationship if tables don't exist
            return $this->belongsToMany(DailyQuest::class, 'user_daily_quests')
                ->whereRaw('1 = 0'); // Always return empty
        }
        
        return $this->belongsToMany(DailyQuest::class, 'user_daily_quests')
            ->withPivot('progress', 'is_completed', 'is_claimed', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Get user's battles
     */
    public function battles()
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('battles')) {
            // Return empty relationship if table doesn't exist
            return $this->hasMany(Battle::class)->whereRaw('1 = 0'); // Always return empty
        }
        
        return $this->hasMany(Battle::class);
    }

    /**
     * Get user's equipped items
     */
    public function equipment()
    {
        return $this->hasMany(UserEquipment::class);
    }

    /**
     * Get user's tasks
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get user's notes
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Get user's exercises
     */
    public function exercises()
    {
        return $this->hasMany(UserExercise::class);
    }

    /**
     * Get user's exercise schedules
     */
    public function exerciseSchedules()
    {
        return $this->hasMany(ExerciseSchedule::class);
    }

    /**
     * Get user's personal info history
     */
    public function personalInfoHistory()
    {
        return $this->hasMany(PersonalInfoHistory::class);
    }

    /**
     * Get user's character relationships
     */
    public function characterRelationships()
    {
        return $this->hasMany(UserCharacterRelationship::class);
    }

    /**
     * Get user's character quests
     */
    public function characterQuests()
    {
        return $this->hasMany(UserCharacterQuest::class);
    }

    public function outfits()
    {
        return $this->belongsToMany(Outfit::class, 'user_outfits')
            ->withPivot('is_equipped', 'purchased_at')
            ->withTimestamps();
    }

    public function currentOutfit()
    {
        return $this->belongsTo(Outfit::class, 'current_outfit_id');
    }
}
