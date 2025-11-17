# Módulo de Ordens de Trabalho

**Versão:** 0.19.0  
**Data:** 16 Nov 2025  
**Status:** ✅ Completo e Funcional

---

## 📋 Visão Geral

O módulo de Ordens de Trabalho é o sistema central de gestão operacional do Gest-App. Permite registar, atribuir e acompanhar tarefas relacionadas com o processamento de encomendas de clientes, desde a validação de stock até à entrega final.

### Características Principais

-   ✅ **Criação Automática** - Workflow gerado automaticamente quando uma encomenda é criada
-   ✅ **Duas Rotas de Workflow** - Envio (9 tarefas) vs Levantamento (7 tarefas)
-   ✅ **Dependências Sequenciais** - Tarefas bloqueadas até conclusão das dependências
-   ✅ **Atribuições por Grupo** - Tarefas atribuídas automaticamente a papéis específicos
-   ✅ **Dashboard Pessoal** - Vista "Minhas Tarefas" para cada utilizador
-   ✅ **Workflow Flexível** - Possibilidade de adicionar/remover tarefas manualmente
-   ✅ **Rastreabilidade Total** - Activity log completo de todas as operações

---

## 🗄️ Estrutura de Base de Dados

### Tabela: work_orders

```sql
id                  - bigint (PK)
customer_order_id   - bigint (FK → customer_orders) NULLABLE CASCADE
title               - string(255)
description         - text NULLABLE
priority            - enum('baixa', 'normal', 'alta', 'urgente')
status              - enum('pendente', 'em_progresso', 'concluida', 'cancelada')
created_by          - bigint (FK → users) NULLABLE
created_at          - timestamp
updated_at          - timestamp
deleted_at          - timestamp NULLABLE (soft delete)
```

**Índices:**

-   `customer_order_id` (foreign key)
-   `created_by` (foreign key)
-   `status`
-   `priority`

### Tabela: work_order_tasks

```sql
id                  - bigint (PK)
work_order_id       - bigint (FK → work_orders) CASCADE
task_type           - string(50)
title               - string(255)
description         - text NULLABLE
assigned_to         - bigint (FK → users) NULLABLE
assigned_group      - string(100) NULLABLE
status              - enum('pendente', 'em_progresso', 'concluida', 'cancelada')
sequence_order      - integer
depends_on_task_id  - bigint (FK → work_order_tasks) NULLABLE
due_date            - date NULLABLE
completed_at        - timestamp NULLABLE
notes               - text NULLABLE
created_at          - timestamp
updated_at          - timestamp
deleted_at          - timestamp NULLABLE (soft delete)
```

**Índices:**

-   `work_order_id` (foreign key)
-   `assigned_to` (foreign key)
-   `depends_on_task_id` (foreign key)
-   `status`
-   `due_date`

---

## 📦 Modelos (Models)

### WorkOrder Model

**Localização:** `app/Models/WorkOrder.php`

**Traits:**

-   `SoftDeletes` - Eliminação suave
-   `LogsActivity` (Spatie) - Histórico de atividades

**Relationships:**

```php
customerOrder()  → BelongsTo CustomerOrder
creator()        → BelongsTo User (created_by)
tasks()          → HasMany WorkOrderTask (ordered by sequence_order)
```

**Scopes:**

```php
scopePendente($query)      - Ordens com status 'pendente'
scopeEmProgresso($query)   - Ordens com status 'em_progresso'
scopeConcluida($query)     - Ordens com status 'concluida'
scopePrioridade($query, $priority) - Filtrar por prioridade
```

**Helper Methods:**

```php
updateStatus()                       - Atualiza status baseado em tarefas
getProgressPercentageAttribute()     - Calcula % de conclusão
```

### WorkOrderTask Model

**Localização:** `app/Models/WorkOrderTask.php`

**Traits:**

-   `SoftDeletes`
-   `LogsActivity`

**Relationships:**

```php
workOrder()     → BelongsTo WorkOrder
assignedUser()  → BelongsTo User (assigned_to)
dependsOn()     → BelongsTo WorkOrderTask (depends_on_task_id)
```

