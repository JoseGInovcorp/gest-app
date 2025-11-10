<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ClientAccountPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar permissões para Conta Corrente de Clientes
        $permissions = [
            'client-accounts.create',
            'client-accounts.read',
            'client-accounts.update',
            'client-accounts.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Atribuir permissões a roles específicos
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        $gestorFinanceiro = Role::where('name', 'Gestor Financeiro')->first();
        if ($gestorFinanceiro) {
            $gestorFinanceiro->givePermissionTo($permissions);
        }

        $visualizador = Role::where('name', 'Visualizador')->first();
        if ($visualizador) {
            $visualizador->givePermissionTo(['client-accounts.read']);
        }

        echo "Permissões de Conta Corrente de Clientes criadas e atribuídas com sucesso!\n";
    }
}
