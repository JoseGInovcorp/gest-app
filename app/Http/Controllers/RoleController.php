<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->withCount('users')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users_count,
                'permissions_count' => $role->permissions->count(),
                'active' => $role->active, // Campo ativo adicionado
                'created_at' => $role->created_at,
            ];
        });

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'can' => [
                'create' => $request->user()->can('roles.create'),
                'view' => $request->user()->can('roles.read'),
                'edit' => $request->user()->can('roles.update'),
                'delete' => $request->user()->can('roles.delete'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Organizar permissões por módulo
        $permissions = $this->getGroupedPermissions();

        return Inertia::render('Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
            'active' => 'boolean',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
            'active' => $validated['active'] ?? true,
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        // Log activity
        activity()
            ->performedOn($role)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('created');

        return redirect()->route('roles.index')
            ->with('success', 'Grupo de permissões criado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = $this->getGroupedPermissions();

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
            'active' => 'boolean',
        ]);

        $role->update([
            'name' => $validated['name'],
            'active' => $validated['active'] ?? $role->active,
        ]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        // Log activity
        activity()
            ->performedOn($role)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('updated');

        return redirect()->route('roles.index')
            ->with('success', 'Grupo de permissões atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Verificar se o role tem utilizadores
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Não é possível eliminar um grupo com utilizadores atribuídos.');
        }

        // Proteger roles de sistema
        if (in_array($role->name, ['Super Admin', 'Administrador'])) {
            return back()->with('error', 'Não é possível eliminar roles de sistema.');
        }

        // Log activity before delete
        activity()
            ->performedOn($role)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'deleted_role' => [
                    'name' => $role->name,
                    'permissions_count' => $role->permissions()->count(),
                ],
            ])
            ->log('deleted');

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Grupo de permissões eliminado com sucesso.');
    }

    /**
     * Organizar permissões por módulo
     */
    private function getGroupedPermissions()
    {
        // Apenas as 4 ações base permitidas (sempre nesta ordem)
        $allowedActions = ['create', 'read', 'update', 'delete'];

        $permissions = Permission::all();

        $grouped = [];

        foreach ($permissions as $permission) {
            // Verificar se o formato é correto (module.action)
            if (!str_contains($permission->name, '.')) {
                continue;
            }

            [$module, $action] = explode('.', $permission->name);

            // Ignorar ações que não sejam as 4 básicas
            if (!in_array($action, $allowedActions)) {
                continue;
            }

            // Obter label do módulo
            $moduleData = $this->getModuleLabel($module);

            if (!$moduleData) {
                continue;
            }

            if (!isset($grouped[$module])) {
                $grouped[$module] = [
                    'name' => $moduleData['name'],
                    'key' => $module,
                    'order' => $moduleData['order'],
                    'group' => $moduleData['group'] ?? null,
                    'permissions' => []
                ];
            }

            $grouped[$module]['permissions'][$action] = [
                'name' => $permission->name,
                'label' => ucfirst($action),
            ];
        }

        // Ordenar módulos por order
        usort($grouped, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        // Garantir que as permissões de cada módulo seguem a ordem correta
        foreach ($grouped as &$module) {
            $orderedPermissions = [];
            foreach ($allowedActions as $action) {
                if (isset($module['permissions'][$action])) {
                    $orderedPermissions[$action] = $module['permissions'][$action];
                }
            }
            $module['permissions'] = $orderedPermissions;
        }

        return array_values($grouped);
    }

    /**
     * Labels dos módulos (ordem da sidebar)
     */
    private function getModuleLabel($module)
    {
        // Organizado pela ordem da sidebar
        $labels = [
            // Menus Principais (ordem 1-6)
            'clients' => ['name' => 'Clientes', 'order' => 2, 'group' => null],
            'suppliers' => ['name' => 'Fornecedores', 'order' => 3, 'group' => null],
            'contacts' => ['name' => 'Contactos', 'order' => 4, 'group' => null],
            'proposals' => ['name' => 'Propostas', 'order' => 5, 'group' => null],

            // Submenu Encomendas (ordem 10-12)
            'orders' => ['name' => 'Encomendas', 'order' => 10, 'group' => 'Encomendas'],
            'work-orders' => ['name' => 'Ordens de Trabalho', 'order' => 11, 'group' => 'Encomendas'],

            // Submenu Financeiro (ordem 20-22)
            'financial' => ['name' => 'Financeiro', 'order' => 20, 'group' => 'Financeiro'],

            // Submenu Gestão de Acessos (ordem 30-31)
            'users' => ['name' => 'Utilizadores', 'order' => 30, 'group' => 'Gestão de Acessos'],
            'roles' => ['name' => 'Permissões', 'order' => 31, 'group' => 'Gestão de Acessos'],

            // Submenu Configurações (ordem 40-47)
            'company' => ['name' => 'Empresa', 'order' => 39, 'group' => 'Configurações'],
            'countries' => ['name' => 'Países', 'order' => 40, 'group' => 'Configurações → Entidades'],
            'contact-functions' => ['name' => 'Funções Contacto', 'order' => 41, 'group' => 'Configurações → Contactos'],
            'articles' => ['name' => 'Artigos', 'order' => 42, 'group' => 'Configurações'],
            'vat-rates' => ['name' => 'Taxas IVA', 'order' => 43, 'group' => 'Configurações → Financeiro'],

            // Outros Menus (ordem 50-59)
            'calendar' => ['name' => 'Calendário', 'order' => 50, 'group' => null],
            'digital-archive' => ['name' => 'Arquivo Digital', 'order' => 51, 'group' => null],
            'logs' => ['name' => 'Logs', 'order' => 52, 'group' => null],
        ];

        return $labels[$module] ?? null;
    }
}
