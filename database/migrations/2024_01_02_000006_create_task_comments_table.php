<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create task_comments table migration
 * 
 * Creates table for comments on tasks.
 * Supports threaded comments through parent_id.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_comment_id')->nullable()->constrained('task_comments')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('task_id', 'idx_task_comments_task_id');
            $table->index('user_id', 'idx_task_comments_user_id');
            $table->index('parent_comment_id', 'idx_task_comments_parent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};
