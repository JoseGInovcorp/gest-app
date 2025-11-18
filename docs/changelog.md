# Histórico de Desenvolvimento — Gest-App

Registo das principais mudanças e desenvolvimentos realizados durante o estágio.

---

## v0.24.0 — 18 Nov 2025

**Integração Financeira Completa - Sistema Automático de Movimentos Bancários e Conta Corrente**

### O que foi feito

**Backend - Sistema de Integração Financeira**

-   ✅ **CustomerOrderObserver - Criação automática de movimentos**

    -   Quando encomenda cliente muda para status "closed"
    -   Cria movimento **DÉBITO** na Conta Corrente Cliente (cliente pagou)
    -   Cria movimento **CRÉDITO** na Conta Bancária (entrada de dinheiro)
    -   Relaciona ambos com referência da encomenda
    -   Observações incluem nome do cliente

-   ✅ **BankTransaction Observer - Cálculo automático de saldos**

    -   Auto-cálculo de `saldo_apos` em cada transação
    -   Triggers automáticos em create/update/delete
    -   Recalcula saldo da conta bancária (`saldo_atual`)
    -   Ordenação cronológica correta dos movimentos

-   ✅ **ClientAccount Observer - Gestão de saldos de clientes**
    -   Cálculo automático de `saldo_apos` por cliente
    -   Atualização em cascata de movimentos subsequentes
    -   Suporte a débitos e créditos

**Backend - Correções e Ajustes**

-   ✅ **Correção de campo entidade**: `nome` → `name`

    -   CustomerOrderObserver corrigido
    -   SupplierOrderObserver corrigido
    -   Scripts de migração atualizados
    -   Transações existentes corrigidas

-   ✅ **Scripts de manutenção criados**
    -   `update_transaction_customer_names.php` - Atualiza nomes de clientes nas transações
    -   `fix_client_account_payments.php` - Corrige tipo de movimento (crédito→débito)
    -   `process_existing_closed_orders.php` - Processa encomendas fechadas retroativamente

**Frontend - BankAccounts/Show.vue**

-   ✅ **Header padronizado** (match com Entities/Show.vue)

    -   Ícone CreditCard em container azul arredondado
    -   Layout: Ícone + Título | Botões de ação
    -   Border-bottom separador
    -   Botão "Voltar" com variant outline
    -   Botão "Exportar PDF" verde
    -   Botão "Editar" azul com ícone Pencil
    -   Dark mode completo

-   ✅ **Layout consistente**

    -   Container principal com `space-y-6`
    -   Largura ajustada (removido max-w-7xl extra)
    -   Indentação correta
    -   Espaçamento entre seções

-   ✅ **Extrato de movimentos melhorado**
    -   Exibe observações com nome do cliente
    -   Badges coloridos por categoria
    -   Valores com cores (verde/vermelho)
    -   Saldo após cada movimento

**Frontend - ClientAccounts/Show.vue**

-   ✅ **Header padronizado**

    -   Mesmo padrão do BankAccounts/Show.vue
    -   Ícone DollarSign
    -   Título: Nome do cliente
    -   Subtítulo: Descrição do movimento
    -   Botões: Voltar (outline), Editar (primary), Eliminar (destructive)

-   ✅ **Estrutura simplificada**
    -   Removido card de ações (ações no header)
    -   Layout limpo com space-y-6
    -   Border-bottom no header
    -   Componente Button reutilizável

**PDF Export**

-   ✅ **Template profissional para extrato bancário**
    -   Header com detalhes da conta (nome, banco, IBAN)
    -   4 cards de resumo (Saldo Inicial, Créditos, Débitos, Saldo Atual)
    -   Tabela completa de transações
    -   Badges coloridos por categoria
    -   Saldo após cada movimento
    -   Footer com timestamp de geração
    -   Estilo consistente com faturas

**Lógica Financeira**

-   ✅ **Encomenda Cliente Fechada**:

    -   Conta Corrente Cliente: DÉBITO (cliente pagou, reduz dívida)
    -   Conta Bancária: CRÉDITO (entrada de dinheiro)

-   ✅ **Encomenda Fornecedor Fechada** (preparado):
    -   Conta Bancária: DÉBITO (saída de dinheiro)

### Testes Realizados

-   ✅ Encomenda EC-2025-0001 (627 EUR)
    -   Movimento bancário criado: +627 EUR (25.000 → 25.627 EUR)
    -   Movimento conta cliente criado: -627 EUR (débito)
    -   Nome cliente exibido corretamente nas observações

### Scripts Executados

```bash
php update_transaction_customer_names.php  # Corrigiu nome do cliente
php fix_client_account_payments.php        # Corrigiu tipo crédito→débito
php process_existing_closed_orders.php     # Processou EC-2025-0001
```

### Impacto

-   🎯 **Automação completa** do ciclo financeiro
-   📊 **Rastreabilidade total** de movimentos
-   💰 **Saldos sempre corretos** e atualizados
-   🔗 **Integração perfeita** entre módulos
-   🎨 **UI consistente** em todas as páginas Show

---

## v0.23.0 — 17 Nov 2025

**Gestão Automática de Stock nas Encomendas de Cliente**

### O que foi feito

**Backend - Article Model**

-   ✅ **Método `hasStock(float $quantity): bool`**
    -   Verifica disponibilidade de stock para quantidade solicitada
    -   Serviços (`tipo = 'servico'`) sempre retornam `true`
    -   Produtos verificam: `stock_quantidade >= $quantity`
-   ✅ **Método `decreaseStock(float $quantity): void`**
    -   Decrementa stock automaticamente ao fechar encomenda
    -   Usa `max(0, stock - quantity)` para evitar valores negativos
    -   Apenas para produtos
-   ✅ **Método `increaseStock(float $quantity): void`**
    -   Repõe stock ao cancelar/reabrir encomenda
    -   Usado quando encomenda volta de "closed" para "draft"
    -   Apenas para produtos

**Backend - CustomerOrderController**

-   ✅ **Validação de stock ao criar encomenda**
    -   Itera sobre todos os itens verificando `hasStock()`
    -   Coleta warnings de stock insuficiente
    -   Array `$stockWarnings` com artigo, quantidade solicitada e disponível
-   ✅ **Validação de stock ao editar encomenda**
    -   Mesma lógica de validação do create
    -   Warnings exibidos antes de salvar
-   ✅ **Atualização automática de stock**
    -   Detecta mudança de status: `draft → closed` ou `closed → draft`
    -   **draft → closed**: Decrementa stock de todos os itens
    -   **closed → draft**: Repõe stock dos itens antigos
    -   Log de atividade registra mudança de stock
-   ✅ **Mensagens contextuais**
    -   Sucesso padrão: "Encomenda criada com sucesso!"
    -   Com warnings: "+ ATENÇÃO: Alguns artigos têm stock insuficiente. Considere criar encomendas ao fornecedor."
    -   Atualização com stock: "+ Stock atualizado."

**Frontend - Create.vue & Edit.vue**

-   ✅ **Indicador visual de stock em tempo real**
    -   Exibido abaixo do seletor de artigo
    -   Verde: Stock suficiente (`stock >= quantidade`)
    -   Laranja/Vermelho: Stock insuficiente (`stock < quantidade`)
    -   Não exibido para serviços
