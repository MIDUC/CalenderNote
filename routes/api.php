<?php

use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\NPCController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\DailyLoginRewardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\OutfitController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PersonalInfoController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\CharacterQuestController;
use App\Http\Controllers\StoryGeneratorController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/online-exp', [AuthController::class, 'addOnlineExp'])->name('online.exp');
    Route::post('/breakthrough', [AuthController::class, 'breakthrough'])->name('breakthrough');
    Route::get('/level-name', [AuthController::class, 'getLevelName'])->name('level-name');
});

// Các route API khác...

Route::middleware('auth:sanctum')->prefix('schedule')->group(function () {

    // Danh sách (POST để có body filter)
    Route::post('listing', [ScheduleController::class, 'listing'])->name('schedule.listing');

    // Tạo mới
    Route::post('store', [ScheduleController::class, 'store'])->name('schedule.store');

    // Xem chi tiết
    Route::get('show/{id}', [ScheduleController::class, 'show'])->name('schedule.show');

    // Cập nhật
    Route::put('update/{id}', [ScheduleController::class, 'update'])->name('schedule.update');

    // Xóa
    Route::delete('delete/{id}', [ScheduleController::class, 'delete'])->name('schedule.delete');
    Route::post('play/{id}', [ScheduleController::class, 'play'])->name('schedule.play');
});

Route::middleware('auth:sanctum')->prefix('task')->group(function () {

    // Danh sách (POST để có body filter)
    Route::post('listing', [TaskController::class, 'listing'])->name('task.listing');

    // Tạo mới
    Route::post('store', [TaskController::class, 'store'])->name('task.store');

    // Xem chi tiết
    Route::get('show/{id}', [TaskController::class, 'show'])->name('task.show');

    // Cập nhật
    Route::put('update/{id}', [TaskController::class, 'update'])->name('task.update');

    // Xóa
    Route::delete('delete/{id}', [TaskController::class, 'delete'])->name('task.delete');
});

Route::middleware('auth:sanctum')->prefix('note')->group(function () {

    // Danh sách (POST để có body filter)
    Route::post('listing', [NoteController::class, 'listing'])->name('note.listing');

    // Tạo mới
    Route::post('store', [NoteController::class, 'store'])->name('note.store');

    // Xem chi tiết
    Route::get('show/{id}', [NoteController::class, 'show'])->name('note.show');

    // Cập nhật
    Route::put('update/{id}', [NoteController::class, 'update'])->name('note.update');

    // Xóa
    Route::delete('delete/{id}', [NoteController::class, 'delete'])->name('note.delete');
});

// Shop routes
Route::middleware('auth:sanctum')->prefix('shop')->group(function () {
    Route::get('items', [ShopController::class, 'list'])->name('shop.items');
    Route::post('buy/{id}', [ShopController::class, 'buy'])->name('shop.buy');
    Route::post('sell/{id}', [ShopController::class, 'sell'])->name('shop.sell');
    Route::get('inventory', [ShopController::class, 'inventory'])->name('shop.inventory');
});

// Outfits
Route::middleware('auth:sanctum')->prefix('outfits')->group(function () {
    Route::get('/', [OutfitController::class, 'index'])->name('outfits.index');
    Route::get('/my', [OutfitController::class, 'myOutfits'])->name('outfits.my');
    Route::post('/{id}/purchase', [OutfitController::class, 'purchase'])->name('outfits.purchase');
    Route::post('/{id}/equip', [OutfitController::class, 'equip'])->name('outfits.equip');
    Route::post('/unequip', [OutfitController::class, 'unequip'])->name('outfits.unequip');
});

// NPC routes
Route::middleware('auth:sanctum')->prefix('npc')->group(function () {
    Route::get('list', [NPCController::class, 'list'])->name('npc.list');
    Route::get('show/{id}', [NPCController::class, 'show'])->name('npc.show');
    Route::post('interact/{id}', [NPCController::class, 'interact'])->name('npc.interact');
});

// Quest routes
Route::middleware('auth:sanctum')->prefix('quest')->group(function () {
    Route::get('list', [QuestController::class, 'list'])->name('quest.list');
    Route::post('accept/{id}', [QuestController::class, 'accept'])->name('quest.accept');
    Route::put('progress/{id}', [QuestController::class, 'updateProgress'])->name('quest.progress');
    Route::post('claim/{id}', [QuestController::class, 'claim'])->name('quest.claim');
});

