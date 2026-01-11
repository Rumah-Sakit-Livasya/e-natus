<div class="flex items-center gap-x-4">
    @if (filament()->auth()->check())
        {{-- Bell Icon with Badge - Links to Notifications Page --}}
        @php
            $unreadCount = auth()->user()->unreadNotifications()->count();
        @endphp

        <a href="{{ \App\Filament\Resources\NotificationResource::getUrl('index') }}"
            class="relative shrink-0 flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition"
            title="Lihat Notifikasi">
            <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                </path>
            </svg>

            @if ($unreadCount > 0)
                <span
                    class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </a>
    @endif

    {{-- User Menu --}}
    @if (filament()->auth()->check())
        <x-filament-panels::user-menu />
    @endif
</div>
