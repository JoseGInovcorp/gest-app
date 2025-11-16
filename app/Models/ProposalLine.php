<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProposalLine extends Model
{
    protected $fillable = [
        'proposal_id',
        'article_id',
        'entity_id',
        'quantidade',
        'preco_custo',
        'total',
    ];

    protected $casts = [
        'quantidade' => 'decimal:2',
        'preco_custo' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($line) {
            // Calcular total: quantidade * preco_custo
            $line->total = $line->quantidade * ($line->preco_custo ?? 0);
        });

        static::saved(function ($line) {
            $line->proposal->calculateTotal();
        });

        static::deleted(function ($line) {
            $line->proposal->calculateTotal();
        });
    }

    /**
     * Get the proposal that owns the line
     */
    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the article
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Get the supplier entity
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    /**
     * Alias for entity (for consistency with "supplier")
     */
    public function supplier(): BelongsTo
    {
        return $this->entity();
    }
}
