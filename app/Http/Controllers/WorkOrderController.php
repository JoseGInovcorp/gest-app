<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderTask;
use App\Models\CustomerOrder;
use App\Models\TaskTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of work orders.
     */
    public function index(Request $request)
    {
        $query = WorkOrder::with(['customerOrder.customer', 'creator', 'tasks']);

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('customerOrder', function ($q) use ($search) {
                        $q->where('number', 'like', "%{$search}%");
                    });
            });
        }

        $workOrders = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('WorkOrders/Index', [
            'workOrders' => $workOrders,
            'filters' => $request->only(['status', 'priority', 'search']),
            'can' => [
                'create' => $request->user()->can('work-orders.create'),
                'view' => $request->user()->can('work-orders.read'),
                'edit' => $request->user()->can('work-orders.update'),
                'delete' => $request->user()->can('work-orders.delete'),
            ],
        ]);
    }

    /**
     * Display my tasks (tasks assigned to current user).
     */
    public function myTasks(Request $request)
    {
        $user = $request->user();
        $userRoles = $user->getRoleNames();

        $query = WorkOrderTask::with(['workOrder.customerOrder.customer', 'assignedUser', 'dependsOn'])
            ->where(function ($q) use ($user, $userRoles) {
                $q->where('assigned_to', $user->id)
                    ->orWhereIn('assigned_group', $userRoles);
            });

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Por padrão, mostrar apenas pendentes e em progresso
            $query->whereIn('status', ['pendente', 'em_progresso']);
        }

        if ($request->filled('overdue')) {
            $query->overdue();
        }

        // Filtro por cliente
        if ($request->filled('customer_id')) {
            $query->whereHas('workOrder.customerOrder', function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            });
        }

        $tasks = $query->orderByRaw("
            CASE status
                WHEN 'em_progresso' THEN 1
                WHEN 'pendente' THEN 2
                WHEN 'concluida' THEN 3
                WHEN 'cancelada' THEN 4
            END
        ")
            ->orderBy('due_date', 'asc')
            ->paginate(20)
            ->withQueryString()
            ->through(fn($task) => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'status' => $task->status,
                'due_date' => $task->due_date,
                'can_start' => $task->can_start,
                'is_overdue' => $task->is_overdue,
                'work_order' => [
                    'id' => $task->workOrder->id,
                    'title' => $task->workOrder->title,
                    'customer_order' => $task->workOrder->customerOrder ? [
                        'customer' => [
                            'name' => $task->workOrder->customerOrder->customer->name ?? null,
                        ],
                    ] : null,
                ],
                'depends_on' => $task->dependsOn ? [
                    'id' => $task->dependsOn->id,
                    'title' => $task->dependsOn->title,
                ] : null,
            ]);

        $taskTemplates = TaskTemplate::active()
            ->orderedBySequence()
            ->get()
            ->mapWithKeys(fn($t) => [$t->code => [
                'label' => $t->label,
                'description' => $t->description,
                'assigned_group' => $t->assigned_group,
            ]]);

        // Buscar clientes únicos das ordens de trabalho do utilizador
        $customers = \App\Models\Entity::whereIn('id', function ($query) use ($user, $userRoles) {
            $query->select('customer_orders.customer_id')
                ->from('customer_orders')
                ->join('work_orders', 'work_orders.customer_order_id', '=', 'customer_orders.id')
                ->join('work_order_tasks', 'work_order_tasks.work_order_id', '=', 'work_orders.id')
                ->where(function ($q) use ($user, $userRoles) {
                    $q->where('work_order_tasks.assigned_to', $user->id)
                        ->orWhereIn('work_order_tasks.assigned_group', $userRoles);
                })
                ->distinct();
        })
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('WorkOrders/MyTasks', [
            'tasks' => $tasks,
            'filters' => $request->only(['status', 'overdue', 'customer_id']),
            'taskTypes' => $taskTemplates,
            'customers' => $customers,
        ]);
    }

    /**
     * Show the form for creating a new work order.
     */
    public function create()
    {
        $customerOrders = CustomerOrder::with('customer')
            ->whereDoesntHave('workOrder')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'number', 'customer_id']);

        $users = User::orderBy('name')->get(['id', 'name']);
        $roles = Role::all(['id', 'name']);

        $taskTemplates = TaskTemplate::active()
            ->orderedBySequence()
            ->get()
            ->mapWithKeys(fn($t) => [$t->code => [
                'label' => $t->label,
                'description' => $t->description,
                'assigned_group' => $t->assigned_group,
            ]]);

        return Inertia::render('WorkOrders/Create', [
            'customerOrders' => $customerOrders,
            'users' => $users,
            'roles' => $roles,
            'taskTypes' => $taskTemplates,
        ]);
    }

    /**
     * Store a newly created work order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_order_id' => 'required|exists:customer_orders,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:baixa,normal,alta,urgente',
            'tasks' => 'required|array|min:1',
            'tasks.*.task_type' => 'required|string',
            'tasks.*.title' => 'required|string',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.assigned_to' => 'nullable|exists:users,id',
            'tasks.*.assigned_group' => 'nullable|string',
            'tasks.*.due_date' => 'nullable|date',
        ]);

        $workOrder = WorkOrder::create([
            'customer_order_id' => $validated['customer_order_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'pendente',
            'created_by' => Auth::id(),
        ]);

        // Criar tarefas
        foreach ($validated['tasks'] as $index => $taskData) {
            WorkOrderTask::create([
                'work_order_id' => $workOrder->id,
                'task_type' => $taskData['task_type'],
                'title' => $taskData['title'],
                'description' => $taskData['description'] ?? null,
                'assigned_to' => $taskData['assigned_to'] ?? null,
                'assigned_group' => $taskData['assigned_group'] ?? null,
                'status' => 'pendente',
                'sequence_order' => $index + 1,
                'depends_on_task_id' => $index > 0 ? $workOrder->tasks()->skip($index - 1)->first()->id : null,
                'due_date' => $taskData['due_date'] ?? null,
            ]);
        }

        activity()
            ->performedOn($workOrder)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('created');

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', "Ordem de trabalho {$workOrder->title} criada com sucesso!");
    }

    /**
     * Display the specified work order.
     */
    public function show(Request $request, WorkOrder $workOrder)
    {
        $workOrder->load([
            'customerOrder.customer',
            'creator',
            'tasks.assignedUser',
            'tasks.dependsOn'
        ]);

        $users = User::orderBy('name')->get(['id', 'name']);
        $roles = Role::all(['id', 'name']);

        $taskTemplates = TaskTemplate::active()
            ->orderedBySequence()
            ->get()
            ->mapWithKeys(fn($t) => [$t->code => [
                'label' => $t->label,
                'description' => $t->description,
                'assigned_group' => $t->assigned_group,
            ]]);

        return Inertia::render('WorkOrders/Show', [
            'workOrder' => $workOrder,
            'users' => $users,
            'roles' => $roles,
            'taskTypes' => $taskTemplates,
            'can' => [
                'edit' => $request->user()->can('work-orders.update'),
                'delete' => $request->user()->can('work-orders.delete'),
            ],
        ]);
    }

    /**
     * Update work order details.
     */
    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:baixa,normal,alta,urgente',
            'status' => 'required|in:pendente,em_progresso,concluida,cancelada',
        ]);

        $workOrder->update($validated);

        activity()
            ->performedOn($workOrder)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('updated');

        return back()->with('success', 'Ordem de trabalho atualizada com sucesso!');
    }

    /**
     * Assign a task to a user.
     */
    public function assignTask(Request $request, WorkOrderTask $task)
    {
        $validated = $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_group' => 'nullable|string',
        ]);

        $task->update($validated);

        activity()
            ->performedOn($task)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('assigned');

        return back()->with('success', 'Tarefa atribuída com sucesso!');
    }

    /**
     * Start a task (change status to em_progresso).
     */
    public function startTask(Request $request, WorkOrderTask $task)
    {
        if (!$task->canStart()) {
            return back()->with('error', 'Esta tarefa depende de outra que ainda não foi concluída.');
        }

        $task->update([
            'status' => 'em_progresso',
            'assigned_to' => $task->assigned_to ?? Auth::id(),
        ]);

        $task->workOrder->updateStatus();

        activity()
            ->performedOn($task)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('started');

        return back()->with('success', 'Tarefa iniciada!');
    }

    /**
     * Complete a task.
     */
    public function completeTask(Request $request, WorkOrderTask $task)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $task->complete($validated['notes'] ?? null);

        activity()
            ->performedOn($task)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'notes' => $validated['notes'] ?? null,
            ])
            ->log('completed');

        return back()->with('success', 'Tarefa concluída!');
    }

    /**
     * Add a new task to work order.
     */
    public function addTask(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'task_type' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'assigned_group' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $lastTask = $workOrder->tasks()->orderBy('sequence_order', 'desc')->first();

        $task = WorkOrderTask::create([
            'work_order_id' => $workOrder->id,
            'task_type' => $validated['task_type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'assigned_group' => $validated['assigned_group'] ?? null,
            'status' => 'pendente',
            'sequence_order' => ($lastTask->sequence_order ?? 0) + 1,
            'due_date' => $validated['due_date'] ?? null,
        ]);

        activity()
            ->performedOn($task)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('created');

        return back()->with('success', 'Tarefa adicionada com sucesso!');
    }

    /**
     * Delete a work order.
     */
    public function destroy(WorkOrder $workOrder)
    {
        activity()
            ->performedOn($workOrder)
            ->causedBy(Auth::user())
            ->withProperties([
                'title' => $workOrder->title,
                'customer_order' => $workOrder->customerOrder->number,
            ])
            ->log('deleted');

        $workOrder->delete();

        return redirect()->route('work-orders.index')
            ->with('success', 'Ordem de trabalho eliminada com sucesso!');
    }
}
