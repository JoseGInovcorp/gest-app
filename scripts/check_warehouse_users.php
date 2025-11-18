<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "\n=== Verificação de Utilizadores - Gestor de Armazém ===\n\n";

$users = User::role('Gestor de Armazém')->get(['id', 'name', 'email']);

if ($users->isEmpty()) {
    echo "❌ Nenhum utilizador com papel 'Gestor de Armazém' encontrado.\n";
    echo "\nCriando utilizador de teste...\n";

    $user = User::create([
        'name' => 'Gestor Armazém Teste',
        'email' => 'armazem@test.com',
        'password' => bcrypt('password'),
    ]);

    $user->assignRole('Gestor de Armazém');

    echo "✅ Utilizador criado: {$user->name} ({$user->email})\n";
    echo "   Password: password\n";
} else {
    echo "✅ Utilizadores encontrados:\n\n";
    foreach ($users as $user) {
        echo "- {$user->name} ({$user->email})\n";
        $roles = $user->getRoleNames();
        if (is_array($roles)) {
            echo "  Papéis: " . implode(', ', $roles) . "\n\n";
        } else {
            echo "  Papéis: " . $roles->join(', ') . "\n\n";
        }
    }
}

// Verificar se há tarefas para este papel
echo "\n=== Verificação de Tarefas ===\n\n";
$tasks = \App\Models\WorkOrderTask::where('assigned_group', 'Gestor de Armazém')
    ->orWhereHas('assignedUser', function ($q) {
        $q->whereHas('roles', function ($q) {
            $q->where('name', 'Gestor de Armazém');
        });
    })
    ->count();

echo "Total de tarefas atribuídas: {$tasks}\n";

if ($tasks > 0) {
    $sample = \App\Models\WorkOrderTask::where('assigned_group', 'Gestor de Armazém')
        ->with('workOrder')
        ->first();

    if ($sample) {
        echo "\nExemplo de tarefa:\n";
        echo "- {$sample->title}\n";
        echo "  Status: {$sample->status}\n";
        echo "  Ordem: {$sample->workOrder->title}\n";
    }
}

echo "\n";
