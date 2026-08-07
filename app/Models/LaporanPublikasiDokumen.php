<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanPublikasiDokumen extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_publikasi_id',
        'nama_file',
        'path',
        'tipe',
        'diunggah_oleh',
    ];

    public function laporanPublikasi(): BelongsTo
    {
        return $this->belongsTo(LaporanPublikasi::class);
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}
