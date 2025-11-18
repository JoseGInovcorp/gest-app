<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerOrder;

$order = CustomerOrder::where('number', 'EC-2025-0001')
    ->with('customer')
    ->first();

if ($order) {
    echo "Encomenda: {$order->number}\n";
    echo "Customer ID: {$order->customer_id}\n";
    echo "Customer: " . ($order->customer ? $order->customer->nome : 'NULL') . "\n";
    echo "Total: {$order->total_value}\n";

    // Verificar a entidade diretamente
    $entity = \App\Models\Entity::find($order->customer_id);
    if ($entity) {
        echo "\nEntidade encontrada:\n";
        echo "ID: {$entity->id}\n";
        echo "Nome: {$entity->nome}\n";
        echo "Name: " . ($entity->name ?? 'NULL') . "\n";
    }
}
