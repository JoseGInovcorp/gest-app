<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovativo de Pagamento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #0066cc;
            margin-bottom: 30px;
        }
        .header img {
            max-height: 80px;
            max-width: 200px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
            margin-top: 10px;
        }
        .content {
            padding: 20px 0;
        }
        .invoice-info {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .invoice-info p {
            margin: 8px 0;
        }
        .invoice-info strong {
            color: #0066cc;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .signature {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($company->logotipo && file_exists(storage_path('app/public/' . $company->logotipo)))
            <img src="{{ asset('storage/' . $company->logotipo) }}" alt="{{ $company->name }}">
        @else
            <div class="company-name">{{ $company->name }}</div>
        @endif
    </div>

    <div class="content">
        <p class="greeting">Estimado(a) {{ $invoice->supplier->name }},</p>

        <p>
            Enviamos em anexo o comprovativo de pagamento da fatura <strong>{{ $invoice->numero }}</strong>.
        </p>

        <div class="invoice-info">
            <p><strong>Número da Fatura:</strong> {{ $invoice->numero }}</p>
            <p><strong>Data da Fatura:</strong> {{ $invoice->data_fatura->format('d/m/Y') }}</p>
            <p><strong>Valor Total:</strong> {{ number_format($invoice->valor_total, 2, ',', '.') }}€</p>
            @if($invoice->supplierOrder)
                <p><strong>Encomenda Relacionada:</strong> {{ $invoice->supplierOrder->number }}</p>
            @endif
        </div>

        <p>
            Qualquer questão, entre em contacto connosco.
        </p>

        <div class="signature">
            <p>Cumprimentos,</p>
            <p><strong>{{ $company->name }}</strong></p>
            @if($company->morada)
                <p style="font-size: 14px; color: #666;">
                    {{ $company->morada }}<br>
                    {{ $company->codigo_postal }} {{ $company->localidade }}<br>
                    @if($company->nif)
                        NIF: {{ $company->nif }}
                    @endif
                </p>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda diretamente.</p>
        <p>&copy; {{ date('Y') }} {{ $company->name }}. Todos os direitos reservados.</p>
    </div>
</body>
</html>
