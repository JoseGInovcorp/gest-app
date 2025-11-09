# Módulo de Encomendas de Clientes

## Visão Geral

O módulo de Encomendas de Clientes permite gerir todo o processo de encomendas dos clientes, desde a criação em rascunho até ao fecho da encomenda.

## Funcionalidades Implementadas

### 1. Estrutura de Base de Dados

#### Tabela: `customer_orders`

-   `id` - Identificador único
-   `number` - Número da encomenda (único, formato: EC-YYYY-####)
-   `proposal_date` - Data da proposta (quando fica fechado)
-   `validity_date` - Data de validade da encomenda
-   `customer_id` - Relação com a entidade cliente (FK para `entities`)
-   `status` - Estado: 'draft' (Rascunho) ou 'closed' (Fechado)
-   `total_value` - Valor total calculado automaticamente
-   `notes` - Notas adicionais
-   `timestamps` - created_at, updated_at
-   `soft_deletes` - deleted_at

#### Tabela: `customer_order_items`

-   `id` - Identificador único
-   `customer_order_id` - Relação com a encomenda (FK para `customer_orders`)
-   `article_id` - Relação com o artigo (FK para `articles`)
-   `supplier_id` - Fornecedor associado (FK para `entities`, opcional)
-   `quantity` - Quantidade (decimal 10,2)
-   `unit_price` - Preço unitário (decimal 12,2)
-   `total` - Total da linha (calculado automaticamente)
-   `notes` - Notas da linha
-   `timestamps` - created_at, updated_at

### 2. Models

#### `CustomerOrder`

**Relações:**

-   `customer()` - BelongsTo Entity (cliente)
-   `items()` - HasMany CustomerOrderItem

**Métodos:**

-   `calculateTotal()` - Calcula e atualiza o valor total
-   `generateNumber()` - Gera o próximo número de encomenda (EC-YYYY-####)

**Casts:**

-   `proposal_date` - date
-   `validity_date` - date
-   `total_value` - decimal:2

#### `CustomerOrderItem`

**Relações:**

-   `customerOrder()` - BelongsTo CustomerOrder
-   `article()` - BelongsTo Article
-   `supplier()` - BelongsTo Entity (fornecedor)

**Funcionalidades Automáticas:**

-   Cálculo automático do total da linha (quantity × unit_price)
-   Atualização automática do total da encomenda quando itens são adicionados/modificados/removidos

**Casts:**

-   `quantity` - decimal:2
-   `unit_price` - decimal:2
-   `total` - decimal:2

### 3. Controller: `CustomerOrderController`

**Métodos CRUD:**

-   `index()` - Lista todas as encomendas com informações do cliente
-   `create()` - Formulário de criação (pré-carrega clientes, artigos, fornecedores)
-   `store()` - Guarda nova encomenda com itens (transação DB)
-   `edit()` - Formulário de edição com dados pré-preenchidos
-   `update()` - Atualiza encomenda e itens (transação DB)
-   `destroy()` - Elimina encomenda (soft delete)

**Métodos Adicionais:**

-   `convertToSupplierOrders()` - Converte encomenda em encomendas de fornecedor (TODO)
-   `generatePDF()` - Gera PDF da encomenda (TODO)

### 4. Rotas

```php
// CRUD básico
GET     /customer-orders                 -> index
GET     /customer-orders/create          -> create
POST    /customer-orders                 -> store
GET     /customer-orders/{id}/edit       -> edit
PATCH   /customer-orders/{id}            -> update
DELETE  /customer-orders/{id}            -> destroy

// Funcionalidades adicionais
POST    /customer-orders/{id}/convert-to-supplier-orders  -> convertToSupplierOrders
GET     /customer-orders/{id}/pdf                         -> generatePDF
```

**Permissões:**

-   `customer-orders.create` - Criar encomendas
-   `customer-orders.read` - Ver encomendas
-   `customer-orders.update` - Editar encomendas
-   `customer-orders.delete` - Eliminar encomendas

### 5. Interface (Vue 3 + Inertia.js)

#### `Index.vue` - Lista de Encomendas

**Características:**

-   Header com ícone ShoppingCart (azul)
-   Breadcrumbs: Dashboard / Encomendas
-   Filtros: Pesquisa (número, cliente) e Estado (draft/closed)
-   Tabela com colunas:
    -   Data (proposal_date)
    -   Número
    -   Validade
    -   Cliente
    -   Valor Total (formatado em EUR)
    -   Estado (badge colorido)
    -   Ações (Editar, Eliminar)
-   Formatação de datas (pt-PT)
-   Formatação de moeda (EUR)

#### `Create.vue` - Criar Encomenda

**Campos:**

-   Número (auto-gerado, readonly)
-   Cliente (dropdown)
-   Data da Proposta (date)
-   Validade (date)
-   Estado (dropdown: Rascunho/Fechado)
-   Notas (textarea)

**Linhas de Artigos:**

-   Adicionar/Remover artigos dinamicamente
-   Por cada artigo:
    -   Artigo (dropdown com referência e nome)
    -   Fornecedor (dropdown, opcional)
    -   Quantidade (número)
    -   Preço Unitário (número, pré-preenchido com sale_price)
    -   Total da Linha (calculado automaticamente)
-   Total Geral (soma de todas as linhas)

**Validações:**

-   Cliente obrigatório
-   Estado obrigatório
-   Mínimo 1 artigo
-   Quantidade mínima: 0.01
-   Preço mínimo: 0

#### `Edit.vue` - Editar Encomenda

Idêntico ao Create.vue mas:

-   Dados pré-preenchidos
-   Número não editável
-   Botão "Atualizar Encomenda" em vez de "Guardar"
-   Usa PATCH em vez de POST

### 6. Permissões

**Criadas:**

-   `customer-orders.create`
-   `customer-orders.read`
-   `customer-orders.update`
-   `customer-orders.delete`

**Atribuição de Grupos:**

-   **Administrador:** Todas as permissões
-   **Gestor:** Todas as permissões
-   **Utilizador:** read, create

### 7. Menu de Navegação

**Localização:** Menu "Encomendas" (ShoppingCart icon)

-   **Encomendas - Clientes** → `customer-orders.index` (ativo)
-   Encomendas - Fornecedores (desativado, futuro)
-   Ordens de Trabalho (desativado, futuro)

## Próximos Passos (TODO)

### 1. Conversão para Encomendas de Fornecedor

Funcionalidade: `convertToSupplierOrders()`

-   Agrupar itens por fornecedor
-   Criar uma encomenda de fornecedor para cada grupo
-   Copiar informações relevantes
-   Marcar encomenda original como convertida

### 2. Geração de PDF

Funcionalidade: `generatePDF()`

-   Layout similar às Propostas
-   Cabeçalho: "ENCOMENDA"
-   Numeração específica (EC-YYYY-####)
-   Informações do cliente
-   Tabela de artigos
-   Totais
-   Notas

### 3. Melhorias de UX

-   [ ] Pesquisa de artigos por referência ou nome (autocomplete)
-   [ ] Duplicar encomenda existente
-   [ ] Histórico de alterações
-   [ ] Notificações de validade

### 4. Relatórios

-   [ ] Encomendas por período
-   [ ] Encomendas por cliente
-   [ ] Análise de artigos mais encomendados
-   [ ] Valor total de encomendas

## Arquivos Criados/Modificados

### Backend

-   `database/migrations/2025_11_09_170315_create_customer_orders_table.php`
-   `database/migrations/2025_11_09_170405_create_customer_order_items_table.php`
-   `app/Models/CustomerOrder.php`
-   `app/Models/CustomerOrderItem.php`
-   `app/Http/Controllers/CustomerOrderController.php`
-   `database/seeders/CustomerOrdersPermissionsSeeder.php`
-   `routes/web.php` (adicionadas rotas)

### Frontend

-   `resources/js/Pages/CustomerOrders/Index.vue`
-   `resources/js/Pages/CustomerOrders/Create.vue`
-   `resources/js/Pages/CustomerOrders/Edit.vue`
-   `resources/js/Layouts/AuthenticatedLayout.vue` (atualizado menu)

## Comandos Executados

```bash
# Criar migrations
php artisan make:migration create_customer_orders_table
php artisan make:migration create_customer_order_items_table

# Criar models
php artisan make:model CustomerOrder
php artisan make:model CustomerOrderItem

# Criar controller
php artisan make:controller CustomerOrderController

# Criar seeder
php artisan make:seeder CustomerOrdersPermissionsSeeder

# Executar migrations
php artisan migrate

# Executar seeder
php artisan db:seed --class=CustomerOrdersPermissionsSeeder

# Compilar frontend
npm run build
```

## Notas Técnicas

### Cálculo Automático de Totais

O modelo `CustomerOrderItem` utiliza eventos do Eloquent:

-   `saving` - Calcula total da linha antes de guardar
-   `saved` - Atualiza total da encomenda após guardar
-   `deleted` - Atualiza total da encomenda após eliminar

### Transações de Base de Dados

Os métodos `store()` e `update()` do controller utilizam transações DB para garantir consistência:

-   Se falhar ao criar/atualizar itens, a encomenda também não é guardada
-   Rollback automático em caso de erro

### Numeração Automática

Formato: `EC-YYYY-####`

-   EC = Encomenda Cliente
-   YYYY = Ano atual
-   #### = Número sequencial (0001, 0002, etc.)
-   Reinicia a cada ano

### Validações Frontend

-   Cliente obrigatório
-   Estado obrigatório
-   Mínimo 1 artigo
-   Quantidade ≥ 0.01
-   Preço ≥ 0

### Formatação

-   Datas: formato português (dd/mm/yyyy)
-   Moeda: EUR com símbolo €
-   Decimais: 2 casas para valores monetários