// Daily Login Reward routes
Route::middleware('auth:sanctum')->prefix('daily-reward')->group(function () {
    Route::get('list', [DailyLoginRewardController::class, 'list'])->name('daily-reward.list');
    Route::get('check', [DailyLoginRewardController::class, 'check'])->name('daily-reward.check');
    Route::post('claim', [DailyLoginRewardController::class, 'claim'])->name('daily-reward.claim');
});

// Equipment routes
Route::middleware('auth:sanctum')->prefix('equipment')->group(function () {
    Route::get('list', [EquipmentController::class, 'list'])->name('equipment.list');
    Route::post('equip/{id}', [EquipmentController::class, 'equip'])->name('equipment.equip');
    Route::post('unequip/{slot}', [EquipmentController::class, 'unequip'])->name('equipment.unequip');
});

// Battle routes
Route::middleware('auth:sanctum')->prefix('battle')->group(function () {
    Route::get('monsters', [BattleController::class, 'list'])->name('battle.monsters');
    Route::post('start/{id}', [BattleController::class, 'start'])->name('battle.start');
    Route::post('attack/{id}', [BattleController::class, 'attack'])->name('battle.attack');
    Route::post('flee/{id}', [BattleController::class, 'flee'])->name('battle.flee');
    Route::get('history', [BattleController::class, 'history'])->name('battle.history');
});

// Achievement routes
Route::middleware('auth:sanctum')->prefix('achievement')->group(function () {
    Route::get('list', [AchievementController::class, 'list'])->name('achievement.list');
});

// Statistics routes
Route::middleware('auth:sanctum')->prefix('statistics')->group(function () {
    Route::get('index', [StatisticsController::class, 'index'])->name('statistics.index');
});

// Leaderboard routes
Route::middleware('auth:sanctum')->prefix('leaderboard')->group(function () {
    Route::get('level', [LeaderboardController::class, 'level'])->name('leaderboard.level');
    Route::get('tasks', [LeaderboardController::class, 'tasks'])->name('leaderboard.tasks');
    Route::get('currency', [LeaderboardController::class, 'currency'])->name('leaderboard.currency');
    Route::get('streak', [LeaderboardController::class, 'streak'])->name('leaderboard.streak');
});

// Personal Info routes
Route::middleware('auth:sanctum')->prefix('personal-info')->group(function () {
    Route::get('show', [PersonalInfoController::class, 'show'])->name('personal-info.show');
    Route::put('update', [PersonalInfoController::class, 'update'])->name('personal-info.update');
    Route::get('history', [PersonalInfoController::class, 'history'])->name('personal-info.history');
});

// Exercise routes
Route::middleware('auth:sanctum')->prefix('exercise')->group(function () {
    Route::get('types', [ExerciseController::class, 'types'])->name('exercise.types');
    Route::get('schedules', [ExerciseController::class, 'schedules'])->name('exercise.schedules');
    Route::post('schedule', [ExerciseController::class, 'createSchedule'])->name('exercise.schedule.create');
    Route::post('schedule/{scheduleId}/accept', [ExerciseController::class, 'acceptSchedule'])->name('exercise.schedule.accept');
    Route::get('today-tasks', [ExerciseController::class, 'todayTasks'])->name('exercise.today-tasks');
    Route::post('log', [ExerciseController::class, 'log'])->name('exercise.log');
    Route::get('history', [ExerciseController::class, 'history'])->name('exercise.history');
});

// Story routes
Route::middleware('auth:sanctum')->prefix('stories')->group(function () {
    Route::get('list', [StoryController::class, 'list'])->name('stories.list');
    Route::get('show/{id}', [StoryController::class, 'show'])->name('stories.show');
});

// Story Generator routes
Route::middleware('auth:sanctum')->prefix('story-generator')->group(function () {
    Route::post('generate', [StoryGeneratorController::class, 'generate'])->name('story-generator.generate');
    Route::post('check-and-generate', [StoryGeneratorController::class, 'checkAndGenerate'])->name('story-generator.check-and-generate');
});

