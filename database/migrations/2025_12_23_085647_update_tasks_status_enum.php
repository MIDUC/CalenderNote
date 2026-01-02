<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table with new enum values
        // Since SQLite doesn't support ALTER COLUMN for enum, we'll use a workaround
        
        if (DB::getDriverName() === 'sqlite') {
            // SQLite: Drop old CHECK constraint and add new one
            DB::statement("
                CREATE TABLE tasks_new (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    schedule_id INTEGER,
                    user_id INTEGER NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    task_date DATE NOT NULL,
                    task_time TIME,
                    status VARCHAR(255) NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'in_progress', 'completed', 'failed', 'done', 'blocked')),
                    note TEXT,
                    checkin_photo_url VARCHAR(255),
                    completed_at DATETIME,
                    require_checkin BOOLEAN DEFAULT 0,
                    fixed_time BOOLEAN DEFAULT 0,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )
            ");
            
            DB::statement("
                INSERT INTO tasks_new 
                SELECT * FROM tasks
            ");
            
            DB::statement("DROP TABLE tasks");
            DB::statement("ALTER TABLE tasks_new RENAME TO tasks");
            
            // Recreate indexes
            DB::statement("CREATE INDEX tasks_schedule_id_index ON tasks(schedule_id)");
            DB::statement("CREATE INDEX tasks_user_id_index ON tasks(user_id)");
        } else {
            // For other databases (MySQL, PostgreSQL)
            Schema::table('tasks', function (Blueprint $table) {
                $table->enum('status', ['pending', 'in_progress', 'completed', 'failed', 'done', 'blocked'])
                    ->default('pending')
                    ->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // Revert to old enum values
            DB::statement("
                CREATE TABLE tasks_old (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    schedule_id INTEGER,
                    user_id INTEGER NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    task_date DATE NOT NULL,
                    task_time TIME,
                    status VARCHAR(255) NOT NULL DEFAULT 'pending' CHECK(status IN ('pending', 'in_progress', 'completed', 'failed')),
                    note TEXT,
                    checkin_photo_url VARCHAR(255),
                    completed_at DATETIME,
                    require_checkin BOOLEAN DEFAULT 0,
                    fixed_time BOOLEAN DEFAULT 0,
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )
            ");
            
            DB::statement("
                INSERT INTO tasks_old 
                SELECT * FROM tasks WHERE status IN ('pending', 'in_progress', 'completed', 'failed')
            ");
            
            DB::statement("DROP TABLE tasks");
            DB::statement("ALTER TABLE tasks_old RENAME TO tasks");
            
            DB::statement("CREATE INDEX tasks_schedule_id_index ON tasks(schedule_id)");
            DB::statement("CREATE INDEX tasks_user_id_index ON tasks(user_id)");
        } else {
            Schema::table('tasks', function (Blueprint $table) {
                $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])
                    ->default('pending')
                    ->change();
            });
        }
    }
};
