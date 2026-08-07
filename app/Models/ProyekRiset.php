<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProyekRiset extends Model
{
    protected $fillable = [
        'satuan_id',
        'nama',
        'kategori',
        'progres',
        'status',
        'target_selesai',
    ];

    protected $casts = [
        'progres' => 'integer',
    ];

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    public function logUji(): HasMany
    {
        return $this->hasMany(LogUjiPengembangan::class);
    }

    /**
     * Warna badge status — dipetakan dari nilai "status" supaya admin cukup
     * pilih satu dropdown status tanpa perlu sinkron dua field manual.
     */
    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'Selesai' => 'ok',
            'Berjalan' => 'warn',
            default => 'ok',
        };
    }
}
