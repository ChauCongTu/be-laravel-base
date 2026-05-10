<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create user_templates table migration
 * 
 * Creates table for user templates.
 * Stores reusable templates for notes and snippets.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('type', 50)->comment('note, snippet, task');
            $table->text('content');
            $table->json('variables')->nullable()->comment('Template variables as JSON');
            $table->boolean('is_default')->default(false);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('user_id', 'idx_user_templates_user_id');
            $table->index('type', 'idx_user_templates_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_templates');
    }
};
