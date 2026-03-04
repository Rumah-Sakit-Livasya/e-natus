<?php

namespace App\Imports;

use App\Models\Participant;
use App\Models\ProjectRequest;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ParticipantImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public int $processedRows = 0;

    public function __construct(
        private readonly ?int $forcedProjectRequestId = null
    ) {}

    public function model(array $row)
    {
        if ($this->shouldSkipRow($row)) {
            return null;
        }

        $id = $this->nullableInt($row['id'] ?? null);

        $projectRequestId = $this->forcedProjectRequestId;
        if (! $projectRequestId) {
            $projectRequestId = $this->nullableInt($row['project_request_id'] ?? null);

            if (! $projectRequestId) {
                $projectName = $this->nullableString($row['nama_proyek'] ?? $row['project_name'] ?? null);
                if ($projectName) {
                    $projectRequestId = ProjectRequest::query()
                        ->where('name', $projectName)
                        ->value('id');

                    if (! $projectRequestId) {
                        throw new \RuntimeException("Proyek '{$projectName}' tidak ditemukan.");
                    }
                }
            }
        }

        $dateOfBirth = $this->normalizeDate($row['date_of_birth'] ?? null);

        $payload = [
            'project_request_id' => $projectRequestId,
            'name' => $this->nullableString($row['name'] ?? $row['nama_peserta'] ?? null),
            'employee_code' => $this->nullableString($row['employee_code'] ?? $row['nomor_pegawai_nik'] ?? null),
            'department' => $this->nullableString($row['department'] ?? $row['instansi'] ?? null),
            'date_of_birth' => $dateOfBirth,
            'address' => $this->nullableString($row['address'] ?? $row['alamat'] ?? null),
            'gender' => $this->nullableString($row['gender'] ?? $row['jenis_kelamin'] ?? null),
            'marital_status' => $this->nullableString($row['marital_status'] ?? $row['status_pernikahan'] ?? null),
            'note' => $this->nullableString($row['note'] ?? $row['catatan'] ?? null),
        ];

        $existing = null;
        if ($id) {
            $existing = Participant::find($id);
        }

        if (! $existing && $projectRequestId && ! empty($payload['employee_code'])) {
            $existing = Participant::query()
                ->where('project_request_id', $projectRequestId)
                ->where('employee_code', $payload['employee_code'])
                ->first();
        }

        if (! $existing && $projectRequestId && ! empty($payload['name']) && $dateOfBirth) {
            $existing = Participant::query()
                ->where('project_request_id', $projectRequestId)
                ->where('name', $payload['name'])
                ->whereDate('date_of_birth', $dateOfBirth)
                ->first();
        }

        if ($existing) {
            $existing->fill($payload);
            $existing->save();
            $this->processedRows++;
            return null;
        }

        Participant::create($payload);
        $this->processedRows++;

        return null;
    }

    public function rules(): array
    {
        $projectRules = ['nullable'];
        $projectNameRules = ['nullable', 'string', 'max:255'];
        $projectNameAltRules = ['nullable', 'string', 'max:255'];

        if (! $this->forcedProjectRequestId) {
            $projectRules = ['nullable', 'integer', 'exists:project_requests,id'];
            $projectRules[] = 'required_without_all:nama_proyek,project_name';
            $projectNameRules[] = 'required_without_all:project_request_id,project_name';
            $projectNameAltRules[] = 'required_without_all:project_request_id,nama_proyek';
        }

        return [
            'id' => ['nullable', 'integer'],
            'project_request_id' => $projectRules,
            'nama_proyek' => $projectNameRules,
            'project_name' => $projectNameAltRules,
            'name' => ['nullable', 'string', 'max:255', 'required_without:nama_peserta'],
            'nama_peserta' => ['nullable', 'string', 'max:255', 'required_without:name'],
            'employee_code' => ['nullable', 'string', 'max:255'],
            'nomor_pegawai_nik' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'instansi' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['required'],
            'address' => ['nullable', 'string', 'required_without:alamat'],
            'alamat' => ['nullable', 'string', 'required_without:address'],
            'gender' => ['nullable', 'in:Laki-laki,Perempuan', 'required_without:jenis_kelamin'],
            'jenis_kelamin' => ['nullable', 'in:Laki-laki,Perempuan', 'required_without:gender'],
            'marital_status' => ['nullable', 'in:Belum Menikah,Menikah,Cerai Hidup,Cerai Mati', 'required_without:status_pernikahan'],
            'status_pernikahan' => ['nullable', 'in:Belum Menikah,Menikah,Cerai Hidup,Cerai Mati', 'required_without:marital_status'],
            'note' => ['nullable', 'string'],
            'catatan' => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'id.exists' => 'ID participant tidak ditemukan.',
            'project_request_id.exists' => 'Project request ID tidak ditemukan.',
            'project_request_id.required_without_all' => 'Isi project_request_id atau nama_proyek.',
            'name.required_without' => 'Nama peserta wajib diisi.',
            'nama_peserta.required_without' => 'Nama peserta wajib diisi.',
            'date_of_birth.required' => 'Tanggal lahir wajib diisi.',
            'address.required_without' => 'Alamat wajib diisi.',
            'alamat.required_without' => 'Alamat wajib diisi.',
            'gender.required_without' => 'Gender wajib diisi.',
            'gender.in' => 'Gender hanya boleh: Laki-laki atau Perempuan.',
            'jenis_kelamin.required_without' => 'Gender wajib diisi.',
            'marital_status.required_without' => 'Status pernikahan wajib diisi.',
            'marital_status.in' => 'Status pernikahan tidak valid.',
            'status_pernikahan.required_without' => 'Status pernikahan wajib diisi.',
        ];
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function normalizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        return Carbon::parse((string) $value)->format('Y-m-d');
    }

    private function shouldSkipRow(array $row): bool
    {
        // Skip full-empty row
        $hasValue = collect($row)->contains(fn($value) => trim((string) ($value ?? '')) !== '');
        if (! $hasValue) {
            return true;
        }

        // Skip contoh row pada baris ke-2 template export
        $name = trim((string) ($row['name'] ?? ''));
        $employeeCode = trim((string) ($row['employee_code'] ?? ''));
        $department = trim((string) ($row['department'] ?? ''));
        $dateOfBirth = trim((string) ($row['date_of_birth'] ?? ''));
        $address = trim((string) ($row['address'] ?? ''));
        $gender = trim((string) ($row['gender'] ?? ''));
        $maritalStatus = trim((string) ($row['marital_status'] ?? ''));

        return $name === 'Nama Peserta'
            && $employeeCode === '123456'
            && $department === 'Instansi'
            && $dateOfBirth === '1990-01-01'
            && $address === 'Alamat lengkap'
            && $gender === 'Laki-laki'
            && $maritalStatus === 'Belum Menikah';
    }

    public function isEmptyWhen(array $row): bool
    {
        $relevant = [
            'name',
            'nama_peserta',
            'employee_code',
            'nomor_pegawai_nik',
            'department',
            'instansi',
            'date_of_birth',
            'address',
            'alamat',
            'gender',
            'jenis_kelamin',
            'marital_status',
            'status_pernikahan',
            'note',
            'catatan',
        ];

        foreach ($relevant as $key) {
            if (trim((string) ($row[$key] ?? '')) !== '') {
                return false;
            }
        }

        return true;
    }

    public function prepareForValidation(array $row, int $index): array
    {
        foreach ([
            'employee_code',
            'nomor_pegawai_nik',
            'name',
            'nama_peserta',
            'department',
            'instansi',
            'address',
            'alamat',
            'gender',
            'jenis_kelamin',
            'marital_status',
            'status_pernikahan',
            'note',
            'catatan',
            'nama_proyek',
            'project_name',
        ] as $field) {
            if (array_key_exists($field, $row) && $row[$field] !== null && $row[$field] !== '') {
                $row[$field] = (string) $row[$field];
            }
        }

        return $row;
    }

}
