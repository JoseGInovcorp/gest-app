<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'proposal_date',
        'validity_date',
        'customer_id',
        'status',
        'total_value',
        'notes',
    ];

    protected $casts = [
        'proposal_date' => 'date',
        'validity_date' => 'date',
        'total_value' => 'decimal:2',
    ];

    /**
     * Get the customer that owns the order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'customer_id');
    }

    /**
     * Get the items for the order
     */
    public function items(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    /**
     * Get the work order for this customer order
     */
    public function workOrder(): HasOne
    {
        return $this->hasOne(WorkOrder::class);
    }

    /**
     * Calculate and update the total value
     */
    public function calculateTotal(): void
    {
        $this->total_value = $this->items()->sum('total');
        $this->save();
    }

    /**
     * Generate the next order number
     */
    public static function generateNumber(): string
    {
        $year = date('Y');
        $prefix = "EC-$year-";

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
     * Registrar pagamento de cliente
     */
    public function registrarPagamento(float $valor, string $referencia = null, int $bankAccountId = null): void
    {
        // Criar movimento de crédito na conta corrente do cliente
        ClientAccount::create([
            'entity_id' => $this->customer_id,
            'data_movimento' => now(),
            'tipo' => 'credito',
            'valor' => $valor,
            'descricao' => "Pagamento Encomenda {$this->number}",
            'categoria' => 'pagamento',
            'referencia' => $referencia,
        ]);

        // Criar movimento bancário (crédito na conta bancária - entrada de dinheiro)
        if ($bankAccountId) {
            BankTransaction::create([
                'bank_account_id' => $bankAccountId,
                'data_movimento' => now(),
                'descricao' => "Recebimento Encomenda {$this->number} - {$this->customer->nome}",
                'tipo' => 'credito',
                'valor' => $valor,
                'referencia' => $referencia,
                'categoria' => 'recebimento',
                'observacoes' => "Cliente: {$this->customer->nome}",
            ]);
        }
    }
}
