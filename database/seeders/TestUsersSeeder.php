<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * IMPORTANTE: Agora os utilizadores recebem GRUPOS (Roles) 
     * em vez de permissões diretas.
     */
    public function run(): void
    {
        $this->command->info('🧪 Criando utilizadores de teste...');

        // Verificar se os grupos existem
        $roleEditor = Role::where('name', 'Editor')->first();
        $roleViewer = Role::where('name', 'Visualizador')->first();
        $roleComercial = Role::where('name', 'Gestor Comercial')->first();
        $roleFinanceiro = Role::where('name', 'Gestor Financeiro')->first();
        $roleAdmin = Role::where('name', 'Administrador')->first();

        if (!$roleEditor || !$roleViewer || !$roleComercial || !$roleFinanceiro) {
            $this->command->error('⚠️  Grupos não encontrados! Execute primeiro: php artisan db:seed --class=UpdateRolesSeeder');
            return;
        }

        // 1️⃣ Editor - Grupo "Editor"
        $editor = User::firstOrCreate(
            ['email' => 'editor@gest-app.com'],
            [
                'name' => 'Editor Teste',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        );

        // Remover permissões diretas antigas e atribuir grupo
        $editor->syncPermissions([]);
        $editor->syncRoles([$roleEditor]);

        $this->command->info('✅ Editor criado: editor@gest-app.com / password');
        $this->command->info('   Grupo: Editor (' . $roleEditor->permissions->count() . ' permissões)');

        // 2️⃣ Viewer - Grupo "Visualizador"
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@gest-app.com'],
            [
                'name' => 'Visualizador Teste',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        );

        $viewer->syncPermissions([]);
        $viewer->syncRoles([$roleViewer]);

        $this->command->info('✅ Viewer criado: viewer@gest-app.com / password');
        $this->command->info('   Grupo: Visualizador (' . $roleViewer->permissions->count() . ' permissões)');

        // 3️⃣ Comercial - Grupo "Gestor Comercial"
        $comercial = User::firstOrCreate(
            ['email' => 'comercial@gest-app.com'],
            [
                'name' => 'Comercial Teste',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        );

        $comercial->syncPermissions([]);
        $comercial->syncRoles([$roleComercial]);

        $this->command->info('✅ Comercial criado: comercial@gest-app.com / password');
        $this->command->info('   Grupo: Gestor Comercial (' . $roleComercial->permissions->count() . ' permissões)');

        // 4️⃣ Financeiro - Grupo "Gestor Financeiro"
        $financeiro = User::firstOrCreate(
            ['email' => 'financeiro@gest-app.com'],
            [
                'name' => 'Financeiro Teste',
                'password' => Hash::make('password'),
                'active' => true,
            ]
        );

        $financeiro->syncPermissions([]);
        $financeiro->syncRoles([$roleFinanceiro]);

        $this->command->info('✅ Financeiro criado: financeiro@gest-app.com / password');
        $this->command->info('   Grupo: Gestor Financeiro (' . $roleFinanceiro->permissions->count() . ' permissões)');

        // 5️⃣ Atualizar utilizadores sem grupo (teste@gest.pt, jose@example.com)
        $testUser = User::where('email', 'teste@gest.pt')->first();
        if ($testUser && $roleAdmin) {
            $testUser->syncRoles([$roleAdmin]);
            $this->command->info('✅ ' . $testUser->email . ' - atribuído grupo Administrador');
        }

        $joseUser = User::where('email', 'jose@example.com')->first();
        if ($joseUser && $roleViewer) {
            $joseUser->syncRoles([$roleViewer]);
            $this->command->info('✅ ' . $joseUser->email . ' - atribuído grupo Visualizador');
        }

        $this->command->newLine();
        $this->command->info('📊 Total de utilizadores de teste criados: 4');
        $this->command->info('');
        $this->command->info('🔐 Credenciais (todos com password: password):');
        $this->command->info('   1. editor@gest-app.com - Grupo: Editor');
        $this->command->info('   2. viewer@gest-app.com - Grupo: Visualizador');
        $this->command->info('   3. comercial@gest-app.com - Grupo: Gestor Comercial');
        $this->command->info('   4. financeiro@gest-app.com - Grupo: Gestor Financeiro');
        $this->command->newLine();
        $this->command->info('💡 Agora as permissões são geridas através dos GRUPOS, não diretamente!');
    }
}
