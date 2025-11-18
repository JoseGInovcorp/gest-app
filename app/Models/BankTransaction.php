<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $fillable = [
        'bank_account_id',
        'data_movimento',
        'descricao',
        'tipo',
        'valor',
        'saldo_apos',
        'referencia',
        'categoria',
        'observacoes',
    ];

    protected $casts = [
        'data_movimento' => 'date',
        'valor' => 'decimal:2',
        'saldo_apos' => 'decimal:2',
    ];

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        // Antes de criar, calcular saldo após movimento
        static::creating(function ($transaction) {
            $transaction->calculateBalance();
        });

        // Após criar movimento, atualizar saldo da conta
        static::created(function ($transaction) {
            $transaction->bankAccount->updateBalance();
        });

        // Após deletar movimento, atualizar saldo da conta
        static::deleted(function ($transaction) {
            $transaction->bankAccount->updateBalance();
        });

        // Após atualizar movimento, recalcular saldo
        static::updated(function ($transaction) {
            $transaction->bankAccount->updateBalance();
        });
    }

    /**
     * Relacionamento: Movimento pertence a uma conta
     */
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Calcula o saldo após este movimento
     */
    public function calculateBalance(): void
    {
        // Buscar o último saldo antes desta data
        $previousTransaction = static::where('bank_account_id', $this->bank_account_id)
            ->where('data_movimento', '<=', $this->data_movimento)
            ->where('id', '!=', $this->id)
            ->orderBy('data_movimento', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $previousBalance = $previousTransaction
            ? $previousTransaction->saldo_apos
            : $this->bankAccount->saldo_inicial;

        // Crédito aumenta o saldo, débito diminui
        if ($this->tipo === 'credito') {
            $this->saldo_apos = $previousBalance + $this->valor;
        } else {
            $this->saldo_apos = $previousBalance - $this->valor;
        }
    }
}
