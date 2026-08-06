<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Postingan extends Model
{
    use HasFactory;

    protected $fillable = [
        'akun_medsos_id',
        'satuan_id',
        'user_id',
        'judul',
        'caption',
        'media_path',
        'media_type',
        'jenis_konten',
        'status',
        'scheduled_at',
        'published_at',
        'likes',
        'komentar',
        'share',
        'dilihat',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function akunMedsos(): BelongsTo
    {
        return $this->belongsTo(AkunMedsos::class, 'akun_medsos_id');
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    /**
     * Pengguna yang membuat/mengunggah postingan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Total interaksi (like + komentar + share) — dipakai untuk statistik
     * performa & pengurutan postingan paling engaging.
     */
    public function getTotalEngagementAttribute(): int
    {
        return $this->likes + $this->komentar + $this->share;
    }
}
