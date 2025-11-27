<?php

use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
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

    // Hoàn thành
    Route::post('complete/{id}', [TaskController::class, 'complete'])->name('task.complete');

    // Bỏ thất bại
    Route::post('fail/{id}', [TaskController::class, 'fail'])->name('task.fail');
});

Route::middleware('auth:sanctum')->prefix('note')->group(function () {

    // Danh sách (POST để có body filter)
    Route::post('listing', [NoteController::class, 'listing'])->name('note.listing');

    // Tạo mới
    // Route::post('store', [TaskController::class, 'store'])->name('task.store');

    // Xem chi tiết
    Route::get('show/{id}', [NoteController::class, 'show'])->name('note.show');

    // Cập nhật
    Route::put('update/{id}', [NoteController::class, 'update'])->name('note.update');

    // Xóa
    Route::delete('delete/{id}', [NoteController::class, 'delete'])->name('note.delete');
});
