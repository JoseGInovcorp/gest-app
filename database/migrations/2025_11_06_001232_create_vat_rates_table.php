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
        Schema::create('vat_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50); // Ex: "IVA Normal", "IVA Reduzido"
            $table->decimal('rate', 5, 2); // Ex: 23.00, 13.00, 6.00, 0.00
            $table->boolean('is_default')->default(false); // Uma taxa pode ser padrão
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vat_rates');
    }
};
