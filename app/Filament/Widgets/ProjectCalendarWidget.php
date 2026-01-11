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

    // List of colors for different projects
    protected array $colors = [
        '#FF6B6B',
        '#4ECDC4',
        '#45B7D1',
        '#96CEB4',
        '#FFEEAD',
        '#D4A5A5',
        '#9B59B6',
        '#3498DB',
        '#E67E22',
        '#2ECC71',
        '#1ABC9C',
        '#F1C40F'
    ];

    // Metode ini akan dipanggil dari file Blade
    public function getCalendarEvents(): array
    {
        $projectColors = [];
        $colorIndex = 0;

        return ProjectRequest::query()
            ->select('id', 'name', 'start_period', 'end_period')
            ->get()
            ->map(function (ProjectRequest $project) use (&$projectColors, &$colorIndex) {
                // Assign a unique color for each project
                if (!isset($projectColors[$project->id])) {
                    $projectColors[$project->id] = $this->colors[$colorIndex % count($this->colors)];
                    $colorIndex++;
                }

                $endDate = Carbon::parse($project->end_period)->addDay()->toDateString();
                return [
                    'title' => trim($project->name),
                    'start' => $project->start_period,
                    'end'   => $endDate,
                    'backgroundColor' => $projectColors[$project->id],
                    'borderColor' => $projectColors[$project->id],
                ];
            })
            ->all();
    }
}
