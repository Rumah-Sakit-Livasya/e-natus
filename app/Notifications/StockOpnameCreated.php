<?php

namespace App\Notifications;

// Pastikan hanya resource yang relevan yang di-import
use App\Filament\Resources\BmhpStockOpnameResource;
use App\Models\BmhpStockOpname;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Ganti nama class agar sesuai dengan isinya (opsional tapi direkomendasikan)
// class BmhpStockOpnameCreated extends Notification
class StockOpnameCreated extends Notification
{
    use Queueable;

    /**
     * Buat instance notifikasi baru.
     *
     * @param \App\Models\BmhpStockOpname $BmhpStockOpname
     */
    public function __construct(public BmhpStockOpname $BmhpStockOpname)
    {
        // Constructor sudah benar
    }

    /**
     * Dapatkan channel pengiriman notifikasi.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Dapatkan representasi email dari notifikasi.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Stock Opname BMHP Baru Telah Dibuat')
            // PERBAIKAN DI SINI: Gunakan $this->BmhpStockOpname
            ->line("Sebuah stock opname BMHP baru ({$this->BmhpStockOpname->document_number}) telah dibuat dan membutuhkan perhatian Anda.")
            // PERBAIKAN DI SINI: Gunakan $this->BmhpStockOpname
            ->action('Lihat Daftar Stock Opname', BmhpStockOpnameResource::getUrl('index'))
            ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Dapatkan representasi database dari notifikasi.
     */
    public function toDatabase($notifiable): DatabaseMessage
    {
        $bmhpName = $this->BmhpStockOpname->bmhp?->name ?? '-';
        $stokFisik = $this->BmhpStockOpname->stok_fisik;
        return new DatabaseMessage([
            'title' => 'Stock Opname BMHP Baru',
            'message' => "Stock opname BMHP untuk {$bmhpName} telah dibuat dengan kuantitas {$stokFisik}.",
            'url' => BmhpStockOpnameResource::getUrl('index', ['record' => $this->BmhpStockOpname]),
        ]);
    }

    /**
     * Dapatkan representasi array dari notifikasi.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
