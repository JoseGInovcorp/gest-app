# 🏗️ Arquitetura Modular — Gest-App

## 📋 Visão Geral

O **Gest-App** é desenvolvido seguindo uma **arquitetura modular** que permite implementação, validação e entrega incremental de funcionalidades. Cada módulo é independente mas integrado no sistema geral.

### 🎯 Benefícios da Abordagem Modular

-   **✅ Validação Incremental** — Cada módulo é validado individualmente antes de prosseguir
-   **✅ Controlo de Qualidade** — Testes e correções focados por módulo
-   **✅ Entrega Estruturada** — Progresso visível e mensurável
-   **✅ Manutenibilidade** — Código organizado e de fácil manutenção
-   **✅ Escalabilidade** — Facilita adição de novos módulos futuramente

---

## 📊 Estado Atual dos Módulos

### 🎯 Progresso Geral: **15%** (2 de 16+ módulos concluídos)

```
┌─────────────────────────────────────────────────────────────┐
│                    MÓDULOS IMPLEMENTADOS                    │
├─────────────────────────────────────────────────────────────┤
│  🏗️  FUNDAÇÃO & SETUP   ████████████████████ 100% ✅       │
│  🎨  MÓDULO ENTIDADES   ████████████████████ 100% ✅       │
│  👥  MÓDULO CONTACTOS   ████████████████████ 100% ✅       │
├─────────────────────────────────────────────────────────────┤
│                    PRÓXIMOS MÓDULOS                        │
├─────────────────────────────────────────────────────────────┤
│  📦  Artigos            ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  📋  Propostas          ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  📅  Calendário         ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  🛒  Encomendas         ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  💰  Financeiro         ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  🔐  Permissões         ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  📁  Arquivo Digital    ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  ⚙️   Configurações     ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  📊  Logs & Auditoria  ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  📱  Notificações       ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  📈  Analytics          ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  🔧  Integrações        ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  📱  Mobile App         ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
│  🚀  Deploy & CI/CD     ░░░░░░░░░░░░░░░░░░░░   0% ⏳         │
└─────────────────────────────────────────────────────────────┘
```

---

## ✅ Módulos Implementados

### 🏗️ **MÓDULO 0: Fundação & Setup** (100% ✅)

#### **Stack Tecnológico Estabelecido**

-   **Laravel 12** — Framework backend com Eloquent ORM
-   **Vue.js 3** — Framework frontend com Composition API
-   **Inertia.js** — Bridge SPA sem API complexa
-   **Shadcn/ui** — Biblioteca de componentes moderna
-   **Tailwind CSS** — Framework CSS utility-first
-   **SQLite** — Base de dados para desenvolvimento

#### **Infraestrutura Base**

-   **Authentication** — Laravel Fortify com 2FA
-   **Layout System** — Layout responsivo com menu lateral
-   **Routing** — Estrutura de rotas organizadas
-   **Asset Building** — Vite configurado para desenvolvimento

---

### 🎨 **MÓDULO 1: Entidades (Clientes/Fornecedores)** (100% ✅)

#### **Arquitetura Unificada**

-   **EntityController** — Controller único com contexto (clients/suppliers)
-   **Entity Model** — Modelo unificado com scopes para separação
-   **Filtragem Contextual** — Separação transparente por tipo

#### **Funcionalidades Core Implementadas**

1. **✅ Validação NIF única** — Real-time validation com backend API
2. **✅ Sistema numeração incremental** — Auto-cálculo e pré-preenchimento
3. **✅ Integração VIES** — Auto-fill dados empresas UE (28 países)
4. **✅ Tabela países** — Migration, Model, Seeder com flags emoji
5. **✅ Data Table Shadcn/ui** — Colunas: NIF, Nome, Telefone, Telemóvel, Website, Email

#### **Componentes Frontend**

-   `resources/js/Pages/Entities/Index.vue` — Listagem com filtros
-   `resources/js/Pages/Entities/Create.vue` — Formulário criação
-   `resources/js/Pages/Entities/Edit.vue` — Formulário edição
-   `resources/js/Components/EntitiesDataTableNew.vue` — Tabela dados

#### **Backend Files**

-   `app/Http/Controllers/EntityController.php` — CRUD completo
-   `app/Models/Entity.php` — Modelo com relacionamentos
-   `app/Models/Country.php` — Países com flags
-   `database/migrations/*_create_entities_table.php` — Schema
-   `database/seeders/CountriesSeeder.php` — 14 países base

---

### 👥 **MÓDULO 2: Contactos** (100% ✅)

#### **Sistema Relacional**

-   **Contact Model** — Relacionamento BelongsTo com Entity
-   **ContactController** — CRUD completo com validações
-   **Numeração Contextual** — Incremental por entidade

#### **Campos & Validações**

