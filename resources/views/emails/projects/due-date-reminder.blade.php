{{-- resources/views/emails/projects/due-date-reminder-html.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengingat Pembayaran</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f7fafc; font-family: Arial, sans-serif;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
        style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;">
        <!-- Header -->
        <tr>
            <td align="center"
                style="padding: 20px 24px; background-color: #2c5282; color: #ffffff; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                <h1 style="margin: 0; font-size: 24px;">Pengingat Pembayaran</h1>
            </td>
        </tr>

        <!-- Main Content -->
        <tr>
            <td style="padding: 30px 24px;">
                <p style="margin-top: 0; color: #2d3748; font-size: 16px;">
                    Yth. Bapak/Ibu <strong>{{ $project->client->name }}</strong>,
                </p>
                <p style="color: #4a5568; line-height: 1.5;">
                    Semoga Anda dalam keadaan baik. Melalui email ini, kami ingin mengingatkan dengan hormat mengenai
                    tagihan untuk proyek Anda yang akan segera jatuh tempo.
                </p>

                <!-- Details Box -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%"
                    style="margin: 24px 0; border: 1px solid #e2e8f0; border-radius: 5px;">
                    <tr>
                        <td style="padding: 20px;">
                            <h3 style="margin-top: 0; color: #2d3748;">Rincian Tagihan</h3>
                            <p style="margin: 16px 0;"><strong>Proyek:</strong> {{ $project->name }}</p>
                            <p style="margin: 16px 0;"><strong>Nilai Tagihan:</strong> <span
                                    style="font-size: 18px; font-weight: bold; color: #2c5282;">Rp
                                    {{ number_format($project->nilai_invoice, 0, ',', '.') }}</span></p>
                            <p style="margin: 16px 0;"><strong>Jatuh Tempo:</strong> <span
                                    style="font-weight: bold; color: #c53030;">{{ \Carbon\Carbon::parse($project->due_date)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Payment Information -->
                <h3 style="color: #2d3748; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">Informasi Pembayaran
                </h3>
                <p style="margin-top: 16px; color: #4a5568;">
                    Pembayaran dapat dilakukan melalui transfer ke rekening berikut:
                </p>
                <div style="padding: 15px; background-color: #f7fafc; border-radius: 5px; margin-top: 10px;">
                    <p style="margin: 8px 0;"><strong>Bank:</strong> [Nama Bank Anda, misal: Bank Mandiri]</p>
                    <p style="margin: 8px 0;"><strong>Nomor Rekening:</strong> [Nomor Rekening Anda]</p>
                    <p style="margin: 8px 0;"><strong>Atas Nama:</strong> [Nama Pemilik Rekening]</p>
                </div>

                <p style="margin-top: 30px; color: #4a5568; line-height: 1.5;">
                    Apabila Anda telah melakukan pembayaran, mohon abaikan pesan ini. Jika ada pertanyaan, jangan ragu
                    untuk menghubungi kami.
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td align="center" style="padding: 20px 24px; background-color: #edf2f7;">
                <p style="margin: 0; color: #718096; font-size: 14px;">
                    Terima kasih atas perhatian dan kerja sama Anda.
                </p>
                <p style="margin: 10px 0 0 0; color: #a0aec0; font-size: 12px;">
                    <strong>{{ config('app.name') }}</strong><br>
                    [Alamat Perusahaan Anda]<br>
                    [Nomor Telepon Anda]
                </p>
            </td>
        </tr>
    </table>
</body>

</html>
