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
        Schema::create('task_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // VALIDATE_STOCK, etc
            $table->string('label'); // Nome exibido
            $table->text('description')->nullable();
            $table->string('assigned_group')->nullable(); // Grupo padrão
            $table->integer('default_sequence')->default(0); // Ordem padrão no workflow
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_templates');
    }
};
