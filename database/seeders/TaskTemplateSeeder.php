<?php

namespace Database\Seeders;

use App\Models\TaskTemplate;
use Illuminate\Database\Seeder;

class TaskTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'code' => 'CONFIRM_ORDER',
                'label' => 'Confirmar Criação de Encomenda',
                'description' => 'Confirmar e validar os detalhes da encomenda do cliente',
                'assigned_group' => 'Gestor Comercial',
                'default_sequence' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'CREATE_CUSTOMER_INVOICE',
                'label' => 'Criar Fatura de Cliente',
                'description' => 'Emitir fatura para o cliente',
                'assigned_group' => 'Gestor Financeiro',
                'default_sequence' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'VALIDATE_STOCK',
                'label' => 'Validar Disponibilidade em Armazém',
                'description' => 'Verificar se os artigos estão disponíveis em stock',
                'assigned_group' => 'Gestor de Armazém',
                'default_sequence' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'CREATE_SUPPLIER_ORDER',
                'label' => 'Criar Encomenda a Fornecedor',
                'description' => 'Criar encomenda ao fornecedor para artigos em falta',
                'assigned_group' => 'Gestor de Compras',
                'default_sequence' => 4,
                'is_active' => true,
            ],
            [
                'code' => 'RECEIVE_STOCK',
                'label' => 'Receção em Armazém',
                'description' => 'Recepcionar artigos encomendados ao fornecedor',
                'assigned_group' => 'Gestor de Armazém',
                'default_sequence' => 5,
                'is_active' => true,
            ],
            [
                'code' => 'WAREHOUSE_PICK',
                'label' => 'Recolha do Armazém',
                'description' => 'Recolher artigos do armazém para preparação',
                'assigned_group' => 'Gestor de Armazém',
                'default_sequence' => 6,
                'is_active' => true,
            ],
            [
                'code' => 'PACKAGING',
                'label' => 'Embalamento',
                'description' => 'Embalar artigos para envio ao cliente',
                'assigned_group' => 'Gestor de Armazém',
                'default_sequence' => 7,
                'is_active' => true,
            ],
            [
                'code' => 'CREATE_SHIPPING_GUIDE',
                'label' => 'Criar Guia de Transporte',
                'description' => 'Gerar guia de transporte para envio',
                'assigned_group' => 'Gestor de Armazém',
                'default_sequence' => 8,
                'is_active' => true,
            ],
            [
                'code' => 'SCHEDULE_PICKUP',
                'label' => 'Agendar Recolha por Transportadora',
                'description' => 'Agendar recolha com transportadora',
                'assigned_group' => 'Gestor de Armazém',
                'default_sequence' => 9,
                'is_active' => true,
            ],
            [
                'code' => 'SHIPPED',
                'label' => 'Encomenda Enviada',
                'description' => 'Marcar encomenda como enviada',
                'assigned_group' => 'Gestor de Armazém',
                'default_sequence' => 10,
                'is_active' => true,
            ],
            [
                'code' => 'READY_FOR_PICKUP',
                'label' => 'Disponível para Levantamento',
                'description' => 'Encomenda pronta para levantamento pelo cliente',
                'assigned_group' => 'Gestor de Armazém',
                'default_sequence' => 11,
                'is_active' => true,
            ],
            [
                'code' => 'DELIVERED',
                'label' => 'Entregue ao Cliente',
                'description' => 'Confirmar entrega ao cliente',
                'assigned_group' => 'Gestor de Armazém',
                'default_sequence' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            TaskTemplate::updateOrCreate(
                ['code' => $template['code']],
                $template
            );
        }
    }
}
