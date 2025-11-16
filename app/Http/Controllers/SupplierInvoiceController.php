<?php

namespace App\Http\Controllers;

use App\Models\SupplierInvoice;
use App\Models\Entity;
use App\Models\SupplierOrder;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentProofMail;

class SupplierInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SupplierInvoice::with(['supplier', 'supplierOrder']);

        // Filtro por fornecedor
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por datas
        if ($request->filled('data_inicio')) {
            $query->where('data_fatura', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->where('data_fatura', '<=', $request->data_fim);
        }

        // Pesquisa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->orderBy('data_fatura', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Lista de fornecedores para filtro
        $suppliers = Entity::whereIn('type', ['supplier', 'both'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('SupplierInvoices/Index', [
            'invoices' => $invoices,
            'suppliers' => $suppliers,
            'filters' => $request->only(['supplier_id', 'estado', 'data_inicio', 'data_fim', 'search']),
            'can' => [
                'create' => $request->user()->can('supplier-invoices.create'),
                'view' => $request->user()->can('supplier-invoices.read'),
                'edit' => $request->user()->can('supplier-invoices.update'),
                'delete' => $request->user()->can('supplier-invoices.delete'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Entity::whereIn('type', ['supplier', 'both'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $supplierOrders = SupplierOrder::with('supplier')
            ->orderBy('number', 'desc')
            ->get(['id', 'number', 'supplier_id']);

        return Inertia::render('SupplierInvoices/Create', [
            'suppliers' => $suppliers,
            'supplierOrders' => $supplierOrders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data_fatura' => 'required|date',
            'data_vencimento' => 'required|date|after_or_equal:data_fatura',
            'supplier_id' => 'required|exists:entities,id',
            'supplier_order_id' => 'nullable|exists:supplier_orders,id',
            'valor_total' => 'required|numeric|min:0.01',
            'documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
            'estado' => 'required|in:pendente,paga',
        ]);

        // Upload do documento
        if ($request->hasFile('documento')) {
            $path = $request->file('documento')->store('supplier_invoices/documents', 'public');
            $validated['documento'] = $path;
        }

        $invoice = SupplierInvoice::create($validated);

        activity()
            ->performedOn($invoice)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('created');

        return redirect()->route('supplier-invoices.index')
            ->with('success', "Fatura {$invoice->numero} criada com sucesso!");
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load(['supplier', 'supplierOrder']);

        return Inertia::render('SupplierInvoices/Show', [
            'invoice' => $supplierInvoice,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierInvoice $supplierInvoice)
    {
        $supplierInvoice->load(['supplier', 'supplierOrder']);

        $suppliers = Entity::whereIn('type', ['supplier', 'both'])
            ->orderBy('name')
            ->get(['id', 'name']);

        $supplierOrders = SupplierOrder::with('supplier')
            ->orderBy('number', 'desc')
            ->get(['id', 'number', 'supplier_id']);

        return Inertia::render('SupplierInvoices/Edit', [
            'invoice' => $supplierInvoice,
            'suppliers' => $suppliers,
            'supplierOrders' => $supplierOrders,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierInvoice $supplierInvoice)
    {
        $validated = $request->validate([
            'data_fatura' => 'required|date',
            'data_vencimento' => 'required|date|after_or_equal:data_fatura',
            'supplier_id' => 'required|exists:entities,id',
            'supplier_order_id' => 'nullable|exists:supplier_orders,id',
            'valor_total' => 'required|numeric|min:0.01',
            'documento' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'estado' => 'required|in:pendente,paga',
        ]);

        // Upload novo documento (se fornecido)
        if ($request->hasFile('documento')) {
            // Apagar documento antigo
            if ($supplierInvoice->documento) {
                Storage::disk('public')->delete($supplierInvoice->documento);
            }
            $path = $request->file('documento')->store('supplier_invoices/documents', 'public');
            $validated['documento'] = $path;
        }

        $supplierInvoice->update($validated);

        activity()
            ->performedOn($supplierInvoice)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('updated');

        return redirect()->route('supplier-invoices.index')
            ->with('success', "Fatura {$supplierInvoice->numero} atualizada com sucesso!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierInvoice $supplierInvoice)
    {
        activity()
            ->performedOn($supplierInvoice)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'deleted_invoice' => [
                    'numero' => $supplierInvoice->numero,
                    'supplier' => $supplierInvoice->supplier->name,
                    'valor_total' => $supplierInvoice->valor_total,
                    'estado' => $supplierInvoice->estado
                ]
            ])
            ->log('deleted');

        // Apagar ficheiros
        if ($supplierInvoice->documento) {
            Storage::disk('public')->delete($supplierInvoice->documento);
        }
        if ($supplierInvoice->comprovativo_pagamento) {
            Storage::disk('public')->delete($supplierInvoice->comprovativo_pagamento);
        }

        $numero = $supplierInvoice->numero;
        $supplierInvoice->delete();

        return redirect()->route('supplier-invoices.index')
            ->with('success', "Fatura {$numero} eliminada com sucesso!");
    }

    /**
     * Enviar comprovativo de pagamento ao fornecedor
     */
    public function sendPaymentProof(Request $request, SupplierInvoice $supplierInvoice)
    {
        $validated = $request->validate([
            'comprovativo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Upload do comprovativo
        $path = $request->file('comprovativo')->store('supplier_invoices/proofs', 'public');
        $supplierInvoice->update(['comprovativo_pagamento' => $path]);

        // Obter dados da empresa
        $company = Company::getInstance();

        // Enviar email ao fornecedor
        $supplier = $supplierInvoice->supplier;

        if ($supplier->email) {
            try {
                Mail::to($supplier->email)->send(
                    new PaymentProofMail($supplierInvoice, $company, storage_path('app/public/' . $path))
                );

                return back()->with('success', 'Comprovativo enviado com sucesso para ' . $supplier->email);
            } catch (\Exception $e) {
                return back()->with('error', 'Erro ao enviar email: ' . $e->getMessage());
            }
        }

        return back()->with('warning', 'Fornecedor não tem email cadastrado.');
    }
}
