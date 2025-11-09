# Módulo de Encomendas (Clientes e Fornecedores)

## Visão Geral

O módulo de Encomendas é composto por dois submódulos interligados:

-   **Encomendas - Clientes**: Gestão de encomendas recebidas de clientes
-   **Encomendas - Fornecedores**: Gestão de encomendas enviadas a fornecedores

Estes módulos permitem converter automaticamente encomendas de clientes em encomendas de fornecedores, agrupando itens por fornecedor.

---

## 1. Encomendas - Clientes

### 1.1 Estrutura de Base de Dados

#### Tabela: `customer_orders`

```sql
- id (bigint, PK)
- number (string, unique) - Formato: EC-YYYY-#### (Ex: EC-2025-0001)
- proposal_date (date, nullable) - Data de criação da encomenda
- validity_date (date, nullable) - Data de validade
- customer_id (FK → entities.id) - Cliente associado
- status (enum: 'draft', 'closed') - Estado da encomenda
- total_value (decimal 12,2) - Valor total (calculado automaticamente)
- notes (text, nullable) - Observações
- timestamps (created_at, updated_at)
- deleted_at (soft deletes)
```

#### Tabela: `customer_order_items`

```sql
- id (bigint, PK)
- customer_order_id (FK → customer_orders.id)
- article_id (FK → articles.id) - Artigo encomendado
- supplier_id (FK → entities.id, nullable) - Fornecedor associado
- quantity (decimal 10,2) - Quantidade
- unit_price (decimal 12,2) - Preço unitário
- total (decimal 12,2) - Total da linha (auto-calculado)
- notes (text, nullable)
- timestamps
```

### 1.2 Models

#### `CustomerOrder`

**Fillable:**

```php
'number', 'proposal_date', 'validity_date', 'customer_id',
'status', 'total_value', 'notes'
```

**Casts:**

```php
'proposal_date' => 'date',
'validity_date' => 'date',
'total_value' => 'decimal:2'
```

**Relações:**

-   `customer()` - BelongsTo Entity
-   `items()` - HasMany CustomerOrderItem

**Métodos:**

-   `generateNumber()` - Gera número sequencial EC-YYYY-####
-   `calculateTotal()` - Calcula soma dos totais dos itens

**Boot Events:**

-   `creating` - Gera número automaticamente se não fornecido

#### `CustomerOrderItem`

**Fillable:**

```php
'customer_order_id', 'article_id', 'supplier_id',
'quantity', 'unit_price', 'total', 'notes'
```

**Casts:**

```php
'quantity' => 'decimal:2',
'unit_price' => 'decimal:2',
'total' => 'decimal:2'
```

**Relações:**

-   `customerOrder()` - BelongsTo CustomerOrder
-   `article()` - BelongsTo Article
-   `supplier()` - BelongsTo Entity

**Boot Events:**

-   `saving` - Calcula total (quantity × unit_price)
-   `saved` - Recalcula total da encomenda
-   `deleted` - Recalcula total da encomenda

### 1.3 Controller: `CustomerOrderController`

**Métodos:**

```php
index(Request $request)
  - Lista encomendas com filtros (search, status)
  - Paginação: 15 registos
  - Eager loading: customer

create()
  - Busca clientes (type: client/both, active)
  - Busca artigos ativos (scope: ativos)
  - Busca fornecedores (type: supplier/both, active)
  - Gera próximo número

store(Request $request)
  - Validação: customer_id, status, items (min:1)
  - Transação DB
  - Gera número automaticamente
  - Cria encomenda e itens
  - Redireciona para index com mensagem

edit(CustomerOrder $customerOrder)
  - Carrega encomenda com relações
  - Busca listas para dropdowns
  - Retorna vista de edição

update(Request $request, CustomerOrder $customerOrder)
  - Valida dados
  - Atualiza encomenda
  - Remove itens antigos e cria novos
  - Transação DB

destroy(CustomerOrder $customerOrder)
  - Soft delete da encomenda
  - Itens eliminados em cascata

convertToSupplierOrders(CustomerOrder $customerOrder)
  - Valida se status = 'closed'
  - Carrega itens com fornecedor
  - Agrupa itens por supplier_id
  - Cria uma SupplierOrder para cada fornecedor
  - Retorna mensagem de sucesso

generatePDF(CustomerOrder $customerOrder)
  - TODO: Implementar geração de PDF
```

### 1.4 Rotas

