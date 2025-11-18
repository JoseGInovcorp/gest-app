<?php

namespace App\Http\Controllers;

use App\Models\TaskTemplate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class TaskTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:task-templates.read')->only(['index']);
        $this->middleware('permission:task-templates.create')->only(['create', 'store']);
        $this->middleware('permission:task-templates.update')->only(['edit', 'update']);
        $this->middleware('permission:task-templates.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TaskTemplate::query();

        // Filtros
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('label', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $templates = $query->orderBy('default_sequence', 'asc')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('TaskTemplates/Index', [
            'templates' => $templates,
            'filters' => $request->only(['search', 'is_active']),
            'can' => [
                'create' => $request->user()->can('task-templates.create'),
                'update' => $request->user()->can('task-templates.update'),
                'delete' => $request->user()->can('task-templates.delete'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all(['id', 'name']);

        return Inertia::render('TaskTemplates/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:task_templates,code',
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_group' => 'nullable|string',
            'default_sequence' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        TaskTemplate::create($validated);

        return redirect()->route('task-templates.index')
            ->with('success', 'Template de tarefa criado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TaskTemplate $taskTemplate)
    {
        $roles = Role::all(['id', 'name']);

        return Inertia::render('TaskTemplates/Edit', [
            'template' => $taskTemplate,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskTemplate $taskTemplate)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:task_templates,code,' . $taskTemplate->id,
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_group' => 'nullable|string',
            'default_sequence' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $taskTemplate->update($validated);

        return redirect()->route('task-templates.index')
            ->with('success', 'Template de tarefa atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskTemplate $taskTemplate)
    {
        $taskTemplate->delete();

        return redirect()->route('task-templates.index')
            ->with('success', 'Template de tarefa eliminado com sucesso!');
    }
}
