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
            ['kode' => 'ADMIN', 'username' => 'admin', 'nama' => 'Administrator Sistem', 'kategori' => Satuan::KATEGORI_ADMIN, 'deskripsi' => 'Kelola akun pengguna, satuan, dan permintaan reset password.', 'urutan' => 0],

            // --- SATLAK (Satuan Pelaksana) ---
            ['kode' => 'SATLAKAL',       'username' => 'satlakkal',   'nama' => 'Satlakal (Penangkalan)',                  'kategori' => Satuan::KATEGORI_SATLAK,      'deskripsi' => 'Pemantauan & pemulihan (mis. website yang diserang).',   'urutan' => 10],
            ['kode' => 'SATLAKSIBERSOS', 'username' => 'satlaksibersos', 'nama' => 'Satlak Sibersos',               'kategori' => Satuan::KATEGORI_SATLAK,      'deskripsi' => 'Pengelolaan media sosial di daerah.',                    'urutan' => 20],
            ['kode' => 'SATLAKRINDAK',   'username' => 'satlakdak',   'nama' => 'Satlak Penindakan',             'kategori' => Satuan::KATEGORI_SATLAK,      'deskripsi' => 'Penanganan aksi cyber: malware, ransomware, serangan.', 'urutan' => 30],
            ['kode' => 'SATLAKBANGTEK',  'username' => 'satlakduktek', 'nama' => 'Satlok Duktek (Dukungan Teknologi)', 'kategori' => Satuan::KATEGORI_SATLAK,      'deskripsi' => 'Riset & pengembangan teknologi terkini (AI, drone, dll).', 'urutan' => 40],

            // --- DIR (Direktorat) ---
            ['kode' => 'BINFUNG', 'username' => 'binfung', 'nama' => 'Binfung (Pembinaan Fungsi)',    'kategori' => Satuan::KATEGORI_DIREKTORAT, 'deskripsi' => 'Penempatan personel yang masuk.',                  'urutan' => 50],
            ['kode' => 'BINKUM',  'username' => 'binum',  'nama' => 'Binkum (Pembinaan Umum)',      'kategori' => Satuan::KATEGORI_DIREKTORAT, 'deskripsi' => 'Pengawasan satuan, lomba internal, personel baru.', 'urutan' => 60],
            ['kode' => 'DIKLAT',  'username' => 'diklat', 'nama' => 'Diklat (Pendidikan & Latihan)', 'kategori' => Satuan::KATEGORI_DIREKTORAT, 'deskripsi' => 'Pendidikan dan latihan satuan.',                   'urutan' => 70],
            ['kode' => 'BINMAT',  'username' => 'binmat', 'nama' => 'Binmat (Pembinaan Materiil)',  'kategori' => Satuan::KATEGORI_DIREKTORAT, 'deskripsi' => 'Pengurusan material/perlengkapan satuan.',         'urutan' => 80],

            // --- Koordinasi / Pimpinan ---
            ['kode' => 'SDIR',   'username' => 'sdir',   'nama' => 'SDIR (Sekretaris Direktorat)',  'kategori' => Satuan::KATEGORI_PIMPINAN, 'deskripsi' => 'Koordinasi antar Satlak, pelaporan ke DANPUS.', 'urutan' => 90],
            ['kode' => 'WADAN',  'username' => 'wadan',  'nama' => 'WADAN (Wakil Komandan)',       'kategori' => Satuan::KATEGORI_PIMPINAN, 'deskripsi' => 'Penerima laporan dari SDIR/Satlak.',            'urutan' => 100],
            ['kode' => 'DANPUS', 'username' => 'danpus', 'nama' => 'DANPUS', 'kategori' => Satuan::KATEGORI_PIMPINAN, 'deskripsi' => 'Penerima laporan tertinggi dari seluruh satuan.', 'urutan' => 110],
        ];

        foreach ($satuans as $data) {
            $satuan = Satuan::updateOrCreate(
                ['kode' => $data['kode']],
                collect($data)->except('username')->all()
            );

            // Satu akun per satuan — memegang seluruh alur (input & verifikasi laporan).
            // Dicocokkan lewat satuan_id (bukan username) supaya perubahan username
            // tidak membuat akun baru/duplikat, melainkan meng-update akun yang sudah ada.
            User::updateOrCreate(
                ['satuan_id' => $satuan->id],
                [
                    'name' => $data['nama'],
                    'username' => $data['username'],
                    'email' => $data['username'].'@pussiberad.mil.id',
                    'password' => Hash::make('111'),
                    'jabatan' => null,
                ]
            );
        }
    }
}
