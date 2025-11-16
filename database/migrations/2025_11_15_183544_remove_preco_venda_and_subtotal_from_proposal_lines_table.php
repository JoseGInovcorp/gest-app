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
        Schema::table('proposal_lines', function (Blueprint $table) {
            $table->dropColumn(['preco_venda', 'subtotal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_lines', function (Blueprint $table) {
            $table->decimal('preco_venda', 10, 2)->after('preco_custo');
            $table->decimal('subtotal', 10, 2)->after('preco_venda');
        });
    }
};
