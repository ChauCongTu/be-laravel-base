<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create task_assignees table migration
 * 
 * Creates pivot table for many-to-many relationship between tasks and users.
 * Allows multiple users to be assigned to a single task.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_assignees', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['assignee', 'reviewer', 'approver'])->default('assignee');
            $table->timestamps();

            // Composite unique index to prevent duplicate assignments
            $table->unique(['task_id', 'user_id'], 'idx_task_assignees_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_assignees');
    }
};
