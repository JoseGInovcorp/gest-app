<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalendarEventAction;

class CalendarEventActionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventActions = [
            [
                'name' => 'Confirmar',
                'description' => 'Confirmar a realização do evento',
                'is_active' => true,
            ],
            [
                'name' => 'Reagendar',
                'description' => 'Alterar data/hora do evento',
                'is_active' => true,
            ],
            [
                'name' => 'Aprovar',
                'description' => 'Aprovar o evento',
                'is_active' => true,
            ],
            [
                'name' => 'Concluir',
                'description' => 'Marcar evento como concluído',
                'is_active' => true,
            ],
            [
                'name' => 'Cancelar',
                'description' => 'Cancelar o evento',
                'is_active' => true,
            ],
            [
                'name' => 'Adiar',
                'description' => 'Adiar evento sem data definida',
                'is_active' => true,
            ],
        ];

        foreach ($eventActions as $eventAction) {
            CalendarEventAction::create($eventAction);
        }
    }
}
