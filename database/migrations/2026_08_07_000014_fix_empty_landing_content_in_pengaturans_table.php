<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migration 2026_08_07_000013 hanya mengisi default konten landing page
     * untuk baris `pengaturans` yang SUDAH ADA saat itu dijalankan. Kalau di
     * suatu environment baris pengaturan baru dibuat SETELAHNYA (mis. lewat
     * Pengaturan::current() saat pertama kali buka dashboard, sebelum
     * perbaikan default di model), field-field landing page-nya jadi kosong.
     * Migration ini menambal ulang baris manapun yang masih kosong.
     */
    public function up(): void
    {
        DB::table('pengaturans')
            ->whereNull('hero_judul_awal')
            ->update([
                'hero_eyebrow' => 'PUSSIBERAD // SISTEM PENDUKUNG OPERASIONAL',
                'hero_judul_awal' => 'SIBER',
                'hero_judul_aksen' => 'AD',
                'hero_subjudul' => 'Sistem Informasi Berbasis Elektronik Angkatan Darat',
                'hero_deskripsi' => 'Mendigitalisasi alur pelaporan kegiatan seluruh Satuan Pelaksana Pusat Siber Angkatan Darat — dari input laporan di lapangan, verifikasi berjenjang, hingga visualisasi real-time bagi pengambil keputusan.',
                'fitur' => json_encode([
                    ['judul' => 'Real-time', 'deskripsi' => 'Laporan dan status pekerjaan dapat dipantau secara langsung tanpa menunggu rekap manual.'],
                    ['judul' => 'Terpusat', 'deskripsi' => 'Seluruh data laporan dan dokumen pendukung tersimpan dalam satu sistem yang mudah diakses.'],
                    ['judul' => 'Efisien', 'deskripsi' => 'Alur persetujuan berjenjang menjadi lebih cepat dan mengurangi proses administrasi berulang.'],
                    ['judul' => 'Aman & Terkontrol', 'deskripsi' => 'Data terjaga dengan sistem cadangan dan hak akses yang diatur sesuai kebutuhan pengguna.'],
                ]),
                'tentang_deskripsi' => "Pussiberad bukan sebuah perusahaan komersial, melainkan satuan resmi di bawah TNI Angkatan Darat yang dibentuk untuk menyelenggarakan pembinaan personel serta fungsi sandi dan siber dalam rangka membantu tugas TNI-AD. Satuan ini bernama Pusat Siber Angkatan Darat (Pussiberad), sebelumnya bernama Pusat Sandi dan Siber TNI Angkatan Darat (Pussansiad).\n\nPembentukan satuan ini merupakan hasil pengembangan Organisasi dan Tugas (Orgas) baru di lingkungan TNI-AD, sesuai Peraturan KASAD Nomor 26 Tahun 2019 tanggal 26 Desember 2019 tentang Organisasi dan Tugas Markas Besar TNI Angkatan Darat, Bab IV Tugas dan Tanggung Jawab, Pasal 35 Pussansiad.",
                'tentang_moto_judul' => 'Satria Yudha Waskita',
                'tentang_moto_deskripsi' => 'Semboyan resmi Pussansiad/Pussiberad ini diambil dari bahasa Sanskerta/Jawa Kuno, yang secara harfiah berarti "prajurit perang yang ahli, bijaksana, dan waspada" — menggambarkan identitas serta tugas utama prajurit siber TNI AD sebagai garda terdepan pertahanan digital bangsa.',
                'website' => 'https://tni-ad.mil.id/',
                'sosial_media' => json_encode([
                    ['platform' => 'instagram', 'label' => 'Instagram @pussiberad', 'url' => 'https://www.instagram.com/pussiberad?igsh=MTA1N2tuMHRobzE5OQ=='],
                    ['platform' => 'tiktok', 'label' => 'TikTok @pusat.siber_ad', 'url' => 'https://www.tiktok.com/@pusat.siber_ad?_r=1&_t=ZS-98XYV7h9dfs'],
                    ['platform' => 'youtube', 'label' => 'YouTube TNI Angkatan Darat', 'url' => 'https://youtube.com/@tniangkatandarat?si=N5Es72T6bSpuLscG'],
                    ['platform' => 'x', 'label' => 'X (Twitter) @tni_ad', 'url' => 'https://x.com/tni_ad'],
                    ['platform' => 'facebook', 'label' => 'Facebook TNI Angkatan Darat', 'url' => 'https://web.facebook.com/TNIAngkatanDarat'],
                    ['platform' => 'wikipedia', 'label' => 'Profil Resmi', 'url' => 'https://id.wikipedia.org/wiki/Pusat_Sandi_dan_Siber_Angkatan_Darat'],
                ]),
                'alamat' => DB::raw("COALESCE(NULLIF(alamat, ''), 'Jl. Veteran No. 5, Gambir, Jakarta Pusat, DKI Jakarta 10110')"),
                'telepon_kontak' => DB::raw("COALESCE(NULLIF(telepon_kontak, ''), '(021) 3849192')"),
            ]);
    }

    public function down(): void
    {
        // Tidak ada rollback — ini migration penambal data, bukan skema.
    }
};
