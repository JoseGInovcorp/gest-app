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
        Schema::create('calendar_event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#3B82F6'); // Hex color code
            $table->string('icon', 50)->nullable(); // Nome do ícone Lucide
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('is_active');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_event_types');
    }
};
