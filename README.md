# 🏢 Gest-App — Sistema de Gestão Empresarial

> **Projeto Final de Estágio** | Desenvolvimento de um sistema completo de gestão empresarial com foco em CRM/ERP integrado para PMEs portuguesas.

<div align="center">

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js 3](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-1.x-9553E9?style=for-the-badge&logo=laravel&logoColor=white)](https://inertiajs.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)

**Status do Projeto:** 🚧 Em Desenvolvimento Ativo

**Fase Atual:** 📦 **Módulo Entidades** (Clientes/Fornecedores) 

**Entrega Final:** 📅 **18 de Novembro de 2025**

</div>

---

## 📋 Índice

-   [📖 Visão Geral](#-visão-geral)
-   [🛠️ Stack Tecnológico](#️-stack-tecnológico)
-   [🏗️ Arquitetura do Sistema](#️-arquitetura-do-sistema)
-   [🔧 Instalação](#-instalação)
-   [🚀 Como Usar](#-como-usar)
-   [📦 Módulos Implementados](#-módulos-implementados)
-   [🔐 Sistema de Permissões](#-sistema-de-permissões)
-   [📈 Roadmap de Desenvolvimento](#-roadmap-de-desenvolvimento)

---

## 📖 Visão Geral

O **Gest-App** é um sistema de gestão empresarial desenvolvido como projeto final de estágio, focado em fornecer uma solução completa e moderna para pequenas e médias empresas portuguesas. 

### 🎯 Objetivos Principais

-   **Centralização**: Unified dashboard para gestão completa do negócio
-   **Automatização**: Processos automatizados de faturação, stock e relatórios  
-   **Conformidade**: Integração com APIs portuguesas (VIES, AT) para validação fiscal
-   **Escalabilidade**: Arquitetura preparada para crescimento futuro
-   **Segurança**: 2FA, permissões granulares e encriptação de dados

### 🏢 Funcionalidades Core

-   **📊 CRM/ERP Integrado**: Gestão completa de clientes e fornecedores
-   **📄 Faturação Eletrónica**: Propostas, orçamentos e faturas automáticas
-   **📦 Gestão de Stock**: Controlo avançado de artigos e inventário
-   **💰 Financeiro**: Reconciliação bancária e relatórios fiscais
-   **📅 Calendário Empresarial**: Agendamento e gestão de tarefas
-   **🔐 Gestão de Acessos**: Sistema hierárquico de utilizadores e permissões

---

## 🛠️ Stack Tecnológico

### Backend
-   **Laravel 12** — Framework PHP moderno com ecosystem completo
-   **Laravel Fortify** — Autenticação 2FA com Google Authenticator
-   **Laravel Sanctum** — API authentication para integrações futuras
-   **Spatie Permission** — Sistema robusto de roles e permissões
-   **MySQL 8.0** — Base de dados relacional otimizada

### Frontend
-   **Vue.js 3** — Framework reativo com Composition API
-   **Inertia.js** — Modern monolith sem necessidade de APIs REST
-   **Shadcn/ui** — Componentes UI modernos e acessíveis
-   **Tailwind CSS** — Utility-first CSS framework
-   **FullCalendar** — Biblioteca avançada de calendários

### Ferramentas & Integrações
-   **Herd** — Ambiente de desenvolvimento Laravel otimizado
-   **VIES API** — Validação automática de VAT numbers europeus
-   **Git & GitHub** — Controlo de versões e colaboração
-   **Pest PHP** — Testing framework moderno

---

## 🏗️ Arquitetura do Sistema

### 🏛️ Padrões Arquitetónicos

-   **MVC Pattern** — Model-View-Controller com Inertia.js
-   **Service Layer** — Lógica de negócio encapsulada em services
-   **Repository Pattern** — Abstração de acesso a dados
-   **Event-Driven** — Sistema de eventos para auditoria e notificações

### 📁 Estrutura de Módulos

```
gest-app/
├── 📦 Core Modules/
│   ├── 👥 Entities (Clientes/Fornecedores) ✅ 
│   ├── 📞 Contacts (Contactos)
│   ├── 📦 Articles (Produtos/Serviços)
│   ├── 📄 Proposals (Orçamentos/Propostas)
│   ├── 🛒 Orders (Encomendas/Pedidos)
│   ├── 💰 Financial (Faturação/Pagamentos)
│   ├── 📅 Calendar (Calendário Empresarial)
│   └── 🔐 Access Management (Utilizadores/Permissões)
├── ⚙️ Configuration Modules/
│   ├── 🏢 Company Settings
│   ├── 💸 Tax Configuration  
│   ├── 🏷️ Categories & Tags
│   ├── 📋 Document Templates
│   ├── 🔗 API Integrations
│   ├── 📊 Reports & Analytics
│   └── 🛠️ System Settings
└── 🔒 Security & Compliance/
    ├── 🔐 2FA Authentication
    ├── 📋 Audit Logs
    ├── 🛡️ Data Encryption
    └── 🇵🇹 Portuguese Tax Compliance
```

---

## 🔧 Instalação

### Pré-requisitos
-   **PHP 8.2+** com extensões necessárias
-   **Node.js 18+** e npm/yarn
-   **MySQL 8.0+** ou MariaDB 10.4+
-   **Composer 2.0+**

### Setup do Projeto

```bash
# 1. Clonar repositório
git clone https://github.com/JoseGInovcorp/gest-app.git
cd gest-app

# 2. Instalar dependências PHP
composer install

# 3. Instalar dependências Node.js
npm install --legacy-peer-deps

# 4. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 5. Configurar base de dados
php artisan migrate
php artisan db:seed

# 6. Build assets
npm run build

# 7. Iniciar servidor
php artisan serve
```

### Acesso Inicial
-   **URL:** `https://gest-app.test` (Herd) ou `http://localhost:8000`
-   **Super Admin:** `admin@gest-app.com` / `password`
-   **2FA:** Configurar Google Authenticator no primeiro login

---

## 🚀 Como Usar

### 🔐 Sistema de Autenticação

1. **Login Inicial**: Usar credenciais de super admin
2. **Configurar 2FA**: Scan QR code com Google Authenticator
3. **Criar Utilizadores**: Gerir através do módulo Access Management
4. **Atribuir Roles**: 6 níveis hierárquicos disponíveis

### 👥 Gestão de Entidades

1. **Aceder ao módulo**: Menu principal → Entidades
2. **Criar Cliente/Fornecedor**: Botão "Nova Entidade"
3. **Validação VIES**: VAT numbers UE validados automaticamente
4. **Gestão Completa**: CRUD completo com soft deletes

---

## 📦 Módulos Implementados

### ✅ **Entidades** (Clientes/Fornecedores) — **COMPLETO**

**Funcionalidades Implementadas:**
-   ✅ **CRUD Completo**: Create, Read, Update, Delete (soft deletes)
-   ✅ **Validação VIES**: Integração com API europeia para VAT validation
-   ✅ **Filtros Avançados**: Por tipo, status, pesquisa textual
-   ✅ **Informação Fiscal**: NIF, VAT, país, validação automática
-   ✅ **Moradas Múltiplas**: Principal e faturação separadas
-   ✅ **Dados Comerciais**: Limite crédito, condições pagamento, descontos
-   ✅ **Auditoria**: Created/updated by tracking
-   ✅ **Permissões**: Controlo granular por role

**Funcionalidades Técnicas:**
-   ✅ **Modelo Eloquent**: 40+ campos otimizados com relacionamentos
-   ✅ **Migração Robusta**: Índices, foreign keys, constraints
-   ✅ **Controlador RESTful**: Endpoints completos com validação
-   ✅ **Serviço VIES**: Classe dedicada para integração API
-   ✅ **Middleware Permissões**: Proteção automática de rotas

**Países VIES Suportados:** 🇦🇹🇧🇪🇧🇬🇨🇾🇨🇿🇩🇪🇩🇰🇪🇪🇬🇷🇪🇸🇫🇮🇫🇷🇭🇷🇭🇺🇮🇪🇮🇹🇱🇹🇱🇺🇱🇻🇲🇹🇳🇱🇵🇱🇵🇹🇷🇴🇸🇪🇸🇮🇸🇰

### 🚧 **Em Desenvolvimento**
-   📞 **Contacts** — Próxima fase
-   📦 **Articles** — A seguir
-   📄 **Proposals** — Fase 2

---

## 🔐 Sistema de Permissões

### 🏛️ Hierarquia de Roles

```
👑 Super Admin
├── 🔧 Administrator  
├── 📊 Manager
├── 💼 Sales Representative
├── 💰 Financial Manager  
├── 📦 Warehouse Manager
└── 👤 Employee
```

### 🛡️ Permissões por Módulo

**Entities (Implementado):**
-   `entities.view` — Visualizar lista e detalhes
-   `entities.create` — Criar novas entidades  
-   `entities.edit` — Editar entidades existentes
-   `entities.delete` — Eliminar entidades (soft delete)
-   `entities.export` — Exportar dados

**Sistema Escalável:**
-   **70+ permissões** granulares cobrindo todos os módulos
-   **Middleware automático** para proteção de rotas
-   **Métodos auxiliares** no User model para verificações

---

## 📈 Roadmap de Desenvolvimento

### 📅 **Fase 1** — Fundação ✅ **CONCLUÍDA**
-   ✅ Setup Laravel 12 + Vue.js 3 + Inertia.js
-   ✅ Sistema de autenticação 2FA
-   ✅ Sistema de permissões (Spatie)
-   ✅ Módulo Entidades completo
-   ✅ Integração VIES API
-   ✅ Documentação base

### 🚧 **Fase 2** — Módulos Core (Em Curso)
-   📞 **Contacts** — Sistema de contactos
-   📦 **Articles** — Gestão de produtos/serviços
-   📄 **Proposals** — Orçamentos e propostas
-   🛒 **Orders** — Sistema de encomendas

### 📅 **Fase 3** — Financeiro & Relatórios
-   💰 **Financial** — Faturação e pagamentos
-   📊 **Reports** — Dashboard e analytics
-   📅 **Calendar** — Calendário empresarial
-   🔗 **Integrations** — APIs externas (AT, Bancos)

### 📅 **Fase 4** — Otimização & Deploy
-   🚀 **Performance** — Otimizações e caching
-   🛡️ **Security** — Hardening e compliance
-   📱 **Mobile** — Responsividade avançada
-   ☁️ **Production** — Deploy e monitoring

---

**📅 Timeline de Entrega: 18 de Novembro de 2025**

**👨‍💻 Desenvolvido por:** [José Gil] — Estágio Final | Inovcorp  
**📧 Contacto:** [jose.gil@inovcorp.com]  
**📁 Repositório:** [https://github.com/JoseGInovcorp/gest-app](https://github.com/JoseGInovcorp/gest-app)

---

<div align="center">
<i>🔥 Sistema desenvolvido com paixão e dedicação | Powered by Laravel & Vue.js 🔥</i>
</div>