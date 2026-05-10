<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create search_history table migration
 * 
 * Creates table for search history.
 * Tracks user search queries for analytics and suggestions.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('search_history', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('query');
            $table->string('search_type', 50)->nullable()->comment('snippet, note, task, folder');
            $table->integer('results_count')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id', 'idx_search_history_user_id');
            $table->index('query', 'idx_search_history_query');
            $table->index('created_at', 'idx_search_history_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_history');
    }
};
