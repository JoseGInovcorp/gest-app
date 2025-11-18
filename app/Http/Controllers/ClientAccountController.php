<?php

namespace App\Http\Controllers;

use App\Models\ClientAccount;
use App\Models\Entity;
use App\Models\Invoice;
use App\Models\BankTransaction;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $query = ClientAccount::with(['entity', 'invoice']);

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
            'can' => [
                'create' => $request->user()->can('client-accounts.create'),
                'update' => $request->user()->can('client-accounts.update'),
                'delete' => $request->user()->can('client-accounts.delete'),
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
            'categoria' => 'required|in:fatura,pagamento,outros',
            'referencia' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        // Se for um pagamento (crédito), gerar fatura automaticamente
        if ($validated['tipo'] === 'credito' && $validated['categoria'] === 'pagamento') {
            // Criar a fatura
            $invoice = Invoice::create([
                'entity_id' => $validated['entity_id'],
                'data_fatura' => $validated['data_movimento'],
                'data_vencimento' => null, // Pagamento imediato
                'valor_total' => $validated['valor'],
                'valor_pago' => $validated['valor'],
                'estado' => 'paga',
                'observacoes' => $validated['observacoes'] ?? null,
            ]);

            // Adicionar invoice_id ao movimento
            $validated['invoice_id'] = $invoice->id;
            $validated['referencia'] = $invoice->numero;
        }

        $clientAccount = ClientAccount::create($validated);

        // Se for um pagamento (débito), criar movimento bancário correspondente
        if ($validated['tipo'] === 'debito' && $validated['categoria'] === 'pagamento') {
            Log::info('Criando transação bancária para pagamento', [
                'tipo' => $validated['tipo'],
                'categoria' => $validated['categoria']
            ]);

            // Obter a conta bancária principal (ou a primeira disponível)
            $bankAccount = BankAccount::where('estado', 'ativa')
                ->orderBy('id')
                ->first();

            Log::info('Conta bancária encontrada', ['conta' => $bankAccount ? $bankAccount->id : 'nenhuma']);

            if ($bankAccount) {
                // Obter o nome do cliente
                $entity = Entity::find($validated['entity_id']);

                $bankTransactionData = [
                    'bank_account_id' => $bankAccount->id,
                    'data_movimento' => $validated['data_movimento'],
                    'descricao' => $validated['descricao'] . ($entity ? " - {$entity->name}" : ''),
                    'tipo' => 'credito', // Entrada de dinheiro na conta bancária
                    'valor' => $validated['valor'],
                    'referencia' => $validated['referencia'],
                    'categoria' => 'recebimento',
                    'observacoes' => $validated['observacoes'] ?? "Cliente: " . ($entity ? $entity->name : ''),
                ];

                Log::info('Dados da transação bancária', $bankTransactionData);

                $bankTransaction = BankTransaction::create($bankTransactionData);

                Log::info('Transação bancária criada', ['id' => $bankTransaction->id]);
            }
        }

        activity()
            ->performedOn($clientAccount)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('created');

        return redirect()->route('client-accounts.index', ['entity_id' => $validated['entity_id']])
            ->with('success', 'Movimento registado com sucesso.' . (isset($invoice) ? ' Fatura gerada: ' . $invoice->numero : ''));
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
            'categoria' => 'required|in:fatura,pagamento,outros',
            'referencia' => 'nullable|string|max:255',
            'observacoes' => 'nullable|string',
        ]);

        $movement->update($validated);

        activity()
            ->performedOn($movement)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('updated');

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

        activity()
            ->performedOn($movement)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'deleted_movement' => [
                    'entity' => $movement->entity->name,
                    'tipo' => $movement->tipo,
                    'valor' => $movement->valor,
                    'descricao' => $movement->descricao
                ]
            ])
            ->log('deleted');

        $movement->delete();

        return redirect()->route('client-accounts.index', ['entity_id' => $entityId])
            ->with('success', 'Movimento eliminado com sucesso.');
    }

    /**
     * Download PDF da fatura associada ao movimento
     */
    public function downloadPdf(ClientAccount $clientAccount)
    {
        $clientAccount->load(['entity', 'invoice']);

        // Verificar se existe fatura associada
        if (!$clientAccount->invoice) {
            return redirect()->back()->with('error', 'Este movimento não possui fatura associada.');
        }

        $invoice = $clientAccount->invoice;
        $company = \App\Models\Company::first();

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'company' => $company,
        ]);

        // Substituir / por - no número da fatura para nome de ficheiro válido
        $filename = str_replace('/', '-', $invoice->numero);

        return $pdf->download("fatura-{$filename}.pdf");
    }
}
