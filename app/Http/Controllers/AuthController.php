<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Services\GamificationService;

class AuthController extends Controller
{
    // Đăng ký
    public function register(Request $request)
    {
        \Log::info('Register request received 1', ['data' => $request->all()]); // Log incoming request data
        \Log::debug("Bị cái éo gì");
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['error' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        \Log::info('Register request validated 2', ['validated' => $validated]); // Log validated data

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        \Log::info('User created', ['user' => $user]); // Log user creation

        $token = $user->createToken('auth_token')->plainTextToken;

        \Log::info('Token generated', ['token' => $token]); // Log token generation

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Đăng nhập
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        // Update login date
        $today = \Carbon\Carbon::today();
        if (!$user->last_login_date || $user->last_login_date->lt($today)) {
            $user->last_login_date = $today;
            $user->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    // Lấy thông tin user hiện tại
    public function me(Request $request)
    {
        $user = $request->user();
        
        // Calculate XP progress (exp is already relative to current level)
        $expNeededForNext = GamificationService::getXPNeededForCurrentLevel($user->level);
        $canBreakthrough = GamificationService::canBreakthrough($user);
        
        // Check level achievements
        \App\Http\Controllers\AchievementController::checkAchievements($user, 'level');
        
        $userData = $user->toArray();
        $userData['gamification'] = [
            'current_exp' => $user->exp,
            'next_level_exp' => $expNeededForNext,
            'progress_percentage' => GamificationService::getProgressToNextLevel($user),
            'can_breakthrough' => $canBreakthrough,
        ];
        
        return response()->json($userData);
    }

    /**
     * Add online experience points (called every 5 seconds when user is online)
     */
    public function addOnlineExp(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get online_exp_per_5s from user, default to 1 if not set
        $expToAdd = $user->online_exp_per_5s ?? 1;
        
        if ($expToAdd <= 0) {
            return response()->json([
                'message' => 'No exp to add',
                'user' => $user
            ]);
        }

        // Add XP without leveling up (user must click breakthrough button)
        GamificationService::addXPWithoutLevelUp($user, $expToAdd);
        
        // Calculate XP progress for response
        $expNeededForNext = GamificationService::getXPNeededForCurrentLevel($user->level);
        $canBreakthrough = GamificationService::canBreakthrough($user);
        $userData = $user->fresh()->toArray();
        $userData['gamification'] = [
            'current_exp' => $user->exp,
            'next_level_exp' => $expNeededForNext,
            'progress_percentage' => GamificationService::getProgressToNextLevel($user),
            'can_breakthrough' => $canBreakthrough,
        ];

        return response()->json([
            'message' => 'Experience added successfully',
            'exp_added' => $expToAdd,
            'user' => $userData,
            'can_breakthrough' => $canBreakthrough
        ]);
    }

    /**
     * Perform breakthrough - level up manually
     */
    public function breakthrough(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Perform breakthrough
        $result = GamificationService::breakthrough($user);
        
        if (!$result['success']) {
            return response()->json([
                'message' => $result['message'],
                'current_exp' => $result['current_exp'],
                'required_exp' => $result['required_exp'],
            ], 400);
        }

        // Calculate XP progress for response
        $expNeededForNext = GamificationService::getXPNeededForCurrentLevel($user->level);
        $userData = $user->fresh()->toArray();
        $userData['gamification'] = [
            'current_exp' => $user->exp,
            'next_level_exp' => $expNeededForNext,
            'progress_percentage' => GamificationService::getProgressToNextLevel($user),
            'can_breakthrough' => GamificationService::canBreakthrough($user),
        ];

        return response()->json([
            'message' => 'Breakthrough successful!',
            'user' => $userData,
            'breakthrough_result' => $result,
        ]);
    }

    /**
     * Get level name for a specific level (with tier)
     */
    public function getLevelName(Request $request)
    {
        $level = $request->input('level', $request->user()?->level ?? 1);
        
        $levelName = \App\Models\LevelName::getNameWithTierForLevel((int)$level);
        
        return response()->json([
            'level' => (int)$level,
            'level_name' => $levelName ?? 'Phàm cảnh Tầng 1',
        ]);
    }
}