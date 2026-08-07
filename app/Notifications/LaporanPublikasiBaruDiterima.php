<?php

namespace App\Notifications;

use App\Models\LaporanPublikasi;
use Illuminate\Notifications\Notification;

class LaporanPublikasiBaruDiterima extends Notification
{
    public function __construct(public LaporanPublikasi $laporanPublikasi)
    {
    }

    /**
     * Hanya lewat channel database — belum ada integrasi email/broadcast.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'laporan_publikasi_id' => $this->laporanPublikasi->id,
            'satuan_asal' => $this->laporanPublikasi->satuan->nama,
            'judul' => $this->laporanPublikasi->judul,
            'pesan' => "Laporan publikasi baru dari {$this->laporanPublikasi->satuan->nama}: {$this->laporanPublikasi->judul}",
        ];
    }
}
