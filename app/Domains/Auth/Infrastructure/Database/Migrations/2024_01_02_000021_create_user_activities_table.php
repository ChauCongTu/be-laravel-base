<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create user_activities table migration
 * 
 * Creates table for user activity tracking.
 * Tracks daily user engagement metrics.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('activity_date');
            $table->integer('login_count')->default(0);
            $table->integer('snippet_count')->default(0);
            $table->integer('note_count')->default(0);
            $table->integer('task_count')->default(0);
            $table->integer('folder_count')->default(0);
            $table->integer('total_actions')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id', 'idx_user_activities_user_id');
            $table->index('activity_date', 'idx_user_activities_date');
            
            // Composite unique index to prevent duplicate daily records
            $table->unique(['user_id', 'activity_date'], 'idx_user_activities_user_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
