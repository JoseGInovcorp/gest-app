<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$calendarEventsPermissions = [
    'calendar-events.create',
    'calendar-events.read',
    'calendar-events.update',
    'calendar-events.delete',
];

echo "=== Atribuindo permissões calendar-events aos roles ===\n\n";

$rolesToUpdate = [
    'Gestor Comercial' => ['create', 'read', 'update', 'delete'],
    'Gestor Financeiro' => ['create', 'read', 'update', 'delete'],
    'Editor' => ['create', 'read', 'update', 'delete'],
    'Visualizador' => ['read'],
];

foreach ($rolesToUpdate as $roleName => $actions) {
    $role = Spatie\Permission\Models\Role::where('name', $roleName)->first();

    if (!$role) {
        echo "❌ Role '{$roleName}' não encontrado\n";
        continue;
    }

    echo "📝 {$roleName}:\n";

    foreach ($actions as $action) {
        $permission = "calendar-events.{$action}";

        if (!$role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
            echo "  ✓ {$permission}\n";
        } else {
            echo "  - {$permission} (já existe)\n";
        }
    }

    echo "\n";
}

echo "✅ Processo concluído!\n";
echo "\nOs utilizadores precisam fazer logout e login para ver as mudanças.\n";
