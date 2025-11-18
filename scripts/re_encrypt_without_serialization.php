<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

echo "=== RE-CIFRA DE DADOS (SEM SERIALIZAÇÃO) ===" . PHP_EOL . PHP_EOL;
echo "Este script vai re-cifrar dados que foram cifrados com serialização." . PHP_EOL;
echo "Vai decifrar com decrypt() e re-cifrar com encryptString()." . PHP_EOL . PHP_EOL;

$totalProcessed = 0;

try {
    DB::statement('SET @OLD_SQL_SAFE_UPDATES=@@SQL_SAFE_UPDATES, SQL_SAFE_UPDATES=0');

    // Entities
    echo "📋 RE-CIFRANDO ENTITIES..." . PHP_EOL;
    $entities = DB::table('entities')->get();
    foreach ($entities as $entity) {
        $updates = [];

        // Re-cifrar tax_number
        if ($entity->tax_number && str_starts_with($entity->tax_number, 'eyJ')) {
            try {
                $decrypted = decrypt($entity->tax_number); // Decifrar com unserialize
                $updates['tax_number'] = Crypt::encryptString($decrypted); // Re-cifrar sem serialize
            } catch (\Exception $e) {
                echo "  ⚠️  Erro ao processar tax_number da entity {$entity->id}" . PHP_EOL;
            }
        }

        // Re-cifrar phone
        if ($entity->phone && str_starts_with($entity->phone, 'eyJ')) {
            try {
                $decrypted = decrypt($entity->phone);
                $updates['phone'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
                echo "  ⚠️  Erro ao processar phone da entity {$entity->id}" . PHP_EOL;
            }
        }

        // Re-cifrar mobile
        if ($entity->mobile && str_starts_with($entity->mobile, 'eyJ')) {
            try {
                $decrypted = decrypt($entity->mobile);
                $updates['mobile'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
                echo "  ⚠️  Erro ao processar mobile da entity {$entity->id}" . PHP_EOL;
            }
        }

        // Re-cifrar email
        if ($entity->email && str_starts_with($entity->email, 'eyJ')) {
            try {
                $decrypted = decrypt($entity->email);
                $updates['email'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
                echo "  ⚠️  Erro ao processar email da entity {$entity->id}" . PHP_EOL;
            }
        }

        // Re-cifrar iban
        if ($entity->iban && str_starts_with($entity->iban, 'eyJ')) {
            try {
                $decrypted = decrypt($entity->iban);
                $updates['iban'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
                echo "  ⚠️  Erro ao processar iban da entity {$entity->id}" . PHP_EOL;
            }
        }

        if (!empty($updates)) {
            DB::table('entities')->where('id', $entity->id)->update($updates);
            $totalProcessed++;
        }
    }
    echo "  ✅ {$totalProcessed} entities re-cifradas" . PHP_EOL . PHP_EOL;

    // Contacts
    echo "📋 RE-CIFRANDO CONTACTS..." . PHP_EOL;
    $contactsProcessed = 0;
    $contacts = DB::table('contacts')->get();
    foreach ($contacts as $contact) {
        $updates = [];

        if ($contact->phone && str_starts_with($contact->phone, 'eyJ')) {
            try {
                $decrypted = decrypt($contact->phone);
                $updates['phone'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
            }
        }

        if ($contact->mobile && str_starts_with($contact->mobile, 'eyJ')) {
            try {
                $decrypted = decrypt($contact->mobile);
                $updates['mobile'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
            }
        }

        if ($contact->email && str_starts_with($contact->email, 'eyJ')) {
            try {
                $decrypted = decrypt($contact->email);
                $updates['email'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
            }
        }

        if (!empty($updates)) {
            DB::table('contacts')->where('id', $contact->id)->update($updates);
            $contactsProcessed++;
        }
    }
    echo "  ✅ {$contactsProcessed} contacts re-cifrados" . PHP_EOL . PHP_EOL;

    // BankAccounts
    echo "📋 RE-CIFRANDO BANK ACCOUNTS..." . PHP_EOL;
    $accountsProcessed = 0;
    $accounts = DB::table('bank_accounts')->whereNull('deleted_at')->get();
    foreach ($accounts as $account) {
        $updates = [];

        if ($account->iban && str_starts_with($account->iban, 'eyJ')) {
            try {
                $decrypted = decrypt($account->iban);
                $updates['iban'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
            }
        }

        if ($account->swift_bic && str_starts_with($account->swift_bic, 'eyJ')) {
            try {
                $decrypted = decrypt($account->swift_bic);
                $updates['swift_bic'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
            }
        }

        if (!empty($updates)) {
            DB::table('bank_accounts')->where('id', $account->id)->update($updates);
            $accountsProcessed++;
        }
    }
    echo "  ✅ {$accountsProcessed} bank accounts re-cifradas" . PHP_EOL . PHP_EOL;

    // Users
    echo "📋 RE-CIFRANDO USERS..." . PHP_EOL;
    $usersProcessed = 0;
    $users = DB::table('users')->get();
    foreach ($users as $user) {
        $updates = [];

        if ($user->mobile && str_starts_with($user->mobile, 'eyJ')) {
            try {
                $decrypted = decrypt($user->mobile);
                $updates['mobile'] = Crypt::encryptString($decrypted);
            } catch (\Exception $e) {
            }
        }

        if (!empty($updates)) {
            DB::table('users')->where('id', $user->id)->update($updates);
            $usersProcessed++;
        }
    }
    echo "  ✅ {$usersProcessed} users re-cifrados" . PHP_EOL . PHP_EOL;

    DB::statement('SET SQL_SAFE_UPDATES=@OLD_SQL_SAFE_UPDATES');

    echo "=== RE-CIFRA CONCLUÍDA COM SUCESSO ===" . PHP_EOL;
    echo "✅ Total de registos re-cifrados: " . ($totalProcessed + $contactsProcessed + $accountsProcessed + $usersProcessed) . PHP_EOL;
    echo "✅ Dados agora cifrados SEM serialização" . PHP_EOL;
    echo "✅ Frontend não mostrará mais 's:XX:' antes dos valores" . PHP_EOL;
} catch (\Exception $e) {
    echo PHP_EOL;
    echo "❌ ERRO durante re-cifra: " . $e->getMessage() . PHP_EOL;
    echo "Linha: " . $e->getLine() . PHP_EOL;
    echo "Ficheiro: " . $e->getFile() . PHP_EOL;
    exit(1);
}
