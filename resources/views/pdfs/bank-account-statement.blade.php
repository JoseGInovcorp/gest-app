<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extrato Bancário - {{ $account->nome }}</title>
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

        .header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            color: #2563eb;
            font-size: 22pt;
            margin-bottom: 5px;
        }

        .header .account-name {
            font-size: 14pt;
            color: #666;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header .account-details {
            font-size: 9pt;
            color: #666;
        }

        .summary-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }

        .summary-box {
            display: table-cell;
            width: 24%;
            padding: 15px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            text-align: center;
            margin-right: 1%;
        }

        .summary-box:last-child {
            margin-right: 0;
        }

        .summary-box .label {
            font-size: 8pt;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-box .value {
            font-size: 14pt;
            font-weight: bold;
            color: #111827;
        }

        .summary-box.credito .value {
            color: #059669;
        }

        .summary-box.debito .value {
            color: #dc2626;
        }

        .summary-box.saldo .value {
            color: #2563eb;
        }

        .transactions-section {
            margin-top: 30px;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #f3f4f6;
        }

        table th {
            padding: 10px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
        }

        table th.right {
            text-align: right;
        }

        table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        table tbody tr:last-child {
            border-bottom: none;
        }

        table td {
            padding: 10px;
            font-size: 9pt;
            color: #4b5563;
        }

        table td.right {
            text-align: right;
        }

        table td.credito {
            color: #059669;
            font-weight: bold;
        }

        table td.debito {
            color: #dc2626;
            font-weight: bold;
        }

        .transaction-desc {
            font-weight: bold;
            color: #111827;
        }

        .transaction-meta {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 3px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge.recebimento {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge.pagamento {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge.transferencia {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge.outros {
            background-color: #f3f4f6;
            color: #374151;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>Extrato Bancário</h1>
            <div class="account-name">{{ $account->nome }}</div>
            <div class="account-details">
                <strong>Banco:</strong> {{ $account->banco }}<br>
                <strong>IBAN:</strong> {{ $account->iban }}<br>
                @if($account->swift_bic)
                <strong>SWIFT/BIC:</strong> {{ $account->swift_bic }}<br>
                @endif
                <strong>Data:</strong> {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-box">
                <div class="label">Saldo Inicial</div>
                <div class="value">{{ number_format($account->saldo_inicial, 2, ',', '.') }} {{ $account->moeda }}</div>
            </div>
            <div class="summary-box credito">
                <div class="label">Total Créditos</div>
                <div class="value">+ {{ number_format($totais['creditos'], 2, ',', '.') }} {{ $account->moeda }}</div>
            </div>
            <div class="summary-box debito">
                <div class="label">Total Débitos</div>
                <div class="value">- {{ number_format($totais['debitos'], 2, ',', '.') }} {{ $account->moeda }}</div>
            </div>
            <div class="summary-box saldo">
                <div class="label">Saldo Atual</div>
                <div class="value">{{ number_format($account->saldo_atual, 2, ',', '.') }} {{ $account->moeda }}</div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="transactions-section">
            <div class="section-title">
                Movimentos ({{ $account->transactions->count() }})
            </div>

            @if($account->transactions->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 12%;">Data</th>
                        <th style="width: 45%;">Descrição</th>
                        <th style="width: 15%;">Categoria</th>
                        <th class="right" style="width: 14%;">Valor</th>
                        <th class="right" style="width: 14%;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($account->transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->data_movimento->format('d/m/Y') }}</td>
                        <td>
                            <div class="transaction-desc">{{ $transaction->descricao }}</div>
                            @if($transaction->referencia || $transaction->observacoes)
                            <div class="transaction-meta">
                                @if($transaction->referencia)
                                Ref: {{ $transaction->referencia }}
                                @endif
                                @if($transaction->observacoes)
                                {{ $transaction->referencia ? ' • ' : '' }}{{ $transaction->observacoes }}
                                @endif
                            </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $transaction->categoria }}">
                                {{ ucfirst($transaction->categoria) }}
                            </span>
                        </td>
                        <td class="right {{ $transaction->tipo }}">
                            {{ $transaction->tipo === 'credito' ? '+' : '-' }}
                            {{ number_format($transaction->valor, 2, ',', '.') }} {{ $account->moeda }}
                        </td>
                        <td class="right">
                            {{ number_format($transaction->saldo_apos, 2, ',', '.') }} {{ $account->moeda }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="text-align: center; color: #9ca3af; padding: 40px 0;">
                Nenhum movimento registado nesta conta.
            </p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Documento gerado automaticamente em {{ now()->format('d/m/Y \à\s H:i') }}</p>
            <p>Este extrato é meramente informativo e não substitui documentos oficiais bancários.</p>
        </div>
    </div>
</body>

</html>