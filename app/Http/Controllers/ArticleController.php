<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\VatRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function __construct()
    {
        // TODO: Adicionar middleware de permissões quando configuradas
        // $this->middleware('permission:articles.view')->only(['index', 'show']);
        // $this->middleware('permission:articles.create')->only(['create', 'store']);
        // $this->middleware('permission:articles.edit')->only(['edit', 'update']);
        // $this->middleware('permission:articles.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Article::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('referencia', 'LIKE', "%{$search}%")
                    ->orWhere('nome', 'LIKE', "%{$search}%")
                    ->orWhere('descricao', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Ordenação
        $sortField = $request->get('sort', 'referencia');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $articles = $query->paginate(15)->withQueryString();

        return Inertia::render('Articles/Index', [
            'articles' => $articles,
            'filters' => $request->only(['search', 'estado']),
            'sort' => $request->only(['sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vatRates = VatRate::where('active', true)
            ->orderBy('rate', 'desc')
            ->get()
            ->map(function ($rate) {
                return [
                    'value' => $rate->rate,
                    'label' => $rate->name . ' (' . $rate->rate . '%)',
                    'is_default' => $rate->is_default,
                ];
            });

        return Inertia::render('Articles/Create', [
            'proximaReferencia' => Article::gerarProximaReferencia(),
            'opcoesIva' => $vatRates,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Obter taxas de IVA válidas
        $validVatRates = VatRate::where('active', true)->pluck('rate')->toArray();

        $validated = $request->validate([
            'referencia' => 'required|string|unique:articles,referencia',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'iva_percentagem' => ['required', 'numeric', Rule::in($validVatRates)],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observacoes' => 'nullable|string',
            'estado' => 'required|in:ativo,inativo',
        ]);

        // Upload da foto se fornecida
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('articles', 'public');
        }

        $article = Article::create($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Artigo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        return Inertia::render('Articles/Show', [
            'article' => $article,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        $vatRates = VatRate::where('active', true)
            ->orderBy('rate', 'desc')
            ->get()
            ->map(function ($rate) {
                return [
                    'value' => $rate->rate,
                    'label' => $rate->name . ' (' . $rate->rate . '%)',
                    'is_default' => $rate->is_default,
                ];
            });

        return Inertia::render('Articles/Edit', [
            'article' => $article,
            'opcoesIva' => $vatRates,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        // Obter taxas de IVA válidas
        $validVatRates = VatRate::where('active', true)->pluck('rate')->toArray();

        $validated = $request->validate([
            'referencia' => [
                'required',
                'string',
                Rule::unique('articles', 'referencia')->ignore($article->id)
            ],
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0',
            'iva_percentagem' => ['required', 'numeric', Rule::in($validVatRates)],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observacoes' => 'nullable|string',
            'estado' => 'required|in:ativo,inativo',
        ]);

        // Upload da nova foto se fornecida
        if ($request->hasFile('foto')) {
            // Deletar foto anterior se existir
            if ($article->foto) {
                Storage::disk('public')->delete($article->foto);
            }
            $validated['foto'] = $request->file('foto')->store('articles', 'public');
        }

        $article->update($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Artigo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        // Deletar foto se existir
        if ($article->foto) {
            Storage::disk('public')->delete($article->foto);
        }

        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Artigo eliminado com sucesso!');
    }
}
