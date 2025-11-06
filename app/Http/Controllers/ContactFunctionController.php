<?php

namespace App\Http\Controllers;

use App\Models\ContactFunction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ContactFunctionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $functions = ContactFunction::query()
            ->orderBy('name')
            ->get();

        return Inertia::render('ContactFunctions/Index', [
            'functions' => $functions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('ContactFunctions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contact_functions,name',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        ContactFunction::create($validated);

        return redirect()
            ->route('contact-functions.index')
            ->with('success', 'Função criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactFunction $contactFunction): Response
    {
        return Inertia::render('ContactFunctions/Show', [
            'function' => $contactFunction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactFunction $contactFunction): Response
    {
        return Inertia::render('ContactFunctions/Edit', [
            'function' => $contactFunction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactFunction $contactFunction): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:contact_functions,name,' . $contactFunction->id,
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        $contactFunction->update($validated);

        return redirect()
            ->route('contact-functions.index')
            ->with('success', 'Função atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactFunction $contactFunction): RedirectResponse
    {
        // Verificar se a função está sendo usada em contactos
        if ($contactFunction->contacts()->exists()) {
            return redirect()
                ->route('contact-functions.index')
                ->with('error', 'Não é possível eliminar esta função porque está associada a contactos.');
        }

        $contactFunction->delete();

        return redirect()
            ->route('contact-functions.index')
            ->with('success', 'Função eliminada com sucesso!');
    }
}
