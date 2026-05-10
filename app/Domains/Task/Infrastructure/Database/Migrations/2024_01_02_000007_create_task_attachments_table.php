<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create task_attachments table migration
 * 
 * Creates table for file attachments on tasks.
 * Supports multiple file types and storage locations.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 100);
            $table->bigInteger('file_size');
            $table->string('storage_location', 50)->default('local'); // local, s3, etc.
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('task_id', 'idx_task_attachments_task_id');
            $table->index('user_id', 'idx_task_attachments_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
    }
};
