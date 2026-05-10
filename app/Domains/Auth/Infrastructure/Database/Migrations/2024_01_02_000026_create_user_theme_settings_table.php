<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create user_theme_settings table migration
 * 
 * Creates table for user theme settings.
 * Stores custom theme configurations.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_theme_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('theme', 50)->default('light'); // light, dark, system
            $table->string('primary_color', 7)->nullable()->comment('Hex color');
            $table->string('secondary_color', 7)->nullable()->comment('Hex color');
            $table->string('accent_color', 7)->nullable()->comment('Hex color');
            $table->boolean('compact_mode')->default(false);
            $table->boolean('show_sidebar')->default(true);
            $table->boolean('show_breadcrumbs')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id', 'idx_user_theme_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_theme_settings');
    }
};
