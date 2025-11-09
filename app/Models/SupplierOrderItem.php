<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierOrderItem extends Model
{
    protected $fillable = [
        'supplier_order_id',
        'article_id',
        'quantity',
        'unit_price',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted()
    {
        // Calcular total ao guardar
        static::saving(function ($item) {
            $item->total = $item->quantity * $item->unit_price;
        });

        // Recalcular total da encomenda após guardar/eliminar item
        static::saved(function ($item) {
            $item->supplierOrder->calculateTotal();
        });

        static::deleted(function ($item) {
            $item->supplierOrder->calculateTotal();
        });
    }

    /**
     * Relação com encomenda
     */
    public function supplierOrder(): BelongsTo
    {
        return $this->belongsTo(SupplierOrder::class);
    }

    /**
     * Relação com artigo
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
