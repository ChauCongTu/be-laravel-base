<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create notes table migration
 * 
 * Stores markdown and text notes with full-text search capabilities.
 * Supports folder organization and pinning functionality.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('folder_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title', 255);
            $table->longText('content');
            $table->enum('type', ['markdown', 'text'])->default('markdown');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance and search
            $table->index('user_id', 'idx_notes_user_id');
            $table->index('folder_id', 'idx_notes_folder_id');
            $table->index('is_pinned', 'idx_notes_pinned');
            $table->index(['user_id', 'folder_id'], 'idx_notes_user_folder');
            
            // FULLTEXT indexes for easy search functionality
            $table->fullText(['title', 'content'], 'ft_notes_title_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
