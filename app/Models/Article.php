<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'referencia',
        'nome',
        'descricao',
        'preco',
        'iva_percentagem',
        'preco_com_iva',
        'foto',
        'observacoes',
        'estado',
        'tipo',
        'gama',
        'stock_quantidade',
        'data_criacao'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'iva_percentagem' => 'decimal:2',
        'preco_com_iva' => 'decimal:2',
        'stock_quantidade' => 'decimal:2',
        'data_criacao' => 'date',
    ];

    protected $appends = [
        'foto_url',
    ];

    /**
     * Boot do modelo - eventos
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            // Definir data_criacao automaticamente
            if (!$article->data_criacao) {
                $article->data_criacao = now()->toDateString();
            }
        });

        static::saving(function ($article) {
            // Calcular preço com IVA automaticamente
            if ($article->preco && $article->iva_percentagem !== null) {
                $article->preco_com_iva = $article->preco * (1 + ($article->iva_percentagem / 100));
            }
        });
    }

    /**
     * Gerar próxima referência automática (ART001, ART002...)
     */
    public static function gerarProximaReferencia()
    {
        $ultimoArticle = static::orderBy('id', 'desc')->first();

        if (!$ultimoArticle) {
            return 'ART001';
        }

        // Extrair número da referência (ex: ART003 → 3)
        $ultimoNumero = (int) substr($ultimoArticle->referencia, 3);
        $proximoNumero = $ultimoNumero + 1;

        return 'ART' . str_pad($proximoNumero, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Scope para artigos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('estado', 'ativo');
    }

    /**
     * Accessor para preço formatado
     */
    public function getPrecoFormatadoAttribute()
    {
        return number_format($this->preco, 2, ',', '.') . '€';
    }

    /**
     * Accessor para preço com IVA formatado
     */
    public function getPrecoComIvaFormatadoAttribute()
    {
        return number_format($this->preco_com_iva, 2, ',', '.') . '€';
    }

    /**
     * Accessor para IVA formatado
     */
    public function getIvaFormatadoAttribute()
    {
        return $this->iva_percentagem . '%';
    }

    /**
     * Get foto URL
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return null;
    }

    /**
     * Check if article has sufficient stock
     */
    public function hasStock(float $quantity): bool
    {
        // Serviços não têm stock
        if ($this->tipo === 'servico') {
            return true;
        }

        return $this->stock_quantidade >= $quantity;
    }

    /**
     * Decrease stock quantity
     */
    public function decreaseStock(float $quantity): void
    {
        if ($this->tipo === 'produto') {
            $this->stock_quantidade = max(0, $this->stock_quantidade - $quantity);
            $this->save();
        }
    }

    /**
     * Increase stock quantity
     */
    public function increaseStock(float $quantity): void
    {
        if ($this->tipo === 'produto') {
            $this->stock_quantidade += $quantity;
            $this->save();
        }
    }
}
