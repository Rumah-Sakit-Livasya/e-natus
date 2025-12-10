<div @if ($visible) ax-load="visible"
    @else
        ax-load @endif
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('database-notifications', 'filament/notifications') }}"
    x-data="databaseNotifications({
        arePollingEnabled: {{ \Filament\Support\Js::from($polling) }},
        isLazy: {{ \Filament\Support\Js::from($lazy) }},
        pollingInterval: '{{ $pollingInterval }}',
        trigger: $refs.trigger,
    })" class="fi-database-notifications">
    {{-- Ini adalah tombol lonceng pemicu notifikasi --}}
    <div x-ref="trigger">
        {{ $trigger }}
    </div>

    {{-- Ini adalah modal/dropdown yang muncul saat lonceng diklik --}}
    <div x-ref="modal" x-cloak x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="fi-modal fixed inset-0 z-40 flex items-start justify-center overflow-y-auto" role="dialog"
        aria-modal="true" wire:ignore.self>
        {{-- Latar belakang gelap --}}
        <div x-on:click="close" class="fi-modal-close-overlay fixed inset-0 bg-gray-950/50 dark:bg-gray-950/75"
            aria-hidden="true"></div>

        {{-- Panel Konten Modal --}}
        <div
            class="fi-modal-panel relative flex w-full flex-col h-full overflow-y-auto sm:h-auto sm:max-w-md sm:rounded-xl sm:my-8">
            {{-- Header Modal --}}
            <div class="fi-modal-header px-6 pt-6">
                <div class="flex items-center justify-between">
                    <h2 class="fi-modal-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        {{ __('filament-notifications::database.modal.heading') }}
                    </h2>

                    <x-filament::icon-button color="gray" icon="heroicon-o-x-mark" icon-alias="modal.close-button"
                        icon-size="lg" x-on:click="close" :label="__('filament-notifications::database.modal.buttons.close.label')" class="-m-1.5" />
                </div>
            </div>

            {{-- Body Modal (Konten Utama) --}}
            <div class="fi-modal-body flex-1 overflow-y-auto p-6">
                {{-- Tombol Aksi Massal (Tandai semua & Hapus semua) --}}
                @if ($this->hasUnreadNotifications())
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <x-filament::button color="gray" wire:click="markAllAsRead" wire:loading.attr="disabled"
                            wire:target="markAllAsRead">
                            {{ __('filament-notifications::database.modal.buttons.mark_all_as_read.label') }}
                        </x-filament::button>

                        <x-filament::link color="gray" wire:click="clear" wire:loading.attr="disabled"
                            wire:target="clear">
                            {{ __('filament-notifications::database.modal.buttons.clear.label') }}
                        </x-filament::link>
                    </div>
                @endif

                {{-- Daftar Notifikasi --}}
                <div @class([
                    '-mx-6 -my-6',
                    'divide-y divide-gray-200 dark:divide-white/10' => $this->hasNotifications(),
                ])>
                    @forelse ($this->getNotifications() as $notification)
                        {{-- ======================================================= --}}
                        {{-- ===== INI ADALAH BAGIAN UTAMA YANG DIKUSTOMISASI ===== --}}
                        {{-- ======================================================= --}}
                        <div class="fi-database-notifications-item relative flex gap-4 px-6 py-4">

                            {{-- KOLOM 1: Ikon Status (Mirip IconColumn) --}}
                            <div class="mt-0.5">
                                @if ($notification->read_at)
                                    <x-heroicon-o-check-circle class="h-6 w-6 text-green-500" />
                                @else
                                    <x-heroicon-o-bell-alert class="h-6 w-6 text-yellow-500" />
                                @endif
                            </div>

                            {{-- KOLOM 2: Konten Notifikasi (Pesan & Waktu) --}}
                            <div class="grid flex-1">
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $notification->data['message'] ?? 'Notifikasi tidak memiliki pesan.' }}
                                </p>
                                <time class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </time>
                            </div>

                            {{-- KOLOM 3: Tombol Aksi "Tandai Dibaca" --}}
                            @if ($notification->unread())
                                <x-filament::icon-button icon="heroicon-o-check-circle" color="gray"
                                    tooltip="Tandai sudah dibaca"
                                    wire:click="markNotificationAsRead('{{ $notification->id }}')"
                                    wire:loading.attr="disabled"
                                    wire:target="markNotificationAsRead('{{ $notification->id }}')" />
                            @endif
                        </div>
                    @empty
                        {{-- Tampilan jika tidak ada notifikasi --}}
                        <div class="fi-database-notifications-empty-state px-6 py-12">
                            <div class="grid justify-items-center gap-y-4">
                                <div
                                    class="fi-database-notifications-empty-state-icon-ctn text-gray-400 dark:text-gray-500">
                                    <x-filament::icon icon="heroicon-o-bell-slash" class="h-12 w-12" />
                                </div>
                                <h3
                                    class="fi-database-notifications-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                    {{ __('filament-notifications::database.modal.empty.heading') }}
                                </h3>
                                <p
                                    class="fi-database-notifications-empty-state-description text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('filament-notifications::database.modal.empty.description') }}
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ======================================================= --}}
            {{-- =====   INTEGRASI DENGAN NOTIFICATION RESOURCE   ====== --}}
            {{-- ======================================================= --}}
            @if ($this->hasNotifications())
                <div class="fi-modal-footer w-full border-t border-gray-200 dark:border-white/10 p-4">
                    <x-filament::link :href="App\Filament\Resources\NotificationResource::getUrl()" color="primary" class="w-full text-center">
                        Lihat Semua Notifikasi
                    </x-filament::link>
                </div>
            @endif

        </div>
    </div>
</div>
