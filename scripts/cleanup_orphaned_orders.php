<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LIMPEZA DE ENCOMENDAS DE CLIENTE ÓRFÃS ===\n\n";

// Encontrar encomendas com customer_id que não existe
$orphanedOrders = \App\Models\CustomerOrder::withTrashed()
    ->whereDoesntHave('customer')
    ->get();

echo "Encomendas órfãs encontradas: " . $orphanedOrders->count() . "\n\n";

if ($orphanedOrders->count() > 0) {
    echo "DETALHES:\n";
    foreach ($orphanedOrders as $order) {
        echo sprintf(
            "  ID: %-5d | Número: %-15s | Customer ID: %-5s | Estado: %-10s | Valor: %s€\n",
            $order->id,
            $order->number ?? 'N/A',
            $order->customer_id ?? 'NULL',
            $order->status ?? 'N/A',
            number_format($order->total_value ?? 0, 2, ',', '.')
        );
    }

    echo "\n";
    echo "Deseja eliminar permanentemente estas encomendas órfãs? (s/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim(strtolower($line)) === 's') {
        foreach ($orphanedOrders as $order) {
            // Eliminar items da encomenda primeiro
            $order->items()->forceDelete();
            // Eliminar a encomenda
            $order->forceDelete();
        }
        echo "\n✅ " . $orphanedOrders->count() . " encomendas órfãs eliminadas com sucesso!\n";
    } else {
        echo "\n❌ Operação cancelada.\n";
    }
} else {
    echo "✅ Não foram encontradas encomendas órfãs.\n";
}

// Verificar também encomendas de fornecedor
echo "\n\n=== LIMPEZA DE ENCOMENDAS DE FORNECEDOR ÓRFÃS ===\n\n";

$orphanedSupplierOrders = \App\Models\SupplierOrder::withTrashed()
    ->whereDoesntHave('supplier')
    ->get();

echo "Encomendas de fornecedor órfãs encontradas: " . $orphanedSupplierOrders->count() . "\n\n";

if ($orphanedSupplierOrders->count() > 0) {
    echo "DETALHES:\n";
    foreach ($orphanedSupplierOrders as $order) {
        echo sprintf(
            "  ID: %-5d | Número: %-15s | Supplier ID: %-5s | Estado: %-10s | Valor: %s€\n",
            $order->id,
            $order->number ?? 'N/A',
            $order->supplier_id ?? 'NULL',
            $order->status ?? 'N/A',
            number_format($order->total_value ?? 0, 2, ',', '.')
        );
    }

    echo "\n";
    echo "Deseja eliminar permanentemente estas encomendas de fornecedor órfãs? (s/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim(strtolower($line)) === 's') {
        foreach ($orphanedSupplierOrders as $order) {
            // Eliminar items da encomenda primeiro
            $order->items()->forceDelete();
            // Eliminar a encomenda
            $order->forceDelete();
        }
        echo "\n✅ " . $orphanedSupplierOrders->count() . " encomendas de fornecedor órfãs eliminadas com sucesso!\n";
    } else {
        echo "\n❌ Operação cancelada.\n";
    }
} else {
    echo "✅ Não foram encontradas encomendas de fornecedor órfãs.\n";
}