-   ✅ **Alertas inline**
    -   "⚠️ Stock insuficiente! Considere adicionar fornecedor."
    -   Aparece quando quantidade solicitada > stock disponível
    -   Usuário pode prosseguir mesmo assim
-   ✅ **Função helper `getArticleStock()`**
    -   Busca stock do artigo selecionado
    -   Retorna `null` para serviços
    -   Retorna quantidade para produtos

**Dados Adicionados ao Controller**

-   ✅ Articles agora incluem:
    -   `stock_quantidade` - Quantidade disponível
    -   `tipo` - Produto ou Serviço
    -   Campos adicionados em `create()` e `edit()`

### Fluxo de Trabalho

**Cenário 1: Stock Suficiente**

1. Criar encomenda → Selecionar artigo
2. ✅ Indicador verde: "Stock disponível: 10"
3. Submeter com status "draft"
4. Mudar para "closed" → Stock decrementado (10 → 7)

**Cenário 2: Stock Insuficiente**

1. Criar encomenda → Selecionar artigo
2. ⚠️ Indicador vermelho: "Stock disponível: 2"
3. ⚠️ Alerta: "Stock insuficiente! Considere adicionar fornecedor."
4. Usuário pode continuar e criar encomenda
5. Mensagem: "Encomenda criada! ATENÇÃO: Stock insuficiente..."
6. Adicionar fornecedor ao item
7. Converter para encomenda fornecedor

**Cenário 3: Cancelamento**

1. Encomenda fechada (stock já decrementado)
2. Reabrir (draft) → Stock reposto automaticamente
3. Permite edição/correção

**Cenário 4: Serviços**

1. Selecionar serviço (ex: "Consultoria IT")
2. Sem indicador de stock (não aplicável)
3. Métodos `decreaseStock/increaseStock` não fazem nada

### Activity Logs

Propriedades registradas:

-   `status_change`: "draft -> closed" ou "closed -> draft"
-   `stock_updated`: true/false
-   `stock_warnings`: Array com artigos em falta

### Resultado Final

Sistema completo de gestão de stock:

-   ✅ Validação em tempo real no frontend
-   ✅ Alertas visuais (verde/laranja/vermelho)
-   ✅ Encomenda pode prosseguir sem stock
-   ✅ Sugestão de criar encomenda fornecedor
-   ✅ Atualização automática ao fechar encomenda
-   ✅ Reposição automática ao reabrir
-   ✅ Serviços não afetam stock
-   ✅ Stock nunca fica negativo

---

## v0.22.0 — 17 Nov 2025

**Dashboard Adaptativo com Métricas Baseadas em Permissões**

### O que foi feito

**Dashboard Controller**

-   ✅ **DashboardController com lógica adaptativa**
    -   7 métricas condicionais baseadas em permissões do utilizador
    -   Propostas Pendentes (`proposals.read`)
    -   Encomendas Cliente Ativas (`customer-orders.read`)
    -   Faturas Fornecedor Pendentes (`supplier-invoices.read`)
    -   Ordens de Trabalho Em Progresso (`work-orders.read`)
    -   Encomendas Fornecedor Pendentes (`supplier-orders.read`)
    -   Saldo Total Contas Bancárias (`bank-accounts.read`)
    -   Clientes com Saldo Devedor (`client-accounts.read`)
    -   Minhas Tarefas Pendentes (sempre visível)
-   ✅ **Widget de Tarefas Urgentes**
    -   Top 5 tarefas por prazo (due_date)
    -   Indicador de tarefas atrasadas (is_overdue)
    -   Relações eager loading (workOrder, customerOrder, customer)
    -   Informação completa: título, cliente, prazo, status, atribuído
-   ✅ **Atividade Recente (Super Admin apenas)**
    -   Últimos 10 logs de atividade
    -   Restrição: apenas Super Admin visualiza
    -   Informação: ação, módulo, utilizador, timestamp

**Dashboard Frontend**

-   ✅ **Componente Vue 3 com renderização condicional**
    -   Props: stats, urgentTasks, recentActivity, permissions
    -   Grid responsivo: 1-4 colunas (mobile → desktop)
    -   Cards condicionais com `v-if="permissions.canViewX"`
    -   8 tipos de cards com ícones e cores específicas:
        -   Propostas: FileText (azul)
        -   Encomendas: ShoppingCart (verde)
        -   Faturas: CreditCard (vermelho)
        -   Ordens: Briefcase (roxo)
        -   Saldo: Wallet (esmeralda)
        -   Devedores: AlertCircle (laranja)
        -   Tarefas: CheckSquare (amarelo)
-   ✅ **Layout Centralizado**
    -   Container `max-w-7xl mx-auto` para centralização
    -   Margens automáticas em ecrãs grandes
    -   Melhor aproveitamento do espaço visual
-   ✅ **Dark Mode Completo**
    -   Todos os cards adaptados
    -   Cores ajustadas para tema escuro
    -   Bordas e fundos consistentes

**Correção de Permissões**

-   ✅ **FixRolePermissionsSeeder criado**
    -   Gestor Financeiro: Removidas permissões comerciais indevidas
    -   Visualizador: Removidas permissões de escrita (create/update/delete)
    -   Gestor de Armazém: Removidas permissões desnecessárias
    -   Cache de permissões limpo com `php artisan permission:cache-reset`

**Bugs Corrigidos**

-   ✅ BankAccount: Coluna `state` → `estado`
-   ✅ ClientAccount: Query corrigida para usar `saldo_apos`
-   ✅ WorkOrderTask: Removido relationship `assignedGroup()` inexistente
-   ✅ Utilizadores com permissões incorretas identificados e corrigidos

### Resultado Final

Dashboard 100% funcional e adaptativo:

-   **Super Admin**: Vê todas as métricas + atividade recente
-   **Gestor Comercial**: Propostas, encomendas, ordens de trabalho
-   **Gestor Financeiro**: Contas bancárias, clientes devedores, faturas
-   **Gestor de Armazém**: Encomendas fornecedor, ordens de trabalho
-   **Visualizador**: Todos os cards (só leitura)
-   **Todos**: Minhas tarefas pendentes sempre visível

---

## v0.21.0 — 17 Nov 2025

**UI/UX Standardization — Complete Interface Consistency**

### O que foi feito

**Componentes de Diálogo Uniformizados**

-   ✅ **ConfirmDialog Component**
    -   Componente reutilizável com 4 variantes: warning, danger, info, success
    -   Props: show, title, message, type, confirmText, cancelText, isProcessing
    -   Ícones dinâmicos: AlertTriangle, Trash2, Info, CheckCircle
    -   Suporte para slot customizado de conteúdo
    -   Dark mode completo
-   ✅ **Substituição Global de Popups**
    -   27+ páginas atualizadas (todos os Index.vue)
    -   Removidos todos `confirm()`, `alert()`, `prompt()` nativos
    -   MyTasks.vue: Custom dialog com Textarea para notas de conclusão
    -   UI consistente em toda a aplicação

**Padronização de Botões de Ação**

-   ✅ **Esquema de Cores Consistente**
    -   Ver (Eye): Cinza (`text-gray-600 hover:text-gray-800 hover:bg-gray-100`)
    -   Editar (Pencil): Azul (`text-blue-600 hover:text-blue-700 hover:bg-blue-50`)
    -   Eliminar (Trash2): Vermelho (`text-red-600 hover:text-red-700 hover:bg-red-50`)
    -   Dark mode suportado em todos
