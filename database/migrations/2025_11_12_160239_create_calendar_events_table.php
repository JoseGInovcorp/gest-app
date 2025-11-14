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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('entity_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('calendar_event_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('calendar_event_action_id')->nullable()->constrained()->onDelete('set null');

            $table->date('data');
            $table->time('hora');
            $table->integer('duracao')->comment('Duração em minutos');
            $table->boolean('partilha')->default(false);
            $table->text('conhecimento')->nullable();
            $table->text('descricao')->nullable();
            $table->enum('estado', ['agendado', 'em_curso', 'concluido', 'cancelado'])->default('agendado');

            $table->softDeletes();
            $table->timestamps();

            // Índices para otimização
            $table->index('data');
            $table->index('estado');
            $table->index(['user_id', 'data']);
            $table->index(['entity_id', 'data']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
