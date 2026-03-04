<?php

namespace App\Exports;

use App\Imports\MedicalCheckImport;
use App\Models\Participant;
use App\Support\MedicalCheckTemplateSchema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromView;

class MedicalCheckTemplateExport implements FromView
{
    public function __construct(
        private readonly int $projectRequestId,
        private readonly string $type
    ) {}

    public function view(): View
    {
        $definition = MedicalCheckImport::typeDefinition($this->type);
        $modelClass = $definition['model'];
        $tableName = $definition['table'];

        $dbColumns = array_values(array_diff(
            Schema::getColumnListing($tableName),
            ['created_at', 'updated_at']
        ));

        $columns = MedicalCheckTemplateSchema::orderedColumns($this->type, $dbColumns);
        $headerLabels = MedicalCheckTemplateSchema::labels($this->type, $columns);

        $participants = Participant::query()
            ->with('projectRequest:id,name')
            ->where('project_request_id', $this->projectRequestId)
            ->orderBy('id')
            ->get();

        $existingByParticipantId = collect();
        if ($participants->isNotEmpty()) {
            $existingByParticipantId = $modelClass::query()
                ->whereIn('participant_id', $participants->pluck('id'))
                ->orderByDesc('id')
                ->get()
                ->unique('participant_id')
                ->keyBy('participant_id');
        }

        $rows = $participants
            ->map(fn(Participant $participant): array => $this->buildRow(
                participant: $participant,
                existingByParticipantId: $existingByParticipantId,
                columns: $columns
            ))
            ->toArray();

        return view('exports.medical-check-template', [
            'columns' => $columns,
            'headerLabels' => $headerLabels,
            'rows' => $rows,
        ]);
    }

    /**
     * @param Collection<int, mixed> $existingByParticipantId
     * @param array<int, string> $columns
     * @return array<string, mixed>
     */
    private function buildRow(Participant $participant, Collection $existingByParticipantId, array $columns): array
    {
        $existing = $existingByParticipantId->get($participant->id);
        $row = [];

        foreach ($columns as $column) {
            $row[$column] = match ($column) {
                'id' => $existing?->id,
                'participant_id' => $participant->id,
                'participant_name' => $participant->name,
                'employee_code' => $participant->employee_code,
                'date_of_birth' => optional($participant->date_of_birth)->format('Y-m-d'),
                'project_request_id' => $participant->project_request_id,
                'nama_proyek' => $participant->projectRequest?->name,
                'dokter_id' => $this->doctorValue($existing),
                // Isi default dari data participant agar template tidak kosong.
                'instansi', 'department' => $this->normalizeValue($existing?->{$column} ?? $participant->department),
                default => $this->normalizeValue($existing?->{$column} ?? null),
            };
        }

        return $row;
    }

    private function normalizeValue(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return $value;
    }

    private function doctorValue(mixed $existing): mixed
    {
        if (! $existing) {
            return null;
        }

        if (method_exists($existing, 'dokter')) {
            $dokter = $existing->dokter;
            if ($dokter?->name) {
                return $dokter->name;
            }
        }

        return $existing->dokter_id ?? null;
    }
}
