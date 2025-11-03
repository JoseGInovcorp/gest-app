# 📝 Changelog — Gest-App (Sistema de Gestão Empresarial)

Registo cronológico de todas as alterações, melhorias e correções implementadas durante o desenvolvimento.

O formato segue as convenções [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) e [Semantic Versioning](https://semver.org/).

---

## [0.1.0] — 2025-11-03

### 🚀 Setup Inicial - Fundação do Projeto

**Milestone:** Configuração base completa para desenvolvimento do sistema de gestão empresarial.

#### ✨ Funcionalidades Base Implementadas

**Stack Tecnológico Configurado:**
- **Laravel 12** - Framework backend com última versão estável
- **Laravel Breeze** - Starter kit de autenticação com Vue.js
- **Laravel Fortify** - Sistema 2FA configurado e funcional
- **Vue.js 3** - Frontend SPA com Composition API
- **Inertia.js** - Bridge Laravel ↔ Vue sem necessidade de APIs
- **Tailwind CSS** - Framework CSS utility-first
- **Shadcn/ui** - Biblioteca de componentes moderna (dependências instaladas)

**Segurança Configurada:**
- ✅ **Autenticação 2FA** - Laravel Fortify com Google Authenticator
- ✅ **Rotas protegidas** - Middleware de autenticação configurado
- ✅ **CSRF Protection** - Proteção ativa em formulários
- ✅ **Sanctum** - Gestão segura de tokens API

**Estrutura de Desenvolvimento:**
- ✅ **Ambiente Herd** - Servidor local configurado (https://gest-app.test)
- ✅ **Vite Build** - Compilação frontend funcionando
- ✅ **Hot Reload** - Desenvolvimento ágil com npm run dev
- ✅ **Migrações Base** - Estrutura inicial da base de dados

#### 🏗️ Arquitetura Estabelecida

**Rotas Funcionais:**
- Sistema completo de autenticação (login, registo, 2FA)
- Dashboard base configurado
- Gestão de perfil de utilizador
- Sistema de confirmação de palavra-passe

**Componentes Base:**
- Layout de autenticação responsivo
- Sistema de modais e formulários
- Navegação SPA fluida
- Estados de loading e feedback

#### 🎨 Interface Foundation

**Design System:**
- **Shadcn/ui components** - Button, Form, Data-table (preparado)
- **Tailwind CSS** - Sistema de cores e espacamento configurado
- **Tema escuro/claro** - Preparado para implementação
- **Responsividade** - Mobile-first approach

#### 📚 Documentação Inicial

**Ficheiros Criados:**
- `README.md` - Documentação principal do projeto
- `docs/changelog.md` - Este ficheiro de histórico
- Estrutura preparada para documentação técnica

#### 🔧 Configurações Técnicas

**Base de Dados:**
- SQLite configurado por defeito (fácil desenvolvimento)
- Preparado para migração para MySQL
- Migrações Fortify executadas (tabelas 2FA)

**Desenvolvimento:**
- Package.json com dependências organizadas
- Vite configurado para Vue.js + Inertia
- Composer com packages de segurança

---

## [Próximas Versões - Roadmap]

### [0.2.0] — Sistema de Permissões e Entidades (Planeado)
- Instalação e configuração Spatie Permissions
- Criação do modelo Entidades (Clientes/Fornecedores)
- Sistema de roles e permissions básico
- CRUD de entidades com validação NIF

### [0.3.0] — Integração VIES e Contactos (Planeado)  
- API VIES para validação de NIF europeu
- Módulo de Contactos associados a entidades
- Formulários Shadcn/ui implementados
- Data tables funcionais

### [0.4.0] — Artigos e Configurações (Planeado)
- Gestão de artigos com preços e IVA
- Upload de imagens de artigos
- Configurações base (países, funções, IVA)
- Dados da empresa

### [0.5.0] — Propostas Comerciais (Planeado)
- Sistema completo de propostas
- Geração de PDF profissional
- Validação de negócio
- Estados e workflow

### [0.6.0] — Encomendas e Conversões (Planeado)
- Conversão Proposta → Encomenda
- Encomendas de fornecedores
- Agrupamento por fornecedor
- Gestão de estados

### [0.7.0] — Financeiro (Planeado)
- Faturas de fornecedor
- Sistema de pagamentos
- Comprovativos e anexos
- Notificações por email

### [0.8.0] — Calendário e Logs (Planeado)
- FullCalendar integrado
- Spatie Activity Log
- Auditoria completa
- Filtros e pesquisas

### [1.0.0] — Release Final (18 Novembro 2025)
- Todos os módulos implementados
- Testes automatizados
- Documentação completa
- Vídeo de apresentação
- Deploy em produção

---

## 🎯 Notas de Desenvolvimento

**Metodologia Adotada:**
- Desenvolvimento incremental por módulos
- Documentação contínua 
- Versionamento semântico
- Testes de cada funcionalidade
- Commits descritivos em português

**Critérios de Quality Gates:**
- Cada versão deve ser funcional independentemente
- Segurança validada em todas as funcionalidades
- Interface consistente com Shadcn/ui
- Performance adequada (< 2s load time)

---

_Desenvolvido durante estágio - Outubro/Novembro 2025_