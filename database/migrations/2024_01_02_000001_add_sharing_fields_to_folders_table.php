<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add sharing fields to folders table migration
 * 
 * Adds sharing-related fields to folders table for collaborative features.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('folders', function (Blueprint $table): void {
            $table->boolean('is_shared')->default(false)->after('parent_id');
            $table->timestamp('shared_at')->nullable()->after('is_shared');
            $table->foreignId('shared_with_user_id')->nullable()->after('shared_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table): void {
            $table->dropColumn(['is_shared', 'shared_at', 'shared_with_user_id']);
        });
    }
};
