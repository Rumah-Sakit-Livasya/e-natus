<x-filament-panels::page>
    @php
        // 1. Ambil daftar karyawan yang ditugaskan ke proyek ini
        $assignedEmployees = $projectRequest->assignedEmployees()->with('user')->get();

        // 2. Buat rentang tanggal berdasarkan periode proyek
        $period = \Carbon\CarbonPeriod::create($projectRequest->start_period, $projectRequest->end_period);

        // 3. Ambil semua data absensi yang sudah ada untuk proyek ini agar efisien
        $attendances = $projectRequest->projectAttendances->keyBy(function ($item) {
            // Buat key unik: "employee_id-tanggal" untuk pencarian cepat
            return $item->employee_id . '-' . $item->tanggal;
        });

        // 4. Siapkan variabel untuk logika tombol "Absen Sekarang"
        $loggedInEmployeeId = auth()->user()->employee->id ?? null;
        $today = now()->toDateString();
        $hasAttendedToday = $loggedInEmployeeId ? isset($attendances[$loggedInEmployeeId . '-' . $today]) : false;
        $isWithinPeriod = now()->between($projectRequest->start_period, $projectRequest->end_period);
    @endphp

    {{-- Tombol Absen Sekarang --}}
    <div class="flex justify-end mb-4">
        @if ($isWithinPeriod && !$hasAttendedToday && $loggedInEmployeeId)
            <x-filament::button tag="a" :href="\App\Filament\Pages\CreateProjectAttendance::getUrl(['projectRequest' => $projectRequest->id])" icon="heroicon-o-camera">
                Absen Sekarang
            </x-filament::button>
        @elseif($hasAttendedToday)
            <span class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg inline-flex items-center">
                <x-heroicon-s-check-circle class="w-5 h-5 mr-2" />
                Anda Sudah Absen Hari Ini
            </span>
        @endif
    </div>

    {{-- Tabel Rekap Absensi --}}
    <div class="overflow-x-auto border border-gray-200 rounded-lg dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                        Nama Karyawan
                    </th>
                    @foreach ($period as $date)
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">
                            {{ $date->format('d M') }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                @forelse ($assignedEmployees as $employee)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $employee->user->name }}
                        </td>
                        @foreach ($period as $date)
                            @php
                                $dateString = $date->toDateString();
                                $key = $employee->id . '-' . $dateString;
                                $attendance = $attendances[$key] ?? null;
                            @endphp
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                @if ($attendance)
                                    {{-- Tombol untuk membuka modal detail --}}
                                    <button type="button" class="cursor-pointer"
                                        wire:click="mountAction('detail', { employeeId: {{ $employee->id }}, date: '{{ $dateString }}' })"
                                        title="Klik untuk melihat detail">
                                        <x-heroicon-s-check-circle class="h-6 w-6 text-green-500 mx-auto" />
                                    </button>
                                @else
                                    {{-- Ikon jika tidak absen --}}
                                    <span class="inline-block">
                                        <x-heroicon-s-x-circle class="h-6 w-6 text-red-600 mx-auto" />
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $period->count() + 1 }}" class="px-6 py-4 text-center text-sm text-gray-500">
                            Tidak ada karyawan yang ditugaskan untuk proyek ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Komponen ini WAJIB ada untuk merender modal --}}
    <x-filament-actions::modals />

</x-filament-panels::page>
