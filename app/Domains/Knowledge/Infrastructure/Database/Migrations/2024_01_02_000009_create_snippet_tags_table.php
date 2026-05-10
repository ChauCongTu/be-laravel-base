<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create snippet_tags table migration
 * 
 * Creates pivot table for many-to-many relationship between snippets and tags.
 * Supports tagging system for code snippets.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('snippet_tags', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('snippet_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Composite unique index to prevent duplicate tags
            $table->unique(['snippet_id', 'tag_id'], 'idx_snippet_tags_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snippet_tags');
    }
};
