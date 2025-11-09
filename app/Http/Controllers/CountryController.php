<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $countries = Country::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('Countries/Index', [
            'countries' => $countries,
            'can' => [
                'create' => $request->user()->can('countries.create'),
                'view' => $request->user()->can('countries.read'),
                'edit' => $request->user()->can('countries.update'),
                'delete' => $request->user()->can('countries.delete'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Countries/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|unique:countries,code|uppercase',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'iso3' => 'nullable|string|size:3|uppercase',
            'numeric_code' => 'nullable|integer|min:1|max:999',
            'phone_prefix' => 'nullable|string|max:10',
            'vies_enabled' => 'boolean',
            'vat_formats' => 'nullable|array',
            'currency_code' => 'nullable|string|size:3|uppercase',
            'timezone' => 'nullable|string|max:255',
            'active' => 'boolean',
        ]);

        Country::create($validated);

        return redirect()
            ->route('countries.index')
            ->with('success', 'País criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Country $country): Response
    {
        return Inertia::render('Countries/Show', [
            'country' => $country,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country): Response
    {
        return Inertia::render('Countries/Edit', [
            'country' => $country,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|size:2|uppercase|unique:countries,code,' . $country->code . ',code',
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'iso3' => 'nullable|string|size:3|uppercase',
            'numeric_code' => 'nullable|integer|min:1|max:999',
            'phone_prefix' => 'nullable|string|max:10',
            'vies_enabled' => 'boolean',
            'vat_formats' => 'nullable|array',
            'currency_code' => 'nullable|string|size:3|uppercase',
            'timezone' => 'nullable|string|max:255',
            'active' => 'boolean',
        ]);

        $country->update($validated);

        return redirect()
            ->route('countries.index')
            ->with('success', 'País atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country): RedirectResponse
    {
        // Verificar se o país está sendo usado em entidades
        if ($country->entities()->exists()) {
            return redirect()
                ->route('countries.index')
                ->with('error', 'Não é possível eliminar este país porque está associado a entidades.');
        }

        $country->delete();

        return redirect()
            ->route('countries.index')
            ->with('success', 'País eliminado com sucesso!');
    }
}
