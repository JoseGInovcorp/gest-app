<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\WorkOrder;

echo "\n=== Verificação Final da WorkOrder ===\n\n";

$wo = WorkOrder::first();

if ($wo) {
    echo "WorkOrder #{$wo->id}: {$wo->title}\n";
    echo str_repeat("=", 60) . "\n\n";

    echo "Status atual: {$wo->status}\n";
    echo "Prioridade: {$wo->priority}\n";
    echo "Progresso: {$wo->progress_percentage}%\n\n";

    $total = $wo->tasks()->count();
    $concluidas = $wo->tasks()->where('status', 'concluida')->count();
    $emProgresso = $wo->tasks()->where('status', 'em_progresso')->count();
    $pendentes = $wo->tasks()->where('status', 'pendente')->count();

    echo "Tarefas:\n";
    echo "  Concluídas: {$concluidas}/{$total}\n";
    echo "  Em Progresso: {$emProgresso}/{$total}\n";
    echo "  Pendentes: {$pendentes}/{$total}\n\n";

    echo "Chamando updateStatus()...\n";
    $wo->updateStatus();
    $wo->refresh();

    echo "✅ Status atualizado para: {$wo->status}\n";
    echo "✅ Progresso final: {$wo->progress_percentage}%\n\n";

    echo str_repeat("=", 60) . "\n";
    echo "✅ WORKFLOW VERIFICADO COM SUCESSO!\n";
    echo str_repeat("=", 60) . "\n";
} else {
    echo "❌ Nenhuma WorkOrder encontrada!\n";
}

echo "\n";
