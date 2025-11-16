<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Entity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'number',
        'name',
        'commercial_name',
        'email',
        'phone',
        'mobile',
        'fax',
        'website',
        'tax_number',
        'vat_number',
        'country_code',
        'vies_valid',
        'vies_last_check',
        'vies_data',
        'address',
        'address_2',
        'postal_code',
        'city',
        'district',
        'country',
        'different_billing_address',
        'billing_address',
        'billing_address_2',
        'billing_postal_code',
        'billing_city',
        'billing_district',
        'billing_country',
        'credit_limit',
        'payment_days',
        'payment_method',
        'discount_percentage',
        'tax_exempt',
        'iban',
        'bic',
        'bank_name',
        'active',
        'notes',
        'custom_fields',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'vies_valid' => 'boolean',
        'vies_last_check' => 'datetime',
        'vies_data' => 'array',
        'different_billing_address' => 'boolean',
        'credit_limit' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'tax_exempt' => 'boolean',
        'active' => 'boolean',
        'custom_fields' => 'array',
        // Campos sensíveis cifrados (DESATIVADO EM DEV - Ativar antes de produção)
        // 'tax_number' => 'encrypted',
        // 'phone' => 'encrypted',
        // 'mobile' => 'encrypted',
        // 'email' => 'encrypted',
        // 'iban' => 'encrypted',
    ];

    protected $appends = [
        'nif',
    ];

    // Relacionamentos
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function activeContacts(): HasMany
    {
        return $this->hasMany(Contact::class)->where('status', 'active');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeClients($query)
    {
        return $query->whereIn('type', ['client', 'both']);
    }

    public function scopeSuppliers($query)
    {
        return $query->whereIn('type', ['supplier', 'both']);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Métodos auxiliares
    public function isClient(): bool
    {
        return in_array($this->type, ['client', 'both']);
    }

    public function isSupplier(): bool
    {
        return in_array($this->type, ['supplier', 'both']);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->address_2,
            $this->postal_code . ' ' . $this->city,
            $this->district,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    public function getBillingAddressAttribute(): string
    {
        if (!$this->different_billing_address) {
            return $this->getFullAddressAttribute();
        }

        $parts = array_filter([
            $this->billing_address,
            $this->billing_address_2,
            $this->billing_postal_code . ' ' . $this->billing_city,
            $this->billing_district,
            $this->billing_country
        ]);

        return implode(', ', $parts);
    }

    public function needsViesCheck(): bool
    {
        return $this->country_code !== 'PT'
            && $this->vat_number
            && (!$this->vies_last_check || $this->vies_last_check->diffInDays(now()) > 30);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->commercial_name ?: $this->name;
    }

    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'client' => 'Cliente',
            'supplier' => 'Fornecedor',
            'both' => 'Cliente/Fornecedor',
            default => 'N/D'
        };
    }

    // Accessor para compatibilidade com formulários que usam "nif"
    public function getNifAttribute(): ?string
    {
        return $this->tax_number;
    }

    // Accessor para retornar country_code quando se acede a "country" em contexto de formulário
    // Nota: Isto pode causar conflito com o campo "country" da BD (nome completo)
    // Solução: usar sempre country_code no formulário
    protected function getCountryCodeForFormAttribute(): ?string
    {
        return $this->country_code;
    }
}
