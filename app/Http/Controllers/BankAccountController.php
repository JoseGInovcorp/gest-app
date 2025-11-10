<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BankAccount::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                    ->orWhere('banco', 'LIKE', "%{$search}%")
                    ->orWhere('iban', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Ordenação
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $accounts = $query->withCount('transactions')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('BankAccounts/Index', [
            'accounts' => $accounts,
            'filters' => $request->only(['search', 'estado', 'tipo']),
            'sort' => $request->only(['sort', 'direction']),
            'can' => [
                'create' => $request->user()->can('bank-accounts.create'),
                'view' => $request->user()->can('bank-accounts.read'),
                'edit' => $request->user()->can('bank-accounts.update'),
                'delete' => $request->user()->can('bank-accounts.delete'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('BankAccounts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'banco' => 'required|string|max:255',
            'iban' => 'required|string|unique:bank_accounts,iban',
            'swift_bic' => 'nullable|string|max:11',
            'saldo_inicial' => 'required|numeric',
            'moeda' => 'required|string|size:3',
            'tipo' => 'required|in:corrente,poupanca,credito,investimento',
            'estado' => 'required|in:ativa,inativa,encerrada',
            'observacoes' => 'nullable|string',
        ]);

        BankAccount::create($validated);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Conta bancária criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load(['transactions' => function ($query) {
            $query->orderBy('data_movimento', 'desc');
        }]);

        return Inertia::render('BankAccounts/Show', [
            'account' => $bankAccount,
            'can' => [
                'edit' => request()->user()->can('bank-accounts.update'),
                'delete' => request()->user()->can('bank-accounts.delete'),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        return Inertia::render('BankAccounts/Edit', [
            'account' => $bankAccount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'banco' => 'required|string|max:255',
            'iban' => 'required|string|unique:bank_accounts,iban,' . $bankAccount->id,
            'swift_bic' => 'nullable|string|max:11',
            'saldo_inicial' => 'required|numeric',
            'moeda' => 'required|string|size:3',
            'tipo' => 'required|in:corrente,poupanca,credito,investimento',
            'estado' => 'required|in:ativa,inativa,encerrada',
            'observacoes' => 'nullable|string',
        ]);

        $bankAccount->update($validated);

        // Recalcular saldo se o saldo inicial foi alterado
        $bankAccount->updateBalance();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Conta bancária atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Conta bancária eliminada com sucesso!');
    }
}
