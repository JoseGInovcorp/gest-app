<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * A chave primária da tabela
     */
    protected $primaryKey = 'code';

    /**
     * O tipo da chave primária
     */
    protected $keyType = 'string';

    /**
     * Indica se a chave primária é auto-incrementável
     */
    public $incrementing = false;

    /**
     * Os atributos que são mass assignable
     */
    protected $fillable = [
        'code',
        'name',
        'name_en',
        'iso3',
        'numeric_code',
        'phone_prefix',
        'vies_enabled',
        'vat_formats',
        'currency_code',
        'timezone',
        'active'
    ];

    /**
     * Os atributos que devem ser cast para tipos nativos
     */
    protected $casts = [
        'vies_enabled' => 'boolean',
        'active' => 'boolean',
        'vat_formats' => 'array',
        'numeric_code' => 'integer'
    ];

    /**
     * Scope para países ativos
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope para países com VIES
     */
    public function scopeViesEnabled($query)
    {
        return $query->where('vies_enabled', true);
    }

    /**
     * Scope para países da UE
     */
    public function scopeEuropeanUnion($query)
    {
        return $query->whereIn('code', [
            'AT',
            'BE',
            'BG',
            'CY',
            'CZ',
            'DE',
            'DK',
            'EE',
            'ES',
            'FI',
            'FR',
            'GR',
            'HR',
            'HU',
            'IE',
            'IT',
            'LT',
            'LU',
            'LV',
            'MT',
            'NL',
            'PL',
            'PT',
            'RO',
            'SE',
            'SI',
            'SK',
            'XI'
        ]);
    }

    /**
     * Accessor para formato de exibição
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->name} ({$this->code})";
    }

    /**
     * Accessor para telefone formatado
     */
    public function getFormattedPhoneAttribute()
    {
        return $this->phone_prefix ? "+{$this->phone_prefix}" : null;
    }

    /**
     * Accessor para flag emoji baseado no código do país
     */
    public function getFlagAttribute()
    {
        if (strlen($this->code) !== 2) {
            return '🏳️'; // Flag genérica para códigos inválidos
        }

        // Converter código do país para emoji flag
        $codePoints = array_map(function ($char) {
            return 127397 + ord($char);
        }, str_split(strtoupper($this->code)));

        return mb_convert_encoding('&#' . implode(';&#', $codePoints) . ';', 'UTF-8', 'HTML-ENTITIES');
    }
}
