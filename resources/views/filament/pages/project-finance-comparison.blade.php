{{-- Gunakan namespace filament:: untuk v2 --}}
<x-filament::page>

    @if ($project)
        {{-- Gunakan grid CSS dengan komponen filament::card --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- TAHAP 1: RENCANA ANGGARAN AWAL --}}
            <x-filament::card>
                <h2 class="text-lg font-semibold flex items-center space-x-2">
                    <x-heroicon-o-document-text class="w-6 h-6 text-gray-500" />
                    <span>{{ __('project.initial_plan') }}</span>
                </h2>
                <div class="space-y-2 mt-2">
                    <p class="text-sm text-gray-500">
                        {{ __('project.initial_plan_desc') }}
                    </p>
                    <div class="text-2xl font-bold tracking-tight">
                        Rp {{ number_format($this->totalAwal(), 0, ',', '.') }}
                    </div>
                </div>
            </x-filament::card>

            {{-- TAHAP 2: REALISASI ANGGARAN --}}
            <x-filament::card>
                <h2 class="text-lg font-semibold flex items-center space-x-2">
                    <x-heroicon-o-currency-dollar class="w-6 h-6 text-gray-500" />
                    <span>{{ __('project.realization') }}</span>
                </h2>
                <div class="space-y-2 mt-2">
                    <p class="text-sm text-gray-500">
                        {{ __('project.realization_desc') }}
                    </p>
                    <div class="text-2xl font-bold tracking-tight text-primary-600">
                        Rp {{ number_format($this->totalRealisasi(), 0, ',', '.') }}
                    </div>
                    <div @class([
                        'flex items-center gap-1 text-sm',
                        'text-success-600' => $this->selisihVsAwal() >= 0,
                        'text-danger-600' => $this->selisihVsAwal() < 0,
                    ])>
                        {{-- Di v2, gunakan komponen ikon langsung --}}
                        @if ($this->selisihVsAwal() >= 0)
                            <x-heroicon-s-arrow-trending-down class="h-4 w-4" />
                        @else
                            <x-heroicon-s-arrow-trending-up class="h-4 w-4" />
                        @endif
                        <span>{{ $this->selisihVsAwalDescription() }}</span>
                    </div>
                </div>
            </x-filament::card>

            {{-- TAHAP 3: CLOSING PROYEK --}}
            <x-filament::card>
                <h2 class="text-lg font-semibold flex items-center space-x-2">
                    <x-heroicon-o-check-circle class="w-6 h-6 text-gray-500" />
                    <span>{{ __('project.closing') }}</span>
                </h2>
                <div class="space-y-2 mt-2">
                    <p class="text-sm text-gray-500">
                        {{ __('project.closing_desc') }}
                    </p>
                    <div class="text-2xl font-bold tracking-tight">
                        Rp {{ number_format($this->totalClosing(), 0, ',', '.') }}
                    </div>
                    <div @class(['text-sm font-semibold', $this->selisihFinalColor()])>
                        {{ $this->selisihFinalDescription() }}
                    </div>
                </div>
            </x-filament::card>
        </div>

        {{-- BAGIAN DETAIL: TABEL PERBANDINGAN LENGKAP --}}
        {{-- Di v2, section biasanya menggunakan card dengan heading --}}
        <x-filament::card :heading="__('project.comparison_details')" class="mt-6">
            <x-filament-tables::table>
                <x-slot:header>
                    <x-filament-tables::header-cell>{{ __('project.item_description') }}</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell
                        class="text-right">{{ __('project.initial_budget') }}</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell
                        class="text-right">{{ __('project.final_budget') }}</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell
                        class="text-right">{{ __('project.realization_cost') }}</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell class="text-right">{{ __('project.difference') }} (Final vs
                        Realisasi)</x-filament-tables::header-cell>
                </x-slot:header>

                @forelse ($this->comparisonData() as $item)
                    @php
                        $itemSelisih = $item['closing_total'] - $item['realisasi_total'];
                    @endphp
                    <x-filament-tables::row>
                        <x-filament-tables::cell>{{ $item['description'] }}</x-filament-tables::cell>
                        <x-filament-tables::cell class="text-right">
                            {{ 'Rp ' . number_format($item['awal_total'], 0, ',', '.') }}
                        </x-filament-tables::cell>
                        <x-filament-tables::cell class="text-right">
                            {{ 'Rp ' . number_format($item['closing_total'], 0, ',', '.') }}
                        </x-filament-tables::cell>
                        <x-filament-tables::cell class="text-right text-primary-600 font-medium">
                            {{ 'Rp ' . number_format($item['realisasi_total'], 0, ',', '.') }}
                        </x-filament-tables::cell>
                        <x-filament-tables::cell @class([
                            'text-right font-semibold',
                            'text-success-600' => $itemSelisih >= 0,
                            'text-danger-600' => $itemSelisih < 0,
                        ])>
                            {{ ($itemSelisih < 0 ? '-' : '') . 'Rp ' . number_format(abs($itemSelisih), 0, ',', '.') }}
                        </x-filament-tables::cell>
                    </x-filament-tables::row>
                @empty
                    <x-filament-tables::row>
                        <x-filament-tables::cell :colspan="5">
                            {{-- Buat empty state manual untuk v2 --}}
                            <div class="flex flex-col items-center justify-center p-4 text-center">
                                <x-heroicon-o-x-circle class="w-12 h-12 text-gray-400 mb-2" />
                                <p class="font-semibold">{{ __('Tidak Ada Data Item') }}</p>
                            </div>
                        </x-filament-tables::cell>
                    </x-filament-tables::row>
                @endforelse
            </x-filament-tables::table>
        </x-filament::card>
    @else
        {{-- Buat empty state manual untuk v2 --}}
        <x-filament::card>
            <div class="flex flex-col items-center justify-center p-8 text-center">
                <x-heroicon-o-folder-open class="w-16 h-16 text-gray-400 mb-4" />
                <h3 class="text-lg font-semibold text-gray-700">{{ __('project.no_project_title') }}</h3>
                <p class="text-sm text-gray-500">{{ __('project.no_project_description') }}</p>
            </div>
        </x-filament::card>
    @endif
</x-filament::page>
