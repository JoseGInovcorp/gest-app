<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalendarEventType;

class CalendarEventTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            [
                'name' => 'Visita',
                'description' => 'Visita a cliente ou fornecedor',
                'color' => '#3B82F6', // Blue
                'icon' => 'Users',
                'is_active' => true,
            ],
            [
                'name' => 'Reunião',
                'description' => 'Reunião interna ou externa',
                'color' => '#8B5CF6', // Purple
                'icon' => 'Calendar',
                'is_active' => true,
            ],
            [
                'name' => 'Intervenção Técnica',
                'description' => 'Trabalho técnico ou manutenção',
                'color' => '#EF4444', // Red
                'icon' => 'Wrench',
                'is_active' => true,
            ],
            [
                'name' => 'Auditoria',
                'description' => 'Auditoria ou inspeção',
                'color' => '#F59E0B', // Amber
                'icon' => 'ClipboardCheck',
                'is_active' => true,
            ],
            [
                'name' => 'Formação',
                'description' => 'Sessão de formação ou treino',
                'color' => '#10B981', // Green
                'icon' => 'GraduationCap',
                'is_active' => true,
            ],
            [
                'name' => 'Apresentação',
                'description' => 'Apresentação comercial ou técnica',
                'color' => '#EC4899', // Pink
                'icon' => 'Presentation',
                'is_active' => true,
            ],
        ];

        foreach ($eventTypes as $eventType) {
            CalendarEventType::create($eventType);
        }
    }
}
