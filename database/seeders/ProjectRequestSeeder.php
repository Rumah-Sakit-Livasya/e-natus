<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Employee;
use App\Models\ProjectRequest;
use App\Models\RabOperasionalItem;
use App\Models\User;
use App\Models\Aset;
use Illuminate\Database\Seeder;

class ProjectRequestSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get existing data
        $clients = Client::all();
        $users = User::all();
        $employees = Employee::all();
        $assets = Aset::all();

        if ($clients->isEmpty() || $users->isEmpty()) {
            $this->command->error('Please seed Clients and Users first!');
            return;
        }

        $projectTypes = [
            'MCU Karyawan',
            'MCU Pre-Employment',
            'MCU Berkala',
            'Pemeriksaan Kesehatan Khusus',
            'Medical Check Up Executive',
            'Screening Kesehatan',
        ];

        $locations = [
            'Jakarta Pusat',
            'Jakarta Selatan',
            'Jakarta Barat',
            'Bandung',
            'Surabaya',
            'Semarang',
            'Yogyakarta',
            'Bekasi',
            'Tangerang',
            'Depok',
        ];

        $operationalItems = [
            ['name' => 'Biaya Transportasi Tim', 'unit' => 'Trip', 'price_range' => [500000, 2000000]],
            ['name' => 'Konsumsi Tim', 'unit' => 'Paket', 'price_range' => [50000, 150000]],
            ['name' => 'Akomodasi', 'unit' => 'Malam', 'price_range' => [300000, 1000000]],
            ['name' => 'Bahan Habis Pakai Medis', 'unit' => 'Paket', 'price_range' => [100000, 500000]],
            ['name' => 'Formulir & Dokumentasi', 'unit' => 'Set', 'price_range' => [25000, 100000]],
            ['name' => 'Equipment Rental', 'unit' => 'Unit', 'price_range' => [500000, 3000000]],
        ];

        $this->command->info('Creating Project Requests...');

        // Create 15 project requests with varying statuses
        for ($i = 1; $i <= 15; $i++) {
            $client = $clients->random();
            $user = $users->random();
            $startDate = now()->addDays(rand(7, 60));
            $endDate = $startDate->copy()->addDays(rand(1, 7));
            $dueDate = $startDate->copy()->subDays(7); // Due date 1 week before start
            $jumlahPeserta = rand(20, 200);

            // Random number of employees (2-5)
            $selectedEmployees = $employees->random(rand(2, min(5, $employees->count())));
            
            // Random number of assets (1-3)
            $selectedAssets = $assets->isEmpty() ? [] : $assets->random(rand(1, min(3, $assets->count())));

            // Generate realistic invoice value
            $nilaiInvoice = $jumlahPeserta * rand(300000, 800000);

            // Determine approval status based on index
            $approvalStatus = $this->getApprovalStatus($i);

            $projectRequest = ProjectRequest::create([
                'client_id' => $client->id,
                'user_id' => $user->id,
                'sdm_ids' => $selectedEmployees->pluck('id')->toArray(),
                'employee_ids' => $selectedEmployees->pluck('id')->toArray(),
                'asset_ids' => $selectedAssets instanceof \Illuminate\Support\Collection ? $selectedAssets->pluck('id')->toArray() : [],
                'name' => $projectTypes[array_rand($projectTypes)] . ' - ' . $client->name,
                'jumlah' => $jumlahPeserta,
                'lokasi' => $locations[array_rand($locations)],
                'status' => $approvalStatus['status'],
                'start_period' => $startDate,
                'end_period' => $endDate,
                'keterangan' => 'Project ' . $projectTypes[array_rand($projectTypes)] . ' untuk ' . $jumlahPeserta . ' peserta di ' . $locations[array_rand($locations)],
                'nilai_invoice' => $nilaiInvoice,
                'due_date' => $dueDate,
                'status_pembayaran' => rand(0, 10) > 7 ? 'paid' : 'unpaid',
                'approval_level_1_status' => $approvalStatus['level1_status'],
                'approval_level_2_status' => $approvalStatus['level2_status'],
                'approval_level_1_by' => $approvalStatus['level1_by'],
                'approval_level_2_by' => $approvalStatus['level2_by'],
                'approval_level_1_at' => $approvalStatus['level1_at'],
                'approval_level_2_at' => $approvalStatus['level2_at'],
                'approval_level_1_notes' => $approvalStatus['level1_notes'],
                'approval_level_2_notes' => $approvalStatus['level2_notes'],
            ]);

            // Create RAB Operasional Items (3-6 items per project)
            $rabItemsCount = rand(3, 6);
            for ($j = 0; $j < $rabItemsCount; $j++) {
                $item = $operationalItems[array_rand($operationalItems)];
                $quantity = rand(1, min(10, $jumlahPeserta));
                $price = rand($item['price_range'][0], $item['price_range'][1]);
                
                RabOperasionalItem::create([
                    'project_request_id' => $projectRequest->id,
                    'description' => $item['name'] . ' untuk ' . $quantity . ' ' . $item['unit'],
                    'qty_aset' => $quantity,
                    'harga_sewa' => $price,
                    'total' => $quantity * $price,
                ]);
            }

            $this->command->info("Created: {$projectRequest->code} - {$projectRequest->name} ({$approvalStatus['status']})");
        }

        $this->command->info('✅ Successfully created 15 Project Requests with RAB items!');
    }

    /**
     * Get approval status based on index to create variety
     */
    private function getApprovalStatus(int $index): array
    {
        $users = User::all();
        $approver1 = $users->random();
        $approver2 = $users->random();

        // Create variety: some pending, some approved, some rejected
        if ($index <= 5) {
            // Pending Level 1
            return [
                'status' => 'pending',
                'level1_status' => 'pending',
                'level2_status' => null,
                'level1_by' => null,
                'level2_by' => null,
                'level1_at' => null,
                'level2_at' => null,
                'level1_notes' => null,
                'level2_notes' => null,
            ];
        } elseif ($index <= 10) {
            // Approved Level 1, Pending Level 2
            return [
                'status' => 'pending',
                'level1_status' => 'approved',
                'level2_status' => 'pending',
                'level1_by' => $approver1->id,
                'level2_by' => null,
                'level1_at' => now()->subDays(rand(1, 5)),
                'level2_at' => null,
                'level1_notes' => 'Disetujui untuk proses selanjutnya',
                'level2_notes' => null,
            ];
        } elseif ($index <= 13) {
            // Fully Approved
            return [
                'status' => 'approved',
                'level1_status' => 'approved',
                'level2_status' => 'approved',
                'level1_by' => $approver1->id,
                'level2_by' => $approver2->id,
                'level1_at' => now()->subDays(rand(5, 10)),
                'level2_at' => now()->subDays(rand(1, 4)),
                'level1_notes' => 'Disetujui untuk proses selanjutnya',
                'level2_notes' => 'Project dapat dilaksanakan',
            ];
        } else {
            // Rejected
            $level = rand(1, 2);
            if ($level === 1) {
                return [
                    'status' => 'rejected',
                    'level1_status' => 'rejected',
                    'level2_status' => null,
                    'level1_by' => $approver1->id,
                    'level2_by' => null,
                    'level1_at' => now()->subDays(rand(1, 3)),
                    'level2_at' => null,
                    'level1_notes' => 'Perlu revisi anggaran dan timeline',
                    'level2_notes' => null,
                ];
            } else {
                return [
                    'status' => 'rejected',
                    'level1_status' => 'approved',
                    'level2_status' => 'rejected',
                    'level1_by' => $approver1->id,
                    'level2_by' => $approver2->id,
                    'level1_at' => now()->subDays(rand(3, 7)),
                    'level2_at' => now()->subDays(rand(1, 2)),
                    'level1_notes' => 'Disetujui untuk proses selanjutnya',
                    'level2_notes' => 'Budget melebihi alokasi yang tersedia',
                ];
            }
        }
    }
}
