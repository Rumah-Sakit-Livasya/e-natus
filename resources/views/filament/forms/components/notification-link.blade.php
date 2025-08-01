{{-- File: resources/views/filament/forms/components/notification-link.blade.php --}}

@php
    // Variabel $url dikirim dari method ->viewData() di resource PHP
@endphp

@if ($url)
    <div class="fi-fo-placeholder">
        {{-- Ini untuk meniru tampilan label seperti Placeholder bawaan --}}
        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3" for="data.link">
            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                Link
            </span>
        </label>

        {{-- Ini adalah link yang bisa diklik --}}
        <div class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                class="text-primary-600 transition hover:underline hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400">
                Klik untuk membuka link terkait
            </a>
        </div>
    </div>
@endif
