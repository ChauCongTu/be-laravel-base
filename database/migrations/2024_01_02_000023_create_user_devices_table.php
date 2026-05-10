<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create user_devices table migration
 * 
 * Creates table for user device tracking.
 * Tracks devices used by users for security purposes.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_name', 100);
            $table->string('device_type', 50)->comment('mobile, desktop, tablet, server');
            $table->string('device_id', 100)->nullable()->comment('Unique device identifier');
            $table->string('os', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id', 'idx_user_devices_user_id');
            $table->index('device_id', 'idx_user_devices_device_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
