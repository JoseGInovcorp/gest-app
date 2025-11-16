<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    /**
     * Check if user is Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Check if user is Administrator
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Administrator');
    }

    /**
     * Check if user is Manager
     */
    public function isManager(): bool
    {
        return $this->hasRole('Manager');
    }

    /**
     * Get user role names as array
     */
    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Get user permissions as array
     */
    public function getPermissionNames(): array
    {
        return $this->getAllPermissions()->pluck('name')->toArray();
    }

    /**
     * Get permissions only from active roles
     */
    public function getActiveRolePermissions()
    {
        // Obter apenas roles ativos
        $activeRoles = $this->roles()->where('active', true)->get();

        // Coletar permissões de todos os roles ativos
        $permissions = collect();
        foreach ($activeRoles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }

        // Adicionar permissões diretas do usuário
        $permissions = $permissions->merge($this->permissions);

        return $permissions->unique('id');
    }

    /**
     * Override Spatie's getAllPermissions to only use active roles
     */
    public function getAllPermissions()
    {
        return $this->getActiveRolePermissions();
    }

    /**
     * Check if user can access module
     */
    public function canAccessModule(string $module): bool
    {
        return $this->can("{$module}.view");
    }
}
