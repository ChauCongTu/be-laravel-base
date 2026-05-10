<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create backups table migration
 * 
 * Creates table for data backups.
 * Stores backup information for data recovery.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('backup_type', 50)->comment('full, incremental, manual, scheduled');
            $table->string('file_path');
            $table->bigInteger('file_size');
            $table->string('storage_location', 50)->default('local');
            $table->integer('records_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_successful')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id', 'idx_backups_user_id');
            $table->index('backup_type', 'idx_backups_type');
            $table->index('is_successful', 'idx_backups_successful');
            $table->index('created_at', 'idx_backups_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