```php
// Web.php
Route::get('/customer-orders/create')->name('customer-orders.create')
  ->middleware('permission:customer-orders.create');

Route::middleware('permission:customer-orders.read')->group(function () {
    Route::get('/customer-orders')->name('customer-orders.index');
    Route::get('/customer-orders/{customerOrder}')->name('customer-orders.show');
});

Route::post('/customer-orders')->name('customer-orders.store')
  ->middleware('permission:customer-orders.create');

Route::get('/customer-orders/{customerOrder}/edit')->name('customer-orders.edit')
  ->middleware('permission:customer-orders.update');

Route::patch('/customer-orders/{customerOrder}')->name('customer-orders.update')
  ->middleware('permission:customer-orders.update');

Route::delete('/customer-orders/{customerOrder}')->name('customer-orders.destroy')
  ->middleware('permission:customer-orders.delete');

Route::post('/customer-orders/{customerOrder}/convert-to-supplier-orders')
  ->name('customer-orders.convert');

Route::get('/customer-orders/{customerOrder}/pdf')
  ->name('customer-orders.pdf');
```

### 1.5 Permissões

```
- customer-orders.create - Criar encomendas
- customer-orders.read - Ver encomendas
- customer-orders.update - Editar encomendas
- customer-orders.delete - Eliminar encomendas
```

**Roles com acesso:**

-   Super Admin
-   Administrador
-   Gestor Comercial
-   Gestor Financeiro
-   Visualizador

### 1.6 Frontend (Vue 3 + Inertia.js)

#### `Index.vue`

-   Header com ícone ShoppingCart (azul)
-   Breadcrumbs: Dashboard / Encomendas
-   Filtros: Search (número/cliente), Status
-   Botão "Nova Encomenda"
-   Tabela sem paginação (todas as encomendas)
-   Colunas: Data, Número, Validade, Cliente, Valor Total, Estado, Ações
-   Badges coloridos para estados

#### `Create.vue`

-   Informação do próximo número (auto-gerado)
-   Campos: Cliente*, Data de Criação, Validade, Estado*, Notas
-   Seção de artigos dinâmica
-   Por artigo: Artigo*, Fornecedor, Quantidade*, Preço Unit.\*
-   Botão "Adicionar Artigo"
-   Preço automático ao selecionar artigo
-   Total calculado em tempo real
-   Quantidade: incremento de 1 em 1 (min: 1)

#### `Edit.vue`

-   Mesma estrutura do Create
-   Campo número readonly
-   Dados pré-preenchidos
-   Itens carregados da encomenda
-   **Botão "Converter para Encomendas Fornecedor"** (apenas se status = 'closed')
    -   Cor verde com ícone Truck
    -   Confirmação antes de converter
    -   Redireciona para customer-orders.index após conversão

---

## 2. Encomendas - Fornecedores

### 2.1 Estrutura de Base de Dados

#### Tabela: `supplier_orders`

```sql
- id (bigint, PK)
- number (string, unique) - Formato: EF-YYYY-#### (Ex: EF-2025-0001)
- order_date (date, nullable) - Data da encomenda
- delivery_date (date, nullable) - Data de entrega prevista
- supplier_id (FK → entities.id) - Fornecedor
- customer_order_id (FK → customer_orders.id, nullable) - Encomenda cliente origem
- status (enum: 'draft', 'sent', 'confirmed', 'received', 'cancelled')
- total_value (decimal 12,2) - Valor total (auto-calculado)
- notes (text, nullable)
- timestamps (created_at, updated_at)
- deleted_at (soft deletes)
```

#### Tabela: `supplier_order_items`

```sql
- id (bigint, PK)
- supplier_order_id (FK → supplier_orders.id)
- article_id (FK → articles.id)
- quantity (decimal 10,2)
- unit_price (decimal 12,2)
- total (decimal 12,2) - Auto-calculado
- notes (text, nullable)
- timestamps
```

### 2.2 Models

#### `SupplierOrder`

**Fillable:**

```php
'number', 'order_date', 'delivery_date', 'supplier_id',
'customer_order_id', 'status', 'total_value', 'notes'
```

**Casts:**

```php
'order_date' => 'date',
'delivery_date' => 'date',
'total_value' => 'decimal:2'
```

**Relações:**

-   `supplier()` - BelongsTo Entity
-   `customerOrder()` - BelongsTo CustomerOrder (encomenda origem)
-   `items()` - HasMany SupplierOrderItem

**Métodos:**

-   `generateNumber()` - Gera EF-YYYY-####
-   `calculateTotal()` - Soma totais dos itens

#### `SupplierOrderItem`

**Fillable:**

```php
'supplier_order_id', 'article_id', 'quantity',
'unit_price', 'total', 'notes'
```

**Casts:**

```php
'quantity' => 'decimal:2',
'unit_price' => 'decimal:2',
'total' => 'decimal:2'
```

**Relações:**

