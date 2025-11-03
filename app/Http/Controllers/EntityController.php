<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Services\ViesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EntityController extends Controller
{
    private ViesService $viesService;

    public function __construct(ViesService $viesService)
    {
        $this->viesService = $viesService;
        
        // Aplicar middleware de permissões
        $this->middleware('permission:entities.view')->only(['index', 'show']);
        $this->middleware('permission:entities.create')->only(['create', 'store']);
        $this->middleware('permission:entities.edit')->only(['edit', 'update']);
        $this->middleware('permission:entities.delete')->only(['destroy']);
    }

    /**
     * Mostrar lista de entidades
     */
    public function index(Request $request)
    {
        $query = Entity::with(['creator', 'updater']);

        // Filtros
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('commercial_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('tax_number', 'LIKE', "%{$search}%");
            });
        }

        // Ordenação
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $entities = $query->paginate(15)->withQueryString();

        return Inertia::render('Entities/Index', [
            'entities' => $entities,
            'filters' => $request->only(['type', 'active', 'search', 'sort', 'direction']),
            'can' => [
                'create' => Auth::user()->can('entities.create'),
                'edit' => Auth::user()->can('entities.edit'),
                'delete' => Auth::user()->can('entities.delete'),
                'export' => Auth::user()->can('entities.export'),
            ]
        ]);
    }

    /**
     * Mostrar formulário de criação
     */
    public function create()
    {
        return Inertia::render('Entities/Create');
    }

    /**
     * Guardar nova entidade
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['client', 'supplier', 'both'])],
            'name' => 'required|string|max:255',
            'commercial_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_number' => 'nullable|string|max:20|unique:entities,tax_number',
            'vat_number' => 'nullable|string|max:20',
            'country_code' => 'required|string|size:2',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
        ]);

        $validated['created_by'] = Auth::id();

        // Validar VAT se necessário
        if (isset($validated['vat_number']) && $validated['vat_number'] && ViesService::isViesCountry($validated['country_code'])) {
            $viesResult = $this->viesService->validateVat($validated['country_code'], $validated['vat_number']);
            $validated['vies_valid'] = $viesResult['valid'];
            $validated['vies_last_check'] = now();
            $validated['vies_data'] = $viesResult;
        }

        $entity = Entity::create($validated);

        return redirect()->route('entities.show', $entity)
            ->with('success', 'Entidade criada com sucesso.');
    }

    /**
     * Mostrar detalhes da entidade
     */
    public function show(Entity $entity)
    {
        $entity->load(['creator', 'updater']);

        return Inertia::render('Entities/Show', [
            'entity' => $entity,
            'can' => [
                'edit' => Auth::user()->can('entities.edit'),
                'delete' => Auth::user()->can('entities.delete'),
            ]
        ]);
    }

    /**
     * Mostrar formulário de edição
     */
    public function edit(Entity $entity)
    {
        return Inertia::render('Entities/Edit', [
            'entity' => $entity
        ]);
    }

    /**
     * Atualizar entidade
     */
    public function update(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['client', 'supplier', 'both'])],
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $entity->update($validated);

        return redirect()->route('entities.show', $entity)
            ->with('success', 'Entidade atualizada com sucesso.');
    }

    /**
     * Eliminar entidade (soft delete)
     */
    public function destroy(Entity $entity)
    {
        $entity->delete();

        return redirect()->route('entities.index')
            ->with('success', 'Entidade eliminada com sucesso.');
    }

    /**
     * Revalidar VAT via VIES
     */
    public function revalidateVat(Entity $entity)
    {
        if (!$entity->vat_number || !ViesService::isViesCountry($entity->country_code)) {
            return back()->with('error', 'Entidade não tem VAT válido para revalidação.');
        }

        $viesResult = $this->viesService->validateVat($entity->country_code, $entity->vat_number);
        
        $entity->update([
            'vies_valid' => $viesResult['valid'],
            'vies_last_check' => now(),
            'vies_data' => $viesResult,
            'updated_by' => Auth::id(),
        ]);

        $message = $viesResult['valid'] ? 'VAT validado com sucesso!' : 'VAT inválido segundo VIES.';
        $type = $viesResult['valid'] ? 'success' : 'warning';

        return back()->with($type, $message);
    }
}
