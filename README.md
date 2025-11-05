# 🏢 Gest-App — Sistema de Gestão Empresarial

> Projeto Final de Estágio | Sistema de gestão empresarial para PMEs

## 📊 Status do Projeto

**Versão:** v0.4.5  
**Progresso:** 22% (4 de 18+ módulos)  
**Entrega:** 18 Nov 2025  
**BD:** ✅ MySQL configurado e funcionando  
**Welcome:** ✅ Navegação funcional

## 🛠️ Tecnologias

-   **Backend:** Laravel 12
-   **Frontend:** Vue.js 3 + Inertia.js
-   **UI:** Tailwind CSS + Shadcn/ui
-   **BD:** MySQL

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
-   **Perfil:** Super Admin (acesso total)

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

### Gestão de Contactos

-   Associação a entidades
-   Dados pessoais e profissionais
-   Consentimento RGPD obrigatório
-   Pesquisa e ordenação

### Interface Moderna

-   Menu accordion com submenus
-   Componentes Shadcn/ui
-   Dark/light mode
-   Mobile-first design

## 🚀 Próximos Módulos

-   [ ] Artigos/Produtos
-   [ ] Propostas/Orçamentos
-   [ ] Encomendas
-   [ ] Sistema Financeiro
-   [ ] Gestão de Utilizadores
-   [ ] Configurações Avançadas

## � Resolução de Problemas

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
