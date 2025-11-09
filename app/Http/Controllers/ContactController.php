<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Contact::with(['entity']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('function', 'like', "%{$search}%")
                    ->orWhereHas('entity', function ($eq) use ($search) {
                        $eq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('entity_id')) {
            $query->where('entity_id', $request->entity_id);
        }

        // Ordenação
        $sortBy = $request->get('sort', 'first_name');
        $sortDirection = $request->get('direction', 'asc');

        $allowedSorts = ['first_name', 'last_name', 'email', 'function', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        $contacts = $query->paginate(15)->withQueryString();

        // Entidades para filtro
        $entities = Entity::active()
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'entities' => $entities,
            'filters' => $request->only(['search', 'status', 'entity_id']),
            'can' => [
                'create' => $request->user()->can('contacts.create'),
                'view' => $request->user()->can('contacts.read'),
                'edit' => $request->user()->can('contacts.update'),
                'delete' => $request->user()->can('contacts.delete'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $entities = Entity::active()
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        return Inertia::render('Contacts/Create', [
            'entities' => $entities,
            'nextNumber' => Contact::getNextNumber()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'entity_id' => ['required', 'exists:entities,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'function' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'rgpd_consent' => ['boolean'],
            'observations' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])]
        ]);

        $validated['number'] = Contact::getNextNumber();
        $validated['created_by'] = request()->user()?->id;

        Contact::create($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Contacto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact): Response
    {
        $contact->load(['entity:id,name,type', 'createdBy:id,name', 'updatedBy:id,name']);

        return Inertia::render('Contacts/Show', [
            'contact' => $contact
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact): Response
    {
        $entities = Entity::active()
            ->select('id', 'name', 'type')
            ->orderBy('name')
            ->get();

        return Inertia::render('Contacts/Edit', [
            'contact' => $contact,
            'entities' => $entities
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validate([
            'entity_id' => ['required', 'exists:entities,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'function' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'rgpd_consent' => ['boolean'],
            'observations' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])]
        ]);

        $validated['updated_by'] = request()->user()?->id;

        $contact->update($validated);

        return redirect()->route('contacts.index')
            ->with('success', 'Contacto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Contacto eliminado com sucesso!');
    }
}
