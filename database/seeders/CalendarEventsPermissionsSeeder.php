<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CalendarEventsPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões para Eventos do Calendário (calendar-events)
        $calendarEventsPermissions = [
            'calendar-events.create',
            'calendar-events.read',
            'calendar-events.update',
            'calendar-events.delete',
        ];

        foreach ($calendarEventsPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Atribuir permissões ao Super Admin
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($calendarEventsPermissions);
        }

        // Atribuir permissões de leitura e criação ao Admin
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'calendar-events.create',
                'calendar-events.read',
                'calendar-events.update',
                'calendar-events.delete',
            ]);
        }

        // Atribuir permissões de leitura e criação ao User
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $userRole->givePermissionTo([
                'calendar-events.create',
                'calendar-events.read',
                'calendar-events.update',
            ]);
        }

        $this->command->info('✓ Permissões de Eventos do Calendário criadas e atribuídas com sucesso');
    }
}
