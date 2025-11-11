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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();

            // Número da fatura (gerado automaticamente: FF-YYYY-####)
            $table->string('numero')->unique();

            // Datas
            $table->date('data_fatura');
            $table->date('data_vencimento');

            // Relação com Fornecedor (Entity)
            $table->foreignId('supplier_id')
                ->constrained('entities')
                ->onDelete('cascade');

            // Relação com Encomenda Fornecedor (opcional)
            $table->foreignId('supplier_order_id')
                ->nullable()
                ->constrained('supplier_orders')
                ->onDelete('set null');

            // Valores
            $table->decimal('valor_total', 10, 2);

            // Documentos (caminhos para storage)
            $table->string('documento')->nullable(); // Fatura PDF
            $table->string('comprovativo_pagamento')->nullable(); // Comprovativo PDF

            // Estado
            $table->enum('estado', ['pendente', 'paga'])->default('pendente');

            $table->timestamps();
            $table->softDeletes();

            // Índices para performance
            $table->index('data_fatura');
            $table->index('estado');
            $table->index(['supplier_id', 'data_fatura']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};
