<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FixRolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Corrigir Gestor Financeiro - REMOVER permissões comerciais
        $gestorFinanceiro = Role::where('name', 'Gestor Financeiro')->first();
        if ($gestorFinanceiro) {
            // Remover permissões que NÃO deveria ter
            $gestorFinanceiro->revokePermissionTo([
                'proposals.read',
                'proposals.delete',
                'customer-orders.read',
                'customer-orders.delete',
                'supplier-orders.read',
                'supplier-orders.delete',
            ]);

            echo "✓ Gestor Financeiro: Removidas permissões comerciais\n";
        }

        // Corrigir Visualizador - REMOVER permissões de escrita
        $visualizador = Role::where('name', 'Visualizador')->first();
        if ($visualizador) {
            // Remover TODAS as permissões de create/update/delete
            $visualizador->revokePermissionTo([
                'customer-orders.create',
                'customer-orders.update',
                'customer-orders.delete',
                'supplier-orders.create',
                'supplier-orders.update',
                'supplier-orders.delete',
            ]);

            echo "✓ Visualizador: Removidas permissões de escrita\n";
        }

        // Corrigir Gestor de Armazém - REMOVER proposals
        $gestorArmazem = Role::where('name', 'Gestor de Armazém')->first();
        if ($gestorArmazem) {
            $gestorArmazem->revokePermissionTo([
                'proposals.read',
                'customer-orders.read', // Também não precisa ver encomendas, só work orders
            ]);

            echo "✓ Gestor de Armazém: Removidas permissões desnecessárias\n";
        }

        echo "\n=== PERMISSÕES CORRIGIDAS COM SUCESSO ===\n";
    }
}
