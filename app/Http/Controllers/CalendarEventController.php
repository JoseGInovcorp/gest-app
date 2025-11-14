<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\CalendarEventType;
use App\Models\CalendarEventAction;
use App\Models\Entity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class CalendarEventController extends Controller
{
    /**
     * Display the calendar UI (FullCalendar will fetch events from the JSON endpoint).
     */
    public function index(Request $request)
    {
        $types = CalendarEventType::active()->orderBy('name')->get(['id', 'name', 'color']);
        $actions = CalendarEventAction::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $users = User::select('id', 'name')->orderBy('name')->get();
        $entities = Entity::select('id', 'name', 'commercial_name')->orderBy('name')->get()->map(function ($e) {
            $e->display_name = $e->commercial_name ?: $e->name;
            return $e;
        });

        return Inertia::render('Calendar/Index', [
            'types' => $types,
            'actions' => $actions,
            'users' => $users,
            'entities' => $entities,
            'can' => [
                'create' => $request->user()->can('calendar-events.create'),
                'update' => $request->user()->can('calendar-events.update'),
                'delete' => $request->user()->can('calendar-events.delete'),
            ],
        ]);
    }

    /**
     * JSON endpoint for FullCalendar events.
     * Accepts optional query params: start, end (ISO dates), user_id, entity_id
     */
    public function events(Request $request)
    {
        $currentUserId = $request->user()->id;

        $query = CalendarEvent::with(['eventType', 'eventAction', 'user', 'entity'])
            ->where(function ($q) use ($currentUserId) {
                // Mostrar eventos onde:
                // 1. O utilizador é o responsável
                $q->where('user_id', $currentUserId)
                    // 2. OU o evento está partilhado com todos (partilha = true sem shared_with específicos)
                    ->orWhere(function ($subQ) use ($currentUserId) {
                        $subQ->where('partilha', true)
                            ->whereDoesntHave('sharedWith');
                    })
                    // 3. OU o evento foi partilhado especificamente com este utilizador
                    ->orWhereHas('sharedWith', function ($shareQ) use ($currentUserId) {
                        $shareQ->where('users.id', $currentUserId);
                    });
            });

        // Filter by date range if provided
        if ($request->has('start')) {
            $start = Carbon::parse($request->query('start'))->startOfDay();
            $query->whereDate('data', '>=', $start->toDateString());
        }

        if ($request->has('end')) {
            $end = Carbon::parse($request->query('end'))->subDay();
            $query->whereDate('data', '<=', $end->toDateString());
        }

        // Filtro adicional por utilizador específico
        // Só mostra eventos desse utilizador que o user atual tenha permissão para ver
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->filled('entity_id')) {
            $query->where('entity_id', $request->query('entity_id'));
        }

        $events = $query->get()->map(function (CalendarEvent $ev) {
            // Build start datetime from data + hora
            $date = $ev->data instanceof Carbon ? $ev->data->toDateString() : Carbon::parse($ev->data)->toDateString();
            $time = $ev->hora ? Carbon::parse($ev->hora)->format('H:i:s') : '00:00:00';
            $start = Carbon::parse("{$date} {$time}");
            $end = $start->copy()->addMinutes($ev->duracao ?? 0);

            $titleParts = [];
            if ($ev->eventType) {
                $titleParts[] = $ev->eventType->name;
            }
            if ($ev->entity) {
                $titleParts[] = $ev->entity->commercial_name ?: $ev->entity->name;
            }

            $title = $titleParts ? implode(' - ', $titleParts) : ($ev->descricao ?: 'Evento');

            return [
                'id' => $ev->id,
                'title' => $title,
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'allDay' => false,
                'color' => $ev->eventType?->color,
                'extendedProps' => [
                    'duracao' => $ev->duracao,
                    'partilha' => (bool) $ev->partilha,
                    'conhecimento' => $ev->conhecimento,
                    'descricao' => $ev->descricao,
                    'estado' => $ev->estado,
                    'type' => $ev->eventType?->only(['id', 'name', 'color']),
                    'action' => $ev->eventAction?->only(['id', 'name']),
                    'user' => $ev->user?->only(['id', 'name']),
                    'entity' => $ev->entity?->only(['id', 'name', 'commercial_name']),
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // frontend handles creation in a modal/page; return necessary lists
        $types = CalendarEventType::active()->orderBy('name')->get(['id', 'name']);
        $actions = CalendarEventAction::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $entities = Entity::select('id', 'name', 'commercial_name')->orderBy('name')->get();
        $users = User::select('id', 'name')->orderBy('name')->get();

        // Accept optional data and hora from query params (when clicking on calendar)
        $defaultData = $request->query('data');
        $defaultHora = $request->query('hora');

        return Inertia::render('Calendar/Create', compact('types', 'actions', 'entities', 'users', 'defaultData', 'defaultHora'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', CalendarEvent::class);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'entity_id' => ['nullable', 'exists:entities,id'],
            'calendar_event_type_id' => ['required', 'exists:calendar_event_types,id'],
            'calendar_event_action_id' => ['nullable', 'exists:calendar_event_actions,id'],
            'data' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'duracao' => ['required', 'integer', 'min:0'],
            'partilha' => ['boolean'],
            'shared_with' => ['nullable', 'array'],
            'shared_with.*' => ['exists:users,id'],
            'conhecimento' => ['nullable', 'string'],
            'descricao' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['agendado', 'em_curso', 'concluido', 'cancelado'])],
        ]);

        $event = CalendarEvent::create($data);

        // Sincronizar utilizadores partilhados se partilha estiver ativa
        if ($data['partilha'] ?? false) {
            $sharedUsers = $request->input('shared_with', []);
            $event->sharedWith()->sync($sharedUsers);
        } else {
            // Se partilha desativada, remover todas as partilhas
            $event->sharedWith()->sync([]);
        }

        return redirect()->route('calendar-events.show', $event)->with('success', 'Evento criado com sucesso');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEvent $calendarEvent)
    {
        $calendarEvent->load(['eventType', 'eventAction', 'user', 'entity', 'sharedWith']);
        return Inertia::render('Calendar/Show', [
            'event' => $calendarEvent,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalendarEvent $calendarEvent)
    {
        $this->authorize('update', $calendarEvent);

        $types = CalendarEventType::active()->orderBy('name')->get(['id', 'name']);
        $actions = CalendarEventAction::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $entities = Entity::select('id', 'name', 'commercial_name')->orderBy('name')->get();
        $users = User::select('id', 'name')->orderBy('name')->get();

        // Carregar utilizadores partilhados
        $calendarEvent->load(['eventType', 'eventAction', 'user', 'entity', 'sharedWith']);

        // Adicionar array de IDs partilhados ao evento
        $calendarEvent->shared_with_ids = $calendarEvent->sharedWith->pluck('id')->toArray();

        return Inertia::render('Calendar/Edit', [
            'event' => $calendarEvent,
            'types' => $types,
            'actions' => $actions,
            'entities' => $entities,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $this->authorize('update', $calendarEvent);

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'entity_id' => ['nullable', 'exists:entities,id'],
            'calendar_event_type_id' => ['required', 'exists:calendar_event_types,id'],
            'calendar_event_action_id' => ['nullable', 'exists:calendar_event_actions,id'],
            'data' => ['required', 'date'],
            'hora' => ['required', 'date_format:H:i'],
            'duracao' => ['required', 'integer', 'min:0'],
            'partilha' => ['boolean'],
            'shared_with' => ['nullable', 'array'],
            'shared_with.*' => ['exists:users,id'],
            'conhecimento' => ['nullable', 'string'],
            'descricao' => ['nullable', 'string'],
            'estado' => ['required', Rule::in(['agendado', 'em_curso', 'concluido', 'cancelado'])],
        ]);

        $calendarEvent->update($data);

        // Sincronizar utilizadores partilhados se partilha estiver ativa
        if ($data['partilha'] ?? false) {
            $sharedUsers = $request->input('shared_with', []);
            $calendarEvent->sharedWith()->sync($sharedUsers);
        } else {
            // Se partilha desativada, remover todas as partilhas
            $calendarEvent->sharedWith()->sync([]);
        }

        return redirect()->route('calendar-events.show', $calendarEvent)->with('success', 'Evento atualizado com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEvent $calendarEvent)
    {
        $this->authorize('delete', $calendarEvent);

        $calendarEvent->delete();

        return redirect()->route('calendar-events.index')->with('success', 'Evento removido');
    }
}
