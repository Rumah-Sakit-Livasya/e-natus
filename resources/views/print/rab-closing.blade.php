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

    <div class="print-container max-w-4xl mx-auto my-8 bg-white p-8 shadow-lg">
        <!-- HEADER -->
        <header class="flex justify-between items-center pb-4 border-b-4 border-orange-500">
            <div>
                {{-- Ganti dengan logo Anda jika ada --}}
                <h1 class="text-4xl font-bold text-gray-800">NVM</h1>
                <p class="text-sm text-gray-500">NATUS VINCERE MEDIKA</p>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-bold text-gray-700">Terpercaya & Utama</h2>
            </div>
        </header>

        <!-- JUDUL DOKUMEN -->
        <div class="text-center my-6">
            <h3 class="text-xl font-bold underline">RENCANA ANGGARAN BIAYA (CLOSING)</h3>
        </div>

        <!-- INFORMASI UMUM -->
        <div class="grid grid-cols-3 gap-x-4 gap-y-2 text-sm mb-6">
            <div class="font-semibold">Lokasi</div>
            <div class="col-span-2">: {{ $record->projectRequest->lokasi }}</div>
            <div class="font-semibold">Perusahaan</div>
            <div class="col-span-2">: {{ $record->projectRequest->client->name }}</div>
            <div class="font-semibold">Tanggal MCU</div>
            <div class="col-span-2">: {{ $record->projectRequest->start_period->format('d') }} s/d
                {{ $record->projectRequest->end_period->format('d M Y') }}</div>
        </div>

        <!-- TABEL OPERASIONAL MCU -->
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

        <!-- TABEL FEE PETUGAS MCU -->
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

        <!-- TOTAL -->
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

</body>

</html>
