# 🔐 Gestão de Acessos

Sistema de controle de utilizadores e permissões do Gest-App.

**Package:** Spatie Laravel Permission v6.23.0  
**Versão:** v0.8.5 (Nov 2025)

---

## 📋 O Que Foi Implementado

### Funcionalidades Principais

-   **Utilizadores**: Criar, editar, desativar utilizadores
-   **Grupos de Permissões**: 6 grupos predefinidos com diferentes níveis de acesso
-   **Sistema de Visibilidade**: Botões só aparecem se o utilizador tiver permissão
-   **64 Permissões**: 16 módulos × 4 ações (criar, ler, editar, eliminar)

### Como Funciona

**Problema Inicial:**  
Todos os botões apareciam sempre. Quando um utilizador sem permissão clicava, recebia erro 403.

**Solução:**  
Os botões só aparecem se o utilizador tiver a permissão necessária.

```
Exemplo: Utilizador "Visualizador"
- Vê listas de clientes ✅
- NÃO vê botão "Criar Cliente" ❌
- NÃO vê botões "Editar" ou "Eliminar" ❌
```

---

## 👥 Grupos de Utilizadores

| Grupo                 | Permissões                  | Para Quem                 |
| --------------------- | --------------------------- | ------------------------- |
| **Super Admin**       | Tudo (64/64)                | Administrador do sistema  |
| **Administrador**     | Quase tudo (56/64)          | Gestão geral              |
| **Gestor Comercial**  | Área comercial (22/64)      | Vendas, clientes, artigos |
| **Gestor Financeiro** | Apenas visualizar (11/64)   | Consulta financeira       |
| **Editor**            | Contactos e arquivos (9/64) | Gestão de contactos       |
| **Visualizador**      | Apenas ler tudo (16/64)     | Consulta geral            |

---

## 🎯 Módulos e Permissões

### 16 Módulos do Sistema

```
Comercial:           clients, suppliers, contacts, articles
Operacional:         calendar, work-orders, digital-archive
Financeiro:          financial, vat-rates
Sistema:             logs, users, roles
Configurações:       countries, contact-functions
Futuros:             proposals, orders
```

### 4 Ações por Módulo

Cada módulo tem 4 permissões:

-   **create** - Criar novos registos
-   **read** - Ver/consultar dados
-   **update** - Editar registos existentes
-   **delete** - Eliminar registos

**Exemplo:** `clients.create`, `articles.read`, `users.delete`

---

## 🛠️ Como Usar

### Criar Utilizador

1. **Gestão de Acessos > Utilizadores > Adicionar**
2. Preencher:
    - Nome, Email, Telemóvel
    - Password (mínimo 8 caracteres)
    - Escolher Grupo de Permissões
    - Ativar/Desativar
3. **Guardar**

### Criar Grupo Personalizado

1. **Gestão de Acessos > Permissões > Adicionar Grupo**
2. Preencher:
    - Nome do grupo (ex: "Gestor Logística")
    - Marcar os módulos que pode aceder
    - Ativar
3. **Guardar**

> Cada módulo marcado dá as 4 permissões (criar, ler, editar, eliminar)

### Testar Permissões

**Teste 1: Login como "Gestor Comercial"**

-   ✅ Vê botão "Novo Cliente"
-   ✅ Vê botões "Editar" e "Eliminar" em clientes

**Teste 2: Login como "Visualizador"**

-   ✅ Vê lista de clientes
-   ❌ NÃO vê nenhum botão de ação

---

## 🔒 Segurança

### Proteções Implementadas

✅ Não pode eliminar o próprio utilizador  
✅ Não pode eliminar Super Admin  
✅ Passwords encriptadas (bcrypt)  
✅ Validação no backend e frontend  
✅ UI adaptativa (botões só aparecem com permissão)

---

## 💻 Implementação Técnica

### Backend (Controller)

```php
public function index(Request $request)
{
    return Inertia::render('Clients/Index', [
        'clients' => Client::paginate(15),
        'can' => [
            'create' => $request->user()->can('clients.create'),
            'edit' => $request->user()->can('clients.update'),
            'delete' => $request->user()->can('clients.delete'),
        ]
    ]);
}
```

### Frontend (Vue)

```vue
<template>
    <!-- Botão só aparece com permissão -->
    <Button v-if="can.create">Novo Cliente</Button>

    <!-- Tabela -->
    <tr v-for="client in clients">
        <td>{{ client.name }}</td>
        <td>
            <Button v-if="can.edit">Editar</Button>
            <Button v-if="can.delete">Eliminar</Button>
        </td>
    </tr>
</template>

<script setup>
defineProps({
    clients: Array,
    can: Object, // Vem do controller
});
</script>
```

---

## 📦 Ficheiros Principais

**Backend:**

-   `app/Models/User.php` - Modelo com permissões
-   `app/Http/Controllers/RoleController.php` - Gestão de grupos
-   `app/Http/Controllers/UserManagementController.php` - Gestão de utilizadores

**Frontend:**

-   `resources/js/Pages/Users/Index.vue` - Lista utilizadores
-   `resources/js/Pages/Roles/Index.vue` - Lista grupos

**Database:**

-   `database/seeders/RoleAndPermissionSeeder.php` - Cria permissões e grupos

---

## 🔧 Comandos Úteis

```bash
# Limpar cache de permissões
php artisan permission:cache-reset

# Recriar permissões
php artisan db:seed --class=RoleAndPermissionSeeder

# Criar Super Admin
php artisan db:seed --class=AssignSuperAdminSeeder
```

---

## � Problemas Comuns

**Utilizador não vê funcionalidade:**

1. Verificar se utilizador está ativo
2. Verificar se grupo tem a permissão
3. Fazer logout e login novamente

**Erro "Permission does not exist":**

```bash
php artisan db:seed --class=RoleAndPermissionSeeder
```

**Super Admin perdeu acesso:**

```bash
php artisan tinker
$admin = User::where('email', 'admin@gest-app.com')->first();
$admin->syncRoles(['Super Admin']);
```

---

**Desenvolvido como parte do Projeto Final de Estágio**  
**Tecnologias:** Laravel 12 + Vue 3 + Spatie Permission  
**Data:** Novembro 2025