-   ✅ **Componentes Atualizados**
    -   EntitiesDataTable.vue (usado por Clients e Suppliers)
    -   ContactsDataTable.vue (usado por Contacts)
    -   27+ páginas Index.vue com botões inline
    -   Formatação: `<div class="flex items-center justify-end space-x-2">`

**Headers de Página Uniformizados**

-   ✅ **Estrutura Padrão Implementada**
    -   Ícone colorido com fundo (`p-2 bg-[color]-100 dark:bg-[color]-900/20 rounded-lg`)
    -   Título: `text-2xl font-bold text-gray-900 dark:text-white`
    -   Descrição: `text-gray-500 dark:text-gray-400`
    -   Breadcrumbs adicionados a todas as páginas
    -   Main Card estruturado: Toolbar → Filtros → Conteúdo
-   ✅ **Páginas Corrigidas**
    -   WorkOrders/Index.vue (era text-3xl, agora text-2xl)
    -   TaskTemplates/Index.vue (era text-3xl, agora text-2xl)
    -   WorkOrders/MyTasks.vue (era text-3xl, agora text-2xl)
    -   SupplierInvoices/Index.vue (formatação de botões corrigida)

**Sidebar Uniformizada**

-   ✅ **Dropdowns Consistentes**
    -   Financeiro, Gestão de Acessos, Configurações
    -   Auto-expansão quando rota ativa
    -   Ícone + Título + ChevronDown
    -   Animação de rotação em ChevronDown
-   ✅ **Ícones sem Emojis**
    -   Apenas ícones Lucide mantidos
    -   Layout profissional e limpo

### Bug Fixes

-   🐛 **SupplierInvoices Action Buttons**
    -   Problema: Botões com `space-x-2` inline (alinhamento incorreto)
    -   Solução: Container `<div class="flex items-center justify-end space-x-2">`
-   🐛 **Headers Inconsistentes**
    -   Problema: WorkOrders, TaskTemplates, MyTasks com `text-3xl` e `dark:text-gray-100`
    -   Solução: Padronizado para `text-2xl` e `dark:text-white` com ícone colorido
-   🐛 **Card Structure**
    -   Problema: Filtros e conteúdo fora do Main Card em algumas páginas
    -   Solução: Estrutura consistente Main Card → Toolbar → Filtros → Conteúdo

### Ficheiros Modificados

**Dialog Component:**

-   `resources/js/Components/ConfirmDialog.vue` - NEW

**Data Tables:**

-   `resources/js/Components/ui/EntitiesDataTable.vue` - Button colors
-   `resources/js/Components/ui/ContactsDataTable.vue` - Button colors

**Pages Updated (27+ files):**

-   `resources/js/Pages/SupplierInvoices/Index.vue`
-   `resources/js/Pages/SupplierOrders/Index.vue`
-   `resources/js/Pages/CustomerOrders/Index.vue`
-   `resources/js/Pages/Proposals/Index.vue`
-   `resources/js/Pages/WorkOrders/Index.vue` - Header + structure
-   `resources/js/Pages/WorkOrders/MyTasks.vue` - Header + structure
-   `resources/js/Pages/TaskTemplates/Index.vue` - Header + structure
-   `resources/js/Pages/ClientAccounts/Index.vue`
-   `resources/js/Pages/VatRates/Index.vue`
-   `resources/js/Pages/Users/Index.vue`
-   `resources/js/Pages/Roles/Index.vue`
-   `resources/js/Pages/Countries/Index.vue`
-   `resources/js/Pages/ContactFunctions/Index.vue`
-   `resources/js/Pages/CalendarEventTypes/Index.vue`
-   `resources/js/Pages/CalendarEventActions/Index.vue`
-   E mais 12 páginas...

**Layout:**

-   `resources/js/Layouts/AuthenticatedLayout.vue` - Sidebar dropdowns

### Impact

-   **UX Excellence** - Interface totalmente consistente em 30+ páginas
-   **Professional Look** - Diálogos modernos, cores padronizadas, layout limpo
-   **Maintainability** - Componente ConfirmDialog reutilizável reduz código duplicado
-   **Accessibility** - Dark mode completo, cores com contraste adequado
-   **Developer Experience** - Padrões claros facilitam desenvolvimento futuro

---

## v0.20.0 — 17 Nov 2025

**Task Templates Management + Form Validations & Business Rules**

### O que foi feito

**Sistema de Gestão de Templates de Tarefas**

-   ✅ **Database & Model**
    -   Tabela `task_templates`: code, label, description, assigned_group, default_sequence, is_active
    -   Model com scopes: active(), orderedBySequence()
    -   Soft deletes implementado
-   ✅ **CRUD Completo em Configurações**
    -   TaskTemplateController com 6 rotas + permissions middleware
    -   Index.vue: Lista de templates com ordenação por sequência
    -   Create.vue/Edit.vue: Formulários com componentes shadcn-vue
    -   Integração no menu Configurações (Gestão de Tarefas)
-   ✅ **Permissions Integration**
    -   4 permissões: task-templates.create/read/update/delete
    -   TaskTemplatePermissionsSeeder: atribuições para 5 roles
    -   UI de Gestão de Permissões atualizada (RoleController)
-   ✅ **Workflow Dinâmico**
    -   CustomerOrderObserver refatorado para usar templates da DB
    -   12 templates pré-carregados (TaskTemplateSeeder)
    -   Workflow agora 100% configurável sem alterar código
-   ✅ **Templates Criados**
    1. VALIDATE_STOCK - Validar Stock (Gestor Comercial)
    2. ORDER_SUPPLIER - Encomendar a Fornecedor (Gestor Comercial)
    3. WAREHOUSE_COLLECT - Recolher no Armazém (Gestor de Armazém)
    4. RECEIVE_GOODS - Receção de Mercadoria (Gestor de Armazém)
    5. PACK_ORDER - Embalar Encomenda (Gestor de Armazém)
    6. TRANSPORT_GUIDE - Gerar Guia de Transporte (Gestor Comercial)
    7. SCHEDULE_TRANSPORT - Agendar Transporte (Gestor Comercial)
    8. SEND_ORDER - Enviar Encomenda (Gestor de Armazém)
    9. PICKUP_ORDER - Levantamento pelo Cliente (Gestor de Armazém)
    10. DELIVER_ORDER - Entregar ao Cliente (Gestor de Armazém)
    11. CONFIRM_ORDER - Confirmar Encomenda (Gestor Comercial)
    12. CREATE_CUSTOMER_INVOICE - Criar Fatura de Cliente (Gestor Financeiro)

**Validações de Formulários - Customer Orders & Proposals**

-   ✅ **Auto-fill de Preços com IVA**
    -   CustomerOrders: Ao selecionar artigo, preenche `preco_com_iva` como `unit_price`
    -   Proposals: Ao selecionar artigo, preenche `preco_com_iva` como `price`
    -   Watch pattern: Observa mudanças em `article_id`, atualiza preço automaticamente
    -   Eventos: @update:modelValue para compatibilidade Vue 3
    -   Comparação: == (string/number compatibility)
