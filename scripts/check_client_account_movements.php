<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\ClientAccount;

echo "Movimentos na Conta Corrente de Clientes:\n\n";

$movements = ClientAccount::with('entity')->orderBy('data_movimento', 'desc')->get();

foreach ($movements as $mov) {
    echo "ID: {$mov->id}\n";
    echo "Cliente: " . ($mov->entity ? $mov->entity->name : 'N/A') . "\n";
    echo "Data: {$mov->data_movimento}\n";
    echo "Tipo: {$mov->tipo}\n";
    echo "Valor: {$mov->valor}\n";
    echo "Categoria: {$mov->categoria}\n";
    echo "Descrição: {$mov->descricao}\n";
    echo "Referência: {$mov->referencia}\n";
    echo "Saldo Após: " . ($mov->saldo_apos ?? 'N/A') . "\n";
    echo "---\n";
}

echo "\nTotal de movimentos: " . $movements->count() . "\n";
