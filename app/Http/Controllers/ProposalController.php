<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ProposalLine;
use App\Models\Entity;
use App\Models\Article;
use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ProposalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proposal::with('entity')
            ->orderBy('created_at', 'desc');

        // Filtro de pesquisa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                    ->orWhereHas('entity', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro de estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $proposals = $query->paginate(15);

        return Inertia::render('Proposals/Index', [
            'proposals' => $proposals,
            'filters' => $request->only(['search', 'estado']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Buscar clientes, artigos e fornecedores
        $clients = Entity::whereIn('type', ['client', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $articles = Article::ativos()
            ->orderBy('nome')
            ->get(['id', 'nome as name', 'preco_com_iva as price', 'referencia as reference']);

        $suppliers = Entity::whereIn('type', ['supplier', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Gerar próximo número
        $nextNumber = Proposal::generateNumber();

        return Inertia::render('Proposals/Create', [
            'clients' => $clients,
            'articles' => $articles,
            'suppliers' => $suppliers,
            'nextNumber' => $nextNumber,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_proposta' => $request->estado === 'fechado' ? 'required|date' : 'nullable|date',
            'entity_id' => 'required|exists:entities,id',
            'validade' => 'nullable|date',
            'estado' => 'required|in:rascunho,fechado',
            'observacoes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.article_id' => 'required|exists:articles,id',
            'lines.*.entity_id' => 'nullable|exists:entities,id',
            'lines.*.quantidade' => 'required|numeric|min:1',
            'lines.*.preco_custo' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Gerar número automaticamente
            $numero = Proposal::generateNumber();

            // Se estado for fechado e tiver data_proposta, calcular validade automaticamente (+30 dias)
            $dataProposta = $validated['data_proposta'] ?? null;
            $validade = null;
            if ($validated['estado'] === 'fechado' && $dataProposta) {
                $date = new \DateTime($dataProposta);
                $date->modify('+30 days');
                $validade = $date->format('Y-m-d');
            }

            $proposal = Proposal::create([
                'numero' => $numero,
                'data_proposta' => $dataProposta,
                'entity_id' => $validated['entity_id'],
                'validade' => $validade,
                'estado' => $validated['estado'],
                'observacoes' => $validated['observacoes'] ?? null,
            ]);

            foreach ($validated['lines'] as $line) {
                ProposalLine::create([
                    'proposal_id' => $proposal->id,
                    'article_id' => $line['article_id'],
                    'entity_id' => $line['entity_id'] ?? null,
                    'quantidade' => $line['quantidade'],
                    'preco_custo' => $line['preco_custo'] ?? null,
                ]);
            }

            DB::commit();

            activity()
                ->performedOn($proposal)
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'lines_count' => count($validated['lines'])
                ])
                ->log('created');

            return redirect()->route('proposals.index')
                ->with('success', 'Proposta criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao criar proposta: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Proposal $proposal)
    {
        $proposal->load(['entity', 'lines.article', 'lines.entity']);

        return Inertia::render('Proposals/Show', [
            'proposal' => $proposal,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proposal $proposal)
    {
        $proposal->load(['entity', 'lines.article', 'lines.entity']);

        $clients = Entity::whereIn('type', ['client', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $articles = Article::ativos()
            ->orderBy('nome')
            ->get(['id', 'nome as name', 'preco_com_iva as price', 'referencia as reference']);

        $suppliers = Entity::whereIn('type', ['supplier', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Proposals/Edit', [
            'proposal' => [
                'id' => $proposal->id,
                'numero' => $proposal->numero,
                'data_proposta' => $proposal->data_proposta?->format('Y-m-d'),
                'entity_id' => $proposal->entity_id,
                'validade' => $proposal->validade?->format('Y-m-d'),
                'estado' => $proposal->estado,
                'observacoes' => $proposal->observacoes,
                'valor_total' => $proposal->valor_total,
                'lines' => $proposal->lines->map(function ($line) {
                    return [
                        'id' => $line->id,
                        'article_id' => $line->article_id,
                        'article_name' => $line->article->nome,
                        'article_reference' => $line->article->referencia,
                        'entity_id' => $line->entity_id,
                        'quantidade' => $line->quantidade,
                        'preco_custo' => $line->preco_custo,
                        'total' => $line->total,
                    ];
                }),
            ],
            'clients' => $clients,
            'articles' => $articles,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proposal $proposal)
    {
        $validated = $request->validate([
            'numero' => 'required|string|unique:proposals,numero,' . $proposal->id,
            'data_proposta' => $request->estado === 'fechado' ? 'required|date' : 'nullable|date',
            'entity_id' => 'required|exists:entities,id',
            'validade' => 'nullable|date',
            'estado' => 'required|in:rascunho,fechado',
            'observacoes' => 'nullable|string',
            'lines' => 'required|array|min:1',
            'lines.*.article_id' => 'required|exists:articles,id',
            'lines.*.entity_id' => 'nullable|exists:entities,id',
            'lines.*.quantidade' => 'required|numeric|min:0.01',
            'lines.*.preco_custo' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Se estado for fechado e tiver data_proposta, calcular validade automaticamente (+30 dias)
            $dataProposta = $validated['data_proposta'] ?? null;
            $validade = null;
            if ($validated['estado'] === 'fechado' && $dataProposta) {
                $date = new \DateTime($dataProposta);
                $date->modify('+30 days');
                $validade = $date->format('Y-m-d');
            }

            $proposal->update([
                'numero' => $validated['numero'],
                'data_proposta' => $dataProposta,
                'entity_id' => $validated['entity_id'],
                'validade' => $validade,
                'estado' => $validated['estado'],
                'observacoes' => $validated['observacoes'] ?? null,
            ]);

            // Delete existing lines
            $proposal->lines()->delete();

            // Create new lines
            foreach ($validated['lines'] as $line) {
                ProposalLine::create([
                    'proposal_id' => $proposal->id,
                    'article_id' => $line['article_id'],
                    'entity_id' => $line['entity_id'] ?? null,
                    'quantidade' => $line['quantidade'],
                    'preco_custo' => $line['preco_custo'] ?? null,
                ]);
            }

            DB::commit();

            activity()
                ->performedOn($proposal)
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'lines_count' => count($validated['lines'])
                ])
                ->log('updated');

            return redirect()->route('proposals.index')
                ->with('success', 'Proposta atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao atualizar proposta: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proposal $proposal)
    {
        try {
            activity()
                ->performedOn($proposal)
                ->causedBy(Auth::user())
                ->withProperties([
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'deleted_proposal' => [
                        'numero' => $proposal->numero,
                        'entity' => $proposal->entity->name,
                        'estado' => $proposal->estado,
                        'valor_total' => $proposal->valor_total
                    ]
                ])
                ->log('deleted');

            $proposal->delete();
            return redirect()->route('proposals.index')
                ->with('success', 'Proposta eliminada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao eliminar proposta: ' . $e->getMessage()]);
        }
    }

    /**
     * Convert proposal to customer order
     */
    public function convertToOrder(Proposal $proposal)
    {
        Log::info('convertToOrder called for proposal: ' . $proposal->id);

        // Verificar se a proposta está fechada
        if ($proposal->estado !== 'fechado') {
            Log::warning('Proposal not closed: ' . $proposal->estado);
            return back()->withErrors(['error' => 'Apenas propostas fechadas podem ser convertidas.']);
        }

        // Carregar linhas
        $proposal->load(['lines.article']);

        // Verificar se há linhas
        if ($proposal->lines->isEmpty()) {
            Log::warning('Proposal has no lines');
            return back()->withErrors(['error' => 'A proposta não tem linhas de artigos.']);
        }

        Log::info('Starting conversion. Lines count: ' . $proposal->lines->count());

        DB::beginTransaction();
        try {
            // Criar encomenda de cliente
            $order = CustomerOrder::create([
                'number' => CustomerOrder::generateNumber(),
                'customer_id' => $proposal->entity_id,
                'proposal_date' => $proposal->data_proposta,
                'validity_date' => $proposal->validade,
                'status' => 'draft',
                'notes' => "Gerada a partir da proposta {$proposal->numero}\n\n" . ($proposal->observacoes ?? ''),
            ]);

            Log::info('Order created: ' . $order->id);

            // Adicionar itens
            foreach ($proposal->lines as $line) {
                CustomerOrderItem::create([
                    'customer_order_id' => $order->id,
                    'article_id' => $line->article_id,
                    'supplier_id' => $line->entity_id,
                    'quantity' => $line->quantidade,
                    'unit_price' => $line->preco_custo ?? $line->article->preco_com_iva ?? 0,
                ]);
            }

            Log::info('Items created successfully');

            DB::commit();

            Log::info('Transaction committed. Redirecting to order: ' . $order->id);

            return redirect()->route('customer-orders.edit', $order->id)
                ->with('success', "Encomenda {$order->number} criada com sucesso a partir da proposta!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error converting proposal: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Erro ao converter proposta: ' . $e->getMessage()]);
        }
    }
    /**
     * Download PDF of the proposal
     */
    public function downloadPdf(Proposal $proposal)
    {
        $proposal->load(['entity', 'lines.article', 'lines.entity']);

        // Obter dados da empresa
        $company = \App\Models\Company::first();

        $pdf = Pdf::loadView('proposals.pdf', [
            'proposal' => $proposal,
            'company' => $company,
        ]);

        return $pdf->download("proposta-{$proposal->numero}.pdf");
    }
}
