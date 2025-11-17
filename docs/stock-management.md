# Gestão de Stock - Encomendas de Cliente

## Visão Geral

Sistema de gestão automática de stock integrado ao módulo de encomendas de cliente. Valida disponibilidade de stock, emite alertas quando insuficiente e atualiza automaticamente as quantidades quando encomendas são fechadas.

---

## Funcionalidades Implementadas

### 1. Validação de Stock ao Criar/Editar Encomenda

#### Backend (`CustomerOrderController`)

**No método `store()`:**

```php
// Verificar stock disponível
$stockWarnings = [];
foreach ($validated['items'] as $index => $item) {
    $article = Article::find($item['article_id']);
    if ($article && !$article->hasStock($item['quantity'])) {
        $stockWarnings[] = [
            'article_id' => $article->id,
            'article_name' => $article->nome,
            'requested' => $item['quantity'],
            'available' => $article->stock_quantidade,
        ];
    }
}
```

**Mensagem de sucesso com alertas:**

```php
$message = 'Encomenda criada com sucesso!';
if (!empty($stockWarnings)) {
    $message .= ' ATENÇÃO: Alguns artigos têm stock insuficiente. Considere criar encomendas ao fornecedor.';
}

return redirect()->route('customer-orders.index')
    ->with('success', $message)
    ->with('stock_warnings', $stockWarnings);
```

#### Frontend (`Create.vue` / `Edit.vue`)

**Indicador visual de stock:**

```vue
<!-- Indicador de Stock -->
<div
    v-if="item.article_id && getArticleStock(item.article_id) !== null"
    class="mt-2 text-sm"
    :class="[
        getArticleStock(item.article_id) >= item.quantity
            ? 'text-green-600 dark:text-green-400'
            : 'text-orange-600 dark:text-orange-400',
    ]"
>
    <span class="font-medium">
        Stock disponível: {{ getArticleStock(item.article_id) }}
    </span>
    <span
        v-if="getArticleStock(item.article_id) < item.quantity"
        class="block mt-1 text-xs text-red-600 dark:text-red-400"
    >
        ⚠️ Stock insuficiente! Considere adicionar fornecedor.
    </span>
</div>
```

**Função helper:**

```javascript
const getArticleStock = (articleId) => {
    const article = props.articles.find((a) => a.id == articleId);
    if (!article) return null;

    // Serviços não têm stock
    if (article.tipo === "servico") return null;

    return parseFloat(article.stock_quantidade) || 0;
};
```

---

### 2. Atualização Automática de Stock

#### Quando Encomenda É Fechada

**No método `update()` do `CustomerOrderController`:**

```php
$oldStatus = $customerOrder->status;
$newStatus = $validated['status'];

// Se a encomenda passou de draft para closed, atualizar stock
$shouldUpdateStock = ($oldStatus === 'draft' && $newStatus === 'closed');

// Se a encomenda voltou de closed para draft, repor stock
$shouldRestoreStock = ($oldStatus === 'closed' && $newStatus === 'draft');

// Repor stock dos itens antigos se estava fechada
if ($shouldRestoreStock) {
    foreach ($customerOrder->items as $oldItem) {
        $article = Article::find($oldItem->article_id);
        if ($article) {
            $article->increaseStock($oldItem->quantity);
        }
    }
}

// ... criar novos itens ...

// Atualizar stock se a encomenda está a ser fechada
if ($shouldUpdateStock) {
    $article = Article::find($item['article_id']);
    if ($article) {
        $article->decreaseStock($item['quantity']);
    }
}
```

#### Métodos do Article Model

**`hasStock(float $quantity): bool`**

-   Verifica se há stock suficiente para a quantidade solicitada
-   Serviços (`tipo = 'servico'`) sempre retornam `true`
-   Produtos verificam: `stock_quantidade >= $quantity`

**`decreaseStock(float $quantity): void`**

-   Reduz o stock em X unidades
-   Apenas para produtos (`tipo = 'produto'`)
-   Usa `max(0, stock_quantidade - quantity)` para evitar negativos
-   Salva automaticamente no banco

**`increaseStock(float $quantity): void`**

-   Aumenta o stock em X unidades
-   Apenas para produtos
-   Usado ao cancelar/reabrir encomendas
-   Salva automaticamente no banco

---

## Fluxo de Trabalho

### Cenário 1: Stock Suficiente

1. Utilizador cria encomenda de cliente
2. Seleciona artigo com stock disponível
3. ✅ Indicador verde mostra "Stock disponível: X"
4. Submete encomenda com status "draft"
5. Encomenda criada sem alertas
6. **Ao mudar para "closed":**
    - Stock é automaticamente decrementado
    - Exemplo: Stock 10 → Venda 3 → Stock 7

### Cenário 2: Stock Insuficiente

1. Utilizador cria encomenda de cliente
2. Seleciona artigo com stock insuficiente
3. ⚠️ Indicador laranja/vermelho mostra:
    - "Stock disponível: 2"
    - "⚠️ Stock insuficiente! Considere adicionar fornecedor."
4. Utilizador **pode continuar** e criar a encomenda
5. Sistema exibe mensagem:
    - "Encomenda criada com sucesso! ATENÇÃO: Alguns artigos têm stock insuficiente. Considere criar encomendas ao fornecedor."
