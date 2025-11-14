<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarEventAction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope para ações ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para ações inativas
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Accessor para classe CSS do badge de estado
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->is_active
            ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
            : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }

    /**
     * Accessor para texto do estado
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Ativo' : 'Inativo';
    }
}
