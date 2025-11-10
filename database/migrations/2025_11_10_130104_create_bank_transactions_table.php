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
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->onDelete('cascade');
            $table->date('data_movimento'); // Data do movimento
            $table->string('descricao'); // Descrição do movimento
            $table->enum('tipo', ['credito', 'debito']); // Crédito (entrada) ou Débito (saída)
            $table->decimal('valor', 15, 2); // Valor do movimento
            $table->decimal('saldo_apos', 15, 2); // Saldo após o movimento
            $table->string('referencia')->nullable(); // Referência externa (ex: cheque, transferência)
            $table->enum('categoria', [
                'transferencia',
                'deposito',
                'levantamento',
                'pagamento',
                'recebimento',
                'juros',
                'comissoes',
                'outros'
            ])->default('outros');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
