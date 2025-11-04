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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // Número sequencial (como nas entidades)
            $table->unsignedInteger('number')->unique()->comment('Número sequencial do contacto');

            // Relação com Entidades (FK)
            $table->foreignId('entity_id')
                ->constrained('entities')
                ->onDelete('cascade')
                ->comment('Entidade associada ao contacto');

            // Dados pessoais
            $table->string('first_name')->comment('Nome próprio');
            $table->string('last_name')->comment('Apelido');
            $table->string('function')->nullable()->comment('Função na empresa');

            // Contactos
            $table->string('phone')->nullable()->comment('Telefone fixo');
            $table->string('mobile')->nullable()->comment('Telemóvel');
            $table->string('email')->nullable()->comment('Email');

            // RGPD e observações
            $table->boolean('rgpd_consent')->default(false)->comment('Consentimento RGPD (Sim/Não)');
            $table->text('observations')->nullable()->comment('Observações');

            // Estado (Ativo/Inativo)
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Estado do contacto');

            // Auditoria
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Índices para performance
            $table->index(['entity_id', 'status']);
            $table->index(['email']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
