<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanMonitoringLampiran extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_monitoring_id',
        'nama_file',
        'path',
        'tipe',
        'diunggah_oleh',
    ];

    public function laporanMonitoring(): BelongsTo
    {
        return $this->belongsTo(LaporanMonitoring::class);
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}
