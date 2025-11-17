<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TaskTemplatePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar permissões para Templates de Tarefas
        $permissions = [
            'task-templates.create',
            'task-templates.read',
            'task-templates.update',
            'task-templates.delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // Atribuir permissões aos roles
        $adminRole = Role::where('name', 'Administrador')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'task-templates.create',
                'task-templates.read',
                'task-templates.update',
                'task-templates.delete',
            ]);
        }

        $gestorComercialRole = Role::where('name', 'Gestor Comercial')->first();
        if ($gestorComercialRole) {
            $gestorComercialRole->givePermissionTo([
                'task-templates.read',
                'task-templates.update',
            ]);
        }

        $gestorFinanceiroRole = Role::where('name', 'Gestor Financeiro')->first();
        if ($gestorFinanceiroRole) {
            $gestorFinanceiroRole->givePermissionTo([
                'task-templates.read',
            ]);
        }

        $gestorArmazemRole = Role::where('name', 'Gestor de Armazém')->first();
        if ($gestorArmazemRole) {
            $gestorArmazemRole->givePermissionTo([
                'task-templates.read',
            ]);
        }

        $gestorComprasRole = Role::where('name', 'Gestor de Compras')->first();
        if ($gestorComprasRole) {
            $gestorComprasRole->givePermissionTo([
                'task-templates.read',
            ]);
        }

        $this->command->info('Permissões de Templates de Tarefas criadas e atribuídas com sucesso!');
    }
}
