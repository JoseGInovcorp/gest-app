<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\ClientAccount;
use App\Models\Entity;

echo "=== Teste de Movimento Conta Corrente Cliente -> Conta Bancária ===\n\n";

// 1. Verificar contas bancárias ativas
echo "1. Verificando contas bancárias ativas:\n";
$bankAccounts = BankAccount::where('estado', 'ativa')->get(['id', 'nome', 'estado']);
if ($bankAccounts->isEmpty()) {
    echo "   ❌ Nenhuma conta bancária ativa encontrada!\n";
    exit(1);
} else {
    foreach ($bankAccounts as $account) {
        echo "   ✓ Conta #{$account->id}: {$account->nome} (Estado: {$account->estado})\n";
    }
}

// 2. Verificar último movimento de conta corrente cliente
echo "\n2. Últimos movimentos de conta corrente cliente:\n";
$lastMovements = ClientAccount::with('entity')
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

foreach ($lastMovements as $movement) {
    $clientName = $movement->entity ? $movement->entity->name : 'N/A';
    echo "   - ID: {$movement->id} | Cliente: {$clientName} | Tipo: {$movement->tipo} | ";
    echo "Categoria: {$movement->categoria} | Valor: {$movement->valor}€ | ";
    echo "Data: {$movement->data_movimento}\n";
}

// 3. Verificar se existem transações bancárias correspondentes
echo "\n3. Últimas transações bancárias:\n";
$lastBankTransactions = BankTransaction::with('bankAccount')
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

foreach ($lastBankTransactions as $transaction) {
    $accountName = $transaction->bankAccount ? $transaction->bankAccount->nome : 'N/A';
    echo "   - ID: {$transaction->id} | Conta: {$accountName} | Tipo: {$transaction->tipo} | ";
    echo "Categoria: {$transaction->categoria} | Valor: {$transaction->valor}€ | ";
    echo "Descrição: {$transaction->descricao}\n";
}

echo "\n=== Teste concluído ===\n";
