<?php

namespace App\Models;

use App\Casts\EncryptedString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'entity_id',
        'first_name',
        'last_name',
        'function',
        'phone',
        'mobile',
        'email',
        'rgpd_consent',
        'observations',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'rgpd_consent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        // Campos sensíveis cifrados com AES-256-CBC (sem serialização)
        'phone' => EncryptedString::class,
        'mobile' => EncryptedString::class,
        'email' => EncryptedString::class,
    ];

    protected $appends = [
        'nome',
        'apelido',
        'funcao',
        'telefone',
        'telemovel',
        'full_name',
        'display_name'
    ];

    // Relacionamentos
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    public function scopeInactive(Builder $query): void
    {
        $query->where('status', 'inactive');
    }

    public function scopeForEntity(Builder $query, int $entityId): void
    {
        $query->where('entity_id', $entityId);
    }

    public function scopeWithRgpdConsent(Builder $query): void
    {
        $query->where('rgpd_consent', true);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->full_name . ($this->function ? " ({$this->function})" : '');
    }

    // Accessors para compatibilidade com nomenclatura portuguesa
    public function getNomeAttribute(): ?string
    {
        return $this->first_name;
    }

    public function getApelidoAttribute(): ?string
    {
        return $this->last_name;
    }

    public function getFuncaoAttribute(): ?string
    {
        return $this->function;
    }

    public function getTelefoneAttribute(): ?string
    {
        return $this->phone;
    }

    public function getTelemovelAttribute(): ?string
    {
        return $this->mobile;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    // Métodos auxiliares
    public static function getNextNumber(): int
    {
        return (int) static::withTrashed()->max('number') + 1;
    }

    public function activate(): bool
    {
        return $this->update(['status' => 'active']);
    }

    public function deactivate(): bool
    {
        return $this->update(['status' => 'inactive']);
    }
}
