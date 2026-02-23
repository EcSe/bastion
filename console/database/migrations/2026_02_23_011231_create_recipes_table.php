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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('risk_level')->default(0);
            $table->unsignedInteger('timeout_sec')->default(300);
            $table->json('parameters')->nullable();
            $table->longText('steps');
            $table->string('version', 32)->default('1.0.0');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('risk_level');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
