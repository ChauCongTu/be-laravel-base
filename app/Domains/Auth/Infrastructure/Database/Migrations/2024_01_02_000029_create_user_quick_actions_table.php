<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create user_quick_actions table migration
 * 
 * Creates table for user quick actions.
 * Stores quick action configurations for users.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_quick_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('action_type', 100)->comment('Action type name');
            $table->json('action_data')->nullable()->comment('Action data as JSON');
            $table->integer('position')->default(0);
            $table->string('icon', 50)->nullable();
            $table->string('color', 7)->nullable()->comment('Hex color');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id', 'idx_user_quick_actions_user_id');
            $table->index('position', 'idx_user_quick_actions_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_quick_actions');
    }
};
