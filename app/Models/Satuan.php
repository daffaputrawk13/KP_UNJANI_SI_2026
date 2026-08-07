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

    /**
     * Akun media sosial resmi yang dikelola satuan ini (mis. akun Instagram
     * resmi Satlak Sibersos) — dipakai fitur manajemen & posting konten.
     */
    public function akunMedsos(): HasMany
    {
        return $this->hasMany(AkunMedsos::class);
    }

    /**
     * Seluruh postingan media sosial yang dibuat oleh satuan ini.
     */
    public function postingan(): HasMany
    {
        return $this->hasMany(Postingan::class);
    }

    /**
     * Personel yang saat ini ditempatkan di satuan ini — dipakai fitur
     * Administrasi Personel (Binfung).
     */
    public function personels(): HasMany
    {
        return $this->hasMany(Personel::class);
    }
}
