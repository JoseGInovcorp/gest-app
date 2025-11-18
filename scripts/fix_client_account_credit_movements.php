<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerOrder;
use App\Models\ClientAccount;

echo "Corrigindo movimentos de conta corrente de clientes...\n\n";

// Buscar todas as encomendas de clientes
$orders = CustomerOrder::with('customer')->get();

echo "Total de encomendas encontradas: {$orders->count()}\n\n";

$added = 0;

foreach ($orders as $order) {
    echo "Verificando encomenda {$order->number}...\n";

    // Verificar se já existe movimento de crédito (fatura) para esta encomenda
    $creditMovement = ClientAccount::where('entity_id', $order->customer_id)
        ->where('referencia', $order->number)
        ->where('tipo', 'credito')
        ->where('categoria', 'fatura')
        ->first();

    if (!$creditMovement) {
        // Criar movimento de crédito (fatura) - cliente ficou a dever
        ClientAccount::create([
            'entity_id' => $order->customer_id,
            'data_movimento' => $order->created_at,
            'tipo' => 'credito',
            'valor' => $order->total_value,
            'descricao' => "Encomenda {$order->number}",
            'categoria' => 'fatura',
            'referencia' => $order->number,
        ]);

        echo "  ✅ Adicionado movimento de crédito (fatura)\n";
        echo "     Cliente: {$order->customer->name}\n";
        echo "     Valor: {$order->total_value} EUR\n";
        $added++;
    } else {
        echo "  ⏭️  Movimento de crédito já existe\n";
    }

    echo "\n";
}

echo "════════════════════════════════════════════\n";
echo "Total de movimentos adicionados: {$added}\n";
echo "════════════════════════════════════════════\n";