**Scopes:**

```php
scopePendente($query)
scopeEmProgresso($query)
scopeConcluida($query)
scopeAssignedTo($query, $userId)
scopeAssignedToGroup($query, $groupName)
scopeOverdue($query)
```

**Helper Methods:**

```php
canStart()                  - Verifica se dependências estão completas
complete($notes)            - Marca tarefa como concluída
getIsOverdueAttribute()     - Computed: se está atrasada
getCanStartAttribute()      - Computed: se pode iniciar
taskTypes()                 - Static: retorna tipos de tarefas
```

---

## 🔄 Tipos de Tarefas

### 10 Task Types Disponíveis

| Code                    | Label                              | Grupo Padrão             |
| ----------------------- | ---------------------------------- | ------------------------ |
| `VALIDATE_STOCK`        | Validar Disponibilidade em Armazém | Gestor Comercial         |
| `CREATE_SUPPLIER_ORDER` | Criar Encomenda a Fornecedor       | Gestor Comercial         |
| `RECEIVE_STOCK`         | Receção de Artigo em Armazém       | Gestor de Armazém        |
| `WAREHOUSE_PICK`        | Recolha do Armazém                 | Gestor de Armazém        |
| `PACKAGING`             | Embalamento                        | Gestor de Armazém        |
| `CREATE_SHIPPING_GUIDE` | Criar Guia de Transporte           | Gestor Financeiro        |
| `SCHEDULE_PICKUP`       | Agendar Recolha por Transportadora | Gestor de Armazém        |
| `SHIPPED`               | Encomenda Enviada                  | Gestor de Armazém        |
| `READY_FOR_PICKUP`      | Disponível para Levantamento       | Gestor Comercial         |
| `DELIVERED`             | Entregue ao Cliente                | Gestor Comercial/Armazém |

---

## 🔁 Workflows Automáticos

### Workflow de Envio (Shipping) - 9 Tarefas

```
1. VALIDATE_STOCK         → Gestor Comercial
2. CREATE_SUPPLIER_ORDER  → Gestor Comercial
3. RECEIVE_STOCK          → Gestor de Armazém
4. WAREHOUSE_PICK         → Gestor de Armazém
5. PACKAGING              → Gestor de Armazém
6. CREATE_SHIPPING_GUIDE  → Gestor Financeiro
7. SCHEDULE_PICKUP        → Gestor de Armazém
8. SHIPPED                → Gestor de Armazém
9. DELIVERED              → Gestor Comercial
```

### Workflow de Levantamento (Pickup) - 7 Tarefas

```
1. VALIDATE_STOCK         → Gestor Comercial
2. CREATE_SUPPLIER_ORDER  → Gestor Comercial
3. RECEIVE_STOCK          → Gestor de Armazém
4. WAREHOUSE_PICK         → Gestor de Armazém
5. PACKAGING              → Gestor de Armazém
6. READY_FOR_PICKUP       → Gestor Comercial
7. DELIVERED              → Gestor Comercial
```

**Lógica de Seleção:**

-   Se `customer_order.delivery_method == 'pickup'` → Workflow Pickup
-   Caso contrário → Workflow Shipping

**Dependências:**

-   Cada tarefa depende da tarefa anterior (depends_on_task_id)
-   Tarefa #1 não tem dependências (pode iniciar imediatamente)
-   Tarefas bloqueadas até dependências completas

**Prazos:**

-   Cada tarefa: +1 dia a partir da data atual
-   Tarefa 1: hoje, Tarefa 2: amanhã, etc.

---

## 🎯 Controller & Endpoints

### WorkOrderController

**Localização:** `app/Http/Controllers/WorkOrderController.php`

#### Endpoints Principais

