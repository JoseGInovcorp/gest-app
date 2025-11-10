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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Nome da conta (ex: "Conta Principal CGD")
            $table->string('banco'); // Nome do banco
            $table->string('iban')->unique(); // IBAN da conta
            $table->string('swift_bic')->nullable(); // Código SWIFT/BIC
            $table->decimal('saldo_inicial', 15, 2)->default(0); // Saldo inicial
            $table->decimal('saldo_atual', 15, 2)->default(0); // Saldo atual (calculado)
            $table->string('moeda', 3)->default('EUR'); // Moeda (EUR, USD, etc.)
            $table->enum('tipo', ['corrente', 'poupanca', 'credito', 'investimento'])->default('corrente');
            $table->enum('estado', ['ativa', 'inativa', 'encerrada'])->default('ativa');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Para soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
