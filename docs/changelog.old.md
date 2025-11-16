# 📝 Changelog — Gest-App

---

## [0.15.0] — 2025-11-15/16

### 📦 Módulos de Propostas e Encomendas

**Sistema completo de gestão de propostas, encomendas cliente e fornecedor com PDFs profissionais**

#### 🎯 Funcionalidades Implementadas

**Módulo Propostas:**
- ✅ CRUD completo para gestão de propostas comerciais
- ✅ Numeração automática PROP-YYYY-#### com verificação de duplicados
- ✅ Sistema de linhas: artigos com quantidade, preço de custo e fornecedor
- ✅ Cálculo automático de totais via boot hooks no model
- ✅ Estados: Rascunho e Fechado (badges coloridos)
- ✅ Conversão para CustomerOrder quando estado='fechado'
- ✅ Geração de PDF profissional com template personalizado
- ✅ Observações integradas no layout do PDF

**Módulo Encomendas Cliente:**
- ✅ CRUD completo com numeração automática EC-YYYY-####
- ✅ Relacionamento opcional com proposta de origem (proposal_id)
- ✅ Conversão multi-fornecedor para SupplierOrders
- ✅ Agrupa artigos automaticamente por fornecedor
- ✅ Geração de PDF com template profissional
- ✅ Estados: draft (rascunho) e closed (fechado)

**Módulo Encomendas Fornecedor:**
- ✅ CRUD completo com numeração automática EF-YYYY-####
- ✅ Relacionamento opcional com encomenda cliente (customer_order_id)
- ✅ 5 estados de workflow: draft, sent, confirmed, received, cancelled
- ✅ Geração de PDF com dados do fornecedor
- ✅ Data de entrega prevista destacada

#### 🗃️ Base de Dados

**Tabela: `proposals`**
- Campos: numero (unique), data_proposta, validade, entity_id (FK), estado (enum), valor_total (decimal), observacoes (text)
- Índices: numero, data_proposta, estado, entity_id
- Soft deletes habilitado

**Tabela: `proposal_lines`**
- Campos: proposal_id (FK cascade), article_id (FK), entity_id (FK fornecedor), quantidade, preco_custo, total
- Índices: [proposal_id, article_id]
- Cálculo automático via boot hooks

**Tabela: `customer_orders`**
- Campos: number, proposal_date, validity_date, customer_id (FK), proposal_id (FK nullable), status, total_value, notes
- Estados: draft, closed

**Tabela: `customer_order_items`**
- Campos: customer_order_id, article_id, supplier_id, quantity, unit_price, total

**Tabela: `supplier_orders`**
- Campos: number, order_date, delivery_date, supplier_id (FK), customer_order_id (FK nullable), status, total_value, notes
- Estados: draft, sent, confirmed, received, cancelled

**Tabela: `supplier_order_items`**
- Campos: supplier_order_id, article_id, quantity, unit_price, total

#### 📄 Sistema de PDFs

**Templates Criados:**
- `resources/views/proposals/pdf.blade.php`
- `resources/views/customer_orders/pdf.blade.php`
- `resources/views/supplier_orders/pdf.blade.php`

**Características dos PDFs:**
- Header com logo e dados da empresa (Company::first())
- Layout otimizado para 1 página A4
- Informações do cliente/fornecedor em 2 colunas
- Tabela de artigos profissional com referências
- Observações integradas na tabela de detalhes (não em seção separada)
- Total geral com destaque visual
- Footer com data de geração e informações adicionais
- Package: barryvdh/laravel-dompdf

**Métodos de Download:**
- ProposalController::downloadPdf()
- CustomerOrderController::generatePDF()
- SupplierOrderController::generatePDF()

**Botões de Download:**
- Ícone FileText (lucide-vue-next) em todas as views Index e Edit
- Cor roxa para diferenciação visual
- Link direto: `route('module.pdf', id)`

#### 🔄 Sistema de Conversão

**Proposta → Encomenda Cliente:**
- Método: ProposalController::convertToOrder()
- Cria CustomerOrder no estado 'draft' para revisão
- Copia: cliente, data, validade, todas as linhas, observações
- Gera número automático EC-YYYY-####
- Mantém rastreabilidade via proposal_id

**Encomenda Cliente → Encomendas Fornecedor:**
- Método: CustomerOrderController::convertToSupplierOrders()
- Agrupa itens por fornecedor (supplier_id)
- Cria uma SupplierOrder por fornecedor único
- Todas criadas no estado 'draft'
- Mantém rastreabilidade via customer_order_id
- Data de entrega: +7 dias da data da encomenda

#### 🎨 Frontend

**Views Criadas:**

**Propostas:**
- Index.vue: DataTable com filtros (pesquisa, estado), badges coloridos, botão PDF
- Create.vue: Formulário com linhas dinâmicas de artigos, dropdown de clientes
- Edit.vue: Botão "Converter para Encomenda" condicional (v-if="form.estado === 'fechado'"), botão PDF

**CustomerOrders:**
- Index.vue: DataTable, botão PDF, filtros de pesquisa
- Edit.vue: Botão PDF, botão converter para supplier orders

**SupplierOrders:**
- Index.vue: DataTable com 5 estados, botão PDF
- Edit.vue: Botão PDF, dropdown de estados

