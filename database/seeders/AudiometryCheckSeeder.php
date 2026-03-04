<?php

namespace Database\Seeders;

use App\Models\AudiometryCheck;
use App\Models\Client;
use App\Models\Dokter;
use App\Models\Participant;
use App\Models\ProjectRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class AudiometryCheckSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();

        $client = Client::query()->first();
        if (!$client) {
            $client = Client::create([
                'name' => 'Client Demo Audiometri',
                'pic' => 'PIC Demo',
                'phone' => '081234567890',
                'email' => 'client.demo@example.com',
            ]);
        }

        $project = ProjectRequest::query()->first();
        if (!$project) {
            $project = ProjectRequest::create([
                'client_id' => $client->id,
                'user_id' => $user?->id,
                'name' => 'Project Demo Audiometri',
                'jumlah' => 1,
                'lokasi' => 'Jakarta',
                'status' => 'approved',
                'start_period' => now()->subDays(7)->toDateString(),
                'end_period' => now()->addDays(7)->toDateString(),
                'nilai_invoice' => 1000000,
                'due_date' => now()->addDays(30)->toDateString(),
                'status_pembayaran' => 'unpaid',
                'keterangan' => 'Seeder data demo audiometri',
            ]);
        }

        $participant = Participant::query()
            ->where('project_request_id', $project->id)
            ->first();

        if (!$participant) {
            $participant = Participant::create([
                'project_request_id' => $project->id,
                'name' => 'AGUS LATEST',
                'employee_code' => '691637',
                'department' => 'THT',
                'date_of_birth' => '1970-08-17',
                'address' => 'Jakarta',
                'gender' => 'Laki-laki',
                'marital_status' => 'Menikah',
                'note' => 'Seeder peserta untuk hasil audiometri',
            ]);
        }

        $dokter = Dokter::query()->first();
        if (!$dokter) {
            $dokter = Dokter::create([
                'name' => 'dr. Irma Suryati',
                'spesialisasi' => 'Spesialis THT',
                'is_active' => true,
            ]);
        }

        AudiometryCheck::updateOrCreate(
            ['no_rm' => '691637'],
            [
                'participant_id' => $participant->id,
                'dokter_id' => $dokter->id,
                'instansi' => 'PT. IPC TPK (Panjang)',
                'tanggal_pemeriksaan' => now()->toDateString(),
                'ad_ac_250' => 40,
                'ad_ac_500' => 40,
                'ad_ac_1000' => 40,
                'ad_ac_2000' => 30,
                'ad_ac_3000' => 30,
                'ad_ac_4000' => 35,
                'ad_ac_6000' => 35,
                'ad_ac_8000' => 30,
                'ad_bc_250' => 5,
                'ad_bc_500' => 5,
                'ad_bc_1000' => 5,
                'ad_bc_2000' => 10,
                'ad_bc_3000' => 10,
                'ad_bc_4000' => 10,
                'ad_bc_6000' => null,
                'ad_bc_8000' => null,
                'as_ac_250' => 35,
                'as_ac_500' => 35,
                'as_ac_1000' => 35,
                'as_ac_2000' => 35,
                'as_ac_3000' => 30,
                'as_ac_4000' => 30,
                'as_ac_6000' => 35,
                'as_ac_8000' => 35,
                'as_bc_250' => 5,
                'as_bc_500' => 5,
                'as_bc_1000' => 5,
                'as_bc_2000' => 5,
                'as_bc_3000' => 10,
                'as_bc_4000' => 10,
                'as_bc_6000' => null,
                'as_bc_8000' => null,
                'derajat_ad' => '36,25 dB',
                'derajat_as' => '33,75 dB',
                'kesimpulan' => 'Tuli konduktif derajat ringan di kedua telinga',
                'saran' => 'Timpanometri',
            ]
        );
    }
}
