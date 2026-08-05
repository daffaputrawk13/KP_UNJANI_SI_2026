<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mengarahkan user ke halaman dashboard sesuai satuan (role) tempat ia login.
     * Seluruh 12 role sudah punya halaman dashboard khusus (ADMIN, DANPUS, WADAN,
     * Satlakal (Penangkalan), Satlak Sibersos, Satlak Penindakan, Satlok Duktek (Dukungan Teknologi),
     * Binfung, Binkum, Diklat, Binmat, SDIR). ADMIN bukan satuan operasional —
     * perannya khusus mengelola akun pengguna, data satuan, dan permintaan
     * reset password. Dashboard generik tetap dipertahankan sebagai fallback
     * jika ada satuan baru di kemudian hari.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('satuan');
        $satuan = $user->satuan;

        return match ($satuan?->kode) {
            'ADMIN' => $this->admin($user, $satuan),
            'DANPUS' => $this->danpus($user, $satuan),
            'WADAN' => $this->wadan($user, $satuan),
            'SATLAKAL' => $this->satlakAlmon($user, $satuan),
            'SATLAKSIBERSOS' => $this->satlakSibersos($user, $satuan),
            'SATLAKRINDAK' => $this->satlakRindak($user, $satuan),
            'SATLAKBANGTEK' => $this->satlakBangtek($user, $satuan),
            'BINFUNG' => $this->binfung($user, $satuan),
            'BINKUM' => $this->binkum($user, $satuan),
            'DIKLAT' => $this->diklat($user, $satuan),
            'BINMAT' => $this->binmat($user, $satuan),
            'SDIR' => $this->sdir($user, $satuan),
            default => view('siberad.dashboards.generic', ['user' => $user, 'satuan' => $satuan]),
        };
    }

    /**
     * ADMIN — pengelola sistem: akun pengguna, data satuan, dan permintaan
     * reset password yang dikirim dari halaman "Pengaturan Akun" tiap satuan.
     */
    private function admin($user, $satuan): View
    {
        $semuaPengguna = User::with('satuan')->orderBy('name')->get();
        $semuaSatuan = Satuan::withCount('users')->orderBy('urutan')->get();

        $permintaanResetPassword = [
            ['satuan' => 'Satlak Sibersos', 'catatan' => 'Lupa kata sandi lama', 'tanggal' => '02 Agu 2026', 'status' => 'Menunggu', 'status_class' => 'amber'],
            ['satuan' => 'Binmat (Pembinaan Materiil)', 'catatan' => 'Akun terkunci setelah beberapa kali salah input', 'tanggal' => '01 Agu 2026', 'status' => 'Menunggu', 'status_class' => 'amber'],
            ['satuan' => 'Diklat (Pendidikan & Latihan)', 'catatan' => 'Pergantian operator baru', 'tanggal' => '29 Jul 2026', 'status' => 'Selesai', 'status_class' => 'green'],
        ];

        $aktivitasTerbaru = [
            ['kegiatan' => 'Permintaan reset password baru dari Satlak Sibersos', 'waktu' => '3 jam lalu', 'status' => 'Menunggu', 'status_class' => 'amber'],
            ['kegiatan' => 'Akun WADAN (Wakil Komandan) login dari perangkat baru', 'waktu' => 'Kemarin', 'status' => 'Info', 'status_class' => 'ok'],
            ['kegiatan' => 'Data satuan Binfung (Pembinaan Fungsi) diperbarui', 'waktu' => '2 hari lalu', 'status' => 'Selesai', 'status_class' => 'green'],
        ];

        return view('siberad.dashboards.admin', [
            'user' => $user,
            'satuan' => $satuan,
            'semuaPengguna' => $semuaPengguna,
            'semuaSatuan' => $semuaSatuan,
            'permintaanResetPassword' => $permintaanResetPassword,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'stats' => [
                'total_pengguna' => $semuaPengguna->count(),
                'total_satuan' => $semuaSatuan->count(),
                'reset_password_pending' => collect($permintaanResetPassword)->where('status_class', 'amber')->count(),
                'satuan_tanpa_pengguna' => $semuaSatuan->where('users_count', 0)->count(),
            ],
        ]);
    }

    /**
     * DANPUS — penerima laporan tertinggi, persetujuan akhir, pantauan seluruh satuan.
     */
    private function danpus($user, $satuan): View
    {
        // ADMIN bukan satuan operasional, jadi tidak ikut ditampilkan di
        // pantauan status satuan milik DANPUS.
        $semuaSatuan = Satuan::where('kode', '!=', 'ADMIN')->orderBy('urutan')->get();

        $statusSatuan = [
            'SATLAKAL' => ['label' => 'Ada Insiden', 'class' => 'bad', 'update' => '10 menit lalu'],
            'SATLAKSIBERSOS' => ['label' => 'Siaga', 'class' => 'warn', 'update' => '35 menit lalu'],
            'SATLAKRINDAK' => ['label' => 'Normal', 'class' => 'ok', 'update' => '1 jam lalu'],
            'SATLAKBANGTEK' => ['label' => 'Normal', 'class' => 'ok', 'update' => '2 jam lalu'],
            'BINFUNG' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'BINKUM' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'DIKLAT' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'BINMAT' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Kemarin'],
            'SDIR' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'WADAN' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'DANPUS' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
        ];

        return view('siberad.dashboards.danpus', [
            'user' => $user,
            'satuan' => $satuan,
            'semuaSatuan' => $semuaSatuan,
            'statusSatuan' => $statusSatuan,
            'stats' => [
                'total_satuan' => $semuaSatuan->count(),
                'insiden_aktif' => 1,
                'laporan_pending' => 3,
                'siaga_hijau' => $semuaSatuan->count() - 2,
            ],
            'laporanPrioritas' => [
                ['satuan' => 'Satlakal (Penangkalan)', 'perihal' => 'Serangan DDoS pada portal utama', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'tanggal' => '02 Agu 2026', 'status' => 'Menunggu DANPUS', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Sibersos', 'perihal' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn', 'tanggal' => '01 Agu 2026', 'status' => 'Menunggu DANPUS', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Penindakan', 'perihal' => 'Indikasi ransomware pada server unit', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'tanggal' => '31 Jul 2026', 'status' => 'Disetujui', 'status_class' => 'green'],
            ],
            'laporanMasuk' => [
                ['satuan' => 'Satlakal (Penangkalan)', 'perihal' => 'Serangan DDoS pada portal utama', 'diteruskan_oleh' => 'WADAN', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'status' => 'Menunggu', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Sibersos', 'perihal' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'diteruskan_oleh' => 'WADAN', 'tanggal' => '01 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn', 'status' => 'Menunggu', 'status_class' => 'amber'],
                ['satuan' => 'Binmat', 'perihal' => 'Pengadaan perangkat pemantauan baru', 'diteruskan_oleh' => 'WADAN', 'tanggal' => '29 Jul 2026', 'prioritas' => 'Rendah', 'prioritas_class' => 'ok', 'status' => 'Disetujui', 'status_class' => 'green'],
            ],
        ]);
    }

    /**
     * WADAN — verifikator laporan dari SDIR/Satlak sebelum diteruskan ke DANPUS.
     */
    private function wadan($user, $satuan): View
    {
        return view('siberad.dashboards.wadan', [
            'user' => $user,
            'satuan' => $satuan,
            'stats' => [
                'menunggu_verifikasi' => 4,
                'diteruskan' => 12,
                'satuan_aktif' => 7,
                'permintaan_koordinasi' => 2,
            ],
            'aktivitasTerbaru' => [
                ['satuan' => 'Satlakal (Penangkalan)', 'perihal' => 'Serangan DDoS pada portal utama', 'tanggal' => '02 Agu 2026', 'status' => 'Menunggu Verifikasi', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Sibersos', 'perihal' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'tanggal' => '01 Agu 2026', 'status' => 'Menunggu Verifikasi', 'status_class' => 'amber'],
                ['satuan' => 'SDIR', 'perihal' => 'Permintaan personel tambahan Satlok Duktek (Dukungan Teknologi)', 'tanggal' => '31 Jul 2026', 'status' => 'Diteruskan', 'status_class' => 'green'],
            ],
            'laporanMasuk' => [
                ['satuan' => 'Satlakal (Penangkalan)', 'perihal' => 'Serangan DDoS pada portal utama', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'status' => 'Menunggu', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Sibersos', 'perihal' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'tanggal' => '01 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn', 'status' => 'Menunggu', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Penindakan', 'perihal' => 'Indikasi ransomware pada server unit', 'tanggal' => '31 Jul 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'status' => 'Menunggu', 'status_class' => 'amber'],
                ['satuan' => 'Binfung', 'perihal' => 'Laporan penempatan 3 personel baru', 'tanggal' => '29 Jul 2026', 'prioritas' => 'Rendah', 'prioritas_class' => 'ok', 'status' => 'Diteruskan', 'status_class' => 'green'],
            ],
            'koordinasi' => [
                ['satuan' => 'Satlok Duktek (Dukungan Teknologi)', 'perihal' => 'Permintaan pengiriman 2 personel untuk riset AI', 'diminta_oleh' => 'SDIR', 'tanggal' => '30 Jul 2026', 'status' => 'Menunggu', 'status_class' => 'amber'],
                ['satuan' => 'Diklat', 'perihal' => 'Koordinasi jadwal latihan gabungan', 'diminta_oleh' => 'SDIR', 'tanggal' => '28 Jul 2026', 'status' => 'Selesai', 'status_class' => 'green'],
            ],
            'riwayatDiteruskan' => [
                ['satuan' => 'Satlak Penindakan', 'perihal' => 'Indikasi ransomware pada server unit', 'tanggal' => '31 Jul 2026', 'status' => 'Disetujui DANPUS', 'status_class' => 'green'],
                ['satuan' => 'Binmat', 'perihal' => 'Pengadaan perangkat pemantauan baru', 'tanggal' => '29 Jul 2026', 'status' => 'Disetujui DANPUS', 'status_class' => 'green'],
                ['satuan' => 'Binfung', 'perihal' => 'Laporan penempatan 3 personel baru', 'tanggal' => '29 Jul 2026', 'status' => 'Menunggu DANPUS', 'status_class' => 'amber'],
            ],
        ]);
    }

    /**
     * Satlakal (Penangkalan) — pemantauan & pemulihan website/aset digital.
     */
    private function satlakAlmon($user, $satuan): View
    {
        $asetMonitoring = [
            ['nama' => 'Portal Utama Pussiberad', 'url' => 'pussiberad.mil.id', 'status' => 'Diserang', 'status_class' => 'bad', 'cek_terakhir' => '2 menit lalu'],
            ['nama' => 'Portal PPID', 'url' => 'ppid.pussiberad.mil.id', 'status' => 'Normal', 'status_class' => 'ok', 'cek_terakhir' => '5 menit lalu'],
            ['nama' => 'Sistem Layanan Internal', 'url' => 'sli.pussiberad.mil.id', 'status' => 'Dalam Pemulihan', 'status_class' => 'warn', 'cek_terakhir' => '12 menit lalu'],
            ['nama' => 'Portal Data Kodim', 'url' => 'data-kodim.mil.id', 'status' => 'Normal', 'status_class' => 'ok', 'cek_terakhir' => '20 menit lalu'],
            ['nama' => 'Email Gateway', 'url' => 'mail.pussiberad.mil.id', 'status' => 'Normal', 'status_class' => 'ok', 'cek_terakhir' => '25 menit lalu'],
        ];

        return view('siberad.dashboards.satlakal', [
            'user' => $user,
            'satuan' => $satuan,
            'asetMonitoring' => $asetMonitoring,
            'stats' => [
                'total_aset' => count($asetMonitoring),
                'normal' => 3,
                'diserang' => 1,
                'pemulihan' => 1,
            ],
            'insidenTerbaru' => [
                ['aset' => 'Portal Utama Pussiberad', 'jenis' => 'DDoS Attack', 'waktu' => '2 menit lalu', 'status' => 'Diserang', 'status_class' => 'bad'],
                ['aset' => 'Sistem Layanan Internal', 'jenis' => 'Percobaan SQL Injection', 'waktu' => '1 jam lalu', 'status' => 'Dalam Pemulihan', 'status_class' => 'warn'],
            ],
            'logInsiden' => [
                ['aset' => 'Portal Utama Pussiberad', 'jenis' => 'DDoS Attack', 'waktu' => '02 Agu 2026, 09:14', 'tindakan' => 'Mitigasi traffic & blokir IP mencurigakan', 'status' => 'Berlangsung', 'status_class' => 'amber'],
                ['aset' => 'Sistem Layanan Internal', 'jenis' => 'Percobaan SQL Injection', 'waktu' => '02 Agu 2026, 08:02', 'tindakan' => 'Patch celah & restart layanan', 'status' => 'Dalam Pemulihan', 'status_class' => 'amber'],
                ['aset' => 'Portal Data Kodim', 'jenis' => 'Defacement', 'waktu' => '28 Jul 2026, 14:40', 'tindakan' => 'Restore dari backup, perkuat firewall', 'status' => 'Pulih', 'status_class' => 'green'],
            ],
            'laporanPiket' => [
                ['aset' => 'Portal Utama Pussiberad', 'perihal' => 'Serangan DDoS pada portal utama', 'pelapor' => 'Piket Satlakal (Penangkalan)', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad'],
                ['aset' => 'Sistem Layanan Internal', 'perihal' => 'Percobaan SQL Injection terdeteksi', 'pelapor' => 'Piket Satlakal (Penangkalan)', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn'],
            ],
        ]);
    }

    /**
     * Satlak Sibersos — pengelolaan & pemantauan media sosial di daerah.
     */
    private function satlakSibersos($user, $satuan): View
    {
        $akunMonitoring = [
            ['nama' => '@infokodim0612', 'platform' => 'Instagram', 'wilayah' => 'Kodim 0612/Bandung', 'status' => 'Normal', 'status_class' => 'ok', 'terakhir' => '10 menit lalu'],
            ['nama' => 'Forum Warga Jabar', 'platform' => 'Facebook', 'wilayah' => 'Jawa Barat', 'status' => 'Terpantau Isu', 'status_class' => 'warn', 'terakhir' => '18 menit lalu'],
            ['nama' => '@tnad_update', 'platform' => 'X (Twitter)', 'wilayah' => 'Nasional', 'status' => 'Normal', 'status_class' => 'ok', 'terakhir' => '30 menit lalu'],
            ['nama' => 'Kanal Info Karawang', 'platform' => 'TikTok', 'wilayah' => 'Karawang', 'status' => 'Normal', 'status_class' => 'ok', 'terakhir' => '1 jam lalu'],
        ];

        return view('siberad.dashboards.satlaksibersos', [
            'user' => $user,
            'satuan' => $satuan,
            'akunMonitoring' => $akunMonitoring,
            'stats' => [
                'akun_dipantau' => count($akunMonitoring),
                'isu_aktif' => 1,
                'wilayah' => 6,
                'laporan_bulan_ini' => 9,
            ],
            'isuTerbaru' => [
                ['platform' => 'Facebook', 'wilayah' => 'Jawa Barat', 'ringkasan' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'waktu' => '18 menit lalu', 'status' => 'Ditindaklanjuti', 'status_class' => 'warn'],
                ['platform' => 'X (Twitter)', 'wilayah' => 'Nasional', 'ringkasan' => 'Narasi provokasi soal latihan gabungan', 'waktu' => '2 jam lalu', 'status' => 'Selesai', 'status_class' => 'ok'],
            ],
            'riwayatIsu' => [
                ['platform' => 'Facebook', 'wilayah' => 'Jawa Barat', 'ringkasan' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn', 'status' => 'Ditindaklanjuti', 'status_class' => 'amber'],
                ['platform' => 'X (Twitter)', 'wilayah' => 'Nasional', 'ringkasan' => 'Narasi provokasi soal latihan gabungan', 'prioritas' => 'Rendah', 'prioritas_class' => 'ok', 'status' => 'Selesai', 'status_class' => 'green'],
                ['platform' => 'Instagram', 'wilayah' => 'Kodim 0612/Bandung', 'ringkasan' => 'Akun tiruan mengatasnamakan satuan', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'status' => 'Selesai', 'status_class' => 'green'],
            ],
            'laporanPiket' => [
                ['platform' => 'Facebook', 'wilayah' => 'Jawa Barat', 'ringkasan' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'pelapor' => 'Piket Satlak Sibersos', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn'],
            ],
        ]);
    }

    /**
     * Satlak Penindakan — penanganan aksi cyber: malware, ransomware, dan serangan.
     */
    private function satlakRindak($user, $satuan): View
    {
        $ancamanTerdeteksi = [
            ['nama' => 'Server File Sharing Ditjen', 'jenis' => 'Ransomware (Lockbit variant)', 'tingkat' => 'Kritis', 'tingkat_class' => 'bad', 'terdeteksi' => '8 menit lalu'],
            ['nama' => 'Endpoint Staf Binmat #14', 'jenis' => 'Malware (Trojan)', 'tingkat' => 'Tinggi', 'tingkat_class' => 'bad', 'terdeteksi' => '40 menit lalu'],
            ['nama' => 'Gateway Email Satlok Duktek (Dukungan Teknologi)', 'jenis' => 'Phishing campaign', 'tingkat' => 'Sedang', 'tingkat_class' => 'warn', 'terdeteksi' => '1 jam lalu'],
            ['nama' => 'Jaringan Internal SDIR', 'jenis' => 'Percobaan brute force', 'tingkat' => 'Rendah', 'tingkat_class' => 'ok', 'terdeteksi' => '3 jam lalu'],
        ];

        return view('siberad.dashboards.satlakrindak', [
            'user' => $user,
            'satuan' => $satuan,
            'ancamanTerdeteksi' => $ancamanTerdeteksi,
            'stats' => [
                'ancaman_aktif' => 2,
                'ransomware' => 1,
                'malware_dikarantina' => 6,
                'insiden_selesai_bulan_ini' => 14,
            ],
            'insidenTerbaru' => [
                ['aset' => 'Server File Sharing Ditjen', 'jenis' => 'Ransomware (Lockbit variant)', 'waktu' => '8 menit lalu', 'status' => 'Isolasi Jaringan', 'status_class' => 'bad'],
                ['aset' => 'Endpoint Staf Binmat #14', 'jenis' => 'Malware (Trojan)', 'waktu' => '40 menit lalu', 'status' => 'Dikarantina', 'status_class' => 'warn'],
            ],
            'logPenanganan' => [
                ['aset' => 'Server File Sharing Ditjen', 'jenis' => 'Ransomware (Lockbit variant)', 'waktu' => '02 Agu 2026, 09:32', 'tindakan' => 'Isolasi jaringan, forensik disk, siapkan restore dari backup', 'status' => 'Berlangsung', 'status_class' => 'amber'],
                ['aset' => 'Endpoint Staf Binmat #14', 'jenis' => 'Malware (Trojan)', 'waktu' => '02 Agu 2026, 09:00', 'tindakan' => 'Karantina endpoint, scan menyeluruh, reset kredensial', 'status' => 'Dalam Penanganan', 'status_class' => 'amber'],
                ['aset' => 'Gateway Email Satlok Duktek (Dukungan Teknologi)', 'jenis' => 'Phishing campaign', 'waktu' => '02 Agu 2026, 08:20', 'tindakan' => 'Blokir domain pengirim, edukasi pengguna terdampak', 'status' => 'Selesai', 'status_class' => 'green'],
                ['aset' => 'Portal Data Kodim', 'jenis' => 'Defacement (rujukan Satlakal (Penangkalan))', 'waktu' => '28 Jul 2026, 15:10', 'tindakan' => 'Analisis malware yang disisipkan, patch celah upload', 'status' => 'Selesai', 'status_class' => 'green'],
            ],
            'laporanPiket' => [
                ['aset' => 'Server File Sharing Ditjen', 'perihal' => 'Indikasi ransomware mengenkripsi file bersama', 'pelapor' => 'Piket Satlak Penindakan', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad'],
                ['aset' => 'Endpoint Staf Binmat #14', 'perihal' => 'Trojan terdeteksi via antivirus terpusat', 'pelapor' => 'Piket Satlak Penindakan', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn'],
            ],
        ]);
    }

    /**
     * Satlok Duktek (Dukungan Teknologi) — riset & pengembangan teknologi (AI, drone, dll).
     */
    private function satlakBangtek($user, $satuan): View
    {
        $proyekRiset = [
            ['nama' => 'Deteksi Anomali Jaringan berbasis AI', 'kategori' => 'AI / Machine Learning', 'progres' => 72, 'status' => 'Berjalan', 'status_class' => 'warn', 'target' => 'Sep 2026'],
            ['nama' => 'Drone Pemantau Perbatasan Gen-2', 'kategori' => 'Drone / UAV', 'progres' => 45, 'status' => 'Berjalan', 'status_class' => 'warn', 'target' => 'Nov 2026'],
            ['nama' => 'Chatbot Internal Layanan Personel', 'kategori' => 'AI / NLP', 'progres' => 100, 'status' => 'Selesai', 'status_class' => 'ok', 'target' => 'Jul 2026'],
            ['nama' => 'Sistem Enkripsi Komunikasi Lapangan', 'kategori' => 'Keamanan Siber', 'progres' => 20, 'status' => 'Riset Awal', 'status_class' => 'ok', 'target' => 'Feb 2027'],
        ];

        return view('siberad.dashboards.satlakbangtek', [
            'user' => $user,
            'satuan' => $satuan,
            'proyekRiset' => $proyekRiset,
            'stats' => [
                'proyek_aktif' => 3,
                'proyek_ai' => 2,
                'unit_drone_uji' => 4,
                'prototipe_selesai' => 1,
            ],
            'aktivitasTerbaru' => [
                ['proyek' => 'Deteksi Anomali Jaringan berbasis AI', 'kegiatan' => 'Pelatihan ulang model dengan data insiden terbaru', 'waktu' => '1 jam lalu', 'status' => 'Berjalan', 'status_class' => 'warn'],
                ['proyek' => 'Drone Pemantau Perbatasan Gen-2', 'kegiatan' => 'Uji terbang prototipe di lapangan Cimahi', 'waktu' => 'Kemarin', 'status' => 'Berjalan', 'status_class' => 'warn'],
            ],
            'logUji' => [
                ['proyek' => 'Drone Pemantau Perbatasan Gen-2', 'kegiatan' => 'Uji terbang prototipe #3 — jangkauan & stabilitas kamera', 'waktu' => '01 Agu 2026, 14:00', 'hasil' => 'Jangkauan tercapai, perlu perbaikan gimbal kamera', 'status' => 'Selesai', 'status_class' => 'green'],
                ['proyek' => 'Deteksi Anomali Jaringan berbasis AI', 'kegiatan' => 'Validasi model terhadap dataset serangan Satlak Penindakan', 'waktu' => '30 Jul 2026, 10:30', 'hasil' => 'Akurasi 91%, false positive masih tinggi', 'status' => 'Perlu Tindak Lanjut', 'status_class' => 'amber'],
                ['proyek' => 'Chatbot Internal Layanan Personel', 'kegiatan' => 'Uji terima pengguna (UAT) bersama Binfung', 'waktu' => '25 Jul 2026, 09:00', 'hasil' => 'Disetujui untuk dipakai satuan', 'status' => 'Selesai', 'status_class' => 'green'],
            ],
            'laporanPiket' => [
                ['proyek' => 'Drone Pemantau Perbatasan Gen-2', 'perihal' => 'Pengajuan anggaran komponen gimbal kamera baru', 'pelapor' => 'Piket Satlok Duktek (Dukungan Teknologi)', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn'],
            ],
        ]);
    }

    /**
     * Binfung — pembinaan fungsi: penempatan personel yang masuk.
     */
    private function binfung($user, $satuan): View
    {
        $penempatan = [
            ['nama' => 'Serda Ahmad Fauzi', 'satuan_tujuan' => 'Satlak Penindakan', 'jabatan' => 'Piket', 'tanggal' => '02 Agu 2026', 'status' => 'Menunggu SK', 'status_class' => 'warn'],
            ['nama' => 'Sertu Rina Wulandari', 'satuan_tujuan' => 'Satlok Duktek (Dukungan Teknologi)', 'jabatan' => 'Staf Riset', 'tanggal' => '01 Agu 2026', 'status' => 'Menunggu SK', 'status_class' => 'warn'],
            ['nama' => 'Serka Budi Santoso', 'satuan_tujuan' => 'Binmat', 'jabatan' => 'Staf', 'tanggal' => '29 Jul 2026', 'status' => 'Ditempatkan', 'status_class' => 'ok'],
            ['nama' => 'Sertu Dewi Anggraini', 'satuan_tujuan' => 'Satlak Sibersos', 'jabatan' => 'Piket', 'tanggal' => '27 Jul 2026', 'status' => 'Ditempatkan', 'status_class' => 'ok'],
        ];

        return view('siberad.dashboards.binfung', [
            'user' => $user,
            'satuan' => $satuan,
            'penempatan' => $penempatan,
            'stats' => [
                'personel_masuk_bulan_ini' => 6,
                'menunggu_sk' => 2,
                'satuan_terisi' => 11,
                'total_personel' => 88,
            ],
            'aktivitasTerbaru' => [
                ['nama' => 'Serda Ahmad Fauzi', 'kegiatan' => 'Pengajuan penempatan ke Satlak Penindakan', 'waktu' => '2 jam lalu', 'status' => 'Menunggu SK', 'status_class' => 'warn'],
                ['nama' => 'Serka Budi Santoso', 'kegiatan' => 'Serah terima jabatan di Binmat', 'waktu' => 'Kemarin', 'status' => 'Selesai', 'status_class' => 'ok'],
            ],
            'riwayatPenempatan' => [
                ['nama' => 'Serka Budi Santoso', 'satuan_tujuan' => 'Binmat', 'jabatan' => 'Staf', 'tanggal' => '29 Jul 2026', 'status' => 'Ditempatkan', 'status_class' => 'green'],
                ['nama' => 'Sertu Dewi Anggraini', 'satuan_tujuan' => 'Satlak Sibersos', 'jabatan' => 'Piket', 'tanggal' => '27 Jul 2026', 'status' => 'Ditempatkan', 'status_class' => 'green'],
                ['nama' => 'Kopda Yusuf Hidayat', 'satuan_tujuan' => 'Satlakal (Penangkalan)', 'jabatan' => 'Piket', 'tanggal' => '20 Jul 2026', 'status' => 'Ditempatkan', 'status_class' => 'green'],
            ],
            'laporanPiket' => [
                ['nama' => 'Serda Ahmad Fauzi', 'perihal' => 'Pengajuan penempatan personel baru ke Satlak Penindakan', 'pelapor' => 'Piket Binfung', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn'],
            ],
        ]);
    }

    /**
     * Binkum — pembinaan umum: pengawasan satuan, lomba internal, personel baru.
     */
    private function binkum($user, $satuan): View
    {
        $pengawasanSatuan = [
            ['satuan' => 'Satlak Penindakan', 'aspek' => 'Kepatuhan SOP penanganan insiden', 'hasil' => 'Baik', 'hasil_class' => 'ok', 'tanggal' => '01 Agu 2026'],
            ['satuan' => 'Satlak Sibersos', 'aspek' => 'Disiplin pelaporan harian', 'hasil' => 'Perlu Perbaikan', 'hasil_class' => 'warn', 'tanggal' => '30 Jul 2026'],
            ['satuan' => 'Binmat', 'aspek' => 'Administrasi inventaris', 'hasil' => 'Baik', 'hasil_class' => 'ok', 'tanggal' => '28 Jul 2026'],
            ['satuan' => 'Satlok Duktek (Dukungan Teknologi)', 'aspek' => 'Keamanan dokumen riset', 'hasil' => 'Pelanggaran Ringan', 'hasil_class' => 'bad', 'tanggal' => '25 Jul 2026'],
        ];

        return view('siberad.dashboards.binkum', [
            'user' => $user,
            'satuan' => $satuan,
            'pengawasanSatuan' => $pengawasanSatuan,
            'stats' => [
                'satuan_diawasi' => 11,
                'lomba_aktif' => 1,
                'personel_baru_diverifikasi' => 6,
                'pelanggaran_tercatat' => 1,
            ],
            'aktivitasTerbaru' => [
                ['satuan' => 'Satlok Duktek (Dukungan Teknologi)', 'kegiatan' => 'Temuan pelanggaran ringan keamanan dokumen riset', 'waktu' => '3 hari lalu', 'status' => 'Ditindaklanjuti', 'status_class' => 'warn'],
                ['satuan' => 'Satlak Sibersos', 'kegiatan' => 'Evaluasi disiplin pelaporan harian', 'waktu' => '5 hari lalu', 'status' => 'Selesai', 'status_class' => 'ok'],
            ],
            'lombaInternal' => [
                ['nama' => 'Lomba Satuan Terbaik Triwulan III', 'peserta' => '11 Satuan', 'periode' => 'Jul – Sep 2026', 'status' => 'Berlangsung', 'status_class' => 'amber'],
                ['nama' => 'Lomba Kedisiplinan Pelaporan', 'peserta' => '11 Satuan', 'periode' => 'Apr – Jun 2026', 'status' => 'Selesai', 'status_class' => 'green'],
            ],
            'laporanPiket' => [
                ['satuan' => 'Satlok Duktek (Dukungan Teknologi)', 'perihal' => 'Temuan pelanggaran ringan keamanan dokumen riset', 'pelapor' => 'Piket Binkum', 'tanggal' => '25 Jul 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn'],
            ],
        ]);
    }

    /**
     * Diklat — pendidikan dan latihan-latihan satuan.
     */
    private function diklat($user, $satuan): View
    {
        $programDiklat = [
            ['nama' => 'Dikjur Operator Siber Dasar', 'kategori' => 'Pendidikan', 'peserta' => 24, 'progres' => 80, 'status' => 'Berjalan', 'status_class' => 'warn', 'selesai' => '20 Agu 2026'],
            ['nama' => 'Latihan Gabungan Penanganan Insiden', 'kategori' => 'Latihan', 'peserta' => 40, 'progres' => 100, 'status' => 'Selesai', 'status_class' => 'ok', 'selesai' => '28 Jul 2026'],
            ['nama' => 'Kursus Forensik Digital Lanjutan', 'kategori' => 'Pendidikan', 'peserta' => 12, 'progres' => 35, 'status' => 'Berjalan', 'status_class' => 'warn', 'selesai' => '15 Sep 2026'],
            ['nama' => 'Latihan Simulasi Serangan Siber (Redteam)', 'kategori' => 'Latihan', 'peserta' => 18, 'progres' => 0, 'status' => 'Belum Mulai', 'status_class' => 'ok', 'selesai' => '10 Okt 2026'],
        ];

        return view('siberad.dashboards.diklat', [
            'user' => $user,
            'satuan' => $satuan,
            'programDiklat' => $programDiklat,
            'stats' => [
                'program_aktif' => 2,
                'total_peserta' => 76,
                'latihan_terjadwal' => 1,
                'lulus_bulan_ini' => 40,
            ],
            'aktivitasTerbaru' => [
                ['program' => 'Dikjur Operator Siber Dasar', 'kegiatan' => 'Ujian praktik tahap 3', 'waktu' => '3 jam lalu', 'status' => 'Berjalan', 'status_class' => 'warn'],
                ['program' => 'Latihan Gabungan Penanganan Insiden', 'kegiatan' => 'Evaluasi akhir & penyerahan sertifikat', 'waktu' => 'Kemarin', 'status' => 'Selesai', 'status_class' => 'ok'],
            ],
            'jadwalLatihan' => [
                ['nama' => 'Latihan Simulasi Serangan Siber (Redteam)', 'satuan_terlibat' => 'Satlak Penindakan, Satlakal (Penangkalan)', 'lokasi' => 'Puslatpur Pussiberad', 'tanggal' => '10 Okt 2026', 'status' => 'Terjadwal', 'status_class' => 'amber'],
                ['nama' => 'Kursus Forensik Digital Lanjutan', 'satuan_terlibat' => 'Satlak Penindakan', 'lokasi' => 'Ruang Kelas Diklat', 'tanggal' => 'Berlangsung s/d 15 Sep 2026', 'status' => 'Berlangsung', 'status_class' => 'amber'],
                ['nama' => 'Dikjur Operator Siber Dasar Angkatan Berikutnya', 'satuan_terlibat' => 'Seluruh Satlak', 'lokasi' => 'Ruang Kelas Diklat', 'tanggal' => '01 Nov 2026', 'status' => 'Direncanakan', 'status_class' => 'ok'],
            ],
            'laporanPiket' => [
                ['program' => 'Kursus Forensik Digital Lanjutan', 'perihal' => 'Permintaan tambahan modul praktik lab', 'pelapor' => 'Piket Diklat', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Rendah', 'prioritas_class' => 'ok'],
            ],
        ]);
    }

    /**
     * Binmat — pengurusan material/perlengkapan satuan.
     */
    private function binmat($user, $satuan): View
    {
        $inventaris = [
            ['nama' => 'Firewall Appliance (unit)', 'kategori' => 'Perangkat Jaringan', 'jumlah' => 14, 'kondisi' => 'Baik', 'kondisi_class' => 'ok', 'update' => '1 hari lalu'],
            ['nama' => 'Laptop Operasional Satlak', 'kategori' => 'Komputer', 'jumlah' => 62, 'kondisi' => 'Baik', 'kondisi_class' => 'ok', 'update' => '2 hari lalu'],
            ['nama' => 'Drone Uji Satlok Duktek (Dukungan Teknologi)', 'kategori' => 'Alat Khusus', 'jumlah' => 4, 'kondisi' => 'Perlu Perawatan', 'kondisi_class' => 'warn', 'update' => 'Kemarin'],
            ['nama' => 'Server Rack Pusat Data', 'kategori' => 'Perangkat Server', 'jumlah' => 6, 'kondisi' => 'Kritis (1 unit rusak)', 'kondisi_class' => 'bad', 'update' => '5 jam lalu'],
        ];

        return view('siberad.dashboards.binmat', [
            'user' => $user,
            'satuan' => $satuan,
            'inventaris' => $inventaris,
            'stats' => [
                'total_item' => 86,
                'permintaan_pending' => 3,
                'kondisi_kritis' => 1,
                'pengadaan_selesai_bulan_ini' => 2,
            ],
            'aktivitasTerbaru' => [
                ['item' => 'Server Rack Pusat Data', 'kegiatan' => 'Laporan kerusakan 1 unit server, menunggu suku cadang', 'waktu' => '5 jam lalu', 'status' => 'Kritis', 'status_class' => 'bad'],
                ['item' => 'Drone Uji Satlok Duktek (Dukungan Teknologi)', 'kegiatan' => 'Jadwal perawatan berkala unit drone', 'waktu' => 'Kemarin', 'status' => 'Perlu Perawatan', 'status_class' => 'warn'],
            ],
            'permintaanPengadaan' => [
                ['item' => 'Suku cadang Server Rack Pusat Data', 'diajukan_oleh' => 'Binmat', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'status' => 'Menunggu Persetujuan', 'status_class' => 'amber'],
                ['item' => 'Komponen gimbal kamera drone', 'diajukan_oleh' => 'Satlok Duktek (Dukungan Teknologi)', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn', 'status' => 'Menunggu Persetujuan', 'status_class' => 'amber'],
                ['item' => 'Firewall Appliance tambahan', 'diajukan_oleh' => 'Satlak Penindakan', 'tanggal' => '29 Jul 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn', 'status' => 'Disetujui', 'status_class' => 'green'],
            ],
            'laporanPiket' => [
                ['item' => 'Server Rack Pusat Data', 'perihal' => 'Kerusakan hardware pada 1 unit server', 'pelapor' => 'Piket Binmat', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad'],
            ],
        ]);
    }

    /**
     * SDIR — koordinasi antar Satlak dan pelaporan ke DANPUS.
     */
    private function sdir($user, $satuan): View
    {
        $koordinasiSatlak = [
            ['satuan' => 'Satlok Duktek (Dukungan Teknologi)', 'perihal' => 'Permintaan pengiriman 2 personel untuk riset AI', 'jenis' => 'Permintaan Personel', 'tanggal' => '30 Jul 2026', 'status' => 'Menunggu Binfung', 'status_class' => 'amber'],
            ['satuan' => 'Diklat', 'perihal' => 'Koordinasi jadwal latihan gabungan', 'jenis' => 'Jadwal', 'tanggal' => '28 Jul 2026', 'status' => 'Selesai', 'status_class' => 'green'],
            ['satuan' => 'Satlak Penindakan', 'perihal' => 'Koordinasi eskalasi insiden ransomware', 'jenis' => 'Insiden', 'tanggal' => '02 Agu 2026', 'status' => 'Berlangsung', 'status_class' => 'amber'],
        ];

        return view('siberad.dashboards.sdir', [
            'user' => $user,
            'satuan' => $satuan,
            'koordinasiSatlak' => $koordinasiSatlak,
            'stats' => [
                'satlak_dikoordinasikan' => 4,
                'laporan_diteruskan' => 9,
                'permintaan_koordinasi_aktif' => 2,
                'menunggu_wadan' => 3,
            ],
            'aktivitasTerbaru' => [
                ['satuan' => 'Satlak Penindakan', 'kegiatan' => 'Koordinasi eskalasi insiden ransomware ke WADAN', 'waktu' => '1 jam lalu', 'status' => 'Berlangsung', 'status_class' => 'amber'],
                ['satuan' => 'Satlok Duktek (Dukungan Teknologi)', 'kegiatan' => 'Meneruskan permintaan personel ke Binfung', 'waktu' => 'Kemarin', 'status' => 'Menunggu', 'status_class' => 'amber'],
            ],
            'laporanDiteruskan' => [
                ['satuan' => 'Satlakal (Penangkalan)', 'perihal' => 'Serangan DDoS pada portal utama', 'tanggal' => '02 Agu 2026', 'diteruskan_ke' => 'WADAN', 'status' => 'Menunggu', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Sibersos', 'perihal' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'tanggal' => '01 Agu 2026', 'diteruskan_ke' => 'WADAN', 'status' => 'Menunggu', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Penindakan', 'perihal' => 'Indikasi ransomware pada server unit', 'tanggal' => '31 Jul 2026', 'diteruskan_ke' => 'WADAN', 'status' => 'Diteruskan', 'status_class' => 'green'],
            ],
            'laporanPiket' => [
                ['satuan' => 'Satlak Penindakan', 'perihal' => 'Permintaan koordinasi eskalasi insiden ransomware', 'pelapor' => 'Piket SDIR', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad'],
            ],
        ]);
    }
}
