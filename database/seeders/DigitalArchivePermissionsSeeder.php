<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DigitalArchivePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar permissões para Arquivo Digital
        $permissions = [
            'digital-archive.create',
            'digital-archive.read',
            'digital-archive.edit',
            'digital-archive.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $this->command->info('✅ Permissões de Arquivo Digital criadas!');

        // Atribuir permissões ao Super Admin
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
            $this->command->info('✅ Permissões atribuídas ao Super Admin!');
        }

        // Atribuir permissões ao Gestor Geral
        $gestorGeral = Role::where('name', 'Gestor Geral')->first();
        if ($gestorGeral) {
            $gestorGeral->givePermissionTo($permissions);
            $this->command->info('✅ Permissões atribuídas ao Gestor Geral!');
        }

        // Permissão de leitura para Visualizador
        $viewer = Role::where('name', 'Visualizador')->first();
        if ($viewer) {
            $viewer->givePermissionTo('digital-archive.read');
            $this->command->info('✅ Permissão de leitura atribuída ao Visualizador!');
        }
    }
}
