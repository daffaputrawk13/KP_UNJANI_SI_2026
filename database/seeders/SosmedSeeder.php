<?php

namespace Database\Seeders;

use App\Models\AkunMedsos;
use App\Models\Postingan;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Database\Seeder;

class SosmedSeeder extends Seeder
{
    /**
     * Data contoh untuk fitur manajemen akun & posting media sosial di
     * dashboard Satlak Sibersos, supaya tab Manajemen Akun, Kalender
     * Konten, Monitoring Engagement, Statistik Performa, dan Arsip Posting
     * tidak kosong saat pertama kali dicoba.
     */
    public function run(): void
    {
        $satuan = Satuan::where('kode', 'SATLAKSISOS')->first();
        if (! $satuan) {
            return; // Jalankan SatuanSeeder dulu.
        }

        $user = User::where('satuan_id', $satuan->id)->first();

        $akunInstagram = AkunMedsos::updateOrCreate(
            ['satuan_id' => $satuan->id, 'username_platform' => '@satlaksibersos'],
            [
                'nama_akun' => 'Instagram Resmi Satlak Sibersos',
                'platform' => 'Instagram',
                'url_profil' => 'https://instagram.com/satlaksibersos',
                'status' => 'Aktif',
            ]
        );

        $akunTiktok = AkunMedsos::updateOrCreate(
            ['satuan_id' => $satuan->id, 'username_platform' => '@sibersos.update'],
            [
                'nama_akun' => 'TikTok Edukasi Sibersos',
                'platform' => 'TikTok',
                'url_profil' => 'https://tiktok.com/@sibersos.update',
                'status' => 'Aktif',
            ]
        );

        if (! $user) {
            return;
        }

        $postingan = [
            [
                'akun_medsos_id' => $akunInstagram->id,
                'judul' => 'Edukasi Anti-Hoaks Minggu Ini',
                'caption' => 'Kenali ciri-ciri berita hoaks sebelum ikut menyebarkan. Cek fakta dulu sebelum share!',
                'jenis_konten' => 'Feed',
                'status' => 'Terbit',
                'published_at' => now()->subDays(2),
                'likes' => 482, 'komentar' => 37, 'share' => 64, 'dilihat' => 5210,
            ],
            [
                'akun_medsos_id' => $akunTiktok->id,
                'judul' => 'Tips Aman Bermedia Sosial',
                'caption' => 'Jangan asal klik link mencurigakan ya! Ini tips singkat biar akunmu tetap aman.',
                'jenis_konten' => 'Reels/Video',
                'status' => 'Terbit',
                'published_at' => now()->subDays(5),
                'likes' => 1290, 'komentar' => 88, 'share' => 210, 'dilihat' => 18400,
            ],
            [
                'akun_medsos_id' => $akunInstagram->id,
                'judul' => 'Klarifikasi Isu Rekrutmen Palsu',
                'caption' => 'Klarifikasi resmi terkait maraknya penipuan rekrutmen yang mengatasnamakan TNI AD.',
                'jenis_konten' => 'Feed',
                'status' => 'Terjadwal',
                'scheduled_at' => now()->addDays(2)->setTime(9, 0),
            ],
            [
                'akun_medsos_id' => $akunTiktok->id,
                'judul' => 'Behind the Scene Latihan Gabungan',
                'caption' => 'Cuplikan suasana latihan gabungan minggu ini.',
                'jenis_konten' => 'Reels/Video',
                'status' => 'Terjadwal',
                'scheduled_at' => now()->addDays(4)->setTime(16, 30),
            ],
            [
                'akun_medsos_id' => $akunInstagram->id,
                'judul' => 'Rancangan Konten HUT Satuan',
                'caption' => 'Draft caption ucapan HUT — masih perlu direview sebelum dijadwalkan.',
                'jenis_konten' => 'Carousel',
                'status' => 'Draft',
            ],
        ];

        foreach ($postingan as $p) {
            Postingan::updateOrCreate(
                ['satuan_id' => $satuan->id, 'judul' => $p['judul']],
                array_merge($p, [
                    'satuan_id' => $satuan->id,
                    'user_id' => $user->id,
                    'likes' => $p['likes'] ?? 0,
                    'komentar' => $p['komentar'] ?? 0,
                    'share' => $p['share'] ?? 0,
                    'dilihat' => $p['dilihat'] ?? 0,
                ])
            );
        }
    }
}
