<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Entity;
use App\Models\Proposal;
use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Document::with(['uploader', 'documentable'])
            ->active()
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        if ($request->filled('module')) {
            $query->module($request->module);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $documents = $query->paginate(15)->withQueryString();

        return Inertia::render('DigitalArchive/Index', [
            'documents' => $documents,
            'filters' => $request->only(['search', 'category', 'module', 'date_from', 'date_to', 'status']),
            'categories' => Document::categories(),
            'modules' => Document::modules(),
            'can' => [
                'create' => Auth::user()->can('digital-archive.create'),
                'view' => Auth::user()->can('digital-archive.view'),
                'edit' => Auth::user()->can('digital-archive.edit'),
                'delete' => Auth::user()->can('digital-archive.delete'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // Max 10MB
            'category' => 'required|string',
            'module' => 'nullable|string',
            'documentable_type' => 'nullable|string',
            'documentable_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'expires_at' => 'nullable|date',
        ]);

        // Upload do ficheiro
        $file = $request->file('file');
        $path = $file->store('documents', 'private');

        $document = Document::create([
            'name' => $request->name,
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'category' => $request->category,
            'module' => $request->module,
            'documentable_type' => $request->documentable_type,
            'documentable_id' => $request->documentable_id,
            'description' => $request->description,
            'tags' => $request->tags,
            'uploaded_by' => Auth::id(),
            'expires_at' => $request->expires_at,
            'status' => 'active',
            'version' => 1,
        ]);

        return redirect()->route('digital-archive.index')
            ->with('success', 'Documento carregado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Document $document)
    {
        $document->load(['uploader', 'documentable', 'versions.uploader']);

        return Inertia::render('DigitalArchive/Show', [
            'document' => $document,
            'can' => [
                'edit' => Auth::user()->can('digital-archive.edit'),
                'delete' => Auth::user()->can('digital-archive.delete'),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'module' => 'nullable|string',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'expires_at' => 'nullable|date',
            'status' => 'required|in:active,archived,deleted',
        ]);

        // Se houver novo ficheiro, criar nova versão
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents', 'private');

            // Criar nova versão
            $newVersion = Document::create([
                'name' => $request->name,
                'original_filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'category' => $request->category,
                'module' => $request->module,
                'documentable_type' => $document->documentable_type,
                'documentable_id' => $document->documentable_id,
                'description' => $request->description,
                'tags' => $request->tags,
                'uploaded_by' => Auth::id(),
                'expires_at' => $request->expires_at,
                'status' => 'active',
                'version' => $document->version + 1,
                'parent_id' => $document->parent_id ?? $document->id,
            ]);

            return redirect()->route('digital-archive.show', $newVersion)
                ->with('success', 'Nova versão do documento criada com sucesso!');
        }

        // Atualizar metadados
        $document->update($request->only([
            'name',
            'category',
            'module',
            'description',
            'tags',
            'expires_at',
            'status',
        ]));

        return redirect()->route('digital-archive.show', $document)
            ->with('success', 'Documento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document $document)
    {
        // Soft delete
        $document->delete();

        return redirect()->route('digital-archive.index')
            ->with('success', 'Documento eliminado com sucesso!');
    }

    /**
     * Download do documento
     */
    public function download(Document $document)
    {
        $path = Storage::disk('private')->path($document->file_path);

        if (!file_exists($path)) {
            abort(404, 'Ficheiro não encontrado');
        }

        return response()->download($path, $document->original_filename);
    }

    /**
     * Visualizar o documento no navegador
     */
    public function view(Document $document)
    {
        $path = Storage::disk('private')->path($document->file_path);

        if (!file_exists($path)) {
            abort(404, 'Ficheiro não encontrado');
        }

        return response()->file($path, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"'
        ]);
    }

    /**
     * Obter entidades para associação (AJAX)
     */
    public function getEntities(Request $request)
    {
        $module = $request->get('module');
        $entities = [];

        switch ($module) {
            case 'clients':
            case 'suppliers':
                $type = $module === 'clients' ? 'client' : 'supplier';
                $entities = Entity::where('type', $type)
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get();
                break;

            case 'proposals':
                $entities = Proposal::with('client:id,name')
                    ->select('id', 'numero', 'client_id')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(fn($p) => [
                        'id' => $p->id,
                        'name' => "Proposta {$p->numero} - {$p->client->name}"
                    ]);
                break;

            case 'customer-orders':
                $entities = CustomerOrder::with('client:id,name')
                    ->select('id', 'numero', 'client_id')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get()
                    ->map(fn($o) => [
                        'id' => $o->id,
                        'name' => "Encomenda {$o->numero} - {$o->client->name}"
                    ]);
                break;
        }

        return response()->json($entities);
    }

    /**
     * Estatísticas do arquivo
     */
    public function stats()
    {
        $totalDocuments = Document::active()->count();
        $totalSize = Document::active()->sum('file_size');
        $expiringDocuments = Document::active()->expiringSoon(30)->count();

        $byCategory = Document::active()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        $byModule = Document::active()
            ->selectRaw('module, COUNT(*) as count')
            ->whereNotNull('module')
            ->groupBy('module')
            ->get();

        return response()->json([
            'total_documents' => $totalDocuments,
            'total_size' => $totalSize,
            'expiring_soon' => $expiringDocuments,
            'by_category' => $byCategory,
            'by_module' => $byModule,
        ]);
    }
}
