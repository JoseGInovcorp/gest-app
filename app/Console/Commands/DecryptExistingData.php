<?php

namespace App\Console\Commands;

use App\Models\Entity;
use App\Models\Contact;
use App\Models\BankAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Encryption\DecryptException;

class DecryptExistingData extends Command
{
    protected $signature = 'security:decrypt-data {--force : Skip confirmation}';
    protected $description = 'Revert encrypted data back to plain text (for development/migration rollback)';

    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  Isto vai DESENCRIPTAR dados sensíveis. Continuar?')) {
                $this->info('Operação cancelada.');
                return 0;
            }
        }

        $this->info('🔓 Iniciando desencriptação de dados...');
        $this->newLine();

        DB::beginTransaction();

        try {
            // Desencriptar Entities
            $this->info('📋 Desencriptando entidades (NIF, IBAN, telefones, emails)...');
            $entities = Entity::all();
            $decrypted = 0;

            foreach ($entities as $entity) {
                $updated = false;

                // Tentar desencriptar cada campo
                foreach (['tax_number', 'phone', 'mobile', 'email', 'iban'] as $field) {
                    if ($entity->$field) {
                        try {
                            // Tentar desencriptar
                            $decrypted_value = decrypt($entity->$field);
                            // Se conseguiu, atualizar diretamente na BD
                            DB::table('entities')
                                ->where('id', $entity->id)
                                ->update([$field => $decrypted_value]);
                            $updated = true;
                        } catch (DecryptException $e) {
                            // Já está desencriptado, ignorar
                        }
                    }
                }

                if ($updated) $decrypted++;
            }

            $this->info("   ✅ {$decrypted} entidades desencriptadas");

            // Desencriptar Contacts
            $this->info('👥 Desencriptando contactos (telefones, emails)...');
            $contacts = Contact::all();
            $decrypted = 0;

            foreach ($contacts as $contact) {
                $updated = false;

                foreach (['phone', 'mobile', 'email'] as $field) {
                    if ($contact->$field) {
                        try {
                            $decrypted_value = decrypt($contact->$field);
                            DB::table('contacts')
                                ->where('id', $contact->id)
                                ->update([$field => $decrypted_value]);
                            $updated = true;
                        } catch (DecryptException $e) {
                            // Já está desencriptado
                        }
                    }
                }

                if ($updated) $decrypted++;
            }

            $this->info("   ✅ {$decrypted} contactos desencriptados");

            // Desencriptar Bank Accounts
            $this->info('🏦 Desencriptando contas bancárias (IBAN, SWIFT)...');
            $accounts = BankAccount::all();
            $decrypted = 0;

            foreach ($accounts as $account) {
                $updated = false;

                foreach (['iban', 'swift_bic'] as $field) {
                    if ($account->$field) {
                        try {
                            $decrypted_value = decrypt($account->$field);
                            DB::table('bank_accounts')
                                ->where('id', $account->id)
                                ->update([$field => $decrypted_value]);
                            $updated = true;
                        } catch (DecryptException $e) {
                            // Já está desencriptado
                        }
                    }
                }

                if ($updated) $decrypted++;
            }

            $this->info("   ✅ {$decrypted} contas bancárias desencriptadas");

            DB::commit();

            $this->newLine();
            $this->info('✅ Dados desencriptados com sucesso!');
            $this->info('🔓 Todos os dados sensíveis estão agora em texto simples.');
            $this->newLine();
            $this->warn('⚠️  PRÓXIMOS PASSOS:');
            $this->warn('1. Manter encriptação DESATIVADA nos models durante desenvolvimento');
            $this->warn('2. Antes de ir para PRODUÇÃO:');
            $this->warn('   - Backup da base de dados');
            $this->warn('   - Ativar encrypted casts nos 3 models');
            $this->warn('   - Executar: php artisan security:encrypt-data');
            $this->warn('   - Testar acesso aos dados');

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Erro ao desencriptar dados: ' . $e->getMessage());
            return 1;
        }
    }
}
