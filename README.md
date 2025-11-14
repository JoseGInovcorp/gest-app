# 🏢 Gest-App — Sistema de Gestão Empresarial

> Projeto Final de Estágio | Sistema de gestão empresarial para PMEs

## 📊 Status do Projeto

**Versão:** v0.14.0  
**Progresso:** 80% (16 de 20 módulos)  
**Entrega:** 18 Nov 2025  
**BD:** ✅ MySQL configurado e funcionando  
**Welcome:** ✅ Navegação funcional  
**Segurança:** ✅ Sistema de permissões com controlo de UI  
**Logs:** ✅ Histórico de atividades completo  
**Branding:** ✅ Logo personalizado integrado  
**UX:** ✅ Interface uniformizada em todos os módulos  
**Financeiro:** ✅ Contas bancárias, conta corrente e faturas fornecedores operacionais  
**Email:** ✅ Sistema de envio configurado e testado (MailHog)  
**Testes:** ✅ Suite automatizada implementada (9 testes, 17 asserções)  
**Calendário:** ✅ FullCalendar integrado com gestão completa de eventos

## 🛠️ Tecnologias

-   **Backend:** Laravel 12
-   **Frontend:** Vue.js 3 + Inertia.js
-   **UI:** Tailwind CSS + Shadcn/ui
-   **BD:** MySQL
-   **ACL:** Spatie Laravel Permission v6.23.0
-   **Email:** Laravel Mail + MailHog (dev)
-   **Testes:** PHPUnit + Laravel Testing

## 📦 Módulos Implementados

### ✅ Módulo 1: Entidades (Clientes/Fornecedores)

-   CRUD completo com validação NIF
-   Integração VIES para dados UE
-   DataTable com filtros e pesquisa
-   Numeração automática

### ✅ Módulo 2: Contactos

-   Sistema relacional com entidades
-   Campos: nome, função, telefones, email
-   Consentimento RGPD
-   Interface moderna com todas as colunas funcionais

### ✅ Módulo 3: Artigos (Produtos/Serviços)

-   CRUD completo com referências automáticas (ART001+)
-   Upload imagens com preview (JPEG, PNG, GIF - máx 2MB)
-   Gestão IVA: dropdown dinâmico carregado da BD
-   Estados Ativo/Inativo
-   Formulários Shadcn/ui Form components

### ✅ Módulo 4: Países (Configurações)

-   CRUD completo para gestão de países
-   Códigos ISO 2, ISO 3, numérico
-   Suporte VIES (União Europeia)
-   Prefixo telefone, moeda, timezone
-   14 países pré-carregados
-   Alimenta dropdown em Clientes/Fornecedores

### ✅ Módulo 5: Funções de Contacto (Configurações)

-   CRUD completo para funções de contactos
-   10 funções pré-definidas (Diretor Geral, Comercial, etc.)
-   Estados Ativo/Inativo
-   Alimenta dropdown em formulário de Contactos

### ✅ Módulo 6: Taxas de IVA (Configurações - Financeiro)

-   CRUD completo para gestão de taxas IVA
-   4 taxas pré-carregadas: 0%, 6%, 13%, 23%
-   Sistema de taxa padrão (apenas uma ativa)
-   Integração dinâmica com formulários de Artigos
-   Labels descritivos: "IVA Normal (23%)"
-   Validação backend garante apenas taxas ativas

### ✅ Módulo 7: Gestão de Acessos (Utilizadores e Permissões)

-   **Utilizadores:** CRUD completo com campos nome, email, telemóvel, role, estado
-   **Permissões:** Sistema baseado em 64 permissões (16 módulos × 4 ações CRUD)
-   **6 Grupos Hierárquicos:** Super Admin, Administrador, Gestor Comercial, Gestor Financeiro, Editor, Visualizador
-   **Controlo Granular de UI:** Botões de ação (Criar, Editar, Eliminar) só aparecem se utilizador tiver permissão
-   **Segurança Aprimorada:**
    -   Utilizadores nunca vêem botões que não podem usar
    -   Zero erros 403 - interface limpa e intuitiva
    -   Sistema genérico que funciona com qualquer grupo criado