// Character routes
Route::middleware('auth:sanctum')->prefix('characters')->group(function () {
    Route::get('story/{storyId}', [CharacterController::class, 'listByStory'])->name('characters.story');
    Route::get('show/{id}', [CharacterController::class, 'show'])->name('characters.show');
    Route::get('relationships', [CharacterController::class, 'relationships'])->name('characters.relationships');
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // User management
    Route::get('users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('users/{id}', [AdminController::class, 'getUser'])->name('admin.users.show');
    Route::put('users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::post('users/{id}/change-password', [AdminController::class, 'changeUserPassword'])->name('admin.users.change-password');
    Route::delete('users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Level management
    Route::get('levels', [AdminController::class, 'levels'])->name('admin.levels');
    Route::post('levels', [AdminController::class, 'updateLevel'])->name('admin.levels.create');
    Route::put('levels/{id}', [AdminController::class, 'updateLevel'])->name('admin.levels.update');
    Route::delete('levels/{id}', [AdminController::class, 'deleteLevel'])->name('admin.levels.delete');
    
    // Level XP Requirements management
    Route::get('level-xp-requirements', [AdminController::class, 'levelXpRequirements'])->name('admin.level-xp-requirements');
    Route::post('level-xp-requirements', [AdminController::class, 'updateLevelXpRequirement'])->name('admin.level-xp-requirements.create');
    Route::put('level-xp-requirements/{id}', [AdminController::class, 'updateLevelXpRequirement'])->name('admin.level-xp-requirements.update');
    Route::delete('level-xp-requirements/{id}', [AdminController::class, 'deleteLevelXpRequirement'])->name('admin.level-xp-requirements.delete');
    
    // Item management
    Route::get('items', [AdminController::class, 'items'])->name('admin.items');
    Route::post('items', [AdminController::class, 'updateItem'])->name('admin.items.create');
    Route::put('items/{id}', [AdminController::class, 'updateItem'])->name('admin.items.update');
    Route::delete('items/{id}', [AdminController::class, 'deleteItem'])->name('admin.items.delete');
    
    // Task management
    Route::get('tasks', [AdminController::class, 'tasks'])->name('admin.tasks');
    Route::get('users/{userId}/tasks', [AdminController::class, 'getUserTasks'])->name('admin.users.tasks');
    
    // Schedule management
    Route::get('schedules', [AdminController::class, 'schedules'])->name('admin.schedules');
    Route::get('users/{userId}/schedules', [AdminController::class, 'getUserSchedules'])->name('admin.users.schedules');
    
    // Game Rates management
    Route::get('game-rates', [AdminController::class, 'gameRates'])->name('admin.game-rates');
    Route::post('game-rates', [AdminController::class, 'updateGameRate'])->name('admin.game-rates.create');
    Route::put('game-rates/{id}', [AdminController::class, 'updateGameRate'])->name('admin.game-rates.update');
    Route::delete('game-rates/{id}', [AdminController::class, 'deleteGameRate'])->name('admin.game-rates.delete');
    
    // Monsters management
    Route::get('monsters', [AdminController::class, 'monsters'])->name('admin.monsters');
    Route::post('monsters', [AdminController::class, 'updateMonster'])->name('admin.monsters.create');
    Route::put('monsters/{id}', [AdminController::class, 'updateMonster'])->name('admin.monsters.update');
    Route::delete('monsters/{id}', [AdminController::class, 'deleteMonster'])->name('admin.monsters.delete');
    
    // NPCs management
    Route::get('npcs', [AdminController::class, 'npcs'])->name('admin.npcs');
    Route::post('npcs', [AdminController::class, 'updateNPC'])->name('admin.npcs.create');
    Route::put('npcs/{id}', [AdminController::class, 'updateNPC'])->name('admin.npcs.update');
    Route::delete('npcs/{id}', [AdminController::class, 'deleteNPC'])->name('admin.npcs.delete');
    
    // Story Rules management
    Route::get('story-rules', [AdminController::class, 'storyRules'])->name('admin.story-rules');
    Route::post('story-rules', [AdminController::class, 'updateStoryRule'])->name('admin.story-rules.create');
    Route::put('story-rules/{id}', [AdminController::class, 'updateStoryRule'])->name('admin.story-rules.update');
    Route::delete('story-rules/{id}', [AdminController::class, 'deleteStoryRule'])->name('admin.story-rules.delete');
});