-   ✅ **Auto-cálculo de Validade (+30 dias)**
    -   CustomerOrders: Ao preencher `proposal_date`, calcula `validity_date` automaticamente
    -   Proposals: Ao alterar estado para "fechado" e ter `data_proposta`, calcula `validade`
    -   Frontend watchers em Create.vue e Edit.vue
    -   Backend calcula validade no store() e update() se necessário

**Business Rules - Proposals**

-   ✅ **Regras de Datas Condicionais**
    -   **Rascunho**: `data_proposta` e `validade` são opcionais
    -   **Fechado**: `data_proposta` obrigatória, `validade` auto-calculada (+30 dias)
    -   Migration: `data_proposta` e `validade` tornadas nullable
    -   Validação condicional: `$request->estado === 'fechado' ? 'required|date' : 'nullable|date'`
    -   Frontend: Asterisco (\*) aparece condicionalmente quando estado = 'fechado'
    -   Mensagem de ajuda dinâmica baseada no estado
-   ✅ **Estado Select Values**
    -   Corrigido: valores lowercase ('rascunho', 'fechado')
    -   Validação backend: `in:rascunho,fechado`
    -   Labels frontend mantém capitalização correta

**Melhorias - Work Orders (Minhas Tarefas)**

-   ✅ **Filtro por Cliente**
    -   Novo filtro: dropdown de clientes nas tarefas do utilizador
    -   Backend: Busca clientes únicos das work orders com tarefas atribuídas
    -   Query otimizada: JOIN customer_orders → work_orders → work_order_tasks
    -   Frontend: Select component com opção "Todos os clientes"
    -   Watch automático: Aplica filtro sem reload
-   ✅ **Interface de Filtros Completa**
    -   4 filtros: Cliente, Estado, Apenas atrasadas, Limpar Filtros
    -   Grid responsivo (md:grid-cols-4)
    -   Ícone Funnel para identificação visual
    -   Checkbox para tarefas atrasadas
    -   Botão "Limpar Filtros" reseta todos os filtros
    -   Preservação de estado nos filtros (query string)

### Bug Fixes

-   🐛 **MyTasks Customer Filter Query Error**
    -   Problema: Column 'customer_orders.entity_id' not found
    -   Causa: Nome incorreto da coluna (entity_id vs customer_id)
    -   Solução: Corrigido em 2 locais (filtro + query clientes)
        -   Filtro: `$q->where('customer_id', $request->customer_id)`
        -   Query: `$query->select('customer_orders.customer_id')`

### Ficheiros Modificados

**Task Templates System:**

-   `database/migrations/2025_11_16_create_task_templates_table.php` - NEW
-   `app/Models/TaskTemplate.php` - NEW
-   `app/Http/Controllers/TaskTemplateController.php` - NEW
-   `app/Observers/CustomerOrderObserver.php` - UPDATED (dynamic templates)
-   `database/seeders/TaskTemplateSeeder.php` - NEW
-   `database/seeders/TaskTemplatePermissionsSeeder.php` - NEW
-   `resources/js/Pages/TaskTemplates/Index.vue` - NEW
-   `resources/js/Pages/TaskTemplates/Create.vue` - NEW
-   `resources/js/Pages/TaskTemplates/Edit.vue` - NEW
-   `routes/web.php` - Task templates routes
-   `app/Http/Controllers/RoleController.php` - Permissions UI

**Proposals Business Logic:**

-   `app/Http/Controllers/ProposalController.php` - Conditional validation
-   `database/migrations/2025_11_17_014110_make_validade_nullable_in_proposals_table.php` - NEW
-   `resources/js/Pages/Proposals/Create.vue` - Conditional required, auto-calc
-   `resources/js/Pages/Proposals/Edit.vue` - Conditional required, auto-calc

**Customer Orders:**

-   `app/Http/Controllers/CustomerOrderController.php` - preco_com_iva
-   `resources/js/Pages/CustomerOrders/Create.vue` - Auto-fill price, validity

**Work Orders:**

-   `app/Http/Controllers/WorkOrderController.php` - Customer filter + query fix
-   `resources/js/Pages/WorkOrders/MyTasks.vue` - Filter UI

**Layout:**

-   `resources/js/Layouts/AuthenticatedLayout.vue` - Menu item "Gestão de Tarefas"

### Impact

-   **Workflow 100% Configurável** - Templates geridos via UI, sem código
-   **Business Logic Compliance** - Proposals seguem regras de negócio corretas
-   **UX Improvements** - Auto-fill elimina erros, filtros melhoram produtividade
-   **Data Integrity** - Validações condicionais garantem consistência

---

## v0.19.0 — 16 Nov 2025

**Work Orders Module - Task Management & Workflow Automation**

### O que foi feito

**Módulo de Ordens de Trabalho**

-   ✅ **Database & Models**
    -   Tabela `work_orders`: customer_order_id, title, description, priority (4 níveis), status (4 estados), created_by
    -   Tabela `work_order_tasks`: task_type, assigned_to/assigned_group, sequence_order, depends_on_task_id, due_date, notes
    -   Models com soft deletes, activity log, relationships completas
    -   10 tipos de tarefas: validação stock, encomenda fornecedor, recolha armazém, receção, embalamento, guia transporte, agendamento, envio, levantamento, entrega
-   ✅ **Workflow Automático**
    -   CustomerOrderObserver: cria WorkOrder automaticamente quando encomenda é criada
    -   Duas rotas de workflow: Envio (9 tarefas) vs Levantamento (7 tarefas)
    -   Dependências sequenciais: cada tarefa depende da conclusão da anterior
    -   Atribuições automáticas a grupos (Gestor Comercial, Gestor de Armazém, Gestor Financeiro)
    -   Prazos calculados automaticamente (1 dia por tarefa)
-   ✅ **Controller & Routes**
    -   11 endpoints: CRUD completo + gestão de tarefas
    -   myTasks(): dashboard pessoal (tarefas atribuídas ao utilizador + grupo)
    -   assignTask(), startTask(), completeTask(): gestão workflow
    -   addTask(): adicionar tarefas a ordens existentes (workflow flexível)
    -   Permissions: work-orders.create/read/update/delete
-   ✅ **Novo Papel - Gestor de Armazém**
    -   Role criado em WorkOrderPermissionsSeeder
    -   Permissões: work-orders.read/update, articles.read/update, supplier-orders.read/update
    -   Substituiu papel "Editor" para operações de armazém
-   ✅ **Interface Vue**
    -   MyTasks.vue: Dashboard pessoal com tarefas pendentes/em progresso
    -   Index.vue: Lista todas as ordens com filtros (status, prioridade, pesquisa)
    -   Show.vue: Timeline de tarefas com indicadores visuais de progresso
    -   Create.vue: Criação manual de ordens com construtor de tarefas
    -   Menu atualizado com submenu: "Minhas Tarefas" e "Todas as Ordens"
-   ✅ **Features Avançadas**
    -   Status automático: ordem atualiza baseado na conclusão de tarefas
    -   Progresso percentual: cálculo automático de completion
    -   Validação dependências: tarefas bloqueadas até dependências completas
    -   Indicadores overdue: alertas visuais para tarefas atrasadas
    -   Activity logging: histórico completo de todas as ações

**Impact**