-   **UI Baseada em Permissões:**
    -   Backend: Controllers verificam `$request->user()->can('module.action')`
    -   Frontend: Componentes usam `v-if="can.action"` para renderização condicional
    -   Exemplo: Utilizador "Visualizador" vê listas mas não vê botões de ação
-   **Package:** Spatie Laravel Permission v6.23.0
-   **Documentação:** Ver `docs/access-management.md` para detalhes técnicos

#### 📋 Distribuição de Permissões por Grupo

| Grupo                 | Permissões    | Módulos com Acesso Completo                                 |
| --------------------- | ------------- | ----------------------------------------------------------- |
| **Super Admin**       | 64/64 (100%)  | Todos os 16 módulos                                         |
| **Administrador**     | 56/64 (87.5%) | Todos exceto algumas restrições                             |
| **Gestor Comercial**  | 22/64 (34%)   | Clientes, Fornecedores, Contactos, Artigos, Ordens Trabalho |
| **Gestor Financeiro** | 11/64 (17%)   | Apenas leitura: Clientes, Fornecedores, Taxas IVA           |
| **Editor**            | 9/64 (14%)    | Contactos, Arquivo Digital                                  |
| **Visualizador**      | 16/64 (25%)   | Apenas leitura em todos os módulos                          |

#### 🎯 Módulos Cobertos pelo Sistema de Permissões

1. **Comercial:** Clientes, Fornecedores, Contactos, Artigos
2. **Financeiro:** Taxas IVA
3. **Operacional:** Calendário, Ordens de Trabalho, Arquivo Digital
4. **Sistema:** Logs, Utilizadores, Grupos de Permissões
5. **Configurações:** Empresa, Países, Funções de Contactos

### ✅ Módulo 8: Logs de Atividade

-   **Histórico Completo:** Registo de todas as ações (CRUD, login, logout)
-   **DataTable com 7 colunas:** Data, Hora, Utilizador, Menu, Ação, Dispositivo, IP
-   **Captura de Contexto:** IP Address e User Agent em cada log
-   **Detecção Automática:** Dispositivo (Desktop/Mobile/Tablet) por user agent
-   **Pesquisa Avançada:** Filtro por utilizador, ação ou módulo
-   **Paginação:** 50 registos por página, ordenação por mais recente
-   **Módulos Monitorizados:** Login/Logout, Utilizadores, Permissões, Entidades
-   **Package:** Spatie Laravel Activity Log v4.10.2
-   **Mapeamentos:** Traduções PT para módulos e ações, badges coloridos por tipo

### ✅ Módulo 9: Configurações - Empresa

-   **Personalização Total:** Dados da empresa que aparecem em toda a aplicação
-   **Campos Editáveis:** Logotipo, Nome, NIF, Morada, Código Postal, Localidade
-   **Upload de Logo:** PNG, JPG, GIF até 2MB com preview em tempo real
-   **Singleton Pattern:** Apenas 1 registo de empresa no sistema
-   **Flash Messages:** Confirmação visual após guardar alterações
-   **Integração Visual Completa:**
    -   **Login Page:** Logo grande (160px) + nome da empresa
    -   **Welcome Page:** Logo médio (80px) + nome + "Sistema Empresarial powered by Inovcorp"
    -   **Sidebar:** Logo pequeno (48px) + nome + subtítulo (mobile + desktop)
    -   **Fallback:** Ícone Building2 quando não há logo configurado
-   **Utilização Futura:** Dados em PDFs, faturas e documentos oficiais
-   **Permissões:** `company.read` (todos) e `company.update` (Admin apenas)
-   **Acesso:** Menu → Configurações → Empresa
-   **Storage:** Link simbólico criado para `storage/app/public/company/logos`

### ✅ Módulo 10: Contas Bancárias

