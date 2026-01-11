<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $project->code }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {

            html,
            body {
                height: 100%;
                margin: 0 !important;
                padding: 0 !important;
                box-sizing: border-box;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                width: 210mm;
                height: 297mm;
                max-width: 210mm;
                max-height: 297mm;
                min-height: 297mm;
                min-width: 210mm;
                font-size: 12px;
                padding: 0 !important;
                margin: 0 !important;
            }

            .page {
                width: 190mm;
                min-height: 277mm;
                max-height: 277mm;
                margin: 10mm auto 0 auto;
                padding: 0;
                box-sizing: border-box;
            }

            .avoid-break {
                page-break-inside: avoid;
            }
        }

        html,
        body {
            background: #fff !important;
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
            setTimeout(function() {
                window.close();
            }, 100);
        }
    </script>
</head>

<body class="bg-white text-gray-800 font-sans p-0 m-0">
    <div class="page mx-auto" style="width:190mm; min-height:277mm; max-width:190mm; max-height:277mm;">
        <!-- HEADER -->
        <div class="flex justify-between items-center pb-2 avoid-break">
            <img src="{{ asset('img/kop.png') }}" alt="Kop Surat" style=" height: auto; display: block;">
        </div>

        <!-- INVOICE DETAILS -->
        <div class="flex justify-between mt-4 avoid-break">
            <div>
                <h3 class="font-bold text-gray-600 text-xs">Ditagihkan Kepada:</h3>
                <p class="font-bold text-base">{{ $project->client->name ?? 'N/A' }}</p>
                <p class="text-xs">{{ $project->lokasi }}</p>
            </div>
            <div class="text-right">
                <h1 class="text-2xl font-bold">INVOICE</h1>
                <p class="text-gray-600 text-xs">{{ $project->code }}</p>
                <div class="mt-1">
                    <span class="text-gray-600 text-xs">Tanggal:</span>
                    <span class="font-bold text-xs">{{ now()->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- BODY PARAGRAPH -->
        <div class="mt-6 avoid-break">
            <p class="font-bold text-xs">Dengan Hormat,</p>
            <p class="mt-1 text-xs">
                Dengan ini kami bermaksud memberikan Tagihan atas Pelaksanaan {{ $project->name }}
                yang dilaksanakan pada tanggal {{ $project->start_period->translatedFormat('d F Y') }}, dengan rincian
                berikut:
            </p>
        </div>

        <!-- ITEMS TABLE -->
        <div class="mt-4 avoid-break">
            <table class="w-full text-left border border-gray-300 text-xs">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="p-2 w-8 text-center">No</th>
                        <th class="p-2">Description</th>
                        <th class="p-2 text-center w-12">Qty</th>
                        <th class="p-2 text-center w-16">Unit</th>
                        <th class="p-2 text-right w-24">Price</th>
                        <th class="p-2 text-right w-24">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200">
                        <td class="p-2 text-center">1</td>
                        <td class="p-2">{{ $project->name }}</td>
                        <td class="p-2 text-center">1</td>
                        <td class="p-2 text-center">Layanan</td>
                        <td class="p-2 text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        <td class="p-2 text-right font-bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- TOTALS -->
        <div class="flex justify-end mt-2 avoid-break">
            <div class="w-2/3 max-w-xs">
                <table class="w-full text-xs">
                    <tbody>
                        <tr>
                            <td class="py-1 pr-2 text-gray-600">Total</td>
                            <td class="py-1 text-right font-bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                        <!-- =================== PERUBAHAN DIMULAI DI SINI =================== -->
                        @if ($ppn > 0)
                            <tr>
                                <td class="py-1 pr-2 text-gray-600">
                                    PPN {{ rtrim(rtrim(number_format($ppnPercentage, 2, ',', ''), '0'), ',') }} %
                                </td>
                                <td class="py-1 text-right font-bold">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        <!-- =================== PERUBAHAN SELESAI DI SINI =================== -->
                        <tr class="border-t-2 border-black">
                            <td class="pt-1 pr-2 font-bold text-base">Grand Total</td>
                            <td class="pt-1 text-right font-bold text-base">Rp
                                {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TERBILANG -->
        <div class="mt-2 text-right italic font-semibold border-t border-b py-1 text-xs avoid-break">
            Terbilang : {{ ucwords(\App\Helpers\Terbilang::make($grandTotal)) }} Rupiah
        </div>

        <!-- NOTES & PAYMENT -->
        <div class="mt-4 avoid-break">
            <h4 class="font-bold text-xs">Note :</h4>
            <p class="text-xs">1. Pembayaran : {{ $paymentDuration }} Hari Kalender dari Invoice di Terima</p>
            <p class="mt-2 text-xs">Mohon dilakukan pembayaran ke Nomor Rekening berikut, terima kasih.</p>
            <div class="mt-1 border p-2 inline-block text-xs">
                <p><span class="font-bold">A/N :</span> Natus Vincere Medika</p>
                <p><span class="font-bold">Bank :</span> BNI</p>
                <p><span class="font-bold">No. :</span> 2110466788</p>
            </div>
        </div>

        <!-- SIGNATURE -->
        <div class="mt-12 flex justify-end avoid-break">
            <div class="text-center text-xs">
                <p>Hormat Kami,</p>
                <p>PT Natus Vincere Medika</p>
                <div style="height: 60px;"><!-- Space for signature --></div>
                <p class="font-bold underline">Wendy Savilla, S.M.</p>
                <p>Finance</p>
            </div>
        </div>
    </div>
</body>

</html>
