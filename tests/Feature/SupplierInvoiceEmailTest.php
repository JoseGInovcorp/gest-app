<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Entity;
use App\Models\Company;
use App\Models\SupplierInvoice;
use App\Models\SupplierOrder;
use App\Mail\PaymentProofMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SupplierInvoiceEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $supplier;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar permissões
        $permissions = ['create', 'read', 'update', 'delete'];
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create([
                'name' => 'supplier-invoices.' . $permission,
                'guard_name' => 'web'
            ]);
        }

        // Criar utilizador com permissões
        $this->user = User::factory()->create();
        $this->user->givePermissionTo([
            'supplier-invoices.create',
            'supplier-invoices.read',
            'supplier-invoices.update',
            'supplier-invoices.delete',
        ]);

        // Criar fornecedor com email
        $this->supplier = Entity::create([
            'type' => 'supplier',
            'name' => 'Fornecedor Teste',
            'number' => 'FOR-001',
            'email' => 'fornecedor@teste.com',
            'nif' => '123456789',
        ]);

        // Criar empresa
        $this->company = Company::create([
            'name' => 'Empresa Teste Lda',
            'nif' => '123456789',
            'morada' => 'Rua Teste, 123',
            'codigo_postal' => '1000-001',
            'localidade' => 'Lisboa',
        ]);

        // Configurar storage fake
        Storage::fake('public');
    }

    /** @test */
    public function email_is_sent_when_payment_proof_is_uploaded()
    {
        // Fake do Mail para interceptar emails
        Mail::fake();

        // Criar uma fatura pendente
        $invoice = SupplierInvoice::create([
            'numero' => 'FF-2025-0001',
            'data_fatura' => now(),
            'data_vencimento' => now()->addDays(30),
            'supplier_id' => $this->supplier->id,
            'valor_total' => 1500.00,
            'estado' => 'pendente',
        ]);

        // Criar ficheiro fake para comprovativo
        $file = UploadedFile::fake()->create('comprovativo.pdf', 500);

        // Enviar comprovativo
        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.send-payment-proof', $invoice->id), [
                'comprovativo' => $file,
            ]);

        // Verificar que o email foi enviado
        Mail::assertSent(PaymentProofMail::class, function ($mail) use ($invoice) {
            return $mail->hasTo($this->supplier->email) &&
                $mail->invoice->id === $invoice->id;
        });

        // Verificar que apenas 1 email foi enviado
        Mail::assertSent(PaymentProofMail::class, 1);

        $response->assertRedirect();
    }

    /** @test */
    public function email_contains_correct_invoice_data()
    {
        Mail::fake();

        $invoice = SupplierInvoice::create([
            'numero' => 'FF-2025-0099',
            'data_fatura' => '2025-11-11',
            'data_vencimento' => '2025-12-11',
            'supplier_id' => $this->supplier->id,
            'valor_total' => 2500.50,
            'estado' => 'paga',
        ]);

        $file = UploadedFile::fake()->create('proof.pdf', 500);

        $this->actingAs($this->user)
            ->post(route('supplier-invoices.send-payment-proof', $invoice->id), [
                'comprovativo' => $file,
            ]);

        Mail::assertSent(PaymentProofMail::class, function ($mail) use ($invoice) {
            // Verificar dados do email
            return $mail->invoice->numero === 'FF-2025-0099' &&
                $mail->invoice->valor_total == 2500.50 &&
                $mail->company->name === 'Empresa Teste Lda';
        });
    }

    /** @test */
    public function email_has_pdf_attachment()
    {
        Mail::fake();

        $invoice = SupplierInvoice::create([
            'numero' => 'FF-2025-0001',
            'data_fatura' => now(),
            'data_vencimento' => now()->addDays(30),
            'supplier_id' => $this->supplier->id,
            'valor_total' => 1000.00,
            'estado' => 'pendente',
        ]);

        $file = UploadedFile::fake()->create('comprovativo.pdf', 500, 'application/pdf');

        $this->actingAs($this->user)
            ->post(route('supplier-invoices.send-payment-proof', $invoice->id), [
                'comprovativo' => $file,
            ]);

        Mail::assertSent(PaymentProofMail::class, function ($mail) {
            // Verificar que o email tem anexos
            $attachments = $mail->attachments();

            return count($attachments) === 1;
        });
    }

    /** @test */
    public function email_has_correct_subject()
    {
        Mail::fake();

        $invoice = SupplierInvoice::create([
            'numero' => 'FF-2025-0042',
            'data_fatura' => now(),
            'data_vencimento' => now()->addDays(30),
            'supplier_id' => $this->supplier->id,
            'valor_total' => 1000.00,
            'estado' => 'pendente',
        ]);

        $file = UploadedFile::fake()->create('proof.pdf', 500);

        $this->actingAs($this->user)
            ->post(route('supplier-invoices.send-payment-proof', $invoice->id), [
                'comprovativo' => $file,
            ]);

        Mail::assertSent(PaymentProofMail::class, function ($mail) {
            $envelope = $mail->envelope();

            return $envelope->subject === 'Comprovativo de Pagamento - Fatura FF-2025-0042';
        });
    }

    /** @test */
    public function file_is_stored_when_sending_proof()
    {
        Mail::fake();

        $invoice = SupplierInvoice::create([
            'numero' => 'FF-2025-0001',
            'data_fatura' => now(),
            'data_vencimento' => now()->addDays(30),
            'supplier_id' => $this->supplier->id,
            'valor_total' => 1000.00,
            'estado' => 'pendente',
        ]);

        $file = UploadedFile::fake()->create('comprovativo.pdf', 500);

        $this->actingAs($this->user)
            ->post(route('supplier-invoices.send-payment-proof', $invoice->id), [
                'comprovativo' => $file,
            ]);

        // Verificar que o ficheiro foi guardado
        $invoice->refresh();

        $this->assertNotNull($invoice->comprovativo_pagamento);
        $this->assertTrue(Storage::disk('public')->exists($invoice->comprovativo_pagamento));
    }

    /** @test */
    public function email_is_not_sent_without_file()
    {
        Mail::fake();

        $invoice = SupplierInvoice::create([
            'numero' => 'FF-2025-0001',
            'data_fatura' => now(),
            'data_vencimento' => now()->addDays(30),
            'supplier_id' => $this->supplier->id,
            'valor_total' => 1000.00,
            'estado' => 'pendente',
        ]);

        // Tentar enviar sem ficheiro
        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.send-payment-proof', $invoice->id), [
                'comprovativo' => null,
            ]);

        // Verificar que nenhum email foi enviado
        Mail::assertNothingSent();

        // Verificar que retornou erro de validação
        $response->assertSessionHasErrors('comprovativo');
    }

    /** @test */
    public function only_pdf_jpg_png_files_are_accepted()
    {
        Mail::fake();

        $invoice = SupplierInvoice::create([
            'numero' => 'FF-2025-0001',
            'data_fatura' => now(),
            'data_vencimento' => now()->addDays(30),
            'supplier_id' => $this->supplier->id,
            'valor_total' => 1000.00,
            'estado' => 'pendente',
        ]);

        // Tentar enviar ficheiro .txt (não permitido)
        $file = UploadedFile::fake()->create('documento.txt', 500);

        $response = $this->actingAs($this->user)
            ->post(route('supplier-invoices.send-payment-proof', $invoice->id), [
                'comprovativo' => $file,
            ]);

        // Verificar que nenhum email foi enviado
        Mail::assertNothingSent();

        // Verificar erro de validação
        $response->assertSessionHasErrors('comprovativo');
    }

    /** @test */
    public function email_includes_supplier_order_if_exists()
    {
        Mail::fake();

        // Criar encomenda fornecedor
        $order = SupplierOrder::create([
            'number' => 'EF-2025-0001',
            'order_date' => now(),
            'supplier_id' => $this->supplier->id,
            'status' => 'confirmed',
            'total_value' => 1500.00,
        ]);

        $invoice = SupplierInvoice::create([
            'numero' => 'FF-2025-0001',
            'data_fatura' => now(),
            'data_vencimento' => now()->addDays(30),
            'supplier_id' => $this->supplier->id,
            'supplier_order_id' => $order->id,
            'valor_total' => 1500.00,
            'estado' => 'pendente',
        ]);

        $file = UploadedFile::fake()->create('proof.pdf', 500);

        $this->actingAs($this->user)
            ->post(route('supplier-invoices.send-payment-proof', $invoice->id), [
                'comprovativo' => $file,
            ]);

        Mail::assertSent(PaymentProofMail::class, function ($mail) use ($order) {
            // Verificar que a encomenda está presente
            return $mail->invoice->supplierOrder !== null &&
                $mail->invoice->supplierOrder->number === 'EF-2025-0001';
        });
    }

    /** @test */
    public function user_needs_permission_to_send_proof()
    {
        Mail::fake();

        // Criar utilizador SEM permissões
        $userWithoutPermission = User::factory()->create();

        $invoice = SupplierInvoice::create([
            'numero' => 'FF-2025-0001',
            'data_fatura' => now(),
            'data_vencimento' => now()->addDays(30),
            'supplier_id' => $this->supplier->id,
            'valor_total' => 1000.00,
            'estado' => 'pendente',
        ]);

        $file = UploadedFile::fake()->create('proof.pdf', 500);

        $response = $this->actingAs($userWithoutPermission)
            ->post(route('supplier-invoices.send-payment-proof', $invoice->id), [
                'comprovativo' => $file,
            ]);

        // Verificar que nenhum email foi enviado
        Mail::assertNothingSent();

        // Verificar que retornou 403 Forbidden
        $response->assertStatus(403);
    }
}
