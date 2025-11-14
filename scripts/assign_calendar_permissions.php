<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$roleName = $argv[1] ?? 'Administrador';

$role = Spatie\Permission\Models\Role::where('name', $roleName)->first();

if (!$role) {
    echo "Role '{$roleName}' não encontrado.\n";
    exit(1);
}

$permissions = [
    'calendar-events.create',
    'calendar-events.read',
    'calendar-events.update',
    'calendar-events.delete',
];

echo "Atribuindo permissões ao role '{$roleName}'...\n\n";

foreach ($permissions as $permission) {
    if (!$role->hasPermissionTo($permission)) {
        $role->givePermissionTo($permission);
        echo "✓ {$permission}\n";
    } else {
        echo "- {$permission} (já existe)\n";
    }
}

echo "\n✓ Permissões atribuídas com sucesso!\n";
