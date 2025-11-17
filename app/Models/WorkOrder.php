<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WorkOrder extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'customer_order_id',
        'title',
        'description',
        'priority',
        'status',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'priority', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function customerOrder(): BelongsTo
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(WorkOrderTask::class)->orderBy('sequence_order');
    }

    // Scopes
    public function scopePendente($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeEmProgresso($query)
    {
        return $query->where('status', 'em_progresso');
    }

    public function scopeConcluida($query)
    {
        return $query->where('status', 'concluida');
    }

    public function scopePrioridade($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Helper methods
    public function updateStatus()
    {
        $totalTasks = $this->tasks()->count();
        $completedTasks = $this->tasks()->where('status', 'concluida')->count();
        $inProgressTasks = $this->tasks()->where('status', 'em_progresso')->count();

        if ($totalTasks == $completedTasks && $totalTasks > 0) {
            $this->update(['status' => 'concluida']);
        } elseif ($inProgressTasks > 0) {
            $this->update(['status' => 'em_progresso']);
        } else {
            $this->update(['status' => 'pendente']);
        }
    }

    public function getProgressPercentageAttribute()
    {
        $total = $this->tasks()->count();
        if ($total == 0) return 0;

        $completed = $this->tasks()->where('status', 'concluida')->count();
        return round(($completed / $total) * 100);
    }
}
