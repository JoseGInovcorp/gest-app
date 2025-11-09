# 🏢 Gest-App — Sistema de Gestão Empresarial

> Projeto Final de Estágio | Sistema de gestão empresarial para PMEs

## 📊 Status do Projeto

**Versão:** v0.9.1  
**Progresso:** 55% (11 de 20 módulos)  
**Entrega:** 18 Nov 2025  
**BD:** ✅ MySQL configurado e funcionando  
**Welcome:** ✅ Navegação funcional  
**Segurança:** ✅ Sistema de permissões com controlo de UI  
**Logs:** ✅ Histórico de atividades completo  
**Branding:** ✅ Logo personalizado integrado  
**UX:** ✅ Interface uniformizada em todos os módulos

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
-   **Estrutura Consistente:** Layout uniformizado em 11 módulos
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
-   ✅ Sistema de permissões granular (64 permissões em 16 módulos)
-   ✅ Controlo de UI baseado em permissões (botões adaptáveis)
-   ✅ Proteção CSRF (Laravel)
-   ✅ Password hashing (bcrypt)
-   ✅ Middleware de autenticação e autorização
-   ✅ Proteção contra auto-eliminação
-   ✅ Validação de roles hierárquicos
-   ✅ Zero erros 403 desnecessários (UI inteligente)

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
