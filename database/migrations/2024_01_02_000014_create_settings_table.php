<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create settings table migration
 * 
 * Creates table for user settings.
 * Stores application configuration per user.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 50)->default('string'); // string, boolean, integer, float, json
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id', 'idx_settings_user_id');
            $table->index('key', 'idx_settings_key');
            
            // Composite unique index to prevent duplicate settings
            $table->unique(['user_id', 'key'], 'idx_settings_user_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
