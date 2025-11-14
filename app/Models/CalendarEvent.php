<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'entity_id',
        'calendar_event_type_id',
        'calendar_event_action_id',
        'data',
        'hora',
        'duracao',
        'partilha',
        'conhecimento',
        'descricao',
        'estado',
    ];

    protected $casts = [
        'data' => 'date',
        'hora' => 'datetime:H:i',
        'duracao' => 'integer',
        'partilha' => 'boolean',
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    public function eventType()
    {
        return $this->belongsTo(CalendarEventType::class, 'calendar_event_type_id');
    }

    public function eventAction()
    {
        return $this->belongsTo(CalendarEventAction::class, 'calendar_event_action_id');
    }

    public function sharedWith()
    {
        return $this->belongsToMany(User::class, 'calendar_event_shares', 'calendar_event_id', 'user_id')
            ->withTimestamps();
    }

    // Scopes
    public function scopeAgendado($query)
    {
        return $query->where('estado', 'agendado');
    }

    public function scopeEmCurso($query)
    {
        return $query->where('estado', 'em_curso');
    }

    public function scopeConcluido($query)
    {
        return $query->where('estado', 'concluido');
    }

    public function scopeCancelado($query)
    {
        return $query->where('estado', 'cancelado');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByEntity($query, $entityId)
    {
        return $query->where('entity_id', $entityId);
    }

    // Accessors
    public function getEstadoBadgeClassAttribute()
    {
        return match ($this->estado) {
            'agendado' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400',
            'em_curso' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
            'concluido' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
            'cancelado' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400',
        };
    }

    public function getEstadoLabelAttribute()
    {
        return match ($this->estado) {
            'agendado' => 'Agendado',
            'em_curso' => 'Em Curso',
            'concluido' => 'Concluído',
            'cancelado' => 'Cancelado',
            default => 'Desconhecido',
        };
    }
}
