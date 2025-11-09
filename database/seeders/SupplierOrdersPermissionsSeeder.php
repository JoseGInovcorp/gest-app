<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SupplierOrdersPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar permissões para encomendas de fornecedores
        $permissions = [
            'supplier-orders.create',
            'supplier-orders.read',
            'supplier-orders.update',
            'supplier-orders.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        echo "✓ Permissões de supplier-orders criadas\n";

        // Encontrar todos os roles que têm permissões "orders.*"
        $rolesWithOrders = Role::whereHas('permissions', function ($query) {
            $query->where('name', 'like', 'orders.%');
        })->get();

        echo "✓ Encontrados " . $rolesWithOrders->count() . " roles com permissões orders.*\n";

        // Atribuir permissões supplier-orders.* a esses roles
        foreach ($rolesWithOrders as $role) {
            foreach ($permissions as $permission) {
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                    echo "  → Permissão '{$permission}' atribuída ao role '{$role->name}'\n";
                }
            }
        }

        echo "✓ Permissões de supplier-orders atribuídas com sucesso!\n";
    }
}
