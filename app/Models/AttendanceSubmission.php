<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_request_id',
        'employee_id',
        'tanggal_absensi',
        'alasan',
        'foto_bukti',
        'status',
        'submitted_by',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'tanggal_absensi' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
