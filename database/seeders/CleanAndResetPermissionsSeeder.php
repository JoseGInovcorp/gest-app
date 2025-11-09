<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class CleanAndResetPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🧹 Limpando permissões antigas...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Remover TODAS as permissões e roles antigas
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        Permission::query()->delete();
        Role::query()->delete();

        $this->command->info('✅ Permissões antigas removidas!');
        $this->command->info('📝 Criando permissões novas...');

        // Define os módulos/menus do sistema (apenas os implementados)
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
        $permissionCount = 0;

        foreach ($modules as $module => $label) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
                $permissionCount++;
            }
        }

        $this->command->info("✅ {$permissionCount} permissões criadas (12 módulos × 4 ações)");
        $this->command->info('👥 Criando roles...');

        // Criar roles
        $superAdmin = Role::create(['name' => 'Super Admin']);
        $admin = Role::create(['name' => 'Administrador']);
        $manager = Role::create(['name' => 'Gestor']);
        $user = Role::create(['name' => 'Utilizador']);

        // Super Admin tem todas as permissões
        $superAdmin->syncPermissions(Permission::all());
        $this->command->info('   ✓ Super Admin: ' . $superAdmin->permissions->count() . ' permissões');

        // Administrador tem quase todas (exceto gestão de utilizadores/roles)
        $adminPerms = Permission::where('name', 'not like', 'users.%')
            ->where('name', 'not like', 'roles.%')
            ->get();
        $admin->syncPermissions($adminPerms);
        $this->command->info('   ✓ Administrador: ' . $admin->permissions->count() . ' permissões');

        // Gestor tem permissões operacionais (create, read, update - sem delete)
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
        $this->command->info('   ✓ Gestor: ' . $manager->permissions->count() . ' permissões');

        // Utilizador tem apenas leitura
        $userPermissions = Permission::where('name', 'like', '%.read')->get();
        $user->syncPermissions($userPermissions);
        $this->command->info('   ✓ Utilizador: ' . $user->permissions->count() . ' permissões');

        $this->command->info('');
        $this->command->info('✅ Sistema de permissões limpo e recriado com sucesso!');
        $this->command->info('📊 Total: ' . Permission::count() . ' permissões | ' . Role::count() . ' roles');
    }
}
