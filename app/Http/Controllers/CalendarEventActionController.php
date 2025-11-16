<?php

namespace App\Http\Controllers;

use App\Models\CalendarEventAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CalendarEventActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CalendarEventAction::query();

        // Pesquisa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        // Ordenação
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $eventActions = $query->get();

        return Inertia::render('CalendarEventActions/Index', [
            'eventActions' => $eventActions,
            'filters' => $request->only(['search', 'status']),
            'can' => [
                'create' => $request->user()->can('calendar-event-actions.create'),
                'edit' => $request->user()->can('calendar-event-actions.update'),
                'delete' => $request->user()->can('calendar-event-actions.delete'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('CalendarEventActions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calendar_event_actions,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $eventAction = CalendarEventAction::create($validated);

        activity()
            ->performedOn($eventAction)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ])
            ->log('created');

        return redirect()->route('calendar-event-actions.index')
            ->with('success', 'Ação de evento criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(CalendarEventAction $calendarEventAction)
    {
        return Inertia::render('CalendarEventActions/Show', [
            'eventAction' => $calendarEventAction,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CalendarEventAction $calendarEventAction)
    {
        return Inertia::render('CalendarEventActions/Edit', [
            'eventAction' => $calendarEventAction,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CalendarEventAction $calendarEventAction)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:calendar_event_actions,name,' . $calendarEventAction->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $calendarEventAction->update($validated);

        activity()
            ->performedOn($calendarEventAction)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ])
            ->log('updated');

        return redirect()->route('calendar-event-actions.index')
            ->with('success', 'Ação de evento atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CalendarEventAction $calendarEventAction)
    {
        $name = $calendarEventAction->name;

        activity()
            ->performedOn($calendarEventAction)
            ->causedBy(Auth::user())
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'deleted_action' => [
                    'name' => $name
                ]
            ])
            ->log('deleted');

        $calendarEventAction->delete();

        return redirect()->route('calendar-event-actions.index')
            ->with('success', "Ação de evento '{$name}' eliminada com sucesso!");
    }
}
