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
        Schema::table('articles', function (Blueprint $table) {
            $table->enum('tipo', ['produto', 'servico'])->default('produto')->after('estado');
            $table->string('gama')->nullable()->after('tipo'); // Ex: Premium, Standard, Básico
            $table->decimal('stock_quantidade', 10, 2)->default(0)->after('gama');
            $table->date('data_criacao')->nullable()->after('stock_quantidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'gama', 'stock_quantidade', 'data_criacao']);
        });
    }
};
