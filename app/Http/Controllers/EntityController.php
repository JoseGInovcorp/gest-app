<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Country;
use App\Services\ViesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
     * Verificar se NIF já existe na base de dados
     */
    public function checkNifExists(Request $request, $nif)
    {
        $exists = Entity::where('tax_number', $nif)->exists();

        return response()->json([
            'exists' => $exists,
            'nif' => $nif,
            'message' => $exists ? 'NIF já existe na base de dados' : 'NIF disponível'
        ]);
    }

    /**
     * Consultar dados da empresa via VIES
     */
    public function viesLookup(Request $request, $country, $nif)
    {
        // Verificar se é país UE
        if (!$this->viesService->isViesCountry($country)) {
            return response()->json([
                'success' => false,
                'message' => 'País não suporta validação VIES',
                'country' => $country
            ]);
        }

        try {
            $result = $this->viesService->validateVat($country, $nif);

            if ($result['valid']) {
                return response()->json([
                    'success' => true,
                    'valid' => true,
                    'data' => [
                        'name' => $result['company_name'] ?? '',
                        'address' => $result['company_address'] ?? '',
                        'country' => $country,
                        'nif' => $nif,
                    ],
                    'message' => 'Dados obtidos via VIES'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'NIF não válido no sistema VIES',
                    'error' => $result['error'] ?? 'NIF inválido'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar VIES: ' . $e->getMessage()
            ], 500);
        }
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
                'create' => true, // Temporariamente true até implementar permissões
                'edit' => true,
                'delete' => true,
                'export' => true,
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
            'countries' => Country::active()->orderBy('name')->get(['code', 'name', 'vies_enabled']),
        ]);
    }

    /**
     * Guardar nova entidade
     */
    public function store(Request $request)
    {
        // Debug: Log da request recebida
        \Log::info('Store request received', [
            'url' => $request->url(),
            'method' => $request->method(),
            'all_data' => $request->all()
        ]);

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

        // Log activity
        activity()
            ->performedOn($entity)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('created');

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
        $countries = Country::where('active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Entities/Edit', [
            'entity' => $entity,
            'countries' => $countries
        ]);
    }

    /**
     * Atualizar entidade
     */
    public function update(Request $request, Entity $entity)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['client', 'supplier', 'both'])],
            'number' => 'required|integer|unique:entities,number,' . $entity->id,
            'nif' => 'required|string|max:20|unique:entities,tax_number,' . $entity->id,
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
            'updated_by' => Auth::id(),
        ];

        // Validar VAT se necessário (usar NIF como VAT) e se mudou
        if ($entityData['vat_number'] !== $entity->vat_number && ViesService::isViesCountry($entityData['country_code'])) {
            $viesResult = $this->viesService->validateVat($entityData['country_code'], $entityData['vat_number']);
            $entityData['vies_valid'] = $viesResult['valid'];
            $entityData['vies_last_check'] = now();
            $entityData['vies_data'] = $viesResult;
        }

        $entity->update($entityData);

        // Log activity
        activity()
            ->performedOn($entity)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('updated');

        // Redirecionar baseado no contexto
        $routeName = $request->route()->getName();
        if (str_starts_with($routeName, 'clients.')) {
            return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso.');
        } elseif (str_starts_with($routeName, 'suppliers.')) {
            return redirect()->route('suppliers.index')->with('success', 'Fornecedor atualizado com sucesso.');
        }

        return redirect()->route('entities.index')->with('success', 'Entidade atualizada com sucesso.');
    }

    /**
     * Eliminar entidade (soft delete)
     */
    public function destroy(Entity $entity)
    {
        // Log activity antes de eliminar
        activity()
            ->performedOn($entity)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'entity_name' => $entity->name,
            ])
            ->log('deleted');

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
