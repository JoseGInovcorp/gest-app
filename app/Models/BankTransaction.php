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
}
