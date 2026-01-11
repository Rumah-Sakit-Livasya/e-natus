@php
    static $lastProcurementId = null;
    $currentProcurementId = $record->procurementItem->procurement->id ?? null;
@endphp

@if ($currentProcurementId !== $lastProcurementId)
    @php $lastProcurementId = $currentProcurementId; @endphp
    <tr class="bg-gray-100 border-t border-b">
        <td colspan="100%" class="px-4 py-2 text-sm font-semibold text-gray-700">
            Pengadaan: {{ $record->procurementItem->procurement->tanggal_pengajuan->format('d M Y') }}
            — {{ $record->procurementItem->procurement->keterangan }}
        </td>
    </tr>
@endif
