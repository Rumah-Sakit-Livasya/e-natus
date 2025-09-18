<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print RAB Closing - {{ $record->projectRequest->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        .table-print,
        .table-print th,
        .table-print td {
            border: 1px solid #e2e8f0;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .page {
                page-break-after: always;
            }

            .attachment-item {
                break-inside: avoid;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="fixed top-4 right-4 no-print">
        <button onclick="window.print()"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg transition duration-200">
            Cetak Halaman
        </button>
    </div>

    <!-- Page 1: Main Report -->
    <div class="page print-container max-w-4xl mx-auto my-8 bg-white p-8 shadow-lg">
        <header class="pb-4">
            <img src="{{ asset('img/kop.png') }}" alt="Kop Surat" class="w-full">
        </header>

        <div class="text-center my-6">
            <h3 class="text-xl font-bold underline">RENCANA ANGGARAN BIAYA (CLOSING)</h3>
        </div>

        <div class="grid grid-cols-3 gap-x-4 gap-y-2 text-sm mb-6">
            <div class="font-semibold">Lokasi</div>
            <div class="col-span-2">: {{ $record->projectRequest->lokasi }}</div>
            <div class="font-semibold">Perusahaan</div>
            <div class="col-span-2">: {{ $record->projectRequest->client->name }}</div>
            <div class="font-semibold">Tanggal MCU</div>
            <div class="col-span-2">: {{ $record->projectRequest->start_period->format('d') }} s/d
                {{ $record->projectRequest->end_period->format('d M Y') }}</div>
        </div>

        <div class="mb-6">
            <h4 class="font-bold bg-yellow-400 text-gray-800 px-3 py-1 mb-2">Operasional MCU :</h4>
            <table class="w-full text-sm table-print">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left w-12">No</th>
                        <th class="px-3 py-2 text-left">Description</th>
                        <th class="px-3 py-2 text-right w-48">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($record->operasionalItems as $index => $item)
                        <tr>
                            <td class="px-3 py-1 text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-1">{{ $item->description }}</td>
                            <td class="px-3 py-1 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mb-8">
            <h4 class="font-bold bg-yellow-400 text-gray-800 px-3 py-1 mb-2">FEE PETUGAS MCU :</h4>
            <table class="w-full text-sm table-print">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left w-12">No</th>
                        <th class="px-3 py-2 text-left">Description</th>
                        <th class="px-3 py-2 text-right w-48">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($record->feePetugasItems as $index => $item)
                        <tr>
                            <td class="px-3 py-1 text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-1">{{ $item->description }}</td>
                            <td class="px-3 py-1 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Tambahkan RAB BMHP di bawah Fee Petugas MCU -->
        <div class="mb-8">
            <h4 class="font-bold bg-yellow-400 text-gray-800 px-3 py-1 mb-2">RAB BMHP :</h4>
            <table class="w-full text-sm table-print">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 text-left w-12">No</th>
                        <th class="px-3 py-2 text-left">Description</th>
                        <th class="px-3 py-2 text-right w-48">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($record->bmhpItems as $index => $item)
                        <tr>
                            <td class="px-3 py-1 text-center">{{ $index + 1 }}</td>
                            <td class="px-3 py-1">{{ $item->name }}</td>
                            <td class="px-3 py-1 text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- End RAB BMHP -->

        <div class="flex justify-end">
            <div class="w-1/2">
                <table class="w-full text-sm">
                    <tbody>
                        <tr class="font-semibold">
                            <td class="px-3 py-1">Total</td>
                            <td class="px-3 py-1 text-right">Rp
                                {{ number_format($record->total_anggaran_closing, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-1">Nilai Invoice</td>
                            <td class="px-3 py-1 text-right">Rp
                                {{ number_format($record->nilai_invoice_closing, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="font-bold border-t-2 border-gray-400">
                            <td class="px-3 py-1">Margin</td>
                            <td class="px-3 py-1 text-right">Rp
                                {{ number_format($record->margin_closing, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Page 2: Justification -->
    <div class="page print-container max-w-4xl mx-auto my-8 bg-white p-8 shadow-lg">
        <header class="pb-4">
            <img src="{{ asset('img/kop.png') }}" alt="Kop Surat" class="w-full">
        </header>

        <div class="mt-8">
            <!-- DATA PESERTA -->
            <div class="mb-6">
                <h4 class="font-bold">Report Data Peserta MCU :</h4>
                <p>ESTIMASI PESERTA AWAL : {{ $record->jumlah_peserta_awal }} PESERTA</p>
                <p>PESERTA SETELAH CLOSED : {{ $record->jumlah_peserta_akhir }} PESERTA</p>
            </div>

            <!-- RAB AWAL -->
            <div class="mb-6">
                <h4 class="font-bold">RAB AWAL :</h4>
                <p>Total RAB, Rp. {{ number_format($record->total_anggaran, 0, ',', '.') }},-</p>
                <p>Nilai Invoice, Rp. {{ number_format($record->projectRequest->nilai_invoice, 0, ',', '.') }},-</p>
                @php
                    $marginAwal = $record->projectRequest->nilai_invoice - $record->total_anggaran;
                @endphp
                <p class="text-red-500">Margin, Rp. {{ number_format($marginAwal, 0, ',', '.') }},-</p>
            </div>

            <!-- DANA OPERASIONAL -->
            <div class="mb-8">
                <table class="w-1/2">
                    <tbody class="bg-blue-100">
                        <tr>
                            <td class="px-3 py-1">Dana Operasional di Transfer oleh Natus,</td>
                            <td class="px-3 py-1 font-semibold text-right">Rp</td>
                            <td class="px-3 py-1 font-semibold text-right">
                                {{ number_format($record->dana_operasional_transfer, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-1">Pengeluaran Operasional Closed</td>
                            <td class="px-3 py-1 font-semibold text-right">Rp</td>
                            <td class="px-3 py-1 font-semibold text-right">
                                {{ number_format($record->pengeluaran_operasional_closing, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="px-3 py-1 text-blue-600 font-semibold">Sisa Dana Operasional (Dana Operasional
                                Minus)</td>
                            <td class="px-3 py-1 font-semibold text-right text-red-500">-Rp</td>
                            <td class="px-3 py-1 font-semibold text-right text-red-500">
                                {{ number_format(abs($record->sisa_dana_operasional), 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- JUSTIFIKASI -->
            <div>
                <h4 class="font-bold text-lg mb-2">Justifikasi :</h4>
                <ol class="list-decimal list-inside space-y-2">
                    @if ($record->justifikasi)
                        @foreach (explode("\n", $record->justifikasi) as $line)
                            @if (trim($line))
                                <li>{{ $line }}</li>
                            @endif
                        @endforeach
                    @else
                        <li>Tidak ada justifikasi yang diberikan.</li>
                    @endif
                </ol>
            </div>
        </div>
    </div>

    <!-- Page 3: Attachments -->
    @php
        // Logika baru untuk mengambil SEMUA item yang memiliki lampiran
        // Kita filter berdasarkan apakah relasi 'attachments' memiliki data (count > 0)
        $operasionalItemsWithAttachments = $record->operasionalItems->filter(
            fn($item) => $item->attachments->isNotEmpty(),
        );
        $feeItemsWithAttachments = $record->feePetugasItems->filter(fn($item) => $item->attachments->isNotEmpty());
        $bmhpItemsWithAttachments = $record->bmhpItems->filter(fn($item) => $item->attachments->isNotEmpty());

        // Gabungkan ketiganya menjadi satu koleksi
        $allItemsWithAttachments = $operasionalItemsWithAttachments
            ->merge($feeItemsWithAttachments)
            ->merge($bmhpItemsWithAttachments);
    @endphp

    @if ($allItemsWithAttachments->isNotEmpty())
        <div class="page print-container max-w-4xl mx-auto my-8 bg-white p-8 shadow-lg">
            <header class="pb-4">
                <img src="{{ asset('img/kop.png') }}" alt="Kop Surat" class="w-full">
            </header>

            <div class="mt-12">
                <h3 class="text-xl font-bold underline text-center mb-8">LAMPIRAN BUKTI TRANSAKSI</h3>

                {{-- Loop melalui setiap ITEM yang memiliki lampiran --}}
                @foreach ($allItemsWithAttachments as $item)
                    <div class="mb-8 attachment-item">
                        <h4 class="font-semibold text-lg mb-2 bg-gray-100 p-2 rounded-t-lg">
                            Bukti untuk: {{ $item->description }}
                        </h4>

                        {{-- Di dalam setiap item, loop melalui SEMUA LAMPIRANNYA --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border p-4 rounded-b-lg">
                            @foreach ($item->attachments as $attachment)
                                <div class="bg-gray-50 p-2 rounded border">
                                    {{-- Gunakan $attachment->file_path untuk mendapatkan path file --}}
                                    <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                        alt="Bukti untuk {{ $item->description }}"
                                        class="w-full h-auto max-h-96 object-contain mx-auto">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</body>

</html>
