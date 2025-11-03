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

        // TODO: Adicionar middleware de permissões quando as permissões estiverem configuradas
        // $this->middleware('permission:entities.view')->only(['index', 'show']);
        // $this->middleware('permission:entities.create')->only(['create', 'store']);
        // $this->middleware('permission:entities.edit')->only(['edit', 'update']);
        // $this->middleware('permission:entities.delete')->only(['destroy']);
    }

    /**
     * Mostrar lista de entidades
     */
    public function index(Request $request)
    {
        $query = Entity::with(['creator', 'updater']);

        // Detectar tipo baseado na rota atual
        $routeName = $request->route()->getName();
        $routeType = null;

        if (str_starts_with($routeName, 'clients.')) {
            $routeType = 'client';
        } elseif (str_starts_with($routeName, 'suppliers.')) {
            $routeType = 'supplier';
        }

        // Filtro por tipo da rota (clients, suppliers ou todos)
        if ($routeType) {
            if ($routeType === 'client') {
                $query->whereIn('type', ['client', 'both']);
            } elseif ($routeType === 'supplier') {
                $query->whereIn('type', ['supplier', 'both']);
            }
        }

        // Filtros adicionais
        if ($request->has('type') && !$routeType) {
            $query->where('type', $request->type);
        }

        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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

        // Determinar o template baseado no tipo da rota
        $template = 'Entities/Index';
        $pageTitle = 'Entidades';
        if ($routeType === 'client') {
            $template = 'Clients/Index';
            $pageTitle = 'Clientes';
        } elseif ($routeType === 'supplier') {
            $template = 'Suppliers/Index';
            $pageTitle = 'Fornecedores';
        }

        return Inertia::render($template, [
            'entities' => $entities,
            'filters' => $request->only(['type', 'active', 'search', 'sort', 'direction']),
            'pageTitle' => $pageTitle,
            'entityType' => $routeType ?? 'all',
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
    public function create(Request $request)
    {
        // Detectar contexto baseado na rota
        $routeName = $request->route()->getName();
        $template = 'Entities/Create';
        $defaultType = 'client';

        if (str_starts_with($routeName, 'clients.')) {
            $template = 'Clients/Create';
            $defaultType = 'client';
        } elseif (str_starts_with($routeName, 'suppliers.')) {
            $template = 'Suppliers/Create';
            $defaultType = 'supplier';
        }

        // Próximo número sequencial
        $nextNumber = Entity::max('number') + 1;

        return Inertia::render($template, [
            'nextNumber' => $nextNumber,
            'defaultType' => $defaultType,
            'countries' => [], // TODO: Carregar da tabela países
        ]);
    }

    /**
     * Guardar nova entidade
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['client', 'supplier', 'both'])],
            'number' => 'required|integer|unique:entities,number',
            'nif' => 'required|string|max:20|unique:entities,tax_number',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:2',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'gdpr_consent' => 'boolean',
            'observations' => 'nullable|string|max:1000',
            'active' => 'boolean',
        ]);

        // Mapear campos do formulário para BD
        $entityData = [
            'type' => $validated['type'],
            'number' => $validated['number'],
            'name' => $validated['name'],
            'tax_number' => $validated['nif'], // NIF -> tax_number
            'vat_number' => $validated['nif'], // Usar NIF como VAT também
            'country_code' => $validated['country'],
            'address' => $validated['address'],
            'postal_code' => $validated['postal_code'] ?? null,
            'city' => $validated['city'],
            'country' => $validated['country'] === 'PT' ? 'Portugal' : $validated['country'],
            'phone' => $validated['phone'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'website' => $validated['website'] ?? null,
            'email' => $validated['email'] ?? null,
            'observations' => $validated['observations'] ?? null,
            'active' => $validated['active'] ?? true,
            'created_by' => Auth::id(),
        ];

        // Validar VAT se necessário (usar NIF como VAT)
        if ($entityData['vat_number'] && ViesService::isViesCountry($entityData['country_code'])) {
            $viesResult = $this->viesService->validateVat($entityData['country_code'], $entityData['vat_number']);
            $entityData['vies_valid'] = $viesResult['valid'];
            $entityData['vies_last_check'] = now();
            $entityData['vies_data'] = $viesResult;
        }

        $entity = Entity::create($entityData);

        // Redirecionar baseado no contexto
        $routeName = $request->route()->getName();
        if (str_starts_with($routeName, 'clients.')) {
            return redirect()->route('clients.index')->with('success', 'Cliente criado com sucesso.');
        } elseif (str_starts_with($routeName, 'suppliers.')) {
            return redirect()->route('suppliers.index')->with('success', 'Fornecedor criado com sucesso.');
        }
        
        return redirect()->route('entities.index')->with('success', 'Entidade criada com sucesso.');
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
