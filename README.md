# 🏢 Gest-App — Sistema de Gestão Empresarial

> Projeto Final de Estágio | Sistema de gestão empresarial para PMEs

## 📊 Status do Projeto

**Versão:** v0.8.0  
**Progresso:** 47% (9 de 19+ módulos)  
**Entrega:** 18 Nov 2025  
**BD:** ✅ MySQL configurado e funcionando  
**Welcome:** ✅ Navegação funcional  
**Segurança:** ✅ Sistema de permissões completo  
**Logs:** ✅ Histórico de atividades completo

## 🛠️ Tecnologias

-   **Backend:** Laravel 12
-   **Frontend:** Vue.js 3 + Inertia.js
-   **UI:** Tailwind CSS + Shadcn/ui
-   **BD:** MySQL
-   **ACL:** Spatie Laravel Permission v6.23.0

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
-   **Permissões:** Grupos com ativação por menu (12 módulos × 4 ações CRUD = 48 permissões)
-   **4 Roles Hierárquicos:** Super Admin, Administrador, Gestor, Utilizador
-   **Segurança:** Proteção contra auto-eliminação e eliminação de Super Admin
-   **UI Simplificada:** 1 checkbox por menu ativa 4 permissões CRUD automaticamente
-   **Package:** Spatie Laravel Permission v6.23.0
-   **Documentação:** Ver `docs/access-management.md` para detalhes técnicos

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

### ✅ Interface & UX

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

### Gestão de Acessos (v0.7.0)

-   **Utilizadores:** Criação, edição, ativação/desativação
-   **Grupos de Permissões:** Interface simplificada (1 checkbox = 4 permissões CRUD)
-   **Roles Predefinidos:**
    -   **Super Admin:** Controle total (96 perms)
    -   **Administrador:** Gestão operacional (85 perms, sem users/roles)
    -   **Gestor:** Operações principais (20 perms, create/read/update)
    -   **Utilizador:** Apenas leitura (12 perms)
-   **Segurança:** Proteção contra auto-eliminação e eliminação de Super Admin
-   **Documentação:** Ver `docs/access-management.md`

### Configurações Sistema

### Configurações Sistema

-   **Países**: 14 países pré-carregados, códigos ISO, VIES
-   **Funções de Contacto**: 10 funções pré-definidas
-   **Taxas de IVA**: 4 taxas configuráveis (0%, 6%, 13%, 23%)

### Interface Moderna

-   Menu accordion com submenus expandíveis
-   Componentes Shadcn/ui (Form, DataTable, Badge, etc.)
-   Dark/light mode
-   Pesquisa e ordenação em DataTables
-   Mobile-first design responsivo

## 🚀 Próximos Módulos

-   [ ] Propostas/Orçamentos
-   [ ] Encomendas/Vendas
-   [ ] Sistema Financeiro
-   [ ] Dashboard Analytics
-   [ ] Relatórios e Exports

## 📚 Documentação Adicional

-   **Changelog Completo:** `docs/changelog.md`
-   **Gestão de Acessos:** `docs/access-management.md` (v0.7.0)
-   **Configuração BD:** `docs/database-config.md`
-   **Arquitetura Modular:** `docs/modular-architecture.md`

## 🔒 Segurança

-   ✅ Validação de inputs em todos os formulários
-   ✅ Sistema de permissões granular (48 permissões)
-   ✅ Proteção CSRF (Laravel)
-   ✅ Password hashing (bcrypt)
-   ✅ Middleware de autenticação
-   ✅ Proteção contra auto-eliminação
-   ✅ Validação de roles hierárquicos

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

## �📝 Documentação Adicional

-   [📋 Changelog](docs/changelog.md)
-   [🏗️ Arquitetura](docs/modular-architecture.md)
-   [💾 Configuração BD](docs/database-config.md)

---

**Desenvolvido durante estágio em:** Novembro 2025
