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
        Schema::create('proposal_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('articles')->onDelete('restrict');
            $table->foreignId('entity_id')->nullable()->constrained('entities')->onDelete('set null');
            $table->integer('quantidade')->default(1);
            $table->decimal('preco_custo', 10, 2)->nullable();
            $table->decimal('preco_venda', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();

            // Índices para otimização
            $table->index('proposal_id');
            $table->index('article_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_lines');
    }
};
