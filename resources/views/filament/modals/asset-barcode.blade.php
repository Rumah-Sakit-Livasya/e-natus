<div class="p-6 text-center">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $asset->custom_name ?? $asset->template->name }}</h3>
        <p class="text-sm text-gray-600">Kode Aset: <strong>{{ $asset->code }}</strong></p>
        @if($asset->serial_number)
            <p class="text-sm text-gray-600">Serial Number: {{ $asset->serial_number }}</p>
        @endif
    </div>
    
    <!-- Barcode using online generator (fallback) -->
    <div class="flex justify-center mb-4">
        <img src="https://barcode.tec-it.com/barcode.ashx?data={{ urlencode($asset->code) }}&code=Code128&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=96&imagetype=Gif&rotation=0&color=%23000000&bgcolor=%23ffffff&qunit=Mm&quiet=0" 
             alt="Barcode {{ $asset->code }}" 
             class="border border-gray-300 rounded"
             onerror="this.style.display='none'; document.getElementById('fallback-barcode').style.display='block';">
    </div>
    
    <!-- Fallback barcode using CSS -->
    <div id="fallback-barcode" class="hidden">
        <div class="inline-block p-4 border-2 border-black">
            <div class="text-2xl font-mono font-bold">{{ $asset->code }}</div>
            <div class="text-xs mt-1">{{ $asset->code }}</div>
        </div>
    </div>
    
    <div class="mt-4 text-xs text-gray-500">
        <p>Scan barcode ini untuk identifikasi aset</p>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
