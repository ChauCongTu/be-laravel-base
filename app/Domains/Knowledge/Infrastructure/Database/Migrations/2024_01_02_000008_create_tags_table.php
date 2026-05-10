<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create tags table migration
 * 
 * Creates table for tags used across the application.
 * Supports shared tags system.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 50);
            $table->string('color', 7)->nullable()->comment('Hex color code');
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id', 'idx_tags_user_id');
            $table->index('name', 'idx_tags_name');
            
            // Composite unique index to prevent duplicate tag names per user
            $table->unique(['user_id', 'name'], 'idx_tags_user_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
