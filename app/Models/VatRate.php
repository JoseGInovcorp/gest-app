<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VatRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'is_default',
        'active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_default' => 'boolean',
        'active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    // Métodos auxiliares
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 0) . '%';
    }
}