-   `supplierOrder()` - BelongsTo SupplierOrder
-   `article()` - BelongsTo Article

**Boot Events:**

-   `saving` - Calcula total
-   `saved/deleted` - Recalcula total da encomenda

### 2.3 Controller: `SupplierOrderController`

**Métodos:**

```php
index(Request $request)
  - Filtros: search (número/fornecedor), status
  - Paginação: 15 registos
  - Eager loading: supplier

create()
  - Busca fornecedores ativos
  - Busca artigos ativos
  - Gera próximo número

store(Request $request)
  - Validação similar a customer orders
  - Estados: draft, sent, confirmed, received, cancelled
  - Transação DB

edit(SupplierOrder $supplierOrder)
  - Carrega com relações
  - Listas para dropdowns

update(Request $request, SupplierOrder $supplierOrder)
  - Atualiza encomenda
  - Remove e recria itens

destroy(SupplierOrder $supplierOrder)
  - Soft delete

generatePDF(SupplierOrder $supplierOrder)
  - TODO: Implementar
```

### 2.4 Rotas

```php
Route::get('/supplier-orders/create')->name('supplier-orders.create')
  ->middleware('permission:supplier-orders.create');

Route::middleware('permission:supplier-orders.read')->group(function () {
    Route::get('/supplier-orders')->name('supplier-orders.index');
    Route::get('/supplier-orders/{supplierOrder}')->name('supplier-orders.show');
});

Route::post('/supplier-orders')->name('supplier-orders.store')
  ->middleware('permission:supplier-orders.create');

Route::get('/supplier-orders/{supplierOrder}/edit')->name('supplier-orders.edit')
  ->middleware('permission:supplier-orders.update');

Route::patch('/supplier-orders/{supplierOrder}')->name('supplier-orders.update')
  ->middleware('permission:supplier-orders.update');

Route::delete('/supplier-orders/{supplierOrder}')->name('supplier-orders.destroy')
  ->middleware('permission:supplier-orders.delete');

Route::get('/supplier-orders/{supplierOrder}/pdf')
  ->name('supplier-orders.pdf');
```

### 2.5 Permissões

```
- supplier-orders.create
- supplier-orders.read
- supplier-orders.update
- supplier-orders.delete
```

**Auto-atribuição:** Todos os roles com `orders.*` recebem automaticamente `supplier-orders.*`

### 2.6 Frontend

#### `Index.vue`

-   Header verde (Package icon)
-   Filtros: Search, Status
-   Paginação corrigida (links null tratados)
-   Estados: Rascunho, Enviado, Confirmado, Recebido, Cancelado

#### `Create.vue`

-   Campos: Fornecedor*, Data Encomenda, Data Entrega, Estado*, Notas
-   Artigos dinâmicos
-   Preço automático
-   Total calculado

#### `Edit.vue`

-   Edição completa
-   Mesmas funcionalidades do Create

---

## 3. Fluxo de Conversão

### 3.1 Processo

1. **Criar Encomenda Cliente**

    - Adicionar artigos
    - Associar fornecedores aos artigos (opcional)
    - Guardar como "Rascunho"

2. **Fechar Encomenda**

    - Editar encomenda
    - Alterar estado para "Fechado"
    - Guardar

3. **Converter**

    - Botão verde aparece no topo da página de edição
    - Clicar "Converter para Encomendas Fornecedor"
    - Confirmar ação

4. **Resultado**
    - Sistema agrupa itens por `supplier_id`
    - Cria uma `SupplierOrder` para cada fornecedor
    - Cada encomenda:
        - Status: draft
        - order_date: now()
        - customer_order_id: referência à origem
        - notes: "Gerada a partir da encomenda de cliente EC-YYYY-####"
    - Redireciona para customer-orders.index
    - Mensagem de sucesso lista números criados

### 3.2 Validações

-   Apenas encomendas com `status = 'closed'` podem ser convertidas
-   Apenas itens com `supplier_id != null` são incluídos
-   Se não houver itens com fornecedor, retorna erro

### 3.3 Rastreabilidade

-   Campo `customer_order_id` em `supplier_orders` mantém link
-   Relação `customerOrder()` permite navegar de volta
-   Notas automáticas identificam origem

---

## 4. Seeders

### 4.1 CustomerOrdersPermissionsSeeder

```php
// Cria permissões
customer-orders.create
customer-orders.read
customer-orders.update
customer-orders.delete

// Atribui automaticamente a roles com orders.*
- Procura roles com whereHas('permissions', like 'orders.%')
- Atribui todas as 4 permissões
```

### 4.2 SupplierOrdersPermissionsSeeder

