<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ClientAccount;

echo "Corrigindo tipo de movimento na Conta Corrente de Clientes...\n\n";

// Buscar movimentos de pagamento que estão como crédito (incorreto)
$movements = ClientAccount::where('categoria', 'pagamento')
    ->where('tipo', 'credito')
    ->get();

$corrected = 0;

foreach ($movements as $movement) {
    echo "Corrigindo movimento ID {$movement->id}\n";
    echo "  Cliente: " . ($movement->entity ? $movement->entity->name : 'N/A') . "\n";
    echo "  Descrição: {$movement->descricao}\n";
    echo "  Tipo: crédito → débito\n";

    // Atualizar o tipo de crédito para débito
    $movement->update(['tipo' => 'debito']);

    echo "  ✅ Corrigido!\n\n";
    $corrected++;
}

echo "Total de movimentos corrigidos: {$corrected}\n";
