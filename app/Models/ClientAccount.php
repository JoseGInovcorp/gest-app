<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ClientAccount extends Model
{
    protected $fillable = [
        'entity_id',
        'data_movimento',
        'tipo',
        'valor',
        'saldo_apos',
        'descricao',
        'categoria',
        'referencia',
        'related_id',
        'related_type',
        'observacoes',
    ];

    protected $casts = [
        'data_movimento' => 'date',
        'valor' => 'decimal:2',
        'saldo_apos' => 'decimal:2',
    ];

    /**
     * Boot do model para calcular saldo após cada movimento
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movement) {
            $movement->calculateBalance();
        });

        static::created(function ($movement) {
            $movement->updateSubsequentBalances();
        });

        static::updated(function ($movement) {
            $movement->updateSubsequentBalances();
        });

        static::deleted(function ($movement) {
            static::recalculateBalancesForEntity($movement->entity_id);
        });
    }

    /**
     * Relacionamento com Entity (Cliente)
     */
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Relacionamento polimórfico com documentos relacionados (Faturas, Pagamentos, etc)
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Calcula o saldo após este movimento
     */
    public function calculateBalance(): void
    {
        // Buscar o último saldo antes desta data
        $previousMovement = static::where('entity_id', $this->entity_id)
            ->where('data_movimento', '<=', $this->data_movimento)
            ->where('id', '!=', $this->id)
            ->orderBy('data_movimento', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $previousBalance = $previousMovement ? $previousMovement->saldo_apos : 0;

        // Débito aumenta o saldo (cliente deve)
        // Crédito diminui o saldo (cliente pagou)
        if ($this->tipo === 'debito') {
            $this->saldo_apos = $previousBalance + $this->valor;
        } else {
            $this->saldo_apos = $previousBalance - $this->valor;
        }
    }

    /**
     * Atualiza os saldos de todos os movimentos subsequentes
     */
    public function updateSubsequentBalances(): void
    {
        $subsequentMovements = static::where('entity_id', $this->entity_id)
            ->where(function ($query) {
                $query->where('data_movimento', '>', $this->data_movimento)
                    ->orWhere(function ($q) {
                        $q->where('data_movimento', '=', $this->data_movimento)
                            ->where('id', '>', $this->id);
                    });
            })
            ->orderBy('data_movimento')
            ->orderBy('id')
            ->get();

        $currentBalance = $this->saldo_apos;

        foreach ($subsequentMovements as $movement) {
            if ($movement->tipo === 'debito') {
                $currentBalance = $currentBalance + $movement->valor;
            } else {
                $currentBalance = $currentBalance - $movement->valor;
            }

            $movement->saldo_apos = $currentBalance;
            $movement->saveQuietly(); // Save sem disparar eventos
        }
    }

    /**
     * Recalcula todos os saldos de uma entidade
     */
    public static function recalculateBalancesForEntity(int $entityId): void
    {
        $movements = static::where('entity_id', $entityId)
            ->orderBy('data_movimento')
            ->orderBy('id')
            ->get();

        $currentBalance = 0;

        foreach ($movements as $movement) {
            if ($movement->tipo === 'debito') {
                $currentBalance = $currentBalance + $movement->valor;
            } else {
                $currentBalance = $currentBalance - $movement->valor;
            }

            $movement->saldo_apos = $currentBalance;
            $movement->saveQuietly();
        }
    }

    /**
     * Scope para filtrar por entidade
     */
    public function scopeForEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    /**
     * Scope para filtrar por tipo (débito/crédito)
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('tipo', $type);
    }

    /**
     * Scope para filtrar por categoria
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('categoria', $category);
    }

    /**
     * Scope para filtrar por intervalo de datas
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('data_movimento', [$startDate, $endDate]);
    }

    /**
     * Obtém o saldo atual de uma entidade
     */
    public static function getCurrentBalance(int $entityId): float
    {
        $lastMovement = static::where('entity_id', $entityId)
            ->orderBy('data_movimento', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        return $lastMovement ? (float) $lastMovement->saldo_apos : 0;
    }

    /**
     * Obtém estatísticas de uma entidade
     */
    public static function getEntityStats(int $entityId): array
    {
        $debits = static::where('entity_id', $entityId)
            ->where('tipo', 'debito')
            ->sum('valor');

        $credits = static::where('entity_id', $entityId)
            ->where('tipo', 'credito')
            ->sum('valor');

        $currentBalance = static::getCurrentBalance($entityId);

        return [
            'total_debitos' => (float) $debits,
            'total_creditos' => (float) $credits,
            'saldo_atual' => $currentBalance,
        ];
    }
}
