<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Verificando permissões do admin...\n\n";

$admin = App\Models\User::where('email', 'admin@gest-app.com')->first();

if (!$admin) {
    echo "❌ Admin não encontrado!\n";
    exit(1);
}

echo "👤 User: {$admin->name} ({$admin->email})\n";
echo "📋 Roles: " . $admin->roles->pluck('name')->join(', ') . "\n\n";

$permissions = $admin->getAllPermissions()->pluck('name')->toArray();
echo "📊 Total de Permissões: " . count($permissions) . "\n\n";

// Verificar permissões específicas
$checkPerms = ['clients.create', 'clients.read', 'clients.update', 'clients.delete'];

echo "✅ Verificação de permissões:\n";
foreach ($checkPerms as $perm) {
    $has = $admin->can($perm);
    $icon = $has ? '✅' : '❌';
    echo "   $icon $perm: " . ($has ? 'SIM' : 'NÃO') . "\n";
}

echo "\n📝 Todas as permissões:\n";
foreach ($permissions as $perm) {
    echo "   - $perm\n";
}
