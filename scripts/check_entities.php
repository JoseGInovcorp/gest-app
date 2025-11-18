<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🏢 CLIENTES:\n";
echo str_repeat('-', 80) . "\n";
$clients = \App\Models\Entity::whereIn('type', ['client', 'both'])->orderBy('number')->get();
foreach ($clients as $e) {
    echo sprintf("  #%-3d %-50s (NIF: %s)\n", $e->number, $e->name, $e->tax_number);
    echo sprintf("        📍 %s, %s %s\n", $e->address, $e->postal_code, $e->city);
    echo sprintf("        📞 %s | ✉️  %s\n", $e->phone, $e->email);
    $contacts = \App\Models\Contact::where('entity_id', $e->id)->get();
    foreach ($contacts as $c) {
        echo sprintf("        👤 %s %s - %s\n", $c->first_name, $c->last_name, $c->function);
    }
    echo "\n";
}

echo "\n🏪 FORNECEDORES:\n";
echo str_repeat('-', 80) . "\n";
$suppliers = \App\Models\Entity::whereIn('type', ['supplier', 'both'])->orderBy('number')->get();
foreach ($suppliers as $e) {
    echo sprintf("  #%-3d %-50s (NIF: %s)\n", $e->number, $e->name, $e->tax_number);
    echo sprintf("        📍 %s, %s %s\n", $e->address, $e->postal_code, $e->city);
    echo sprintf("        📞 %s | ✉️  %s\n", $e->phone, $e->email);
    $contacts = \App\Models\Contact::where('entity_id', $e->id)->get();
    foreach ($contacts as $c) {
        echo sprintf("        👤 %s %s - %s\n", $c->first_name, $c->last_name, $c->function);
    }
    echo "\n";
}

echo "\n📊 RESUMO:\n";
echo str_repeat('-', 80) . "\n";
echo sprintf("Total de Clientes: %d\n", \App\Models\Entity::whereIn('type', ['client', 'both'])->count());
echo sprintf("Total de Fornecedores: %d\n", \App\Models\Entity::whereIn('type', ['supplier', 'both'])->count());
echo sprintf("Total de Entidades: %d\n", \App\Models\Entity::count());
echo sprintf("Total de Contactos: %d\n", \App\Models\Contact::count());
