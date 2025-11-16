<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Entity;

class EntitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter próximo número disponível
        $nextNumber = Entity::withTrashed()->max('number') ?? 0;

        // 6 Clientes
        $clients = [
            [
                'number' => ++$nextNumber,
                'name' => 'TechCorp Solutions',
                'type' => 'client',
                'tax_number' => '501234567',
                'email' => 'contato@techcorp.pt',
                'phone' => '211234567',
                'mobile' => '912345678',
                'address' => 'Rua da Tecnologia, 123',
                'postal_code' => '1000-001',
                'city' => 'Lisboa',
                'country' => 'Portugal',
                'active' => true,
            ],
            [
                'number' => ++$nextNumber,
                'name' => 'Restaurante O Sabor',
                'type' => 'client',
                'tax_number' => '502345678',
                'email' => 'geral@osabor.pt',
                'phone' => '212345678',
                'mobile' => '923456789',
                'address' => 'Avenida Central, 45',
                'postal_code' => '2000-002',
                'city' => 'Porto',
                'country' => 'Portugal',
                'active' => true,
            ],
            [
                'number' => ++$nextNumber,
                'name' => 'AutoPeças Silva',
                'type' => 'client',
                'tax_number' => '503456789',
                'email' => 'vendas@autopecassilva.pt',
                'phone' => '213456789',
                'mobile' => '934567890',
                'address' => 'Zona Industrial de Coimbra, Lote 5',
                'postal_code' => '3000-003',
                'city' => 'Coimbra',
                'country' => 'Portugal',
                'active' => true,
            ],
            [
                'number' => ++$nextNumber,
                'name' => 'Clínica Dentária Sorriso',
                'type' => 'client',
                'tax_number' => '504567890',
                'email' => 'recepcao@clinicasorriso.pt',
                'phone' => '214567890',
                'mobile' => '945678901',
                'address' => 'Praça da Saúde, 12',
                'postal_code' => '4000-004',
                'city' => 'Braga',
                'country' => 'Portugal',
                'active' => true,
            ],
            [
                'number' => ++$nextNumber,
                'name' => 'Construções Almeida & Filhos',
                'type' => 'client',
                'tax_number' => '505678901',
                'email' => 'info@construcoesalmeida.pt',
                'phone' => '215678901',
                'mobile' => '956789012',
                'address' => 'Rua dos Construtores, 78',
                'postal_code' => '5000-005',
                'city' => 'Faro',
                'country' => 'Portugal',
                'active' => true,
            ],
            [
                'number' => ++$nextNumber,
                'name' => 'Mercearia São João',
                'type' => 'client',
                'tax_number' => '506789012',
                'email' => 'mercearia@saojoao.pt',
                'phone' => '216789012',
                'mobile' => '967890123',
                'address' => 'Largo de São João, 3',
                'postal_code' => '6000-006',
                'city' => 'Évora',
                'country' => 'Portugal',
                'active' => true,
            ],
        ];

        // 5 Fornecedores
        $suppliers = [
            [
                'number' => ++$nextNumber,
                'name' => 'Distribuidora Central Lda',
                'type' => 'supplier',
                'tax_number' => '507890123',
                'email' => 'encomendas@distribuidoracentral.pt',
                'phone' => '217890123',
                'mobile' => '978901234',
                'address' => 'Parque Industrial Norte, Armazém 10',
                'postal_code' => '7000-007',
                'city' => 'Lisboa',
                'country' => 'Portugal',
                'active' => true,
            ],
            [
                'number' => ++$nextNumber,
                'name' => 'Fornecimentos Industriais SA',
                'type' => 'supplier',
                'tax_number' => '508901234',
                'email' => 'vendas@fornecimentosindustriais.pt',
                'phone' => '218901234',
                'mobile' => '989012345',
                'address' => 'Zona Industrial de Aveiro, Lote 23',
                'postal_code' => '8000-008',
                'city' => 'Aveiro',
                'country' => 'Portugal',
                'active' => true,
            ],
            [
                'number' => ++$nextNumber,
                'name' => 'Papelaria e Escritório Modern',
                'type' => 'supplier',
                'tax_number' => '509012345',
                'email' => 'geral@modernoffice.pt',
                'phone' => '219012345',
                'mobile' => '990123456',
                'address' => 'Rua do Comércio, 156',
                'postal_code' => '9000-009',
                'city' => 'Porto',
                'country' => 'Portugal',
                'active' => true,
            ],
            [
                'number' => ++$nextNumber,
                'name' => 'Tech Supplies Portugal',
                'type' => 'supplier',
                'tax_number' => '510123456',
                'email' => 'comercial@techsupplies.pt',
                'phone' => '220123456',
                'mobile' => '901234567',
                'address' => 'Avenida da Tecnologia, 89',
                'postal_code' => '1100-010',
                'city' => 'Lisboa',
                'country' => 'Portugal',
                'active' => true,
            ],
            [
                'number' => ++$nextNumber,
                'name' => 'Alimentar Gourmet Lda',
                'type' => 'supplier',
                'tax_number' => '511234567',
                'email' => 'pedidos@alimentargourmet.pt',
                'phone' => '221234567',
                'mobile' => '912345670',
                'address' => 'Mercado Central, Pavilhão 4',
                'postal_code' => '2100-011',
                'city' => 'Setúbal',
                'country' => 'Portugal',
                'active' => true,
            ],
        ];

        // Inserir clientes
        foreach ($clients as $client) {
            Entity::create($client);
        }

        // Inserir fornecedores
        foreach ($suppliers as $supplier) {
            Entity::create($supplier);
        }

        $this->command->info('✅ 6 clientes e 5 fornecedores criados com sucesso!');
    }
}
