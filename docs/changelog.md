# Histórico de Desenvolvimento — Gest-App

Registo das principais mudanças e desenvolvimentos realizados durante o estágio.

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
