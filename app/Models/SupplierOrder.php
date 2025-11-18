<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'order_date',
        'delivery_date',
        'supplier_id',
        'customer_order_id',
        'status',
        'total_value',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'total_value' => 'decimal:2',
    ];

    protected static function booted()
    {
        // Ao criar, gerar número automaticamente se não fornecido
        static::creating(function ($order) {
            if (empty($order->number)) {
                $order->number = static::generateNumber();
            }
        });
    }

    /**
     * Gerar próximo número de encomenda (EF-YYYY-####)
     */
    public static function generateNumber(): string
    {
        $year = now()->year;
        $prefix = "EF-{$year}-";

        $lastOrder = static::withTrashed()
            ->where('number', 'like', $prefix . '%')
            ->orderBy('number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Calcular e atualizar total
     */
    public function calculateTotal(): void
    {
        $this->total_value = $this->items()->sum('total');
        $this->saveQuietly();
    }

    /**
     * Relação com fornecedor (Entity)
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'supplier_id');
    }

    /**
     * Relação com encomenda cliente origem
     */
    public function customerOrder(): BelongsTo
    {
        return $this->belongsTo(CustomerOrder::class, 'customer_order_id');
    }

    /**
     * Relação com itens da encomenda
     */
    public function items(): HasMany
    {
        return $this->hasMany(SupplierOrderItem::class);
    }

    /**
     * Registrar pagamento a fornecedor
     */
    public function registrarPagamento(float $valor, string $referencia = null, int $bankAccountId = null): void
    {
        // Criar movimento bancário (débito na conta bancária - saída de dinheiro)
        if ($bankAccountId) {
            BankTransaction::create([
                'bank_account_id' => $bankAccountId,
                'data_movimento' => now(),
                'descricao' => "Pagamento Encomenda {$this->number} - {$this->supplier->nome}",
                'tipo' => 'debito',
                'valor' => $valor,
                'referencia' => $referencia,
                'categoria' => 'pagamento',
                'observacoes' => "Fornecedor: {$this->supplier->nome}",
            ]);
        }
    }
}