-   **Módulo 20/20 Completo** - Última funcionalidade antes do delivery final
-   **Automação Total** - Zero intervenção manual para processar encomendas
-   **Rastreabilidade** - Histórico completo de todas as operações
-   **Flexibilidade** - Workflow adaptável a diferentes tipos de encomendas

---

## v0.18.0 — 16 Nov 2025

**Security Compliance + Data Protection**

### O que foi feito

**Segurança - 100% Compliance**

-   ✅ **Encriptação de Dados Sensíveis (AES-256)**
    -   Entity Model: tax_number, phone, mobile, email, iban
    -   Contact Model: phone, mobile, email
    -   BankAccount Model: iban, swift_bic
    -   Comando Artisan: `php artisan security:encrypt-data` (migração de dados existentes)
    -   Encryption transparente via Laravel encrypted casts
-   ✅ **Proteção de Documentos**
    -   Disco privado criado: `storage/app/private/` (fora da web root)
    -   DocumentController: Documentos em disco privado
    -   SupplierInvoiceController: Faturas e comprovativos em disco privado
    -   Download controlado com autenticação obrigatória
    -   Apenas imagens públicas (logos, fotos artigos) mantidas acessíveis
-   ✅ **HTTPS Obrigatório (Produção)**
    -   URL::forceScheme('https') em AppServiceProvider
    -   Middleware ForceHttps (redirect 301 HTTP → HTTPS)
    -   Ativo apenas em APP_ENV=production
-   ✅ **Proteção contra Ataques**
    -   CSRF: Laravel tokens nativos (já implementado)
    -   XSS: Vue 3 auto-escaping + Laravel validation
    -   SQL Injection: Eloquent ORM + prepared statements
-   ✅ **Documentação Completa**
    -   `docs/compliance-check.md` - Verificação 100% requisitos
    -   `docs/security-implementation.md` - Guia de deployment
    -   `docs/security-summary.md` - Resumo executivo
    -   Instruções detalhadas para produção

**Bug Fixes - Supplier Invoices**

-   🐛 **Document Upload Failed**
    -   Problema: Uploads falhavam com erro "failed to upload"
    -   Causa: Limite PHP `upload_max_filesize` = 2MB vs validação Laravel 5MB
    -   Solução: Aumentado `php.ini` limites para 10MB
        -   `upload_max_filesize = 10M`
        -   `post_max_size = 10M`
    -   Create.vue/Edit.vue: Adicionado `forceFormData: true` para file uploads
    -   SupplierInvoiceController: Logs detalhados para debug de uploads
-   🐛 **Payment Proof Email Attachment**
    -   PaymentProofMail: Mudado de `fromPath()` para `fromData()` com Storage facade
    -   Estado atualizado para "paga" ao enviar comprovativo
-   🐛 **Document Download 403 Forbidden**
    -   Rotas protegidas criadas: `download-document`, `download-proof`
    -   Index.vue/Edit.vue: Mudado de `/storage/` URLs para routes protegidas
    -   Download com autenticação e permissões verificadas

**Bug Fixes - Articles**

-   🐛 **Articles Photo Upload**
    -   Edit.vue: Adicionado `_method: 'PUT'` para file uploads
    -   Article.php: Adicionado `$appends = ['foto_url']`
    -   Fix: Laravel method spoofing com multipart/form-data

### Ficheiros Modificados

**Security Implementation:**

-   `app/Models/Entity.php` - Encrypted casts
-   `app/Models/Contact.php` - Encrypted casts
-   `app/Models/BankAccount.php` - Encrypted casts
-   `app/Console/Commands/EncryptExistingData.php` - NEW
-   `app/Http/Middleware/ForceHttps.php` - NEW
-   `app/Providers/AppServiceProvider.php` - HTTPS forcing
-   `bootstrap/app.php` - Middleware registration
-   `config/filesystems.php` - Private disk configuration

**Document Protection:**

-   `app/Http/Controllers/DocumentController.php` - Private storage
-   `app/Http/Controllers/SupplierInvoiceController.php` - Private storage

**Bug Fixes:**

-   `app/Models/Article.php` - $appends fix
-   `resources/js/Pages/Articles/Edit.vue` - \_method fix

---

## v0.17.0 — 16 Nov 2025

**Digital Archive Module + UX Improvements**

### O que foi feito

**Novo Módulo: Arquivo Digital**

-   ✅ **Sistema completo de gestão de documentos**
    -   Migration: `documents` table com polymorphic relations (documentable_type/id)
    -   Campos: name, original_filename, file_path, file_size, mime_type, category, module
    -   Versioning system: parent_id para histórico de versões
    -   Metadata: description, tags (JSON), expires_at, status (active/archived/deleted)
    -   Soft deletes implementado
-   ✅ **Document Model**
    -   Relations: morphTo (documentable), belongsTo (uploader, parent), hasMany (versions)
    -   Scopes: active, category, module, search, expiringSoon
    -   Accessors: file_url, formatted_size, is_expired
    -   Static methods: categories() array, modules() array
-   ✅ **DocumentController**
    -   CRUD completo com validação (max 10MB)
    -   Métodos especiais: download(), getEntities() (AJAX), stats() (dashboard)
    -   Storage em `storage/documents`
    -   Suporta versioning (upload novo ficheiro cria nova versão)
-   ✅ **Frontend Vue 3 + Inertia**
    -   Index.vue: Grid view (1-4 colunas responsive) com filtros (search, category, module, date range)
    -   Show.vue: Preview (PDF em iframe, imagens), metadata sidebar, version history
    -   UploadModal.vue: Custom modal com drag & drop, file preview, form completo
    -   Default imports (não named) para componentes Shadcn/ui
-   ✅ **9 Categorias de Documentos**
    -   contrato (blue), fatura (red), proposta (green), identificacao (purple)
    -   certificado (yellow), relatorio (indigo), comprovativo (pink)
    -   correspondencia (cyan), outros (gray)
-   ✅ **Módulos Integrados**
    -   Associação polimórfica com: clients, suppliers, proposals, customer-orders
    -   Dropdown dinâmico carrega entidades via AJAX
-   ✅ **Permissions System**
    -   4 permissões: digital-archive.create/read/edit/delete
    -   Seeder: DigitalArchivePermissionsSeeder
    -   Atribuídas a: Super Admin (todas), Gestor Geral (todas), Visualizador (read only)
-   ✅ **Menu Integration**
    -   Item "Arquivo Digital" no sidebar (ícone FolderOpen purple)
    -   Requires digital-archive permission para aparecer
    -   Disabled: false (ativado)

**Melhorias de UX:**

-   ✅ **Padding em Filtros** (6 componentes atualizados)
    -   ContactsDataTable.vue: filtros status e entidades
    -   Articles/Index.vue: 4 filtros (tipo, gama, estado, ordenação)
    -   EntitiesDataTable.vue: filtro ativo/inativo (já corrigido anteriormente)
    -   Novo padrão: `h-10 px-6 py-2 pr-12` (24px base, 48px right para seta)
    -   Focus ring adicionado: `focus:outline-none focus:ring-2 focus:ring-blue-500`
    -   Acomoda textos longos (ex: "Inativos", "Todas as Entidades", "Maior Stock")
-   ✅ **Menu Configurações**
    -   Corrigido: dropdown permanece expandido ao navegar para "Financeiro - IVA" e "Logs"
    -   Adicionados `vat-rates` e `logs` à lista de rotas que expandem automaticamente
    -   AuthenticatedLayout.vue: configRoutes array atualizado