| Method | Route                   | Permission           | Descrição                                 |
| ------ | ----------------------- | -------------------- | ----------------------------------------- |
| GET    | `/work-orders/my-tasks` | -                    | Dashboard pessoal (tarefas do utilizador) |
| GET    | `/work-orders`          | `work-orders.read`   | Listar todas as ordens                    |
| GET    | `/work-orders/create`   | `work-orders.create` | Formulário criação                        |
| POST   | `/work-orders`          | `work-orders.create` | Criar ordem                               |
| GET    | `/work-orders/{id}`     | `work-orders.read`   | Ver detalhes                              |
| PATCH  | `/work-orders/{id}`     | `work-orders.update` | Atualizar ordem                           |
| DELETE | `/work-orders/{id}`     | `work-orders.delete` | Eliminar ordem                            |

#### Endpoints de Tarefas

| Method | Route                             | Permission           | Descrição        |
| ------ | --------------------------------- | -------------------- | ---------------- |
| POST   | `/work-order-tasks/{id}/assign`   | `work-orders.update` | Atribuir tarefa  |
| POST   | `/work-order-tasks/{id}/start`    | -                    | Iniciar tarefa   |
| POST   | `/work-order-tasks/{id}/complete` | -                    | Concluir tarefa  |
| POST   | `/work-orders/{id}/tasks`         | `work-orders.update` | Adicionar tarefa |

**Notas:**

-   `start` e `complete` não requerem permissões - utilizadores gerem as suas próprias tarefas
-   `myTasks` acessível a todos os utilizadores autenticados

#### Métodos do Controller

**index()**

```php
// Lista todas as ordens com filtros
Filtros: status, priority, search (título/nº encomenda)
Retorna: paginação 15 itens
Relationships: customerOrder.customer, creator, tasks
```

**myTasks()**

```php
// Dashboard pessoal do utilizador
Retorna tarefas:
  - Atribuídas diretamente ao utilizador (assigned_to)
  - Atribuídas a grupos do utilizador (assigned_group)
Ordenação: status (em_progresso → pendente → concluida), due_date
Relationships: workOrder.customerOrder.customer
```

**create()**

```php
// Formulário criação
Retorna:
  - customerOrders (sem work order associada)
  - users (para atribuições)
  - roles (para grupos)
  - taskTypes (10 tipos disponíveis)
```

**store(Request $request)**

```php
// Cria nova ordem + tarefas
Validação:
  - title: required, max:255
  - priority: required, in:baixa,normal,alta,urgente
  - tasks: required, array, min:1
  - tasks.*.title: required
Cria:
  - WorkOrder
  - WorkOrderTask[] com sequence_order sequencial
Activity Log: work_order criada
```

**show(WorkOrder $workOrder)**

```php
// Detalhes completos
Relationships:
  - tasks.assignedUser, tasks.dependsOn
  - customerOrder.customer
  - creator
Retorna também: users, roles, taskTypes
```

**update(Request $request, WorkOrder $workOrder)**

```php
// Atualiza detalhes da ordem
Campos atualizáveis: title, description, priority
Activity Log: work_order atualizada
```

**assignTask(Request $request, WorkOrderTask $task)**

```php
// Atribui tarefa a utilizador/grupo
Validação:
  - assigned_to: exists:users,id
  - assigned_group: string
Activity Log: tarefa atribuída
```

**startTask(WorkOrderTask $task)**

```php
// Inicia tarefa
Validação: pode iniciar (dependências completas)
Ações:
  - status = 'em_progresso'
  - assigned_to = utilizador atual (se null)
Activity Log: tarefa iniciada
```

**completeTask(Request $request, WorkOrderTask $task)**

```php
// Conclui tarefa
Ações:
  - status = 'concluida'
  - completed_at = agora
  - notes = observações
  - workOrder->updateStatus() (recalcula status ordem)
Activity Log: tarefa concluída
```

**addTask(Request $request, WorkOrder $workOrder)**

```php
// Adiciona nova tarefa a ordem existente
Validação:
  - task_type, title, description
  - assigned_to, assigned_group, due_date
  - sequence_order
Calcula: próximo sequence_order
Activity Log: tarefa adicionada
```

**destroy(WorkOrder $workOrder)**

```php
// Elimina ordem (soft delete)
Cascata: elimina todas as tarefas associadas
Activity Log: work_order eliminada
```

---

## 🔐 Permissões & Papéis

### Permissões Criadas

```php
'work-orders.create'
'work-orders.read'
'work-orders.update'
'work-orders.delete'
```

