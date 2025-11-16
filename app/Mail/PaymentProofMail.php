<?php

namespace App\Mail;

use App\Models\SupplierInvoice;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentProofMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public SupplierInvoice $invoice,
        public Company $company,
        public string $proofPath  // Agora é o path relativo (supplier_invoices/proofs/xxx.pdf)
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Comprovativo de Pagamento - Fatura {$this->invoice->numero}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-proof',
            with: [
                'invoice' => $this->invoice,
                'company' => $this->company,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Verificar se o ficheiro existe no disco privado
        if (!Storage::disk('private')->exists($this->proofPath)) {
            Log::error('PaymentProofMail: Ficheiro não encontrado', [
                'path' => $this->proofPath,
                'invoice_id' => $this->invoice->id,
            ]);
            return [];
        }

        // Obter conteúdo do ficheiro
        $fileContents = Storage::disk('private')->get($this->proofPath);

        // Obter extensão real do ficheiro
        $extension = pathinfo($this->proofPath, PATHINFO_EXTENSION);

        // Determinar MIME type baseado na extensão
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        $mimeType = $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';

        Log::info('PaymentProofMail: Anexando ficheiro', [
            'path' => $this->proofPath,
            'exists' => Storage::disk('private')->exists($this->proofPath),
            'size' => Storage::disk('private')->size($this->proofPath),
            'extension' => $extension,
            'mime' => $mimeType,
        ]);

        return [
            Attachment::fromData(fn() => $fileContents, 'comprovativo_pagamento_' . $this->invoice->numero . '.' . $extension)
                ->withMime($mimeType),
        ];
    }
}
