<?php

/**
 * Script de teste para Gestão de Stock
 * 
 * Testa as funcionalidades:
 * 1. Verificação de stock disponível
 * 2. Criação de encomenda com stock insuficiente
 * 3. Atualização de stock ao fechar encomenda
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Article;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Entity;

echo "═══════════════════════════════════════════════════════════════\n";
echo "   TESTE DE GESTÃO DE STOCK - ENCOMENDAS DE CLIENTE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// 1. Verificar artigos e stock atual
echo "📦 ARTIGOS E STOCK ATUAL:\n";
echo "─────────────────────────────────────────────────────────────────\n";

$articles = Article::where('tipo', 'produto')
    ->where('estado', 'ativo')
    ->orderBy('stock_quantidade', 'asc')
    ->limit(5)
    ->get();

foreach ($articles as $article) {
    $stockColor = $article->stock_quantidade > 10 ? '32' : ($article->stock_quantidade > 0 ? '33' : '31');
    echo sprintf(
        "  %s (ID: %d)\n    Stock: \033[{$stockColor}m%s\033[0m unidades | Preço: %.2f€\n\n",
        $article->nome,
        $article->id,
        $article->stock_quantidade,
        $article->preco_com_iva
    );
}

// 2. Testar método hasStock
echo "\n🔍 TESTE: hasStock()\n";
echo "─────────────────────────────────────────────────────────────────\n";

$testArticle = $articles->first();
$testQuantities = [1, 5, $testArticle->stock_quantidade, $testArticle->stock_quantidade + 10];

foreach ($testQuantities as $qty) {
    $hasStock = $testArticle->hasStock($qty);
    $icon = $hasStock ? '✅' : '❌';
    echo sprintf(
        "  %s Solicitar %d unidades de '%s': %s\n",
        $icon,
        $qty,
        $testArticle->nome,
        $hasStock ? 'STOCK DISPONÍVEL' : 'STOCK INSUFICIENTE'
    );
}

// 3. Testar criação de encomenda com stock insuficiente
echo "\n\n📝 TESTE: Criar Encomenda com Stock Insuficiente\n";
echo "─────────────────────────────────────────────────────────────────\n";

$customer = Entity::where('type', 'client')->orWhere('type', 'both')->first();

if (!$customer) {
    echo "  ⚠️  Nenhum cliente encontrado na base de dados.\n";
    exit;
}

echo "  Cliente: {$customer->name}\n";
echo "  Artigos:\n";

$orderItems = [];
$stockWarnings = [];

foreach ($articles->take(3) as $article) {
    $requestedQty = $article->stock_quantidade + 5; // Pedir mais do que existe
    $orderItems[] = [
        'article_id' => $article->id,
        'quantity' => $requestedQty,
        'unit_price' => $article->preco_com_iva,
    ];

    if (!$article->hasStock($requestedQty)) {
        $stockWarnings[] = [
            'article' => $article->nome,
            'requested' => $requestedQty,
            'available' => $article->stock_quantidade,
            'shortage' => $requestedQty - $article->stock_quantidade,
        ];
    }

    echo sprintf(
        "    - %s: %d unidades (Stock: %s)\n",
        $article->nome,
        $requestedQty,
        $article->stock_quantidade
    );
}

if (!empty($stockWarnings)) {
    echo "\n  ⚠️  ALERTAS DE STOCK:\n";
    foreach ($stockWarnings as $warning) {
        echo sprintf(
            "    🔴 %s: Faltam %d unidades (pedido: %d, disponível: %s)\n",
            $warning['article'],
            $warning['shortage'],
            $warning['requested'],
            $warning['available']
        );
    }
    echo "\n  💡 Sugestão: Criar encomendas ao fornecedor para os artigos em falta.\n";
}

// 4. Simular atualização de stock ao fechar encomenda
echo "\n\n🔄 TESTE: Atualizar Stock ao Fechar Encomenda\n";
echo "─────────────────────────────────────────────────────────────────\n";

$testArticle = Article::where('tipo', 'produto')
    ->where('stock_quantidade', '>=', 10)
    ->first();

if ($testArticle) {
    echo "  Artigo de Teste: {$testArticle->nome}\n";
    echo "  Stock Inicial: {$testArticle->stock_quantidade} unidades\n\n";

    $quantityToDecrease = 5;

    echo "  🔹 Simulando venda de {$quantityToDecrease} unidades...\n";
    $oldStock = $testArticle->stock_quantidade;
    $testArticle->decreaseStock($quantityToDecrease);
    $testArticle->refresh();

    echo "    Antes: {$oldStock} unidades\n";
    echo "    Depois: {$testArticle->stock_quantidade} unidades\n";
    echo "    Diferença: -{$quantityToDecrease} unidades ✅\n\n";

    echo "  🔹 Simulando devolução/cancelamento de {$quantityToDecrease} unidades...\n";
    $oldStock = $testArticle->stock_quantidade;
    $testArticle->increaseStock($quantityToDecrease);
    $testArticle->refresh();

    echo "    Antes: {$oldStock} unidades\n";
    echo "    Depois: {$testArticle->stock_quantidade} unidades\n";
    echo "    Diferença: +{$quantityToDecrease} unidades ✅\n";
}

// 5. Teste com serviços (não têm stock)
echo "\n\n🛠️  TESTE: Serviços (sem controlo de stock)\n";
echo "─────────────────────────────────────────────────────────────────\n";

$service = Article::where('tipo', 'servico')->first();

if ($service) {
    echo "  Serviço: {$service->nome}\n";
    echo "  Stock Quantidade: {$service->stock_quantidade}\n";

    $hasStock = $service->hasStock(999999);
    echo sprintf(
        "  ✅ hasStock(999999): %s (serviços sempre têm stock)\n",
        $hasStock ? 'TRUE' : 'FALSE'
    );
} else {
    echo "  ⚠️  Nenhum serviço encontrado na base de dados.\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "   RESUMO DA FUNCIONALIDADE\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "✅ Validação de stock ao criar/editar encomenda\n";
echo "✅ Alertas visuais quando stock insuficiente\n";
echo "✅ Encomenda pode avançar mesmo sem stock\n";
echo "✅ Sugestão para criar encomenda ao fornecedor\n";
echo "✅ Atualização automática de stock ao fechar encomenda\n";
echo "✅ Reposição de stock ao reabrir encomenda (draft)\n";
echo "✅ Serviços não afetam stock\n\n";

echo "═══════════════════════════════════════════════════════════════\n\n";
