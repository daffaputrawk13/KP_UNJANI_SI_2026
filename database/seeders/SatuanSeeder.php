<?php

namespace Database\Seeders;

use App\Models\Satuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SatuanSeeder extends Seeder
{
    /**
     * Daftar satuan sesuai hasil rapat.
     * Setiap satuan hanya punya SATU akun (tidak ada pembagian Komandan/Piket
     * lagi) — satu orang memegang penuh satuannya, mulai dari input laporan
     * sampai verifikasi/teruskan laporan.
     */
    public function run(): void
    {
        $satuans = [
            // --- ADMIN (Pengelola sistem, bukan satuan operasional) ---
            ['kode' => 'ADMIN', 'nama' => 'Administrator Sistem', 'kategori' => Satuan::KATEGORI_ADMIN, 'deskripsi' => 'Kelola akun pengguna, satuan, dan permintaan reset password.', 'urutan' => 0],

            // --- SATLAK (Satuan Pelaksana) ---
            ['kode' => 'SATLAKAL',       'nama' => 'Satlakal (Penangkalan)',                  'kategori' => Satuan::KATEGORI_SATLAK,      'deskripsi' => 'Pemantauan & pemulihan (mis. website yang diserang).',   'urutan' => 10],
            ['kode' => 'SATLAKSIBERSOS', 'nama' => 'Satlak Sibersos',               'kategori' => Satuan::KATEGORI_SATLAK,      'deskripsi' => 'Pengelolaan media sosial di daerah.',                    'urutan' => 20],
            ['kode' => 'SATLAKRINDAK',   'nama' => 'Satlak Penindakan',             'kategori' => Satuan::KATEGORI_SATLAK,      'deskripsi' => 'Penanganan aksi cyber: malware, ransomware, serangan.', 'urutan' => 30],
            ['kode' => 'SATLAKBANGTEK',  'nama' => 'Satlok Duktek (Dukungan Teknologi)', 'kategori' => Satuan::KATEGORI_SATLAK,      'deskripsi' => 'Riset & pengembangan teknologi terkini (AI, drone, dll).', 'urutan' => 40],

            // --- DIR (Direktorat) ---
            ['kode' => 'BINFUNG', 'nama' => 'Binfung (Pembinaan Fungsi)',    'kategori' => Satuan::KATEGORI_DIREKTORAT, 'deskripsi' => 'Penempatan personel yang masuk.',                  'urutan' => 50],
            ['kode' => 'BINKUM',  'nama' => 'Binkum (Pembinaan Umum)',      'kategori' => Satuan::KATEGORI_DIREKTORAT, 'deskripsi' => 'Pengawasan satuan, lomba internal, personel baru.', 'urutan' => 60],
            ['kode' => 'DIKLAT',  'nama' => 'Diklat (Pendidikan & Latihan)', 'kategori' => Satuan::KATEGORI_DIREKTORAT, 'deskripsi' => 'Pendidikan dan latihan satuan.',                   'urutan' => 70],
            ['kode' => 'BINMAT',  'nama' => 'Binmat (Pembinaan Materiil)',  'kategori' => Satuan::KATEGORI_DIREKTORAT, 'deskripsi' => 'Pengurusan material/perlengkapan satuan.',         'urutan' => 80],

            // --- Koordinasi / Pimpinan ---
            ['kode' => 'SDIR',   'nama' => 'SDIR (Sekretaris Direktorat)',  'kategori' => Satuan::KATEGORI_PIMPINAN, 'deskripsi' => 'Koordinasi antar Satlak, pelaporan ke DANPUS.', 'urutan' => 90],
            ['kode' => 'WADAN',  'nama' => 'WADAN (Wakil Komandan)',       'kategori' => Satuan::KATEGORI_PIMPINAN, 'deskripsi' => 'Penerima laporan dari SDIR/Satlak.',            'urutan' => 100],
            ['kode' => 'DANPUS', 'nama' => 'DANPUS', 'kategori' => Satuan::KATEGORI_PIMPINAN, 'deskripsi' => 'Penerima laporan tertinggi dari seluruh satuan.', 'urutan' => 110],
        ];

        foreach ($satuans as $data) {
            $satuan = Satuan::updateOrCreate(['kode' => $data['kode']], $data);

            // Satu akun per satuan — memegang seluruh alur (input & verifikasi laporan).
            User::updateOrCreate(
                ['username' => strtolower($data['kode'])],
                [
                    'name' => $data['nama'],
                    'email' => strtolower($data['kode']).'@pussiberad.mil.id',
                    'password' => Hash::make('password'),
                    'satuan_id' => $satuan->id,
                    'jabatan' => null,
                ]
            );
        }
    }
}