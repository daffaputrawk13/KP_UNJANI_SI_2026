<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogUjiPengembangan extends Model
{
    protected $fillable = [
        'satuan_id',
        'proyek_riset_id',
        'kegiatan',
        'hasil',
        'status',
        'waktu_uji',
    ];

    protected $casts = [
        'waktu_uji' => 'datetime',
    ];

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    public function proyekRiset(): BelongsTo
    {
        return $this->belongsTo(ProyekRiset::class);
    }

    public function getStatusClassAttribute(): string
    {
        return $this->status === 'Selesai' ? 'green' : 'amber';
    }
}
