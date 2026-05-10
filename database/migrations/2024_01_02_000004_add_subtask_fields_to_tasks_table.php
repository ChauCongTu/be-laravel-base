<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add subtask fields to tasks table migration
 * 
 * Adds subtask-related fields to tasks table for task hierarchy.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table): void {
            $table->foreignId('parent_task_id')->nullable()->after('user_id')
                ->constrained('tasks')->nullOnDelete();
            $table->integer('order')->default(0)->after('parent_task_id');
            $table->integer('estimated_time')->nullable()->after('order')->comment('Estimated time in minutes');
            $table->integer('actual_time')->nullable()->after('estimated_time')->comment('Actual time in minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table): void {
            $table->dropColumn(['parent_task_id', 'order', 'estimated_time', 'actual_time']);
        });
    }
};
