<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BankTransaction;
use App\Models\CustomerOrder;

echo "Atualizando transações bancárias com nome do cliente...\n\n";

// Buscar transações de recebimento que não têm nome do cliente
$transactions = BankTransaction::where('categoria', 'recebimento')
    ->where('tipo', 'credito')
    ->whereNotNull('referencia')
    ->get();

$updated = 0;

foreach ($transactions as $transaction) {
    // Extrair número da encomenda da referência
    $orderNumber = $transaction->referencia;

    // Buscar encomenda
    $order = CustomerOrder::where('number', $orderNumber)
        ->with('customer')
        ->first();

    if ($order && $order->customer) {
        // Atualizar descrição e observações
        $transaction->update([
            'descricao' => "Recebimento Encomenda {$orderNumber} - {$order->customer->name}",
            'observacoes' => "Cliente: {$order->customer->name}",
        ]);

        echo "✅ Transação ID {$transaction->id} atualizada\n";
        echo "   Encomenda: {$orderNumber}\n";
        echo "   Cliente: {$order->customer->name}\n\n";
        $updated++;
    }
}

echo "\nTotal atualizado: {$updated} transações\n";
