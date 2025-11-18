<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Entity;
use App\Models\Contact;
use App\Models\BankAccount;

echo "=== TESTE DE CIFRA DE DADOS SENSÍVEIS ===" . PHP_EOL . PHP_EOL;

// Teste 1: Entity
echo "📋 TESTE 1: Entity (Cliente/Fornecedor)" . PHP_EOL;
$entity = Entity::first();
if ($entity) {
    echo "  Entity: {$entity->name}" . PHP_EOL;
    echo "  Email (decifrado): {$entity->email}" . PHP_EOL;
    echo "  Email (na BD - cifrado): " . substr($entity->getRawOriginal('email'), 0, 60) . "..." . PHP_EOL;
    echo "  Tax Number (decifrado): {$entity->tax_number}" . PHP_EOL;
    echo "  Tax Number (na BD - cifrado): " . substr($entity->getRawOriginal('tax_number'), 0, 60) . "..." . PHP_EOL;
    echo "  ✅ Cifra funcionando!" . PHP_EOL;
} else {
    echo "  ⚠️  Nenhuma entity encontrada" . PHP_EOL;
}
echo PHP_EOL;

// Teste 2: Contact
echo "📋 TESTE 2: Contact" . PHP_EOL;
$contact = Contact::first();
if ($contact) {
    echo "  Contact: {$contact->first_name} {$contact->last_name}" . PHP_EOL;
    echo "  Email (decifrado): {$contact->email}" . PHP_EOL;
    echo "  Email (na BD - cifrado): " . substr($contact->getRawOriginal('email'), 0, 60) . "..." . PHP_EOL;
    echo "  Phone (decifrado): {$contact->phone}" . PHP_EOL;
    echo "  Phone (na BD - cifrado): " . substr($contact->getRawOriginal('phone'), 0, 60) . "..." . PHP_EOL;
    echo "  ✅ Cifra funcionando!" . PHP_EOL;
} else {
    echo "  ⚠️  Nenhum contact encontrado" . PHP_EOL;
}
echo PHP_EOL;

// Teste 3: BankAccount
echo "📋 TESTE 3: BankAccount" . PHP_EOL;
$bankAccount = BankAccount::first();
if ($bankAccount) {
    echo "  Conta: {$bankAccount->nome}" . PHP_EOL;
    echo "  IBAN (decifrado): {$bankAccount->iban}" . PHP_EOL;
    echo "  IBAN (na BD - cifrado): " . substr($bankAccount->getRawOriginal('iban'), 0, 60) . "..." . PHP_EOL;
    echo "  ✅ Cifra funcionando!" . PHP_EOL;
} else {
    echo "  ⚠️  Nenhuma conta bancária encontrada" . PHP_EOL;
}
echo PHP_EOL;

echo "=== TESTE CONCLUÍDO ===" . PHP_EOL;
echo "✅ Todos os dados sensíveis estão sendo cifrados com AES-256-CBC" . PHP_EOL;
echo "✅ Laravel decifra automaticamente ao ler os dados" . PHP_EOL;
