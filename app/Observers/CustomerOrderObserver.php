<?php

namespace App\Observers;

use App\Models\CustomerOrder;
use App\Models\WorkOrder;
use App\Models\WorkOrderTask;
use App\Models\TaskTemplate;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\ClientAccount;
use Illuminate\Support\Facades\Auth;

class CustomerOrderObserver
{
    /**
     * Handle the CustomerOrder "created" event.
     */
    public function created(CustomerOrder $customerOrder): void
    {
        // Criar Work Order automaticamente
        $workOrder = WorkOrder::create([
            'customer_order_id' => $customerOrder->id,
            'title' => "Processar Encomenda {$customerOrder->number}",
            'description' => "Workflow automático para encomenda de cliente",
            'priority' => 'normal',
            'status' => 'pendente',
            'created_by' => Auth::id() ?? 1,
        ]);

        // Buscar templates ativos ordenados por sequência
        $templates = TaskTemplate::active()
            ->orderedBySequence()
            ->get();

        // Criar tarefas baseadas nos templates
        $previousTask = null;

        foreach ($templates as $index => $template) {
            $task = WorkOrderTask::create([
                'work_order_id' => $workOrder->id,
                'task_type' => $template->code,
                'title' => $template->label,
                'description' => $template->description,
                'assigned_group' => $template->assigned_group,
                'status' => 'pendente',
                'sequence_order' => $template->default_sequence,
                'depends_on_task_id' => $previousTask ? $previousTask->id : null,
                'due_date' => now()->addDays($template->default_sequence),
            ]);

            $previousTask = $task;
        }
    }

    /**
     * Handle the CustomerOrder "updated" event.
     */
    public function updated(CustomerOrder $customerOrder): void
    {
        // Verificar se o status mudou para 'closed' (encomenda fechada/paga)
        if ($customerOrder->isDirty('status') && $customerOrder->status === 'closed') {
            // Buscar conta bancária principal (Conta Corrente Principal)
            $bankAccount = BankAccount::where('nome', 'Conta Corrente Principal')
                ->orWhere('tipo', 'corrente')
                ->first();

            if ($bankAccount) {
                // Criar movimento de DÉBITO na conta corrente do cliente
                // (cliente pagou, logo reduz o que ele deve à empresa)
                ClientAccount::create([
                    'entity_id' => $customerOrder->customer_id,
                    'data_movimento' => now(),
                    'tipo' => 'debito',
                    'valor' => $customerOrder->total_value,
                    'descricao' => "Pagamento Encomenda {$customerOrder->number}",
                    'categoria' => 'pagamento',
                    'referencia' => $customerOrder->number,
                ]);

                // Criar movimento bancário (crédito - entrada de dinheiro)
                BankTransaction::create([
                    'bank_account_id' => $bankAccount->id,
                    'data_movimento' => now(),
                    'descricao' => "Recebimento Encomenda {$customerOrder->number} - {$customerOrder->customer->name}",
                    'tipo' => 'credito',
                    'valor' => $customerOrder->total_value,
                    'referencia' => $customerOrder->number,
                    'categoria' => 'recebimento',
                    'observacoes' => "Cliente: {$customerOrder->customer->name}",
                ]);
            }
        }
    }

    /**
     * Handle the CustomerOrder "deleted" event.
     */
    public function deleted(CustomerOrder $customerOrder): void
    {
        //
    }

    /**
     * Handle the CustomerOrder "restored" event.
     */
    public function restored(CustomerOrder $customerOrder): void
    {
        //
    }

    /**
     * Handle the CustomerOrder "force deleted" event.
     */
    public function forceDeleted(CustomerOrder $customerOrder): void
    {
        //
    }
}
