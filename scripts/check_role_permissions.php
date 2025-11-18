<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;

$roles = ['Gestor Comercial', 'Gestor Financeiro', 'Gestor de Armazém', 'Visualizador'];

echo "=== PERMISSÕES POR ROLE ===\n\n";

foreach ($roles as $roleName) {
    $role = Role::where('name', $roleName)->first();

    if (!$role) {
        echo "Role '{$roleName}' não encontrado!\n\n";
        continue;
    }

    echo "ROLE: {$roleName} (ID: {$role->id}, Active: " . ($role->active ? 'SIM' : 'NÃO') . ")\n";
    echo "Permissões:\n";

    $permissions = $role->permissions->pluck('name')->sort()->values();

    if ($permissions->isEmpty()) {
        echo "  NENHUMA\n";
    } else {
        foreach ($permissions as $permission) {
            echo "  - {$permission}\n";
        }
    }

    echo "\n";
}
