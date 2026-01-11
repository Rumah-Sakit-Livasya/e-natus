<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAttendance extends Model
{
    protected $fillable = [
        'project_request_id',
        'employee_id',
        'tanggal',
        'lokasi_maps',
        'foto',
        'notes',
    ];

    public function project()
    {
        return $this->belongsTo(ProjectRequest::class, 'project_request_id');
    }

    public function employee()
    {
        // Ganti \App\Models\Employee::class dengan model yang benar
        return $this->belongsTo(\App\Models\Employee::class, 'employee_id');
    }

    public function sdm()
    {
        // Ganti nama model SDM::class jika berbeda
        return $this->belongsTo(SDM::class, 'employee_id');
    }
}
