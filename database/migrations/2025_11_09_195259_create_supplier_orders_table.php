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
        Schema::create('supplier_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique(); // EF-YYYY-#### (Encomenda Fornecedor)
            $table->date('order_date')->nullable(); // Data da encomenda
            $table->date('delivery_date')->nullable(); // Data de entrega prevista
            $table->foreignId('supplier_id')->constrained('entities')->onDelete('cascade');
            $table->foreignId('customer_order_id')->nullable()->constrained('customer_orders')->onDelete('set null'); // Referência à encomenda cliente origem
            $table->enum('status', ['draft', 'sent', 'confirmed', 'received', 'cancelled'])->default('draft');
            $table->decimal('total_value', 12, 2)->default(0); // Calculado automaticamente
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
        Schema::dropIfExists('supplier_orders');
    }
};
