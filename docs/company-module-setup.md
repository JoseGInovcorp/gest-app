# 🏢 Módulo Empresa - Instruções de Instalação

## 📋 O Que Foi Implementado

O módulo **Configurações - Empresa** permite personalizar os dados que aparecem em toda a aplicação:

-   ✅ **Logotipo** (upload de imagem até 2MB)
-   ✅ **Nome da Empresa**
-   ✅ **NIF** (9 dígitos)
-   ✅ **Morada Completa** (Rua, Código Postal, Localidade)

## 🚀 Instalação

### 1. Executar Migration

```bash
php artisan migrate --path=database/migrations/2025_11_09_000001_create_companies_table.php
```

Cria a tabela `companies` na base de dados.

### 2. Executar Seeders

```bash
# Criar registo inicial da empresa
php artisan db:seed --class=CompanySeeder

# Adicionar permissões do módulo
php artisan db:seed --class=AddCompanyPermissionsSeeder
```

### 3. Compilar Frontend

```bash
npm run build
```

### 4. Criar Link Simbólico (Storage)

Para que os uploads de logo funcionem:

```bash
php artisan storage:link
```

## 📍 Acesso

-   **Menu:** Configurações → Empresa (primeiro item)
-   **Rota:** `/company/settings`
-   **Permissão Necessária:** `company.read`

## 🔐 Permissões

O módulo criou **2 permissões**:

-   `company.read` - Ver configurações
-   `company.update` - Editar configurações

**Distribuição Automática:**

-   **Super Admin / Administrador**: read + update
-   **Todos os outros grupos**: apenas read

## ✅ Verificação

1. Fazer login como **Super Admin** ou **Administrador**
2. Menu lateral → **Configurações** → **Empresa**
3. Página deve carregar com formulário vazio (nome "Gest-App" por defeito)
4. Testar upload de logo e preenchimento de campos
5. Clicar **Guardar Alterações**

## 📂 Ficheiros Criados

**Backend:**

-   `app/Models/Company.php`
-   `app/Http/Controllers/CompanyController.php`
-   `database/migrations/2025_11_09_000001_create_companies_table.php`
-   `database/seeders/CompanySeeder.php`
-   `database/seeders/AddCompanyPermissionsSeeder.php`

**Frontend:**

-   `resources/js/Pages/Company/Edit.vue`

**Routes:**

-   `routes/web.php` (2 rotas adicionadas)

**Menu:**

-   `resources/js/Layouts/AuthenticatedLayout.vue` (item "Empresa" adicionado)

## 🔍 Troubleshooting

### Logo não aparece após upload

```bash
# Verificar se link simbólico existe
php artisan storage:link

# Verificar permissões da pasta
chmod -R 775 storage/app/public
```

### Permissões não funcionam

```bash
# Limpar cache de permissões
php artisan cache:clear
php artisan config:clear

# Re-executar seeder
php artisan db:seed --class=AddCompanyPermissionsSeeder
```

### Menu não aparece

```bash
# Recompilar frontend
npm run build

# Verificar se utilizador tem permissão company.read
```

## 📊 Verificar Instalação

```bash
# Ver tabela companies
php artisan tinker
>>> App\Models\Company::first()

# Ver permissões do módulo
>>> Spatie\Permission\Models\Permission::where('name', 'like', 'company.%')->get()

# Total de permissões (deve ser 66)
>>> Spatie\Permission\Models\Permission::count()
```

## ✨ Funcionalidades

-   **Singleton Pattern**: Apenas 1 empresa no sistema
-   **Upload de Logo**: Preview em tempo real
-   **Validação**: NIF com 9 dígitos, logo máx 2MB
-   **Storage**: Logos em `storage/app/public/company/logos`
-   **Utilização Futura**: Dados aparecem em PDFs, faturas, documentos oficiais

---

**Versão:** v0.9.0  
**Data:** 09 Nov 2025  
**Desenvolvido por:** [Seu Nome] - Projeto Final de Estágio
