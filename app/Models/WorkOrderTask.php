<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WorkOrderTask extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'work_order_id',
        'task_type',
        'title',
        'description',
        'assigned_to',
        'assigned_group',
        'status',
        'sequence_order',
        'depends_on_task_id',
        'due_date',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Activity Log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'assigned_to', 'completed_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Relationships
    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function dependsOn(): BelongsTo
    {
        return $this->belongsTo(WorkOrderTask::class, 'depends_on_task_id');
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

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeAssignedToGroup($query, $group)
    {
        return $query->where('assigned_group', $group);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', 'concluida');
    }

    // Helper methods
    public function canStart()
    {
        // Verifica se a tarefa dependente foi concluída
        if ($this->depends_on_task_id) {
            $dependentTask = $this->dependsOn;
            return $dependentTask && $dependentTask->status === 'concluida';
        }
        return true;
    }

    public function complete($notes = null)
    {
        $this->update([
            'status' => 'concluida',
            'completed_at' => now(),
            'notes' => $notes ?? $this->notes,
        ]);

        // Atualizar status da work order
        $this->workOrder->updateStatus();
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date &&
            $this->due_date->isPast() &&
            $this->status !== 'concluida';
    }

    public function getCanStartAttribute()
    {
        return $this->canStart();
    }

    // Tipos de tarefas disponíveis
    public static function taskTypes()
    {
        return [
            'VALIDATE_STOCK' => [
                'label' => 'Validar Disponibilidade em Armazém',
                'description' => 'Verificar se os artigos estão disponíveis em stock',
                'assigned_group' => 'Gestor de Armazém',
            ],
            'CREATE_SUPPLIER_ORDER' => [
                'label' => 'Criar Encomenda a Fornecedor',
                'description' => 'Criar encomenda ao fornecedor para artigos em falta',
                'assigned_group' => 'Gestor de Compras',
            ],
            'RECEIVE_STOCK' => [
                'label' => 'Receção em Armazém',
                'description' => 'Recepcionar artigos encomendados ao fornecedor',
                'assigned_group' => 'Gestor de Armazém',
            ],
            'WAREHOUSE_PICK' => [
                'label' => 'Recolha do Armazém',
                'description' => 'Recolher artigos do armazém para preparação',
                'assigned_group' => 'Gestor de Armazém',
            ],
            'PACKAGING' => [
                'label' => 'Embalamento',
                'description' => 'Embalar artigos para envio ao cliente',
                'assigned_group' => 'Gestor de Armazém',
            ],
            'CREATE_SHIPPING_GUIDE' => [
                'label' => 'Criar Guia de Transporte',
                'description' => 'Gerar guia de transporte para envio',
                'assigned_group' => 'Gestor de Armazém',
            ],
            'SCHEDULE_PICKUP' => [
                'label' => 'Agendar Recolha por Transportadora',
                'description' => 'Agendar recolha com transportadora',
                'assigned_group' => 'Gestor de Armazém',
            ],
            'SHIPPED' => [
                'label' => 'Encomenda Enviada',
                'description' => 'Marcar encomenda como enviada',
                'assigned_group' => 'Gestor de Armazém',
            ],
            'READY_FOR_PICKUP' => [
                'label' => 'Disponível para Levantamento',
                'description' => 'Encomenda pronta para levantamento pelo cliente',
                'assigned_group' => 'Gestor de Armazém',
            ],
            'DELIVERED' => [
                'label' => 'Entregue ao Cliente',
                'description' => 'Confirmar entrega ao cliente',
                'assigned_group' => 'Gestor de Armazém',
            ],
        ];
    }
}
