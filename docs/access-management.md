# 🔐 Gestão de Acessos

Sistema de controle de utilizadores e permissões implementado com **Spatie Laravel Permission v6.23.0**.

---

## 📋 Visão Geral

### Funcionalidades

-   **Utilizadores**: CRUD com campos nome, email, telemóvel, grupo, estado
-   **Grupos de Permissões**: Ativar/desativar menus completos (cada menu = 4 permissões CRUD)
-   **48 Permissões**: 12 módulos × 4 ações (create, read, update, delete)
-   **4 Roles Predefinidos**: Super Admin, Administrador, Gestor, Utilizador

---

## 🎯 Estrutura de Permissões

### 12 Módulos Disponíveis

```
clients          → Clientes
suppliers        → Fornecedores
contacts         → Contactos
articles         → Artigos
proposals        → Propostas
orders           → Encomendas
financial        → Financeiro
users            → Utilizadores
roles            → Permissões (Grupos)
countries        → Países
contact-functions → Funções Contacto
vat-rates        → Taxas IVA
```

### 4 Ações por Módulo

```
{módulo}.create   → Criar
{módulo}.read     → Visualizar
{módulo}.update   → Editar
{módulo}.delete   → Eliminar
```

**Exemplo:** `clients.create`, `articles.read`, `users.update`

---

## 👥 Roles Hierárquicos

### 1. Super Admin

-   **Permissões:** Todas (acesso total)
-   **Proteção:** Não pode ser eliminado

### 2. Administrador

-   **Permissões:** Tudo EXCETO gestão de users e roles
-   **Proteção:** Não pode ser eliminado

### 3. Gestor

-   **Permissões:** Create, Read, Update em módulos operacionais (clientes, fornecedores, contactos, artigos, propostas)
-   **Sem acesso:** Encomendas, Financeiro, Users, Roles, Configurações

### 4. Utilizador

-   **Permissões:** Apenas Read em todos os módulos
-   **Uso:** Consulta de dados

---

## 🛠️ Como Usar

### Criar Grupo de Permissões

1. Ir a **Gestão de Acessos > Permissões**
2. Clicar **"Adicionar Grupo"**
3. Preencher:
    - Nome: Ex: "Gestor Comercial"
    - Ativar menus desejados (1 checkbox = 4 permissões CRUD)
    - Estado: Ativo
4. Salvar

> **Nota:** Ao marcar um menu, são atribuídas automaticamente as 4 permissões (create, read, update, delete).

### Criar Utilizador

1. Ir a **Gestão de Acessos > Utilizadores**
2. Clicar **"Adicionar Utilizador"**
3. Preencher:
    - Nome, Email, Telemóvel
    - Password (min 8 caracteres)
    - Grupo de Permissões
    - Estado: Ativo
4. Salvar

### Editar Permissões de Utilizador

1. Editar utilizador
2. Alterar **"Grupo de Permissões"** no dropdown
3. Salvar (mudança é imediata)

### Desativar Utilizador (sem eliminar)

1. Editar utilizador
2. Desmarcar **"Ativo"**
3. Salvar (utilizador não consegue fazer login)

---

## 🔒 Segurança

### Proteções Implementadas

✅ **Não pode eliminar seu próprio utilizador**  
✅ **Não pode eliminar utilizadores Super Admin**  
✅ **Não pode eliminar roles Super Admin e Administrador**  
✅ **Não pode eliminar role com utilizadores associados**  
✅ **Passwords sempre com hash (bcrypt)**

---

## 💻 Para Developers

### Verificar Permissão no Controller

```php
use Illuminate\Support\Facades\Auth;

// Abortar se não tem permissão
abort_unless(Auth::user()->can('clients.create'), 403);

// Passar para Vue
return Inertia::render('Clients/Index', [
    'canCreate' => Auth::user()->can('clients.create'),
    'canEdit' => Auth::user()->can('clients.update'),
    'canDelete' => Auth::user()->can('clients.delete'),
]);
```

### Verificar Role

```php
if (Auth::user()->hasRole('Super Admin')) {
    // Código específico
}
```

### No Blade/Vue

```php
// Blade
@can('clients.create')
    <button>Criar Cliente</button>
@endcan
```

```vue
<!-- Vue -->
<Button v-if="canCreate">Adicionar Cliente</Button>
```

---

## 🔧 Comandos Úteis

```bash
# Limpar cache de permissões
php artisan permission:cache-reset

# Recriar permissões e roles
php artisan db:seed --class=RoleAndPermissionSeeder

# Atribuir Super Admin
php artisan db:seed --class=AssignSuperAdminSeeder
```

---

## 📦 Estrutura de Ficheiros

### Backend

```
app/
├── Models/User.php                      # HasRoles trait
├── Http/Controllers/
│   ├── RoleController.php               # CRUD grupos
│   └── UserManagementController.php     # CRUD utilizadores
database/
├── migrations/
│   ├── *_add_mobile_and_active_to_users_table.php
│   └── *_add_active_to_roles_table.php
└── seeders/
    ├── RoleAndPermissionSeeder.php      # 48 perms + 4 roles
    └── AssignSuperAdminSeeder.php       # Super Admin setup
```

### Frontend

```
resources/js/Pages/
├── Roles/
│   ├── Index.vue    # Lista grupos
│   ├── Create.vue   # Criar grupo
│   └── Edit.vue     # Editar grupo
└── Users/
    ├── Index.vue    # Lista utilizadores
    ├── Create.vue   # Criar utilizador
    └── Edit.vue     # Editar utilizador
```

---

## 🐛 Troubleshooting

**Utilizador tem permissões mas não vê funcionalidade:**

-   Verificar se utilizador está ativo
-   Verificar se role está ativo
-   Limpar cache: `php artisan permission:cache-reset`
-   Re-login do utilizador

**"Permission does not exist":**

-   Executar: `php artisan db:seed --class=RoleAndPermissionSeeder`

**Super Admin perdeu permissões:**

```bash
php artisan tinker
$admin = User::where('email', 'admin@gest-app.com')->first();
$admin->syncRoles(['Super Admin']);
$superAdmin = Role::where('name', 'Super Admin')->first();
$superAdmin->syncPermissions(Permission::all());
```

---

**Versão:** 0.7.0  
**Package:** Spatie Laravel Permission v6.23.0  
**Data:** 2025-11-06
