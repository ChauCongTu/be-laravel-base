<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create oauth_clients table migration
 * 
 * Creates table for OAuth clients.
 * Supports OAuth2 authentication flow.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oauth_clients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug', 50)->unique();
            $table->string('secret', 100)->unique();
            $table->string('redirect_url');
            $table->boolean('is_personal_access_client')->default(false);
            $table->boolean('is_password_client')->default(false);
            $table->boolean('revoked')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id', 'idx_oauth_clients_user_id');
            $table->index('slug', 'idx_oauth_clients_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oauth_clients');
    }
};
