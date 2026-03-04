<?php

namespace App\Exports;

use App\Models\Participant;
use App\Models\ProjectRequest;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ParticipantTemplateExport implements FromView
{
    public function __construct(
        private readonly ?int $projectRequestId = null
    ) {}

    public function view(): View
    {
        $project = null;
        if ($this->projectRequestId) {
            $project = ProjectRequest::query()
                ->select(['id', 'name'])
                ->find($this->projectRequestId);
        }

        $query = Participant::query()
            ->with('projectRequest:id,name')
            ->orderBy('id');

        if ($this->projectRequestId) {
            $query->where('project_request_id', $this->projectRequestId);
        }

        $rows = $query
            ->get()
            ->map(function (Participant $participant): array {
                return [
                    'id' => $participant->id,
                    'project_request_id' => $participant->project_request_id,
                    'nama_proyek' => $participant->projectRequest?->name,
                    'name' => $participant->name,
                    'employee_code' => $participant->employee_code,
                    'department' => $participant->department,
                    'date_of_birth' => optional($participant->date_of_birth)->format('Y-m-d'),
                    'address' => $participant->address,
                    'gender' => $participant->gender,
                    'marital_status' => $participant->marital_status,
                    'note' => $participant->note,
                ];
            })
            ->toArray();

        return view('exports.participant-template', [
            'rows' => $rows,
            'project' => $project,
        ]);
    }
}
