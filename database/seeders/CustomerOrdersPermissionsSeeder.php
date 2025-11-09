<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CustomerOrdersPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📦 Adicionando permissões para Encomendas de Clientes...');

        // Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissões CRUD para customer-orders
        $permissions = [
            'customer-orders.create' => 'Criar Encomendas de Clientes',
            'customer-orders.read' => 'Ver Encomendas de Clientes',
            'customer-orders.update' => 'Editar Encomendas de Clientes',
            'customer-orders.delete' => 'Eliminar Encomendas de Clientes',
        ];

        $totalCreated = 0;
        $totalExisting = 0;

        foreach ($permissions as $name => $description) {
            $permission = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);

            if ($permission->wasRecentlyCreated) {
                $totalCreated++;
                $this->command->info("  ✅ Criada: {$name}");
            } else {
                $totalExisting++;
                $this->command->info("  ⏭️  Já existe: {$name}");
            }
        }

        // Atribuir permissões aos grupos
        $this->command->info('🔐 Atribuindo permissões aos grupos...');

        // Buscar TODOS os grupos que têm permissões 'orders.*'
        // Esses grupos devem ter também 'customer-orders.*'
        $rolesWithOrdersPermission = Role::whereHas('permissions', function ($query) {
            $query->where('name', 'like', 'orders.%');
        })->get();

        $this->command->info("  📋 Encontrados {$rolesWithOrdersPermission->count()} grupos com permissões 'orders.*'");

        foreach ($rolesWithOrdersPermission as $role) {
            $role->syncPermissions(array_unique(array_merge(
                $role->permissions->pluck('name')->toArray(),
                array_keys($permissions)
            )));
            $this->command->info("     ✅ {$role->name}: permissões customer-orders adicionadas");
        }

        // Também garantir nos grupos padrão
        $defaultRoles = [
            'Super Admin' => array_keys($permissions),
            'Administrador' => array_keys($permissions),
            'Gestor' => array_keys($permissions),
            'Utilizador' => ['customer-orders.read', 'customer-orders.create'],
        ];

        foreach ($defaultRoles as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            if ($role && !$rolesWithOrdersPermission->contains('id', $role->id)) {
                $role->syncPermissions(array_unique(array_merge(
                    $role->permissions->pluck('name')->toArray(),
                    $perms
                )));
                $this->command->info("  ✅ {$roleName}: permissões adicionadas");
            }
        }

        $this->command->newLine();
        $this->command->info("📊 Resumo:");
        $this->command->info("   ✅ Permissões criadas: {$totalCreated}");
        $this->command->info("   ⏭️  Permissões existentes: {$totalExisting}");
        $this->command->newLine();
    }
}
