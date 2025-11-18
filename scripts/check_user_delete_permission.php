<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;

echo "=== Verificação de Permissão users.delete ===\n\n";

// Verificar se a permissão existe
$permission = Permission::where('name', 'users.delete')->first();
if (!$permission) {
    echo "❌ Permissão 'users.delete' NÃO existe no sistema!\n";
    echo "\nCriando permissão...\n";
    $permission = Permission::create(['name' => 'users.delete', 'guard_name' => 'web']);
    echo "✅ Permissão 'users.delete' criada!\n\n";
} else {
    echo "✅ Permissão 'users.delete' existe (ID: {$permission->id})\n\n";
}

// Listar todos os utilizadores e suas permissões
echo "Utilizadores e permissão users.delete:\n";
echo str_repeat('-', 80) . "\n";

$users = User::with('roles.permissions')->get();

foreach ($users as $user) {
    $hasPermission = $user->can('users.delete');
    $icon = $hasPermission ? '✓' : '✗';
    $roles = $user->roles->pluck('name')->join(', ');

    echo "{$icon} ID: {$user->id} | {$user->name} ({$user->email})\n";
    echo "   Roles: " . ($roles ?: 'Nenhum') . "\n";
    echo "   Tem users.delete: " . ($hasPermission ? 'SIM' : 'NÃO') . "\n\n";
}

echo "\n=== Teste concluído ===\n";
