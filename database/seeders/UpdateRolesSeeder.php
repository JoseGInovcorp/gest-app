<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UpdateRolesSeeder extends Seeder
{
    /**
     * Atualiza os grupos (roles) com permissões mais específicas
     * conforme os cenários de uso reais.
     */
    public function run(): void
    {
        $this->command->info('🔄 Atualizando grupos e permissões...');

        // Limpar cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1️⃣ Super Admin - Acesso Total (mantém como está)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'active' => true]);
        $superAdmin->syncPermissions(Permission::all());
        $this->command->info("✅ Super Admin: {$superAdmin->permissions->count()} permissões (todas)");

        // 2️⃣ Administrador - Gestão completa exceto configurações de sistema
        $admin = Role::firstOrCreate(['name' => 'Administrador', 'active' => true]);
        $adminPermissions = Permission::where('name', 'not like', 'users.%')
            ->where('name', 'not like', 'roles.%')
            ->get();
        $admin->syncPermissions($adminPermissions);
        $this->command->info("✅ Administrador: {$admin->permissions->count()} permissões");

        // 3️⃣ Gestor Comercial - Clientes, Fornecedores, Contactos, Propostas
        $comercial = Role::firstOrCreate(['name' => 'Gestor Comercial', 'active' => true]);
        $comercialPermissions = [
            'clients.create',
            'clients.read',
            'clients.update',
            'clients.delete',
            'suppliers.create',
            'suppliers.read',
            'suppliers.update',
            'suppliers.delete',
            'contacts.create',
            'contacts.read',
            'contacts.update',
            'contacts.delete',
            'proposals.create',
            'proposals.read',
            'proposals.update',
            'proposals.delete',
            'articles.read', // pode ver artigos
            'calendar.read', // pode ver calendário
            'work-orders.create', // pode gerir ordens de trabalho
            'work-orders.read',
            'work-orders.update',
            'work-orders.delete',
        ];
        $comercial->syncPermissions($comercialPermissions);
        $this->command->info("✅ Gestor Comercial: {$comercial->permissions->count()} permissões");

        // 4️⃣ Gestor Financeiro - Financeiro, Encomendas, IVA
        $financeiro = Role::firstOrCreate(['name' => 'Gestor Financeiro', 'active' => true]);
        $financeiroPermissions = [
            'financial.create',
            'financial.read',
            'financial.update',
            'financial.delete',
            'orders.create',
            'orders.read',
            'orders.update',
            'orders.delete',
            'vat-rates.read', // pode ver taxas IVA
            'clients.read', // pode ver clientes
            'suppliers.read', // pode ver fornecedores
        ];
        $financeiro->syncPermissions($financeiroPermissions);
        $this->command->info("✅ Gestor Financeiro: {$financeiro->permissions->count()} permissões");

        // 5️⃣ Editor de Conteúdo - Artigos e configurações básicas
        $editor = Role::firstOrCreate(['name' => 'Editor', 'active' => true]);
        $editorPermissions = [
            'articles.create',
            'articles.read',
            'articles.update',
            'articles.delete',
            'countries.read',
            'contact-functions.read',
            'clients.read', // pode visualizar clientes
            'suppliers.read', // pode visualizar fornecedores
            'contacts.read', // pode visualizar contactos
        ];
        $editor->syncPermissions($editorPermissions);
        $this->command->info("✅ Editor: {$editor->permissions->count()} permissões");

        // 6️⃣ Visualizador - Apenas leitura em tudo
        $viewer = Role::firstOrCreate(['name' => 'Visualizador', 'active' => true]);
        $viewerPermissions = Permission::where('name', 'like', '%.read')->get();
        $viewer->syncPermissions($viewerPermissions);
        $this->command->info("✅ Visualizador: {$viewer->permissions->count()} permissões");

        // 7️⃣ Remover grupo "Gestor" antigo (se não tiver utilizadores)
        $oldGestor = Role::where('name', 'Gestor')->first();
        if ($oldGestor && $oldGestor->users()->count() === 0) {
            $oldGestor->delete();
            $this->command->info("🗑️  Removido grupo antigo: Gestor");
        }

        // 8️⃣ Remover grupo "Utilizador" antigo (se não tiver utilizadores)
        $oldUser = Role::where('name', 'Utilizador')->first();
        if ($oldUser && $oldUser->users()->count() === 0) {
            $oldUser->delete();
            $this->command->info("🗑️  Removido grupo antigo: Utilizador");
        }

        $this->command->newLine();
        $this->command->info('✨ Grupos atualizados com sucesso!');
        $this->command->info('');
        $this->command->info('📋 Resumo dos Grupos:');
        $this->command->info('   1. Super Admin - Acesso total ao sistema');
        $this->command->info('   2. Administrador - Gestão completa (exceto utilizadores/permissões)');
        $this->command->info('   3. Gestor Comercial - Clientes, Fornecedores, Contactos, Propostas');
        $this->command->info('   4. Gestor Financeiro - Financeiro, Encomendas, Taxas IVA');
        $this->command->info('   5. Editor - Artigos e configurações básicas');
        $this->command->info('   6. Visualizador - Apenas leitura em todos os menus');
    }
}
