<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define os módulos/menus do sistema
        $modules = [
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

        // Criar permissões CRUD para cada módulo
        $actions = ['create', 'read', 'update', 'delete'];

        foreach ($modules as $module => $label) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Criar roles
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $manager = Role::firstOrCreate(['name' => 'Gestor']);
        $user = Role::firstOrCreate(['name' => 'Utilizador']);

        // Super Admin tem todas as permissões
        $superAdmin->syncPermissions(Permission::all());

        // Administrador tem quase todas (exceto gestão de utilizadores/roles)
        $admin->syncPermissions(Permission::where('name', 'not like', 'users.%')
            ->where('name', 'not like', 'roles.%')
            ->get());

        // Gestor tem permissões operacionais
        $managerPermissions = Permission::whereIn('name', [
            'clients.create',
            'clients.read',
            'clients.update',
            'suppliers.create',
            'suppliers.read',
            'suppliers.update',
            'contacts.create',
            'contacts.read',
            'contacts.update',
            'contacts.delete',
            'articles.create',
            'articles.read',
            'articles.update',
            'proposals.create',
            'proposals.read',
            'proposals.update',
            'orders.create',
            'orders.read',
            'orders.update',
            'financial.read',
        ])->get();
        $manager->syncPermissions($managerPermissions);

        // Utilizador tem apenas leitura
        $userPermissions = Permission::where('name', 'like', '%.read')->get();
        $user->syncPermissions($userPermissions);

        $this->command->info('✅ Roles e Permissões criadas com sucesso!');
        $this->command->info('   - Super Admin: ' . $superAdmin->permissions->count() . ' permissões');
        $this->command->info('   - Administrador: ' . $admin->permissions->count() . ' permissões');
        $this->command->info('   - Gestor: ' . $manager->permissions->count() . ' permissões');
        $this->command->info('   - Utilizador: ' . $user->permissions->count() . ' permissões');
    }
}
