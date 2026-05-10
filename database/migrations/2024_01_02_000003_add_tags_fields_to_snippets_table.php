<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add tags fields to snippets table migration
 * 
 * Adds tags-related fields to snippets table for content organization.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('snippets', function (Blueprint $table): void {
            $table->string('tags')->nullable()->after('language')->comment('JSON array of tags');
            $table->integer('view_count')->default(0)->after('tags');
            $table->integer('star_count')->default(0)->after('view_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('snippets', function (Blueprint $table): void {
            $table->dropColumn(['tags', 'view_count', 'star_count']);
        });
    }
};
