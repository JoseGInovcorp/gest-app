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
        Schema::table('proposals', function (Blueprint $table) {
            // Remover a constraint antiga
            $table->dropForeign(['entity_id']);

            // Adicionar a nova constraint com cascade
            $table->foreign('entity_id')
                ->references('id')
                ->on('entities')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // Reverter para restrict
            $table->dropForeign(['entity_id']);

            $table->foreign('entity_id')
                ->references('id')
                ->on('entities')
                ->onDelete('restrict');
        });
    }
};
