<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactFunction;

class ContactFunctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $functions = [
            [
                'name' => 'Diretor Geral',
                'description' => 'Responsável pela gestão geral da empresa',
                'active' => true,
            ],
            [
                'name' => 'Diretor Comercial',
                'description' => 'Responsável pela área comercial e vendas',
                'active' => true,
            ],
            [
                'name' => 'Diretor Financeiro',
                'description' => 'Responsável pela gestão financeira',
                'active' => true,
            ],
            [
                'name' => 'Gerente',
                'description' => 'Gestão operacional e coordenação de equipas',
                'active' => true,
            ],
            [
                'name' => 'Técnico',
                'description' => 'Execução técnica e suporte operacional',
                'active' => true,
            ],
            [
                'name' => 'Administrativo',
                'description' => 'Gestão administrativa e apoio ao cliente',
                'active' => true,
            ],
            [
                'name' => 'Comercial',
                'description' => 'Vendas e relacionamento com clientes',
                'active' => true,
            ],
            [
                'name' => 'Compras',
                'description' => 'Gestão de compras e fornecedores',
                'active' => true,
            ],
            [
                'name' => 'Contabilidade',
                'description' => 'Gestão contabilística e fiscal',
                'active' => true,
            ],
            [
                'name' => 'Recursos Humanos',
                'description' => 'Gestão de pessoas e desenvolvimento organizacional',
                'active' => true,
            ],
        ];

        foreach ($functions as $function) {
            ContactFunction::create($function);
        }
    }
}
