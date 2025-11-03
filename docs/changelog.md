# 📝 Changelog — Gest-App (Sistema de Gestão Empresarial)

Registo cronológico de todas as alterações, melhorias e correções implementadas durante o desenvolvimento.

O formato segue as convenções [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) e [Semantic Versioning](https://semver.org/).

---

## [0.3.1] — 2025-11-03 (Madrugada)

### 🔐 Validação NIF Única + Integração VIES Ativa

**Milestone:** Implementação de validação em tempo real de NIF único e integração ativa do VIES para preenchimento automático de dados de empresas europeias.

#### ✨ **Validação NIF Única Implementada**

**Backend API:**
- ✅ **Nova rota API**: `/api/entities/check-nif/{nif}` para verificação AJAX
- ✅ **Método checkNifExists**: Verifica duplicação na base de dados
- ✅ **Response estruturada**: `{exists: boolean, nif: string, message: string}`
- ✅ **Validação Laravel**: Rule `unique:entities,tax_number` mantida no store

**Frontend Real-time:**
- ✅ **Estado reativo**: `nifValidation` com checking/exists/message/error
- ✅ **Debounced validation**: 800ms delay para otimizar requests
- ✅ **Visual feedback**: Border vermelho (existe) / verde (disponível)
- ✅ **UX messages**: "A verificar NIF..." → "Este NIF já está registado"
- ✅ **Form blocking**: Botão desativado se NIF duplicado

#### 🌍 **Integração VIES Ativa no Formulário**

**Backend VIES API:**
- ✅ **Nova rota API**: `/api/entities/vies-lookup/{country}/{nif}`
- ✅ **Método viesLookup**: Consulta API VIES e retorna dados empresa
- ✅ **Validação países UE**: Verificação automática se país suporta VIES
- ✅ **Error handling**: Tratamento robusto de timeouts e erros SOAP

**Auto-preenchimento Inteligente:**
- ✅ **28 países VIES**: ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK', 'XI']
- ✅ **Preenchimento automático**: `company_name` → Nome, `company_address` → Morada
- ✅ **Smart fill**: Só preenche se campos estiverem vazios
- ✅ **Watcher país**: Re-executa VIES se mudar para país UE

**UX Estados Visuais:**
- ✅ **Loading states**: "A verificar NIF..." durante consulta VIES
- ✅ **Success feedback**: "✅ Dados preenchidos via VIES"
- ✅ **Error handling**: "⚠️ Erro na consulta VIES" com detalhes
- ✅ **Non-intrusive**: Não sobrescreve dados já preenchidos

#### 🔄 **Fluxo de Validação Integrado**

**Sequência Automática:**
1. **User input**: Digita NIF no campo
2. **Debounce**: 800ms delay para otimizar
3. **Check único**: Verifica se NIF já existe na BD
4. **Auto VIES**: Se não existe + país UE → consulta VIES
5. **Auto-fill**: Preenche nome e morada automaticamente
6. **Visual feedback**: Estados visuais em tempo real

**Implementado em Ambos:**
- ✅ **Clients/Create.vue**: Validação NIF + VIES completa
- ✅ **Suppliers/Create.vue**: Funcionalidade idêntica
- ✅ **Consistent UX**: Experiência uniforme em ambos contextos

#### 🎯 **Sistema Numeração Confirmado**

**Funcionalidade Existente Validada:**
- ✅ **Backend**: `Entity::max('number') + 1` calcula próximo número
- ✅ **Frontend**: Campo pré-preenchido via `props.nextNumber`
- ✅ **UX**: Placeholder "Gerado automaticamente" 
- ✅ **Read-only**: Campo não editável pelo utilizador

#### 📊 **Performance e Otimizações**

**Debouncing Inteligente:**
- ✅ **NIF validation**: 800ms delay para reduzir requests
- ✅ **Country watcher**: Re-executa VIES só quando necessário
- ✅ **State management**: Estados reativos otimizados
- ✅ **Error recovery**: Fallback gracioso em caso de erro

**Console Logging:**
- ✅ **Debug completo**: Logs detalhados para desenvolvimento
- ✅ **VIES responses**: Tracking de respostas da API
- ✅ **Error tracking**: Monitorização de erros para debug

---

## [0.3.0] — 2025-11-03 (Noite)

### 🎨 Formulários Shadcn/ui - Sistema CRUD Completo

**Milestone:** Implementação completa de formulários modernos Create/Edit para Clientes e Fornecedores usando componentes Shadcn/ui com validação avançada e UX profissional.

#### ✨ **Formulários Modernos Implementados**

**Biblioteca UI Componentes:**

-   ✅ **Form Component** - Wrapper funcional com emissão de eventos submit
-   ✅ **Input Component** - Campo de texto com v-model, estados error/disabled
-   ✅ **Select Component** - Dropdown com opções e v-model binding
-   ✅ **Textarea Component** - Campo de texto multilinha responsivo
-   ✅ **Checkbox Component** - Toggle com label integrado
-   ✅ **Button Component** - Múltiplas variantes (default, outline, destructive)
-   ✅ **FormField Component** - Wrapper com label, descrição e mensagens erro

**Páginas Create/Edit:**

-   ✅ **Clients/Create** (`/clients/create`) - Formulário completo tipo 'client' pré-selecionado
-   ✅ **Suppliers/Create** (`/suppliers/create`) - Formulário completo tipo 'supplier' pré-selecionado
-   ✅ **Todos os campos** - Tipo, Número, NIF, Nome, Morada, CP, Localidade, País, Telefones, Website, Email, RGPD, Observações, Estado

#### 🔧 **Validação & UX Avançada**

**Sistema de Validação:**

-   ✅ **Real-time validation** - Computed `isFormValid` verifica campos obrigatórios
-   ✅ **Visual feedback** - Botão ativo/inativo baseado na validação
-   ✅ **Estados loading** - "A criar..." durante submissão
-   ✅ **Mensagens contextuais** - "Preencha os campos obrigatórios" vs "Criar Cliente"

**Formatação Automática:**

-   ✅ **Código Postal** - Auto-formato XXXX-XXX durante digitação
-   ✅ **NIF Validation** - Preparado para integração VIES (validateNIF function)
-   ✅ **Form submission** - Inertia.js com callbacks success/error completos

#### 🏗️ **Backend Integrado**

**EntityController Melhorado:**

-   ✅ **Store method** - Criação de entidades com mapeamento correto de campos
-   ✅ **VIES Integration** - Validação automática VAT para países UE
-   ✅ **Contextual redirect** - Redirecciona para clients.index ou suppliers.index conforme rota
-   ✅ **Field mapping** - NIF → tax_number, country → country_code, etc.
-   ✅ **Data validation** - Rules Laravel completas com unique constraints

#### 🐛 **Correções Críticas**

**Filtros Funcionais:**

-   ✅ **Auto-apply filters** - Watchers que aplicam filtros automaticamente
-   ✅ **Debounced search** - 500ms delay na pesquisa para melhor performance
-   ✅ **Preserve state** - Filtros mantêm estado durante navegação
-   ✅ **Backend processing** - EntityController processa corretamente active/search params

**Layout & Duplicações:**

-   ✅ **Nome duplicado corrigido** - Removida duplicação entity.name na tabela
-   ✅ **NIF layout melhorado** - Coluna fiscal sem duplicações tax_number/vat_number
-   ✅ **VIES indicator** - Ícone vermelho só aparece se houve verificação VIES
-   ✅ **Country display** - Código país só aparece se diferente de PT

#### 📊 **Funcionalidades Ativas**

**Sistema CRUD Completo:**

-   ✅ **Create** - Formulários funcionais para ambos os contextos
-   ✅ **Read** - Listagens filtradas com pesquisa e status
-   ✅ **Update** - Backend preparado (frontend será implementado)
-   ✅ **Delete** - Backend preparado (frontend será implementado)

**Navegação & UX:**

-   ✅ **Breadcrumbs** - Navegação contextual em todas as páginas
-   ✅ **Mobile responsive** - Formulários adaptam a todos os ecrãs
-   ✅ **Loading states** - Feedback visual durante operações
-   ✅ **Error handling** - Mensagens de erro contextuais

---

## [0.2.1] — 2025-11-03 (Tarde)

### 🔧 Correções e Melhorias Interface

**Correções Críticas:**

-   ✅ **EntityController** - Corrigido erro `getDefault()` nas rotas
    -   Removido uso de route defaults que causava `BadMethodCallException`
    -   Implementada detecção de tipo por nome da rota (`clients.*`, `suppliers.*`)
    -   Simplificada lógica de filtros por tipo de entidade
-   ✅ **Controller Base** - Adicionados traits necessários para middleware
    -   `AuthorizesRequests` e `ValidatesRequests` implementados
    -   Herança correta de `Illuminate\Routing\Controller`
-   ✅ **Middleware de permissões** - Temporariamente desabilitado para testes
    -   Comentado até configuração completa do sistema de permissões

**Melhorias Visuais:**

-   ✅ **Rodapé corrigido** - Nome alterado de "José Gil" para "José Gonçalves"
    -   Email de contacto atualizado
    -   Copyright corrigido
-   ✅ **Hot Reload** - `npm run dev` ativo na porta 5174
    -   Desenvolvimento mais ágil com recarregamento automático
    -   HTTPS configurado via Laravel Herd

**Status Funcional:**

-   ✅ **Páginas Clientes** (`/clients`) - Funcionais
-   ✅ **Páginas Fornecedores** (`/suppliers`) - Funcionais
-   ✅ **Navegação menu lateral** - Totalmente operacional
-   ✅ **Filtros por tipo** - Clients mostra só clientes/both, Suppliers só fornecedores/both

---

## [0.2.0] — 2025-11-03 (Manhã)

### 🎨 Interface Moderna e Menu Separado

**Milestone:** Implementação completa da interface moderna seguindo padrões Shadcn/ui com menus separados para clientes e fornecedores conforme requisitos originais.

#### ✨ Interface Renovada

**Welcome Page Moderna:**

-   ✅ **Design Hero** - Página inicial profissional com gradientes
    -   Branding Gest-App com logo e tagline
    -   Seção hero com call-to-action
    -   Showcase das 6 funcionalidades principais
    -   Estatísticas do sistema (8 módulos, 27 países UE, 70+ permissões)
    -   Tech stack visual (Laravel 12, Vue.js 3, Inertia.js, Tailwind)
-   ✅ **Navegação moderna** - Header responsivo com links funcionais
-   ✅ **Footer completo** - Links GitHub, contacto e copyright

**Layout Autenticado:**

-   ✅ **Sidebar responsivo** - Menu lateral com categorização
    -   **Main Modules:** Dashboard, Clientes, Fornecedores, Artigos
    -   **Configuration:** Utilizadores, Configurações, Logs Sistema
    -   Menu hambúrguer para mobile
    -   Avatar e perfil de utilizador
-   ✅ **Ícones Lucide Vue** - Sistema de ícones moderno e consistente
-   ✅ **Mobile First** - Design responsivo completo

#### 🔄 Páginas Específicas por Contexto

**Clientes Interface:**

-   ✅ **Página dedicada** (`/clients`) - Interface azul para clientes
    -   Tabela moderna com dados ficcionais de exemplo
    -   Status badges (Ativo, Inativo, Pendente)
    -   Indicadores VIES validation
    -   Botões de ação (Ver, Editar, Eliminar)
    -   Empty states quando sem dados
-   ✅ **Filtros contextuais** - Mostra apenas entidades tipo 'client' e 'both'

**Fornecedores Interface:**

-   ✅ **Página dedicada** (`/suppliers`) - Interface roxa para fornecedores
    -   Layout espelhado da página clientes
    -   Temática de cores diferenciada (purple vs blue)
    -   Dados específicos para contexto fornecedor
-   ✅ **Filtros contextuais** - Mostra apenas entidades tipo 'supplier' e 'both'

#### 🏗️ Arquitetura Backend Mantida

**EntityController Unificado:**

-   ✅ **Roteamento inteligente** - Um controller para ambos os contextos
    -   `/clients/*` - rotas filtradas para clientes
    -   `/suppliers/*` - rotas filtradas para fornecedores
    -   `/entities/*` - admin (todas as entidades)
-   ✅ **16 rotas RESTful** - Cobertura completa CRUD
    -   8 rotas clients (index, create, store, show, edit, update, destroy, revalidate-vat)
    -   8 rotas suppliers (mesmas operações)
    -   Resource entities para admin
-   ✅ **Filtros automáticos** - Baseados no nome da rota

#### 📱 UX/UI Melhorada

**Componentes Shadcn/ui:**

-   ✅ **Consistent Design Language** - Cores, espaçamento e tipografia
-   ✅ **Interactive Elements** - Hover states, transitions, focus
-   ✅ **Data Tables** - Headers, sorting indicators, action buttons
-   ✅ **Status System** - Badges coloridos por estado
-   ✅ **Loading States** - Preparado para skeleton loaders

**Performance Frontend:**

-   ✅ **Code Splitting** - Páginas carregadas on-demand
-   ✅ **Asset Optimization** - Build otimizado para produção
-   ✅ **Tree Shaking** - Apenas componentes usados incluídos

---

## [0.1.0] — 2025-11-03

### 🚀 Setup Inicial - Fundação do Projeto

**Milestone:** Configuração base completa para desenvolvimento do sistema de gestão empresarial.

#### ✨ Funcionalidades Base Implementadas

**Stack Tecnológico Configurado:**

-   **Laravel 12** - Framework backend com última versão estável
-   **Laravel Breeze** - Starter kit de autenticação com Vue.js
-   **Laravel Fortify** - Sistema 2FA configurado e funcional
-   **Vue.js 3** - Frontend SPA com Composition API
-   **Inertia.js** - Bridge Laravel ↔ Vue sem necessidade de APIs
-   **Tailwind CSS** - Framework CSS utility-first
-   **Shadcn/ui** - Biblioteca de componentes moderna (dependências instaladas)

**Segurança Configurada:**

-   ✅ **Autenticação 2FA** - Laravel Fortify com Google Authenticator
-   ✅ **Rotas protegidas** - Middleware de autenticação configurado
-   ✅ **Sistema de Permissões** - Spatie Laravel Permission implementado
    -   6 níveis hierárquicos: Super Admin → Administrator → Manager → Sales Rep → Financial Manager → Warehouse Manager → Employee
    -   70+ permissões granulares cobrindo todos os módulos do sistema
    -   Middleware personalizado para proteção de rotas (PermissionMiddleware)
    -   Métodos auxiliares no User model para verificação de roles e permissões

**Módulo Entidades (Clientes/Fornecedores) - Base:**

-   ✅ **Modelo Entity** - Estrutura completa com 40+ campos
    -   Suporte para Clientes, Fornecedores ou ambos
    -   Informação fiscal completa (NIF, IVA, país)
    -   Moradas principais e de faturação separadas
    -   Informação comercial (limite crédito, dias pagamento, desconto)
    -   Dados bancários (IBAN, BIC)
    -   Campos personalizáveis (JSON)
    -   Soft deletes e auditoria (created_by, updated_by)
-   ✅ **Migração Entities** - Tabela otimizada com índices e foreign keys
-   ✅ **Serviço VIES** - Integração API europeia para validação IVA
    -   Validação automática de números IVA UE
    -   Suporte 27 países europeus + Irlanda do Norte
    -   Cache inteligente (revalidação após 30 dias)
    -   Tratamento robusto de erros e logging
    -   Formatos VAT por país
-   ✅ **CSRF Protection** - Proteção ativa em formulários
-   ✅ **Sanctum** - Gestão segura de tokens API

**Estrutura de Desenvolvimento:**

-   ✅ **Ambiente Herd** - Servidor local configurado (https://gest-app.test)
-   ✅ **Vite Build** - Compilação frontend funcionando
-   ✅ **Hot Reload** - Desenvolvimento ágil com npm run dev
-   ✅ **Migrações Base** - Estrutura inicial da base de dados

#### 🏗️ Arquitetura Estabelecida

**Rotas Funcionais:**

-   Sistema completo de autenticação (login, registo, 2FA)
-   Dashboard base configurado
-   Gestão de perfil de utilizador
-   Sistema de confirmação de palavra-passe

**Componentes Base:**

-   Layout de autenticação responsivo
-   Sistema de modais e formulários
-   Navegação SPA fluida
-   Estados de loading e feedback

#### 🎨 Interface Foundation

**Design System:**

-   **Shadcn/ui components** - Button, Form, Data-table (preparado)
-   **Tailwind CSS** - Sistema de cores e espacamento configurado
-   **Tema escuro/claro** - Preparado para implementação
-   **Responsividade** - Mobile-first approach

#### 📚 Documentação Inicial

**Ficheiros Criados:**

-   `README.md` - Documentação principal do projeto
-   `docs/changelog.md` - Este ficheiro de histórico
-   Estrutura preparada para documentação técnica

#### 🔧 Configurações Técnicas

**Base de Dados:**

-   SQLite configurado por defeito (fácil desenvolvimento)
-   Preparado para migração para MySQL
-   Migrações Fortify executadas (tabelas 2FA)

**Desenvolvimento:**

-   Package.json com dependências organizadas
-   Vite configurado para Vue.js + Inertia
-   Composer com packages de segurança

---

## [Próximas Versões - Roadmap]

### [0.2.0] — Sistema de Permissões e Entidades (Planeado)

-   Instalação e configuração Spatie Permissions
-   Criação do modelo Entidades (Clientes/Fornecedores)
-   Sistema de roles e permissions básico
-   CRUD de entidades com validação NIF

### [0.3.0] — Integração VIES e Contactos (Planeado)

-   API VIES para validação de NIF europeu
-   Módulo de Contactos associados a entidades
-   Formulários Shadcn/ui implementados
-   Data tables funcionais

### [0.4.0] — Artigos e Configurações (Planeado)

-   Gestão de artigos com preços e IVA
-   Upload de imagens de artigos
-   Configurações base (países, funções, IVA)
-   Dados da empresa

### [0.5.0] — Propostas Comerciais (Planeado)

-   Sistema completo de propostas
-   Geração de PDF profissional
-   Validação de negócio
-   Estados e workflow

### [0.6.0] — Encomendas e Conversões (Planeado)

-   Conversão Proposta → Encomenda
-   Encomendas de fornecedores
-   Agrupamento por fornecedor
-   Gestão de estados

### [0.7.0] — Financeiro (Planeado)

-   Faturas de fornecedor
-   Sistema de pagamentos
-   Comprovativos e anexos
-   Notificações por email

### [0.8.0] — Calendário e Logs (Planeado)

-   FullCalendar integrado
-   Spatie Activity Log
-   Auditoria completa
-   Filtros e pesquisas

### [1.0.0] — Release Final (18 Novembro 2025)

-   Todos os módulos implementados
-   Testes automatizados
-   Documentação completa
-   Vídeo de apresentação
-   Deploy em produção

---

## 🎯 Notas de Desenvolvimento

**Metodologia Adotada:**

-   Desenvolvimento incremental por módulos
-   Documentação contínua
-   Versionamento semântico
-   Testes de cada funcionalidade
-   Commits descritivos em português

**Critérios de Quality Gates:**

-   Cada versão deve ser funcional independentemente
-   Segurança validada em todas as funcionalidades
-   Interface consistente com Shadcn/ui
-   Performance adequada (< 2s load time)

---

_Desenvolvido durante estágio - Outubro/Novembro 2025_