-   **Gestão Financeira:** Controlo completo das contas bancárias da empresa
-   **Campos Principais:** Nome conta, Banco, IBAN, SWIFT/BIC, Moeda (EUR/USD/GBP)
-   **Tipos de Conta:** Conta Corrente, Conta Poupança, Conta Ordenados, Conta Investimentos
-   **Estados:** Ativa, Inativa, Encerrada
-   **Saldos Automáticos:** Saldo inicial e saldo atual calculado automaticamente
-   **Movimentos Bancários:** Histórico de débitos e créditos com saldo após cada movimento
-   **Categorias:** 9 categorias (Transferência, Pagamento, Recebimento, Juros, Comissões, etc.)
-   **Validações:** IBAN único, SWIFT até 11 caracteres, valores mínimos
-   **Formatação IBAN:** Display automático em blocos de 4 caracteres
-   **Cálculo Automático:** Balance recalculado após cada transação (saldo_atual = saldo_inicial + sum(créditos) - sum(débitos))
-   **Soft Deletes:** Contas podem ser restauradas
-   **Filtros Avançados:** Por nome, banco, IBAN, tipo, estado
-   **Permissões:** `bank-accounts.{create,read,update,delete}`
-   **Acesso:** Menu → Financeiro → Contas Bancárias
-   **Documentação:** Ver `docs/bank-accounts-module.md`

### ✅ Módulo 11: Conta Corrente Clientes

-   **Débitos e Créditos:** Sistema completo de movimentos financeiros por cliente
-   **Conceitos Fundamentais:**
    -   **Débito:** Dinheiro que cliente deve à empresa (aumenta saldo)
    -   **Crédito:** Dinheiro que empresa recebe do cliente (diminui saldo)
-   **Cálculo Automático de Saldos:**
    -   Saldo calculado automaticamente após cada movimento
    -   Atualização em cascata de movimentos subsequentes
    -   Recálculo completo ao eliminar movimento
-   **Métodos Avançados do Model:**
    -   `calculateBalance()`: Calcula saldo baseado no movimento anterior
    -   `updateSubsequentBalances()`: Atualiza todos os movimentos posteriores
    -   `recalculateBalancesForEntity()`: Recalcula saldo completo do cliente
    -   `getCurrentBalance($entityId)`: Retorna saldo atual do cliente
    -   `getEntityStats($entityId)`: Estatísticas (total débitos, créditos, saldo)
-   **Categorias de Movimento:** 7 categorias (Fatura, Pagamento, Nota Crédito, Nota Débito, Juros, Comissões, Ajuste)
-   **Painel de Estatísticas:** Total de débitos, créditos e saldo atual por cliente
-   **Filtros Avançados:** Por cliente, tipo, categoria, período (data início/fim), pesquisa
-   **Validações:** Entity obrigatória, tipo enum, valor mínimo €0.01, categoria enum
-   **Interface Intuitiva:**
    -   Colunas separadas para Débito e Crédito
    -   Saldo após cada movimento visível
    -   Badges coloridos por categoria
    -   Estatísticas destacadas quando cliente selecionado
-   **Permissões:** `client-accounts.{create,read,update,delete}`
-   **Acesso:** Menu → Financeiro → Conta Corrente Clientes
-   **Documentação:** Ver `docs/client-accounts-module.md`
-   **Performance:** Índices compostos para queries otimizadas
-   **Segurança:** Validação em cascata, foreign keys com ON DELETE CASCADE

### ✅ Módulo 13: Faturas de Fornecedores

-   **CRUD Completo:** Criar, visualizar, editar e eliminar faturas recebidas de fornecedores
-   **Numeração Automática:** FF-YYYY-#### (Fatura Fornecedor) com verificação de duplicados
-   **Campos Principais:**
    -   Data fatura e data vencimento
    -   Fornecedor (relação com entities)
    -   Encomenda fornecedor (opcional, relação com supplier_orders)
    -   Valor total
    -   Upload documento da fatura (PDF/JPG/PNG até 5MB)
    -   Upload comprovativo de pagamento (quando marcada como paga)
    -   Estado: Pendente ou Paga
-   **Sistema de Comprovativos:**
    -   Modal automático quando fatura muda de "Pendente" para "Paga"
    -   3 opções: Cancelar (reverte), Não Enviar (salva sem email), Enviar (upload + email)
    -   Validação de ficheiros: Apenas PDF/JPG/PNG
    -   Armazenamento em `supplier_invoices/proofs/`