### Novo Papel: Gestor de Armazém

**Criado em:** `database/seeders/WorkOrderPermissionsSeeder.php`

**Permissões:**

```php
// Work Orders
'work-orders.read'
'work-orders.update'

// Articles (precisa ver stock)
'articles.read'
'articles.update'

// Supplier Orders (precisa criar encomendas)
'supplier-orders.read'
'supplier-orders.update'
```

**Justificação:**

-   Substitui papel "Editor" genérico
-   Focado em operações de armazém
-   Sem permissões financeiras ou de gestão de utilizadores

### Distribuição de Permissões

| Papel             | create | read | update | delete |
| ----------------- | ------ | ---- | ------ | ------ |
| Super Admin       | ✅     | ✅   | ✅     | ✅     |
| Administrador     | ✅     | ✅   | ✅     | ✅     |
| Gestor Comercial  | ✅     | ✅   | ✅     | ❌     |
| Gestor Financeiro | ❌     | ✅   | ✅     | ❌     |
| Gestor de Armazém | ❌     | ✅   | ✅     | ❌     |

---

## 🎨 Interface Vue

### Componentes Criados

#### 1. MyTasks.vue

**Localização:** `resources/js/Pages/WorkOrders/MyTasks.vue`  
**Route:** `work-orders.my-tasks`  
**Permissão:** Nenhuma (todos os utilizadores autenticados)

**Features:**

-   Lista tarefas atribuídas ao utilizador ou aos seus grupos
-   Status badges (pendente/em progresso/concluída)
-   Indicadores de atraso (overdue)
-   Bloqueio visual para tarefas com dependências
-   Botões ação: "Iniciar" (pendente), "Concluir" (em progresso)
-   Link para ver ordem completa
-   Paginação

**Props:**

```javascript
tasks: Object(paginated);
filters: Object;
taskTypes: Object;
```

#### 2. Index.vue

**Localização:** `resources/js/Pages/WorkOrders/Index.vue`  
**Route:** `work-orders.index`  
**Permissão:** `work-orders.read`

**Features:**

-   Lista todas as ordens de trabalho
-   Filtros: status, prioridade, pesquisa (título/nº encomenda)
-   Badges de status e prioridade
-   Progresso percentual
-   Botão "Nova Ordem" (se permission create)
-   Botão "Eliminar" (se permission delete)
-   Link para detalhes
-   Paginação

**Props:**

```javascript
workOrders: Object (paginated)
filters: Object
can: Object (create, delete)
```

#### 3. Show.vue

**Localização:** `resources/js/Pages/WorkOrders/Show.vue`  
**Route:** `work-orders.show`  
**Permissão:** `work-orders.read`

**Features:**

-   Timeline visual de tarefas
-   Status icons (lock/unlock/play/check)
-   Indicadores de dependências
-   Informações da ordem e encomenda associada
-   Botões por tarefa: "Atribuir", "Iniciar", "Concluir"
-   Modal de atribuição (utilizador/grupo)
-   Progresso percentual geral
-   Link para encomenda cliente

**Props:**

```javascript
workOrder: Object (full relationships)
can: Object (update)
users: Array
roles: Array
taskTypes: Object
```

#### 4. Create.vue

**Localização:** `resources/js/Pages/WorkOrders/Create.vue`  
**Route:** `work-orders.create`  
**Permissão:** `work-orders.create`

**Features:**

-   Formulário criação manual
-   Seleção de encomenda cliente (opcional)
-   Campos: título, descrição, prioridade
-   Construtor de tarefas dinâmico
-   Adicionar/remover tarefas
-   Por tarefa: tipo, título, descrição, atribuições, prazo
-   Validação: mínimo 1 tarefa
-   Botão submit

**Props:**

```javascript
customerOrders: Array (sem work order)
users: Array
roles: Array
taskTypes: Object
```

### Menu de Navegação

**AuthenticatedLayout.vue:**

