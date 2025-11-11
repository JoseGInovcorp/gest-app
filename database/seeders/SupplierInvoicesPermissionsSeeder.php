<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SupplierInvoicesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar as 4 permissões CRUD para Faturas de Fornecedores
        $permissions = [
            'supplier-invoices.create',
            'supplier-invoices.read',
            'supplier-invoices.update',
            'supplier-invoices.delete',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Atribuir todas as permissões ao Super Admin
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        // Atribuir todas as permissões ao Gestor Financeiro
        $gestorFinanceiro = Role::where('name', 'Gestor Financeiro')->first();
        if ($gestorFinanceiro) {
            $gestorFinanceiro->givePermissionTo($permissions);
        }

        // Atribuir apenas leitura ao Visualizador
        $visualizador = Role::where('name', 'Visualizador')->first();
        if ($visualizador) {
            $visualizador->givePermissionTo('supplier-invoices.read');
        }

        $this->command->info('Permissões de Faturas de Fornecedores criadas e atribuídas com sucesso!');
    }
}
