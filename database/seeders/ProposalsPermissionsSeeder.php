<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProposalsPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Criar permissões para propostas
        $permissions = [
            'proposals.create',
            'proposals.read',
            'proposals.update',
            'proposals.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Atribuir permissões aos grupos
        $superAdmin = Role::where('name', 'Super Admin')->first();
        $admin = Role::where('name', 'Administrator')->first();
        $gestorComercial = Role::where('name', 'Gestor Comercial')->first();
        $visualizador = Role::where('name', 'Visualizador')->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        if ($admin) {
            $admin->givePermissionTo($permissions);
        }

        if ($gestorComercial) {
            $gestorComercial->givePermissionTo($permissions);
        }

        if ($visualizador) {
            $visualizador->givePermissionTo(['proposals.read']);
        }

        echo "✅ Permissões de propostas criadas e atribuídas aos grupos!\n";
    }
}
