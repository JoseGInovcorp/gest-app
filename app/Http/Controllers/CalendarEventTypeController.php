<?php

namespace App\Http\Controllers;

use App\Models\CalendarEventType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CalendarEventTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CalendarEventType::query();

        // Pesquisa
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $eventTypes = $query->orderBy('name')->get();

        return Inertia::render('CalendarEventTypes/Index', [
            'eventTypes' => $eventTypes,
            'filters' => $request->only(['search', 'status']),
            'can' => [
                'create' => $request->user()->can('calendar-event-types.create'),
                'view' => $request->user()->can('calendar-event-types.read'),
                'edit' => $request->user()->can('calendar-event-types.update'),
                'delete' => $request->user()->can('calendar-event-types.delete'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CalendarEventTypes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calendar_event_types,name',
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ]);

        $eventType = CalendarEventType::create($validated);

        activity()
            ->performedOn($eventType)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('created');

        return redirect()->route('calendar-event-types.index')
            ->with('success', 'Tipo de evento criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEventType $calendarEventType)
    {
        return Inertia::render('CalendarEventTypes/Show', [
            'eventType' => $calendarEventType,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalendarEventType $calendarEventType)
    {
        return Inertia::render('CalendarEventTypes/Edit', [
            'eventType' => $calendarEventType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarEventType $calendarEventType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calendar_event_types,name,' . $calendarEventType->id,
            'description' => 'nullable|string',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'required|boolean',
        ]);

        $calendarEventType->update($validated);

        activity()
            ->performedOn($calendarEventType)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('updated');

        return redirect()->route('calendar-event-types.index')
            ->with('success', 'Tipo de evento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEventType $calendarEventType)
    {
        $name = $calendarEventType->name;

        activity()
            ->performedOn($calendarEventType)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'deleted_type' => [
                    'name' => $name,
                    'color' => $calendarEventType->color
                ]
            ])
            ->log('deleted');

        $calendarEventType->delete();

        return redirect()->route('calendar-event-types.index')
            ->with('success', "Tipo de evento '{$name}' eliminado com sucesso!");
    }
}
