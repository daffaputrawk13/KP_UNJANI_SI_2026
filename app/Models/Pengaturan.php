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
