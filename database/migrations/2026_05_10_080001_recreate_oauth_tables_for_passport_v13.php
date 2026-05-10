<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recreate all oauth_* tables to match Laravel Passport v13 schema exactly.
 *
 * The existing tables were created by a legacy migration with a different
 * schema. We drop and recreate them with the correct Passport v13 structure.
 *
 * WARNING: This drops all existing OAuth tokens and clients.
 * Run `php artisan passport:client --password` after this migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop in reverse dependency order
        Schema::dropIfExists('oauth_refresh_tokens');
        Schema::dropIfExists('oauth_access_tokens');
        Schema::dropIfExists('oauth_auth_codes');
        Schema::dropIfExists('oauth_tokens');       // legacy table — has FK to oauth_clients
        Schema::dropIfExists('oauth_clients');

        // oauth_clients — Passport v13
        Schema::create('oauth_clients', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('owner_type')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('name');
            $table->string('secret')->nullable();
            $table->string('provider')->nullable();
            $table->text('redirect_uris');
            $table->text('grant_types');
            $table->boolean('revoked')->default(false);
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
        });

        // oauth_auth_codes — Passport v13
        Schema::create('oauth_auth_codes', function (Blueprint $table): void {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('user_id')->index();
            $table->uuid('client_id');
            $table->text('scopes')->nullable();
            $table->boolean('revoked')->default(false);
            $table->dateTime('expires_at')->nullable();
        });

        // oauth_access_tokens — Passport v13
        Schema::create('oauth_access_tokens', function (Blueprint $table): void {
            $table->string('id', 100)->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->uuid('client_id');
            $table->string('name')->nullable();
            $table->text('scopes')->nullable();
            $table->boolean('revoked')->default(false);
            $table->timestamps();
            $table->dateTime('expires_at')->nullable();
        });

        // oauth_refresh_tokens — Passport v13
        Schema::create('oauth_refresh_tokens', function (Blueprint $table): void {
            $table->string('id', 100)->primary();
            $table->string('access_token_id', 100)->index();
            $table->boolean('revoked')->default(false);
            $table->dateTime('expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('oauth_refresh_tokens');
        Schema::dropIfExists('oauth_access_tokens');
        Schema::dropIfExists('oauth_auth_codes');
        Schema::dropIfExists('oauth_clients');
    }
};
