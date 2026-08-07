<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonelMutasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'personel_id',
        'satuan_asal_id',
        'satuan_tujuan_id',
        'jabatan_asal_id',
        'jabatan_tujuan_id',
        'nomor_sk',
        'tanggal_mutasi',
        'keterangan',
        'status',
        'diajukan_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mutasi' => 'date',
        ];
    }

    public const STATUS_MENUNGGU = 'Menunggu SK';
    public const STATUS_DISETUJUI = 'Disetujui';
    public const STATUS_DITOLAK = 'Ditolak';

    public function personel(): BelongsTo
    {
        return $this->belongsTo(Personel::class);
    }

    public function satuanAsal(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_asal_id');
    }

    public function satuanTujuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuan_tujuan_id');
    }

    public function jabatanAsal(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_asal_id');
    }

    public function jabatanTujuan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_tujuan_id');
    }

    public function diajukanOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }
}
