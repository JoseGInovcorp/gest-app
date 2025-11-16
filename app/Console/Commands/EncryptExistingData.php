<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Entity;
use App\Models\Contact;
use App\Models\BankAccount;
use Illuminate\Support\Facades\DB;

class EncryptExistingData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:encrypt-data {--force : Forçar execução sem confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cifra dados sensíveis existentes na base de dados (NIF, IBAN, telefones, emails)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  ATENÇÃO: Este comando vai cifrar todos os dados sensíveis. Tem backup da BD?')) {
                $this->error('Operação cancelada. Faça backup antes de continuar.');
                return 1;
            }
        }

        $this->info('🔐 Iniciando cifragem de dados sensíveis...');
        $this->newLine();

        $bar = $this->output->createProgressBar(3);
        $bar->start();

        try {
            DB::transaction(function () use ($bar) {
                // Entities
                $this->info('📋 Cifrando entidades (NIF, IBAN, telefones, emails)...');
                $entitiesCount = 0;
                Entity::chunk(100, function ($entities) use (&$entitiesCount) {
                    foreach ($entities as $entity) {
                        // Força re-save para acionar encryption nos casts
                        $entity->save();
                        $entitiesCount++;
                    }
                });
                $this->info("   ✅ {$entitiesCount} entidades processadas");
                $bar->advance();
                $this->newLine();

                // Contacts
                $this->info('👥 Cifrando contactos (telefones, emails)...');
                $contactsCount = 0;
                Contact::chunk(100, function ($contacts) use (&$contactsCount) {
                    foreach ($contacts as $contact) {
                        $contact->save();
                        $contactsCount++;
                    }
                });
                $this->info("   ✅ {$contactsCount} contactos processados");
                $bar->advance();
                $this->newLine();

                // Bank Accounts
                $this->info('🏦 Cifrando contas bancárias (IBAN, SWIFT)...');
                $accountsCount = 0;
                BankAccount::chunk(100, function ($accounts) use (&$accountsCount) {
                    foreach ($accounts as $account) {
                        $account->save();
                        $accountsCount++;
                    }
                });
                $this->info("   ✅ {$accountsCount} contas bancárias processadas");
                $bar->advance();
                $this->newLine();
            });

            $bar->finish();
            $this->newLine(2);
            $this->info('✅ Dados cifrados com sucesso!');
            $this->info('🔒 Todos os dados sensíveis estão agora protegidos com AES-256.');
            $this->newLine();
            $this->warn('⚠️  IMPORTANTE: Guarde o APP_KEY em local seguro. Sem ele, os dados não podem ser decifrados!');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Erro ao cifrar dados: ' . $e->getMessage());
            $this->error('💾 A transação foi revertida. Nenhum dado foi alterado.');
            return 1;
        }
    }
}
