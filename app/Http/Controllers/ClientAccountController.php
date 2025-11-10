<?php

namespace App\Http\Controllers;

use App\Models\ClientAccount;
use App\Models\Entity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obter parâmetros de filtro
        $entityId = $request->input('entity_id');
        $tipo = $request->input('tipo');
        $categoria = $request->input('categoria');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');

        // Query base
        $query = ClientAccount::with('entity');

        // Filtrar por cliente se especificado
        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        // Filtrar por tipo (débito/crédito)
        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        // Filtrar por categoria
        if ($categoria) {
            $query->where('categoria', $categoria);
        }

        // Filtrar por intervalo de datas
        if ($startDate && $endDate) {
            $query->whereBetween('data_movimento', [$startDate, $endDate]);
        }

        // Pesquisa geral (descrição ou referência)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                    ->orWhere('referencia', 'like', "%{$search}%");
            });
        }

        // Ordenar por data (mais recente primeiro)
        $movements = $query->orderBy('data_movimento', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Obter lista de clientes para o filtro
        $entities = Entity::whereIn('type', ['client', 'both'])
            ->orderBy('name')
            ->get(['id', 'name']);

        // Se um cliente específico estiver selecionado, obter estatísticas
        $stats = null;
        if ($entityId) {
            $stats = ClientAccount::getEntityStats($entityId);
            $selectedEntity = Entity::find($entityId);
            $stats['entity_name'] = $selectedEntity ? $selectedEntity->name : null;
        }

        return Inertia::render('ClientAccounts/Index', [
            'movements' => $movements,
            'entities' => $entities,
            'stats' => $stats,
            'filters' => [
                'entity_id' => $entityId,
                'tipo' => $tipo,
                'categoria' => $categoria,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obter lista de clientes
        $entities = Entity::whereIn('type', ['client', 'both'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('ClientAccounts/Create', [
            'entities' => $entities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'data_movimento' => 'required|date',
            'tipo' => 'required|in:debito,credito',
            'valor' => 'required|numeric|min:0.01',
            'descricao' => 'required|string|max:255',
            'categoria' => 'required|in:fatura,pagamento,nota_credito,nota_debito,juros,ajuste,outros',
            'referencia' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        ClientAccount::create($validated);

        return redirect()->route('client-accounts.index', ['entity_id' => $validated['entity_id']])
            ->with('success', 'Movimento registado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $movement = ClientAccount::with('entity')->findOrFail($id);

        return Inertia::render('ClientAccounts/Show', [
            'movement' => $movement,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $movement = ClientAccount::with('entity')->findOrFail($id);

        $entities = Entity::whereIn('type', ['client', 'both'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('ClientAccounts/Edit', [
            'movement' => $movement,
            'entities' => $entities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $movement = ClientAccount::findOrFail($id);

        $validated = $request->validate([
            'entity_id' => 'required|exists:entities,id',
            'data_movimento' => 'required|date',
            'tipo' => 'required|in:debito,credito',
            'valor' => 'required|numeric|min:0.01',
            'descricao' => 'required|string|max:255',
            'categoria' => 'required|in:fatura,pagamento,nota_credito,nota_debito,juros,ajuste,outros',
            'referencia' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $movement->update($validated);

        return redirect()->route('client-accounts.index', ['entity_id' => $validated['entity_id']])
            ->with('success', 'Movimento atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $movement = ClientAccount::findOrFail($id);
        $entityId = $movement->entity_id;
        $movement->delete();

        return redirect()->route('client-accounts.index', ['entity_id' => $entityId])
            ->with('success', 'Movimento eliminado com sucesso.');
    }
}
