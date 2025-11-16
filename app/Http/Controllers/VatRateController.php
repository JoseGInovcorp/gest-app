<?php

namespace App\Http\Controllers;

use App\Models\VatRate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class VatRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vatRates = VatRate::orderBy('rate', 'desc')->get();

        return Inertia::render('VatRates/Index', [
            'vatRates' => $vatRates,
            'can' => [
                'create' => $request->user()->can('vat-rates.create'),
                'view' => $request->user()->can('vat-rates.read'),
                'edit' => $request->user()->can('vat-rates.update'),
                'delete' => $request->user()->can('vat-rates.delete'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('VatRates/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'rate' => 'required|numeric|min:0|max:100',
            'is_default' => 'boolean',
            'active' => 'boolean',
        ]);

        // Se esta taxa for definida como padrão, remover padrão das outras
        if ($validated['is_default'] ?? false) {
            VatRate::where('is_default', true)->update(['is_default' => false]);
        }

        $vatRate = VatRate::create($validated);

        activity()
            ->performedOn($vatRate)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('created');

        return redirect()->route('vat-rates.index')
            ->with('success', 'Taxa de IVA criada com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VatRate $vatRate)
    {
        return Inertia::render('VatRates/Edit', [
            'vatRate' => $vatRate
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VatRate $vatRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'rate' => 'required|numeric|min:0|max:100',
            'is_default' => 'boolean',
            'active' => 'boolean',
        ]);

        // Se esta taxa for definida como padrão, remover padrão das outras
        if ($validated['is_default'] ?? false) {
            VatRate::where('id', '!=', $vatRate->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $vatRate->update($validated);

        activity()
            ->performedOn($vatRate)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('updated');

        return redirect()->route('vat-rates.index')
            ->with('success', 'Taxa de IVA atualizada com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VatRate $vatRate)
    {
        activity()
            ->performedOn($vatRate)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'deleted_vat_rate' => [
                    'name' => $vatRate->name,
                    'rate' => $vatRate->rate,
                    'is_default' => $vatRate->is_default
                ]
            ])
            ->log('deleted');

        // Verificar se a taxa está em uso (opcional - adicionar verificação com Article se necessário)
        $vatRate->delete();

        return redirect()->route('vat-rates.index')
            ->with('success', 'Taxa de IVA eliminada com sucesso.');
    }
}
