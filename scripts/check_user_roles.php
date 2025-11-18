<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$users = User::with('roles')->get();

echo "=== UTILIZADORES E ROLES ===\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "Nome: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Roles: ";

    if ($user->roles->isEmpty()) {
        echo "NENHUM\n";
    } else {
        echo $user->roles->pluck('name')->join(', ') . "\n";

        // Testar algumas permissões específicas
        echo "  - proposals.read: " . ($user->can('proposals.read') ? 'SIM' : 'NÃO') . "\n";
        echo "  - customer-orders.read: " . ($user->can('customer-orders.read') ? 'SIM' : 'NÃO') . "\n";
        echo "  - bank-accounts.read: " . ($user->can('bank-accounts.read') ? 'SIM' : 'NÃO') . "\n";
    }

    echo "\n";
}
