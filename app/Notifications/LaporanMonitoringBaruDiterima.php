<?php

namespace App\Notifications;

use App\Models\LaporanMonitoring;
use Illuminate\Notifications\Notification;

class LaporanMonitoringBaruDiterima extends Notification
{
    public function __construct(public LaporanMonitoring $laporanMonitoring)
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
            'laporan_monitoring_id' => $this->laporanMonitoring->id,
            'satuan_asal' => $this->laporanMonitoring->satuan->nama,
            'perihal' => $this->laporanMonitoring->perihal,
            'prioritas' => $this->laporanMonitoring->prioritas,
            'pesan' => "Laporan monitoring & recovery baru dari {$this->laporanMonitoring->satuan->nama}: {$this->laporanMonitoring->perihal}",
        ];
    }
}
