<?php

namespace App\Filament\Widgets;

use App\Models\ProjectRequest;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class ProjectCalendarWidget extends Widget
{
    protected static string $view = 'filament.widgets.project-calendar-widget';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    // Metode ini akan dipanggil dari file Blade
    public function getCalendarEvents(): array
    {
        return ProjectRequest::query()
            ->select('name', 'start_period', 'end_period')
            ->get()
            ->map(function (ProjectRequest $project) {
                $endDate = Carbon::parse($project->end_period)->addDay()->toDateString();
                return [
                    'title' => trim($project->name),
                    'start' => $project->start_period,
                    'end'   => $endDate,
                ];
            })
            ->all();
    }
}
