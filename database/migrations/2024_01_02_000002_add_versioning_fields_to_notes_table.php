<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add versioning fields to notes table migration
 * 
 * Adds versioning-related fields to notes table for content history.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table): void {
            $table->integer('version')->default(1)->after('type');
            $table->timestamp('last_edited_at')->nullable()->after('version');
            $table->foreignId('last_edited_by')->nullable()->after('last_edited_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table): void {
            $table->dropColumn(['version', 'last_edited_at', 'last_edited_by']);
        });
    }
};
