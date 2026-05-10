<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add extended profile fields to users table.
 *
 * Adds optional personal information fields:
 * phone, user_name (unique handle), nationality, city, address, gender.
 * Note: avatar column already exists from previous migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('user_name', 50)->nullable()->unique()->after('name')
                ->comment('Unique username handle e.g. @john_doe');
            $table->string('phone', 20)->nullable()->after('user_name');
            $table->string('nationality', 100)->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('nationality');
            $table->text('address')->nullable()->after('city');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])
                ->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['user_name', 'phone', 'nationality', 'city', 'address', 'gender']);
        });
    }
};
