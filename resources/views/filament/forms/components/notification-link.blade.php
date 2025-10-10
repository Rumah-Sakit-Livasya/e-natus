{{-- File:resources/views/filament/forms/components/notification-link.blade.php --}}

@php
    // Variabel $url dikirim dari method ->viewData() di resource PHP
@endphp

@if ($url)
    <div class="fi-fo-placeholder">
        {{-- Label mirip Placeholder --}}
        <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3" for="data.link">
            <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
                Link
            </span>
        </label>

        <div class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                class="text-primary-600 transition hover:underline hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400">
                Klik untuk membuka link terkait
            </a>
        </div>

        {{-- Tombol Approve & Batalkan (disabled) --}}
        <div class="mt-4 flex gap-2">
            <button type="button"
                class="inline-flex items-center px-3 py-1.5 rounded text-sm font-medium bg-green-400 text-white opacity-60 cursor-not-allowed"
                disabled>
                Approve
            </button>
            <button type="button"
                class="inline-flex items-center px-3 py-1.5 rounded text-sm font-medium bg-red-400 text-white opacity-60 cursor-not-allowed"
                disabled>
                Batalkan
            </button>
        </div>
    </div>
@endif
