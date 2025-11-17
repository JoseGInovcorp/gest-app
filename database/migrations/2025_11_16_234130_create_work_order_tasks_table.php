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
        Schema::create('work_order_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->string('task_type'); // VALIDATE_STOCK, CREATE_SUPPLIER_ORDER, etc
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->string('assigned_group')->nullable(); // role name
            $table->enum('status', ['pendente', 'em_progresso', 'concluida', 'cancelada'])->default('pendente');
            $table->integer('sequence_order')->default(0);
            $table->foreignId('depends_on_task_id')->nullable()->constrained('work_order_tasks')->onDelete('set null');
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_tasks');
    }
};
