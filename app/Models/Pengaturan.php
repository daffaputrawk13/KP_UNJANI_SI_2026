<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = [
        'nama_instansi',
        'singkatan',
        'logo_path',
        'alamat',
        'email_kontak',
        'telepon_kontak',
        'hero_eyebrow',
        'hero_judul_awal',
        'hero_judul_aksen',
        'hero_subjudul',
        'hero_deskripsi',
        'hero_image_path',
        'fitur',
        'tentang_deskripsi',
        'tentang_moto_judul',
        'tentang_moto_deskripsi',
        'website',
        'sosial_media',
    ];

    protected $casts = [
        'fitur' => 'array',
        'sosial_media' => 'array',
    ];

    /**
     * Ambil baris pengaturan (dibuat otomatis kalau belum ada) — dipakai
     * di seluruh aplikasi untuk menampilkan nama instansi & logo.
     */
    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'nama_instansi' => 'Pusat Siber dan Sandi Angkatan Darat',
            'singkatan' => 'Pussiberad',
        ]);
    }
}
