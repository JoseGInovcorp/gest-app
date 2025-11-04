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
        Schema::create('countries', function (Blueprint $table) {
            $table->char('code', 2)->primary(); // ISO 3166-1 alpha-2 (PT, ES, FR...)
            $table->string('name'); // Nome do país
            $table->string('name_en')->nullable(); // Nome em inglês
            $table->char('iso3', 3)->nullable(); // ISO 3166-1 alpha-3 (PRT, ESP, FRA...)
            $table->smallInteger('numeric_code')->nullable(); // Código numérico ISO
            $table->string('phone_prefix', 10)->nullable(); // +351, +34, +33...
            $table->boolean('vies_enabled')->default(false); // Suporta VIES
            $table->json('vat_formats')->nullable(); // Formatos de NIF válidos
            $table->string('currency_code', 3)->nullable(); // EUR, USD, GBP...
            $table->string('timezone')->nullable(); // Europe/Lisbon, Europe/Madrid...
            $table->boolean('active')->default(true); // País ativo no sistema
            $table->timestamps();

            // Índices
            $table->index('name');
            $table->index('vies_enabled');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
