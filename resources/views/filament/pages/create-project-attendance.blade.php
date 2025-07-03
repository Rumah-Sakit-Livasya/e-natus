<x-filament-panels::page>
    <form wire:submit="create">
        {{-- Ini akan merender semua field dari metode form(), termasuk ViewField --}}
        {{ $this->form }}

        {{-- Ini akan merender tombol "Kirim Absen" dari metode getFormActions() --}}
        <x-filament-panels::form.actions :actions="$this->getFormActions()" />
    </form>
</x-filament-panels::page>
