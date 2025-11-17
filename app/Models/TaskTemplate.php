<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TaskTemplate extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'code',
        'label',
        'description',
        'assigned_group',
        'default_sequence',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_sequence' => 'integer',
    ];

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['label', 'description', 'assigned_group', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderedBySequence($query)
    {
        return $query->orderBy('default_sequence', 'asc');
    }
}
