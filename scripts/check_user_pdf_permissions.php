<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "Verificando permissões de utilizadores para PDF de propostas...\n\n";

$users = User::with(['roles.permissions'])->get();

foreach ($users as $user) {
    echo "Utilizador: {$user->name} (ID: {$user->id})\n";
    echo "Email: {$user->email}\n";

    $roles = $user->roles->pluck('name')->toArray();
    echo "Roles: " . (empty($roles) ? 'Nenhuma' : implode(', ', $roles)) . "\n";

    // Verificar permissão específica
    $hasPermission = $user->can('proposals.read');
    echo "Permissão 'proposals.read': " . ($hasPermission ? '✓ SIM' : '✗ NÃO') . "\n";

    echo str_repeat('-', 60) . "\n";
}

echo "\nVerificando se a permissão 'proposals.read' existe...\n";
$permission = \Spatie\Permission\Models\Permission::where('name', 'proposals.read')->first();

if ($permission) {
    echo "✓ Permissão existe\n";
    echo "  Guard: {$permission->guard_name}\n";

    $rolesWithPermission = $permission->roles()->pluck('name')->toArray();
    echo "  Atribuída aos roles: " . (empty($rolesWithPermission) ? 'Nenhum' : implode(', ', $rolesWithPermission)) . "\n";
} else {
    echo "✗ Permissão NÃO existe!\n";
}
