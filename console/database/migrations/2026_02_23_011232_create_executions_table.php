<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('executions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->restrictOnDelete();
            $table->foreignId('target_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['queued', 'running', 'success', 'failed', 'cancelled'])->default('queued');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('params')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('exit_code')->nullable();
            $table->text('error_summary')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('executions');
    }
};
