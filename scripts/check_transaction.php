<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BankTransaction;

$transaction = BankTransaction::latest()->first();

if ($transaction) {
    echo "Última transação:\n";
    echo "ID: {$transaction->id}\n";
    echo "Descrição: {$transaction->descricao}\n";
    echo "Observações: {$transaction->observacoes}\n";
    echo "Valor: {$transaction->valor}\n";
    echo "Tipo: {$transaction->tipo}\n";
    echo "\n";
} else {
    echo "Nenhuma transação encontrada.\n";
}
