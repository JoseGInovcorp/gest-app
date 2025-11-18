<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LIMPEZA DE PROPOSTAS ÓRFÃS ===\n\n";

// Encontrar propostas com entity_id nulo ou que não existe
$orphanedProposals = \App\Models\Proposal::withTrashed()
    ->whereDoesntHave('entity')
    ->get();

echo "Propostas órfãs encontradas: " . $orphanedProposals->count() . "\n\n";

if ($orphanedProposals->count() > 0) {
    echo "DETALHES:\n";
    foreach ($orphanedProposals as $proposal) {
        echo sprintf(
            "  ID: %-5d | Número: %-15s | Entity ID: %-5s | Estado: %-10s | Valor: %s€\n",
            $proposal->id,
            $proposal->numero ?? 'N/A',
            $proposal->entity_id ?? 'NULL',
            $proposal->estado ?? 'N/A',
            number_format($proposal->valor_total ?? 0, 2, ',', '.')
        );
    }

    echo "\n";
    echo "Deseja eliminar permanentemente estas propostas órfãs? (s/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);

    if (trim(strtolower($line)) === 's') {
        foreach ($orphanedProposals as $proposal) {
            // Eliminar linhas da proposta primeiro
            $proposal->lines()->forceDelete();
            // Eliminar a proposta
            $proposal->forceDelete();
        }
        echo "\n✅ " . $orphanedProposals->count() . " propostas órfãs eliminadas com sucesso!\n";
    } else {
        echo "\n❌ Operação cancelada.\n";
    }
} else {
    echo "✅ Não foram encontradas propostas órfãs.\n";
}
