<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "=== Adicionar permissão users.delete ao role Administrador ===\n\n";

// Obter o role Administrador
$role = Role::where('name', 'Administrador')->first();

if (!$role) {
    echo "❌ Role 'Administrador' não encontrado!\n";
    exit(1);
}

echo "✓ Role encontrado: {$role->name} (ID: {$role->id})\n\n";

// Obter a permissão
$permission = Permission::where('name', 'users.delete')->first();

if (!$permission) {
    echo "❌ Permissão 'users.delete' não encontrada!\n";
    exit(1);
}

echo "✓ Permissão encontrada: {$permission->name} (ID: {$permission->id})\n\n";

// Verificar se já tem a permissão
if ($role->hasPermissionTo('users.delete')) {
    echo "ℹ Role 'Administrador' já tem a permissão 'users.delete'\n";
} else {
    // Adicionar a permissão
    $role->givePermissionTo('users.delete');
    echo "✅ Permissão 'users.delete' adicionada ao role 'Administrador'\n";
}

// Verificar outras permissões de users
echo "\nPermissões de 'users' do role Administrador:\n";
$userPermissions = $role->permissions->filter(function ($p) {
    return str_starts_with($p->name, 'users.');
})->pluck('name');

foreach ($userPermissions as $perm) {
    echo "  ✓ {$perm}\n";
}

echo "\n=== Concluído ===\n";
