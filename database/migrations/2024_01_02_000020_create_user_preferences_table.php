<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create user_preferences table migration
 * 
 * Creates table for user preferences.
 * Stores user-specific application preferences.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 50)->default('string');
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id', 'idx_user_preferences_user_id');
            $table->index('key', 'idx_user_preferences_key');
            
            // Composite unique index to prevent duplicate preferences
            $table->unique(['user_id', 'key'], 'idx_user_preferences_user_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