```php
// Cria permissões
supplier-orders.create
supplier-orders.read
supplier-orders.update
supplier-orders.delete

// Atribui automaticamente a roles com orders.*
- Mesma lógica do CustomerOrders
```

**Execução:**

```bash
php artisan db:seed --class=CustomerOrdersPermissionsSeeder
php artisan db:seed --class=SupplierOrdersPermissionsSeeder
```

---

## 5. Menu Sidebar

### Configuração

```vue
// AuthenticatedLayout.vue const allOrdersNavigationItems = [ { name:
"Encomendas - Clientes", href: "customer-orders.index", icon: ShoppingCart,
permission: "customer-orders", // Verifica customer-orders.* }, { name:
"Encomendas - Fornecedores", href: "supplier-orders.index", icon: Truck,
permission: "supplier-orders", // Verifica supplier-orders.* }, { name: "Ordens
de Trabalho", href: "dashboard", icon: Briefcase, disabled: true, permission:
"work-orders", }, ];
```

---

## 6. Correções Implementadas

### 6.1 Geração de Números

**Problema:** Números duplicados por não verificar soft deletes

**Solução:**

```php
$lastOrder = static::withTrashed()
    ->where('number', 'like', $prefix . '%')
    ->orderBy('number', 'desc')
    ->first();
```

### 6.2 Queries de Entities

**Problema:** Uso de colunas `is_customer`/`is_supplier` inexistentes

**Solução:**

```php
// Clientes
Entity::whereIn('type', ['client', 'both'])
    ->where('active', true)

// Fornecedores
Entity::whereIn('type', ['supplier', 'both'])
    ->where('active', true)
```

### 6.3 Nomes de Colunas de Articles

**Problema:** Tentativa de usar colunas em inglês (`name`, `unit_price`, `reference`)

**Solução:**

```php
Article::ativos()
    ->orderBy('nome')
    ->get(['id', 'nome as name', 'preco as unit_price', 'referencia as reference']);
```

### 6.4 Validação de Quantidade

**Problema:** Backend aceitava `min:0.01` mas frontend usava `step="1" min="1"`

**Solução:**

```php
// Backend
'items.*.quantity' => 'required|numeric|min:1'

// Frontend
step="1" min="1"
```

### 6.5 Paginação com Links Null

**Problema:** Inertia Link não aceita `href=null`

**Solução:**

```vue
<template v-for="link in orders.links" :key="link.label">
    <Link v-if="link.url" :href="link.url" ... />
    <span v-else ... />
    <!-- Desabilitado -->
</template>
```

---

## 7. Melhorias Futuras

### 7.1 Funcionalidades Pendentes

-   [ ] Geração de PDF para encomendas (clientes e fornecedores)
-   [ ] Página de visualização (show) com mais detalhes
-   [ ] Histórico de alterações de estado
-   [ ] Notificações por email ao criar/enviar encomendas
-   [ ] Dashboard com estatísticas de encomendas
-   [ ] Exportação para Excel/CSV
-   [ ] Duplicar encomenda
-   [ ] Cancelamento com motivo
-   [ ] Anexos (ficheiros PDF, imagens)

### 7.2 Otimizações

-   [ ] Cache de listas de clientes/fornecedores/artigos
-   [ ] Autocomplete para artigos (muitos registos)
-   [ ] Lazy loading de itens (para encomendas grandes)
-   [ ] Validação em tempo real no frontend
-   [ ] Testes unitários e feature tests

### 7.3 UX

-   [ ] Atalhos de teclado
-   [ ] Calculadora de totais com IVA
-   [ ] Sugestões de fornecedores baseadas em histórico
-   [ ] Timeline visual do processo
-   [ ] Impressão otimizada

---

## 8. Comandos Úteis

```bash
# Migrations
php artisan migrate

# Seeders
php artisan db:seed --class=CustomerOrdersPermissionsSeeder
php artisan db:seed --class=SupplierOrdersPermissionsSeeder

# Limpar caches
php artisan permission:cache-reset
php artisan optimize:clear

# Frontend
npm run build
npm run dev

# Testes (quando implementados)
php artisan test --filter CustomerOrder
php artisan test --filter SupplierOrder
```

---

## 9. Dependências

### Backend

-   Laravel 12.36.1
-   Spatie Laravel-Permission
-   Inertia.js Laravel Adapter

### Frontend

-   Vue 3
-   Inertia.js
-   Lucide Vue Next (ícones)
-   Vite 7.1.12

### Base de Dados

-   MySQL
-   Tabelas relacionadas: `entities`, `articles`, `customer_orders`, `customer_order_items`, `supplier_orders`, `supplier_order_items`

---

**Última atualização:** 9 de Novembro de 2025
