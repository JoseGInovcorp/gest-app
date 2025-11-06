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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('referencia')->unique(); // ART001, ART002...
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2); // Preço com 2 casas decimais
            $table->decimal('iva_percentagem', 5, 2); // Ex: 23.00 para 23%
            $table->string('foto')->nullable(); // Path da foto
            $table->text('observacoes')->nullable();
            $table->enum('estado', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
