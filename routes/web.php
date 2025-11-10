<?php

use Illuminate\Support\Facades\Route;

Route::match(['GET', 'POST'], '/', function () {
    return response()->json(['message' => 'Route hỗ trợ cả GET và POST']);
});

Route::get('/{any}', function () {
    return view('app'); // hoặc view chứa Vue mount (ví dụ app.blade.php)
})->where('any', '.*');