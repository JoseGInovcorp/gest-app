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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // Número sequencial da fatura
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade'); // Cliente
            $table->date('data_fatura'); // Data de emissão
            $table->date('data_vencimento')->nullable(); // Data de vencimento
            $table->decimal('valor_total', 10, 2); // Valor total da fatura
            $table->decimal('valor_pago', 10, 2)->default(0); // Valor já pago
            $table->enum('estado', ['pendente', 'parcialmente_paga', 'paga', 'vencida', 'cancelada'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['entity_id', 'data_fatura']);
            $table->index('estado');
        });

        // Adicionar invoice_id à tabela client_accounts
        Schema::table('client_accounts', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->after('related_type')->constrained('invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_accounts', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });

        Schema::dropIfExists('invoices');
    }
};
