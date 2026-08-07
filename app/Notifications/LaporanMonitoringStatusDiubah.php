<?php

namespace App\Notifications;

use App\Models\LaporanMonitoring;
use Illuminate\Notifications\Notification;

/**
 * Dikirim ke pembuat laporan (Satlakal) setiap kali DANPUS memutuskan
 * laporan monitoring & recovery-nya: Disetujui, Ditolak, atau Direvisi.
 */
class LaporanMonitoringStatusDiubah extends Notification
{
    public function __construct(public LaporanMonitoring $laporanMonitoring)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $status = $this->laporanMonitoring->status;

        $pesan = match ($status) {
            'Disetujui' => "Laporan \"{$this->laporanMonitoring->perihal}\" telah disetujui DANPUS.",
            'Ditolak' => "Laporan \"{$this->laporanMonitoring->perihal}\" ditolak DANPUS.",
            'Direvisi' => "Laporan \"{$this->laporanMonitoring->perihal}\" perlu direvisi. Silakan periksa catatan DANPUS.",
            default => "Status laporan \"{$this->laporanMonitoring->perihal}\" diperbarui menjadi {$status}.",
        };

        return [
            'laporan_monitoring_id' => $this->laporanMonitoring->id,
            'status' => $status,
            'perihal' => $this->laporanMonitoring->perihal,
            'pesan' => $pesan,
        ];
    }
}
