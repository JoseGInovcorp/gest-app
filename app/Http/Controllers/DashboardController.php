<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\CustomerOrder;
use App\Models\SupplierInvoice;
use App\Models\WorkOrderTask;
use App\Models\BankAccount;
use App\Models\ClientAccount;
use App\Models\WorkOrder;
use App\Models\SupplierOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $stats = [];

        // Propostas Pendentes (se tiver permissão)
        if ($user->can('proposals.read')) {
            $stats['proposals_pending'] = Proposal::where('estado', 'rascunho')->count();
        }

        // Encomendas Cliente Ativas (se tiver permissão)
        if ($user->can('customer-orders.read')) {
            $stats['customer_orders_active'] = CustomerOrder::whereNotIn('status', ['concluida', 'cancelada'])->count();
        }

        // Faturas Fornecedor Pendentes (se tiver permissão)
        if ($user->can('supplier-invoices.read')) {
            $stats['supplier_invoices_pending'] = SupplierInvoice::where('estado', 'pendente')->count();
        }

        // Ordens de Trabalho Em Progresso (se tiver permissão)
        if ($user->can('work-orders.read')) {
            $stats['work_orders_in_progress'] = WorkOrder::where('status', 'em_progresso')->count();
        }

        // Encomendas Fornecedor Pendentes (se tiver permissão)
        if ($user->can('supplier-orders.read')) {
            $stats['supplier_orders_pending'] = SupplierOrder::whereIn('status', ['draft', 'sent'])->count();
        }

        // Saldo Total Contas Bancárias (se tiver permissão)
        if ($user->can('bank-accounts.read')) {
            $stats['bank_accounts_balance'] = BankAccount::where('estado', 'ativa')
                ->sum('saldo_atual') ?? 0;
        }

        // Clientes com Saldo Devedor (se tiver permissão)
        if ($user->can('client-accounts.read')) {
            // Obter o último movimento de cada cliente e contar quantos têm saldo positivo (devem à empresa)
            $stats['clients_with_debt'] = ClientAccount::selectRaw('entity_id, MAX(id) as last_movement_id')
                ->groupBy('entity_id')
                ->get()
                ->filter(function ($item) {
                    $lastMovement = ClientAccount::find($item->last_movement_id);
                    return $lastMovement && $lastMovement->saldo_apos > 0;
                })
                ->count();
        }

        // Minhas Tarefas Pendentes (sempre visível)
        $userRoles = $user->getRoleNames();

        $myTasksQuery = WorkOrderTask::where(function ($query) use ($user, $userRoles) {
            $query->where('assigned_to', $user->id)
                ->orWhereIn('assigned_group', $userRoles);
        })
            ->whereIn('status', ['pendente', 'em_progresso']);

        $stats['my_tasks_pending'] = $myTasksQuery->count();

        // Minhas Tarefas Urgentes (próximas 5 por prazo)
        $urgentTasks = (clone $myTasksQuery)
            ->with(['workOrder.customerOrder.customer', 'assignedUser', 'dependsOn'])
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'work_order_id' => $task->work_order_id,
                    'work_order_title' => $task->workOrder->title ?? 'N/A',
                    'customer_name' => $task->workOrder->customerOrder->customer->nome ?? 'N/A',
                    'task_type' => $task->task_type,
                    'due_date' => $task->due_date?->format('Y-m-d'),
                    'status' => $task->status,
                    'is_overdue' => $task->due_date && $task->due_date->isPast() && $task->status !== 'concluida',
                    'assigned_to_name' => $task->assignedUser?->name ?? $task->assigned_group ?? 'Não atribuído',
                ];
            });

        // Atividade Recente (apenas para Super Admin)
        $recentActivity = [];
        if ($user->hasRole('Super Admin')) {
            $recentActivity = Activity::with('causer')
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'description' => $activity->description,
                        'subject_type' => class_basename($activity->subject_type ?? 'Sistema'),
                        'causer_name' => $activity->causer->name ?? 'Sistema',
                        'created_at' => $activity->created_at->diffForHumans(),
                    ];
                })
                ->values();
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'urgentTasks' => $urgentTasks,
            'recentActivity' => $recentActivity,
            'permissions' => [
                'canViewProposals' => $user->can('proposals.read'),
                'canViewCustomerOrders' => $user->can('customer-orders.read'),
                'canViewSupplierInvoices' => $user->can('supplier-invoices.read'),
                'canViewWorkOrders' => $user->can('work-orders.read'),
                'canViewSupplierOrders' => $user->can('supplier-orders.read'),
                'canViewBankAccounts' => $user->can('bank-accounts.read'),
                'canViewClientAccounts' => $user->can('client-accounts.read'),
                'canViewRecentActivity' => $user->hasRole('Super Admin'),
            ],
        ]);
    }
}
