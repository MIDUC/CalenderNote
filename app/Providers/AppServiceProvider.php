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
                // Convert bindings to strings, handling DateTime objects, booleans, nulls, and special characters
                $bindings = array_map(function ($binding) {
                    if ($binding instanceof \DateTime || $binding instanceof \DateTimeInterface) {
                        return $binding->format('Y-m-d H:i:s');
                    }
                    if (is_bool($binding)) {
                        return $binding ? '1' : '0';
                    }
                    if (is_null($binding)) {
                        return 'NULL';
                    }
                    // Escape single quotes và convert to string
                    $stringValue = (string) $binding;
                    // Escape single quotes for SQL
                    $stringValue = str_replace("'", "''", $stringValue);
                    return $stringValue;
                }, $query->bindings);
                
                // Replace placeholders manually to avoid vsprintf issues with % characters
                $sql = $query->sql;
                $fullSql = $sql;
                
                foreach ($bindings as $binding) {
                    $pos = strpos($fullSql, '?');
                    if ($pos !== false) {
                        $fullSql = substr_replace($fullSql, "'{$binding}'", $pos, 1);
                    }
                }

                // Ghi log ra kênh mặc định (stack/stderr)
                Log::info("SQL Query: {$fullSql} | Time: {$query->time}ms");
            });
        }
    }
}
