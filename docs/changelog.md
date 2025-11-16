# Histórico de Desenvolvimento — Gest-App

Registo das principais mudanças e desenvolvimentos realizados durante o estágio.

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
-   Removidos wrappers py-12 e max-w-*
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
