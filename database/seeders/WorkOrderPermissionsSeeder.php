<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WorkOrderPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Criar permissões para Work Orders
        $permissions = [
            'work-orders.create',
            'work-orders.read',
            'work-orders.update',
            'work-orders.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Criar role "Gestor de Armazém" (warehouse manager)
        $warehouseManager = Role::firstOrCreate(['name' => 'Gestor de Armazém']);

        // Permissões para Gestor de Armazém
        $warehouseManager->givePermissionTo([
            // Work Orders - pode ver e atualizar tarefas
            'work-orders.read',
            'work-orders.update',

            // Articles - pode ver e atualizar stock
            'articles.read',
            'articles.update',

            // Supplier Orders - pode ver e receber encomendas
            'supplier-orders.read',
            'supplier-orders.update',
        ]);

        // Atualizar permissões dos outros roles
        $superAdmin = Role::findByName('Super Admin');
        $admin = Role::findByName('Administrador');
        $gestorComercial = Role::findByName('Gestor Comercial');
        $gestorFinanceiro = Role::findByName('Gestor Financeiro');

        // Super Admin e Administrador têm todas as permissões
        $superAdmin->givePermissionTo($permissions);
        $admin->givePermissionTo($permissions);

        // Gestor Comercial pode criar, ver e atualizar work orders
        $gestorComercial->givePermissionTo([
            'work-orders.create',
            'work-orders.read',
            'work-orders.update',
        ]);

        // Gestor Financeiro pode ver work orders
        $gestorFinanceiro->givePermissionTo([
            'work-orders.read',
            'work-orders.update',
        ]);

        $this->command->info('✅ Work Orders permissions created successfully!');
        $this->command->info('✅ Gestor de Armazém role created with appropriate permissions!');
    }
}