### Padrão de Implementação

**Polymorphic Relations:**

```php
// Migration
$table->morphs('documentable'); // _type + _id

// Model
public function documentable() {
    return $this->morphTo();
}
```

**File Upload com Validação:**

```php
$request->validate([
    'file' => 'required|file|max:10240', // 10MB
]);
$path = $request->file('file')->store('documents');
```

**Vue Import Pattern (Shadcn/ui):**

```javascript
// Default imports (not named)
import Button from "@/Components/ui/Button.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
```

**Select Padding Pattern:**

```vue
<select class="h-10 px-6 py-2 pr-12 text-sm ...">
```

### Bugs Corrigidos

1. **Import Errors - Case Sensitivity**
    - Problema: Windows case-insensitive mas Vite case-sensitive
    - Solução: Todos imports com casing exato (Button.vue, Input.vue, Select.vue)
2. **Import Errors - Named vs Default**
    - Problema: `"Button" is not exported by "Button.vue"`
    - Solução: Default imports em vez de named (`import Button` não `import { Button }`)
3. **Dialog Component Missing**
    - Problema: Dialog.vue não existe no projeto
    - Solução: Modal custom com fixed overlay em vez de Shadcn Dialog
4. **Dropdown Padding Insufficient**
    - Problema: Seta dropdown tocando texto ("Todos", "Inativos", nomes longos)
    - Solução: Aumentado de px-3 para px-6, pr-12 (3 iterações até satisfatório)
5. **Menu Configurações Fechando**
    - Problema: Ao clicar "Financeiro IVA" ou "Logs" dropdown fechava
    - Solução: Rotas adicionadas ao array de auto-expansão

### Ficheiros Criados

-   database/migrations/2025_11_16_180325_create_documents_table.php
-   app/Models/Document.php
-   app/Http/Controllers/DocumentController.php
-   resources/js/Pages/DigitalArchive/Index.vue
-   resources/js/Pages/DigitalArchive/Show.vue
-   resources/js/Components/UploadModal.vue
-   database/seeders/DigitalArchivePermissionsSeeder.php

### Ficheiros Modificados

-   routes/web.php (DocumentController routes adicionadas)
-   resources/js/Layouts/AuthenticatedLayout.vue (menu item + configRoutes)
-   resources/js/Components/ui/ContactsDataTable.vue (padding)
-   resources/js/Components/ui/EntitiesDataTable.vue (padding - anterior)
-   resources/js/Pages/Articles/Index.vue (padding 4 selects)

### Estatísticas

-   **Novo módulo:** Digital Archive (18º módulo)
-   **Componentes criados:** 3 (Index, Show, UploadModal)
-   **Permissions:** 4 novas (digital-archive.\*)
-   **Categorias de documentos:** 9
-   **File upload:** Max 10MB, múltiplos formatos (PDF, DOC, XLS, IMG)
-   **UX improvements:** 6 componentes com padding corrigido
-   **Bugs corrigidos:** 5 (imports, modal, padding, menu)

### Impacto

-   ✅ Sistema de arquivo digital completo e funcional
-   ✅ Gestão de documentos com versioning e metadata
-   ✅ Preview de PDF e imagens no browser
-   ✅ Drag & drop para upload de ficheiros
-   ✅ UX melhorada em filtros (espaço adequado para textos longos)
-   ✅ Navegação de menu mais intuitiva (configurações não fecha)
-   ✅ Zero erros de build (todos imports resolvidos)

---

## v0.16.0 — 16 Nov 2025

**Supplier Invoices - Refatoração Completa para Shadcn Form & Consistência Visual**

### O que foi feito

**Refatoração do Módulo Faturas Fornecedor:**

-   ✅ **Create.vue** refatorado com Shadcn/ui Form components
    -   Todos os 5 campos convertidos para FormField + Input/Select
    -   Computed filteredOrders para encomendas do fornecedor
    -   Redução de ~40% no código, melhor legibilidade
-   ✅ **Edit.vue** refatorado com Shadcn/ui Form components
    -   6 seções incluindo campo readonly para número da fatura
    -   Modal de comprovativo de pagamento funcional
    -   Corrigida estrutura de breadcrumbs duplicada (500 error)
-   ✅ **Show.vue** criada do zero
    -   Layout 2 colunas: informações principais + sidebar
    -   Seção de documentos com downloads (fatura + comprovativo)
    -   Botões de navegação: Voltar (ArrowLeft) + Editar (Pencil)
    -   Metadados do sistema (created_at, updated_at)
-   ✅ **Index.vue** corrigida e atualizada
    -   Toolbar integrado dentro do card principal
    -   Filtros inline (1ª linha: search, fornecedor, estado, botão criar)
    -   Filtros de data na 2ª linha
    -   Corrigida paginação (estava dentro `</tbody>` causando erro de load)
    -   Ícones de ação padronizados (h-4 w-4)

**Consistência Visual Aplicada (4 páginas):**

-   Header compacto: h1 2xl (não 3xl), ícone h-6 w-6 (não h-8 w-8)
-   Ícone background: p-2 rounded-lg (não p-3 rounded-full)
-   Breadcrumbs simplificados: separador "/" sem divs extras
-   Removidos wrappers py-12 e max-w-\*
-   Botões com gap-3, rounded-lg, transition-colors

**Padrão de Implementação:**

```vue
<!-- Antes (HTML puro) -->
<input type="text" v-model="form.field" class="..." />

<!-- Depois (Shadcn Form) -->
<FormField id="field" label="Label" :error="form.errors.field">
    <Input v-model="form.field" />
</FormField>
```

**Imports Individuais:**

```javascript
import Form from "@/Components/ui/Form.vue";
import FormField from "@/Components/ui/FormField.vue";
import Input from "@/Components/ui/Input.vue";
import Select from "@/Components/ui/Select.vue";
import Button from "@/Components/ui/Button.vue";
```

### Bugs Corrigidos

1. **Index.vue - Module Load Error**
    - Problema: Paginação HTML dentro `</tbody>` causava "Failed to fetch dynamically imported module"
    - Solução: Movida paginação para fora da estrutura `<table>`
2. **Edit.vue - 500 Internal Server Error**
    - Problema: Breadcrumbs duplicados (dois `</nav>` closures)
    - Solução: Removidas tags de fecho duplicadas
3. **Index.vue - Botão visualizar não funcionava**
    - Problema: Show.vue não existia
    - Solução: Criada página Show.vue completa
4. **Show.vue - Faltava botão voltar**
    - Problema: Apenas botão editar no header
    - Solução: Adicionado Link com ArrowLeft icon

### Ficheiros Modificados

-   resources/js/Pages/SupplierInvoices/Create.vue (refatorado)
-   resources/js/Pages/SupplierInvoices/Edit.vue (refatorado + corrigido)
-   resources/js/Pages/SupplierInvoices/Index.vue (corrigido + atualizado)
-   resources/js/Pages/SupplierInvoices/Show.vue (criado)

### Estatísticas

