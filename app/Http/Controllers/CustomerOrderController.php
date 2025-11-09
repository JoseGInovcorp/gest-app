<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\CustomerOrderItem;
use App\Models\Entity;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class CustomerOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = CustomerOrder::with('customer')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'number' => $order->number,
                    'proposal_date' => $order->proposal_date?->format('Y-m-d'),
                    'validity_date' => $order->validity_date?->format('Y-m-d'),
                    'customer' => $order->customer->name,
                    'total_value' => $order->total_value,
                    'status' => $order->status,
                ];
            });

        return Inertia::render('CustomerOrders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Buscar artigos, clientes e fornecedores
        $customers = Entity::whereIn('type', ['client', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $articles = Article::ativos()
            ->orderBy('nome')
            ->get(['id', 'nome as name', 'preco_com_iva as unit_price', 'referencia as reference']);

        $suppliers = Entity::whereIn('type', ['supplier', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Gerar próximo número
        $nextNumber = CustomerOrder::generateNumber();

        return Inertia::render('CustomerOrders/Create', [
            'customers' => $customers,
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
            'proposal_date' => 'nullable|date',
            'validity_date' => 'nullable|date',
            'customer_id' => 'required|exists:entities,id',
            'status' => 'required|in:draft,closed',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.article_id' => 'required|exists:articles,id',
            'items.*.supplier_id' => 'nullable|exists:entities,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Gerar número automaticamente
            $number = CustomerOrder::generateNumber();

            $order = CustomerOrder::create([
                'number' => $number,
                'proposal_date' => $validated['proposal_date'],
                'validity_date' => $validated['validity_date'],
                'customer_id' => $validated['customer_id'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                CustomerOrderItem::create([
                    'customer_order_id' => $order->id,
                    'article_id' => $item['article_id'],
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            DB::commit();

            return redirect()->route('customer-orders.index')
                ->with('success', 'Encomenda criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao criar encomenda: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerOrder $customerOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerOrder $customerOrder)
    {
        $customerOrder->load(['customer', 'items.article', 'items.supplier']);

        $customers = Entity::whereIn('type', ['client', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $articles = Article::ativos()
            ->orderBy('nome')
            ->get(['id', 'nome as name', 'preco_com_iva as unit_price', 'referencia as reference']);

        $suppliers = Entity::whereIn('type', ['supplier', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('CustomerOrders/Edit', [
            'order' => [
                'id' => $customerOrder->id,
                'number' => $customerOrder->number,
                'proposal_date' => $customerOrder->proposal_date?->format('Y-m-d'),
                'validity_date' => $customerOrder->validity_date?->format('Y-m-d'),
                'customer_id' => $customerOrder->customer_id,
                'status' => $customerOrder->status,
                'notes' => $customerOrder->notes,
                'total_value' => $customerOrder->total_value,
                'items' => $customerOrder->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'article_id' => $item->article_id,
                        'article_name' => $item->article->name,
                        'article_reference' => $item->article->reference,
                        'supplier_id' => $item->supplier_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total' => $item->total,
                    ];
                }),
            ],
            'customers' => $customers,
            'articles' => $articles,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerOrder $customerOrder)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:customer_orders,number,' . $customerOrder->id,
            'proposal_date' => 'nullable|date',
            'validity_date' => 'nullable|date',
            'customer_id' => 'required|exists:entities,id',
            'status' => 'required|in:draft,closed',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.article_id' => 'required|exists:articles,id',
            'items.*.supplier_id' => 'nullable|exists:entities,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $customerOrder->update([
                'number' => $validated['number'],
                'proposal_date' => $validated['proposal_date'],
                'validity_date' => $validated['validity_date'],
                'customer_id' => $validated['customer_id'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Delete existing items
            $customerOrder->items()->delete();

            // Create new items
            foreach ($validated['items'] as $item) {
                CustomerOrderItem::create([
                    'customer_order_id' => $customerOrder->id,
                    'article_id' => $item['article_id'],
                    'supplier_id' => $item['supplier_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            DB::commit();

            return redirect()->route('customer-orders.index')
                ->with('success', 'Encomenda atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao atualizar encomenda: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerOrder $customerOrder)
    {
        try {
            $customerOrder->delete();
            return redirect()->route('customer-orders.index')
                ->with('success', 'Encomenda eliminada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao eliminar encomenda: ' . $e->getMessage()]);
        }
    }

    /**
     * Convert customer order to supplier orders
     */
    public function convertToSupplierOrders(CustomerOrder $customerOrder)
    {
        // Verificar se a encomenda está fechada
        if ($customerOrder->status !== 'closed') {
            return back()->withErrors(['error' => 'Apenas encomendas fechadas podem ser convertidas.']);
        }

        // Carregar itens com fornecedores
        $customerOrder->load(['items' => function ($query) {
            $query->whereNotNull('supplier_id');
        }, 'items.supplier']);

        // Verificar se há itens com fornecedor
        if ($customerOrder->items->isEmpty()) {
            return back()->withErrors(['error' => 'A encomenda não tem itens com fornecedor associado.']);
        }

        DB::beginTransaction();
        try {
            // Agrupar itens por fornecedor
            $itemsBySupplier = $customerOrder->items->groupBy('supplier_id');

            $createdOrders = [];
            foreach ($itemsBySupplier as $supplierId => $items) {
                // Criar encomenda de fornecedor
                $supplierOrder = \App\Models\SupplierOrder::create([
                    'supplier_id' => $supplierId,
                    'customer_order_id' => $customerOrder->id,
                    'order_date' => now(),
                    'status' => 'draft',
                    'notes' => "Gerada a partir da encomenda de cliente {$customerOrder->number}",
                ]);

                // Adicionar itens
                foreach ($items as $item) {
                    \App\Models\SupplierOrderItem::create([
                        'supplier_order_id' => $supplierOrder->id,
                        'article_id' => $item->article_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                    ]);
                }

                $createdOrders[] = $supplierOrder->number;
            }

            DB::commit();

            $message = count($createdOrders) === 1
                ? "Encomenda de fornecedor criada: {$createdOrders[0]}"
                : "Encomendas de fornecedores criadas: " . implode(', ', $createdOrders);

            return redirect()->route('customer-orders.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao converter encomenda: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate PDF for the order
     */
    public function generatePDF(CustomerOrder $customerOrder)
    {
        // TODO: Implementar geração de PDF

        return back()->with('info', 'Funcionalidade em desenvolvimento');
    }
}
