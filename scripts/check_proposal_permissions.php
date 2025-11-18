<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFICAÇÃO DE PERMISSÕES PARA PROPOSTAS ===\n\n";

// Obter primeiro usuário
$user = \App\Models\User::first();

if (!$user) {
    echo "❌ Nenhum utilizador encontrado!\n";
    exit(1);
}

echo "Utilizador: {$user->name} ({$user->email})\n\n";

echo "PERMISSÕES DO UTILIZADOR:\n";
$permissions = $user->getAllPermissions();
foreach ($permissions as $permission) {
    echo "  - {$permission->name}\n";
}

echo "\n";

// Verificar permissões específicas de propostas
$proposalPermissions = [
    'proposals.read',
    'proposals.create',
    'proposals.update',
    'proposals.delete',
];

echo "VERIFICAÇÃO DE PERMISSÕES DE PROPOSTAS:\n";
foreach ($proposalPermissions as $perm) {
    $has = $user->hasPermissionTo($perm);
    $icon = $has ? '✅' : '❌';
    echo "  $icon $perm\n";
}

echo "\nPAPÉIS DO UTILIZADOR:\n";
$roles = $user->getRoleNames();
foreach ($roles as $role) {
    echo "  - $role\n";
}

echo "\n✅ Verificação concluída!\n";
