<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Entity;
use App\Models\ClientAccount;
use App\Models\Invoice;

echo "=== CRIAR PAGAMENTO DE TESTE ===\n\n";

// Obter primeiro cliente disponível
$client = Entity::whereIn('type', ['client', 'both'])->first();

if (!$client) {
    echo "❌ Erro: Nenhum cliente encontrado na base de dados.\n";
    exit(1);
}

echo "Cliente selecionado: {$client->name} (ID: {$client->id})\n\n";

// Criar a fatura
echo "Criando fatura...\n";
$invoice = Invoice::create([
    'entity_id' => $client->id,
    'data_fatura' => now(),
    'data_vencimento' => null,
    'valor_total' => 500.00,
    'valor_pago' => 500.00,
    'estado' => 'paga',
    'observacoes' => 'Fatura de teste gerada automaticamente',
]);

echo "✅ Fatura criada: {$invoice->numero}\n\n";

// Criar movimento de pagamento
echo "Criando movimento de pagamento...\n";
$movement = ClientAccount::create([
    'entity_id' => $client->id,
    'data_movimento' => now(),
    'tipo' => 'credito',
    'valor' => 500.00,
    'descricao' => 'Pagamento de teste',
    'categoria' => 'pagamento',
    'referencia' => $invoice->numero,
    'invoice_id' => $invoice->id,
    'observacoes' => 'Movimento de teste para validar download de fatura',
]);

echo "✅ Movimento criado (ID: {$movement->id})\n\n";

echo "=== TESTE COMPLETO ===\n";
echo "Agora o ícone de download PDF deve aparecer no índice de Conta Corrente Clientes!\n";
echo "URL: /client-accounts?entity_id={$client->id}\n";
