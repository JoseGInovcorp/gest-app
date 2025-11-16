<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura #{{ $invoice->numero }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
        }
        .container {
            padding: 30px;
        }
        .company-header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc2626;
        }
        .company-logo {
            display: table-cell;
            width: 30%;
            vertical-align: top;
        }
        .company-logo img {
            max-width: 150px;
            max-height: 80px;
        }
        .company-info {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            padding-left: 20px;
        }
        .company-info h2 {
            color: #dc2626;
            font-size: 16pt;
            margin-bottom: 5px;
        }
        .company-info p {
            font-size: 9pt;
            color: #666;
            margin: 2px 0;
        }
        .document-info {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: right;
        }
        .document-info h1 {
            color: #dc2626;
            font-size: 22pt;
            margin-bottom: 5px;
        }
        .document-info .numero {
            font-size: 14pt;
            color: #666;
            font-weight: bold;
        }
        .parties-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .party-box {
            display: table-cell;
            width: 48%;
            padding: 15px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .party-box:first-child {
            margin-right: 4%;
        }
        .party-box h3 {
            color: #dc2626;
            font-size: 11pt;
            margin-bottom: 8px;
            border-bottom: 2px solid #dc2626;
            padding-bottom: 5px;
        }
        .party-box p {
            margin: 3px 0;
            font-size: 9pt;
        }
        .party-box .name {
            font-weight: bold;
            font-size: 10pt;
            color: #1f2937;
        }
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
        }
        .detail-row {
            display: table-row;
        }
        .detail-label {
            display: table-cell;
            font-weight: bold;
            width: 25%;
            padding: 8px 12px;
            background-color: #fee2e2;
            color: #991b1b;
            border-bottom: 1px solid #fecaca;
        }
        .detail-value {
            display: table-cell;
            padding: 8px 12px;
            border-bottom: 1px solid #fecaca;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
        }
        .badge-pendente {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-paga {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-parcialmente_paga {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-vencida {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-cancelada {
            background-color: #e5e7eb;
            color: #374151;
        }
        .payment-info {
            margin-bottom: 25px;
            padding: 12px;
            background-color: #f0fdfa;
            border-left: 4px solid #14b8a6;
        }
        .payment-info h4 {
            color: #0f766e;
            font-size: 10pt;
            margin-bottom: 8px;
        }
        .payment-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .payment-label {
            display: table-cell;
            width: 40%;
            font-weight: bold;
            color: #0f766e;
        }
        .payment-value {
            display: table-cell;
            text-align: right;
            font-size: 11pt;
        }
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #fef2f2;
            border: 2px solid #dc2626;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .total-label {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            font-size: 11pt;
            color: #991b1b;
            padding-right: 20px;
        }
        .total-value {
            display: table-cell;
            text-align: right;
            font-size: 18pt;
            font-weight: bold;
            color: #dc2626;
            width: 200px;
        }
        .observacoes {
            margin-top: 30px;
            padding: 15px;
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
        }
        .observacoes h3 {
            margin-bottom: 10px;
            color: #92400e;
            font-size: 11pt;
        }
        .observacoes p {
            color: #78350f;
            font-size: 9pt;
            white-space: pre-wrap;
        }
        .footer {
            margin-top: 50px;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
        }
        .footer p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-logo">
                @if($company && $company->logo)
                    <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo">
                @endif
            </div>
            <div class="company-info">
                @if($company)
                    <h2>{{ $company->name ?? 'Empresa' }}</h2>
                    @if($company->address)
                        <p>{{ $company->address }}</p>
                    @endif
                    @if($company->postal_code || $company->city)
                        <p>{{ $company->postal_code }} {{ $company->city }}</p>
                    @endif
                    @if($company->nif)
                        <p><strong>NIF:</strong> {{ $company->nif }}</p>
                    @endif
                @endif
            </div>
            <div class="document-info">
                <h1>FATURA</h1>
                <div class="numero">#{{ $invoice->numero }}</div>
            </div>
        </div>

        <!-- Client Section -->
        <div class="parties-section">
            <div class="party-box">
                <h3>Cliente</h3>
                @if($invoice->entity)
                    <div style="display: table; width: 100%;">
                        <div style="display: table-cell; width: 50%; padding-right: 10px; vertical-align: top;">
                            <p class="name">{{ $invoice->entity->name }}</p>
                            @if($invoice->entity->nif)
                                <p><strong>NIF:</strong> {{ $invoice->entity->nif }}</p>
                            @endif
                            @if($invoice->entity->address)
                                <p>{{ $invoice->entity->address }}</p>
                            @endif
                        </div>
                        <div style="display: table-cell; width: 50%; padding-left: 10px; vertical-align: top;">
                            @if($invoice->entity->postal_code || $invoice->entity->city)
                                <p>{{ $invoice->entity->postal_code }} {{ $invoice->entity->city }}</p>
                            @endif
                            @if($invoice->entity->email)
                                <p><strong>Email:</strong> {{ $invoice->entity->email }}</p>
                            @endif
                            @if($invoice->entity->phone)
                                <p><strong>Tel:</strong> {{ $invoice->entity->phone }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p>N/A</p>
                @endif
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="detail-row">
                <div class="detail-label">Data da Fatura:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($invoice->data_fatura)->format('d/m/Y') }}</div>
            </div>
            @if($invoice->data_vencimento)
            <div class="detail-row">
                <div class="detail-label">Data de Vencimento:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($invoice->data_vencimento)->format('d/m/Y') }}</div>
            </div>
            @endif
            <div class="detail-row">
                <div class="detail-label">Estado:</div>
                <div class="detail-value">
                    <span class="badge badge-{{ $invoice->estado }}">
                        @if($invoice->estado === 'pendente') Pendente
                        @elseif($invoice->estado === 'paga') Paga
                        @elseif($invoice->estado === 'parcialmente_paga') Parcialmente Paga
                        @elseif($invoice->estado === 'vencida') Vencida
                        @else Cancelada
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        @if($invoice->valor_pago > 0 || $invoice->estado !== 'paga')
        <div class="payment-info">
            <h4>Informação de Pagamento</h4>
            <div class="payment-row">
                <span class="payment-label">Valor Total:</span>
                <span class="payment-value">{{ number_format($invoice->valor_total, 2, ',', '.') }} €</span>
            </div>
            @if($invoice->valor_pago > 0)
            <div class="payment-row">
                <span class="payment-label">Valor Pago:</span>
                <span class="payment-value" style="color: #059669;">{{ number_format($invoice->valor_pago, 2, ',', '.') }} €</span>
            </div>
            @endif
            @if($invoice->valor_devido > 0)
            <div class="payment-row">
                <span class="payment-label">Valor em Dívida:</span>
                <span class="payment-value" style="color: #dc2626; font-weight: bold;">{{ number_format($invoice->valor_devido, 2, ',', '.') }} €</span>
            </div>
            @endif
        </div>
        @endif

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Total da Fatura (IVA incluído):</span>
                <span class="total-value">{{ number_format($invoice->valor_total, 2, ',', '.') }} €</span>
            </div>
        </div>

        <!-- Observações -->
        @if($invoice->observacoes)
        <div class="observacoes">
            <h3>Observações</h3>
            <p>{{ $invoice->observacoes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Documento gerado em {{ now()->format('d/m/Y H:i') }}</p>
            @if($company)
                <p>{{ $company->name }} @if($company->nif) - NIF: {{ $company->nif }} @endif</p>
            @endif
            @if($invoice->data_vencimento && $invoice->estado !== 'paga')
            <p style="margin-top: 10px; font-style: italic; color: #9ca3af;">
                Esta fatura deve ser paga até {{ \Carbon\Carbon::parse($invoice->data_vencimento)->format('d/m/Y') }}
            </p>
            @endif
        </div>
    </div>
</body>
</html>
