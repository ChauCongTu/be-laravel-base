<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Upgrade oauth_clients table to match Laravel Passport v13 schema.
 *
 * The existing table was created by an older migration and is missing
 * columns required by Passport v13 (provider, grant_types, redirect_uris
 * as JSON, owner_type/owner_id polymorphic columns).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table): void {
            // Passport v13 uses UUID primary key
            if (!$this->columnExists('oauth_clients', 'provider')) {
                $table->string('provider')->nullable()->after('secret');
            }

            if (!$this->columnExists('oauth_clients', 'grant_types')) {
                $table->text('grant_types')->nullable()->after('redirect_uris');
            }

            // Passport v13 uses polymorphic owner instead of user_id
            if (!$this->columnExists('oauth_clients', 'owner_type')) {
                $table->string('owner_type')->nullable()->after('id');
            }

            if (!$this->columnExists('oauth_clients', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('owner_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table): void {
            $table->dropColumn(['provider', 'grant_types', 'owner_type', 'owner_id']);
        });
    }

    private function columnExists(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column);
    }
};
