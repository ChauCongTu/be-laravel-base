<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create api_tokens table migration
 * 
 * Creates table for API tokens.
 * Supports personal access tokens and OAuth tokens.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('api_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('token', 80)->unique();
            $table->string('type', 50)->default('personal'); // personal, oauth, system
            $table->text('abilities')->nullable()->comment('JSON array of abilities');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id', 'idx_api_tokens_user_id');
            $table->index('token', 'idx_api_tokens_token');
            $table->index('expires_at', 'idx_api_tokens_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
