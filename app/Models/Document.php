<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'original_filename',
        'file_path',
        'file_size',
        'mime_type',
        'category',
        'module',
        'documentable_type',
        'documentable_id',
        'description',
        'tags',
        'version',
        'parent_id',
        'uploaded_by',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'expires_at' => 'datetime',
        'file_size' => 'integer',
        'version' => 'integer',
    ];

    protected $appends = ['file_url', 'formatted_size', 'is_expired'];

    /**
     * Relação polimórfica - documento pode pertencer a qualquer entidade
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Utilizador que fez upload
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Documento pai (para versionamento)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    /**
     * Versões deste documento
     */
    public function versions()
    {
        return $this->hasMany(Document::class, 'parent_id');
    }

    /**
     * URL do ficheiro
     */
    public function getFileUrlAttribute(): string
    {
        return route('digital-archive.view', $this->id);
    }

    /**
     * Tamanho formatado
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Verificar se expirou
     */
    public function getIsExpiredAttribute(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return $this->expires_at->isPast();
    }

    /**
     * Scope - Documentos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope - Por categoria
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope - Por módulo
     */
    public function scopeModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope - Pesquisa
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('original_filename', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope - Documentos que expiram em breve
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays($days));
    }

    /**
     * Categorias disponíveis
     */
    public static function categories(): array
    {
        return [
            'contrato' => 'Contrato',
            'fatura' => 'Fatura',
            'proposta' => 'Proposta',
            'relatorio' => 'Relatório',
            'comprovativo' => 'Comprovativo',
            'encomenda_cliente' => 'Encomenda Cliente',
            'encomenda_fornecedor' => 'Encomenda Fornecedor',
            'extrato_bancario' => 'Extrato Bancário',
            'outros' => 'Outros',
        ];
    }

    /**
     * Módulos disponíveis
     */
    public static function modules(): array
    {
        return [
            'clients' => 'Clientes',
            'suppliers' => 'Fornecedores',
            'contacts' => 'Contactos',
            'proposals' => 'Propostas',
            'customer-orders' => 'Encomendas de Clientes',
            'supplier-orders' => 'Encomendas de Fornecedores',
            'client-accounts' => 'Conta Corrente Clientes',
            'supplier-invoices' => 'Faturas de Fornecedores',
            'bank-accounts' => 'Contas Bancárias',
            'calendar' => 'Calendário',
            'general' => 'Geral',
        ];
    }
}
