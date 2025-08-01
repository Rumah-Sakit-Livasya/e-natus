<x-filament-panels::page>
    <x-filament-panels::form wire:submit="generateReport">
        {{ $this->form }}
        <x-filament-panels::form.actions :actions="$this->getFormActions()" />
    </x-filament-panels::form>

    <div class="mt-6">
        @if (!empty($summary_stats))
            <div class="space-y-6">

                {{-- Widget Statistik (Sudah Benar) --}}
                @livewire(\App\Filament\Widgets\ReportStatsOverview::class, ['stats' => $summary_stats])

                {{--
                    ==========================================================
                    ===== PERBAIKAN UTAMA DIMULAI DARI SINI ==================
                    ==========================================================
                --}}
                <x-filament::section class="overflow-x-auto">
                    <table class="w-full text-sm text-left rtl:text-right">
                        {{-- Header Tabel --}}
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Nama Proyek</th>
                                <th class="px-6 py-3 w-40">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-white/5">
                            @forelse ($report_data->groupBy('date') as $date => $days)
                                {{-- Baris Header Tanggal yang sudah diperbaiki --}}
                                <tr class="bg-gray-50 dark:bg-white/5">
                                    <td colspan="2"
                                        class="px-6 py-2 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $date }}
                                    </td>
                                </tr>

                                {{-- Baris Rincian Proyek --}}
                                @foreach ($days as $day)
                                    <tr>
                                        <td
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $day['project_name'] }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($day['status'] !== 'Tidak Ada Penugasan')
                                                <x-filament::badge :color="match ($day['status']) {
                                                    'Hadir' => 'success',
                                                    'Tidak Hadir' => 'danger',
                                                }">
                                                    {{ $day['status'] }}
                                                </x-filament::badge>
                                            @else
                                                <span
                                                    class="text-xs text-gray-500 dark:text-gray-400">{{ $day['status'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="2" class="py-12 text-center">
                                        <x-filament-tables::empty-state heading="Tidak ada data penugasan"
                                            description="Tidak ada data penugasan untuk karyawan ini pada rentang tanggal yang dipilih." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </x-filament::section>
            </div>
        @else
            {{-- Tampilan Awal (Sudah Benar) --}}
            <x-filament::section class="flex flex-col items-center justify-center text-center">
                <div
                    class="flex items-center justify-center w-12 h-12 mb-4 text-primary-500 bg-primary-100 rounded-full dark:bg-gray-700">
                    <x-heroicon-o-chart-bar-square class="w-6 h-6" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Siap Membuat Laporan</h3>
                <p class="max-w-md mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Silakan pilih seorang karyawan dan tentukan rentang tanggal, lalu klik tombol "Buat Laporan" untuk
                    melihat rincian absensi.
                </p>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
