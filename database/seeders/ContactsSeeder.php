<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Entity;

class ContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obter entidades (clientes e fornecedores)
        $entities = Entity::all();

        if ($entities->isEmpty()) {
            $this->command->error('❌ Nenhuma entidade encontrada. Execute primeiro o EntitiesSeeder.');
            return;
        }

        // Obter próximo número disponível
        $nextNumber = Contact::withTrashed()->max('number') ?? 0;

        // Contactos para diferentes entidades
        $contacts = [
            // TechCorp Solutions
            ['first_name' => 'João', 'last_name' => 'Silva', 'function' => 'Diretor Geral', 'email' => 'joao.silva@techcorp.pt', 'mobile' => '912345678', 'phone' => '211234567'],
            ['first_name' => 'Maria', 'last_name' => 'Santos', 'function' => 'Gestora de Projetos', 'email' => 'maria.santos@techcorp.pt', 'mobile' => '923456789'],

            // Restaurante O Sabor
            ['first_name' => 'António', 'last_name' => 'Costa', 'function' => 'Chef de Cozinha', 'email' => 'antonio.costa@osabor.pt', 'mobile' => '934567890', 'phone' => '212345678'],
            ['first_name' => 'Sofia', 'last_name' => 'Ferreira', 'function' => 'Gerente', 'email' => 'sofia.ferreira@osabor.pt', 'mobile' => '945678901'],

            // AutoPeças Silva
            ['first_name' => 'Carlos', 'last_name' => 'Mendes', 'function' => 'Diretor Comercial', 'email' => 'carlos.mendes@autopecassilva.pt', 'mobile' => '956789012', 'phone' => '213456789'],
            ['first_name' => 'Ana', 'last_name' => 'Rodrigues', 'function' => 'Contabilista', 'email' => 'ana.rodrigues@autopecassilva.pt', 'mobile' => '967890123'],

            // Clínica Dentária Sorriso
            ['first_name' => 'Dr. Paulo', 'last_name' => 'Oliveira', 'function' => 'Médico Dentista', 'email' => 'paulo.oliveira@clinicasorriso.pt', 'mobile' => '978901234', 'phone' => '214567890'],
            ['first_name' => 'Rita', 'last_name' => 'Martins', 'function' => 'Rececionista', 'email' => 'recepcao@clinicasorriso.pt', 'mobile' => '989012345'],

            // Construções Almeida & Filhos
            ['first_name' => 'Manuel', 'last_name' => 'Almeida', 'function' => 'Diretor de Obra', 'email' => 'manuel.almeida@construcoesalmeida.pt', 'mobile' => '990123456', 'phone' => '215678901'],
            ['first_name' => 'Teresa', 'last_name' => 'Pereira', 'function' => 'Administrativa', 'email' => 'teresa.pereira@construcoesalmeida.pt', 'mobile' => '901234567'],

            // Mercearia São João
            ['first_name' => 'José', 'last_name' => 'Gonçalves', 'function' => 'Proprietário', 'email' => 'jose@saojoao.pt', 'mobile' => '912345670', 'phone' => '216789012'],

            // Distribuidora Central
            ['first_name' => 'Luís', 'last_name' => 'Carvalho', 'function' => 'Gestor de Vendas', 'email' => 'luis.carvalho@distribuidoracentral.pt', 'mobile' => '923456701', 'phone' => '217890123'],

            // Fornecimentos Industriais
            ['first_name' => 'Beatriz', 'last_name' => 'Lopes', 'function' => 'Diretora Comercial', 'email' => 'beatriz.lopes@fornecimentosindustriais.pt', 'mobile' => '934567012', 'phone' => '218901234'],

            // Papelaria Modern
            ['first_name' => 'Ricardo', 'last_name' => 'Sousa', 'function' => 'Responsável de Compras', 'email' => 'ricardo.sousa@modernoffice.pt', 'mobile' => '945678123', 'phone' => '219012345'],

            // Tech Supplies
            ['first_name' => 'Cláudia', 'last_name' => 'Nunes', 'function' => 'Account Manager', 'email' => 'claudia.nunes@techsupplies.pt', 'mobile' => '956789234', 'phone' => '220123456'],
        ];

        // Distribuir contactos pelas entidades
        $entityIndex = 0;
        foreach ($contacts as $index => $contactData) {
            // Selecionar entidade (2 contactos por entidade em média, mas alguns têm 1)
            if ($index < 2) {
                $entityId = $entities[0]->id; // TechCorp - 2 contactos
            } elseif ($index < 4) {
                $entityId = $entities[1]->id; // Restaurante - 2 contactos
            } elseif ($index < 6) {
                $entityId = $entities[2]->id; // AutoPeças - 2 contactos
            } elseif ($index < 8) {
                $entityId = $entities[3]->id; // Clínica - 2 contactos
            } elseif ($index < 10) {
                $entityId = $entities[4]->id; // Construções - 2 contactos
            } elseif ($index < 11) {
                $entityId = $entities[5]->id; // Mercearia - 1 contacto
            } elseif ($index < 12) {
                $entityId = $entities[6]->id; // Distribuidora - 1 contacto
            } elseif ($index < 13) {
                $entityId = $entities[7]->id; // Fornecimentos - 1 contacto
            } elseif ($index < 14) {
                $entityId = $entities[8]->id; // Papelaria - 1 contacto
            } else {
                $entityId = $entities[9]->id; // Tech Supplies - 1 contacto
            }

            Contact::create([
                'number' => ++$nextNumber,
                'entity_id' => $entityId,
                'first_name' => $contactData['first_name'],
                'last_name' => $contactData['last_name'],
                'function' => $contactData['function'],
                'email' => $contactData['email'],
                'mobile' => $contactData['mobile'],
                'phone' => $contactData['phone'] ?? null,
                'rgpd_consent' => true,
                'status' => 'active',
            ]);
        }

        $this->command->info('✅ ' . count($contacts) . ' contactos criados com sucesso!');
    }
}