```javascript
{
    name: "Ordens de Trabalho",
    href: "work-orders",
    icon: Briefcase,
    permission: "work-orders",
    children: [
        {
            name: "Minhas Tarefas",
            href: "work-orders.my-tasks",
            icon: CheckSquare,
        },
        {
            name: "Todas as Ordens",
            href: "work-orders.index",
            icon: List,
        },
    ],
}
```

**Icons Usados:**

-   `Briefcase` - Menu principal
-   `CheckSquare` - Minhas Tarefas
-   `List` - Todas as Ordens
-   `Play` - Iniciar tarefa
-   `CheckCircle2` - Concluir tarefa
-   `Clock` - Prazo
-   `User` - Atribuído a utilizador
-   `Users` - Atribuído a grupo
-   `AlertCircle` - Dependências
-   `Lock/Unlock` - Estado bloqueio
-   `Plus` - Adicionar
-   `Trash2` - Remover
-   `ArrowLeft` - Voltar

---

## 🤖 Sistema Automático

### Observer: CustomerOrderObserver

**Localização:** `app/Observers/CustomerOrderObserver.php`  
**Registado em:** `app/Providers/AppServiceProvider.php`

**Evento:** `created` (quando CustomerOrder é criada)

**Fluxo:**

```php
1. Criar WorkOrder
   - title: "Processar Encomenda {order_number}"
   - customer_order_id: {id}
   - priority: 'normal'
   - status: 'pendente'
   - created_by: utilizador autenticado

2. Determinar tipo de workflow
   if (delivery_method == 'pickup')
       → Workflow Pickup (7 tarefas)
   else
       → Workflow Shipping (9 tarefas)

3. Gerar tarefas sequenciais
   foreach task in workflow:
       - sequence_order: incremental (1, 2, 3...)
       - depends_on_task_id: tarefa anterior (null para primeira)
       - assigned_group: baseado no tipo de tarefa
       - due_date: +1 dia por tarefa
       - status: 'pendente'
```

**Exemplo Prático:**

**Input:** Encomenda #ORD-123 criada com `delivery_method = 'shipping'`

**Output:**

```
WorkOrder criada:
  - title: "Processar Encomenda ORD-123"
  - customer_order_id: 123
  - priority: normal
  - status: pendente

Tasks criadas:
  1. Validar Stock          [Gestor Comercial]     Due: Hoje
  2. Criar Enc. Fornecedor  [Gestor Comercial]     Due: Amanhã      Depende: #1
  3. Receção Armazém        [Gestor de Armazém]    Due: +2 dias     Depende: #2
  4. Recolha                [Gestor de Armazém]    Due: +3 dias     Depende: #3
  5. Embalamento            [Gestor de Armazém]    Due: +4 dias     Depende: #4
  6. Criar Guia             [Gestor Financeiro]    Due: +5 dias     Depende: #5
  7. Agendar Recolha        [Gestor de Armazém]    Due: +6 dias     Depende: #6
  8. Enviado                [Gestor de Armazém]    Due: +7 dias     Depende: #7
  9. Entregue               [Gestor Comercial]     Due: +8 dias     Depende: #8
```

### Atualização Automática de Status

**Lógica em WorkOrder::updateStatus():**

```php
$totalTasks = tasks()->count()
$completedTasks = tasks()->where('status', 'concluida')->count()
$inProgressTasks = tasks()->where('status', 'em_progresso')->count()

if ($completedTasks == $totalTasks)
    → status = 'concluida'
else if ($inProgressTasks > 0 || $completedTasks > 0)
    → status = 'em_progresso'
else
    → status = 'pendente'
```

**Trigger:**

-   Chamado automaticamente em `WorkOrderTask::complete()`
-   Pode ser chamado manualmente: `$workOrder->updateStatus()`

---

## 📊 Activity Logging

### Eventos Registados

**WorkOrder:**

-   `created` - Ordem criada
-   `updated` - Ordem atualizada
-   `deleted` - Ordem eliminada

**WorkOrderTask:**

-   `created` - Tarefa criada
-   `updated` - Tarefa atualizada (atribuição, início, conclusão)
-   `deleted` - Tarefa eliminada

**Campos Logged:**

