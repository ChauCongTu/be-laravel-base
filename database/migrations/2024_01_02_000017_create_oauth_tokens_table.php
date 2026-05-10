<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create oauth_tokens table migration
 * 
 * Creates table for OAuth tokens.
 * Supports OAuth2 token storage.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oauth_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('oauth_client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('access_token', 255)->unique();
            $table->string('refresh_token', 255)->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('refresh_expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('oauth_client_id', 'idx_oauth_tokens_client_id');
            $table->index('user_id', 'idx_oauth_tokens_user_id');
            $table->index('access_token', 'idx_oauth_tokens_access_token');
            $table->index('refresh_token', 'idx_oauth_tokens_refresh_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_tokens');
    }
};
