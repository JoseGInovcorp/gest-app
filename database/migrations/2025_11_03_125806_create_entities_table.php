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
        Schema::create('entities', function (Blueprint $table) {
            $table->id();

            // Tipo de entidade
            $table->enum('type', ['client', 'supplier', 'both'])->default('client');

            // Informação básica
            $table->string('name');
            $table->string('commercial_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('website')->nullable();

            // Informação fiscal
            $table->string('tax_number')->unique()->nullable();
            $table->string('vat_number')->nullable(); // Para VIES
            $table->string('country_code', 2)->default('PT'); // Para VIES
            $table->boolean('vies_valid')->default(false);
            $table->timestamp('vies_last_check')->nullable();
            $table->json('vies_data')->nullable(); // Dados da consulta VIES

            // Morada principal
            $table->string('address')->nullable();
            $table->string('address_2')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('country')->default('Portugal');

            // Morada de faturação (se diferente)
            $table->boolean('different_billing_address')->default(false);
            $table->string('billing_address')->nullable();
            $table->string('billing_address_2')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_district')->nullable();
            $table->string('billing_country')->nullable();

            // Informação comercial
            $table->decimal('credit_limit', 12, 2)->nullable();
            $table->integer('payment_days')->default(30);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'card', 'mb_way'])->default('bank_transfer');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->boolean('tax_exempt')->default(false);

            // Informação bancária
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->string('bank_name')->nullable();

            // Status e observações
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();
            $table->json('custom_fields')->nullable();

            // Metadados
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Índices
            $table->index(['type', 'active']);
            $table->index('name');
            $table->index('tax_number');
            $table->index(['country_code', 'vat_number']);

            // Chaves estrangeiras
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
