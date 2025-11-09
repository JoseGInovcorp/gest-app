<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddNewModulesPermissionsSeeder extends Seeder
{
    /**
     * Adiciona permissões para novos módulos:
     * - Calendário (calendar)
     * - Ordens de Trabalho (work-orders)
     * - Arquivo Digital (digital-archive)
     * - Logs (logs)
     */
    public function run(): void
    {
        $this->command->info('📦 Adicionando permissões para novos módulos...');

        // Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Novos módulos a adicionar
        $newModules = [
            'calendar' => 'Calendário',
            'work-orders' => 'Ordens de Trabalho',
            'digital-archive' => 'Arquivo Digital',
            'logs' => 'Logs',
        ];

        // Ações CRUD
        $actions = ['create', 'read', 'update', 'delete'];

        $totalCreated = 0;
        $totalExisting = 0;

        foreach ($newModules as $module => $label) {
            $this->command->info("  📋 Processando: {$label} ({$module})");

            foreach ($actions as $action) {
                $permissionName = "{$module}.{$action}";

                $permission = Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);

                if ($permission->wasRecentlyCreated) {
                    $totalCreated++;
                    $this->command->info("    ✅ Criada: {$permissionName}");
                } else {
                    $totalExisting++;
                    $this->command->info("    ⏭️  Já existe: {$permissionName}");
                }
            }
        }

        $this->command->newLine();
        $this->command->info("📊 Resumo:");
        $this->command->info("   ✅ Permissões criadas: {$totalCreated}");
        $this->command->info("   ⏭️  Permissões existentes: {$totalExisting}");
        $this->command->info("   📦 Total de módulos processados: " . count($newModules));
        $this->command->newLine();

        // Verificar total de permissões no sistema
        $totalPermissions = Permission::count();
        $this->command->info("🔢 Total de permissões no sistema: {$totalPermissions}");
        $this->command->info("   (Esperado: 64 = 16 módulos × 4 ações)");
    }
}
