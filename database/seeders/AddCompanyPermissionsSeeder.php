<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddCompanyPermissionsSeeder extends Seeder
{
    /**
     * Adiciona permissões para o módulo Company (Configurações - Empresa).
     * 
     * Nota: Company só tem 2 permissões (read, update) porque é um singleton.
     * Não faz sentido criar ou eliminar a empresa.
     */
    public function run(): void
    {
        $this->command->info('🏢 Adicionando permissões do módulo Empresa (Company)...');

        // Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissões específicas para Company (apenas read e update)
        $companyPermissions = [
            'company.read' => 'Ver configurações da empresa',
            'company.update' => 'Editar configurações da empresa',
        ];

        $created = 0;
        $existing = 0;

        foreach ($companyPermissions as $permissionName => $description) {
            $permission = Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);

            if ($permission->wasRecentlyCreated) {
                $created++;
                $this->command->info("  ✅ Criada: {$permissionName} ({$description})");
            } else {
                $existing++;
                $this->command->info("  ⏭️  Já existe: {$permissionName}");
            }
        }

        // Atribuir permissões aos grupos
        $this->assignPermissionsToRoles();

        $this->command->newLine();
        $this->command->info("📊 Resumo:");
        $this->command->info("   ✅ Permissões criadas: {$created}");
        $this->command->info("   ⏭️  Permissões existentes: {$existing}");
        $this->command->newLine();

        // Total de permissões no sistema
        $totalPermissions = Permission::count();
        $this->command->info("🔢 Total de permissões no sistema: {$totalPermissions}");
    }

    /**
     * Atribui permissões de Company aos grupos de utilizadores apropriados.
     */
    private function assignPermissionsToRoles(): void
    {
        $this->command->info('👥 Atribuindo permissões de Company aos grupos...');

        // Super Admin - Todas as permissões
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(['company.read', 'company.update']);
            $this->command->info('  ✅ Super Admin: read, update');
        }

        // Administrador - Todas as permissões
        $admin = Role::where('name', 'Administrador')->first();
        if ($admin) {
            $admin->givePermissionTo(['company.read', 'company.update']);
            $this->command->info('  ✅ Administrador: read, update');
        }

        // Gestor Comercial - Apenas leitura (para ver dados da empresa)
        $gestorComercial = Role::where('name', 'Gestor Comercial')->first();
        if ($gestorComercial) {
            $gestorComercial->givePermissionTo(['company.read']);
            $this->command->info('  ✅ Gestor Comercial: read');
        }

        // Gestor Financeiro - Apenas leitura
        $gestorFinanceiro = Role::where('name', 'Gestor Financeiro')->first();
        if ($gestorFinanceiro) {
            $gestorFinanceiro->givePermissionTo(['company.read']);
            $this->command->info('  ✅ Gestor Financeiro: read');
        }

        // Editor - Apenas leitura
        $editor = Role::where('name', 'Editor')->first();
        if ($editor) {
            $editor->givePermissionTo(['company.read']);
            $this->command->info('  ✅ Editor: read');
        }

        // Visualizador - Apenas leitura
        $viewer = Role::where('name', 'Visualizador')->first();
        if ($viewer) {
            $viewer->givePermissionTo(['company.read']);
            $this->command->info('  ✅ Visualizador: read');
        }
    }
}
