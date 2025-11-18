<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CustomerOrder;
use App\Models\ClientAccount;

echo "Testando criação de encomenda em draft...\n\n";

// Limpar teste anterior se existir
CustomerOrder::where('number', 'TEST-DRAFT-001')->forceDelete();
ClientAccount::where('referencia', 'TEST-DRAFT-001')->delete();

// Criar encomenda em draft
$order = CustomerOrder::create([
    'number' => 'TEST-DRAFT-001',
    'customer_id' => 43,
    'proposal_date' => now(),
    'validity_date' => now()->addDays(3),
    'status' => 'draft',
    'total_value' => 100.00,
    'notes' => 'Teste de encomenda draft'
]);

echo "✓ Encomenda criada:\n";
echo "  ID: {$order->id}\n";
echo "  Número: {$order->number}\n";
echo "  Status: {$order->status}\n\n";

// Verificar se criou movimento na conta corrente
$movimento = ClientAccount::where('referencia', 'TEST-DRAFT-001')->first();

if ($movimento) {
    echo "✓ OK: Movimento de CRÉDITO criado na conta corrente\n";
    echo "  Tipo: {$movimento->tipo}\n";
    echo "  Valor: {$movimento->valor}\n";
    echo "  (Cliente deve este valor à empresa)\n\n";
} else {
    echo "✗ ERRO: Nenhum movimento criado (deveria criar!)\n\n";
}

// Verificar se criou work order
if ($order->workOrder) {
    echo "✗ ERRO: Work Order foi criada (não deveria para draft!)\n";
} else {
    echo "✓ OK: Nenhuma Work Order criada (correto para draft)\n";
}

// Limpar teste
$order->delete();
echo "\n✓ Encomenda de teste removida\n";
