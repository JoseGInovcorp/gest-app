<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AssignSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'admin@gest-app.com')->first();

        if ($user) {
            $user->assignRole('Super Admin');
            $this->command->info('✅ Role Super Admin atribuída ao usuário admin@gest-app.com');
        } else {
            $this->command->error('❌ Usuário admin@gest-app.com não encontrado!');
        }
    }
}
