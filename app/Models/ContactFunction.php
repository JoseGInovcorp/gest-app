<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactFunction extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope para funções ativas
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Relacionamento com contactos
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'function', 'name');
    }
}
