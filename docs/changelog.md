# 📝 Changelog — Gest-App

---

## [0.4.5] — 2025-11-05

### Correção Página Welcome

-   Corrigida funcionalidade dos botões Login/Registo
-   Utilizados componentes Link do Inertia.js em vez de tags HTML
-   Adicionado z-index e pointer-events para melhor interatividade
-   Navegação SPA agora funciona corretamente

---

## [0.4.4] — 2025-11-04

### Correção Base de Dados MySQL

-   Configuração alterada para MySQL (conforme enunciado)
-   Atualizado .env.example e .env para usar MySQL
-   Base de dados: `gest_app` em vez de SQLite
-   Instruções de instalação atualizadas

---

## [0.4.3] — 2025-11-04

### Correção Tabela Contactos

-   Corrigida exibição de dados na tabela (apenas aparecia email)
-   Adicionado array `$appends` no Model Contact
-   Corrigida referência `entity.nome` → `entity.name`
-   Todas as colunas agora funcionam corretamente

---

## [0.4.2] — 2025-11-04

### Menu Accordion

-   Implementado menu lateral com secções expandíveis
-   3 grupos: Financeiro, Gestão Acessos, Configurações
-   Animações CSS suaves
-   Funciona em desktop e mobile

---

## [0.4.0] — 2025-11-04

### Módulo Contactos

-   Sistema completo de contactos para entidades
-   CRUD completo com interface moderna
-   Relacionamentos com clientes/fornecedores
-   Validações e consentimento RGPD

---

## [0.3.0] — 2025-11-03

### Módulo Entidades

-   Sistema de clientes e fornecedores
-   Validação NIF e integração VIES
-   DataTable com Shadcn/ui
-   Numeração automática

---

## [0.2.0] — 2025-11-02

### Setup Base

-   Laravel 12 + Vue.js 3 + Inertia.js
-   Autenticação e layout base
-   Configuração Tailwind CSS + Shadcn/ui
