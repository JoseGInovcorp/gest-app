<?php

/**
 * Script para corrigir números duplicados de contactos
 * 
 * Este script renumera todos os contactos (incluindo apagados) para garantir
 * que não existem números duplicados e que a sequência está correta.
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Contact;
use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "CORREÇÃO DE NÚMEROS DE CONTACTOS\n";
echo "==============================================\n\n";

try {
    DB::beginTransaction();

    // Buscar todos os contactos (incluindo apagados) ordenados por ID
    $contacts = Contact::withTrashed()->orderBy('id')->get();

    echo "Total de contactos encontrados: " . $contacts->count() . "\n\n";

    if ($contacts->isEmpty()) {
        echo "Nenhum contacto para processar.\n";
        DB::rollBack();
        exit(0);
    }

    // Renumerar todos os contactos sequencialmente
    $number = 1;
    $updated = 0;

    foreach ($contacts as $contact) {
        $oldNumber = $contact->number;

        // Atualizar número diretamente sem passar pelas validações
        DB::table('contacts')
            ->where('id', $contact->id)
            ->update(['number' => $number]);

        if ($oldNumber != $number) {
            $deleted = $contact->deleted_at ? ' (APAGADO)' : '';
            echo "Contacto ID {$contact->id}: {$oldNumber} → {$number}{$deleted}\n";
            $updated++;
        }

        $number++;
    }

    echo "\n----------------------------------------------\n";
    echo "Total de contactos renumerados: {$updated}\n";
    echo "Próximo número disponível: {$number}\n";
    echo "----------------------------------------------\n\n";

    // Verificar se ainda existem duplicados
    $duplicates = DB::table('contacts')
        ->select('number', DB::raw('COUNT(*) as count'))
        ->groupBy('number')
        ->having('count', '>', 1)
        ->get();

    if ($duplicates->isEmpty()) {
        echo "✅ Nenhum número duplicado encontrado!\n\n";
        DB::commit();
        echo "✅ Alterações guardadas com sucesso!\n";
    } else {
        echo "❌ ERRO: Ainda existem números duplicados:\n";
        foreach ($duplicates as $duplicate) {
            echo "   Número {$duplicate->number}: {$duplicate->count} ocorrências\n";
        }
        DB::rollBack();
        echo "\n❌ Alterações revertidas!\n";
        exit(1);
    }
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n==============================================\n";
echo "SCRIPT CONCLUÍDO\n";
echo "==============================================\n";
