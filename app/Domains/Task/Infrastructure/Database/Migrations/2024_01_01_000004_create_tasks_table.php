<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create tasks table migration
 * 
 * Stores task management data with status tracking, priority levels,
 * and reminder functionality for the LifeOS task system.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('status', ['todo', 'doing', 'done'])->default('todo');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('reminder_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance and filtering
            $table->index('user_id', 'idx_tasks_user_id');
            $table->index('status', 'idx_tasks_status');
            $table->index('priority', 'idx_tasks_priority');
            $table->index('due_date', 'idx_tasks_due_date');
            $table->index('reminder_at', 'idx_tasks_reminder');
            
            // Composite indexes for common queries
            $table->index(['user_id', 'status'], 'idx_tasks_user_status');
            $table->index(['user_id', 'priority'], 'idx_tasks_user_priority');
            $table->index(['status', 'due_date'], 'idx_tasks_status_due');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