```php
// WorkOrder
'title', 'description', 'priority', 'status'

// WorkOrderTask
'title', 'description', 'status', 'assigned_to',
'assigned_group', 'completed_at', 'notes'
```

**Acesso ao Log:**

```php
// Histórico de uma ordem
$workOrder->activities;

// Histórico de uma tarefa
$task->activities;

// Último registo
$workOrder->activities()->latest()->first();
```

---

## 🧪 Testing

### Cenários de Teste

#### 1. Criação Automática de Workflow

**Test:** Criar CustomerOrder e verificar WorkOrder gerada

```
1. Criar CustomerOrder com delivery_method = 'shipping'
2. Verificar WorkOrder criada automaticamente
3. Verificar 9 tarefas criadas
4. Verificar dependências sequenciais corretas
5. Verificar atribuições a grupos corretas
6. Verificar prazos incrementais
```

#### 2. Workflow de Dependências

**Test:** Tentar iniciar tarefa bloqueada

```
1. Criar WorkOrder com 3 tarefas dependentes
2. Tentar iniciar Tarefa #2 (depende de #1)
3. Verificar bloqueio (canStart() = false)
4. Concluir Tarefa #1
5. Verificar desbloqueio (canStart() = true)
6. Iniciar Tarefa #2 com sucesso
```

#### 3. Atualização Automática de Status

**Test:** Status da ordem muda com conclusão de tarefas

```
1. Criar WorkOrder com 3 tarefas
2. Verificar status = 'pendente'
3. Iniciar Tarefa #1
4. Verificar WorkOrder.status = 'em_progresso'
5. Concluir todas as tarefas
6. Verificar WorkOrder.status = 'concluida'
```

#### 4. Dashboard Pessoal (myTasks)

**Test:** Utilizador vê apenas suas tarefas + grupo

```
1. Criar 3 utilizadores (A, B, C)
2. Utilizador A tem papel "Gestor Comercial"
3. Criar tarefas:
   - Task 1: assigned_to = A
   - Task 2: assigned_group = "Gestor Comercial"
   - Task 3: assigned_to = B
4. Login como A
5. Aceder myTasks
6. Verificar vê Tasks 1 e 2 (não vê Task 3)
```

#### 5. Criação Manual

**Test:** Criar ordem manualmente via formulário

```
1. Aceder /work-orders/create
2. Preencher: título, prioridade
3. Adicionar 2 tarefas
4. Atribuir tarefas a utilizador específico
5. Submit
6. Verificar WorkOrder criada
7. Verificar 2 tarefas com atribuições corretas
```

### Comandos de Teste

```bash
# Testar criação automática
php artisan tinker
>>> $order = CustomerOrder::create([...])
>>> $order->workOrder  # deve existir
>>> $order->workOrder->tasks->count()  # 9 ou 7

# Testar dependências
>>> $task = WorkOrderTask::find(2)
>>> $task->canStart()  # false se #1 não concluída
>>> $task->dependsOn->complete('Done')
>>> $task->fresh()->canStart()  # true

# Testar activity log
>>> WorkOrder::first()->activities
```

---

## 🚀 Deployment

### Checklist de Deployment

1. **Migrations**

```bash
php artisan migrate
```

2. **Seeders**

```bash
php artisan db:seed --class=WorkOrderPermissionsSeeder
```

3. **Observer Registration**

```php
// app/Providers/AppServiceProvider.php
CustomerOrder::observe(CustomerOrderObserver::class);
```

4. **Frontend Build**

```bash
npm run build
```

5. **Cache Clear**

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

6. **Permissions Check**

```bash
# Verificar se papel existe
php artisan tinker
>>> Role::where('name', 'Gestor de Armazém')->exists()
>>> Permission::where('name', 'LIKE', 'work-orders.%')->get()
```

### Rollback (se necessário)

```bash
# Reverter migrations
php artisan migrate:rollback --step=2

# Remover permissões
php artisan tinker
>>> Role::where('name', 'Gestor de Armazém')->first()->delete();
>>> Permission::where('name', 'LIKE', 'work-orders.%')->delete();
```

---

## 📈 Métricas & KPIs

