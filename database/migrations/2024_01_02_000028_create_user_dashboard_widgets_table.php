<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create user_dashboard_widgets table migration
 * 
 * Creates table for user dashboard widgets.
 * Stores widget configuration for user dashboards.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_dashboard_widgets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('widget_type', 100)->comment('Widget type name');
            $table->integer('column_position')->default(0);
            $table->integer('row_position')->default(0);
            $table->integer('width')->default(1);
            $table->integer('height')->default(1);
            $table->json('settings')->nullable()->comment('Widget settings as JSON');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id', 'idx_user_widgets_user_id');
            $table->index('column_position', 'idx_user_widgets_column');
            $table->index('row_position', 'idx_user_widgets_row');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_widgets');
    }
};
