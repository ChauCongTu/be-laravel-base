<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create folders table migration
 * 
 * Creates hierarchical folder structure for organizing knowledge and tasks.
 * Supports nested folders through self-referencing parent_id relationship.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('folders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->string('color', 7)->nullable()->comment('Hex color code');
            $table->string('icon', 50)->nullable()->comment('Icon name or emoji');
            $table->foreignId('parent_id')->nullable()->constrained('folders')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id', 'idx_folders_user_id');
            $table->index('parent_id', 'idx_folders_parent_id');
            $table->index(['user_id', 'parent_id'], 'idx_folders_user_parent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folders');
    }
};