### Dados Disponíveis

**Por Ordem:**

-   Progresso percentual (`progress_percentage` attribute)
-   Tempo de processamento (created_at → updated_at quando concluída)
-   Tarefas atrasadas (tasks overdue)

**Por Utilizador:**

-   Tarefas pendentes (via myTasks)
-   Tarefas em progresso
-   Tarefas concluídas (completed_at)
-   Média de tempo por tarefa

**Global:**

-   Ordens pendentes/em progresso/concluídas
-   Taxa de conclusão por tipo de tarefa
-   Gargalos (tarefas que demoram mais)
-   Distribuição por prioridade

### Queries Úteis

```php
// Ordens atrasadas
WorkOrder::whereHas('tasks', function($q) {
    $q->where('status', '!=', 'concluida')
      ->where('due_date', '<', now());
})->get();

// Tarefas mais demoradas
WorkOrderTask::selectRaw('task_type, AVG(TIMESTAMPDIFF(HOUR, created_at, completed_at)) as avg_hours')
    ->whereNotNull('completed_at')
    ->groupBy('task_type')
    ->orderByDesc('avg_hours')
    ->get();

// Performance por utilizador
User::withCount([
    'assignedTasks as completed_count' => fn($q) => $q->where('status', 'concluida'),
    'assignedTasks as pending_count' => fn($q) => $q->where('status', 'pendente')
])->get();
```

---

## 🔧 Manutenção & Troubleshooting

### Problemas Comuns

**1. Workflow não é criado automaticamente**

**Sintomas:** CustomerOrder criada mas sem WorkOrder associada

**Verificar:**

```bash
# Observer está registado?
grep -r "CustomerOrderObserver" app/Providers/

# Testar manualmente
php artisan tinker
>>> $order = CustomerOrder::find(X)
>>> event(new \Illuminate\Database\Events\ModelCreated($order))
```

**Solução:**

-   Verificar `AppServiceProvider::boot()` tem `CustomerOrder::observe(...)`
-   Limpar cache: `php artisan config:clear`

**2. Tarefas não desbloqueiam após conclusão**

**Sintomas:** `canStart()` retorna false mesmo com dependência concluída

**Verificar:**

```php
$task = WorkOrderTask::find(X);
dd($task->dependsOn->status);  // deve ser 'concluida'
dd($task->canStart());
```

**Solução:**

-   Verificar `depends_on_task_id` está correto
-   Recarregar relationships: `$task->load('dependsOn')`

**3. myTasks não mostra tarefas do grupo**

**Sintomas:** Utilizador não vê tarefas atribuídas ao seu papel

**Verificar:**

```php
$user = auth()->user();
dd($user->getRoleNames());  // deve incluir papel esperado

// Testar query
WorkOrderTask::where('assigned_group', 'Gestor Comercial')->get();
```

**Solução:**

-   Verificar utilizador tem papel correto
-   Verificar `assigned_group` na tarefa corresponde ao nome do papel

### Logs & Debug

**Activity Log:**

```php
// Ver ações recentes
Activity::where('subject_type', 'App\Models\WorkOrder')
    ->latest()
    ->take(20)
    ->get();
```

**Query Log:**

```php
// Enable query logging
DB::enableQueryLog();

// ... execute queries ...

dd(DB::getQueryLog());
```

---

## 📝 Notas de Desenvolvimento

### Decisões Técnicas

**1. Observer vs Event Listeners**

-   **Escolha:** Observer
-   **Razão:** Simplicidade, acoplamento direto ao modelo
-   **Alternativa considerada:** Event/Listener (mais desacoplado mas overhead desnecessário)

**2. Soft Deletes**

-   **Escolha:** Implementado em ambas as tabelas
-   **Razão:** Recuperação de dados, auditoria
-   **Trade-off:** Queries precisam `withTrashed()` quando necessário

**3. Status Enum vs Tabela**

-   **Escolha:** Enum no migration
-   **Razão:** Estados fixos, validação DB-level
-   **Alternativa considerada:** Tabela `statuses` (overhead desnecessário)

**4. Activity Log (Spatie)**

