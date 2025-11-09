<?php

namespace App\Http\Controllers;

use App\Models\SupplierOrder;
use App\Models\SupplierOrderItem;
use App\Models\Entity;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SupplierOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupplierOrder::with('supplier')
            ->orderBy('created_at', 'desc');

        // Filtro de pesquisa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro de estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        return Inertia::render('SupplierOrders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Buscar artigos e fornecedores
        $suppliers = Entity::whereIn('type', ['supplier', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $articles = Article::ativos()
            ->orderBy('nome')
            ->get(['id', 'nome as name', 'preco as unit_price', 'referencia as reference']);

        // Gerar próximo número
        $nextNumber = SupplierOrder::generateNumber();

        return Inertia::render('SupplierOrders/Create', [
            'suppliers' => $suppliers,
            'articles' => $articles,
            'nextNumber' => $nextNumber,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'supplier_id' => 'required|exists:entities,id',
            'status' => 'required|in:draft,sent,confirmed,received,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.article_id' => 'required|exists:articles,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Gerar número automaticamente
            $number = SupplierOrder::generateNumber();

            $order = SupplierOrder::create([
                'number' => $number,
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'],
                'supplier_id' => $validated['supplier_id'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                SupplierOrderItem::create([
                    'supplier_order_id' => $order->id,
                    'article_id' => $item['article_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            DB::commit();

            return redirect()->route('supplier-orders.index')
                ->with('success', 'Encomenda de fornecedor criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao criar encomenda: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierOrder $supplierOrder)
    {
        $supplierOrder->load(['supplier', 'items.article', 'customerOrder']);

        return Inertia::render('SupplierOrders/Show', [
            'order' => $supplierOrder,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierOrder $supplierOrder)
    {
        $supplierOrder->load(['items']);

        $suppliers = Entity::whereIn('type', ['supplier', 'both'])
            ->where('active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $articles = Article::ativos()
            ->orderBy('nome')
            ->get(['id', 'nome as name', 'preco as unit_price', 'referencia as reference']);

        return Inertia::render('SupplierOrders/Edit', [
            'order' => $supplierOrder,
            'suppliers' => $suppliers,
            'articles' => $articles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierOrder $supplierOrder)
    {
        $validated = $request->validate([
            'order_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'supplier_id' => 'required|exists:entities,id',
            'status' => 'required|in:draft,sent,confirmed,received,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.article_id' => 'required|exists:articles,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $supplierOrder->update([
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'],
                'supplier_id' => $validated['supplier_id'],
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Remover itens antigos
            $supplierOrder->items()->delete();

            // Adicionar novos itens
            foreach ($validated['items'] as $item) {
                SupplierOrderItem::create([
                    'supplier_order_id' => $supplierOrder->id,
                    'article_id' => $item['article_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
            }

            DB::commit();

            return redirect()->route('supplier-orders.index')
                ->with('success', 'Encomenda de fornecedor atualizada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao atualizar encomenda: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierOrder $supplierOrder)
    {
        try {
            $supplierOrder->delete();

            return redirect()->route('supplier-orders.index')
                ->with('success', 'Encomenda de fornecedor eliminada com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao eliminar encomenda: ' . $e->getMessage()]);
        }
    }

    /**
     * Gerar PDF da encomenda
     */
    public function generatePDF(SupplierOrder $supplierOrder)
    {
        // TODO: Implementar geração de PDF
        return response()->json(['message' => 'Funcionalidade em desenvolvimento']);
    }
}
