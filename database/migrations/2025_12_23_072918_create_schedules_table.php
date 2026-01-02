<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['event', 'task', 'reminder'])->default('event');
            $table->enum('repeat_type', ['none', 'daily', 'weekly', 'monthly', 'yearly'])->default('none');
            $table->json('days_of_week')->nullable();
            $table->json('days_of_month')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('has_fixed_time')->default(false);
            $table->time('fixed_time')->nullable();
            $table->integer('notify_before_minutes')->nullable();
            $table->json('notify_times')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('shareable')->default(false);
            $table->boolean('require_checkin')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