6. Utilizador adiciona fornecedor ao item
7. Converte encomenda cliente → encomenda fornecedor

### Cenário 3: Cancelamento de Encomenda

1. Encomenda está fechada (stock já decrementado)
2. Utilizador reabre encomenda (muda para "draft")
3. **Stock é automaticamente reposto:**
    - Exemplo: Stock 7 → Cancela venda 3 → Stock 10
4. Permite edição/correção da encomenda

### Cenário 4: Serviços (sem stock)

1. Utilizador seleciona serviço (ex: "Consultoria IT")
2. Não é exibido indicador de stock
3. Encomenda criada normalmente
4. Métodos `decreaseStock` e `increaseStock` não fazem nada

---

## Mensagens e Alertas

### Cores dos Indicadores

| Condição              | Cor              | Significado        |
| --------------------- | ---------------- | ------------------ |
| `stock >= quantidade` | Verde            | Stock suficiente   |
| `stock < quantidade`  | Laranja/Vermelho | Stock insuficiente |
| Serviço               | Nenhuma          | Não aplicável      |

### Mensagens de Feedback

**Encomenda Criada (com stock OK):**

```
✅ Encomenda criada com sucesso!
```

**Encomenda Criada (stock insuficiente):**

```
✅ Encomenda criada com sucesso! ATENÇÃO: Alguns artigos têm stock insuficiente. Considere criar encomendas ao fornecedor.
```

**Encomenda Fechada (stock atualizado):**

```
✅ Encomenda atualizada com sucesso! Stock atualizado.
```

**Encomenda Reaberta:**

```
✅ Encomenda atualizada com sucesso!
```

---

## Dados Passados ao Frontend

### Controllers (`create()` e `edit()`)

```php
$articles = Article::ativos()
    ->orderBy('nome')
    ->get([
        'id',
        'nome as name',
        'preco_com_iva as unit_price',
        'referencia as reference',
        'stock_quantidade',  // ← Adicionado
        'tipo'               // ← Adicionado
    ]);
```

### Props no Vue

```javascript
const props = defineProps({
    customers: Array,
    articles: Array, // Agora inclui stock_quantidade e tipo
    suppliers: Array,
    nextNumber: String,
});
```

---

## Activity Logs

### Propriedades Registradas

```php
activity()
    ->performedOn($customerOrder)
    ->causedBy(Auth::user())
    ->withProperties([
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'items_count' => count($validated['items']),
        'status_change' => $oldStatus !== $newStatus ? "$oldStatus -> $newStatus" : null,
        'stock_updated' => $shouldUpdateStock,
        'stock_warnings' => $stockWarnings
    ])
    ->log('updated');
```

---

## Regras de Negócio

### ✅ Permitido

-   Criar encomenda mesmo sem stock suficiente
-   Editar encomenda em rascunho sem atualizar stock
-   Reabrir encomenda fechada (stock é reposto)
-   Serviços não afetam stock

### ❌ Não Permitido

-   Stock negativo (usa `max(0, stock - quantidade)`)
-   Atualizar stock quando encomenda permanece em draft
-   Decrementar stock de serviços

### 🔄 Transições de Status e Stock

| Transição       | Ação no Stock    |
| --------------- | ---------------- |
| draft → draft   | Nada             |
| draft → closed  | Decrementa stock |
| closed → closed | Nada             |
| closed → draft  | Repõe stock      |

---

## Testes

### Executar Testes

```bash
php test_stock_management.php
```

### Casos de Teste Cobertos

1. ✅ Verificação de stock com `hasStock()`
2. ✅ Criação de encomenda com stock insuficiente
3. ✅ Decremento de stock com `decreaseStock()`
4. ✅ Incremento de stock com `increaseStock()`
5. ✅ Serviços sempre têm stock disponível
6. ✅ Stock não fica negativo
7. ✅ Alertas visuais no frontend

---

## Melhorias Futuras

### Possíveis Adições

1. **Reserva de Stock:**

    - Stock físico vs. stock disponível
    - Encomendas em draft reservam stock

2. **Histórico de Movimentos:**

    - Tabela `stock_movements`
    - Rastreio completo de entradas/saídas

3. **Alertas Automáticos:**

    - Email quando stock < mínimo
    - Notificações no dashboard

4. **Stock por Armazém:**

    - Múltiplos locais de armazenamento
    - Transferências entre armazéns

5. **Previsão de Reabastecimento:**
    - Baseado em histórico de vendas
    - Sugestão automática de quantidades

---

## Arquivos Modificados

### Backend

-   `app/Models/Article.php` - Métodos `hasStock()`, `decreaseStock()`, `increaseStock()`
-   `app/Http/Controllers/CustomerOrderController.php` - Lógica de validação e atualização

### Frontend

-   `resources/js/Pages/CustomerOrders/Create.vue` - Indicador visual de stock
-   `resources/js/Pages/CustomerOrders/Edit.vue` - Indicador visual de stock

### Testes

-   `test_stock_management.php` - Script de teste completo

---

## Versão

**v0.23.0** - Gestão de Stock (17 Nov 2025)
