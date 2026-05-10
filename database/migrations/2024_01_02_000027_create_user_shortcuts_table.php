<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create user_shortcuts table migration
 * 
 * Creates table for user keyboard shortcuts.
 * Stores custom keyboard shortcuts for users.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_shortcuts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action', 100)->comment('Action name');
            $table->string('shortcut', 100)->comment('Keyboard shortcut');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id', 'idx_user_shortcuts_user_id');
            $table->index('action', 'idx_user_shortcuts_action');
            
            // Composite unique index to prevent duplicate actions
            $table->unique(['user_id', 'action'], 'idx_user_shortcuts_user_action_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_shortcuts');
    }
};
