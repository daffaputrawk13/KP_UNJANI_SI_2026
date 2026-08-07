<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanPublikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'satuan_id',
        'user_id',
        'tujuan_satuan_id',
        'judul',
        'platform',
        'link_publikasi',
        'deskripsi',
        'status',
        'tanggal_kirim',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kirim' => 'datetime',
        ];
    }

    /**
     * Satuan asal pembuat laporan publikasi (Satuan Pelaksanaan Siber Sosial).
     */
    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_id');
    }

    /**
     * Satuan tujuan laporan (DANPUS).
     */
    public function tujuanSatuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'tujuan_satuan_id');
    }

    /**
     * Pengguna yang membuat laporan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Dokumentasi (foto/video/PDF bukti) yang dilampirkan ke laporan ini.
     */
    public function dokumentasi(): HasMany
    {
        return $this->hasMany(LaporanPublikasiDokumen::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'Draft';
    }
}
