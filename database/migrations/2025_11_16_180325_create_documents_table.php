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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome do documento
            $table->string('original_filename'); // Nome original do ficheiro
            $table->string('file_path'); // Caminho do ficheiro no storage
            $table->unsignedBigInteger('file_size'); // Tamanho em bytes
            $table->string('mime_type'); // Tipo MIME
            $table->string('category'); // contrato, fatura, proposta, identificacao, certificado, outros
            $table->string('module')->nullable(); // clients, suppliers, proposals, orders, invoices, etc.
            $table->string('documentable_type')->nullable(); // Polymorphic relation
            $table->unsignedBigInteger('documentable_id')->nullable(); // Polymorphic relation
            $table->text('description')->nullable(); // Descrição do documento
            $table->json('tags')->nullable(); // Tags para pesquisa
            $table->integer('version')->default(1); // Número da versão
            $table->foreignId('parent_id')->nullable()->constrained('documents')->onDelete('set null'); // Para versionamento
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // Quem fez upload
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            $table->timestamp('expires_at')->nullable(); // Data de expiração
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['documentable_type', 'documentable_id']);
            $table->index(['category', 'module']);
            $table->index('status');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
