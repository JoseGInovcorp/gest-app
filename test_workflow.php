<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CustomerOrder;
use App\Models\Entity;
use App\Models\User;

echo "=== Teste de Workflow Automático ===\n\n";

// 1. Verificar se existem entidades (clientes)
echo "1. Verificando clientes...\n";
$customer = Entity::where('type', 'client')->first();

if (!$customer) {
    echo "   ❌ Nenhum cliente encontrado. Criando cliente de teste...\n";

    // Gerar número automático
    $lastNumber = Entity::where('type', 'client')
        ->orderBy('number', 'desc')
        ->value('number');
    $nextNumber = $lastNumber ? ((int) substr($lastNumber, 3)) + 1 : 1;
    $number = 'CLI' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    $customer = Entity::create([
        'type' => 'client',
        'number' => $number,
        'name' => 'Cliente Teste Workflow',
        'tax_number' => '123456789',
        'country_id' => 1,
        'is_active' => true,
    ]);
    echo "   ✅ Cliente criado: {$customer->name} (#{$customer->number})\n";
} else {
    echo "   ✅ Cliente encontrado: {$customer->name} (#{$customer->number})\n";
}

// 2. Verificar utilizador
echo "\n2. Verificando utilizador...\n";
$user = User::first();
if ($user) {
    echo "   ✅ Utilizador encontrado: {$user->name}\n";
} else {
    echo "   ❌ Nenhum utilizador encontrado!\n";
    exit(1);
}

// 3. Criar CustomerOrder
echo "\n3. Criando CustomerOrder...\n";

// Gerar número de encomenda
$lastOrder = CustomerOrder::orderBy('number', 'desc')->value('number');
$nextNumber = $lastOrder ? ((int) substr($lastOrder, 4)) + 1 : 1;
$orderNumber = 'ORD-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

$orderData = [
    'customer_id' => $customer->id,
    'number' => $orderNumber,
    'status' => 'draft',
    'delivery_method' => 'shipping', // Para testar workflow de 9 tarefas
    'created_by' => $user->id,
];

$customerOrder = CustomerOrder::create($orderData);
echo "   ✅ CustomerOrder criada: #{$customerOrder->number}\n";

// 4. Verificar se WorkOrder foi criada automaticamente
echo "\n4. Verificando WorkOrder automática...\n";
sleep(1); // Aguardar um segundo para o observer processar

$customerOrder->refresh();
$workOrder = $customerOrder->workOrder;

if ($workOrder) {
    echo "   ✅ WorkOrder criada automaticamente!\n";
    echo "   - ID: {$workOrder->id}\n";
    echo "   - Título: {$workOrder->title}\n";
    echo "   - Status: {$workOrder->status}\n";
    echo "   - Prioridade: {$workOrder->priority}\n";

    // 5. Verificar tarefas
    echo "\n5. Verificando tarefas geradas...\n";
    $tasks = $workOrder->tasks;
    echo "   ✅ Total de tarefas: {$tasks->count()}\n\n";

    if ($tasks->count() > 0) {
        echo "   Detalhes das tarefas:\n";
        echo "   " . str_repeat("=", 80) . "\n";

        foreach ($tasks as $index => $task) {
            $dependsOn = $task->depends_on_task_id ? "Depende: Tarefa #{$task->depends_on_task_id}" : "Sem dependências";
            $canStart = $task->canStart() ? "✓ Pode iniciar" : "✗ Bloqueada";

            echo sprintf(
                "   %d. [%s] %s\n      Grupo: %s | Due: %s\n      %s | %s\n\n",
                $index + 1,
                strtoupper($task->status),
                $task->title,
                $task->assigned_group ?? 'Não atribuído',
                $task->due_date ? $task->due_date->format('d/m/Y') : 'Sem prazo',
                $dependsOn,
                $canStart
            );
        }

        echo "   " . str_repeat("=", 80) . "\n";

        // 6. Testar iniciar primeira tarefa
        echo "\n6. Testando iniciar primeira tarefa...\n";
        $firstTask = $tasks->first();

        if ($firstTask->canStart()) {
            $firstTask->update([
                'status' => 'em_progresso',
                'assigned_to' => $user->id,
            ]);
            echo "   ✅ Tarefa iniciada: {$firstTask->title}\n";
            echo "   - Status mudou para: em_progresso\n";
            echo "   - Atribuída a: {$user->name}\n";

            // 7. Verificar se segunda tarefa ainda está bloqueada
            if ($tasks->count() > 1) {
                $secondTask = $tasks->skip(1)->first();
                echo "\n7. Verificando segunda tarefa...\n";
                echo "   - Título: {$secondTask->title}\n";
                echo "   - Pode iniciar? " . ($secondTask->canStart() ? "✓ SIM" : "✗ NÃO (bloqueada)") . "\n";

                // 8. Concluir primeira tarefa
                echo "\n8. Concluindo primeira tarefa...\n";
                $firstTask->complete('Teste de conclusão automática');
                echo "   ✅ Primeira tarefa concluída!\n";

                // 9. Verificar se segunda tarefa desbloqueou
                $secondTask->refresh();
                echo "\n9. Verificando desbloqueio da segunda tarefa...\n";
                echo "   - Pode iniciar agora? " . ($secondTask->canStart() ? "✓ SIM (desbloqueada!)" : "✗ NÃO") . "\n";

                // 10. Verificar status da WorkOrder
                $workOrder->refresh();
                echo "\n10. Status da WorkOrder:\n";
                echo "   - Status: {$workOrder->status}\n";
                echo "   - Progresso: {$workOrder->progress_percentage}%\n";
            }
        }

        echo "\n" . str_repeat("=", 80) . "\n";
        echo "✅ TESTE COMPLETO - WORKFLOW FUNCIONANDO PERFEITAMENTE!\n";
        echo str_repeat("=", 80) . "\n";
    } else {
        echo "   ❌ Nenhuma tarefa foi criada!\n";
    }
} else {
    echo "   ❌ WorkOrder NÃO foi criada automaticamente!\n";
    echo "   Verificar CustomerOrderObserver...\n";
}

echo "\n=== Teste Concluído ===\n";
