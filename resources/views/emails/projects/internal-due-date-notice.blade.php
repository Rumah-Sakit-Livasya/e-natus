{{-- resources/views/emails/projects/internal-due-date-notice-html.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Pembayaran Proyek</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f4f7; font-family: Arial, sans-serif;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;">
        <!-- Header -->
        <tr>
            <td align="center"
                style="padding: 20px 24px; background-color: #4A5568; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                <h1 style="margin: 0; font-size: 24px; color:#e2e8f0; text-align: center;">Pemberitahuan Internal</h1>
            </td>
        </tr>

        <!-- Main Content -->
        <tr>
            <td style="padding: 30px 24px;">
                <h2 style="margin-top: 0; color: #2d3748; font-size: 20px;">Pembayaran Proyek Memerlukan Tindak Lanjut
                </h2>
                <p style="margin: 16px 0; color: #4a5568; line-height: 1.5;">
                    Halo Tim, sistem telah mendeteksi bahwa pembayaran untuk proyek di bawah ini belum lunas dan
                    memerlukan tindakan segera dari Anda.
                </p>

                <!-- Status Alert Box -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="margin: 24px 0; border: 1px solid #f6e05e; background-color: #fffbeb; border-radius: 5px;">
                    <tr>
                        <td style="padding: 16px;">
                            <strong style="font-size: 16px; color: #975a16;">Status: {{ $daysRemaining }}</strong>
                        </td>
                    </tr>
                </table>

                <!-- Details Table -->
                <h3 style="color: #2d3748; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">Rincian Proyek &
                    Klien</h3>
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="margin-top: 16px; color: #4a5568;">
                    <tr style="background-color: #f7fafc;">
                        <td style="padding: 10px; width: 35%;"><strong>Nama Proyek</strong></td>
                        <td style="padding: 10px;">{{ $project->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px;"><strong>Tanggal Jatuh Tempo</strong></td>
                        <td style="padding: 10px;">
                            {{ \Carbon\Carbon::parse($project->due_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        </td>
                    </tr>
                    <tr style="background-color: #f7fafc;">
                        <td style="padding: 10px;"><strong>Nilai Invoice</strong></td>
                        <td style="padding: 10px;">Rp {{ number_format($project->nilai_invoice, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px;"><strong>Status Pembayaran</strong></td>
                        <td style="padding: 10px;"><strong
                                style="color: #c53030;">{{ ucwords($project->status_pembayaran) }}</strong></td>
                    </tr>
                    <tr style="background-color: #f7fafc;">
                        <td style="padding: 10px;"><strong>Klien</strong></td>
                        <td style="padding: 10px;">{{ $project->client->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px;"><strong>PIC Klien</strong></td>
                        <td style="padding: 10px;">{{ $project->client->pic ?? '-' }}</td>
                    </tr>
                    <tr style="background-color: #f7fafc;">
                        <td style="padding: 10px;"><strong>Kontak Klien</strong></td>
                        <td style="padding: 10px;">
                            @if ($project->client->phone)
                                @php
                                    $phone = $project->client->phone;
                                    // Convert 08 to 628 if needed
                                    if (substr($phone, 0, 2) === '08') {
                                        $phone = '62' . substr($phone, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $phone }}"
                                    target="_blank">{{ $project->client->phone }}</a>
                            @else
                                {{ $project->client->email ?? '-' }}
                            @endif
                        </td>
                    </tr>
                </table>

                <!-- Call to Action -->
                <p style="margin-top: 30px; color: #4a5568; line-height: 1.5;">
                    Mohon segera hubungi klien terkait untuk mengonfirmasi status pembayaran. Gunakan tombol di bawah
                    untuk mengakses detail proyek di sistem.
                </p>
                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td align="center" style="padding: 20px 0;">
                            <a href="{{ $projectUrl }}"
                                style="background-color: #38a169; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                Buka Detail Proyek
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td align="center" style="padding: 20px 24px; background-color: #edf2f7;">
                <p style="margin: 0; color: #718096; font-size: 12px; text-align: center;">
                    Ini adalah email yang dibuat secara otomatis oleh Sistem {{ config('app.name') }}.
                </p>
                <p style="margin: 4px 0 0 0; color: #a0aec0; font-size: 12px; text-align: center;">
                    © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