-   **Envio Automático de Emails:**
    -   Email personalizado com logo e dados da empresa
    -   Assunto: "Comprovativo de Pagamento - Fatura {numero}"
    -   Template HTML responsivo com detalhes da fatura
    -   Anexo: PDF do comprovativo
    -   Destinatário: Email do fornecedor
    -   Mailable: `PaymentProofMail.php`
-   **Interface Vue:**
    -   **Index.vue:** DataTable com 8 colunas, 5 filtros, badges coloridos
    -   **Create.vue:** Formulário com dropdown de encomendas filtrado por fornecedor
    -   **Edit.vue:** Watch automático no estado, modal personalizado para comprovativo

### ✅ Módulo 14: Calendário - Tipos de Eventos (Configurações) 🆕

-   **CRUD Completo:** Gestão de tipos de eventos para o futuro módulo Calendário
-   **Campos Principais:**
    -   Nome único (ex: Visita, Reunião, Intervenção Técnica)
    -   Descrição opcional
    -   **Cor personalizada:** Color picker HTML5 + input texto hexadecimal (#RRGGBB)
    -   **Ícone Lucide:** Campo opcional com link para documentação (max 50 caracteres)
    -   Estado: Ativo/Inativo
-   **Validações:**
    -   Cor obrigatória com regex `/^#[0-9A-Fa-f]{6}$/`
    -   Nome único na base de dados
    -   Sincronização automática entre color picker e campo de texto
-   **Dados Pré-carregados (Seeder):**
    -   6 tipos prontos: Visita (azul), Reunião (roxo), Intervenção Técnica (vermelho), Auditoria (âmbar), Formação (verde), Apresentação (rosa)
    -   Cada tipo com cor e ícone apropriado
-   **Interface Vue:**
    -   **Index.vue:** DataTable com display visual de cores (quadrado colorido + código hex)
    -   **Create/Edit.vue:** Color picker integrado com validação em tempo real
-   **Propósito:** Alimentar categorização visual de eventos no módulo Calendário

### ✅ Módulo 15: Calendário - Ações de Eventos (Configurações) 🆕

-   **CRUD Completo:** Gestão de ações de workflow para eventos
-   **Campos Principais:**
    -   Nome único (ex: Confirmar, Reagendar, Aprovar, Concluir)
    -   Descrição opcional
    -   Estado: Ativo/Inativo
-   **Dados Pré-carregados (Seeder):**
    -   6 ações prontas: Confirmar, Reagendar, Aprovar, Concluir, Cancelar, Adiar
    -   Cada ação com descrição do seu propósito
-   **Interface Vue:**
    -   **Index.vue:** DataTable simples com pesquisa e filtros
    -   **Create/Edit.vue:** Formulário minimalista (nome, descrição, estado)
-   **Propósito:** Padronizar workflow e gestão do ciclo de vida dos eventos no módulo Calendário
-   **Integração Futura:** Permitirá definir ações específicas por tipo de evento

### ✅ Módulo 16: Calendário - Gestão de Eventos 🆕

-   **Interface FullCalendar:** Visualização interativa de eventos com múltiplas vistas (Mês, Semana, Dia, Lista)
-   **Criação Rápida:** Clicar em data/hora no calendário para criar evento automaticamente
-   **Drag & Drop:** Arrastar eventos para reagendar datas e horas
-   **Filtros Dinâmicos:**
    -   Por Utilizador (responsável pelo evento)
    -   Por Entidade (cliente/fornecedor associado)
    -   Refetch automático de eventos ao alterar filtros
-   **Campos Principais:**
    -   **Data e Hora:** Date picker + time picker
    -   **Duração:** Em minutos (padrão 60 min, step 15 min)
    -   **Utilizador:** Responsável pelo evento (obrigatório)
    -   **Entidade:** Cliente/Fornecedor associado (opcional)
    -   **Tipo:** Dropdown carregado de Calendário - Tipos (cores visuais)
    -   **Ação:** Dropdown carregado de Calendário - Ações (opcional)
    -   **Estado:** Agendado, Em Curso, Concluído, Cancelado
    -   **Partilha:** Checkbox (evento partilhado com equipa)
    -   **Conhecimento:** Campo texto para lições aprendidas (opcional)
    -   **Descrição:** Campo texto para detalhes do evento (opcional)
-   **Visualização de Eventos:**
    -   Cores baseadas no tipo de evento (configurado em Calendário - Tipos)
    -   Título composto: "Tipo - Entidade"
    -   Click no evento abre página de detalhes
    -   Badges visuais para estado e partilha
-   **Sistema de Estados:**
    -   **Agendado:** Azul (evento ainda não iniciado)
    -   **Em Curso:** Amarelo (evento em execução)
    -   **Concluído:** Verde (evento finalizado)
    -   **Cancelado:** Vermelho (evento cancelado)
-   **CRUD Completo:**
    -   **Index:** Calendário interativo com filtros e botão "Criar Evento"
    -   **Create:** Formulário completo com todos os campos
    -   **Show:** Visualização detalhada com badges e formatação
    -   **Edit:** Formulário pré-preenchido para alterações
    -   **Delete:** Soft delete com confirmação
-   **Backend Robusto:**
    -   **Controller:** CalendarEventController com 7 métodos
    -   **Model:** CalendarEvent com 4 relações (user, entity, eventType, eventAction)
    -   **Scopes:** agendado(), emCurso(), concluido(), cancelado(), byUser(), byEntity()
    -   **Accessors:** estadoBadgeClass, estadoLabel
    -   **Policy:** CalendarEventPolicy com autorização completa
-   **JSON API Endpoint:**
    -   `/calendar/events-json` retorna eventos no formato FullCalendar
    -   Aceita query params: start, end (ISO dates), user_id, entity_id
    -   Cálculo automático de end datetime baseado em duração
-   **Packages FullCalendar:**
    -   @fullcalendar/core, vue3, daygrid, timegrid, interaction, list
    -   Localização portuguesa (pt-BR)
    -   Dark mode suportado com CSS overrides
-   **Base de Dados:**
    -   Tabela: `calendar_events`
    -   FKs: user_id, entity_id, calendar_event_type_id, calendar_event_action_id
    -   Índices: data, estado, [user_id, data], [entity_id, data]
    -   Soft deletes habilitado
-   **Permissões:** `calendar-events.{create,read,update,delete}`
-   **Acesso:** Menu → Calendário (ativado e funcional)
-   **Integração:** Relacionado com módulos Utilizadores, Entidades, Calendário - Tipos/Ações
-   **Status:** ✅ Totalmente implementado e testado

### ✅ Módulo 13: Faturas de Fornecedores

-   **CRUD Completo:** Criar, visualizar, editar e eliminar faturas recebidas de fornecedores
-   **Numeração Automática:** FF-YYYY-#### (Fatura Fornecedor) com verificação de duplicados
-   **Campos Principais:**
    -   Data fatura e data vencimento
    -   Fornecedor (relação com entities)
    -   Encomenda fornecedor (opcional, relação com supplier_orders)
    -   Valor total
    -   Upload documento da fatura (PDF/JPG/PNG até 5MB)
    -   Upload comprovativo de pagamento (quando marcada como paga)
    -   Estado: Pendente ou Paga
-   **Sistema de Comprovativos:**
    -   Modal automático quando fatura muda de "Pendente" para "Paga"
    -   3 opções: Cancelar (reverte), Não Enviar (salva sem email), Enviar (upload + email)
    -   Validação de ficheiros: Apenas PDF/JPG/PNG
    -   Armazenamento em `supplier_invoices/proofs/`
-   **Envio Automático de Emails:**
    -   Email personalizado com logo e dados da empresa
    -   Assunto: "Comprovativo de Pagamento - Fatura {numero}"
    -   Template HTML responsivo com detalhes da fatura
    -   Anexo: PDF do comprovativo
    -   Destinatário: Email do fornecedor
    -   Mailable: `PaymentProofMail.php`
-   **Interface Vue:**
    -   **Index.vue:** DataTable com 8 colunas, 5 filtros, badges coloridos
    -   **Create.vue:** Formulário com dropdown de encomendas filtrado por fornecedor
    -   **Edit.vue:** Watch automático no estado, modal personalizado para comprovativo
-   **Sistema de Email:**
    -   MailHog configurado para desenvolvimento (localhost:1025)
    -   Interface web em http://localhost:8025
    -   Configuração no `.env` documentada
    -   Templates blade customizados com branding
-   **Testes Automatizados:**
    -   Suite completa: `SupplierInvoiceEmailTest.php`
    -   10 métodos de teste, 17 asserções
    -   100% de cobertura no fluxo de emails
    -   Técnicas: Mail::fake(), Storage::fake(), RefreshDatabase
-   **Permissões:** `supplier-invoices.{create,read,update,delete}`
-   **Acesso:** Menu → Financeiro → Faturas Fornecedores
-   **Documentação:**
    -   Sistema de email: `docs/mailhog-setup.md`
    -   Guia completo com instalação, configuração e troubleshooting
-   **Bugs Corrigidos:** 5 correções aplicadas (campos nome/number, modal customizado, método PATCH)
-   **Status:** ✅ Testado e validado (email recebido no MailHog com anexo)

### ✅ Interface & UX - Uniformização Completa

-   **Headers Padronizados:** Todos os módulos com ícone colorido, título e subtítulo
-   **Breadcrumbs de Navegação:** Caminho completo em todas as páginas (Dashboard / Categoria / Módulo)
-   **Paleta de Cores por Módulo:**
    -   Clientes/Artigos/Empresa: Azul
    -   Fornecedores/Taxas IVA: Verde
    -   Contactos: Laranja
    -   Países: Índigo
    -   Funções/Logs: Roxo
    -   Utilizadores: Âmbar
    -   Grupos Permissões: Vermelho
    -   Contas Bancárias: Verde Esmeralda
    -   Conta Corrente Clientes: Azul Celeste
-   **Estrutura Consistente:** Layout uniformizado em 13 módulos
-   **Código Limpo:** Remoção de templates antigos e divs desnecessárias

### ✅ Páginas e Navegação

-   Página Welcome com navegação funcional
-   Menu lateral accordion expandível
-   3 seções: Financeiro, Gestão Acessos, Configurações
-   Animações CSS suaves e interatividade otimizada
-   Totalmente responsivo

## 🔧 Instalação

1. **Clonar repositório**

```bash
git clone [repo-url]
cd gest-app
```

2. **Instalar dependências**

```bash
composer install
npm install
```

3. **Configurar ambiente**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Base de dados MySQL**

```bash
# Iniciar XAMPP e MySQL
# Abrir XAMPP Control Panel e iniciar MySQL

# Criar base de dados (via phpMyAdmin ou MySQL)
mysql -u root -p
CREATE DATABASE gest_app;
exit

# Executar migrações
php artisan migrate --seed
```

5. **Executar aplicação**

```bash
# Iniciar frontend (obrigatório)
npm run dev
```

## 🚀 Como Acessar a Aplicação

### **Pré-requisitos**

1. **XAMPP MySQL** deve estar a funcionar
2. **Laravel Herd** instalado (para servir a aplicação)
3. **Frontend Vite** em execução

### **Passos para Aceder**

1. **Iniciar XAMPP** → MySQL Service
2. **Iniciar Frontend:**
    ```bash
    cd c:\Inovcorp\gest-app
    npm run dev
    ```
3. **Acessar:** `https://gest-app.test`

### **Credenciais de Acesso**

-   **Email:** `admin@gest-app.com`
-   **Password:** `password`
-   **Perfil:** Super Admin (acesso total ao sistema)

### **URLs Úteis**

-   **Aplicação:** `https://gest-app.test`
-   **phpMyAdmin:** `http://localhost/phpmyadmin`
-   **Base de Dados:** `gest_app`
-   **MailHog (Email Testing):** `http://localhost:8025`

### **Configuração de Email (Desenvolvimento)**

Para testar o envio de emails localmente:

1. **Iniciar MailHog:**
    ```bash
    C:\MailHog\mailhog.exe
    ```
2. **Acessar interface:** `http://localhost:8025`
3. **Verificar `.env`:**
    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=127.0.0.1
    MAIL_PORT=1025
    MAIL_ENCRYPTION=null
    ```
4. **Documentação completa:** Ver `docs/mailhog-setup.md`

## 📋 Funcionalidades Principais

### Gestão de Entidades

-   Clientes e fornecedores unificados
-   Validação automática de NIF
-   Preenchimento automático via VIES (UE)
-   Filtros avançados por tipo/país
-   ✅ **Edição corrigida** (v0.5.2): NIF e País carregam corretamente

### Gestão de Contactos

-   Associação a entidades
-   Dados pessoais e profissionais
-   Consentimento RGPD obrigatório

### Gestão de Artigos

-   Produtos e serviços
-   Sistema de referências automáticas
-   Upload e gestão de imagens
-   Taxas IVA dinâmicas da BD

### Gestão de Acessos e Permissões

#### 🔐 Sistema de Controlo de Acesso Baseado em Permissões

**Visibilidade Inteligente de UI:**

-   Botões de ação (Criar, Editar, Eliminar) só aparecem se utilizador tiver permissão
-   Zero erros 403 - interface limpa e adaptativa
-   Sistema 100% genérico que funciona com qualquer combinação de permissões

**Arquitetura do Sistema:**

```
Backend (Controller) → Verifica permissões → Envia objeto 'can'
        ↓
Frontend (Vue) → Recebe props → Renderiza condicionalmente com v-if
        ↓
Resultado → Botões só existem se houver permissão
```

**Exemplos de Comportamento:**

| Grupo                 | Módulo Clientes | Botões Visíveis         |
| --------------------- | --------------- | ----------------------- |
| **Super Admin**       | CRUD completo   | Criar, Editar, Eliminar |
| **Gestor Comercial**  | CRUD completo   | Criar, Editar, Eliminar |
| **Gestor Financeiro** | Apenas leitura  | Nenhum botão            |
| **Visualizador**      | Apenas leitura  | Nenhum botão            |

**Módulos com Controlo de Permissões:**

-   ✅ Clientes e Fornecedores
-   ✅ Contactos
-   ✅ Artigos
-   ✅ Países
-   ✅ Funções de Contactos
-   ✅ Taxas de IVA
-   ✅ Grupos de Permissões
-   ✅ Utilizadores

**Grupos de Utilizadores:**

1. **Super Admin** (64 permissões) - Acesso total ao sistema
2. **Administrador** (56 permissões) - Gestão operacional completa
3. **Gestor Comercial** (22 permissões) - Área comercial e operacional
4. **Gestor Financeiro** (11 permissões) - Apenas visualização financeira
5. **Editor** (9 permissões) - Contactos e arquivo digital
6. **Visualizador** (16 permissões) - Apenas leitura em todos módulos

**Segurança Implementada:**

-   ✅ Proteção contra auto-eliminação
-   ✅ Proteção de Super Admin (não pode ser eliminado)
-   ✅ Validação backend em todos os endpoints
-   ✅ UI adaptativa baseada em permissões reais
-   ✅ Middleware de autorização em todas as rotas

### Configurações Sistema

-   **Empresa**: Logotipo, nome, NIF, morada completa (dados para documentos)
-   **Países**: 14 países pré-carregados, códigos ISO, VIES
-   **Funções de Contacto**: 10 funções pré-definidas
-   **Taxas de IVA**: 4 taxas configuráveis (0%, 6%, 13%, 23%)

### Gestão Financeira

-   **Contas Bancárias**: Gestão de contas da empresa com IBAN, SWIFT, multi-moeda
-   **Movimentos Bancários**: Histórico de débitos/créditos com saldo automático
-   **Conta Corrente Clientes**: Acompanhamento de débitos e créditos por cliente
-   **Faturas Fornecedores**: Gestão completa com upload de documentos e comprovativos
-   **Envio Automático de Emails**: Comprovativos de pagamento enviados por email ao fornecedor
-   **Cálculos Automáticos**: Saldos calculados e atualizados em tempo real

### Sistema de Email

-   **MailHog Configurado**: Captura emails localmente sem enviar para destinatários reais
-   **Templates Personalizados**: Emails com logo e branding da empresa
-   **Anexos Suportados**: PDF de comprovativos anexado automaticamente
-   **Testes Automatizados**: Suite completa validando todo o fluxo de envio
-   **Documentação**: Guia completo em `docs/mailhog-setup.md`
-   **Estatísticas**: Painel com totais de débitos, créditos e saldo atual

### Interface Moderna

-   Menu accordion com submenus expandíveis
-   Componentes Shadcn/ui (Form, DataTable, Badge, etc.)
-   Dark/light mode
-   Pesquisa e ordenação em DataTables
-   Mobile-first design responsivo

## 🚀 Próximos Módulos

-   [x] ~~Faturas a Fornecedores~~ ✅ Concluído (v0.12.0)
-   [ ] Faturas a Clientes
-   [ ] Propostas/Orçamentos
-   [ ] Encomendas/Vendas
-   [ ] Dashboard Analytics
-   [ ] Relatórios e Exports

## 📚 Documentação Adicional

-   **Changelog Completo:** `docs/changelog.md`
-   **Gestão de Acessos:** `docs/access-management.md` (v0.7.0)
-   **Contas Bancárias:** `docs/bank-accounts-module.md` (v0.11.0)
-   **Conta Corrente Clientes:** `docs/client-accounts-module.md` (v0.11.0)
-   **Faturas Fornecedores & Email:** `docs/mailhog-setup.md` (v0.12.0) 🆕
-   **Configuração BD:** `docs/database-config.md`
-   **Arquitetura Modular:** `docs/modular-architecture.md`

## 🧪 Testes

-   **Framework:** PHPUnit + Laravel Testing
-   **Testes Implementados:** 9 testes automatizados
-   **Asserções:** 17 asserções totais
-   **Cobertura:** 100% no fluxo de emails
-   **Suite Atual:** `SupplierInvoiceEmailTest.php`
-   **Executar Testes:**
    ```bash
    php artisan test
    # Ou específico:
    php artisan test --filter=SupplierInvoiceEmailTest
    ```

## 🔒 Segurança

-   ✅ Validação de inputs em todos os formulários (frontend + backend)
-   ✅ Sistema de permissões granular (68 permissões em 17 módulos)
-   ✅ Controlo de UI baseado em permissões (botões adaptáveis)
-   ✅ Proteção CSRF (Laravel)
-   ✅ Password hashing (bcrypt)
-   ✅ Middleware de autenticação e autorização
-   ✅ Proteção contra auto-eliminação
-   ✅ Validação de roles hierárquicos
-   ✅ Zero erros 403 desnecessários (UI inteligente)
-   ✅ Upload de ficheiros com validação de tipo e tamanho
-   ✅ Sanitização de dados antes de armazenamento
-   ✅ Foreign keys com integridade referencial

## 🛠️ Resolução de Problemas

### **MySQL não inicia no XAMPP**

-   Verificar se porta 3306 está livre
-   Reiniciar XAMPP como Administrador
-   Verificar logs em `C:\xampp\mysql\data\mysql_error.log`

### **Aplicação não carrega**

-   Confirmar que `npm run dev` está a correr
-   Verificar se Herd está instalado e ativo
-   Limpar cache: `php artisan config:clear`

### **Erro de conexão à BD**

-   Confirmar MySQL no XAMPP está ON
-   Base `gest_app` existe
-   Credenciais corretas no `.env`

### **Emails não aparecem no MailHog**

-   Verificar se MailHog está a correr: `Get-Process -Name "mailhog"`
-   Iniciar MailHog: `C:\MailHog\mailhog.exe`
-   Verificar `.env`: `MAIL_MAILER=smtp`, `MAIL_PORT=1025`
-   Limpar cache: `php artisan config:clear`
-   Ver guia completo: `docs/mailhog-setup.md`

## 📝 Documentação Adicional

-   [📋 Changelog](docs/changelog.md) - v0.12.0
-   [🏗️ Arquitetura](docs/modular-architecture.md)
-   [💾 Configuração BD](docs/database-config.md)
-   [🔐 Gestão de Acessos](docs/access-management.md)
-   [🏦 Contas Bancárias](docs/bank-accounts-module.md)
-   [💰 Conta Corrente Clientes](docs/client-accounts-module.md)

---

**Desenvolvido durante estágio em:** Novembro 2025
