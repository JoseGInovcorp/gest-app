<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'role' => $user->roles->first()?->name,
                'active' => $user->active,
                'created_at' => $user->created_at,
            ];
        });

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all()->map(function ($role) {
            return [
                'value' => $role->name,
                'label' => $role->name,
            ];
        });

        return Inertia::render('Users/Create', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
            'password' => Hash::make($validated['password']),
            'active' => $validated['active'] ?? true,
        ]);

        $user->assignRole($validated['role']);

        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('created');

        return redirect()->route('users.index')
            ->with('success', 'Utilizador criado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all()->map(function ($role) {
            return [
                'value' => $role->name,
                'label' => $role->name,
            ];
        });

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'role' => $user->roles->first()?->name,
                'active' => $user->active,
            ],
            'roles' => $roles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'active' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
            'active' => $validated['active'] ?? true,
        ]);

        if (!empty($validated['password'])) {
            $user->update([
                'password' => Hash::make($validated['password'])
            ]);
        }

        $user->syncRoles([$validated['role']]);

        // Log activity
        activity()
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('updated');

        return redirect()->route('users.index')
            ->with('success', 'Utilizador atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Não permitir eliminar o próprio utilizador
        if (auth()->user()->id === $user->id) {
            return back()->with('error', 'Não é possível eliminar o próprio utilizador.');
        }

        // Proteger Super Admin
        if ($user->hasRole('Super Admin')) {
            return back()->with('error', 'Não é possível eliminar um Super Admin.');
        }

        // Log activity before delete
        activity()
            ->performedOn($user)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'deleted_user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ])
            ->log('deleted');

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilizador eliminado com sucesso.');
    }
}
