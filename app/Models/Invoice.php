<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'numero',
        'entity_id',
        'data_fatura',
        'data_vencimento',
        'valor_total',
        'valor_pago',
        'estado',
        'observacoes',
    ];

    protected $casts = [
        'data_fatura' => 'date',
        'data_vencimento' => 'date',
        'valor_total' => 'decimal:2',
        'valor_pago' => 'decimal:2',
    ];

    /**
     * Boot do model para gerar número automático
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->numero)) {
                $invoice->numero = static::generateNextNumber();
            }
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
     * Relacionamento com movimentos de conta corrente
     */
    public function clientAccountMovements(): HasMany
    {
        return $this->hasMany(ClientAccount::class);
    }

    /**
     * Gera o próximo número de fatura
     */
    public static function generateNextNumber(): string
    {
        $year = now()->year;
        $lastInvoice = static::where('numero', 'like', "FT{$year}/%")
            ->orderBy('numero', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->numero, strlen("FT{$year}/"));
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('FT%d/%05d', $year, $nextNumber);
    }

    /**
     * Obtém o próximo número sem criar a fatura
     */
    public static function getNextNumber(): string
    {
        return static::generateNextNumber();
    }

    /**
     * Calcula o valor em dívida
     */
    public function getValorDevidoAttribute(): float
    {
        return (float) ($this->valor_total - $this->valor_pago);
    }

    /**
     * Verifica se a fatura está vencida
     */
    public function getIsVencidaAttribute(): bool
    {
        return $this->data_vencimento && 
               $this->data_vencimento->isPast() && 
               $this->estado !== 'paga' &&
               $this->estado !== 'cancelada';
    }

    /**
     * Atualiza o estado da fatura baseado nos pagamentos
     */
    public function updateEstado(): void
    {
        if ($this->valor_pago >= $this->valor_total) {
            $this->estado = 'paga';
        } elseif ($this->valor_pago > 0) {
            $this->estado = 'parcialmente_paga';
        } elseif ($this->is_vencida) {
            $this->estado = 'vencida';
        } else {
            $this->estado = 'pendente';
        }
        
        $this->save();
    }

    /**
     * Registra um pagamento
     */
    public function registrarPagamento(float $valor, string $referencia = null): ClientAccount
    {
        // Atualizar valor pago
        $this->valor_pago += $valor;
        $this->updateEstado();

        // Criar movimento de crédito na conta corrente
        $movement = ClientAccount::create([
            'entity_id' => $this->entity_id,
            'invoice_id' => $this->id,
            'data_movimento' => now(),
            'tipo' => 'credito',
            'valor' => $valor,
            'descricao' => "Pagamento Fatura {$this->numero}",
            'categoria' => 'pagamento',
            'referencia' => $referencia,
        ]);

        return $movement;
    }
}
