@php
    $user = auth()->user();

    // Ambil 5 notifikasi terbaru yang belum dibaca
    $unreadNotifications = $user->notifications()->whereNull('read_at')->latest()->take(5)->get();
    $countUnread = $unreadNotifications->count();
@endphp

<div x-data="{ open: false }" class="relative inline-block text-left" @style('margin-right: 10rem;')>
    {{-- Tombol Notifikasi --}}
    <button @click="open = !open" @class([
        'relative inline-flex items-center px-3 py-1 rounded-md shadow-sm text-sm font-medium',
        'border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500' => !config(
            'filament.dark_mode'),
        'border-gray-700 text-gray-300' => config('filament.dark_mode'),
    ]) :aria-expanded="open.toString()"
        aria-controls="notification-menu">
        <x-heroicon-o-bell @class([
            'w-5 h-5',
            'text-gray-700' => !config('filament.dark_mode'),
            'text-gray-300' => config('filament.dark_mode'),
        ]) />
        @if ($countUnread > 0)
            <span
                class="-top-1 -right-1 inline-flex items-center justify-center py-0.5 rounded-full text-xs font-bold bg-danger-600">
                {{ $countUnread }}
            </span>
        @endif
    </button>

    {{-- Panel Dropdown --}}
    <div x-show="open" x-transition @click.outside="open = false" id="notification-menu" @style('width: 14rem;')
        class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50">

        {{-- Daftar Notifikasi --}}
        <div class="py-2 max-h-72 overflow-y-auto">
            @forelse ($unreadNotifications as $notification)
                @php
                    $data = $notification->data;
                    $message = $data['message'] ?? 'Notifikasi baru';
                    $url = $data['url'] ?? route('notifications.index');
                @endphp
                <a href="{{ $url }}"
                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700"
                    @click="open = false">
                    {{ $message }}
                    <br>
                    <small class="text-gray-400 dark:text-gray-500">
                        {{ $notification->created_at->diffForHumans() }}
                    </small>
                </a>
            @empty
                <p class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">Tidak ada notifikasi baru.</p>
            @endforelse
        </div>

        {{-- Link ke semua notifikasi --}}
        <div class="border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('notifications.index') }}"
                class="block text-center text-sm text-primary-600 dark:text-primary-400 hover:underline px-4 py-2">
                Lihat semua notifikasi
            </a>
        </div>
    </div>
</div>
