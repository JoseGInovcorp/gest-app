<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Entity;
use App\Models\Contact;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== MIGRAÇÃO DE DADOS PARA CIFRA ===" . PHP_EOL . PHP_EOL;
echo "Este script vai cifrar todos os dados sensíveis existentes na base de dados." . PHP_EOL;
echo "Os dados em texto simples serão convertidos para formato cifrado AES-256-CBC." . PHP_EOL . PHP_EOL;

$totalProcessed = 0;

try {
    // Desativar temporariamente os casts de cifra para ler valores originais
    echo "📋 MIGRANDO ENTITIES..." . PHP_EOL;

    DB::statement('SET @OLD_SQL_SAFE_UPDATES=@@SQL_SAFE_UPDATES, SQL_SAFE_UPDATES=0');

    $entities = DB::table('entities')->get();
    foreach ($entities as $entity) {
        $updates = [];

        // Apenas cifrar se não estiver já cifrado (texto simples)
        if ($entity->tax_number && !str_starts_with($entity->tax_number, 'eyJ')) {
            $updates['tax_number'] = encrypt($entity->tax_number);
        }
        if ($entity->phone && !str_starts_with($entity->phone, 'eyJ')) {
            $updates['phone'] = encrypt($entity->phone);
        }
        if ($entity->mobile && !str_starts_with($entity->mobile, 'eyJ')) {
            $updates['mobile'] = encrypt($entity->mobile);
        }
        if ($entity->email && !str_starts_with($entity->email, 'eyJ')) {
            $updates['email'] = encrypt($entity->email);
        }
        if ($entity->iban && !str_starts_with($entity->iban, 'eyJ')) {
            $updates['iban'] = encrypt($entity->iban);
        }

        if (!empty($updates)) {
            DB::table('entities')->where('id', $entity->id)->update($updates);
            $totalProcessed++;
        }
    }
    echo "  ✅ {$totalProcessed} entities processadas" . PHP_EOL . PHP_EOL;

    // Contacts
    echo "📋 MIGRANDO CONTACTS..." . PHP_EOL;
    $contactsProcessed = 0;
    $contacts = DB::table('contacts')->get();
    foreach ($contacts as $contact) {
        $updates = [];

        if ($contact->phone && !str_starts_with($contact->phone, 'eyJ')) {
            $updates['phone'] = encrypt($contact->phone);
        }
        if ($contact->mobile && !str_starts_with($contact->mobile, 'eyJ')) {
            $updates['mobile'] = encrypt($contact->mobile);
        }
        if ($contact->email && !str_starts_with($contact->email, 'eyJ')) {
            $updates['email'] = encrypt($contact->email);
        }

        if (!empty($updates)) {
            DB::table('contacts')->where('id', $contact->id)->update($updates);
            $contactsProcessed++;
        }
    }
    echo "  ✅ {$contactsProcessed} contacts processados" . PHP_EOL . PHP_EOL;

    // BankAccounts
    echo "📋 MIGRANDO BANK ACCOUNTS..." . PHP_EOL;
    $accountsProcessed = 0;
    $accounts = DB::table('bank_accounts')->whereNull('deleted_at')->get();
    foreach ($accounts as $account) {
        $updates = [];

        if ($account->iban && !str_starts_with($account->iban, 'eyJ')) {
            $updates['iban'] = encrypt($account->iban);
        }
        if ($account->swift_bic && !str_starts_with($account->swift_bic, 'eyJ')) {
            $updates['swift_bic'] = encrypt($account->swift_bic);
        }

        if (!empty($updates)) {
            DB::table('bank_accounts')->where('id', $account->id)->update($updates);
            $accountsProcessed++;
        }
    }
    echo "  ✅ {$accountsProcessed} bank accounts processadas" . PHP_EOL . PHP_EOL;

    // Users
    echo "📋 MIGRANDO USERS..." . PHP_EOL;
    $usersProcessed = 0;
    $users = DB::table('users')->get();
    foreach ($users as $user) {
        $updates = [];

        // Apenas mobile é cifrado, email não (usado para login)
        if ($user->mobile && !str_starts_with($user->mobile, 'eyJ')) {
            $updates['mobile'] = encrypt($user->mobile);
        }

        if (!empty($updates)) {
            DB::table('users')->where('id', $user->id)->update($updates);
            $usersProcessed++;
        }
    }
    echo "  ✅ {$usersProcessed} users processados" . PHP_EOL . PHP_EOL;

    DB::statement('SET SQL_SAFE_UPDATES=@OLD_SQL_SAFE_UPDATES');

    echo "=== MIGRAÇÃO CONCLUÍDA COM SUCESSO ===" . PHP_EOL;
    echo "✅ Total de registos processados: " . ($totalProcessed + $contactsProcessed + $accountsProcessed + $usersProcessed) . PHP_EOL;
    echo "✅ Todos os dados sensíveis estão agora cifrados com AES-256-CBC" . PHP_EOL;
    echo PHP_EOL;
    echo "⚠️  IMPORTANTE: Faça backup da sua APP_KEY do ficheiro .env" . PHP_EOL;
    echo "⚠️  Se a APP_KEY for perdida, os dados cifrados não poderão ser recuperados!" . PHP_EOL;
} catch (\Exception $e) {
    echo PHP_EOL;
    echo "❌ ERRO durante migração: " . $e->getMessage() . PHP_EOL;
    echo "Linha: " . $e->getLine() . PHP_EOL;
    echo "Ficheiro: " . $e->getFile() . PHP_EOL;
    exit(1);
}
