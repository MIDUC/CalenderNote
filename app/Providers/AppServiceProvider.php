<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kiểm tra APP_DEBUG để chỉ chạy logic này khi ở môi trường phát triển
        if (config('app.debug')) {
            DB::listen(function ($query) {
                // Lấy SQL thô và thay thế các dấu hỏi (?) bằng giá trị binding
                // Đây là một cách đơn giản để tạo chuỗi SQL hoàn chỉnh để dễ đọc
                $sql = str_replace('?', "'%s'", $query->sql);
                $fullSql = vsprintf($sql, $query->bindings);

                // Ghi log ra kênh mặc định (stack/stderr)
                Log::info("SQL Query: {$fullSql} | Time: {$query->time}ms");
            });
        }
    }
}
