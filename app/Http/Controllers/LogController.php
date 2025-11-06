<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class LogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $query = Activity::with('causer:id,name,email')
            ->latest();

        // Aplicar filtros se existirem
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('log_name', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $activities = $query->paginate(50)->through(function ($activity) {
            return [
                'id' => $activity->id,
                'log_name' => $activity->log_name,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type ? class_basename($activity->subject_type) : null,
                'subject_id' => $activity->subject_id,
                'event' => $activity->event ?? $activity->description, // Usar description se event for null
                'causer' => $activity->causer ? [
                    'id' => $activity->causer->id,
                    'name' => $activity->causer->name,
                    'email' => $activity->causer->email,
                ] : null,
                'properties' => $activity->properties,
                'created_at' => $activity->created_at,
                'ip_address' => $activity->properties['ip'] ?? null,
                'user_agent' => $activity->properties['user_agent'] ?? null,
            ];
        });

        return Inertia::render('Logs/Index', [
            'activities' => $activities,
            'filters' => $request->only(['search']),
        ]);
    }
}
