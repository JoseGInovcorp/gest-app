<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'numero',
        'data_fatura',
        'data_vencimento',
        'supplier_id',
        'supplier_order_id',
        'valor_total',
        'documento',
        'comprovativo_pagamento',
        'estado',
    ];

    protected $casts = [
        'data_fatura' => 'date',
        'data_vencimento' => 'date',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Boot do model - gera número automático
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->numero)) {
                $invoice->numero = self::generateNumber();
            }
        });
    }

    /**
     * Gera número sequencial: FF-YYYY-####
     */
    public static function generateNumber(): string
    {
        $year = now()->year;
        $prefix = "FF-{$year}-";

        // Busca o último número do ano (incluindo soft deleted)
        $lastInvoice = self::withTrashed()
            ->where('numero', 'like', "{$prefix}%")
            ->orderBy('numero', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extrai o número sequencial
            $lastNumber = (int) substr($lastInvoice->numero, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relação com Fornecedor (Entity)
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    /**
     * Relação com Encomenda Fornecedor
     */
    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class, 'supplier_order_id');
    }

    /**
     * Scopes
     */
    public function scopePendente($query)
    {
        return $query->where('estado', 'pendente');
    }

    public function scopePaga($query)
    {
        return $query->where('estado', 'paga');
    }

    public function scopeVencidas($query)
    {
        return $query->where('estado', 'pendente')
            ->where('data_vencimento', '<', now());
    }

    public function scopeSupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    /**
     * Acessors
     */
    public function getValorTotalFormatadoAttribute(): string
    {
        return number_format($this->valor_total, 2, ',', '.') . '€';
    }

    public function getEstadoBadgeClassAttribute(): string
    {
        return match ($this->estado) {
            'paga' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'pendente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'paga' => 'Paga',
            'pendente' => 'Pendente de Pagamento',
            default => $this->estado,
        };
    }

    public function getIsVencidaAttribute(): bool
    {
        return $this->estado === 'pendente' && $this->data_vencimento < now();
    }
}
