<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Personel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nrp',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'pangkat_id',
        'jabatan_id',
        'satuan_id',
        'status',
        'tanggal_masuk',
        'no_hp',
        'alamat',
        'foto_path',
        'catatan',
        'dicatat_oleh',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tanggal_masuk' => 'date',
        ];
    }

    public const STATUS_AKTIF = 'Aktif';
    public const STATUS_MUTASI = 'Mutasi';
    public const STATUS_PURNA = 'Purna';

    public function pangkat(): BelongsTo
    {
        return $this->belongsTo(Pangkat::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function mutasis(): HasMany
    {
        return $this->hasMany(PersonelMutasi::class);
    }

    public function dokumens(): HasMany
    {
        return $this->hasMany(PersonelDokumen::class);
    }
}
