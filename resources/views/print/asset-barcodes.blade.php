<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Barcode Aset</title>
    <!-- Filament Style Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        @page {
            margin: 0;
            size: 40mm 30mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
            margin: 0;
            padding: 24px;
            background-color: #f9fafb;
            /* Gray 50 - Filament background */
            color: #111827;
            /* Gray 900 */
            min-height: 100vh;
        }

        .page {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* --- Header Section --- */
        .preview-header {
            margin-bottom: 24px;
        }

        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .title-block h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
            letter-spacing: -0.025em;
        }

        .title-block .subtitle {
            margin-top: 4px;
            font-size: 14px;
            color: #6b7280;
            /* Gray 500 */
        }

        /* --- Stats Cards --- */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .info-card {
            background: #ffffff;
            padding: 16px 20px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            /* Gray 200 */
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            transition: box-shadow 0.2s;
        }

        .info-card .label {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .info-card .value {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
        }

        /* --- Print Button --- */
        .print-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            background-color: #4f46e5;
            /* Indigo 600 - Filament Primary */
            color: #ffffff;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            transition: background-color 0.2s;
        }

        .print-button:hover {
            background-color: #4338ca;
            /* Indigo 700 */
        }

        /* --- Barcode Grid --- */
        .barcode-grid {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 32px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        }

        .grid-header {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 24px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f3f4f6;
        }

        /* --- Barcode Item Layout --- */
        .barcode-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10mm;
            justify-content: flex-start;
        }

        .barcode-item {
            width: 40mm;
            height: 30mm;
            padding: 3mm;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background-color: #ffffff;
            page-break-inside: avoid;
        }

        .asset-name {
            font-weight: 700;
            font-size: 7pt;
            color: #111827;
            text-align: center;
            line-height: 1.2;
            word-break: break-word;
            flex-shrink: 0;
            height: 8mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barcode-image {
            width: 100%;
            height: 14mm;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1mm 0;
            overflow: hidden;
        }

        .barcode-image svg {
            width: 100% !important;
            height: 100% !important;
            max-width: 36mm;
        }

        .asset-code {
            font-size: 8pt;
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            color: #374151;
            background-color: #f3f4f6;
            padding: 1mm 4px;
            border-radius: 4px;
            width: 100%;
            flex-shrink: 0;
        }

        .fallback-barcode {
            display: none;
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            .page {
                max-width: 100%;
                margin: 0;
            }

            .barcode-grid {
                border: none;
                box-shadow: none;
                padding: 0;
                border-radius: 0;
            }

            .barcode-container {
                gap: 0;
                display: block;
            }

            .barcode-item {
                width: 40mm;
                height: 30mm;
                margin: 0;
                border: none;
                border-radius: 0;
                page-break-after: always;
            }

            .asset-code {
                background: transparent;
                border: none;
            }
        }
    </style>

    <script>
        window.onload = function() {
            const params = new URLSearchParams(window.location.search);
            if (params.get('autoprint') === '1') {
                window.print();
                setTimeout(() => window.close(), 700);
            }
        };
    </script>
</head>

<body>
    <div class="page">
        <!-- Dashboard Header Style -->
        <header class="preview-header no-print">
            <div class="header-row">
                <div class="title-block">
                    <h1>Cetak Barcode Aset</h1>
                    <div class="subtitle">Pratinjau label sebelum dicetak ke printer barcode.</div>
                </div>

                <button class="print-button" onclick="window.print()">
                    <svg style="width: 18px; height: 18px; margin-right: 8px;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak Sekarang
                </button>
            </div>

            <!-- Dashboard Stats Style -->
            <div class="info-cards">
                <div class="info-card">
                    <div class="label">Total Item</div>
                    <div class="value">{{ $assets->count() }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Tanggal</div>
                    <div class="value">{{ date('d M Y') }}</div>
                </div>
                <div class="info-card">
                    <div class="label">Peringatan</div>
                    <div class="value" style="font-size: 14px; font-weight: 500;">Sistem menggunakan generator lokal.
                    </div>
                </div>
            </div>
        </header>

        <main class="barcode-grid">
            <div class="grid-header no-print">
                Preview Barcode (Ukuran 40mm x 30mm)
            </div>

            <div class="barcode-container">
                @foreach ($assets as $asset)
                    <div class="barcode-item">
                        <div class="asset-name">
                            {{ Str::limit($asset->custom_name ?? $asset->template->name, 40) }}
                        </div>

                        <div class="barcode-image">
                            {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeSVG($asset->code, 'C128', 1.2, 45) !!}
                        </div>

                        <div class="asset-code">{{ $asset->code }}</div>
                    </div>
                @endforeach
            </div>
            <div style="clear: both;"></div>
        </main>
    </div>
</body>

</html>
