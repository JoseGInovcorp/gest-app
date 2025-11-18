<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ClientAccount;

echo "Recalculando saldos da Conta Corrente de Clientes...\n\n";

// Buscar todos os clientes com movimentos
$entities = ClientAccount::select('entity_id')
    ->distinct()
    ->pluck('entity_id');

echo "Clientes com movimentos: {$entities->count()}\n\n";

foreach ($entities as $entityId) {
    echo "Recalculando cliente ID {$entityId}...\n";

    // Buscar todos os movimentos do cliente ordenados por data e ID
    $movements = ClientAccount::where('entity_id', $entityId)
        ->orderBy('data_movimento', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    $saldo = 0;

    foreach ($movements as $movement) {
        // Crédito aumenta a dívida (cliente deve mais)
        // Débito diminui a dívida (cliente pagou)
        if ($movement->tipo === 'credito') {
            $saldo += $movement->valor;
        } else {
            $saldo -= $movement->valor;
        }

        $movement->saldo_apos = $saldo;
        $movement->saveQuietly(); // Salvar sem disparar observers

        echo "  Movimento ID {$movement->id}: {$movement->tipo} {$movement->valor} EUR → Saldo: {$saldo} EUR\n";
    }

    echo "  ✅ Saldo final: {$saldo} EUR\n\n";
}

echo "════════════════════════════════════════════\n";
echo "Recálculo concluído!\n";
echo "════════════════════════════════════════════\n";
