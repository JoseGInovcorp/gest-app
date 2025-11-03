<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permissions for each module
        $permissions = [
            // Entities (Clients/Suppliers)
            'entities.view',
            'entities.create',
            'entities.edit',
            'entities.delete',
            'entities.export',

            // Contacts
            'contacts.view',
            'contacts.create',
            'contacts.edit',
            'contacts.delete',
            'contacts.export',

            // Articles (Products/Services)
            'articles.view',
            'articles.create',
            'articles.edit',
            'articles.delete',
            'articles.manage_stock',
            'articles.export',

            // Proposals/Quotes
            'proposals.view',
            'proposals.create',
            'proposals.edit',
            'proposals.delete',
            'proposals.send',
            'proposals.approve',
            'proposals.export',

            // Orders
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.delete',
            'orders.process',
            'orders.export',

            // Financial
            'financial.view',
            'financial.create',
            'financial.edit',
            'financial.delete',
            'financial.reconcile',
            'financial.reports',
            'financial.export',

            // Calendar
            'calendar.view',
            'calendar.create',
            'calendar.edit',
            'calendar.delete',

            // Access Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'roles.manage',
            'permissions.manage',

            // Configuration
            'config.system',
            'config.company',
            'config.taxes',
            'config.categories',
            'config.templates',
            'config.integrations',
            'config.reports',

            // Reports & Analytics
            'reports.view',
            'reports.create',
            'reports.export',
            'analytics.view',

            // System Administration
            'system.backup',
            'system.logs',
            'system.maintenance',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $roles = [
            'Super Admin' => 'all', // Will get all permissions
            'Administrator' => [
                'entities.view',
                'entities.create',
                'entities.edit',
                'entities.delete',
                'entities.export',
                'contacts.view',
                'contacts.create',
                'contacts.edit',
                'contacts.delete',
                'contacts.export',
                'articles.view',
                'articles.create',
                'articles.edit',
                'articles.delete',
                'articles.manage_stock',
                'articles.export',
                'proposals.view',
                'proposals.create',
                'proposals.edit',
                'proposals.delete',
                'proposals.send',
                'proposals.approve',
                'proposals.export',
                'orders.view',
                'orders.create',
                'orders.edit',
                'orders.delete',
                'orders.process',
                'orders.export',
                'financial.view',
                'financial.create',
                'financial.edit',
                'financial.delete',
                'financial.reconcile',
                'financial.reports',
                'financial.export',
                'calendar.view',
                'calendar.create',
                'calendar.edit',
                'calendar.delete',
                'users.view',
                'users.create',
                'users.edit',
                'config.system',
                'config.company',
                'config.taxes',
                'config.categories',
                'config.templates',
                'config.integrations',
                'config.reports',
                'reports.view',
                'reports.create',
                'reports.export',
                'analytics.view',
            ],
            'Manager' => [
                'entities.view',
                'entities.create',
                'entities.edit',
                'entities.export',
                'contacts.view',
                'contacts.create',
                'contacts.edit',
                'contacts.export',
                'articles.view',
                'articles.create',
                'articles.edit',
                'articles.manage_stock',
                'articles.export',
                'proposals.view',
                'proposals.create',
                'proposals.edit',
                'proposals.send',
                'proposals.approve',
                'proposals.export',
                'orders.view',
                'orders.create',
                'orders.edit',
                'orders.process',
                'orders.export',
                'financial.view',
                'financial.create',
                'financial.edit',
                'financial.reports',
                'financial.export',
                'calendar.view',
                'calendar.create',
                'calendar.edit',
                'calendar.delete',
                'reports.view',
                'reports.create',
                'reports.export',
                'analytics.view',
            ],
            'Sales Representative' => [
                'entities.view',
                'entities.create',
                'entities.edit',
                'contacts.view',
                'contacts.create',
                'contacts.edit',
                'articles.view',
                'proposals.view',
                'proposals.create',
                'proposals.edit',
                'proposals.send',
                'orders.view',
                'orders.create',
                'orders.edit',
                'calendar.view',
                'calendar.create',
                'calendar.edit',
                'calendar.delete',
                'reports.view',
            ],
            'Financial Manager' => [
                'entities.view',
                'contacts.view',
                'articles.view',
                'proposals.view',
                'proposals.approve',
                'orders.view',
                'financial.view',
                'financial.create',
                'financial.edit',
                'financial.reconcile',
                'financial.reports',
                'financial.export',
                'calendar.view',
                'reports.view',
                'reports.create',
                'reports.export',
                'analytics.view',
            ],
            'Warehouse Manager' => [
                'entities.view',
                'articles.view',
                'articles.create',
                'articles.edit',
                'articles.manage_stock',
                'articles.export',
                'orders.view',
                'orders.edit',
                'orders.process',
                'orders.export',
                'reports.view',
            ],
            'Employee' => [
                'entities.view',
                'contacts.view',
                'articles.view',
                'proposals.view',
                'orders.view',
                'calendar.view',
                'calendar.create',
                'calendar.edit',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);

            if ($rolePermissions === 'all') {
                // Super Admin gets all permissions
                $role->givePermissionTo(Permission::all());
            } else {
                // Assign specific permissions
                $role->givePermissionTo($rolePermissions);
            }
        }

        // Create a super admin user if it doesn't exist
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gest-app.com'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'), // Change this in production!
                'email_verified_at' => now(),
            ]
        );

        // Assign Super Admin role
        $superAdmin->assignRole('Super Admin');

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Super Admin user created: admin@gest-app.com / password');
    }
}
