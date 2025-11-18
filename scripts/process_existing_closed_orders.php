<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CustomerOrder;
use App\Models\SupplierOrder;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\ClientAccount;

echo "Processando encomendas fechadas existentes...\n\n";

// Buscar conta bancária principal
$bankAccount = BankAccount::where('nome', 'Conta Corrente Principal')
    ->orWhere('tipo', 'corrente')
    ->first();

if (!$bankAccount) {
    echo "❌ Conta bancária principal não encontrada!\n";
    exit(1);
}

echo "✅ Conta bancária: {$bankAccount->nome} (ID: {$bankAccount->id})\n";
echo "   Saldo atual: " . number_format($bankAccount->saldo_atual, 2, ',', '.') . " EUR\n\n";

// Processar encomendas de clientes fechadas
echo "--- ENCOMENDAS DE CLIENTES FECHADAS ---\n";
$customerOrders = CustomerOrder::where('status', 'closed')
    ->with('customer')
    ->get();

$processedCustomer = 0;
foreach ($customerOrders as $order) {
    // Verificar se já existe movimento bancário para esta encomenda
    $exists = BankTransaction::where('referencia', $order->number)
        ->where('categoria', 'recebimento')
        ->exists();

    if ($exists) {
        echo "⏭️  Encomenda {$order->number} - já processada\n";
        continue;
    }

    // Criar movimento de crédito na conta corrente do cliente
    ClientAccount::create([
        'entity_id' => $order->customer_id,
        'data_movimento' => now(),
        'tipo' => 'debito',
        'valor' => $order->total_value,
        'descricao' => "Pagamento Encomenda {$order->number}",
        'categoria' => 'pagamento',
        'referencia' => $order->number,
    ]);

    // Criar movimento bancário (crédito - entrada de dinheiro)
    BankTransaction::create([
        'bank_account_id' => $bankAccount->id,
        'data_movimento' => now(),
        'descricao' => "Recebimento Encomenda {$order->number} - {$order->customer->name}",
        'tipo' => 'credito',
        'valor' => $order->total_value,
        'referencia' => $order->number,
        'categoria' => 'recebimento',
        'observacoes' => "Cliente: {$order->customer->name}",
    ]);

    echo "✅ Encomenda {$order->number} - " . number_format($order->total_value, 2, ',', '.') . " EUR\n";
    echo "   Cliente: {$order->customer->name}\n";
    $processedCustomer++;
}

echo "\nTotal processado (clientes): {$processedCustomer} encomendas\n\n";

// Processar encomendas de fornecedores fechadas
echo "--- ENCOMENDAS DE FORNECEDORES FECHADAS ---\n";
$supplierOrders = SupplierOrder::where('status', 'closed')
    ->with('supplier')
    ->get();

$processedSupplier = 0;
foreach ($supplierOrders as $order) {
    // Verificar se já existe movimento bancário para esta encomenda
    $exists = BankTransaction::where('referencia', $order->number)
        ->where('categoria', 'pagamento')
        ->exists();

    if ($exists) {
        echo "⏭️  Encomenda {$order->number} - já processada\n";
        continue;
    }

    // Criar movimento bancário (débito - saída de dinheiro para fornecedor)
    BankTransaction::create([
        'bank_account_id' => $bankAccount->id,
        'data_movimento' => now(),
        'descricao' => "Pagamento Encomenda {$order->number} - {$order->supplier->name}",
        'tipo' => 'debito',
        'valor' => $order->total_value,
        'referencia' => $order->number,
        'categoria' => 'pagamento',
        'observacoes' => "Fornecedor: {$order->supplier->nome}",
    ]);

    echo "✅ Encomenda {$order->number} - " . number_format($order->total_value, 2, ',', '.') . " EUR\n";
    echo "   Fornecedor: {$order->supplier->nome}\n";
    $processedSupplier++;
}

echo "\nTotal processado (fornecedores): {$processedSupplier} encomendas\n\n";

// Mostrar saldo atualizado
$bankAccount->refresh();
echo "💰 Saldo atualizado: " . number_format($bankAccount->saldo_atual, 2, ',', '.') . " EUR\n";
echo "   Diferença: " . number_format($bankAccount->saldo_atual - 25000, 2, ',', '.') . " EUR\n";

echo "\n✅ Processo concluído!\n";
