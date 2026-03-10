<?php

namespace App\Imports;

use App\Models\Dokter;
use App\Models\Participant;
use App\Models\ProjectRequest;
use App\Support\MedicalCheckTemplateSchema;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class MedicalCheckImport implements OnEachRow, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @var array<string, array{
     *     label: string,
     *     model: class-string<Model>,
     *     table: string,
     *     unique: array<int, string>,
     *     required_for_create: array<int, string>
     * }>
     */
    private const TYPE_CONFIG = [
        'audiometry' => [
            'label' => 'Audiometri',
            'model' => \App\Models\AudiometryCheck::class,
            'table' => 'audiometry_checks',
            'unique' => ['no_rm'],
            'required_for_create' => ['participant_id', 'tanggal_pemeriksaan', 'no_rm'],
        ],
        'drug_test' => [
            'label' => 'Tes Narkoba',
            'model' => \App\Models\DrugTest::class,
            'table' => 'drug_tests',
            'unique' => ['no_mcu'],
            'required_for_create' => ['participant_id', 'tanggal_pemeriksaan', 'no_mcu'],
        ],
        'ekg' => [
            'label' => 'EKG',
            'model' => \App\Models\EkgCheck::class,
            'table' => 'ekg_checks',
            'unique' => ['no_rm'],
            'required_for_create' => ['participant_id', 'tanggal_pemeriksaan'],
        ],
        'lab' => [
            'label' => 'Lab Lengkap',
            'model' => \App\Models\LabCheck::class,
            'table' => 'lab_checks',
            'unique' => ['no_lab'],
            'required_for_create' => ['participant_id', 'tanggal_pemeriksaan'],
        ],
        'rontgen' => [
            'label' => 'Rontgen',
            'model' => \App\Models\RontgenCheck::class,
            'table' => 'rontgen_checks',
            'unique' => ['no_rontgen'],
            'required_for_create' => ['participant_id', 'tanggal_pemeriksaan'],
        ],
        'spirometry' => [
            'label' => 'Spirometri',
            'model' => \App\Models\SpirometryCheck::class,
            'table' => 'spirometry_checks',
            'unique' => ['no_rm'],
            'required_for_create' => ['participant_id', 'tanggal_pemeriksaan'],
        ],
        'treadmill' => [
            'label' => 'Treadmill',
            'model' => \App\Models\TreadmillCheck::class,
            'table' => 'treadmill_checks',
            'unique' => ['no_rm'],
            'required_for_create' => ['participant_id', 'tanggal_pemeriksaan'],
        ],
        'usg_abdomen' => [
            'label' => 'USG Abdomen',
            'model' => \App\Models\UsgAbdomenCheck::class,
            'table' => 'usg_abdomen_checks',
            'unique' => ['no_rm'],
            'required_for_create' => ['participant_id', 'tanggal_pemeriksaan'],
        ],
        'usg_mammae' => [
            'label' => 'USG Mammae',
            'model' => \App\Models\UsgMammaeCheck::class,
            'table' => 'usg_mammae_checks',
            'unique' => ['no_rm'],
            'required_for_create' => ['participant_id', 'tanggal_pemeriksaan'],
        ],
    ];

    /**
     * @var array<string, array<int, string>>
     */
    private const COLUMN_ALIASES = [
        'no_rm' => ['nomor_rm', 'nomer_rm'],
        'no_lab' => ['nomor_lab'],
        'no_mcu' => ['nomor_mcu'],
        'no_rontgen' => ['nomor_rontgen'],
        'tanggal_pemeriksaan' => ['tanggal_periksa', 'tgl_pemeriksaan', 'tgl_periksa', 'tanggal_check'],
        'participant_id' => ['id_participant'],
    ];

    /**
     * @var array<int, string>
     */
    private array $columns;

    /**
     * @var array<string, string>|null
     */
    private ?array $headingAliases = null;

    /**
     * @param class-string<Model> $modelClass
     * @param array<int, string> $uniqueLookupColumns
     * @param array<int, string> $requiredForCreate
     */
    private function __construct(
        private readonly string $type,
        private readonly string $checkLabel,
        private readonly string $modelClass,
        private readonly string $tableName,
        private readonly array $uniqueLookupColumns,
        private readonly array $requiredForCreate,
        private readonly ?int $forcedProjectRequestId = null,
    ) {
        $this->columns = array_values(array_diff(
            Schema::getColumnListing($this->tableName),
            ['id', 'created_at', 'updated_at']
        ));
    }

    /**
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        $options = [];
        foreach (self::TYPE_CONFIG as $key => $config) {
            $options[$key] = $config['label'];
        }

        return $options;
    }

    public static function fromType(string $type, ?int $forcedProjectRequestId = null): self
    {
        $config = self::typeDefinition($type);

        return new self(
            type: $type,
            checkLabel: $config['label'],
            modelClass: $config['model'],
            tableName: $config['table'],
            uniqueLookupColumns: $config['unique'],
            requiredForCreate: $config['required_for_create'],
            forcedProjectRequestId: $forcedProjectRequestId,
        );
    }

    /**
     * @return array{
     *     label: string,
     *     model: class-string<Model>,
     *     table: string,
     *     unique: array<int, string>,
     *     required_for_create: array<int, string>
     * }
     */
    public static function typeDefinition(string $type): array
    {
        $config = self::TYPE_CONFIG[$type] ?? null;
        if (! $config) {
            throw new \InvalidArgumentException("Jenis pemeriksaan '{$type}' tidak dikenali.");
        }

        return $config;
    }

    public int $processedRows = 0;

    public function getCheckLabel(): string
    {
        return $this->checkLabel;
    }

    public function onRow(Row $row): void
    {
        $index = $row->getIndex();
        $rawRow = $this->normalizeRow($row->toArray());

        if ($this->isRowEmpty($rawRow)) {
            return;
        }

        $recordId = $this->nullableInt($rawRow['id'] ?? null);
        $participantId = $this->resolveParticipantId($rawRow, $index);
        $payload = $this->buildPayload($rawRow, $participantId, $index);
        $this->hydrateDoctorFields($payload, $rawRow, $index);

        $existing = $this->resolveExistingRecord($recordId, $payload);

        if ($existing) {
            $existing->fill($payload);
            $existing->save();
            $this->processedRows++;

            return;
        }

        $this->ensureCreateRequirements($payload, $index);
        $this->modelClass::query()->create($payload);
        $this->processedRows++;
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function normalizeRow(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $keyString = trim((string) $key);
            if ($keyString === '') {
                continue;
            }

            $normalizedKey = $this->normalizeHeadingKey($keyString);
            if ($normalizedKey === '') {
                continue;
            }

            $normalizedKey = $this->getHeadingAliasMap()[$normalizedKey] ?? $normalizedKey;

            $normalized[$normalizedKey] = $this->normalizeCell($value);
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizeCell(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);
            return $value === '' ? null : $value;
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function resolveParticipantId(array $row, int $index): int
    {
        $participantId = $this->nullableInt(
            $this->firstNotNull(
                $row['participant_id'] ?? null,
                $row['id_participant'] ?? null,
            )
        );
        if ($participantId) {
            $participantExists = Participant::query()
                ->whereKey($participantId)
                ->exists();

            if (! $participantExists) {
                throw new \RuntimeException("Baris {$index}: participant_id {$participantId} tidak ditemukan.");
            }

            return $participantId;
        }

        $projectId = $this->resolveProjectRequestId($row);

        $employeeCode = $this->firstNotNull(
            $row['participant_employee_code'] ?? null,
            $row['employee_code'] ?? null,
            $row['nomor_pegawai_nik'] ?? null,
            $row['participant_nik'] ?? null,
        );

        if ($employeeCode !== null) {
            $query = Participant::query()->where('employee_code', (string) $employeeCode);
            if ($projectId) {
                $query->where('project_request_id', $projectId);
            }

            $matches = $query->get(['id']);
            if ($matches->count() === 1) {
                return (int) $matches->first()->id;
            }

            if ($matches->count() > 1) {
                throw new \RuntimeException("Baris {$index}: participant dengan employee_code '{$employeeCode}' lebih dari satu. Tambahkan project_request_id atau participant_id.");
            }
        }

        $participantName = $this->firstNotNull(
            $row['participant_name'] ?? null,
            $row['name'] ?? null,
            $row['nama_peserta'] ?? null,
        );

        if ($participantName !== null) {
            $query = Participant::query()->where('name', (string) $participantName);
            if ($projectId) {
                $query->where('project_request_id', $projectId);
            }

            $dateOfBirth = $row['date_of_birth'] ?? null;
            if ($dateOfBirth === null || $dateOfBirth === '') {
                $dateOfBirth = $row['tanggal_lahir'] ?? null;
            }

            if ($dateOfBirth !== null && $dateOfBirth !== '') {
                $query->whereDate('date_of_birth', $this->normalizeDate($dateOfBirth, $index));
            }

            $matches = $query->get(['id']);
            if ($matches->count() === 1) {
                return (int) $matches->first()->id;
            }

            if ($matches->count() > 1) {
                throw new \RuntimeException("Baris {$index}: participant dengan nama '{$participantName}' lebih dari satu. Tambahkan employee_code atau participant_id.");
            }
        }

        throw new \RuntimeException("Baris {$index}: participant tidak ditemukan. Isi salah satu: participant_id, employee_code, atau nama_peserta.");
    }

    /**
     * @param array<string, mixed> $row
     */
    private function resolveProjectRequestId(array $row): ?int
    {
        if ($this->forcedProjectRequestId) {
            return $this->forcedProjectRequestId;
        }

        $projectId = $this->nullableInt(
            $this->firstNotNull(
                $row['project_request_id'] ?? null,
                $row['project_id'] ?? null,
            )
        );

        if ($projectId) {
            return $projectId;
        }

        $projectName = $this->firstNotNull(
            $row['nama_proyek'] ?? null,
            $row['project_name'] ?? null
        );

        if ($projectName === null) {
            return null;
        }

        return ProjectRequest::query()
            ->where('name', (string) $projectName)
            ->value('id');
    }

    /**
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function buildPayload(array $row, int $participantId, int $index): array
    {
        $payload = [
            'participant_id' => $participantId,
        ];

        foreach ($this->columns as $column) {
            if ($column === 'participant_id') {
                continue;
            }
            if (! MedicalCheckTemplateSchema::isExcelInputColumn($column)) {
                continue;
            }

            $value = $this->extractColumnValue($column, $row);
            if ($value === null || $value === '') {
                continue;
            }

            if ($column === 'tanggal_pemeriksaan') {
                $value = $this->normalizeDate($value, $index);
            }

            $payload[$column] = $value;
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function extractColumnValue(string $column, array $row): mixed
    {
        if (array_key_exists($column, $row)) {
            return $row[$column];
        }

        foreach (self::COLUMN_ALIASES[$column] ?? [] as $alias) {
            if (array_key_exists($alias, $row)) {
                return $row[$alias];
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function resolveExistingRecord(?int $recordId, array $payload): ?Model
    {
        /** @var Model|null $existing */
        $existing = null;

        if ($recordId) {
            $existing = $this->modelClass::query()->find($recordId);
            if ($existing) {
                return $existing;
            }
        }

        foreach ($this->uniqueLookupColumns as $uniqueColumn) {
            $value = $payload[$uniqueColumn] ?? null;
            if ($value === null || $value === '') {
                continue;
            }

            $existing = $this->modelClass::query()
                ->where($uniqueColumn, $value)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $participantId = $payload['participant_id'] ?? null;
        $tanggalPemeriksaan = $payload['tanggal_pemeriksaan'] ?? null;

        if ($participantId && $tanggalPemeriksaan) {
            return $this->modelClass::query()
                ->where('participant_id', $participantId)
                ->whereDate('tanggal_pemeriksaan', $tanggalPemeriksaan)
                ->latest('id')
                ->first();
        }

        return null;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function ensureCreateRequirements(array $payload, int $index): void
    {
        $missing = [];
        foreach ($this->requiredForCreate as $column) {
            if (! isset($payload[$column]) || $payload[$column] === null || $payload[$column] === '') {
                $missing[] = $column;
            }
        }

        if ($missing === []) {
            return;
        }

        $list = implode(', ', $missing);
        throw new \RuntimeException("Baris {$index}: field wajib untuk {$this->checkLabel} belum lengkap ({$list}).");
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function normalizeDate(mixed $value, int $index): string
    {
        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }

            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Throwable $e) {
            throw new \RuntimeException("Baris {$index}: format tanggal tidak valid ({$value}).");
        }
    }

    private function firstNotNull(mixed ...$values): mixed
    {
        foreach ($values as $value) {
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function normalizeHeadingKey(string $key): string
    {
        return MedicalCheckTemplateSchema::normalizeHeading($key);
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $row
     */
    private function hydrateDoctorFields(array &$payload, array $row, int $index): void
    {
        if (! in_array('dokter_id', $this->columns, true)) {
            return;
        }

        $doctorInput = $this->firstNotNull(
            $payload['dokter_id'] ?? null,
            $row['dokter_id'] ?? null,
            $row['dokter_pemeriksa'] ?? null,
            $row['radiologist'] ?? null,
            $row['cardiologist'] ?? null,
            $row['penanggung_jawab'] ?? null,
        );

        if ($doctorInput === null || $doctorInput === '') {
            return;
        }

        $dokter = null;

        if (is_numeric($doctorInput)) {
            $dokter = Dokter::query()->find((int) $doctorInput);
        }

        if (! $dokter) {
            $doctorName = trim((string) $doctorInput);

            $dokter = Dokter::query()
                ->where('name', $doctorName)
                ->orWhere('name', 'like', $doctorName)
                ->first();
        }

        if (! $dokter) {
            throw new \RuntimeException("Baris {$index}: dokter '{$doctorInput}' tidak ditemukan.");
        }

        $payload['dokter_id'] = $dokter->id;

        foreach (['dokter_pemeriksa', 'radiologist', 'cardiologist', 'penanggung_jawab'] as $doctorNameColumn) {
            if (in_array($doctorNameColumn, $this->columns, true)) {
                $payload[$doctorNameColumn] = $dokter->name;
            }
        }

        if (in_array('tanda_tangan', $this->columns, true)) {
            $payload['tanda_tangan'] = $dokter->tanda_tangan;
        }
    }

    /**
     * @return array<string, string>
     */
    private function getHeadingAliasMap(): array
    {
        if ($this->headingAliases !== null) {
            return $this->headingAliases;
        }

        $this->headingAliases = MedicalCheckTemplateSchema::headingAliasMap($this->type);

        return $this->headingAliases;
    }
}
