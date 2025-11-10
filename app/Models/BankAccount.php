<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'banco',
        'iban',
        'swift_bic',
        'saldo_inicial',
        'saldo_atual',
        'moeda',
        'tipo',
        'estado',
        'observacoes',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'saldo_atual' => 'decimal:2',
    ];

    /**
     * Boot do modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            // Definir saldo atual igual ao saldo inicial na criação
            if ($account->saldo_atual === null) {
                $account->saldo_atual = $account->saldo_inicial;
            }
        });
    }

    /**
     * Relacionamento: Conta tem muitos movimentos
     */
    public function transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }

    /**
     * Atualizar saldo atual com base nos movimentos
     */
    public function updateBalance()
    {
        $creditos = $this->transactions()->where('tipo', 'credito')->sum('valor');
        $debitos = $this->transactions()->where('tipo', 'debito')->sum('valor');

        $this->saldo_atual = $this->saldo_inicial + $creditos - $debitos;
        $this->save();
    }

    /**
     * Accessor para IBAN formatado
     */
    public function getIbanFormatadoAttribute()
    {
        return chunk_split($this->iban, 4, ' ');
    }
}
