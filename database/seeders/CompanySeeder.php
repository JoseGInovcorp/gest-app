<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Seed company settings with initial data.
     */
    public function run(): void
    {
        $this->command->info('🏢 Criando dados iniciais da empresa...');

        // Verificar se já existe uma empresa (singleton)
        $existing = Company::first();

        if ($existing) {
            $this->command->info('  ⏭️  Empresa já existe (ID: ' . $existing->id . ')');
            $this->command->info('  📋 Nome: ' . ($existing->name ?? 'Não definido'));
            return;
        }

        // Criar registo inicial
        $company = Company::create([
            'name' => 'Gest-App',
            'nif' => null,
            'address' => null,
            'postal_code' => null,
            'city' => null,
            'logo' => null,
        ]);

        $this->command->info('  ✅ Empresa criada com sucesso!');
        $this->command->info('  📋 Nome: ' . $company->name);
        $this->command->newLine();
        $this->command->info('💡 Pode personalizar os dados em: Configurações → Empresa');
    }
}