-   **Número** — Auto-incremental único por entidade
-   **Dados Pessoais** — Nome, apelido, função (obrigatórios)
-   **Contactos** — Telefone, telemóvel, email com validações
-   **RGPD** — Consentimento obrigatório para proteção dados
-   **Estado** — Ativo/Inativo com soft delete

#### **Interface Moderna**

-   **DataTable Colunas** — Nome, Apelido, Função, Entidade, Telefone, Telemóvel, Email
-   **Componentes Shadcn/ui** — Form, FormField, Input, Select, Table completos
-   **Acessores Portuguese** — Compatibilidade getNomeAttribute(), etc.

#### **Backend Files**

-   `app/Http/Controllers/ContactController.php` — CRUD completo
-   `app/Models/Contact.php` — Modelo com relacionamentos e acessores
-   `database/migrations/*_create_contacts_table.php` — Schema completo

#### **Frontend Files**

-   `resources/js/Pages/Contacts/Index.vue` — Listagem com filtros
-   `resources/js/Pages/Contacts/Create.vue` — Formulário criação
-   `resources/js/Pages/Contacts/Edit.vue` — Formulário edição
-   `resources/js/Components/ContactsDataTableNew.vue` — Tabela dados

---

## 🔄 Metodologia de Implementação

### 📋 **Processo por Módulo**

1. **🎯 Análise de Requisitos**

    - Definição clara do âmbito do módulo
    - Identificação de dependências
    - Especificação de campos e validações

2. **🏗️ Implementação Backend**

    - Migrations e models
    - Controllers com CRUD completo
    - Validações e business logic
    - Testes unitários

3. **🎨 Implementação Frontend**

    - Páginas Vue.js com Inertia
    - Componentes Shadcn/ui
    - Formulários com validação real-time
    - DataTables com filtros e paginação

4. **✅ Validação & Testes**

    - Testes funcionais completos
    - Validação contra especificações
    - Correção de bugs e refinamentos
    - Aprovação para produção

5. **📚 Documentação**
    - Atualização README e changelog
    - Documentação técnica do módulo
    - Preparação para próximo módulo

---

## 🚀 Próximos Módulos Planeados

### 📦 **MÓDULO 3: Artigos (Produtos/Serviços)**

-   Gestão completa de catálogo
-   Categorias, stocks, preços
-   Códigos de barras e referências
-   Imagens e documentos anexos

### 📋 **MÓDULO 4: Propostas Comerciais**

-   Criação de orçamentos
-   Templates personalizáveis
-   Aprovação e versionamento
-   Conversão em encomendas

### 📅 **MÓDULO 5: Calendário & Tarefas**

-   Agenda empresarial
-   Eventos e reuniões
-   Notificações automáticas
-   Integração com contactos

### 🛒 **MÓDULO 6: Encomendas & Vendas**

-   Gestão do pipeline de vendas
-   Estados de encomenda
-   Faturação automática
-   Integração com stock

---

## 🎯 Timeline de Entrega

**Deadline:** 📅 **18 de Novembro de 2025** (14 dias restantes)

**Estratégia:** Implementação de 1 módulo por dia com validação incremental

```
Semana 1 (04-10 Nov): Módulos 3-8  (Artigos → Encomendas)
Semana 2 (11-17 Nov): Módulos 9-16 (Financeiro → Deploy)
Dia 18 Nov: Entrega final e apresentação
```

---

## 📋 Standards de Desenvolvimento

### 🎨 **Frontend Standards**

-   **Shadcn/ui Components** obrigatórios para consistência
-   **Vue.js 3 Composition API** para reatividade
-   **Tailwind CSS** para styling responsivo
-   **Validação Real-time** em todos os formulários

### 🏗️ **Backend Standards**

-   **Laravel 12** convenções e best practices
-   **Eloquent ORM** para relacionamentos
-   **Form Request Validation** para todas as validações
-   **Resource Controllers** para CRUD padronizado

### 📊 **Database Standards**

-   **Migrations versionadas** com rollback
-   **Foreign keys** com cascade appropriado
-   **Soft deletes** para auditoria
-   **Timestamps** automáticos (created_at, updated_at)
-   **User auditing** (created_by, updated_by)

---

## 🔧 Ferramentas de Desenvolvimento

### 💻 **Environment Setup**

-   **Laravel Herd** — Ambiente local otimizado
-   **Vite** — Build tool com HMR
-   **SQLite** — Base de dados desenvolvimento
-   **VS Code** — Editor com extensões Laravel/Vue

### 🧪 **Testing & Quality**

-   **Pest PHP** — Testes backend
-   **Vitest** — Testes frontend
-   **Laravel Pint** — Code styling
-   **PHP Stan** — Static analysis

---

_Última atualização: 04 de Novembro de 2025_
