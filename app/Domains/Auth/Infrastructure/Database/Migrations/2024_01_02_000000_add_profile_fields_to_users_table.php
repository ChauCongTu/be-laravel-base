<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add profile fields to users table migration
 * 
 * Adds additional fields to users table for profile information.
 * These fields are commonly needed in knowledge and task management applications.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Profile fields
            $table->string('avatar')->nullable()->after('email')->comment('User avatar URL');
            $table->string('timezone')->default('UTC')->after('avatar')->comment('User timezone');
            $table->string('locale')->default('en')->after('timezone')->comment('User locale/language');
            $table->string('theme')->default('light')->after('locale')->comment('User preferred theme');
            
            // Timestamps
            $table->timestamp('last_login_at')->nullable()->after('theme');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['avatar', 'timezone', 'locale', 'theme', 'last_login_at', 'last_login_ip']);
        });
    }
};
