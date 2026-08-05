<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Satuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'deskripsi',
        'urutan',
    ];

    /**
     * Kategori satuan yang tersedia, dipakai untuk pengelompokan di dropdown login.
     */
    public const KATEGORI_SATLAK = 'satlak';
    public const KATEGORI_DIREKTORAT = 'direktorat';
    public const KATEGORI_PIMPINAN = 'pimpinan';
    public const KATEGORI_ADMIN = 'admin';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Laporan yang dikirim oleh satuan ini (mis. laporan dari Satlok Duktek).
     */
    public function laporanTerkirim(): HasMany
    {
        return $this->hasMany(Laporan::class, 'satuan_id');
    }

    /**
     * Laporan yang ditujukan ke satuan ini (mis. laporan masuk ke DANPUS).
     */
    public function laporanDiterima(): HasMany
    {
        return $this->hasMany(Laporan::class, 'tujuan_satuan_id');
    }
}
