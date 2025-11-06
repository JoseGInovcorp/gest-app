# 📝 Changelog — Gest-App

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
