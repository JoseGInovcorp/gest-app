<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Entity;
use App\Models\Contact;

class RefreshEntitiesSeeder extends Seeder
{
    private int $entityNumber = 1;
    private int $contactNumber = 1;

    /**
     * Repovoar base de dados com clientes e fornecedores da área informática
     */
    public function run(): void
    {
        // Desabilitar foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Limpar dados existentes
        echo "🗑️  Limpando dados existentes...\n";
        Contact::query()->forceDelete();
        Entity::query()->forceDelete();

        // Re-habilitar foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        echo "✨ Criando novos clientes e fornecedores...\n\n";

        // ============================================================
        // CLIENTES (Empresas da área de IT)
        // ============================================================

        // 1. CLIENTE: Startup de Software
        $startup = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '509123456',
            'name' => 'TechVision, Lda',
            'type' => 'client',
            'address' => 'Rua dos Fanqueiros, 276',
            'postal_code' => '1100-232',
            'city' => 'Lisboa',
            'country' => 'Portugal',
            'phone' => '+351 213 456 789',
            'email' => 'geral@techvision.pt',
            'website' => 'https://techvision.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Startup de desenvolvimento de software. Necessita regularmente de equipamento IT para a equipa.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $startup->id,
            'first_name' => 'Miguel',
            'last_name' => 'Ferreira',
            'function' => 'Diretor Geral',
            'email' => 'miguel.ferreira@techvision.pt',
            'phone' => '+351 213 456 789',
            'mobile' => '+351 912 345 678',
            'rgpd_consent' => true,
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $startup->id,
            'first_name' => 'Ana',
            'last_name' => 'Costa',
            'function' => 'Responsável de IT',
            'email' => 'ana.costa@techvision.pt',
            'mobile' => '+351 934 567 890',
            'rgpd_consent' => true,
        ]);

        // 2. CLIENTE: Agência de Marketing Digital
        $agencia = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '509234567',
            'name' => 'Digital Creators, S.A.',
            'type' => 'client',
            'address' => 'Avenida da Liberdade, 123',
            'postal_code' => '1250-140',
            'city' => 'Lisboa',
            'country' => 'Portugal',
            'phone' => '+351 217 890 123',
            'email' => 'contacto@digitalcreators.pt',
            'website' => 'https://digitalcreators.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Agência criativa com 30+ funcionários. Cliente regular de equipamentos Apple.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $agencia->id,
            'first_name' => 'Carla',
            'last_name' => 'Rodrigues',
            'function' => 'Diretor Comercial',
            'email' => 'carla.rodrigues@digitalcreators.pt',
            'phone' => '+351 217 890 123',
            'mobile' => '+351 918 765 432',
            'rgpd_consent' => true,
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $agencia->id,
            'first_name' => 'Pedro',
            'last_name' => 'Santos',
            'function' => 'Responsável de IT',
            'email' => 'pedro.santos@digitalcreators.pt',
            'mobile' => '+351 925 678 901',
            'rgpd_consent' => true,
        ]);

        // 3. CLIENTE: Escritório de Contabilidade
        $contabilidade = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '509345678',
            'name' => 'ContaGest - Contabilidade e Gestão, Lda',
            'type' => 'client',
            'address' => 'Rua do Comércio, 45',
            'postal_code' => '4000-203',
            'city' => 'Porto',
            'country' => 'Portugal',
            'phone' => '+351 223 456 789',
            'email' => 'geral@contagest.pt',
            'website' => 'https://contagest.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Gabinete de contabilidade com necessidades de servidores e backups seguros.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $contabilidade->id,
            'first_name' => 'António',
            'last_name' => 'Silva',
            'function' => 'Diretor Geral',
            'email' => 'antonio.silva@contagest.pt',
            'phone' => '+351 223 456 789',
            'mobile' => '+351 916 789 012',
            'rgpd_consent' => true,
        ]);

        // 4. CLIENTE: Clínica Médica
        $clinica = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '509456789',
            'name' => 'HealthCare Center, S.A.',
            'type' => 'client',
            'address' => 'Rua da Saúde, 89',
            'postal_code' => '2750-341',
            'city' => 'Cascais',
            'country' => 'Portugal',
            'phone' => '+351 214 567 890',
            'email' => 'info@healthcarecenter.pt',
            'website' => 'https://healthcarecenter.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Clínica privada com necessidades de sistemas RGPD compliant e backups médicos.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $clinica->id,
            'first_name' => 'Maria',
            'last_name' => 'Almeida',
            'function' => 'Diretor Geral',
            'email' => 'maria.almeida@healthcarecenter.pt',
            'phone' => '+351 214 567 890',
            'mobile' => '+351 927 890 123',
            'rgpd_consent' => true,
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $clinica->id,
            'first_name' => 'João',
            'last_name' => 'Pereira',
            'function' => 'Responsável de Compras',
            'email' => 'joao.pereira@healthcarecenter.pt',
            'mobile' => '+351 938 901 234',
            'rgpd_consent' => true,
        ]);

        // 5. CLIENTE: Escola Privada
        $escola = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '509567890',
            'name' => 'Colégio Internacional de Lisboa',
            'type' => 'client',
            'address' => 'Avenida dos Descobrimentos, 200',
            'postal_code' => '1400-092',
            'city' => 'Lisboa',
            'country' => 'Portugal',
            'phone' => '+351 218 901 234',
            'email' => 'administracao@colegio-lisboa.pt',
            'website' => 'https://colegio-lisboa.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Escola com 500+ alunos. Necessita de equipamento para salas de aula e laboratórios.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $escola->id,
            'first_name' => 'Ricardo',
            'last_name' => 'Oliveira',
            'function' => 'Diretor Geral',
            'email' => 'ricardo.oliveira@colegio-lisboa.pt',
            'phone' => '+351 218 901 234',
            'mobile' => '+351 919 012 345',
            'rgpd_consent' => true,
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $escola->id,
            'first_name' => 'Tiago',
            'last_name' => 'Martins',
            'function' => 'Responsável de IT',
            'email' => 'tiago.martins@colegio-lisboa.pt',
            'mobile' => '+351 926 123 456',
            'rgpd_consent' => true,
        ]);

        // 6. CLIENTE: Loja Online
        $loja = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '509678901',
            'name' => 'Fashion Online Portugal, Lda',
            'type' => 'client',
            'address' => 'Rua do Comércio Eletrónico, 33',
            'postal_code' => '2795-195',
            'city' => 'Linda-a-Velha',
            'country' => 'Portugal',
            'phone' => '+351 214 123 456',
            'email' => 'tech@fashiononline.pt',
            'website' => 'https://fashiononline.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'E-commerce de moda. Necessita de servidores, segurança e infraestrutura cloud.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $loja->id,
            'first_name' => 'Sofia',
            'last_name' => 'Mendes',
            'function' => 'Diretor Comercial',
            'email' => 'sofia.mendes@fashiononline.pt',
            'mobile' => '+351 917 234 567',
            'rgpd_consent' => true,
        ]);

        // 7. CLIENTE: Pequena Empresa Industrial
        $industrial = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '509789012',
            'name' => 'MetalTech Industries, S.A.',
            'type' => 'client',
            'address' => 'Zona Industrial de Setúbal, Lote 15',
            'postal_code' => '2910-692',
            'city' => 'Setúbal',
            'country' => 'Portugal',
            'phone' => '+351 265 234 567',
            'email' => 'info@metaltech.pt',
            'website' => 'https://metaltech.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Indústria metalomecânica. Cliente de soluções ERP e equipamento industrial.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $industrial->id,
            'first_name' => 'Carlos',
            'last_name' => 'Nunes',
            'function' => 'Diretor Geral',
            'email' => 'carlos.nunes@metaltech.pt',
            'phone' => '+351 265 234 567',
            'mobile' => '+351 928 345 678',
            'rgpd_consent' => true,
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $industrial->id,
            'first_name' => 'Luís',
            'last_name' => 'Correia',
            'function' => 'Responsável de Compras',
            'email' => 'luis.correia@metaltech.pt',
            'mobile' => '+351 939 456 789',
            'rgpd_consent' => true,
        ]);

        // ============================================================
        // FORNECEDORES (Distribuidores IT)
        // ============================================================

        // 1. FORNECEDOR: Distribuidor HP/Dell
        $hp = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '502123456',
            'name' => 'Tech Distribution Portugal, S.A.',
            'type' => 'supplier',
            'address' => 'Estrada Nacional 10, Km 3',
            'postal_code' => '2695-066',
            'city' => 'Bobadela',
            'country' => 'Portugal',
            'phone' => '+351 219 876 543',
            'email' => 'vendas@techdist.pt',
            'website' => 'https://techdist.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Distribuidor oficial HP, Dell, Lenovo. Principal fornecedor de computadores e servidores.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $hp->id,
            'first_name' => 'Bruno',
            'last_name' => 'Carvalho',
            'function' => 'Diretor Comercial',
            'email' => 'bruno.carvalho@techdist.pt',
            'phone' => '+351 219 876 543',
            'mobile' => '+351 911 222 333',
            'rgpd_consent' => true,
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $hp->id,
            'first_name' => 'Rita',
            'last_name' => 'Sousa',
            'function' => 'Diretor Técnico',
            'email' => 'rita.sousa@techdist.pt',
            'mobile' => '+351 922 333 444',
            'rgpd_consent' => true,
        ]);

        // 2. FORNECEDOR: Apple Distributor
        $apple = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '502234567',
            'name' => 'iStore Portugal - Distribuição, Lda',
            'type' => 'supplier',
            'address' => 'Avenida 5 de Outubro, 77',
            'postal_code' => '1050-050',
            'city' => 'Lisboa',
            'country' => 'Portugal',
            'phone' => '+351 213 456 777',
            'email' => 'b2b@istore.pt',
            'website' => 'https://istore.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Revendedor autorizado Apple. Fornecedor de MacBooks, iMacs, iPads.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $apple->id,
            'first_name' => 'Gonçalo',
            'last_name' => 'Ribeiro',
            'function' => 'Diretor Comercial',
            'email' => 'goncalo.ribeiro@istore.pt',
            'phone' => '+351 213 456 777',
            'mobile' => '+351 933 444 555',
            'rgpd_consent' => true,
        ]);

        // 3. FORNECEDOR: Componentes e Periféricos
        $componentes = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '502345678',
            'name' => 'PCDiga - Comércio de Componentes, S.A.',
            'type' => 'supplier',
            'address' => 'Rua Alfredo da Silva, 8',
            'postal_code' => '2610-016',
            'city' => 'Amadora',
            'country' => 'Portugal',
            'phone' => '+351 214 999 888',
            'email' => 'empresas@pcdiga.com',
            'website' => 'https://pcdiga.com',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Loja de componentes e periféricos. Fornecedor de ratos, teclados, monitores, SSD, RAM.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $componentes->id,
            'first_name' => 'Nuno',
            'last_name' => 'Dias',
            'function' => 'Diretor Técnico',
            'email' => 'nuno.dias@pcdiga.com',
            'mobile' => '+351 944 555 666',
            'rgpd_consent' => true,
        ]);

        // 4. FORNECEDOR: Networking (Cisco, Ubiquiti)
        $network = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '502456789',
            'name' => 'NetSolutions Portugal, Lda',
            'type' => 'supplier',
            'address' => 'Parque das Nações, Lote 7',
            'postal_code' => '1990-079',
            'city' => 'Lisboa',
            'country' => 'Portugal',
            'phone' => '+351 218 888 999',
            'email' => 'vendas@netsolutions.pt',
            'website' => 'https://netsolutions.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Distribuidor Cisco, Ubiquiti, MikroTik. Especialista em equipamento de rede.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $network->id,
            'first_name' => 'André',
            'last_name' => 'Lopes',
            'function' => 'Diretor Comercial',
            'email' => 'andre.lopes@netsolutions.pt',
            'phone' => '+351 218 888 999',
            'mobile' => '+351 955 666 777',
            'rgpd_consent' => true,
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $network->id,
            'first_name' => 'Patrícia',
            'last_name' => 'Ramos',
            'function' => 'Técnico',
            'email' => 'patricia.ramos@netsolutions.pt',
            'mobile' => '+351 966 777 888',
            'rgpd_consent' => true,
        ]);

        // 5. FORNECEDOR: Software e Licenças Microsoft
        $microsoft = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '502567890',
            'name' => 'SoftwareHouse Portugal, S.A.',
            'type' => 'supplier',
            'address' => 'Avenida da República, 50',
            'postal_code' => '1050-196',
            'city' => 'Lisboa',
            'country' => 'Portugal',
            'phone' => '+351 217 777 666',
            'email' => 'licensing@softwarehouse.pt',
            'website' => 'https://softwarehouse.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Revendedor Microsoft CSP. Fornecedor de licenças Office 365, Windows, Azure.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $microsoft->id,
            'first_name' => 'Isabel',
            'last_name' => 'Ferreira',
            'function' => 'Diretor Comercial',
            'email' => 'isabel.ferreira@softwarehouse.pt',
            'phone' => '+351 217 777 666',
            'mobile' => '+351 977 888 999',
            'rgpd_consent' => true,
        ]);

        // 6. FORNECEDOR: Impressoras e Consumíveis
        $impressoras = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '502678901',
            'name' => 'PrintPro - Soluções de Impressão, Lda',
            'type' => 'supplier',
            'address' => 'Rua da Indústria, 12',
            'postal_code' => '4470-177',
            'city' => 'Maia',
            'country' => 'Portugal',
            'phone' => '+351 229 666 555',
            'email' => 'vendas@printpro.pt',
            'website' => 'https://printpro.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Distribuidor HP, Epson, Brother. Fornecedor de impressoras, multifunções e toners.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $impressoras->id,
            'first_name' => 'Fernando',
            'last_name' => 'Costa',
            'function' => 'Diretor Técnico',
            'email' => 'fernando.costa@printpro.pt',
            'mobile' => '+351 988 999 000',
            'rgpd_consent' => true,
        ]);

        // 7. FORNECEDOR: Servidores e Storage
        $servidores = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '502789012',
            'name' => 'ServerTech Portugal, S.A.',
            'type' => 'supplier',
            'address' => 'Avenida Marechal Gomes da Costa, 33',
            'postal_code' => '1800-255',
            'city' => 'Lisboa',
            'country' => 'Portugal',
            'phone' => '+351 218 555 444',
            'email' => 'enterprise@servertech.pt',
            'website' => 'https://servertech.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Especialista em servidores Dell PowerEdge, HPE ProLiant, Synology NAS.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $servidores->id,
            'first_name' => 'Rui',
            'last_name' => 'Tavares',
            'function' => 'Diretor Comercial',
            'email' => 'rui.tavares@servertech.pt',
            'phone' => '+351 218 555 444',
            'mobile' => '+351 999 000 111',
            'rgpd_consent' => true,
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $servidores->id,
            'first_name' => 'Mariana',
            'last_name' => 'Silva',
            'function' => 'Técnico',
            'email' => 'mariana.silva@servertech.pt',
            'mobile' => '+351 910 111 222',
            'rgpd_consent' => true,
        ]);

        // ============================================================
        // ENTIDADES MISTAS (Cliente + Fornecedor)
        // ============================================================

        // 1. AMBOS: Parceiro IT que também compra serviços
        $parceiro = Entity::create([
            'number' => $this->entityNumber++,
            'tax_number' => '509890123',
            'name' => 'ITPartner Soluções Informáticas, Lda',
            'type' => 'both',
            'address' => 'Rua Engenheiro Ferreira Dias, 924',
            'postal_code' => '4100-246',
            'city' => 'Porto',
            'country' => 'Portugal',
            'phone' => '+351 225 444 333',
            'email' => 'geral@itpartner.pt',
            'website' => 'https://itpartner.pt',
            'active' => true,
            'tax_exempt' => false,
            'notes' => 'Parceiro IT. Cliente de serviços de outsourcing e fornecedor de equipamento especializado.',
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $parceiro->id,
            'first_name' => 'Vasco',
            'last_name' => 'Cunha',
            'function' => 'Diretor Geral',
            'email' => 'vasco.cunha@itpartner.pt',
            'phone' => '+351 225 444 333',
            'mobile' => '+351 921 333 444',
            'rgpd_consent' => true,
        ]);

        Contact::create([
            'number' => $this->contactNumber++,
            'entity_id' => $parceiro->id,
            'first_name' => 'Helena',
            'last_name' => 'Cardoso',
            'function' => 'Diretor Comercial',
            'email' => 'helena.cardoso@itpartner.pt',
            'mobile' => '+351 932 444 555',
            'rgpd_consent' => true,
        ]);

        echo "\n✅ Seeders executados com sucesso!\n\n";
        echo "📊 Resumo:\n";
        echo "   • Clientes: " . Entity::where('type', 'client')->count() . "\n";
        echo "   • Fornecedores: " . Entity::where('type', 'supplier')->count() . "\n";
        echo "   • Ambos: " . Entity::where('type', 'both')->count() . "\n";
        echo "   • Total Entidades: " . Entity::count() . "\n";
        echo "   • Total Contactos: " . Contact::count() . "\n";
    }
}