-   **Escolha:** LogsActivity trait
-   **Razão:** Package maduro, flexível, bem mantido
-   **Trade-off:** Tabela `activity_log` pode crescer (considerar cleanup periódico)

### Melhorias Futuras

**v0.20.0 Possível:**

-   [ ] Notificações push quando tarefa atribuída
-   [ ] Email automático ao completar ordem
-   [ ] SLA tracking (alertas se tarefa atrasada)
-   [ ] Dashboard analytics (gráficos de performance)
-   [ ] Export relatórios (PDF/Excel)
-   [ ] Comentários por tarefa (thread de discussão)
-   [ ] Anexos em tarefas (upload files)
-   [ ] Templates de workflow (reutilizar padrões)

**Performance:**

-   [ ] Cache de queries frequentes (count de tarefas)
-   [ ] Eager loading otimizado (N+1 queries)
-   [ ] Index adicional: `(status, due_date)` composto

**UX:**

-   [ ] Drag & drop para reordenar tarefas
-   [ ] Kanban board view (alternativa a lista)
-   [ ] Filtros avançados (múltiplos status, ranges de data)
-   [ ] Bulk actions (atribuir múltiplas tarefas)

---

## 📚 Referências

### Código-Fonte

-   Models: `app/Models/WorkOrder.php`, `WorkOrderTask.php`
-   Controller: `app/Http/Controllers/WorkOrderController.php`
-   Observer: `app/Observers/CustomerOrderObserver.php`
-   Seeder: `database/seeders/WorkOrderPermissionsSeeder.php`
-   Migrations: `database/migrations/2025_11_16_234026_create_work_orders_table.php`
-   Vue Components: `resources/js/Pages/WorkOrders/`
-   Routes: `routes/web.php` (linhas Work Orders)

### Documentação Relacionada

-   `docs/changelog.md` - Histórico de versões
-   `docs/modular-architecture.md` - Arquitetura geral do sistema
-   `docs/customer-orders-module.md` - Módulo de encomendas (integração)
-   `docs/access-management.md` - Permissões e papéis

### Packages Utilizados

-   **Spatie Laravel Permission** - Gestão de permissões
-   **Spatie Laravel Activitylog** - Histórico de atividades
-   **Inertia.js** - Stack frontend
-   **Vue 3** - Framework UI
-   **Tailwind CSS** - Styling
-   **Lucide Vue** - Icons

---

## ✅ Checklist de Funcionalidades

### Backend ✅ 100%

-   [x] Migrations criadas e executadas
-   [x] Models com relationships completos
-   [x] Scopes e helpers implementados
-   [x] Controller com 11 métodos
-   [x] Observer registado e funcional
-   [x] Permissions seeder executado
-   [x] Routes configuradas (11 rotas)
-   [x] Activity logging ativo
-   [x] Soft deletes implementados

### Frontend ✅ 100%

-   [x] MyTasks.vue - Dashboard pessoal
-   [x] Index.vue - Lista de ordens
-   [x] Show.vue - Timeline de tarefas
-   [x] Create.vue - Formulário criação
-   [x] Menu atualizado com submenu
-   [x] Icons importados
-   [x] Build compilado sem erros

### Testing ⏳ Pendente

-   [ ] Criar CustomerOrder e verificar WorkOrder automática
-   [ ] Testar workflow de envio (9 tarefas)
-   [ ] Testar workflow de levantamento (7 tarefas)
-   [ ] Testar dependências sequenciais
-   [ ] Testar myTasks com diferentes utilizadores
-   [ ] Testar atribuições (utilizador + grupo)
-   [ ] Testar atualização de status
-   [ ] Testar criação manual

### Documentação ✅ 100%

-   [x] changelog.md atualizado
-   [x] work-orders-module.md criado (este ficheiro)
-   [x] Comentários inline no código
-   [x] Guia de deployment

---

**Autor:** Tiago (Estagiário)  
**Supervisor:** Coordenador Gest-App  
**Empresa:** INOVCORP  
**Data Conclusão:** 16 Novembro 2025

---

_Módulo 20/20 - Sistema Completo! 🎉_
