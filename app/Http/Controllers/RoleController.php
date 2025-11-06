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
    public function index()
    {
        $roles = Role::with('permissions')->withCount('users')->get()->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'users_count' => $role->users_count,
                'permissions_count' => $role->permissions->count(),
                'created_at' => $role->created_at,
            ];
        });

        return Inertia::render('Roles/Index', [
            'roles' => $roles
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
        $permissions = Permission::all();

        $grouped = [];

        foreach ($permissions as $permission) {
            [$module, $action] = explode('.', $permission->name);

            if (!isset($grouped[$module])) {
                $grouped[$module] = [
                    'name' => $this->getModuleLabel($module),
                    'key' => $module,
                    'permissions' => []
                ];
            }

            $grouped[$module]['permissions'][$action] = [
                'name' => $permission->name,
                'label' => ucfirst($action),
            ];
        }

        return array_values($grouped);
    }

    /**
     * Labels dos módulos
     */
    private function getModuleLabel($module)
    {
        $labels = [
            'clients' => 'Clientes',
            'suppliers' => 'Fornecedores',
            'contacts' => 'Contactos',
            'articles' => 'Artigos',
            'proposals' => 'Propostas',
            'orders' => 'Encomendas',
            'financial' => 'Financeiro',
            'users' => 'Utilizadores',
            'roles' => 'Permissões',
            'countries' => 'Países',
            'contact-functions' => 'Funções Contacto',
            'vat-rates' => 'Taxas IVA',
        ];

        return $labels[$module] ?? ucfirst($module);
    }
}
