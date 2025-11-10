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
        Schema::create('client_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->onDelete('cascade');
            $table->date('data_movimento');
            $table->enum('tipo', ['debito', 'credito']); // débito = deve, crédito = recebeu
            $table->decimal('valor', 10, 2);
            $table->decimal('saldo_apos', 10, 2)->nullable(); // Saldo após este movimento
            $table->string('descricao');
            $table->enum('categoria', [
                'fatura',
                'pagamento',
                'nota_credito',
                'nota_debito',
                'juros',
                'ajuste',
                'outros'
            ])->default('outros');
            $table->string('referencia')->nullable(); // Número da fatura, recibo, etc
            $table->foreignId('related_id')->nullable(); // ID da fatura ou pagamento relacionado
            $table->string('related_type')->nullable(); // Tipo do relacionamento (Invoice, Payment, etc)
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Índices para melhorar performance
            $table->index(['entity_id', 'data_movimento']);
            $table->index(['tipo', 'categoria']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_accounts');
    }
};
