<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Permissões de Calendário na Base de Dados ===\n\n";

$permissions = Spatie\Permission\Models\Permission::where('name', 'like', 'calendar%')
    ->orderBy('name')
    ->get(['name']);

if ($permissions->isEmpty()) {
    echo "Nenhuma permissão encontrada.\n";
} else {
    foreach ($permissions as $perm) {
        echo "  - {$perm->name}\n";
    }
}

echo "\n=== Roles que têm permissões de calendário ===\n\n";

$roles = Spatie\Permission\Models\Role::all();
foreach ($roles as $role) {
    $calendarPerms = $role->permissions()
        ->where('name', 'like', 'calendar%')
        ->pluck('name');

    if ($calendarPerms->isNotEmpty()) {
        echo "{$role->name}:\n";
        foreach ($calendarPerms as $perm) {
            echo "  - {$perm}\n";
        }
        echo "\n";
    }
}
