<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create user_email_preferences table migration
 * 
 * Creates table for user email preferences.
 * Controls email notifications for users.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_email_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('receive_notifications')->default(true);
            $table->boolean('receive_digest')->default(false);
            $table->boolean('receive_marketing')->default(false);
            $table->string('digest_frequency', 20)->default('weekly'); // daily, weekly, monthly
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id', 'idx_user_email_prefs_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_email_preferences');
    }
};
