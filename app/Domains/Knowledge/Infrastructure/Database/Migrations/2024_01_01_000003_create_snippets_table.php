<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create snippets table migration
 * 
 * Stores code snippets with language identification and full-text search.
 * Supports various programming languages for code organization.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('snippets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('folder_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title', 255);
            $table->longText('code_block');
            $table->string('language', 50)->comment('Programming language: php, docker, nginx, etc.');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id', 'idx_snippets_user_id');
            $table->index('folder_id', 'idx_snippets_folder_id');
            $table->index('language', 'idx_snippets_language');
            
            // FULLTEXT indexes for easy search functionality
            $table->fullText(['title', 'code_block'], 'ft_snippets_title_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snippets');
    }
};
