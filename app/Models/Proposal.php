<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero',
        'data_proposta',
        'entity_id',
        'validade',
        'estado',
        'valor_total',
        'observacoes',
    ];

    protected $casts = [
        'data_proposta' => 'date',
        'validade' => 'date',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Get the client entity that owns the proposal
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    /**
     * Alias for entity (for consistency with "client")
     */
    public function client(): BelongsTo
    {
        return $this->entity();
    }

    /**
     * Get the lines for the proposal
     */
    public function lines(): HasMany
    {
        return $this->hasMany(ProposalLine::class);
    }

    /**
     * Calculate and update the total value
     */
    public function calculateTotal(): void
    {
        $this->valor_total = $this->lines()->sum('total');
        $this->save();
    }

    /**
     * Generate the next proposal number
     */
    public static function generateNumber(): string
    {
        $year = date('Y');
        $prefix = "PROP-$year-";

        $lastProposal = static::withTrashed()
            ->where('numero', 'like', $prefix . '%')
            ->orderBy('numero', 'desc')
            ->first();

        if ($lastProposal) {
            $lastNumber = (int) substr($lastProposal->numero, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get status label
     */
    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'rascunho' => 'Rascunho',
            'fechado' => 'Fechado',
            default => $this->estado,
        };
    }
}