**Características Comuns:**
- Ícone FileText importado de lucide-vue-next
- Botões PDF em roxo (#9333ea hover:#7e22ce)
- Formatação de valores monetários: Intl.NumberFormat('pt-PT', currency: 'EUR')
- Formatação de datas: Carbon PT

#### 🔒 Permissões

**Permissões Criadas:**
- `proposals.create`, `proposals.read`, `proposals.update`, `proposals.delete`
- `proposals.convert-to-order` (específica para conversão)
- `customer-orders.create`, `customer-orders.read`, `customer-orders.update`, `customer-orders.delete`
- `supplier-orders.create`, `supplier-orders.read`, `supplier-orders.update`, `supplier-orders.delete`

**Seeder:**
- ProposalPermissionsSeeder.php executado com sucesso
- Permissões atribuídas aos grupos Super Admin e Administrador

**Rotas Protegidas:**
- Middleware `permission:module.action` em todas as rotas
- Rota especial: proposals.convert-to-order com middleware `permission:proposals.update`

#### 🐛 Correções de Bugs

**Problema 1: Campo 'total' faltando em proposal_lines**
- ❌ SQL error: "Unknown column 'total'"
- ✅ Criada migration: `2025_11_15_230737_add_total_column_to_proposal_lines_table.php`
- ✅ Adicionado campo `total` decimal(10,2) com default 0

**Problema 2: Nome de rota incorreto**
- ❌ Edit.vue usava `route('proposals.convert')` (404)
- ✅ Corrigido para `route('proposals.convert-to-order')` (rota registada)

**Problema 3: CustomerOrder sem número ao converter**
- ❌ Campo 'number' ficava null
- ✅ Adicionado `CustomerOrder::generateNumber()` no método convertToOrder()

**Problema 4: Nome do campo de artigo**
- ❌ PDF usava `article.name` (inexistente)
- ✅ Corrigido para `article.nome` (correto conforme BD)

**Problema 5: PDF muito longo**
- ❌ Observações em seção separada ocupava muito espaço
- ✅ Movido observações para dentro da tabela de detalhes (azul)
- ✅ Cliente em 2 colunas em vez de 1 coluna
- ✅ Layout agora cabe em 1 página A4

#### 🔧 Sistema de Permissões - Correções Críticas

**Problema 1: Checkbox 'active' com tipo errado**
- ❌ Vue warning: "Expected Boolean, got Number with value 1"
- ✅ RoleController::edit() agora retorna `'active' => (bool) $role->active`
- ✅ Cast explícito para boolean resolve problema no componente Checkbox

**Problema 2: Roles inativos ainda concediam permissões**
- ❌ Spatie getAllPermissions() ignorava campo `active` dos roles
- ✅ Override de getAllPermissions() no modelo User
- ✅ Método getActiveRolePermissions() filtra apenas roles com `active = true`
- ✅ Utilizadores com roles inativos perdem permissões imediatamente

**Problema 3: Módulos não apareciam na edição de grupos**
- ❌ customer-orders, supplier-orders e outros 8 módulos NÃO apareciam na UI de edição
- ❌ RoleController::getModuleLabel() não tinha mapeamento para esses módulos
- ✅ Adicionados 12 módulos faltantes ao array $labels:
  - customer-orders (Encomendas Cliente)
  - supplier-orders (Encomendas Fornecedor)
  - bank-accounts (Contas Bancárias)
  - client-accounts (Contas Correntes Cliente)
  - supplier-invoices (Faturas Fornecedor)
  - calendar-events (Eventos)
  - calendar-event-types (Tipos de Eventos)
  - calendar-event-actions (Ações de Eventos)

**Impacto:**
- ✅ Interface de edição de permissões agora mostra TODOS os módulos
- ✅ Checkboxes de CRUD aparecem para todos os 17 módulos
- ✅ Gestor Financeiro pode ter permissões editadas corretamente
- ✅ Sistema de permissões 100% funcional

**Teste Validado:**
- Role "Gestor Financeiro" editado com sucesso
- Permissões customer-orders.delete e supplier-orders.delete agora disponíveis para seleção
- Utilizador financeiro agora vê/não vê botões conforme permissões reais
- Cache de permissões limpo com `php artisan permission:cache-reset`

#### 📦 Build

**Frontend:**
- `npm run build` executado com sucesso
- 2529 módulos transformados
- Assets compilados em public/build/
- Todas as views Vue atualizadas e compiladas

#### 📊 Estatísticas

**Linhas de Código:**
- 3 Models criados (Proposal, ProposalLine + relações em CustomerOrder/SupplierOrder)
- 3 Controllers com métodos de conversão e PDF
- 9 Views Vue (3 Index, 3 Create, 3 Edit)
- 3 Templates PDF Blade
- 4 Migrations (proposals, proposal_lines, add_total_column, seeders)
- 1 Seeder de permissões

**Permissões:**
- Total de permissões no sistema: 68 (17 módulos × 4 ações)
- Propostas: 5 permissões (4 CRUD + 1 conversão)

---

## [0.14.1] — 2025-11-12

### 🐛 Correções de Bugs - Módulo Calendário

**Problema de Permissões:**

-   🔧 Corrigida verificação de permissões no `Show.vue` (usava sintaxe incorreta `$page.props.auth.can['permission']`)
-   ✅ Implementada sintaxe correta: `$page.props.auth.permissions.includes('permission')`

**Conflito de Nomes de Propriedades Vue:**

-   🔧 Renomeados campos do formulário em `Create.vue` para evitar conflito com propriedades internas do Vue
-   ✅ `form.data` → `form.event_date` (com transformação no submit para manter compatibilidade com backend)
-   ✅ `form.hora` → `form.event_time`
-   📝 Razão: `data` é palavra reservada em Vue e causava erro "modelValue expected String|Number, got Function"

**Sistema de Permissões:**

-   🔧 Atribuídas permissões `calendar-events.*` aos roles que tinham apenas `calendar.*`
-   ✅ Roles atualizados: Gestor Comercial, Gestor Financeiro, Editor (full access), Visualizador (read only)
-   ✅ Menu Calendário agora visível para todos os utilizadores com permissões corretas

**Scripts de Diagnóstico Criados:**

-   `check_user_permissions.php` - Verificar permissões de utilizador
-   `check_calendar_permissions.php` - Listar permissões de calendário na BD
-   `assign_calendar_permissions.php` - Atribuir permissões a role específico
-   `update_calendar_permissions.php` - Atualizar permissões em massa

---

## [0.14.0] — 2025-11-12

### 📅 Módulo Principal do Calendário

**Sistema de Gestão de Eventos com FullCalendar integrado**

#### 🎯 Funcionalidades Implementadas

**Calendário Principal:**

-   ✅ Interface FullCalendar com visualizações: Mês, Semana, Dia, Lista
-   ✅ Criação rápida de eventos clicando no calendário
-   ✅ Drag & drop para reagendar eventos
-   ✅ Click em evento para visualizar detalhes
-   ✅ Filtros: Utilizador e Entidade (cliente/fornecedor)
-   ✅ Eventos coloridos por tipo (cor configurada em Calendário - Tipos)
-   ✅ Localização em português (pt-BR)

**Gestão de Eventos:**

-   ✅ CRUD completo: Criar, Visualizar, Editar, Eliminar
-   ✅ Campos: Data, Hora, Duração (minutos), Partilha (boolean), Conhecimento, Entidade, Tipo, Ação, Descrição, Estado
-   ✅ Estados: Agendado, Em Curso, Concluído, Cancelado
-   ✅ Relacionamentos: user, entity, calendar_event_type, calendar_event_action
-   ✅ Soft deletes habilitado

#### 🗃️ Base de Dados

**Tabela: `calendar_events`**

-   Campos principais:
    -   `user_id` (FK users, cascade)
    -   `entity_id` (FK entities, nullable, set null)
    -   `calendar_event_type_id` (FK calendar_event_types, cascade)
    -   `calendar_event_action_id` (FK calendar_event_actions, nullable, set null)
    -   `data` (date)
    -   `hora` (time)
    -   `duracao` (integer, minutes)
    -   `partilha` (boolean, default false)
    -   `conhecimento` (text, nullable)
    -   `descricao` (text, nullable)
    -   `estado` (enum: agendado, em_curso, concluido, cancelado)
-   Índices: data, estado, [user_id, data], [entity_id, data]
-   Soft deletes, timestamps

**Model: `CalendarEvent.php`**

-   Relationships: user(), entity(), eventType(), eventAction()
-   Scopes: agendado(), emCurso(), concluido(), cancelado(), byUser($userId), byEntity($entityId)
-   Accessors: estadoBadgeClass, estadoLabel
-   Casts: data (date), hora (datetime:H:i), duracao (integer), partilha (boolean)

#### 🔒 Segurança & Permissões

**Permissões criadas:**

-   `calendar-events.create`
-   `calendar-events.read`
-   `calendar-events.update`
-   `calendar-events.delete`

**Policy: `CalendarEventPolicy.php`**

-   Métodos: viewAny, view, create, update, delete, restore, forceDelete
-   Autorização baseada em permissões Spatie

**Distribuição:**

-   Super Admin: todas as permissões
-   Admin: todas as permissões
-   User: create, read, update (sem delete)

#### 🌐 Backend

**Controller: `CalendarEventController.php`**

-   `index()`: Renderiza página Index.vue com listas de tipos/ações/users/entities
-   `events()`: Endpoint JSON para FullCalendar
    -   Aceita query params: start, end (ISO dates), user_id, entity_id
    -   Retorna eventos no formato FullCalendar (id, title, start, end, color, extendedProps)
-   `create()`: Renderiza página Create com listas
-   `store(Request)`: Validação e criação de evento
-   `show(CalendarEvent)`: Renderiza Show com evento carregado
-   `edit(CalendarEvent)`: Renderiza Edit com evento e listas
-   `update(Request, CalendarEvent)`: Validação e atualização
-   `destroy(CalendarEvent)`: Soft delete

**Rotas (`routes/web.php`):**

-   `GET /calendar` → calendar.index (middleware: permission:calendar-events.read)
-   `GET /calendar/events-json` → calendar.events.json (JSON endpoint)
-   `GET /calendar-events/create` → calendar-events.create (middleware: permission:calendar-events.create)
-   `GET /calendar-events` → calendar-events.index
-   `GET /calendar-events/{calendarEvent}` → calendar-events.show
-   `POST /calendar-events` → calendar-events.store
-   `GET /calendar-events/{calendarEvent}/edit` → calendar-events.edit
-   `PATCH /calendar-events/{calendarEvent}` → calendar-events.update
-   `DELETE /calendar-events/{calendarEvent}` → calendar-events.destroy

#### 🎨 Frontend

**FullCalendar Integração:**

-   Packages instalados: `@fullcalendar/{core, vue3, daygrid, timegrid, interaction, list}`
-   Plugins: dayGrid, timeGrid, interaction, list
-   Configuração: PT locale, editable, selectable

**Pages Vue:**

-   **Calendar/Index.vue** (Main Calendar):

    -   FullCalendar component com toolbar (prev/next/today, view switchers)
    -   Filtros: select Utilizador, select Entidade, botão Limpar Filtros
    -   Button: Criar Evento
    -   Handlers:
        -   `select`: navega para Create com data/hora pré-preenchidas
        -   `eventClick`: navega para Show
        -   `eventDrop/eventResize`: atualiza evento via PATCH (se can.update)
    -   Refetch events quando filtros mudam
    -   Dark mode CSS overrides

-   **Calendar/Create.vue**:

    -   Formulário: user_id, entity_id, calendar_event_type_id, calendar_event_action_id, data, hora, duracao, estado, partilha (checkbox), conhecimento, descricao
    -   Selects populados com dados do backend
    -   Validação: campos obrigatórios (user, type, data, hora, duracao), formato hora (H:i)
    -   Props: types, actions, entities, users, data?, hora?

-   **Calendar/Edit.vue**:

    -   Formulário idêntico ao Create, pré-preenchido com dados do evento
    -   Props: event, types, actions, entities, users

-   **Calendar/Show.vue**:
    -   Display somente-leitura: data/hora/duração, utilizador, entidade, tipo (com cor), ação, partilha (badge), conhecimento, descrição, estado (badge), timestamps
    -   Buttons: Editar (se can.update), Eliminar (se can.delete)
    -   Ícones: Clock, User, Building2, Tag, Zap
    -   Confirmação antes de eliminar

#### 🧪 Migrações & Seeders

-   Migration: `2025_11_12_160239_create_calendar_events_table.php` ✅ Run
-   Seeder: `CalendarEventsPermissionsSeeder.php` ✅ Run

#### 📐 Menu & Navegação

-   Menu principal atualizado: "Calendário" agora ativa (href: `calendar.index`, permission: `calendar-events`)
-   Submenu Configurações: "Calendário - Tipos" e "Calendário - Ações" (já implementados em v0.13.0)

#### 📚 Observações

-   Utilizadores podem ver apenas eventos que têm permissão (filtro via permissions)
-   Entidades podem ser clientes ou fornecedores (FK para `entities`)
-   Campo `conhecimento` destina-se a armazenar lições aprendidas ou informação relevante
-   Soft deletes permitem restaurar eventos eliminados se necessário
-   FullCalendar refetch via API endpoint garante filtros dinâmicos sem reload da página

---

## [0.13.0] — 2025-11-12

### ⚙️ Módulos de Configuração do Calendário

**Sistema de Configuração de Tipos e Ações para Eventos de Calendário**

#### 🎯 Funcionalidades Implementadas

**Calendário - Tipos de Eventos:**

-   Definição de tipos de eventos (Visita, Reunião, Intervenção Técnica, Auditoria, Formação, Apresentação)
-   Personalização visual com cores (hex color picker)
-   Atribuição de ícones Lucide para identificação visual
-   Ativação/desativação de tipos
-   Validação de cores hexadecimais (#RRGGBB)
-   CRUD completo com pesquisa e filtros

**Calendário - Ações de Eventos:**

-   Definição de ações de workflow (Confirmar, Reagendar, Aprovar, Concluir, Cancelar, Adiar)
-   Padronização do fluxo de cada tipo de evento
-   Ativação/desativação de ações
-   CRUD completo com pesquisa e filtros

#### 🗃️ Base de Dados

**Tabelas Criadas:**

-   `calendar_event_types`:

    -   Campos: name (único), description, color (7 chars hex), icon (50 chars), is_active
    -   Índices: is_active, name
    -   Soft deletes habilitado
    -   Validação: color regex `/^#[0-9A-Fa-f]{6}$/`

-   `calendar_event_actions`:
    -   Campos: name (único), description, is_active
    -   Índices: is_active, name
    -   Soft deletes habilitado

**Models:**

-   `CalendarEventType.php`:

    -   Scopes: active(), inactive()
    -   Accessor: getStatusBadgeClassAttribute
    -   Fillable: name, description, color, icon, is_active

-   `CalendarEventAction.php`:
    -   Scopes: active(), inactive()
    -   Accessor: getStatusBadgeClassAttribute
    -   Fillable: name, description, is_active

#### 🎨 Interface

**Calendário - Tipos (3 páginas Vue):**

-   **Index.vue**:

    -   DataTable com 6 colunas: Nome, Descrição, Cor, Ícone, Estado, Ações
    -   Pesquisa em tempo real
    -   Display visual de cor (quadrado colorido + código hex)
    -   Display de ícone Lucide
    -   Badges coloridos por estado

-   **Create.vue**:

    -   Formulário com color picker nativo HTML5
    -   Input duplo para cor (picker visual + texto hex)
    -   Campo de ícone com link para documentação Lucide
    -   Validação de formato hexadecimal
    -   Checkbox de ativação

-   **Edit.vue**:
    -   Mesmas funcionalidades do Create
    -   Pré-preenchimento com dados existentes
    -   Validação unique excluindo o próprio registro

**Calendário - Ações (3 páginas Vue):**

-   **Index.vue**:

    -   DataTable com 4 colunas: Nome, Descrição, Estado, Ações
    -   Pesquisa em tempo real
    -   Badges coloridos por estado
    -   Ícone ListChecks para identificação

-   **Create.vue**:

    -   Formulário simples (nome, descrição, estado)
    -   Validação de campos obrigatórios
    -   Checkbox de ativação

-   **Edit.vue**:
    -   Mesmas funcionalidades do Create
    -   Validação unique excluindo o próprio registro

#### 🌱 Seeders

**Dados Pré-carregados:**

**CalendarEventTypesSeeder:**

1. Visita (Azul #3B82F6, ícone Users)
2. Reunião (Roxo #8B5CF6, ícone Calendar)
3. Intervenção Técnica (Vermelho #EF4444, ícone Wrench)
4. Auditoria (Âmbar #F59E0B, ícone ClipboardCheck)
5. Formação (Verde #10B981, ícone GraduationCap)
6. Apresentação (Rosa #EC4899, ícone Presentation)

**CalendarEventActionsSeeder:**

1. Confirmar - Confirmar a realização do evento
2. Reagendar - Alterar data/hora do evento
3. Aprovar - Aprovar o evento
4. Concluir - Marcar evento como concluído
5. Cancelar - Cancelar o evento
6. Adiar - Adiar evento sem data definida

#### 🔐 Permissões

**Seeders Criados:**

-   `CalendarEventTypesPermissionsSeeder.php`:

    -   4 permissões: calendar-event-types.{create, read, update, delete}
    -   Atribuídas a: Super Admin, Administrator

-   `CalendarEventActionsPermissionsSeeder.php`:
    -   4 permissões: calendar-event-actions.{create, read, update, delete}
    -   Atribuídas a: Super Admin, Administrator

#### 🛣️ Rotas

**14 Rotas Criadas:**

-   `calendar-event-types.*`: 7 rotas CRUD com middleware de permissões
-   `calendar-event-actions.*`: 7 rotas CRUD com middleware de permissões

#### 🧩 Controllers

**CalendarEventTypeController:**

-   Métodos: index, create, store, show, edit, update, destroy
-   Pesquisa por nome e descrição
-   Filtro por estado (ativo/inativo)
-   Validações completas (color regex, icon max length)
-   Mensagens de sucesso em português

**CalendarEventActionController:**

-   Métodos: index, create, store, show, edit, update, destroy
-   Pesquisa por nome e descrição
-   Filtro por estado (ativo/inativo)
-   Ordenação customizável
-   Mensagens de sucesso em português

#### 🎨 Design System

**Ícones Lucide:**

-   Tipos: Calendar (azul)
-   Ações: ListChecks (verde)
-   Interface consistente com resto da aplicação

**Color Picker:**

-   Input type="color" nativo HTML5
-   Sincronização com input de texto hexadecimal
-   Validação em tempo real do formato

#### 📍 Menu de Navegação

**Localização:** Configurações > Calendário

-   Calendário - Tipos (ícone Calendar, cor azul)
-   Calendário - Ações (ícone ListChecks, cor verde)
-   Controle de permissões por item
-   Badges disabled removidos (módulos ativos)

#### ✅ Validações

**Tipos de Eventos:**

-   Nome: obrigatório, único, max 255
-   Cor: obrigatório, 7 caracteres, formato hex válido
-   Ícone: opcional, max 50 caracteres
-   Descrição: opcional

**Ações de Eventos:**

-   Nome: obrigatório, único, max 255
-   Descrição: opcional

#### 🔄 Preparação para Módulo Calendário

Estes módulos foram criados como **dependências de configuração** para o futuro módulo de Calendário, que utilizará:

-   Tipos de eventos para categorização visual
-   Ações para workflow e gestão do ciclo de vida dos eventos
-   Cores e ícones para interface rica e intuitiva

---

## [0.12.0] — 2025-11-11

### 💰 Módulo de Faturas de Fornecedores

**Sistema Completo de Gestão de Faturas de Fornecedores com Envio Automático de Comprovativos**

#### 🎯 Funcionalidades Implementadas

**Gestão de Faturas:**

-   Numeração automática: FF-YYYY-#### (Fatura Fornecedor)
-   Campos completos: Data fatura, data vencimento, fornecedor, encomenda (opcional), valor total
-   Upload de documento da fatura (PDF/JPG/PNG até 5MB)
-   Estados: Pendente, Paga
-   Associação com fornecedor (entities) e encomenda de fornecedor (supplier_orders)

**Sistema de Comprovativos de Pagamento:**

-   Upload de comprovativo quando fatura é marcada como "Paga"
-   Modal automático com 3 opções:
    -   ❌ Cancelar: Reverte estado para pendente
    -   ⚠️ Não Enviar: Salva como paga sem enviar email
    -   ✅ Enviar: Faz upload e envia email com comprovativo ao fornecedor
-   Armazenamento em `supplier_invoices/proofs/`

**Envio Automático de Emails:**

-   Email personalizado com logo e dados da empresa
-   Assunto: "Comprovativo de Pagamento - Fatura {numero}"
-   Detalhes da fatura formatados (número, data, valor, encomenda)
-   Anexo: PDF do comprovativo de pagamento
-   Destinatário: Email do fornecedor
-   Integração com MailHog para testes locais

#### 🗃️ Base de Dados

**Tabela Criada:**

-   `supplier_invoices`:
    -   Campos: numero (único), data_fatura, data_vencimento, supplier_id (FK), supplier_order_id (FK nullable), valor_total, documento, comprovativo_pagamento, estado
    -   Índices: data_fatura, estado, composto (supplier_id, data_fatura)
    -   Soft deletes habilitado

**Model:**

-   `SupplierInvoice.php`:
    -   Método `generateNumber()`: Gera FF-YYYY-#### com verificação withTrashed()
    -   Scopes: pendente(), paga(), vencidas(), supplier()
    -   Accessors: getValorTotalFormatadoAttribute, getEstadoBadgeClassAttribute
    -   Boot event: Auto-geração de número na criação

#### 🎨 Interface

**Páginas Vue:**

-   **Index.vue** (556 linhas):
    -   DataTable com 8 colunas: Data, Número, Fornecedor, Encomenda, Documento, Valor Total, Estado, Ações
    -   5 filtros: pesquisa, fornecedor, estado, data início, data fim
    -   Badges coloridos por estado (verde=paga, amarelo=pendente)
    -   Botão de download para documentos
    -   Ações com controle de permissões
-   **Create.vue** (347 linhas):
    -   Formulário completo com validação
    -   Dropdown de encomendas filtrado por fornecedor selecionado
    -   Upload de documento da fatura
-   **Edit.vue** (559 linhas):
    -   Watch automático no campo estado
    -   Modal personalizado para envio de comprovativo
    -   Upload via axios com FormData
    -   Tratamento de erros e mensagens de sucesso

#### 📧 Sistema de Email

**Mailable:**

-   `PaymentProofMail.php`:
    -   Construtor: SupplierInvoice, Company, proofPath
    -   Envelope: Assunto dinâmico com número da fatura
    -   Conteúdo: View emails.payment-proof
    -   Anexo: PDF do comprovativo com nome formatado

**Template:**

-   `payment-proof.blade.php`:
    -   HTML responsivo com logo da empresa
    -   Saudação personalizada ao fornecedor
    -   Box com detalhes da fatura
    -   Assinatura com dados da empresa (NIF, morada)

#### 🔐 Permissões

**Seeder Criado:**

-   `SupplierInvoicesPermissionsSeeder.php`:
    -   4 permissões: supplier-invoices.{create, read, update, delete}
    -   Atribuídas a: Super Admin (todas), Gestor Financeiro (todas), Visualizador (read)

**Rotas Protegidas:**

-   8 rotas com middleware de permissões
-   Rota especial POST para envio de comprovativo

#### 🧪 Testes Automatizados

**Arquivo Criado:**

-   `SupplierInvoiceEmailTest.php` (345 linhas):
    -   10 métodos de teste
    -   17 asserções totais
    -   Cobertura completa do fluxo de email

**Testes Implementados:**

1. ✅ Email enviado quando comprovativo é carregado
2. ✅ Email contém dados corretos da fatura
3. ✅ Email tem anexo PDF
4. ✅ Email tem assunto correto
5. ✅ Ficheiro guardado no storage
6. ✅ Validação: email não enviado sem ficheiro
7. ✅ Validação: apenas PDF/JPG/PNG aceites
8. ✅ Email inclui encomenda quando existe
9. ✅ Controle de permissões (403 sem permissão)

**Técnicas Utilizadas:**

-   `Mail::fake()` para interceptar emails
-   `Storage::fake()` para simular armazenamento
-   `RefreshDatabase` para testes isolados
-   Criação manual de fixtures (User, Entity, Company)

#### 📚 Documentação

**Arquivo Criado:**

-   `docs/mailhog-setup.md` (500+ linhas):
    -   Guia completo de instalação do MailHog
    -   Configuração do Laravel (.env)
    -   Comandos úteis para gestão
    -   Resolução de 5 problemas comuns
    -   Alternativas (Mailtrap, Gmail, Log)
    -   Checklist de funcionamento
    -   Exemplos de código

#### 🐛 Correções Aplicadas

**Bugs Corrigidos:**

1. **Campo nome → name**: Corrigidas 8 referências em controller, views e email template
2. **Campo order_number → number**: Corrigidas 5 referências em supplier_orders
3. **AlertDialog removido**: Substituído por modal personalizado (componente não existia)
4. **Campo comprovativo_pagamento → comprovativo**: Corrigido em Edit.vue e testes
5. **Método PATCH**: Adicionado `_method: 'PATCH'` no formulário de edição

#### 🎨 Menu

**Navegação Atualizada:**

-   Menu: Financeiro → Faturas Fornecedores
-   Ícone: FileText (vermelho)
-   Rota: supplier-invoices
-   Permissão: supplier-invoices

#### ✅ Validação Completa

**Status:**

-   ✅ Migration executada com sucesso
-   ✅ Seeder de permissões executado
-   ✅ Frontend compilado (2494 módulos, 6.37s)
-   ✅ 9 testes passaram (17 asserções)
-   ✅ Email testado e validado no MailHog
-   ✅ Workflow completo funcionando

**Fluxo Testado:**

1. ✅ Criar fatura com documento
2. ✅ Marcar como paga
3. ✅ Modal aparece automaticamente
4. ✅ Upload de comprovativo
5. ✅ Email enviado com anexo
6. ✅ Recepção confirmada no MailHog

---

## [0.11.0] — 2025-11-10

### 🏦 Módulo de Contas Bancárias

**Gestão Completa de Contas Bancárias da Empresa**

#### 🎯 Funcionalidades Implementadas

**Gestão de Contas:**

-   Cadastro de contas bancárias com IBAN, banco, SWIFT/BIC
-   Tipos de conta: Corrente, Poupança, Crédito, Investimento
-   Estados: Ativa, Inativa, Encerrada
-   Controle de saldo inicial e saldo atual
-   Suporte para múltiplas moedas (EUR, USD, GBP)

**Tabela de Movimentos Bancários:**

-   Registro de todas as transações (débitos e créditos)
-   Categorização: Transferências, Pagamentos, Depósitos, Juros, etc.
-   Cálculo automático de saldo após cada movimento
-   Soft deletes para histórico completo

#### 🗃️ Base de Dados

**Tabelas Criadas:**

-   `bank_accounts`: Dados das contas (IBAN único, saldos, tipo, estado)
-   `bank_transactions`: Movimentos bancários com relacionamento cascade

**Models:**

-   `BankAccount.php`: Cálculo automático de saldo, IBAN formatado
-   `BankTransaction.php`: Atualização automática do saldo da conta

#### 🎨 Interface

**Páginas:**

-   **Index**: Listagem com filtros (tipo, estado), pesquisa, badges coloridos
-   **Create**: Formulário completo para nova conta
-   **Edit**: Edição com recálculo automático de saldo
-   **Show**: Visualização detalhada com lista de movimentos

**Recursos:**

-   Pesquisa por nome, banco ou IBAN
-   Filtros por tipo e estado
-   Saldos coloridos (verde=positivo, vermelho=negativo)
-   Contador de movimentos por conta
-   Paginação (15 registos/página)

#### 🔐 Permissões

**Criadas:**

-   `bank-accounts.create`
-   `bank-accounts.read`
-   `bank-accounts.update`
-   `bank-accounts.delete`

**Atribuição:**

-   Super Admin: Todas
-   Gestor Financeiro: Todas
-   Visualizador: Apenas leitura

#### 📍 Navegação

**Menu Lateral:**

-   Localização: **Financeiro > Contas Bancárias**
-   Ícone: CreditCard
-   Primeiro item do submenu Financeiro

---

### 💰 Módulo de Conta Corrente de Clientes

**Acompanhamento de Débitos, Créditos e Saldos por Cliente**

#### 🎯 Funcionalidades Implementadas

**Gestão de Movimentos:**

-   Registro de débitos (cliente deve) e créditos (cliente pagou)
-   Categorias: Fatura, Pagamento, Nota Crédito/Débito, Juros, Ajuste
-   Cálculo automático e em tempo real de saldos
-   Atualização em cascata de movimentos subsequentes
-   Referência a documentos (nº fatura, recibo)

**Lógica de Saldo:**

-   **Débito**: Aumenta saldo (cliente deve à empresa)
-   **Crédito**: Diminui saldo (cliente pagou)
-   **Saldo > 0**: Cliente em dívida
-   **Saldo < 0**: Crédito a favor do cliente
-   Recálculo automático ao criar/editar/eliminar

#### 🗃️ Base de Dados

**Tabela Criada:**

-   `client_accounts`: Movimentos com saldo calculado, relacionamento com entities

**Campos Principais:**

-   `entity_id`: Cliente (FK para entities)
-   `tipo`: debito/credito
-   `valor`: Valor do movimento
-   `saldo_apos`: Saldo após movimento (calculado)
-   `categoria`: Tipo de operação
-   `referencia`: Nº documento relacionado

**Model:**

-   `ClientAccount.php`: Lógica complexa de cálculo de saldos
    -   `calculateBalance()`: Calcula saldo do movimento
    -   `updateSubsequentBalances()`: Atualiza em cascata
    -   `recalculateBalancesForEntity()`: Recalcula tudo do cliente
    -   `getCurrentBalance()`: Retorna saldo atual
    -   `getEntityStats()`: Estatísticas completas

#### 🎨 Interface

**Painel de Estatísticas:**

-   Total Débitos (vermelho)
-   Total Créditos (verde)
-   Saldo Atual (colorido conforme positivo/negativo)
-   Visível quando cliente selecionado

**Listagem:**

-   Filtros: Cliente, Tipo, Categoria, Período (data início/fim)
-   Pesquisa: Descrição ou referência
-   Colunas separadas para Débito e Crédito
-   Saldo após cada movimento
-   Badges coloridos por categoria
-   Ordenação por data (mais recente primeiro)

**Formulários:**

-   **Create**: Novo movimento (tipo, valor, categoria, referência)
-   **Edit**: Edição com recálculo automático
-   **Show**: Visualização detalhada com sidebar de ações

#### 🔐 Permissões

**Criadas:**

-   `client-accounts.create`
-   `client-accounts.read`
-   `client-accounts.update`
-   `client-accounts.delete`

**Atribuição:**

-   Super Admin: Todas
-   Gestor Financeiro: Todas
-   Visualizador: Apenas leitura

#### 📍 Navegação

**Menu Lateral:**

-   Localização: **Financeiro > Conta Corrente Clientes**
-   Ícone: DollarSign
-   Segundo item do submenu Financeiro

#### 🔧 Lógica Técnica

**Cálculo de Saldos:**

```
Movimento 1 (Débito 500€):  Saldo = 0 + 500 = 500€
Movimento 2 (Crédito 300€): Saldo = 500 - 300 = 200€
Movimento 3 (Débito 150€):  Saldo = 200 + 150 = 350€
```

**Recálculo em Cascata:**

-   Ao editar Movimento 2 de 300€ para 400€:
    -   Movimento 2: 500 - 400 = 100€
    -   Movimento 3: 100 + 150 = 250€ (atualizado automaticamente)

---

### 📚 Documentação

**Novos Documentos:**

-   `docs/bank-accounts-module.md`: Documentação completa do módulo de Contas Bancárias
-   `docs/client-accounts-module.md`: Documentação completa do módulo de Conta Corrente

**Conteúdo:**

-   Estrutura de base de dados
-   Models e relacionamentos
-   Controllers e rotas
-   Interface e componentes
-   Lógica de negócio
-   Permissões e segurança
-   Casos de uso
-   Performance e otimizações
-   Troubleshooting

---

### 🐛 Correções

**Navegação:**

-   Corrigido posicionamento de "Contas Bancárias" no menu (movido para submenu Financeiro)
-   Removida entrada duplicada de banco de dados

**Paginação:**

-   Corrigido erro de `href` null em links de paginação
-   Implementada renderização condicional (Link vs span)

**Compilação:**

-   Todos os componentes Vue compilados com sucesso
-   Assets otimizados (gzip)

---

## [0.10.1] — 2025-11-09

### 💰 Cálculo Automático de Preço com IVA nos Artigos

**Melhoria no Módulo de Artigos para Preço Final com IVA**

#### 🎯 Funcionalidade Implementada

**Campo Preço com IVA:**

-   Novo campo `preco_com_iva` na tabela `articles`
-   Cálculo automático: `preço base × (1 + IVA%/100)`
-   Atualização via model event (boot/saving)
-   Exibição em tempo real nos formulários

#### 🎨 Interface de Artigos

**Formulários (Create e Edit):**

-   Campo "Preço Final (com IVA)" readonly
-   Cálculo dinâmico ao alterar preço base ou taxa IVA
-   Visual destacado (background cinza, valor em negrito)
-   Formato: `12.30€`

#### 🔄 Integração com Encomendas

**Uso nas Encomendas de Clientes:**

-   Ao selecionar artigo, usa `preco_com_iva` em vez de `preco`
-   Preço unitário já inclui IVA aplicado
-   CustomerOrderController atualizado (create e edit)
-   Query alterada: `'preco_com_iva as unit_price'`

#### 🗃️ Base de Dados

**Migration:**

-   `add_preco_com_iva_to_articles_table`
-   Campo: `decimal(10,2)` após `iva_percentagem`
-   Nullable para retrocompatibilidade

**Model Article:**

-   Adicionado ao `$fillable` e `$casts`
-   Boot event para cálculo automático no save
-   Accessor `getPrecoComIvaFormatadoAttribute()`

#### 📊 Migração de Dados

**Seeder:**

-   `UpdateArticlesPriceSeeder` - Atualiza artigos existentes
-   Executa `save()` em todos os artigos (trigger boot event)
-   7 artigos atualizados com sucesso

#### 🔧 Alterações Técnicas

**Ficheiros Modificados:**

-   `database/migrations/2025_11_09_203614_add_preco_com_iva_to_articles_table.php`
-   `app/Models/Article.php` - Boot event e accessor
-   `app/Http/Controllers/CustomerOrderController.php` - Queries nos métodos create() e edit()
-   `resources/js/Pages/Articles/Create.vue` - Campo calculado
-   `resources/js/Pages/Articles/Edit.vue` - Campo calculado

**Computed Property (Vue):**

```javascript
const precoComIva = computed(() => {
    const preco = parseFloat(form.preco) || 0;
    const iva = parseFloat(form.iva_percentagem) || 0;
    return preco * (1 + iva / 100);
});
```

---

## [0.10.0] — 2025-11-09

### 📦 Módulo de Encomendas (Clientes e Fornecedores)

**Sistema Completo de Gestão de Encomendas com Conversão Automática**

#### 🎯 Funcionalidades Principais

**Encomendas - Clientes:**

-   CRUD completo de encomendas de clientes
-   Numeração automática: EC-YYYY-#### (Ex: EC-2025-0001)
-   Gestão de artigos por encomenda com fornecedores associados
-   Estados: Rascunho, Fechado
-   Conversão automática para encomendas de fornecedores
-   Cálculo automático de totais

**Encomendas - Fornecedores:**

-   CRUD completo de encomendas a fornecedores
-   Numeração automática: EF-YYYY-#### (Ex: EF-2025-0001)
-   Estados: Rascunho, Enviado, Confirmado, Recebido, Cancelado
-   Rastreamento de origem (customer_order_id)
-   Gestão de artigos e quantidades
-   Paginação (15 registos por página)

#### ✨ Conversão Inteligente

**Processo de Conversão:**

-   Botão "Converter para Encomendas Fornecedor" (apenas quando status = fechado)
-   Agrupa itens por fornecedor automaticamente
-   Cria uma encomenda separada para cada fornecedor
-   Mantém rastreabilidade com encomenda de origem
-   Mensagem de sucesso com números criados

#### 🗃️ Base de Dados

**Tabelas Criadas:**

-   `customer_orders` - Encomendas de clientes
-   `customer_order_items` - Itens das encomendas de clientes
-   `supplier_orders` - Encomendas a fornecedores
-   `supplier_order_items` - Itens das encomendas a fornecedores

**Funcionalidades:**

-   Soft deletes em todas as tabelas
-   Auto-cálculo de totais via events
-   Numeração única com prevenção de duplicados (withTrashed)
-   Relações completas entre entidades

#### 🔐 Permissões

**Novas Permissões:**

-   `customer-orders.create|read|update|delete`
-   `supplier-orders.create|update|update|delete`

**Auto-atribuição:**

-   Todos os roles com `orders.*` recebem automaticamente ambos os conjuntos
-   5 roles configurados: Super Admin, Administrador, Gestor Comercial, Gestor Financeiro, Visualizador

#### 🎨 Interface (Vue 3 + Inertia.js)

**Encomendas - Clientes:**

-   Ícone: ShoppingCart (azul)
-   Listagem com filtros de pesquisa e estado
-   Formulários de criação/edição com validação
-   Auto-preenchimento de preços ao selecionar artigo
-   Quantidade: incremento de 1 em 1
-   Botão de conversão em encomendas fechadas

**Encomendas - Fornecedores:**

-   Ícone: Truck (verde)
-   Paginação com tratamento null-safe de links
-   Badges coloridos por estado
-   Filtros de pesquisa e estado
-   Formulários completos de gestão

#### 🐛 Correções Implementadas

1. **Numeração Duplicada**

    - Adicionado `withTrashed()` aos métodos `generateNumber()`
    - Previne duplicados mesmo com soft deletes

2. **Queries de Entities**

    - Corrigido uso de `is_customer`/`is_supplier` para `type` enum
    - Queries: `whereIn('type', ['client', 'both'])` e `whereIn('type', ['supplier', 'both'])`

3. **Colunas de Articles**

    - Mapeamento de colunas portuguesas: `nome as name`, `preco as unit_price`, `referencia as reference`
    - Uso do scope `ativos()` para artigos ativos

4. **Validação de Quantidade**

    - Backend: `min:1` (inteiros)
    - Frontend: `step="1" min="1"`

5. **Paginação Null-Safe**
    - Tratamento de links com `href=null` (Previous/Next desabilitados)
    - Conditional rendering: `<Link v-if="link.url">` / `<span v-else>`

#### 📋 Rotas Adicionadas

```php
// Encomendas - Clientes
/customer-orders (index, create, store, edit, update, destroy)
/customer-orders/{id}/convert-to-supplier-orders (convert)
/customer-orders/{id}/pdf (generatePDF - TODO)

// Encomendas - Fornecedores
/supplier-orders (index, create, store, edit, update, destroy)
/supplier-orders/{id}/pdf (generatePDF - TODO)
```

#### 🧪 Seeders

-   `CustomerOrdersPermissionsSeeder` - Cria e atribui permissões
-   `SupplierOrdersPermissionsSeeder` - Cria e atribui permissões

#### 📚 Documentação

-   Criado `docs/orders-module.md` com documentação completa:
    -   Estrutura de base de dados
    -   Models e relações
    -   Controllers e métodos
    -   Rotas e permissões
    -   Fluxo de conversão
    -   Correções implementadas
    -   Melhorias futuras

#### 🔄 Menu Sidebar

**Adicionado em "Gestão de Vendas":**

-   Encomendas - Clientes (ShoppingCart, azul)
-   Encomendas - Fornecedores (Truck, verde)
-   Ordens de Trabalho (Briefcase, desabilitado)

#### ⚙️ Configurações

**Validações:**

-   Cliente/Fornecedor obrigatório
-   Mínimo 1 item por encomenda
-   Quantidade mínima: 1
-   Preço unitário obrigatório

**Auto-preenchimento:**

-   Preço unitário ao selecionar artigo
-   Total da linha ao alterar quantidade/preço
-   Total geral da encomenda

---

## [0.9.1] — 2025-11-09

### 🎨 Uniformização de Interface - Headers e Breadcrumbs

**Padronização da Experiência do Utilizador em Todos os Módulos**

#### 🎯 Objetivo

Garantir consistência visual e de navegação em todas as páginas de índice dos módulos, facilitando a orientação do utilizador e melhorando a usabilidade geral da aplicação.

#### ✨ Alterações Implementadas

**Padrão de Header Uniformizado:**

Todas as páginas de índice agora seguem o mesmo layout:

1. **Cabeçalho com Ícone**

    - Ícone temático dentro de círculo colorido (diferente por módulo)
    - Título principal em H1
    - Subtítulo descritivo

2. **Breadcrumbs de Navegação**

    - Caminho completo: Dashboard / [Categoria] / Módulo Atual
    - Links clicáveis para navegação rápida
    - Último elemento (página atual) sem link

3. **Estrutura Simplificada**
    - Removido template `#header` antigo
    - Removidas divs wrapper desnecessárias (`py-12`, `max-w-7xl mx-auto`)
    - Layout direto no `AuthenticatedLayout`

#### 📂 Módulos Atualizados

**11 Módulos Padronizados:**

1. **Contactos** - Laranja (`bg-orange-100`, `text-orange-600`)
    - Breadcrumb: Dashboard / Contactos
2. **Fornecedores** - Verde (`bg-green-100`, `text-green-600`)
    - Breadcrumb: Dashboard / Fornecedores
3. **Artigos** - Azul (`bg-blue-100`, `text-blue-600`)
    - Breadcrumb: Dashboard / Artigos
4. **Países** - Índigo (`bg-indigo-100`, `text-indigo-600`)
    - Breadcrumb: Dashboard / Configurações / Países
5. **Funções de Contacto** - Roxo (`bg-purple-100`, `text-purple-600`)
    - Breadcrumb: Dashboard / Configurações / Funções de Contactos
6. **Taxas IVA** - Verde (`bg-green-100`, `text-green-600`)
    - Breadcrumb: Dashboard / Configurações / Taxas de IVA
7. **Utilizadores** - Âmbar (`bg-amber-100`, `text-amber-600`)
    - Breadcrumb: Dashboard / Gestão de Acessos / Utilizadores
8. **Grupos de Permissões** - Vermelho (`bg-red-100`, `text-red-600`)
    - Breadcrumb: Dashboard / Gestão de Acessos / Grupos de Permissões
9. **Logs de Atividade** - Roxo (`bg-purple-100`, `text-purple-600`)
    - Breadcrumb: Dashboard / Gestão de Acessos / Logs de Atividade
10. **Empresa** - Azul (`bg-blue-100`, `text-blue-600`)

    - Breadcrumb: Dashboard / Configurações / Empresa

11. **Clientes** - Azul (já estava padronizado - serviu de referência)
    - Breadcrumb: Dashboard / Clientes

#### 💡 Benefícios

-   ✅ **Consistência Visual**: Mesma aparência em todos os módulos
-   ✅ **Navegação Melhorada**: Breadcrumbs facilitam orientação
-   ✅ **Identidade por Módulo**: Cores distintas ajudam identificação rápida
-   ✅ **Código Limpo**: Estrutura HTML mais simples e mantível
-   ✅ **Acessibilidade**: Hierarquia clara de headings e navegação

#### 🔧 Correções Técnicas

-   Corrigida tag `<label` duplicada em `Company/Edit.vue`
-   Removidas divs extras em `Countries/Index.vue`
-   Ajustada indentação em todos os ficheiros modificados

---

## [0.9.0] — 2025-11-09

### 🏢 Módulo Configurações - Empresa

**Gestão Centralizada dos Dados da Empresa**

#### 🎯 Objetivo

Permitir que o utilizador personalize os dados da empresa que aparecem em toda a aplicação (login, welcome page, sidebar, documentos PDF, etc.).

#### ✨ Funcionalidades Implementadas

**Campos Configuráveis:**

-   **Logotipo**: Upload de imagem (PNG, JPG, GIF - máx 2MB)
-   **Nome da Empresa**: Texto livre (aparece em documentos e interface)
-   **NIF**: 9 dígitos (Número de Identificação Fiscal)
-   **Morada**: Endereço completo
-   **Código Postal**: Formato português
-   **Localidade**: Cidade/Vila

**Características Técnicas:**

-   **Singleton Pattern**: Apenas 1 registo de empresa no sistema
-   **Upload de Logo**: Armazenamento em `storage/app/public/company/logos`
-   **Validação**: NIF com 9 dígitos, logo até 2MB
-   **Preview em Tempo Real**: Visualização do logo durante upload
-   **Flash Messages**: Confirmação de sucesso após guardar

**Integração Visual:**

-   **Login Page (GuestLayout)**: Logo grande (160px altura) + nome da empresa
-   **Welcome Page**: Logo médio (80px altura) + nome da empresa + "Sistema Empresarial powered by Inovcorp"
-   **Sidebar (Mobile + Desktop)**: Logo pequeno (48px altura) + nome da empresa + "Sistema Empresarial powered by Inovcorp"
-   **Fallback**: Ícone Building2 quando não há logo configurado

#### 🔐 Permissões

**2 Permissões Específicas** (não segue padrão CRUD por ser singleton):

-   `company.read` - Ver configurações da empresa
-   `company.update` - Editar configurações da empresa

**Distribuição por Grupos:**

-   **Super Admin / Administrador**: read + update (gestão completa)
-   **Todos os outros grupos**: apenas read (visualização)

#### 📂 Estrutura de Ficheiros

**Backend:**

-   `app/Models/Company.php` - Model Eloquent com método `getInstance()`
-   `app/Http/Controllers/CompanyController.php` - Edit e Update com upload
-   `app/Http/Middleware/HandleInertiaRequests.php` - Partilha dados da empresa globalmente
-   `database/migrations/2025_11_09_000001_create_companies_table.php`
-   `database/seeders/CompanySeeder.php` - Dados iniciais
-   `database/seeders/AddCompanyPermissionsSeeder.php` - Permissões

**Frontend:**

-   `resources/js/Pages/Company/Edit.vue` - Formulário completo com upload
-   `resources/js/Layouts/GuestLayout.vue` - Integração do logo na página de login
-   `resources/js/Pages/Welcome.vue` - Integração do logo na página inicial
-   `resources/js/Layouts/AuthenticatedLayout.vue` - Integração do logo na sidebar
-   Menu: **Configurações → Empresa** (item adicionado ao submenu)

**Routes:**

```php
Route::get('/company/settings', [CompanyController::class, 'edit'])->name('company.edit');
Route::patch('/company/settings', [CompanyController::class, 'update'])->name('company.update');
```

#### 📍 Onde São Utilizados os Dados

-   **Logotipo**:
    -   Página de login (160px altura)
    -   Welcome page (80px altura)
    -   Sidebar da aplicação (48px altura)
    -   Futuramente: PDFs, relatórios
-   **Nome da Empresa**: Aparece em todas as páginas junto ao logo
-   **Nome + NIF**: Faturas, propostas, orçamentos (implementação futura)
-   **Morada Completa**: Rodapé de documentos oficiais (implementação futura)

#### 💡 Como Usar

1. Aceder a **Configurações → Empresa** no menu lateral
2. Fazer upload do logotipo (opcional - PNG, JPG, GIF até 2MB)
3. Preencher dados da empresa (nome, NIF, morada, código postal, localidade)
4. Clicar **Guardar Alterações**
5. O logo aparecerá automaticamente:
    - Na página de login
    - Na welcome page (com texto "Sistema Empresarial powered by Inovcorp")
    - Na sidebar da aplicação (desktop e mobile)

#### 🔧 Comandos de Instalação

```bash
php artisan migrate
php artisan db:seed --class=CompanySeeder
php artisan db:seed --class=AddCompanyPermissionsSeeder
php artisan storage:link  # Criar link simbólico para storage público
```

#### 🎨 Especificações Visuais

**Tamanhos do Logo:**

-   **Login Page**: 160px altura (h-40), largura máxima adaptável
-   **Welcome Page**: 80px altura (h-20), largura máxima 280px
-   **Sidebar**: 48px altura (h-12), largura máxima 180px

**Texto Acompanhante:**

-   Nome da empresa sempre visível
-   Subtítulo: "Sistema Empresarial powered by Inovcorp"

---

## [0.8.5] — 2025-11-09

### 🔐 Sistema de Visibilidade de Botões Baseado em Permissões

**Implementação de Controlo Granular de UI por Permissões**

#### 🎯 Objetivo

Implementar um sistema genérico onde os botões de ação (Criar, Editar, Eliminar) só aparecem se o utilizador tiver a permissão correspondente. Anteriormente, os botões apareciam sempre e devolviam erro 403 quando clicados por utilizadores sem permissão.

#### ✨ Funcionalidades Implementadas

**Backend (Controllers)**

-   Todos os controllers agora enviam objeto `can` com verificação real de permissões:

```php
'can' => [
    'create' => $request->user()->can('module.create'),
    'view' => $request->user()->can('module.read'),
    'edit' => $request->user()->can('module.update'),
    'delete' => $request->user()->can('module.delete'),
]
```

**Frontend (Vue Components)**

-   Botões usam diretiva `v-if` para renderização condicional baseada em permissões:

```vue
<Button v-if="can.create">Criar</Button>
<Button v-if="can.edit">Editar</Button>
<Button v-if="can.delete">Eliminar</Button>
```

#### 📋 Módulos Atualizados

**Controllers Modificados:**

1. `EntityController.php` - Clientes/Fornecedores (com lógica dinâmica de prefixo)
2. `ArticleController.php` - Artigos
3. `ContactController.php` - Contactos
4. `VatRateController.php` - Taxas de IVA
5. `CountryController.php` - Países
6. `ContactFunctionController.php` - Funções de Contactos
7. `RoleController.php` - Grupos de Permissões
8. `UserManagementController.php` - Utilizadores

**Componentes Vue Modificados:**

1. `EntitiesDataTableNew.vue` - Tabela de Clientes/Fornecedores
    - Props: `canCreate`, `canView`, `canEdit`, `canDelete`
2. `ContactsDataTableNew.vue` - Tabela de Contactos
    - Props: `canCreate`, `canView`, `canEdit`, `canDelete`

**Páginas Index.vue Atualizadas:**

1. `Clients/Index.vue` - Passa props de permissões
2. `Suppliers/Index.vue` - Passa props de permissões
3. `Contacts/Index.vue` - Passa props de permissões
4. `Articles/Index.vue` - Usa objeto `can` em vez de `hasPermission()`
5. `Countries/Index.vue` - Usa objeto `can` em vez de `hasPermission()`
6. `ContactFunctions/Index.vue` - Usa objeto `can` em vez de `hasPermission()`
7. `VatRates/Index.vue` - Usa objeto `can` em vez de `hasPermission()`
8. `Roles/Index.vue` - Usa objeto `can` em vez de `hasPermission()`
9. `Users/Index.vue` - Usa objeto `can` em vez de `hasPermission()`

#### 🎯 Comportamento por Tipo de Utilizador

**Exemplo: Utilizador "Visualizador" (apenas permissões `.read`)**

-   ✅ Vê todas as listas de dados
-   ❌ NÃO vê botão "Criar"
-   ❌ NÃO vê botão "Editar"
-   ❌ NÃO vê botão "Eliminar"
-   ✅ Nunca recebe erro 403 (botões simplesmente não existem)

**Exemplo: Utilizador "Gestor Financeiro"**

-   ✅ Vê listas: Clientes, Fornecedores, Taxas IVA
-   ✅ Pode visualizar detalhes
-   ❌ NÃO vê botões de criação/edição/eliminação
-   ❌ Não tem acesso a módulos sem permissão

#### 🔧 Padrão de Implementação

**1. Controller envia permissões:**

```php
return Inertia::render('Module/Index', [
    'data' => $data,
    'can' => [
        'create' => $request->user()->can('module.create'),
        'view' => $request->user()->can('module.read'),
        'edit' => $request->user()->can('module.update'),
        'delete' => $request->user()->can('module.delete'),
    ]
]);
```

**2. View recebe como prop:**

```vue
const props = defineProps({ data: Object, can: { type: Object, default: () => ({
create: false, view: true, edit: false, delete: false, }), }, });
```

**3. Componentes usam v-if:**

```vue
<Link v-if="can.create" :href="route('module.create')">
    <Button>Novo</Button>
</Link>
<Button v-if="can.edit" @click="edit(row)">Editar</Button>
<Button v-if="can.delete" @click="destroy(row)">Eliminar</Button>
```

#### ✅ Benefícios

1. **Segurança Aprimorada**: Utilizadores nunca vêem opções que não podem usar
2. **UX Melhorada**: Sem erros 403 confusos - interface limpa e clara
3. **Sistema Genérico**: Funciona automaticamente para qualquer grupo criado
4. **Manutenção Simples**: Permissões geridas centralmente via Spatie Permission
5. **Performance**: Botões não renderizados = menos HTML no DOM

#### 📦 Arquivos Modificados

**Backend:**

-   `app/Http/Controllers/EntityController.php`
-   `app/Http/Controllers/ArticleController.php`
-   `app/Http/Controllers/ContactController.php`
-   `app/Http/Controllers/VatRateController.php`
-   `app/Http/Controllers/CountryController.php`
-   `app/Http/Controllers/ContactFunctionController.php`
-   `app/Http/Controllers/RoleController.php`
-   `app/Http/Controllers/UserManagementController.php`

**Frontend:**

-   `resources/js/Components/EntitiesDataTableNew.vue`
-   `resources/js/Components/ContactsDataTableNew.vue`
-   `resources/js/Pages/Clients/Index.vue`
-   `resources/js/Pages/Suppliers/Index.vue`
-   `resources/js/Pages/Contacts/Index.vue`
-   `resources/js/Pages/Articles/Index.vue`
-   `resources/js/Pages/Countries/Index.vue`
-   `resources/js/Pages/ContactFunctions/Index.vue`
-   `resources/js/Pages/VatRates/Index.vue`
-   `resources/js/Pages/Roles/Index.vue`
-   `resources/js/Pages/Users/Index.vue`

#### 🧪 Testes Recomendados

1. Login como cada grupo de utilizador
2. Verificar quais botões aparecem em cada módulo
3. Confirmar que correspondem às permissões do grupo
4. Verificar que não há erros 403 ao navegar normalmente

---

## [0.8.4] — 2025-11-09

### 📦 Adição de Novos Módulos ao Sistema de Permissões

**Novos Módulos Adicionados**

1. **Calendário** (`calendar`)
    - 4 permissões CRUD (create, read, update, delete)
2. **Ordens de Trabalho** (`work-orders`)
    - 4 permissões CRUD (create, read, update, delete)
3. **Arquivo Digital** (`digital-archive`)
    - 4 permissões CRUD (create, read, update, delete)
4. **Logs** (`logs`)
    - 4 permissões CRUD (create, read, update, delete)

**Distribuição de Permissões por Grupo**

-   ✅ **Super Admin**: Todas as 64 permissões (16 módulos × 4 ações)
-   ✅ **Administrador**: 56 permissões (inclui todos os novos módulos)
-   ✅ **Gestor Comercial**: 22 permissões
    -   Calendário: apenas leitura
    -   Ordens de Trabalho: CRUD completo
-   ✅ **Gestor Financeiro**: 11 permissões (sem novos módulos)
-   ✅ **Editor**: 10 permissões
    -   Arquivo Digital: CRUD completo
-   ✅ **Visualizador**: 16 permissões (apenas leitura em todos os módulos)

**Arquivos Criados**

-   `database/seeders/AddNewModulesPermissionsSeeder.php`

**Arquivos Modificados**

-   `database/seeders/UpdateRolesSeeder.php` - Adicionadas permissões aos grupos
-   `database/seeders/RoleAndPermissionSeeder.php` - Incluídos novos módulos

**Comandos Executados**

```bash
# Criar permissões dos novos módulos
php artisan db:seed --class=AddNewModulesPermissionsSeeder

# Atualizar grupos com novas permissões
php artisan db:seed --class=UpdateRolesSeeder
```

**Estatísticas Finais**

-   Total de Permissões: 64 (antes: 48)
-   Total de Módulos: 16 (antes: 12)
-   Novos módulos: 4 (calendar, work-orders, digital-archive, logs)

---

## [0.8.3] — 2025-11-09

### 🔧 Correção de Formulários e Reorganização do Sistema de Permissões

**Problemas Corrigidos**

1. **Erro 405 ao Editar Utilizadores e Grupos**

    - Formulários Vue usavam `form.put()` mas rotas Laravel esperavam `PATCH`
    - Correção aplicada em 5 formulários de edição

2. **Sistema de Permissões Desorganizado**

    - Utilizadores tinham permissões diretas em vez de grupos
    - Grupos não estavam atribuídos aos utilizadores
    - Confusão sobre como funcionava o sistema de permissões

3. **Campo 'active' não aparecia na tabela de Permissões**
    - Controller não enviava o campo 'active' para o Vue
    - Correção no `RoleController::index()`

**Soluções Implementadas**

**Frontend - Correção de Formulários**

-   ✅ Alterado `form.put()` para `form.patch()` em:
    -   `resources/js/Pages/Users/Edit.vue`
    -   `resources/js/Pages/Roles/Edit.vue`
    -   `resources/js/Pages/VatRates/Edit.vue`
    -   `resources/js/Pages/ContactFunctions/Edit.vue`
    -   `resources/js/Pages/Contacts/Edit.vue`

**Backend - Reorganização de Grupos**

-   ✅ **UpdateRolesSeeder**: Novo seeder que cria 6 grupos específicos

    -   👑 Super Admin (48 permissões → 64) - Acesso total
    -   🔧 Administrador (40 permissões → 56) - Tudo exceto users/roles
    -   💼 Gestor Comercial (17 permissões → 22) - Clientes, Fornecedores, Contactos, Propostas, Ordens de Trabalho
    -   💰 Gestor Financeiro (11 permissões) - Financeiro, Encomendas, Taxas IVA
    -   ✏️ Editor (6 permissões → 10) - Artigos, configurações básicas e Arquivo Digital
    -   👁️ Visualizador (12 permissões → 16) - Apenas leitura em tudo

-   ✅ **TestUsersSeeder Atualizado**: Agora atribui grupos aos utilizadores

    -   Removidas todas as permissões diretas
    -   Todos os 7 utilizadores têm grupos atribuídos
    -   Permissões geridas APENAS através dos grupos

-   ✅ **RoleController**: Adicionado campo 'active' no método index()

**Estrutura Final**

-   ✅ 6 grupos ativos com permissões bem definidas
-   ✅ 7 utilizadores todos com grupos atribuídos
-   ✅ 0 utilizadores com permissões diretas
-   ✅ Sistema funcionando corretamente

**Arquivos Criados**

-   `database/seeders/UpdateRolesSeeder.php`
-   `database/seeders/AddNewModulesPermissionsSeeder.php`
-   `docs/fix-access-management.md` (documentação completa)

**Arquivos Modificados**

-   `database/seeders/TestUsersSeeder.php`
-   `database/seeders/RoleAndPermissionSeeder.php`
-   `app/Http/Controllers/RoleController.php`
-   5 formulários Edit.vue (Users, Roles, VatRates, ContactFunctions, Contacts)

**Comandos para Aplicar**

```bash
# Criar permissões dos novos módulos
php artisan db:seed --class=AddNewModulesPermissionsSeeder

# Atualizar grupos
php artisan db:seed --class=UpdateRolesSeeder

# Atribuir grupos aos utilizadores
php artisan db:seed --class=TestUsersSeeder
```

---

## [0.8.2] — 2025-11-08

### 🔒 Sistema de Permissões - Implementação Completa e Correções

**Problema Identificado**

-   Permissões não bloqueavam acesso real aos módulos
-   Sidebar mostrava todos os menus independentemente das permissões do utilizador
-   Duplicação de permissões na base de dados (96 em vez de 48)
-   Nomenclatura inconsistente (view/edit vs read/update)
-   Middleware de permissões criado mas não aplicado nas rotas

**Soluções Implementadas**

**Backend - Middleware e Rotas**

-   ✅ **CheckPermission Middleware**: Criado middleware para verificar permissões

    -   Valida se user está autenticado
    -   Verifica permissão específica com `$user->can($permission)`
    -   Retorna 403 se não tiver permissão
    -   Registrado em `bootstrap/app.php` com alias `permission`

-   ✅ **Rotas Protegidas**: Aplicado middleware em todas as rotas
    -   `clients.*` → `permission:clients.{create|read|update|delete}`
    -   `suppliers.*` → `permission:suppliers.{create|read|update|delete}`
    -   `contacts.*` → `permission:contacts.{create|read|update|delete}`
    -   `articles.*` → `permission:articles.{create|read|update|delete}`
    -   `countries.*` → `permission:countries.{create|read|update|delete}`
    -   `contact-functions.*` → `permission:contact-functions.{create|read|update|delete}`
    -   `vat-rates.*` → `permission:vat-rates.{create|read|update|delete}`
    -   `users.*` → `permission:users.{create|read|update|delete}`
    -   `roles.*` → `permission:roles.{create|read|update|delete}`

**Backend - Limpeza Permissões**

-   ✅ **CleanAndResetPermissionsSeeder**: Novo seeder para limpeza completa

    -   Remove TODAS as permissões e roles antigas
    -   Recria exatamente 48 permissões (12 módulos × 4 ações)
    -   Nomenclatura padronizada: `create`, `read`, `update`, `delete`
    -   Estrutura limpa sem duplicações

-   ✅ **Estrutura Final de Permissões**:

    ```
    📊 12 Módulos × 4 Ações = 48 Permissões
    - clients: create, read, update, delete
    - suppliers: create, read, update, delete
    - contacts: create, read, update, delete
    - articles: create, read, update, delete
    - proposals: create, read, update, delete
    - orders: create, read, update, delete
    - financial: create, read, update, delete
    - users: create, read, update, delete
    - roles: create, read, update, delete
    - countries: create, read, update, delete
    - contact-functions: create, read, update, delete
    - vat-rates: create, read, update, delete
    ```

-   ✅ **Distribuição por Role**:
    -   **Super Admin**: 48 permissões (100%)
    -   **Administrador**: 40 permissões (sem users e roles)
    -   **Gestor**: 20 permissões (operacionais, sem delete)
    -   **Utilizador**: 12 permissões (apenas read)

**Frontend - Middleware e Compartilhamento**

-   ✅ **HandleInertiaRequests**: Atualizado para compartilhar permissões
    -   Antes: Apenas `auth.user`
    -   Depois: `auth.user` + `auth.permissions` (array de nomes)
    -   Exemplo: `['clients.create', 'clients.read', 'articles.update']`

**Frontend - AuthenticatedLayout.vue**

-   ✅ **Helper Functions**:

    ```javascript
    // Armazena permissões do user logado
    const permissions = computed(() => {
        const perms = page.props.auth?.permissions;
        return Array.isArray(perms) ? perms : [];
    });

    // Verifica permissão específica
    const hasPermission = (permission) => {
        if (!permission || !Array.isArray(permissions.value)) return false;
        return permissions.value.includes(permission);
    };

    // Verifica se tem qualquer permissão de um módulo
    const hasAnyPermission = (module) => {
        if (!module || !Array.isArray(permissions.value)) return false;
        return ["create", "read", "update", "delete"].some((action) =>
            hasPermission(`${module}.${action}`)
        );
    };

    // Verifica se rota está ativa
    const isActive = (routeName) => {
        return route().current(routeName) || route().current(routeName + ".*");
    };
    ```

-   ✅ **Navegação Filtrada**: Todos os arrays de menu convertidos para `computed`

    ```javascript
    // Antes: array estático
    const mainNavigationItems = [...];

    // Depois: computed com filtro
    const mainNavigationItems = computed(() => {
        return allMainNavigationItems.filter((item) => {
            if (!item.permission) return true; // Sem permissão = sempre visível
            return hasAnyPermission(item.permission);
        });
    });
    ```

-   ✅ **Menus Protegidos**:

    -   `mainNavigationItems` (Dashboard, Clientes, Fornecedores, Contactos, Propostas, Calendário)
    -   `ordersNavigationItems` (Encomendas)
    -   `financialNavigationItems` (Financeiro)
    -   `accessManagementItems` (Utilizadores, Permissões)
    -   `configurationItems` (Países, Funções, Artigos, IVA, Logs)

-   ✅ **Seções Ocultas**: Adicionado `v-if` para ocultar seções completas
    ```vue
    <!-- Só mostra seção se user tiver pelo menos 1 permissão -->
    <li v-if="ordersNavigationItems.length > 0">
        <!-- Encomendas -->
    </li>
    <li v-if="financialNavigationItems.length > 0">
        <!-- Financeiro -->
    </li>
    <li v-if="accessManagementItems.length > 0">
        <!-- Gestão de Acessos -->
    </li>
    <li v-if="configurationItems.length > 0">
        <!-- Configurações -->
    </li>
    ```

**Frontend - Página de Erro 403**

-   ✅ **resources/js/Pages/Errors/403.vue**: Criada página personalizada
    -   Design moderno com ícone de aviso
    -   Mensagem clara: "Não tem permissão para aceder a este recurso"
    -   Botões: Voltar ao Dashboard | Voltar à Página Anterior
    -   Responsive e com dark mode

**Frontend - Controlo Visibilidade Botões (UX Melhorada)**

-   ✅ **hasPermission() Global**: Função `inject` disponível em todos os componentes

    -   Exportada via `provide("hasPermission", hasPermission)` no AuthenticatedLayout
    -   Permite verificar permissões específicas (ex: `hasPermission('articles.create')`)
    -   Reutilizável em qualquer componente filho

-   ✅ **Botões Condicionais**: Aplicado `v-if` baseado em permissões

    -   **Botão "Criar/Adicionar"**: `v-if="hasPermission('module.create')"`
        -   Articles, Countries, ContactFunctions, VatRates, Users, Roles
    -   **Botão "Editar"**: `v-if="hasPermission('module.update')"`
        -   Todos os botões de edição nas tabelas
    -   **Botão "Eliminar"**: `v-if="hasPermission('module.delete')"`
        -   Todos os botões de eliminação nas tabelas

-   ✅ **Benefícios UX**:
    -   **Antes**: Botão visível → Clique → Erro 403 (má experiência)
    -   **Depois**: Botão oculto → Zero frustração do utilizador
    -   Interface limpa e sem elementos não funcionais
    -   Comunicação clara: "Se vejo, posso usar"

**Correções de Bugs**

-   ✅ **Links Não Funcionavam**: Removido propriedade `current: false` dos arrays

    -   Propriedade causava conflito com computed properties
    -   Substituído por função `isActive(item.href)` dinâmica no template

-   ✅ **Vite Manifest Error**: Executado `npm run build`

    -   Recompilou todos os assets
    -   Criou novo manifest com todos os componentes Vue
    -   Users/Index.vue agora encontrado corretamente

-   ✅ **`.forEach()` em Computed**: Removido código que tentava mutar computeds
    -   Erro: `mainNavigationItems.forEach is not a function`
    -   Solução: Usar `isActive()` diretamente no template em vez de modificar arrays

**Fluxo de Proteção Completo**

1. **User Faz Login**:

    - Laravel autentica user
    - `HandleInertiaRequests` carrega permissões via `getAllPermissions()`
    - Frontend recebe `auth.permissions` array

2. **Sidebar é Renderizada**:

    - Cada menu verifica `hasAnyPermission(module)`
    - Menus sem permissão não aparecem
    - Seções vazias são ocultadas

3. **User Clica em Menu**:

    - Inertia.js faz request para rota
    - Middleware `CheckPermission` verifica permissão
    - Se não tiver: retorna 403 com página de erro
    - Se tiver: Controller processa normalmente

4. **User Tenta URL Direto**:
    - Mesmo sem link visível, middleware bloqueia
    - Retorna 403 Forbidden
    - Previne acesso não autorizado

**Impacto**

-   ✅ **Segurança Real**: Permissões agora bloqueiam acesso efetivamente
-   ✅ **Frontend Limpo**: Users só veem o que podem acessar
-   ✅ **Backend Protegido**: Rotas verificam permissões antes de executar
-   ✅ **UX Melhorada**: Mensagens de erro claras quando acesso negado
-   ✅ **Consistência**: Nomenclatura padronizada em todo o sistema
-   ✅ **Performance**: Permissões cached pelo Spatie Permission
-   ✅ **Manutenibilidade**: Sistema organizado e documentado

**Decisões Técnicas**

-   ✅ Middleware aplicado por rota individual (mais granular que por grupo)
-   ✅ Permissões compartilhadas via Inertia (evita requests adicionais)
-   ✅ Computed properties para reatividade automática
-   ✅ Validação dupla: frontend (UX) + backend (segurança)
-   ✅ Logs sempre visível (não requer permissão específica)
-   ✅ **UX First**: Botões ocultos em vez de erro 403 (melhor experiência)
-   ✅ **Provide/Inject**: hasPermission() disponível globalmente via Vue composition API

---

## [0.8.1] — 2025-11-06

### 🔐 Sistema de Permissões Granulares

**Problema Identificado**

-   Sistema anterior usava toggle único por módulo (ativava/desativava todas as 4 permissões)
-   Impossível dar apenas permissões de leitura ou criar roles com acesso limitado
-   UX não intuitiva para gestão granular de acessos

**Solução Implementada**

**Frontend - Roles/Create.vue e Roles/Edit.vue**

-   ✅ **4 Checkboxes Individuais** por menu em vez de 1 toggle geral
-   ✅ **Labels Traduzidas**: Criar, Visualizar, Editar, Eliminar
-   ✅ **Color Coding** para identificação rápida:
    -   🟢 Criar (verde): `text-green-600 dark:text-green-400`
    -   🔵 Visualizar (azul): `text-blue-600 dark:text-blue-400`
    -   🟡 Editar (amarelo): `text-yellow-600 dark:text-yellow-400`
    -   🔴 Eliminar (vermelho): `text-red-600 dark:text-red-400`
-   ✅ **Grid Responsivo**: 2 colunas mobile, 4 colunas desktop
-   ✅ **Toggle Individual**: Método `togglePermission(permissionName)` substitui `toggleModule()`
-   ✅ **Organização Sidebar**: Permissões ordenadas conforme ordem do menu lateral
-   ✅ **Identificação Submenus**: Exibe grupo de origem (ex: "Países (Configurações → Entidades)")

**Backend - RoleController**

-   ✅ **Filtro de Ações**: Apenas create, read, update, delete (4 permissões por módulo)
-   ✅ **Ordem Consistente**: Permissões sempre na mesma ordem (Criar → Visualizar → Editar → Eliminar)
-   ✅ **Ordenação Inteligente**: Módulos ordenados conforme estrutura da sidebar:
    1. Menus Principais (Clientes, Fornecedores, Contactos, Propostas)
    2. Submenu Encomendas
    3. Submenu Financeiro
    4. Submenu Gestão de Acessos (Utilizadores, Permissões)
    5. Submenu Configurações (Países, Funções Contacto, Artigos, Taxas IVA)
-   ✅ **Metadata de Grupos**: Cada módulo identifica seu grupo pai
    -   Ex: `'countries'` → `{name: 'Países', group: 'Configurações → Entidades', order: 40}`
    -   Ex: `'users'` → `{name: 'Utilizadores', group: 'Gestão de Acessos', order: 30}`

**Métodos Atualizados**

```javascript
// Antes (módulo completo)
toggleModule(module); // Ativava/desativava todas as 4 permissões

// Depois (permissão individual)
togglePermission(permissionName); // Ativa/desativa 1 permissão específica
isPermissionActive(permissionName); // Verifica se permissão está ativa
getPermissionLabel(action); // Retorna label PT (Criar, Visualizar, etc.)
getActionColor(action); // Retorna classe Tailwind para cor
```

**Template Atualizado**

```vue
<!-- Antes -->
<Checkbox :checked="isModuleActive(module)" @update:checked="toggleModule(module)" />
<span>{{ module.name }}</span>
<span>Create, Read, Update, Delete</span>

<!-- Depois -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    <div v-for="(permission, action) in module.permissions">
        <Checkbox :checked="isPermissionActive(permission.name)"
                  @update:checked="togglePermission(permission.name)" />
        <label :class="getActionColor(action)">
            {{ getPermissionLabel(action) }}
        </label>
    </div>
</div>
<!-- Identificação do Submenu -->
<span v-if="module.group" class="text-xs text-gray-500">
    ({{ module.group }})
</span>
```

**Casos de Uso Suportados**

-   ✅ **Leitura Apenas**: Ativar só "Visualizar" para relatórios
-   ✅ **Editor Sem Eliminação**: Criar + Visualizar + Editar (sem Eliminar)
-   ✅ **Aprovador**: Apenas Visualizar + Editar (workflow aprovação)
-   ✅ **Administrador Limitado**: Todas exceto Eliminar (segurança)

**Backend Compatível**

-   Sistema Spatie Permission já suportava permissões individuais
-   Backend recebe array de nomes: `['clients.create', 'clients.read']`
-   Apenas frontend precisou de refatoração

**Impacto UX**

-   ✅ Interface mais intuitiva e visual
-   ✅ Controlo fino de acessos por grupo
-   ✅ Cores facilitam identificação rápida do tipo de permissão
-   ✅ Redução de erros ao configurar roles
-   ✅ Organização espelha estrutura do menu lateral (facilita localização)
-   ✅ Identificação clara de submenus e seus grupos pais
-   ✅ **Ordem consistente**: Checkboxes sempre na sequência Criar → Visualizar → Editar → Eliminar

---

## [0.8.0] — 2025-11-06

### 📊 Módulo Logs de Atividade

**Funcionalidades Core**

-   **Histórico Completo**: Registo de todas as ações realizadas no sistema
-   **DataTable Avançado**: Pesquisa, paginação e 7 colunas de informação
-   **Captura de Contexto**: IP, User Agent, dispositivo automático
-   **Logs Granulares**: Login, Logout, CRUD de todos os módulos

**Package Instalado**

-   **Spatie Laravel Activity Log v4.10.2**
-   Tabela: `activity_log` com batch_uuid e event columns
-   Métodos: `activity()`, `performedOn()`, `causedBy()`, `withProperties()`

**Backend**

**LogController**

-   `index()`: Lista activities com paginação (50 por página)
-   Filtro de pesquisa: utilizador, ação, módulo
-   Ordenação: latest (mais recentes primeiro)
-   Mapeamento automático de dados:
    -   IP Address extraído de properties
    -   User Agent extraído de properties
    -   Event fallback para description
    -   Subject type com class_basename()

**Activity Logging - Controllers**

-   ✅ **AuthenticatedSessionController**: Login e Logout
-   ✅ **UserManagementController**: Create, Update, Delete users
-   ✅ **RoleController**: Create, Update, Delete roles
-   ✅ **EntityController**: Create, Update entities

Cada log captura:

```php
activity()
    ->performedOn($model)           // Subject (opcional)
    ->causedBy(Auth::user())        // Causer (quem fez)
    ->withProperties([               // Properties custom
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent(),
    ])
    ->log('action');                 // Description (created, updated, deleted, login, logout)
```

**Migrations**

-   `create_activity_log_table`: id, log_name, description, subject_type, subject_id, causer_type, causer_id, properties (json), created_at
-   `add_event_column_to_activity_log_table`: event (string 255)
-   `add_batch_uuid_column_to_activity_log_table`: batch_uuid

**Frontend - Vue Component**

**Logs/Index.vue**

-   **DataTable com 7 colunas**:

    1. **Data**: Formatada PT (dd/mm/yyyy)
    2. **Hora**: Formatada PT (HH:mm)
    3. **Utilizador**: Nome + Email (ou "Sistema")
    4. **Menu**: Módulo traduzido (Utilizadores, Permissões, Entidades, etc.)
    5. **Ação**: Badge colorido (Criado=verde, Atualizado=azul, Eliminado=vermelho, Login/Logout=cinza)
    6. **Dispositivo**: Detecção automática (Desktop, Mobile, Tablet)
    7. **IP**: Endereço IP formatado como monospace

-   **Pesquisa**: Input com ícone Search, filtro por utilizador/ação/módulo
-   **Paginação**: Completa com links e contagem de registos
-   **Empty State**: Mensagem quando não há logs

**Mapeamentos Frontend**

Labels de Módulos:

```javascript
Entity → "Entidades"
Contact → "Contactos"
Article → "Artigos"
User → "Utilizadores"
Role → "Permissões"
Country → "Países"
ContactFunction → "Funções Contacto"
VatRate → "Taxas IVA"
```

Labels de Ações:

```javascript
created → "Criado" (badge success)
updated → "Atualizado" (badge default)
deleted → "Eliminado" (badge destructive)
login → "Login" (badge default)
logout → "Logout" (badge secondary)
```

Detecção de Dispositivo:

```javascript
Mobile/Android/iPhone → "Mobile"
Tablet/iPad → "Tablet"
Outros → "Desktop"
```

**Rotas**

-   `GET /logs` → `logs.index` (LogController@index)

**Menu Navegação**

-   **Logs** (ícone: Activity)
    -   Rota: `logs.index`
    -   Active state: `route().current("logs.*")`
    -   Menu principal (não é submenu)

**Decisões Técnicas**

-   ✅ **Logs manuais apenas**: Removido LogsActivity trait dos models para evitar duplicação
-   ✅ **IP e User Agent sempre capturados**: Contexto completo em cada log
-   ✅ **Subject opcional**: Logs de sistema (login/logout) não têm subject
-   ✅ **Paginação 50 registos**: Balance entre performance e usabilidade
-   ✅ **Event fallback**: Usa description quando event é null (compatibilidade)
-   ✅ **Pesquisa global**: Filtra por description, log_name e causer name/email

---

## [0.7.0] — 2025-11-06

### 🔐 Módulo Gestão de Acessos (Utilizadores e Permissões)

**Funcionalidades Core**

-   **Gestão de Utilizadores**: CRUD completo com atribuição de roles
-   **Gestão de Permissões**: Grupos de permissões com ativação por menu
-   **Sistema Hierárquico**: 4 roles predefinidos com permissões granulares
-   **Segurança**: Proteção contra auto-eliminação e eliminação de Super Admin

**Package Instalado**

-   **Spatie Laravel Permission v6.23.0**
-   Traits: `HasRoles` no User model
-   Métodos: `syncPermissions()`, `syncRoles()`, `can()`

**Backend**

**User Model - Extensões**

-   Novos campos: `mobile` (string 20, nullable), `active` (boolean, default true)
-   Trait: `HasRoles` de Spatie Permission
-   Fillable: name, email, mobile, password, active
-   Cast: active (boolean)

**Role Model - Extensões**

-   Novo campo: `active` (boolean, default true) para estado do grupo

**RoleAndPermissionSeeder**

-   ✅ **48 permissões**: 12 módulos × 4 ações (create, read, update, delete)
-   Módulos: clients, suppliers, contacts, articles, proposals, orders, financial, users, roles, countries, contact-functions, vat-rates
-   ✅ **4 Roles Hierárquicos**:
    -   **Super Admin**: Todas as permissões (96 - inclui todas menos algumas específicas)
    -   **Administrador**: Gestão operacional sem users/roles (85 perms)
    -   **Gestor**: Operações principais (20 perms - create/read/update nos módulos core)
    -   **Utilizador**: Apenas leitura (12 perms - read only)
-   Método: `firstOrCreate()` para evitar duplicação
-   Sync: `syncPermissions()` para updates idempotentes

**AssignSuperAdminSeeder**

-   Atribui role Super Admin ao admin@gest-app.com
-   Executado automaticamente após RoleAndPermissionSeeder

**RoleController**

-   Resource controller com validação de sistema
-   `index()`: Retorna roles com `users_count` e `active`
-   `create()`/`edit()`: Passa `getGroupedPermissions()` para Vue
-   `store()`: Valida name (unique), permissions (array), active (boolean)
-   `update()`: Mesma validação + unique exceto próprio ID
-   `destroy()`: **Proteção** contra eliminação de Super Admin e Administrador
-   `getGroupedPermissions()`: Agrupa por módulo com labels em português
-   `getModuleLabel()`: Mapeia keys para nomes legíveis

**UserManagementController**

-   Resource controller para gestão de utilizadores
-   `index()`: Lista users com primeiro role name
-   `create()`/`edit()`: Carrega roles disponíveis
-   `store()`: Hash password, `syncRoles([role_id])`
-   `update()`: Password opcional (blank = mantém atual), `syncRoles()`
-   `destroy()`: **Proteções**:
    -   Impede auto-eliminação (auth()->user()->id check)
    -   Impede eliminação de users com role Super Admin

**Migrations**

-   `add_mobile_and_active_to_users_table`: mobile (string 20), active (boolean)
-   `add_active_to_roles_table`: active (boolean default true) after guard_name

**Frontend - Vue Components**

**Roles/Index.vue**

-   DataTable com colunas: Nome do Grupo | Utilizadores Relacionados | Estado | Ações
-   Search por nome do role
-   Badge para contagem de utilizadores e estado (Ativo/Inativo verde/cinza)
-   Ações: Editar (Pencil) | Eliminar (Trash2)
-   Ícone: Shield (lucide-vue-next)

**Roles/Create.vue**

-   Form Shadcn/ui com campos:
    -   Nome do Grupo (required, unique)
    -   **Ativar ou Inativar Menus**: 1 checkbox por módulo
        -   Ao ativar: atribui automaticamente 4 permissões CRUD
        -   Label: "Create, Read, Update, Delete"
        -   Design: Lista simples com hover effect
    -   Estado: Checkbox "Ativo" (default true)
-   Método: `toggleModule()` - adiciona/remove todas as 4 permissões
-   Computed: `isModuleActive()` - verifica se todas as 4 perms estão ativas

**Roles/Edit.vue**

-   Idêntico a Create.vue com pré-preenchimento
-   Props: role, permissions, rolePermissions (array de nomes)
-   Form inicializado com `props.role.active` e `props.rolePermissions`
-   PUT para `roles.update`

**Users/Index.vue**

-   DataTable: Nome | Email | Telemóvel | Grupo | Estado | Ações
-   Search: nome, email ou telemóvel
-   Badge: role name (default) e active status (success/secondary)
-   Ícone: Users (lucide-vue-next)

**Users/Create.vue**

-   Form com campos:
    -   Nome, Email, Telemóvel
    -   Password + Confirmação (min 8 chars)
    -   Grupo de Permissões (Select com roles disponíveis)
    -   Ativo (Checkbox default true)
-   Validação: `isFormValid` verifica password match

**Users/Edit.vue**

-   Campos idênticos a Create
-   **Password opcional**: "Deixe em branco para manter a password atual"
-   Pré-preenchimento: name, email, mobile, role, active
-   PUT para `users.update`

**Rotas**

-   `Route::resource('roles', RoleController::class)` - 7 rotas
-   `Route::resource('users', UserManagementController::class)` - 7 rotas

**Menu Navegação**

-   **Gestão de Acessos** (ícone: ShieldCheck)
    -   Utilizadores → `users.index` (ícone: UserCog)
    -   Permissões → `roles.index` (ícone: Lock)
-   Active state: `route().current("users.*")` e `route().current("roles.*")`

**Decisões Técnicas**

-   ✅ Permissões mantidas granulares no backend (48 perms) mas UI simplificada (12 checkboxes)
-   ✅ Sistema permite controle fino via código enquanto UI é user-friendly
-   ✅ Spatie Permission escolhido por ser o standard Laravel para ACL
-   ✅ Hierarquia de roles clara: Super Admin > Administrador > Gestor > Utilizador
-   ✅ Proteções de segurança em múltiplos níveis (controller + UI)

---

## [0.6.0] — 2025-11-06

### 💰 Módulo Taxas de IVA (Configurações - Financeiro)

**Funcionalidades Core**

-   **CRUD Completo**: Create, Read, Update, Delete para taxas de IVA
-   **Gestão Dinâmica**: Taxas configuráveis em vez de valores fixos
-   **Taxa Padrão**: Sistema garante apenas uma taxa padrão ativa
-   **Integração Artigos**: Dropdown dinâmico nos formulários de Artigos

**Backend**

**VatRate Model**

-   Campos: `name` (string 50), `rate` (decimal 5,2), `is_default` (boolean), `active` (boolean)
-   Scopes: `active()`, `default()`
-   Accessor: `getFormattedRateAttribute()` retorna "23%"
-   Casts: rate (decimal:2), is_default/active (boolean)

**VatRateController**

-   Resource controller com todos os métodos CRUD
-   `store()`: Remove padrão de outras taxas se nova taxa marcada como padrão
-   `update()`: Mesma lógica de exclusividade do padrão
-   `destroy()`: Eliminação simples (verificação de uso opcional)
-   Ordenação: rate DESC (maior taxa primeiro)

**Migration**

-   Tabela `vat_rates` com id, name, rate, is_default, active, timestamps
-   Rate: decimal(5,2) para suportar 0.00 até 999.99%

**VatRateSeeder**

-   ✅ 4 taxas pré-carregadas:
    -   **IVA Normal**: 23% (padrão)
    -   **IVA Intermédio**: 13%
    -   **IVA Reduzido**: 6%
    -   **Isento**: 0%

**ArticleController - Integração**

-   `create()` e `edit()`: Carregam VatRates ativas da BD
-   opcoesIva mapeado: `[{value: 23, label: "IVA Normal (23%)", is_default: true}]`
-   `store()` e `update()`: Validação dinâmica com `Rule::in($validVatRates)`
-   Substituiu array estático [0,6,13,23] por consulta BD

**Frontend**

**VatRates/Index.vue**

-   Listagem tabela com 5 colunas: Nome, Taxa (%), Padrão, Estado, Ações
-   Taxa exibida com destaque: `<span class="text-lg font-semibold text-blue-600">23%</span>`
-   Badge verde "Padrão" para taxa padrão
-   Badge Ativo/Inativo para estado
-   Pesquisa por nome ou taxa
-   Botões: Adicionar Taxa IVA, Editar (Pencil), Eliminar (Trash2)
-   Ícone: `Percent` do lucide-vue-next

**VatRates/Create.vue**

-   Formulário Shadcn/ui com 4 campos:
    -   Nome: Input text (max 50) - Ex: "IVA Normal"
    -   Taxa (%): Input number (min 0, max 100, step 0.01)
    -   Taxa Padrão: Checkbox - "Esta é a taxa padrão"
    -   Estado: Checkbox - "Taxa ativa"
-   Validação: nome obrigatório, taxa 0-100
-   Submit: POST para `vat-rates.store`

**VatRates/Edit.vue**

-   Idêntico ao Create, mas pré-preenchido com dados existentes
-   Título: "Editar Taxa de IVA"
-   Submit: PUT para `vat-rates.update`
-   Botão: "Atualizar Taxa IVA"

**Articles/Create.vue & Edit.vue - Impacto**

-   Dropdown IVA agora dinâmico: carrega de `props.opcoesIva`
-   Labels descritivos: "IVA Normal (23%)" em vez de só "23%"
-   Validação backend garante apenas taxas ativas aceites

**Navegação**

-   **Menu**: Configurações → Financeiro - IVA (ativado)
-   **Routes**: vat-rates.index, .create, .store, .edit, .update, .destroy
-   **Ícone**: DollarSign (menu), Percent (páginas)

**Benefícios**

-   ✅ Taxas IVA configuráveis sem alterar código
-   ✅ Facilita adaptação a mudanças legislativas
-   ✅ Suporte multi-país (taxas específicas por jurisdição)
-   ✅ Dropdown Artigos sempre atualizado automaticamente
-   ✅ Uma única taxa padrão garantida pelo sistema

---

## [0.5.2] — 2025-11-06

### 🔧 Correções Formulários de Edição

**Problema Identificado**

-   Formulários de edição de Clientes/Fornecedores não carregavam dados existentes
-   Campos NIF e País apareciam vazios ao editar
-   Formulário comportava-se como criação em vez de edição

**Correções Implementadas**

**Backend - Entity Model**

-   ✅ Adicionado accessor `getNifAttribute()` para mapear `tax_number` → `nif`
-   ✅ Adicionado `$appends = ['nif']` para incluir accessor na serialização JSON
-   ✅ Accessor garante compatibilidade entre BD (tax_number) e formulário (nif)

**Backend - EntityController**

-   ✅ Método `edit()` atualizado para passar `countries` ao formulário
-   ✅ Método `update()` completamente reescrito com validação completa
-   ✅ Mapeamento correto: `nif` → `tax_number`, `country` → `country_code`
-   ✅ Validação unique com exceção do registo atual (`unique:entities,tax_number,{id}`)
-   ✅ Suporte VIES: Re-validação VAT se número mudou
-   ✅ Redirecionamento contextual (clients.index vs suppliers.index)

**Frontend - Entities/Edit.vue**

-   ✅ Props alterado de `{countries, nextNumber}` para `{entity, countries}`
-   ✅ Form inicializado com dados da entidade existente
-   ✅ Campo `country` corrigido para usar `entity.country_code` em vez de `entity.country`
-   ✅ Validação NIF: Apenas verifica duplicados se NIF foi alterado
-   ✅ Título dinâmico baseado no tipo (Cliente/Fornecedor/Entidade)
-   ✅ Método submit: `form.post()` → `form.put(route('clients.update', entity.id))`
-   ✅ Campo número: placeholder mostra número existente

**Impacto**

-   ✅ Edição de Clientes funcional com todos os campos preenchidos
-   ✅ Edição de Fornecedores funcional com todos os campos preenchidos
-   ✅ Validação NIF inteligente (ignora NIF original)
-   ✅ País carrega corretamente do `country_code`

---

## [0.5.1] — 2025-11-05

### 🌍 Módulo Países (Configurações)

**Funcionalidades Core**

-   **CRUD Completo**: Create, Read, Update, Delete para países
-   **Gestão Centralizada**: Administração de países do sistema
-   **Validação ISO**: Códigos ISO 2, ISO 3 e numérico
-   **Suporte VIES**: Marcação países União Europeia
-   **Estados**: Ativo/Inativo para controlo disponibilidade

**Backend**

-   **Country Model**: Campos code, name, name_en, iso3, numeric_code, phone_prefix, vies_enabled, currency_code, timezone, active
-   **CountryController**: Resource controller com proteção eliminação (verifica uso em entidades)
-   **Relacionamentos**: hasMany com Entity (clientes/fornecedores)
-   **Validação**: Códigos ISO únicos, uppercase automático

**Frontend**

-   **Countries/Index.vue**: Tabela completa com 9 colunas (Código, Nome PT/EN, ISO3, Prefixo Tel, VIES, Moeda, Estado, Ações)
-   **Countries/Create.vue**: Formulário Shadcn/ui com todos os campos ISO e configurações
-   **Pesquisa**: Filtro por código, nome ou prefixo telefone
-   **Componentes**: Table, Input, Button, Badge, Label, Checkbox

**Navegação**

-   **Menu**: Configurações → Entidades - Países (ativado)
-   **Routes**: countries.index, countries.create, countries.store, countries.edit, countries.update, countries.destroy
-   **Integração**: Alimenta dropdown países em formulários Clientes/Fornecedores

**Dados Iniciais**

-   **14 Países**: Pré-carregados via seeder (PT, ES, FR, DE, UK, etc.)
-   **UE Configurada**: Países com VIES enabled

---

## [0.5.0] — 2025-11-05

### 📦 Módulo Artigos (Produtos/Serviços)

**Funcionalidades Core**

-   **CRUD Completo**: Create, Read, Update, Delete para artigos
-   **Referências Automáticas**: Sistema ART001, ART002, ART003...
-   **Upload Imagens**: Preview, validação formato/tamanho (2MB máx)
-   **Gestão IVA**: Dropdown taxas 0%, 6%, 13%, 23%
-   **Estados**: Ativo/Inativo com filtros

**Backend**

-   **Article Model**: Campos referencia, nome, descricao, preco, iva_percentagem, foto, observacoes, estado
-   **ArticleController**: Resource controller com validações completas
-   **Migration**: Schema com constraints e indexes otimizados
-   **Seeder**: 6 artigos exemplo para testes
-   **Storage**: Configuração upload imagens em `storage/app/public/articles`

**Frontend**

-   **Articles/Index.vue**: Listagem com display cards responsivo
-   **Articles/Create.vue**: Formulário Shadcn/ui Form conforme especificação
-   **Componentes**: Form, FormField, Input, Select, Textarea, Button
-   **Validação**: Frontend + backend com feedback visual
-   **Preview Imagem**: Upload com preview e remoção

**Navegação**

-   **Menu**: Submenu "Artigos" em Configurações → Artigos
-   **Breadcrumbs**: Navegação contextual completa
-   **Routes**: articles.index, articles.create, articles.store, articles.edit, articles.update, articles.destroy

**Validações**

-   **Campos Obrigatórios**: Nome, Preço, IVA, Estado
-   **Formato Preço**: Decimal 2 casas, valor positivo
-   **Upload Imagem**: JPEG, PNG, JPG, GIF - máx 2MB
-   **Referência Única**: Constraint database + validação

---

## [0.4.5] — 2025-11-05

### 🔧 Correção Navegação Welcome + Limpeza Projeto

**Frontend**

-   **Welcome.vue**: Corrigidos botões Login/Registo usando componentes `Link` do Inertia.js
-   **Styling**: Adicionado `z-index: 50` e `pointer-events: auto` para melhor interatividade
-   **Navegação**: SPA routing agora funciona corretamente sem refresh da página

**Contactos**

-   **ContactsDataTableNew.vue**: Corrigidas referências `entity.nome` → `entity.name`
-   **Contact Model**: Adicionado `$appends` array para serialização JSON dos accessors
-   **Exibição**: Todas as colunas (nome, função, telefone, etc.) agora visíveis

**Manutenção**

-   Removidos arquivos backup desnecessários (`.backup`, `toArray()`)
-   Documentação atualizada e simplificada para nível de estágio
-   Configuração MySQL mantida e validada

---

## [0.4.4] — 2025-11-04

### 🗄️ Migração Base de Dados MySQL

**Configuração**

-   **.env**: Alterado de SQLite para MySQL conforme especificações do projeto
-   **Database**: `gest_app` database criada e configurada
-   **Credenciais**: Host `127.0.0.1`, Port `3306`, username `root`

**Documentação**

-   **README.md**: Instruções XAMPP atualizadas
-   **database-config.md**: Criado guia específico MySQL
-   **.env.example**: Template atualizado para MySQL

**Validação**

-   Migrations executadas com sucesso
-   Seeders funcionais (países, entities de teste)
-   Conexão VIES mantida operacional

---

## [0.4.3] — 2025-11-04

### 🐛 Correção Tabela Contactos

**Problema Identificado**

-   DataTable apenas exibia coluna "email"
-   Accessors do modelo não sendo serializados

**Solução Implementada**

-   **Contact.php**: Adicionado `protected $appends = ['nome', 'apelido', 'funcao', 'telefone', 'telemovel']`
-   **ContactsDataTableNew.vue**: Corrigidas todas as referências de campos
-   **Relacionamentos**: Validado `belongsTo(Entity::class)` funcionando

**Resultado**

-   Tabela exibe todas as colunas corretamente
-   Filtros e ordenação operacionais
-   Performance otimizada com eager loading

---

## [0.4.2] — 2025-11-04

### 🎯 Menu Accordion Lateral

**Interface**

-   **AuthenticatedLayout.vue**: Menu lateral expandível implementado
-   **Grupos**: Financeiro, Gestão Acessos, Configurações organizados
-   **Animações**: Transições CSS suaves para expand/collapse

**Funcionalidades**

-   Estado persistente por sessão
-   Responsivo (mobile + desktop)
-   Icons consistentes (Lucide React)
-   Hover effects e active states

**Navegação**

-   Integração completa com Inertia.js routing
-   Breadcrumbs automáticos
-   Links diretos para todas as secções

---

## [0.4.1] — 2025-11-04

### 📚 Documentação Arquitetura Modular

**Documentação Técnica**

-   **modular-architecture.md**: Arquitetura completa documentada
-   **README.md**: Progresso modular (2/16+ módulos = 15%)
-   **Roadmap**: Timeline detalhada até 18/11/2025

**Validação Módulos**

-   **Módulo 1 (Entidades)**: ✅ 100% completo e validado
-   **Módulo 2 (Contactos)**: ✅ 100% completo e validado
-   **Stack**: Laravel 12 + Vue.js 3 + Shadcn/ui + Inertia.js

**Próximos Passos**

-   Módulo 3: Artigos (Produtos/Serviços)
-   Desenvolvimento incremental com controlo qualidade

---

## [0.4.0] — 2025-11-04

### 👥 Módulo Contactos Completo

**Funcionalidades Core**

-   **CRUD**: Create, Read, Update, Delete completos
-   **Relacionamentos**: Contactos ↔ Entidades (clientes/fornecedores)
-   **Validação**: Campos obrigatórios + formatos (email, telefone)
-   **RGPD**: Checkbox consentimento obrigatório

**Interface**

-   **ContactsDataTable**: Tabela moderna com Shadcn/ui
-   **Create/Edit Forms**: Formulários responsivos e validados
-   **Filtros**: Busca por nome, empresa, função
-   **Paginação**: Performance otimizada para grandes datasets

**Integrações**

-   **Countries**: Dropdown países com flags
-   **Entities**: Seleção automática cliente/fornecedor
-   **Permissions**: Sistema preparado para roles/permissions

---

## [0.3.1] — 2025-11-03

### 🔐 Validação NIF + Integração VIES

**Validação NIF**

-   **Algoritmo**: Implementado cálculo dígito controlo português
-   **Unique**: Constraint database + validation rules
-   **Feedback**: Mensagens erro claras e específicas

**VIES Integration**

-   **API**: Integração European Commission VIES webservice
-   **Validação**: NIFs UE validados em tempo real
-   **Cache**: Resultados cached para performance
-   **Fallback**: Sistema funciona mesmo com VIES indisponível

**UX Improvements**

-   Loading states durante validação VIES
-   Success/error feedback visual
-   Auto-preenchimento dados quando disponível

---

## [0.3.0] — 2025-11-03

### 🏢 Módulo Entidades (Clientes/Fornecedores)

**Funcionalidades Base**

-   **Clientes**: CRUD completo com numeração automática (C001, C002...)
-   **Fornecedores**: CRUD completo com numeração automática (F001, F002...)
-   **Campos**: Nome, NIF, morada, contactos, observações

**DataTable Shadcn/ui**

-   **Performance**: Paginação server-side
-   **Filtros**: Busca global + filtros específicos
-   **Ordenação**: Todas as colunas ordenáveis
-   **Actions**: Edit, Delete, View inline

**Validações**

-   **NIF**: Validação algoritmo português + unique
-   **Required Fields**: Nome e NIF obrigatórios
-   **Business Logic**: Separação clara cliente vs fornecedor

---

## [0.2.1] — 2025-11-02

### 🎨 Interface Moderna + Menus Separados

**Layout Improvements**

-   **AuthenticatedLayout**: Design moderno com sidebar
-   **Navigation**: Menus separados Clientes/Fornecedores
-   **Breadcrumbs**: Navegação contextual
-   **Footer**: Informações projeto + autor

**UI Components**

-   **Shadcn/ui**: Componentes base implementados
-   **Forms**: Input, Button, Card, Badge components
-   **DataTable**: Componente reutilizável
-   **Theme**: Dark/light mode preparado

**UX**

-   **Responsive**: Mobile-first approach
-   **Loading States**: Skeleton loaders
-   **Error Handling**: Messages user-friendly

---

## [0.2.0] — 2025-11-02

### 🚀 Setup Base Tecnológico

**Stack Principal**

-   **Laravel 12**: Framework PHP com latest features
-   **Vue.js 3**: Composition API + TypeScript ready
-   **Inertia.js**: SPA sem API complexity
-   **Vite**: Build tool moderno e rápido

**Styling & UI**

-   **Tailwind CSS 3**: Utility-first CSS framework
-   **Shadcn/ui**: Component library enterprise-grade
-   **Lucide Icons**: Icon set moderno e consistente
-   **Responsive**: Mobile-first design

**Autenticação & Segurança**

-   **Laravel Fortify**: Authentication backend
-   **Middleware**: Proteção rotas authenticated
-   **CSRF**: Proteção automática forms
-   **Validation**: Server + client-side

---

## [0.1.0] — 2025-11-01

### 🎯 Projeto Inicial

**Setup Ambiente**

-   **Laravel**: Projeto inicializado com composer
-   **Database**: SQLite configuração inicial
-   **Git**: Repositório + .gitignore configurado
-   **Environment**: .env template criado

**Estrutura Base**

-   **MVC**: Controllers, Models, Views estruturados
-   **Routes**: web.php configurado
-   **Migrations**: Schema base preparado
-   **Seeders**: Dados teste implementados

**Documentação**

-   **README**: Objetivos e setup inicial
-   **Changelog**: Controlo versões implementado
-   **Comments**: Código documentado inline
