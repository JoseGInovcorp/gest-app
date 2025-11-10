<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BankAccountPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar permissões para Contas Bancárias
        $permissions = [
            'bank-accounts.create',
            'bank-accounts.read',
            'bank-accounts.update',
            'bank-accounts.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('✅ Permissões de Contas Bancárias criadas!');

        // Atribuir permissões ao Super Admin
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
            $this->command->info('✅ Permissões atribuídas ao Super Admin!');
        }

        // Atribuir permissões ao Gestor Financeiro
        $financeiro = Role::where('name', 'Gestor Financeiro')->first();
        if ($financeiro) {
            $financeiro->givePermissionTo($permissions);
            $this->command->info('✅ Permissões atribuídas ao Gestor Financeiro!');
        }

        // Permissão de leitura para Visualizador
        $viewer = Role::where('name', 'Visualizador')->first();
        if ($viewer) {
            $viewer->givePermissionTo('bank-accounts.read');
            $this->command->info('✅ Permissão de leitura atribuída ao Visualizador!');
        }
    }
}
