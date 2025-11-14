<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Verificar utilizador (mudar o ID conforme necessário)
$userId = $argv[1] ?? 2; // ID do utilizador como argumento ou padrão 2

$user = App\Models\User::find($userId);

if (!$user) {
    echo "Utilizador com ID {$userId} não encontrado.\n";
    exit(1);
}

echo "=== Verificação de Permissões ===\n\n";
echo "Utilizador: {$user->name} (ID: {$user->id})\n";
echo "Email: {$user->email}\n\n";

echo "Roles:\n";
$roles = $user->getRoleNames();
if (count($roles) === 0) {
    echo "  - Nenhuma role atribuída\n";
} else {
    foreach ($roles as $role) {
        echo "  - {$role}\n";
    }
}

echo "\nPermissões:\n";
$permissions = $user->getAllPermissions()->pluck('name')->sort();
if ($permissions->isEmpty()) {
    echo "  - Nenhuma permissão atribuída\n";
} else {
    foreach ($permissions as $permission) {
        echo "  - {$permission}\n";
    }
}

// Verificar especificamente permissões de calendar-events
echo "\n=== Permissões de Calendar Events ===\n";
$calendarPermissions = $permissions->filter(function ($perm) {
    return str_starts_with($perm, 'calendar-events');
});

if ($calendarPermissions->isEmpty()) {
    echo "❌ Nenhuma permissão de calendar-events encontrada!\n";
    echo "\nEste utilizador NÃO conseguirá ver o menu Calendário.\n";
} else {
    echo "✓ Permissões encontradas:\n";
    foreach ($calendarPermissions as $perm) {
        echo "  - {$perm}\n";
    }
    echo "\n✓ Este utilizador DEVE conseguir ver o menu Calendário.\n";
}