-   **Páginas refatoradas:** 4 (Create, Edit, Index, Show)
-   **Redução de código:** ~40% em Create/Edit
-   **Componentes Shadcn:** Form, FormField, Input, Select, Button
-   **Ícones Lucide:** FileText, Plus, Eye, Pencil, Trash2, Download, ArrowLeft, Mail
-   **Bugs corrigidos:** 4 (pagination, breadcrumbs, view button, back button)

### Impacto

-   ✅ 100% compliance com especificação Shadcn/ui Form
-   ✅ Consistência visual com outros módulos (CustomerOrders, ClientAccounts)
-   ✅ Código mais limpo e manutenível (~40% menos linhas)
-   ✅ UX melhorada (botão voltar, view funcional, filtros integrados)
-   ✅ Zero erros de sintaxe ou carregamento

---

## v0.15.1 — 16 Nov 2025

**Activity Logging Completo em Todos os Controllers**

### O que foi feito

**Implementação Abrangente de Activity Logging:**

-   ✅ Adicionado Spatie Activity Log em **16 controllers** (100% cobertura)
-   ✅ Logs automáticos para create, update, delete em todos os módulos
-   ✅ Captura de IP address, user agent e deleted entity details
-   ✅ Atualização da interface Logs/Index.vue com 18 módulos mapeados
-   ✅ Labels em português para todos os módulos (Entity→Entidades, Contact→Contactos, etc.)

**Controllers com Logging (Priority 1 - Config):**

-   ContactController: store, update, destroy com deleted_contact details
-   ArticleController: store, update, destroy com deleted_article details (referencia, nome, preco)
-   CountryController: store, update, destroy com deleted_country details (name, iso_code)
-   ContactFunctionController: store, update, destroy com deleted_function details
-   VatRateController: store, update, destroy com deleted_vat_rate details (name, rate, is_default)

**Controllers com Logging (Priority 2 - Business):**

-   ProposalController: store/update após DB.commit() com lines_count, destroy com deleted_proposal
-   CustomerOrderController: store/update após DB.commit() com items_count, destroy com deleted_order
-   SupplierOrderController: store/update após DB.commit() com items_count, destroy com deleted_order
-   BankAccountController: store, update, destroy com deleted_account details (nome, banco, iban, saldo)
-   ClientAccountController: store, update, destroy com deleted_movement details
-   SupplierInvoiceController: store, update, destroy com deleted_invoice details

**Controllers com Logging (Priority 3 - Calendar/Settings):**

-   CalendarEventController: store/update após sharedWith sync, destroy com deleted_event details
-   CalendarEventTypeController: store, update, destroy com deleted_type details (name, color)
-   CalendarEventActionController: store, update, destroy com deleted_action details
-   CompanyController: update com logo_updated boolean (singleton - sem create/delete)

**UI Atualizada - Logs/Index.vue:**

-   18 módulos mapeados: Entity, Contact, Article, Country, ContactFunction, VatRate, User, Role
-   Novos: Proposal, CustomerOrder, SupplierOrder, BankAccount, ClientAccount, SupplierInvoice
-   Novos: CalendarEvent, CalendarEventType, CalendarEventAction, Company
-   Labels portugueses completos: getModuleLabel() com 18 módulos
-   Action labels completos: created→Criado, updated→Atualizado, deleted→Eliminado

### Padrão de Implementação

**Código consistente em todos os controllers:**

```php
use Illuminate\Support\Facades\Auth;

// store()
activity()
    ->performedOn($model)
    ->causedBy(Auth::user())
    ->withProperties([
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ])
    ->log('created');

// update()
activity()
    ->performedOn($model)
    ->causedBy(Auth::user())
    ->withProperties([
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ])
    ->log('updated');

// destroy()
activity()
    ->performedOn($model)
    ->causedBy(Auth::user())
    ->withProperties([
        'deleted_entity' => [...details...],
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ])
    ->log('deleted');
$model->delete();
```

### Casos Especiais Tratados

**Transações DB:**

-   ProposalController, CustomerOrderController, SupplierOrderController
-   Logs colocados APÓS `DB::commit()` para garantir sucesso da transação
-   Propriedades adicionais: `lines_count` e `items_count`

**Deleted Entity Details:**

-   Todos os destroy() methods capturam detalhes ANTES de `$model->delete()`
-   Informações preservadas: números, nomes, valores, estados
-   Permite reconstruir histórico completo mesmo após eliminação

**Singleton Pattern:**

-   CompanyController apenas tem update() (sem create/delete)
-   Propriedade adicional `logo_updated` (boolean) quando logo é alterado

**Shared Relationships:**

-   CalendarEventController logs após `sharedWith->sync()` para incluir partilha

### Estatísticas

-   **Controllers modificados:** 16
-   **Edits de código:** ~64 string replacements
-   **Módulos UI mapeados:** 18 (11 novos)
-   **Propriedades capturadas:** IP, user agent, deleted details em 100% dos logs
-   **Commits:** 1 (b74c73e)
-   **Tempo:** 3 horas

### Impacto

-   ✅ Sistema de auditoria 100% completo
-   ✅ Rastreamento total de todas as operações CRUD
-   ✅ Histórico completo preservado mesmo após eliminações
-   ✅ Interface pronta para exibir todos os logs corretamente
-   ✅ Compliance com requisitos de controlo e rastreabilidade

---

## v0.15.0 — 15-16 Nov 2025

**Módulos criados:** Propostas Comerciais, Encomendas Cliente (melhorias), Encomendas Fornecedor (melhorias)

### O que foi feito

**Propostas Comerciais (novo):**

-   Criação completa do módulo de gestão de propostas a clientes
-   Numeração automática tipo PROP-2025-0001
-   Sistema de linhas: artigo + quantidade + preço + fornecedor
-   Estados: Rascunho e Fechado
-   Botão para converter proposta em encomenda cliente (só aparece quando fechado)
-   Download de PDF profissional com logo da empresa

**Encomendas Cliente (melhorias):**

-   Adicionei geração de PDF (antes não tinha)
-   Botão roxo para download
-   Conversão automática para múltiplas encomendas fornecedor (agrupa por fornecedor)

**Encomendas Fornecedor (melhorias):**

-   Adicionei geração de PDF
-   Template específico para fornecedores (não clientes)
-   Data de entrega em destaque

**PDFs:**

-   Templates profissionais para os 3 módulos
-   Logo da empresa no cabeçalho
-   Informação cliente/fornecedor em 2 colunas
-   Tabela de artigos
-   Otimizado para caber em 1 página A4

### Base de Dados

**Tabelas criadas:**

-   `proposals` - propostas com número único, data, cliente, estado, total
-   `proposal_lines` - linhas da proposta com artigo, quantidade, preço, fornecedor

**Tabelas modificadas:**

-   Adicionei coluna `total` em `proposal_lines` (estava a faltar, dava erro)

**Migrations executadas:**

-   create_proposals_table.php
-   create_proposal_lines_table.php
-   add_total_column_to_proposal_lines_table.php

### Bugs Corrigidos

1. **Faltava coluna 'total'** na tabela proposal_lines → Criei migration para adicionar
2. **Nome da rota errado** ('proposals.convert' vs 'proposals.convert-to-order') → Corrigi para hyphen
3. **CustomerOrder sem número** após conversão → Adicionei generateNumber() no controller
4. **Campo 'name' vs 'nome'** nos artigos → Corrigi para usar 'nome' (português)
5. **PDF muito longo** → Mudei observações para dentro da tabela de detalhes
6. **Active checkbox com erro** nas permissões → Converti para boolean no controller
7. **Roles inativas ainda davam permissões** → Sobrescrevi getAllPermissions() no User model
8. **Módulos não apareciam** na edição de permissões → Adicionei 12 módulos em falta no getModuleLabel()

