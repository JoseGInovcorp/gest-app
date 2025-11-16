<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encomenda #{{ $order->number }}</title>
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
            border-bottom: 3px solid: #2563eb;
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
            color: #2563eb;
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
            color: #2563eb;
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
            color: #2563eb;
            font-size: 11pt;
            margin-bottom: 8px;
            border-bottom: 2px solid #2563eb;
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
        .order-details {
            display: table;
            width: 100%;
            margin-bottom: 25px;
            background-color: #f0f9ff;
            border: 1px solid #bfdbfe;
        }
        .detail-row {
            display: table-row;
        }
        .detail-label {
            display: table-cell;
            font-weight: bold;
            width: 25%;
            padding: 8px 12px;
            background-color: #dbeafe;
            color: #1e40af;
            border-bottom: 1px solid #bfdbfe;
        }
        .detail-value {
            display: table-cell;
            padding: 8px 12px;
            border-bottom: 1px solid #bfdbfe;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
        }
        .badge-draft {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-closed {
            background-color: #d1fae5;
            color: #065f46;
        }
        .items-section {
            margin-bottom: 25px;
        }
        .items-section h3 {
            color: #2563eb;
            font-size: 13pt;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 2px solid #2563eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            background-color: #2563eb;
            color: white;
        }
        th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9pt;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .article-name {
            font-weight: bold;
            color: #1f2937;
        }
        .article-ref {
            font-size: 8pt;
            color: #6b7280;
            font-style: italic;
        }
        .total-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f9ff;
            border: 2px solid #2563eb;
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
            color: #1e40af;
            padding-right: 20px;
        }
        .total-value {
            display: table-cell;
            text-align: right;
            font-size: 18pt;
            font-weight: bold;
            color: #2563eb;
            width: 200px;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
        }
        .notes h3 {
            margin-bottom: 10px;
            color: #92400e;
            font-size: 11pt;
        }
        .notes p {
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
                <h1>ENCOMENDA</h1>
                <div class="numero">#{{ $order->number }}</div>
            </div>
        </div>

        <!-- Parties Section -->
        <div class="parties-section">
            <div class="party-box">
                <h3>Cliente</h3>
                @if($order->customer)
                    <div style="display: table; width: 100%;">
                        <div style="display: table-cell; width: 50%; padding-right: 10px; vertical-align: top;">
                            <p class="name">{{ $order->customer->name }}</p>
                            @if($order->customer->nif)
                                <p><strong>NIF:</strong> {{ $order->customer->nif }}</p>
                            @endif
                            @if($order->customer->address)
                                <p>{{ $order->customer->address }}</p>
                            @endif
                        </div>
                        <div style="display: table-cell; width: 50%; padding-left: 10px; vertical-align: top;">
                            @if($order->customer->postal_code || $order->customer->city)
                                <p>{{ $order->customer->postal_code }} {{ $order->customer->city }}</p>
                            @endif
                            @if($order->customer->email)
                                <p><strong>Email:</strong> {{ $order->customer->email }}</p>
                            @endif
                            @if($order->customer->phone)
                                <p><strong>Tel:</strong> {{ $order->customer->phone }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <p>N/A</p>
                @endif
            </div>
        </div>

        <!-- Order Details -->
        <div class="order-details">
            @if($order->proposal_date)
            <div class="detail-row">
                <div class="detail-label">Data da Proposta:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($order->proposal_date)->format('d/m/Y') }}</div>
            </div>
            @endif
            @if($order->validity_date)
            <div class="detail-row">
                <div class="detail-label">Válida até:</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($order->validity_date)->format('d/m/Y') }}</div>
            </div>
            @endif
            <div class="detail-row">
                <div class="detail-label">Estado:</div>
                <div class="detail-value">
                    <span class="badge badge-{{ $order->status }}">
                        {{ $order->status === 'draft' ? 'Rascunho' : 'Fechado' }}
                    </span>
                </div>
            </div>
            @if($order->notes)
            <div class="detail-row">
                <div class="detail-label">Observações:</div>
                <div class="detail-value" style="white-space: pre-wrap;">{{ $order->notes }}</div>
            </div>
            @endif
        </div>

        <!-- Items Section -->
        <div class="items-section">
            <h3>Artigos / Serviços</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;" class="text-center">#</th>
                        <th style="width: 37%;">Artigo</th>
                        <th style="width: 20%;">Fornecedor</th>
                        <th style="width: 10%;" class="text-right">Qtd.</th>
                        <th style="width: 12%;" class="text-right">Preço Unit.</th>
                        <th style="width: 13%;" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="article-name">{{ $item->article->nome ?? 'N/A' }}</div>
                        @if($item->article && $item->article->referencia)
                            <div class="article-ref">Ref: {{ $item->article->referencia }}</div>
                        @endif
                    </td>
                    <td>{{ $item->supplier->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->quantity, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2, ',', '.') }} €</td>
                    <td class="text-right">{{ number_format($item->total, 2, ',', '.') }} €</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Section -->
        <div class="total-section">
            <div class="total-row">
                <span class="total-label">Total Geral (IVA incluído):</span>
                <span class="total-value">{{ number_format($order->total_value, 2, ',', '.') }} €</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Documento gerado em {{ now()->format('d/m/Y H:i') }}</p>
            @if($company)
                <p>{{ $company->name }} @if($company->nif) - NIF: {{ $company->nif }} @endif</p>
            @endif
            @if($order->validity_date)
            <p style="margin-top: 10px; font-style: italic; color: #9ca3af;">
                Esta encomenda é válida até {{ \Carbon\Carbon::parse($order->validity_date)->format('d/m/Y') }}
            </p>
            @endif
        </div>
    </div>
</body>
</html>
