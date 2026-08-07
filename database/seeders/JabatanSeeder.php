<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            ['nama' => 'Piket', 'deskripsi' => 'Petugas piket harian satuan'],
            ['nama' => 'Staf', 'deskripsi' => 'Staf pelaksana umum'],
            ['nama' => 'Staf Riset', 'deskripsi' => 'Staf riset dan pengembangan teknologi'],
            ['nama' => 'Operator', 'deskripsi' => 'Operator sistem/monitoring'],
            ['nama' => 'Kepala Satuan', 'deskripsi' => 'Pimpinan satuan pelaksana'],
            ['nama' => 'Administrasi', 'deskripsi' => 'Pengelola administrasi satuan'],
        ];

        foreach ($jabatans as $j) {
            Jabatan::updateOrCreate(['nama' => $j['nama']], $j);
        }
    }
}