### Ficheiros Criados

**Models:**

-   app/Models/Proposal.php
-   app/Models/ProposalLine.php

**Controllers:**

-   app/Http/Controllers/ProposalController.php (7 métodos CRUD + PDF + conversão)

**Views:**

-   resources/js/Pages/Proposals/Index.vue
-   resources/js/Pages/Proposals/Create.vue
-   resources/js/Pages/Proposals/Edit.vue
-   resources/views/proposals/pdf.blade.php
-   resources/views/customer_orders/pdf.blade.php
-   resources/views/supplier_orders/pdf.blade.php

**Rotas:**

-   Route::resource('proposals') → 7 rotas RESTful
-   Route::post('proposals/{proposal}/convert-to-order')
-   Route::get('proposals/{proposal}/pdf')

### Permissões

Criei 4 permissões novas para o módulo Propostas:

-   proposals.create
-   proposals.read
-   proposals.update
-   proposals.delete

Total agora: **68 permissões** (17 módulos × 4 ações)

### Estatísticas

-   Código escrito: ~1200 linhas
-   Commits: 18
-   Tempo: 2 dias (15-16 Nov)
-   Bugs corrigidos: 8

---

## v0.14.0 — 13-14 Nov 2025

**Módulos criados:** Calendário de Eventos, Tipos de Eventos, Ações de Eventos

### O que foi feito

**Calendário:**

-   Integração com FullCalendar v6
-   Interface interativa com vistas mês/semana/dia/lista
-   Criar eventos clicando na data
-   Drag & drop para reagendar
-   Filtros por utilizador e cliente/fornecedor
-   Estados com cores: Agendado (azul), Em Curso (amarelo), Concluído (verde), Cancelado (vermelho)

**Tipos de Eventos (configuração):**

-   Reunião, Visita Cliente, Tarefa, Formação
-   Cada tipo com cor personalizada
-   Color picker no formulário

**Ações de Eventos (configuração):**

-   Confirmar, Reagendar, Aprovar, Concluir, Cancelar, Adiar
-   Workflow para gestão de follow-ups

### Base de Dados

**Tabelas criadas:**

-   `calendar_events` - eventos com data/hora, tipo, estado, utilizador, entidade
-   `calendar_event_types` - tipos configuráveis com cores
-   `calendar_event_actions` - ações de workflow

### Permissões

Criei 12 permissões novas (3 módulos × 4 ações):

-   calendar-events.\*
-   calendar-event-types.\*
-   calendar-event-actions.\*

Total: **64 permissões** (16 módulos × 4 ações)

---

## v0.13.0 — 12 Nov 2025

**Módulo criado:** Conta Corrente de Clientes

### O que foi feito

-   Sistema de débitos e créditos por cliente
-   Cálculo automático de saldos após cada movimento
-   Painel de estatísticas (total débito, crédito, saldo)
-   7 categorias: Fatura, Pagamento, Nota Crédito, etc.
-   Filtros por cliente, tipo, categoria, período

### Base de Dados

**Tabela criada:**

-   `client_account_movements` - movimentos com débito/crédito e saldo

**Métodos especiais:**

-   calculateBalance() - calcula saldo baseado no anterior
-   updateSubsequentBalances() - atualiza todos os posteriores
-   recalculateBalancesForEntity() - recalcula saldo completo
-   getCurrentBalance() - retorna saldo atual do cliente

---

## v0.12.0 — 11 Nov 2025

**Módulo criado:** Faturas de Fornecedores

### O que foi feito

-   CRUD completo de faturas
-   Numeração automática FF-YYYY-####
-   Upload de documento da fatura (PDF/imagem)
-   Upload de comprovativo de pagamento
-   Envio automático de email ao fornecedor com comprovativo anexo
-   Modal quando muda estado para "Paga"
-   Estados: Pendente, Paga, Vencida, Cancelada

### Email

Criei `PaymentProofMail.php`:

-   Template HTML com logo e dados da empresa
-   Anexa PDF do comprovativo
-   Assunto: "Comprovativo de Pagamento - Fatura {numero}"
-   Envia para email do fornecedor

---

## v0.11.0 — 10 Nov 2025

**Módulo criado:** Conta Corrente Bancária (Transações)

### O que foi feito

-   Registo de movimentos bancários (débito/crédito)
-   Saldo calculado automaticamente
-   Modal de criação rápida
-   Filtros por conta, tipo, período
-   9 categorias: Transferência, Pagamento, Recebimento, Juros, Comissões, etc.

---

## v0.10.0 — 9 Nov 2025

**Módulo criado:** Contas Bancárias

### O que foi feito

-   CRUD completo de contas bancárias
-   Validação de IBAN automática
-   Campos: banco, IBAN, SWIFT, moeda (EUR/USD/GBP)
-   Tipos: Conta Corrente, Poupança, Ordenados, Investimentos
-   Estados: Ativa, Inativa, Encerrada
-   Checkbox para conta padrão
-   Formatação IBAN em blocos de 4

---

## v0.9.0 — 8 Nov 2025

**Módulo criado:** Configurações da Empresa

### O que foi feito

-   Formulário para editar dados da empresa
-   Upload de logotipo (PNG/JPG/GIF até 2MB)
-   Logo aparece em: Login, Welcome, Sidebar, PDFs
-   Campos: nome, NIF, morada, código postal, localidade
-   Singleton (só 1 registo)

---

## v0.8.0 — 7 Nov 2025

**Módulo criado:** Histórico de Atividades (Logs)

### O que foi feito

-   Spatie Activitylog instalado e configurado
-   Tabela com 7 colunas: Data, Hora, Utilizador, Menu, Ação, Dispositivo, IP
-   Captura automática de IP e User Agent
-   Detecção de dispositivo (Desktop/Mobile/Tablet)
-   Logs em todos os módulos (criar/editar/eliminar)
-   Badges coloridos por tipo de ação

---

## v0.7.0 — 6 Nov 2025

**Módulo criado:** Gestão de Acessos (Utilizadores e Permissões)

### O que foi feito

**Utilizadores:**

-   CRUD completo (nome, email, telemóvel, grupo, estado)
-   Estados: Ativo/Inativo

**Permissões:**

-   Spatie Laravel Permission instalado (v6.23.0)
-   68 permissões criadas (17 módulos × 4 ações CRUD)
-   6 grupos: Super Admin, Admin, Gestor Comercial, Gestor Financeiro, Editor, Visualizador
-   Botões só aparecem se utilizador tiver permissão
-   Sistema genérico com `v-if="can.action"` em Vue

---

## Versões Anteriores (v0.1 - v0.6)

Desenvolvimento inicial do projeto com módulos básicos:

**v0.6** — Taxas de IVA  
**v0.5** — Funções de Contacto  
**v0.4** — Países  
**v0.3** — Artigos (Produtos/Serviços)  
**v0.2** — Contactos  
**v0.1** — Entidades (Clientes/Fornecedores)

---

_Última atualização: 16 Nov 2025_
