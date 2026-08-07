<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DukunganTeknisLog;
use App\Models\Jabatan;
use App\Models\Laporan;
use App\Models\LaporanMonitoring;
use App\Models\LaporanPublikasi;
use App\Models\LogUjiPengembangan;
use App\Models\Pangkat;
use App\Models\Pengaturan;
use App\Models\Pengumuman;
use App\Models\Personel;
use App\Models\PersonelDokumen;
use App\Models\PersonelMutasi;
use App\Models\ProyekRiset;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Mengarahkan user ke halaman dashboard sesuai satuan (role) tempat ia login.
     * Seluruh 12 role sudah punya halaman dashboard khusus (ADMIN, DANPUS, WADAN,
     * Satlakal (Penangkalan), Satlak Sibersos, Satlak Penindakan, satlak Duktek (Dukungan Teknologi),
     * Binfung, Binum, Diklat, Binmat, SDIR). ADMIN bukan satuan operasional —
     * perannya khusus mengelola akun pengguna, data satuan, dan permintaan
     * reset password. Dashboard generik tetap dipertahankan sebagai fallback
     * jika ada satuan baru di kemudian hari.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('satuan');
        $satuan = $user->satuan;

        // Normalisasi kode (trim + uppercase) supaya pencocokan di bawah tidak
        // meleset gara-gara spasi tersembunyi atau perbedaan huruf besar/kecil
        // pada data di database — penyebab dashboard jatuh ke tampilan generic
        // padahal view khususnya sudah ada.
        $kode = $satuan?->kode ? strtoupper(trim($satuan->kode)) : null;

        return match ($kode) {
            'ADMIN' => $this->admin($user, $satuan),
            'DANPUS' => $this->danpus($user, $satuan),
            'WADAN' => $this->wadan($user, $satuan),
            'SATLAKKAL' => $this->satlakAlmon($user, $satuan),
            'SATLAKSISOS' => $this->satlakSibersos($user, $satuan),
            'SATLAKDAK' => $this->satlakRindak($user, $satuan),
            'SATLAKDUKTEK' => $this->satlakDuktek($user, $satuan),
            'BINFUNG' => $this->binfung($user, $satuan),
            'BINUM' => $this->binum($user, $satuan),
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

        // ===== Data untuk grafik Dashboard =====

        // Grafik 1: distribusi jumlah pengguna per kategori satuan.
        $labelKategori = [
            Satuan::KATEGORI_SATLAK => 'Satlak',
            Satuan::KATEGORI_DIREKTORAT => 'Direktorat',
            Satuan::KATEGORI_PIMPINAN => 'Pimpinan',
            Satuan::KATEGORI_ADMIN => 'Admin',
        ];
        $distribusiPenggunaKategori = $semuaSatuan
            ->groupBy('kategori')
            ->map(fn ($group, $kategori) => [
                'kategori' => $labelKategori[$kategori] ?? ucfirst($kategori),
                'jumlah' => $group->sum('users_count'),
            ])
            ->values();

        // Grafik 2: status permintaan reset password.
        $statusResetPassword = collect($permintaanResetPassword)
            ->groupBy('status')
            ->map(fn ($group, $status) => ['status' => $status, 'jumlah' => $group->count()])
            ->values();

        // Grafik 3: kelengkapan akun tiap satuan (sudah vs belum punya akun).
        $satuanSudahPunyaAkun = $semuaSatuan->where('users_count', '>', 0)->count();
        $satuanBelumPunyaAkun = $semuaSatuan->where('users_count', 0)->count();

        // Log aktivitas sistem — dipakai tab "Log Aktivitas" (Monitoring
        // Aktivitas Sistem). Diambil dari tabel activity_logs yang diisi
        // otomatis dari login/logout dan seluruh aksi CRUD Admin.
        $logAktivitas = ActivityLog::with('user')->latest('created_at')->limit(200)->get();

        // Daftar backup database yang sudah pernah dibuat.
        $daftarBackup = app(\App\Http\Controllers\Admin\BackupController::class)->index();

        // Pengumuman — dikelola admin, tampil sebagai banner di seluruh dashboard satuan.
        $daftarPengumuman = Pengumuman::with('pembuat')->latest()->get();

        // Sesi login aktif (SESSION_DRIVER=database) — dipakai tab "Sesi Aktif"
        // untuk memantau siapa saja yang sedang login dan bisa paksa logout.
        $sesiAktif = DB::table('sessions')
            ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
            ->orderByDesc('sessions.last_activity')
            ->get([
                'sessions.id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.name as user_name',
            ]);

        // Rekap laporan lintas Satlak — ringkasan jumlah laporan & statusnya
        // per satuan pelaksana, dipakai tab "Rekap Laporan".
        $rekapLaporanSatuan = Satuan::where('kategori', Satuan::KATEGORI_SATLAK)
            ->withCount([
                'laporanTerkirim as total_laporan',
                'laporanTerkirim as laporan_menunggu' => fn ($q) => $q->where('status', 'Menunggu'),
                'laporanTerkirim as laporan_disetujui' => fn ($q) => $q->where('status', 'Disetujui DANPUS'),
                'laporanTerkirim as laporan_ditolak' => fn ($q) => $q->where('status', 'Ditolak DANPUS'),
            ])
            ->orderBy('urutan')
            ->get();

        return view('siberad.dashboards.admin', [
            'user' => $user,
            'satuan' => $satuan,
            'semuaPengguna' => $semuaPengguna,
            'semuaSatuan' => $semuaSatuan,
            'permintaanResetPassword' => $permintaanResetPassword,
            'aktivitasTerbaru' => $aktivitasTerbaru,
            'distribusiPenggunaKategori' => $distribusiPenggunaKategori,
            'statusResetPassword' => $statusResetPassword,
            'kelengkapanAkunSatuan' => [
                'sudah' => $satuanSudahPunyaAkun,
                'belum' => $satuanBelumPunyaAkun,
            ],
            'pengaturan' => Pengaturan::current(),
            'logAktivitas' => $logAktivitas,
            'daftarBackup' => $daftarBackup,
            'daftarPengumuman' => $daftarPengumuman,
            'sesiAktif' => $sesiAktif,
            'sesiSayaId' => session()->getId(),
            'rekapLaporanSatuan' => $rekapLaporanSatuan,
            'modulHakAkses' => Satuan::MODUL_HAK_AKSES,
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
            'SATLAKKAL' => ['label' => 'Ada Insiden', 'class' => 'bad', 'update' => '10 menit lalu'],
            'SATLAKSISOS' => ['label' => 'Siaga', 'class' => 'warn', 'update' => '35 menit lalu'],
            'SATLAKDAK' => ['label' => 'Normal', 'class' => 'ok', 'update' => '1 jam lalu'],
            'SATLAKDUKTEK' => ['label' => 'Normal', 'class' => 'ok', 'update' => '2 jam lalu'],
            'BINFUNG' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'BINUM' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'DIKLAT' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'BINMAT' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Kemarin'],
            'SDIR' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'WADAN' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
            'DANPUS' => ['label' => 'Normal', 'class' => 'ok', 'update' => 'Hari ini'],
        ];

        // Laporan asli yang dikirim langsung ke DANPUS (mis. dari satlak Duktek/
        // Bangtek). Tidak ada lagi data dummy — semua baris di tabel "Laporan
        // Masuk" sekarang murni berasal dari database.
        $laporanMasukFinal = Laporan::with('satuan')
            ->where('tujuan_satuan_id', $satuan->id)
            ->latest()
            ->get()
            ->map(fn (Laporan $l) => [
                'id' => $l->id,
                'satuan' => $l->satuan->nama,
                'perihal' => $l->perihal,
                'diteruskan_oleh' => $l->satuan->nama, // dikirim langsung, bukan lewat WADAN
                'tanggal' => $l->created_at->translatedFormat('d M Y'),
                // Format ISO (YYYY-MM-DD) dipakai khusus untuk filter rentang
                // tanggal di tab "Riwayat Laporan" — tidak ditampilkan langsung.
                'tanggal_iso' => $l->created_at->format('Y-m-d'),
                'prioritas' => $l->prioritas,
                'prioritas_class' => match ($l->prioritas) {
                    'Tinggi' => 'bad',
                    'Sedang' => 'warn',
                    default => 'ok',
                },
                'status' => $l->status,
                'status_class' => match ($l->status) {
                    'Disetujui DANPUS' => 'green',
                    'Ditolak DANPUS' => 'bad',
                    default => 'amber',
                },
                // URL lampiran PDF (kalau ada) — dipakai tombol "Lihat/Unduh PDF" di tabel.
                // Butuh `php artisan storage:link` supaya folder storage/app/public bisa diakses lewat /storage/...
                'lampiran_url' => $l->lampiran_path ? asset('storage/'.$l->lampiran_path) : null,
            ])
            ->toArray();

        // Laporan Monitoring & Recovery yang masuk dari Satlakal (REAL, dari DB)
        // — DANPUS bisa Setujui / Tolak / Minta Revisi lewat tab ini.
        $laporanMonitoringMasuk = LaporanMonitoring::where('tujuan_satuan_id', $satuan->id)
            ->where('status', 'Dikirim')
            ->with('satuan')
            ->latest('tanggal_kirim')
            ->get();

        // Notifikasi laporan baru untuk DANPUS yang sedang login — ditampilkan
        // di lonceng notifikasi pada topbar.
        $notifikasi = $user->unreadNotifications;

        // ===== Grafik 1: Distribusi status seluruh satuan (Normal / Siaga / Ada Insiden) =====
        $jumlahPerStatusClass = collect($statusSatuan)->groupBy('class')->map->count();
        $statusDistribusi = [
            ['label' => 'Normal', 'jumlah' => $jumlahPerStatusClass->get('ok', 0)],
            ['label' => 'Siaga', 'jumlah' => $jumlahPerStatusClass->get('warn', 0)],
            ['label' => 'Ada Insiden', 'jumlah' => $jumlahPerStatusClass->get('bad', 0)],
        ];

        // ===== Grafik 2: Jumlah laporan masuk per tingkat prioritas =====
        $jumlahPerPrioritas = collect($laporanMasukFinal)->groupBy('prioritas')->map->count();
        $laporanPerPrioritas = [
            ['label' => 'Tinggi', 'jumlah' => $jumlahPerPrioritas->get('Tinggi', 0)],
            ['label' => 'Sedang', 'jumlah' => $jumlahPerPrioritas->get('Sedang', 0)],
            ['label' => 'Rendah', 'jumlah' => $jumlahPerPrioritas->get('Rendah', 0)],
        ];

        // ===== Grafik 3: Satuan paling aktif melapor (jumlah laporan per satuan) =====
        $laporanPerSatuanChart = collect($laporanMasukFinal)
            ->groupBy('satuan')
            ->map(fn ($grup, $nama) => ['satuan' => $nama, 'jumlah' => $grup->count()])
            ->sortByDesc('jumlah')
            ->values()
            ->toArray();

        return view('siberad.dashboards.danpus', [
            'notifikasi' => $notifikasi,
            'user' => $user,
            'satuan' => $satuan,
            'semuaSatuan' => $semuaSatuan,
            'statusSatuan' => $statusSatuan,
            'stats' => [
                'total_satuan' => $semuaSatuan->count(),
                'insiden_aktif' => 1,
                'laporan_pending' => collect($laporanMasukFinal)->where('status', 'Menunggu')->count(),
                'siaga_hijau' => $semuaSatuan->count() - 2,
            ],
            'laporanPrioritas' => [
                ['satuan' => 'Satlakal (Penangkalan)', 'perihal' => 'Serangan DDoS pada portal utama', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'tanggal' => '02 Agu 2026', 'status' => 'Menunggu DANPUS', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Sibersos', 'perihal' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn', 'tanggal' => '01 Agu 2026', 'status' => 'Menunggu DANPUS', 'status_class' => 'amber'],
                ['satuan' => 'Satlak Penindakan', 'perihal' => 'Indikasi ransomware pada server unit', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'tanggal' => '31 Jul 2026', 'status' => 'Disetujui', 'status_class' => 'green'],
            ],
            'laporanMasuk' => $laporanMasukFinal,
            'laporanMonitoringMasuk' => $laporanMonitoringMasuk,
            'statusDistribusi' => $statusDistribusi,
            'laporanPerPrioritas' => $laporanPerPrioritas,
            'laporanPerSatuanChart' => $laporanPerSatuanChart,
        ]);
    }

    /**
     * WADAN — verifikator laporan dari SDIR/Satlak sebelum diteruskan ke DANPUS.
     */
    private function wadan($user, $satuan): View
    {
        $laporanMasuk = [
            ['satuan' => 'Satlakal (Penangkalan)', 'perihal' => 'Serangan DDoS pada portal utama', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'status' => 'Menunggu', 'status_class' => 'amber'],
            ['satuan' => 'Satlak Sibersos', 'perihal' => 'Hoaks rekrutmen mengatasnamakan TNI AD', 'tanggal' => '01 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn', 'status' => 'Menunggu', 'status_class' => 'amber'],
            ['satuan' => 'Satlak Penindakan', 'perihal' => 'Indikasi ransomware pada server unit', 'tanggal' => '31 Jul 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'status' => 'Menunggu', 'status_class' => 'amber'],
            ['satuan' => 'Binfung', 'perihal' => 'Laporan penempatan 3 personel baru', 'tanggal' => '29 Jul 2026', 'prioritas' => 'Rendah', 'prioritas_class' => 'ok', 'status' => 'Diteruskan', 'status_class' => 'green'],
        ];

        $koordinasi = [
            ['satuan' => 'satlak Duktek (Dukungan Teknologi)', 'perihal' => 'Permintaan pengiriman 2 personel untuk riset AI', 'diminta_oleh' => 'SDIR', 'tanggal' => '30 Jul 2026', 'status' => 'Menunggu', 'status_class' => 'amber'],
            ['satuan' => 'Diklat', 'perihal' => 'Koordinasi jadwal latihan gabungan', 'diminta_oleh' => 'SDIR', 'tanggal' => '28 Jul 2026', 'status' => 'Selesai', 'status_class' => 'green'],
        ];

        // ===== Grafik 1: Jumlah laporan masuk per tingkat prioritas =====
        $jumlahPerPrioritas = collect($laporanMasuk)->groupBy('prioritas')->map->count();
        $laporanPerPrioritas = [
            ['label' => 'Tinggi', 'jumlah' => $jumlahPerPrioritas->get('Tinggi', 0)],
            ['label' => 'Sedang', 'jumlah' => $jumlahPerPrioritas->get('Sedang', 0)],
            ['label' => 'Rendah', 'jumlah' => $jumlahPerPrioritas->get('Rendah', 0)],
        ];

        // ===== Grafik 2: Satuan paling aktif melapor (jumlah laporan per satuan) =====
        $laporanPerSatuanChart = collect($laporanMasuk)
            ->groupBy('satuan')
            ->map(fn ($grup, $nama) => ['satuan' => $nama, 'jumlah' => $grup->count()])
            ->sortByDesc('jumlah')
            ->values()
            ->toArray();

        // ===== Grafik 3: Status permintaan koordinasi (Menunggu vs Selesai) =====
        $jumlahPerStatusKoordinasi = collect($koordinasi)->groupBy('status')->map->count();
        $statusKoordinasi = [
            ['label' => 'Menunggu', 'jumlah' => $jumlahPerStatusKoordinasi->get('Menunggu', 0)],
            ['label' => 'Selesai', 'jumlah' => $jumlahPerStatusKoordinasi->get('Selesai', 0)],
        ];

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
                ['satuan' => 'SDIR', 'perihal' => 'Permintaan personel tambahan satlak Duktek (Dukungan Teknologi)', 'tanggal' => '31 Jul 2026', 'status' => 'Diteruskan', 'status_class' => 'green'],
            ],
            'laporanMasuk' => $laporanMasuk,
            'koordinasi' => $koordinasi,
            'laporanPerPrioritas' => $laporanPerPrioritas,
            'laporanPerSatuanChart' => $laporanPerSatuanChart,
            'statusKoordinasi' => $statusKoordinasi,
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
        // Data pemakaian resource (CPU/RAM/Storage/Network) & uptime masih mock
        // (belum ditarik dari agent monitoring sungguhan) — akan disambungkan ke
        // database/monitoring service pada iterasi berikutnya.
        $asetMonitoring = [
            [
                'nama' => 'Portal Utama Pussiberad', 'url' => 'pussiberad.mil.id', 'status' => 'Diserang', 'status_class' => 'bad', 'cek_terakhir' => '2 menit lalu',
                'cpu' => 92, 'ram' => 81, 'storage' => 64, 'network' => 340.0,
                'uptime_24h' => 97.20, 'uptime_30h' => 99.10, 'avg_response' => 850,
            ],
            [
                'nama' => 'Portal PPID', 'url' => 'ppid.pussiberad.mil.id', 'status' => 'Normal', 'status_class' => 'ok', 'cek_terakhir' => '5 menit lalu',
                'cpu' => 18, 'ram' => 34, 'storage' => 45, 'network' => 6.2,
                'uptime_24h' => 100.00, 'uptime_30h' => 99.95, 'avg_response' => 120,
            ],
            [
                'nama' => 'Sistem Layanan Internal', 'url' => 'sli.pussiberad.mil.id', 'status' => 'Dalam Pemulihan', 'status_class' => 'warn', 'cek_terakhir' => '12 menit lalu',
                'cpu' => 55, 'ram' => 62, 'storage' => 58, 'network' => 15.8,
                'uptime_24h' => 94.50, 'uptime_30h' => 98.70, 'avg_response' => 410,
            ],
            [
                'nama' => 'Portal Data Kodim', 'url' => 'data-kodim.mil.id', 'status' => 'Normal', 'status_class' => 'ok', 'cek_terakhir' => '20 menit lalu',
                'cpu' => 22, 'ram' => 40, 'storage' => 51, 'network' => 4.1,
                'uptime_24h' => 100.00, 'uptime_30h' => 99.98, 'avg_response' => 95,
            ],
            [
                'nama' => 'Email Gateway', 'url' => 'mail.pussiberad.mil.id', 'status' => 'Normal', 'status_class' => 'ok', 'cek_terakhir' => '25 menit lalu',
                'cpu' => 15, 'ram' => 28, 'storage' => 38, 'network' => 3.5,
                'uptime_24h' => 100.00, 'uptime_30h' => 100.00, 'avg_response' => 80,
            ],
        ];

        // ===== Laporan Monitoring & Recovery ke DANPUS (REAL, dari DB) =====
        // Fitur inti Satlakal: Buat Laporan, Draft, Riwayat, Status, Upload
        // Lampiran, dan Detail Laporan (semua berbasis tabel ini).
        $semuaLaporanMonitoring = LaporanMonitoring::where('satuan_id', $satuan->id)
            ->with('lampiran', 'tujuanSatuan')
            ->orderByDesc('created_at')
            ->get();

        $draftLaporanMonitoring = $semuaLaporanMonitoring->whereIn('status', ['Draft', 'Direvisi'])->values();
        $statusLaporanMonitoring = $semuaLaporanMonitoring->where('status', '!=', 'Draft')->values();

        // Dibangun di sini (bukan langsung di dalam @json() pada Blade) karena
        // Blade meng-explode isi @json(...) berdasarkan koma untuk mencari
        // parameter options/depth. Array kompleks dengan banyak koma di dalam
        // @json() akan membuat hasil kompilasinya rusak (ParseError).
        $laporanMonitoringData = $semuaLaporanMonitoring->keyBy('id')->map(function ($l) {
            return [
                'aset' => $l->aset ?? '—',
                'jenis_insiden' => $l->jenis_insiden ?? '—',
                'perihal' => $l->perihal,
                'status' => $l->status,
                'prioritas' => $l->prioritas,
                'tanggal' => $l->tanggal_kirim?->translatedFormat('d M Y H:i') ?? '—',
                'deskripsi' => $l->deskripsi,
                'tindakan' => $l->tindakan ?? '—',
                'catatan_danpus' => $l->catatan_danpus,
                'lampiran' => $l->lampiran->map(fn ($d) => [
                    'nama' => $d->nama_file,
                    'url' => asset('storage/'.$d->path),
                ]),
            ];
        });

        // Notifikasi status laporan (Disetujui/Ditolak/Direvisi) untuk user
        // Satlakal yang sedang login — ditampilkan di lonceng notifikasi topbar.
        $notifikasi = $user->unreadNotifications;

        return view('siberad.dashboards.satlakal', [
            'user' => $user,
            'satuan' => $satuan,
            'notifikasi' => $notifikasi,
            'asetMonitoring' => $asetMonitoring,

            'semuaLaporanMonitoring' => $semuaLaporanMonitoring,
            'draftLaporanMonitoring' => $draftLaporanMonitoring,
            'statusLaporanMonitoring' => $statusLaporanMonitoring,
            'laporanMonitoringData' => $laporanMonitoringData,
            'statsLaporanMonitoring' => [
                'total' => $semuaLaporanMonitoring->count(),
                'draft' => $semuaLaporanMonitoring->where('status', 'Draft')->count(),
                'dikirim' => $semuaLaporanMonitoring->where('status', 'Dikirim')->count(),
                'disetujui' => $semuaLaporanMonitoring->where('status', 'Disetujui')->count(),
            ],
            'stats' => [
                'total_aset' => count($asetMonitoring),
                'normal' => 3,
                'diserang' => 1,
                'pemulihan' => 1,
            ],
            // Ringkasan resource lintas-aset untuk kartu statistik di tab
            // "Monitoring Sistem". Dihitung sederhana dari $asetMonitoring di atas.
            'resourceSummary' => [
                'avg_cpu' => (int) round(array_sum(array_column($asetMonitoring, 'cpu')) / count($asetMonitoring)),
                'avg_ram' => (int) round(array_sum(array_column($asetMonitoring, 'ram')) / count($asetMonitoring)),
                'avg_storage' => (int) round(array_sum(array_column($asetMonitoring, 'storage')) / count($asetMonitoring)),
                'total_network' => round(array_sum(array_column($asetMonitoring, 'network')), 1),
                'avg_uptime_30h' => round(array_sum(array_column($asetMonitoring, 'uptime_30h')) / count($asetMonitoring), 2),
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
            // Rekap periodik (harian/mingguan/bulanan) untuk tab "Laporan Periodik".
            // Sumber data sementara masih statis; nanti diganti agregat query dari
            // tabel laporan/insiden begitu monitoring tersambung ke database.
            'laporanPeriodik' => [
                'harian' => [
                    ['periode' => '02 Agu 2026', 'total_insiden' => 2, 'diselesaikan' => 1, 'rata_waktu' => '45 menit', 'uptime' => '97.20%'],
                    ['periode' => '01 Agu 2026', 'total_insiden' => 1, 'diselesaikan' => 1, 'rata_waktu' => '20 menit', 'uptime' => '100.00%'],
                    ['periode' => '31 Jul 2026', 'total_insiden' => 0, 'diselesaikan' => 0, 'rata_waktu' => '-', 'uptime' => '100.00%'],
                    ['periode' => '30 Jul 2026', 'total_insiden' => 1, 'diselesaikan' => 1, 'rata_waktu' => '32 menit', 'uptime' => '99.80%'],
                ],
                'mingguan' => [
                    ['periode' => 'Minggu 5 (29 Jul – 04 Agu 2026)', 'total_insiden' => 4, 'diselesaikan' => 3, 'rata_waktu' => '38 menit', 'uptime' => '98.85%'],
                    ['periode' => 'Minggu 4 (22 – 28 Jul 2026)', 'total_insiden' => 2, 'diselesaikan' => 2, 'rata_waktu' => '25 menit', 'uptime' => '99.60%'],
                    ['periode' => 'Minggu 3 (15 – 21 Jul 2026)', 'total_insiden' => 3, 'diselesaikan' => 3, 'rata_waktu' => '29 menit', 'uptime' => '99.40%'],
                ],
                'bulanan' => [
                    ['periode' => 'Agustus 2026', 'total_insiden' => 4, 'diselesaikan' => 3, 'rata_waktu' => '38 menit', 'uptime' => '98.85%'],
                    ['periode' => 'Juli 2026', 'total_insiden' => 9, 'diselesaikan' => 9, 'rata_waktu' => '31 menit', 'uptime' => '99.42%'],
                    ['periode' => 'Juni 2026', 'total_insiden' => 6, 'diselesaikan' => 6, 'rata_waktu' => '27 menit', 'uptime' => '99.55%'],
                ],
            ],
        ]);
    }

    /**
     * Satlak Sibersos — pengelolaan & pemantauan media sosial di daerah.
     */
    private function satlakSibersos($user, $satuan): View
    {
        // ===== Laporan Publikasi ke DANPUS (REAL, dari DB) =====
        // Satu-satunya fitur inti Satlak Sibersos sekarang: Dashboard, Buat
        // Laporan Publikasi, Draft Laporan, Status Laporan, Riwayat Laporan,
        // Upload Dokumentasi, dan Detail Laporan (semua berbasis tabel ini).
        $semuaLaporanPublikasi = LaporanPublikasi::where('satuan_id', $satuan->id)
            ->with('dokumentasi', 'tujuanSatuan')
            ->orderByDesc('created_at')
            ->get();

        $draftLaporanPublikasi = $semuaLaporanPublikasi->where('status', 'Draft')->values();
        $statusLaporanPublikasi = $semuaLaporanPublikasi->where('status', '!=', 'Draft')->values();

        // Dibangun di sini (bukan langsung di dalam @json() pada Blade) karena
        // Blade meng-explode isi @json(...) berdasarkan koma untuk mencari
        // parameter options/depth. Array kompleks dengan banyak koma di dalam
        // @json() akan membuat hasil kompilasinya rusak (ParseError).
        $laporanPublikasiData = $semuaLaporanPublikasi->keyBy('id')->map(function ($l) {
            return [
                'judul' => $l->judul,
                'platform' => $l->platform ?? '—',
                'status' => $l->status,
                'tanggal' => $l->tanggal_kirim?->translatedFormat('d M Y H:i') ?? '—',
                'link' => $l->link_publikasi,
                'deskripsi' => $l->deskripsi,
                'dokumentasi' => $l->dokumentasi->map(fn ($d) => [
                    'nama' => $d->nama_file,
                    'url' => asset('storage/'.$d->path),
                ]),
            ];
        });

        return view('siberad.dashboards.satlaksibersos', [
            'user' => $user,
            'satuan' => $satuan,

            'stats' => [
                'total_laporan' => $semuaLaporanPublikasi->count(),
                'draft' => $draftLaporanPublikasi->count(),
                'menunggu' => $semuaLaporanPublikasi->where('status', 'Menunggu')->count(),
                'disetujui' => $semuaLaporanPublikasi->where('status', 'Disetujui DANPUS')->count(),
            ],

            'semuaLaporanPublikasi' => $semuaLaporanPublikasi,
            'draftLaporanPublikasi' => $draftLaporanPublikasi,
            'statusLaporanPublikasi' => $statusLaporanPublikasi,
            'laporanPublikasiData' => $laporanPublikasiData,
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
            ['nama' => 'Gateway Email satlak Duktek (Dukungan Teknologi)', 'jenis' => 'Phishing campaign', 'tingkat' => 'Sedang', 'tingkat_class' => 'warn', 'terdeteksi' => '1 jam lalu'],
            ['nama' => 'Jaringan Internal SDIR', 'jenis' => 'Percobaan brute force', 'tingkat' => 'Rendah', 'tingkat_class' => 'ok', 'terdeteksi' => '3 jam lalu'],
        ];

        $logPenanganan = [
            ['aset' => 'Server File Sharing Ditjen', 'jenis' => 'Ransomware (Lockbit variant)', 'waktu' => '02 Agu 2026, 09:32', 'tindakan' => 'Isolasi jaringan, forensik disk, siapkan restore dari backup', 'status' => 'Berlangsung', 'status_class' => 'amber'],
            ['aset' => 'Endpoint Staf Binmat #14', 'jenis' => 'Malware (Trojan)', 'waktu' => '02 Agu 2026, 09:00', 'tindakan' => 'Karantina endpoint, scan menyeluruh, reset kredensial', 'status' => 'Dalam Penanganan', 'status_class' => 'amber'],
            ['aset' => 'Gateway Email satlak Duktek (Dukungan Teknologi)', 'jenis' => 'Phishing campaign', 'waktu' => '02 Agu 2026, 08:20', 'tindakan' => 'Blokir domain pengirim, edukasi pengguna terdampak', 'status' => 'Selesai', 'status_class' => 'green'],
            ['aset' => 'Portal Data Kodim', 'jenis' => 'Defacement (rujukan Satlakal (Penangkalan))', 'waktu' => '28 Jul 2026, 15:10', 'tindakan' => 'Analisis malware yang disisipkan, patch celah upload', 'status' => 'Selesai', 'status_class' => 'green'],
        ];

        // Data komposisi untuk 3 grafik mini di dashboard — diturunkan dari
        // daftar ancaman & log penanganan yang sama, bukan angka acak baru.
        $ancamanPerTingkat = collect($ancamanTerdeteksi)
            ->groupBy('tingkat')
            ->map(fn ($g, $tingkat) => ['tingkat' => $tingkat, 'jumlah' => $g->count()])
            ->values();
        $jenisAncamanChart = collect($ancamanTerdeteksi)
            ->groupBy('jenis')
            ->map(fn ($g, $jenis) => ['jenis' => $jenis, 'jumlah' => $g->count()])
            ->values();
        $statusPenangananChart = collect($logPenanganan)
            ->groupBy('status')
            ->map(fn ($g, $status) => ['status' => $status, 'jumlah' => $g->count()])
            ->values();

        return view('siberad.dashboards.satlakrindak', [
            'user' => $user,
            'satuan' => $satuan,
            'ancamanTerdeteksi' => $ancamanTerdeteksi,
            'ancamanPerTingkat' => $ancamanPerTingkat,
            'jenisAncamanChart' => $jenisAncamanChart,
            'statusPenangananChart' => $statusPenangananChart,
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
            'logPenanganan' => $logPenanganan,
            'laporanPiket' => [
                ['aset' => 'Server File Sharing Ditjen', 'perihal' => 'Indikasi ransomware mengenkripsi file bersama', 'pelapor' => 'Piket Satlak Penindakan', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad'],
                ['aset' => 'Endpoint Staf Binmat #14', 'perihal' => 'Trojan terdeteksi via antivirus terpusat', 'pelapor' => 'Piket Satlak Penindakan', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn'],
            ],
            'investigasi' => [
                ['kasus' => 'INV-2026-014', 'aset' => 'Server File Sharing Ditjen', 'jenis' => 'Ransomware (Lockbit variant)', 'investigator' => 'Serma Ridho Pratama', 'mulai' => '02 Agu 2026', 'progres' => 65, 'status' => 'Berlangsung', 'status_class' => 'amber'],
                ['kasus' => 'INV-2026-013', 'aset' => 'Endpoint Staf Binmat #14', 'jenis' => 'Malware (Trojan)', 'investigator' => 'Sertu Dimas Aditya', 'mulai' => '02 Agu 2026', 'progres' => 40, 'status' => 'Berlangsung', 'status_class' => 'amber'],
                ['kasus' => 'INV-2026-012', 'aset' => 'Gateway Email satlak Duktek (Dukungan Teknologi)', 'jenis' => 'Phishing campaign', 'investigator' => 'Serma Ridho Pratama', 'mulai' => '02 Agu 2026', 'progres' => 100, 'status' => 'Selesai', 'status_class' => 'green'],
                ['kasus' => 'INV-2026-009', 'aset' => 'Portal Data Kodim', 'jenis' => 'Defacement', 'investigator' => 'Sertu Dimas Aditya', 'mulai' => '28 Jul 2026', 'progres' => 100, 'status' => 'Selesai', 'status_class' => 'green'],
            ],
            'timelinePenanganan' => [
                ['waktu' => '02 Agu 2026, 09:32', 'judul' => 'Insiden Terdeteksi', 'deskripsi' => 'Sistem monitoring mendeteksi aktivitas enkripsi massal pada Server File Sharing Ditjen.', 'state' => 'done'],
                ['waktu' => '02 Agu 2026, 09:40', 'judul' => 'Isolasi Jaringan', 'deskripsi' => 'Server diisolasi dari jaringan untuk mencegah penyebaran ransomware ke sistem lain.', 'state' => 'done'],
                ['waktu' => '02 Agu 2026, 10:15', 'judul' => 'Forensik & Analisis', 'deskripsi' => 'Tim investigasi melakukan analisis disk image untuk identifikasi varian ransomware.', 'state' => 'active'],
                ['waktu' => 'Menyusul', 'judul' => 'Pemulihan dari Backup', 'deskripsi' => 'Restorasi data dari backup terakhir setelah sistem dipastikan bersih.', 'state' => 'pending'],
                ['waktu' => 'Menyusul', 'judul' => 'Laporan Akhir & Penutupan Kasus', 'deskripsi' => 'Penyusunan laporan investigasi lengkap dan rekomendasi mitigasi jangka panjang.', 'state' => 'pending'],
            ],
            'mitigasi' => [
                ['aset' => 'Server File Sharing Ditjen', 'ancaman' => 'Ransomware (Lockbit variant)', 'tindakan' => 'Perkuat segmentasi jaringan & aktifkan backup terenkripsi harian', 'penanggung_jawab' => 'Serma Ridho Pratama', 'tenggat' => '10 Agu 2026', 'status' => 'Berjalan', 'status_class' => 'amber'],
                ['aset' => 'Endpoint Staf Binmat #14', 'ancaman' => 'Malware (Trojan)', 'tindakan' => 'Update signature antivirus terpusat & audit endpoint lain', 'penanggung_jawab' => 'Sertu Dimas Aditya', 'tenggat' => '08 Agu 2026', 'status' => 'Berjalan', 'status_class' => 'amber'],
                ['aset' => 'Gateway Email satlak Duktek (Dukungan Teknologi)', 'ancaman' => 'Phishing campaign', 'tindakan' => 'Blokir domain pengirim & edukasi pengguna terdampak', 'penanggung_jawab' => 'Serma Ridho Pratama', 'tenggat' => '05 Agu 2026', 'status' => 'Selesai', 'status_class' => 'green'],
            ],
        ]);
    }

    /**
     * satlak Duktek (Dukungan Teknologi) — riset & pengembangan teknologi (AI, drone, dll).
     */
    private function satlakDuktek($user, $satuan): View
    {
        // Proyek riset & pengembangan (AI, drone, keamanan siber, dll) — dulunya
        // data dummy, sekarang CRUD nyata lewat tab "Riset & Pengembangan".
        $proyekRiset = ProyekRiset::where('satuan_id', $satuan->id)
            ->orderByDesc('created_at')
            ->get();

        // Distribusi kategori proyek — untuk grafik komposisi riset.
        $kategoriDistribusi = $proyekRiset
            ->groupBy('kategori')
            ->map(fn ($group, $kategori) => ['kategori' => $kategori, 'jumlah' => $group->count()])
            ->values();

        // Perbandingan progres antar Satlak — punya Duktek dihitung dari rata-rata
        // progres proyek riset asli; 3 satlak lain tetap perbandingan simulasi
        // karena metrik progres mereka bukan bagian dari modul ini.
        $perbandinganSatlak = [
            ['nama' => 'Duktek (Bangtek)', 'singkatan' => 'Bangtek', 'progres' => $proyekRiset->isNotEmpty() ? round($proyekRiset->avg('progres')) : 0, 'highlight' => true],
            ['nama' => 'Penangkalan (Almon)', 'singkatan' => 'Almon', 'progres' => 60, 'highlight' => false],
            ['nama' => 'Sosial Media (Sibersos)', 'singkatan' => 'Sibersos', 'progres' => 67, 'highlight' => false],
            ['nama' => 'Penindakan (Rindak)', 'singkatan' => 'Rindak', 'progres' => 50, 'highlight' => false],
        ];

        // Laporan yang sudah pernah dikirim satuan ini ke DANPUS — dipakai di
        // tab "Status Laporan" dan "Riwayat Laporan", jadi bukan data dummy lagi.
        $laporanTerkirim = Laporan::where('satuan_id', $satuan->id)
            ->latest()
            ->get();

        // Log uji & pengembangan — riwayat pengujian prototipe per proyek riset.
        $logUji = LogUjiPengembangan::with('proyekRiset')
            ->where('satuan_id', $satuan->id)
            ->latest('waktu_uji')
            ->get();

        // Log dukungan teknis ke 3 Satlak operasional lain (Satlakal, Sibersos, Rindak).
        $satuanTujuanDukungan = Satuan::whereIn('kode', ['SATLAKKAL', 'SATLAKSISOS', 'SATLAKDAK'])
            ->orderBy('urutan')
            ->get();
        $dukunganTeknis = DukunganTeknisLog::with('satuanTujuan')
            ->where('satuan_id', $satuan->id)
            ->latest()
            ->get();

        return view('siberad.dashboards.satlakduktek', [
            'user' => $user,
            'satuan' => $satuan,
            'proyekRiset' => $proyekRiset,
            'laporanTerkirim' => $laporanTerkirim,
            'kategoriDistribusi' => $kategoriDistribusi,
            'perbandinganSatlak' => $perbandinganSatlak,
            'stats' => [
                'proyek_aktif' => $proyekRiset->where('status', '!=', 'Selesai')->count(),
                'proyek_ai' => $proyekRiset->filter(fn ($p) => str_contains(strtolower($p->kategori), 'ai'))->count(),
                'unit_drone_uji' => $logUji->count(),
                'prototipe_selesai' => $proyekRiset->where('status', 'Selesai')->count(),
            ],
            'aktivitasTerbaru' => $logUji->take(2)->map(fn ($l) => [
                'proyek' => $l->proyekRiset->nama ?? '-',
                'kegiatan' => $l->kegiatan,
                'waktu' => $l->waktu_uji?->diffForHumans() ?? '-',
                'status' => $l->status,
                'status_class' => $l->status === 'Selesai' ? 'ok' : 'warn',
            ]),
            'logUji' => $logUji,
            'satuanTujuanDukungan' => $satuanTujuanDukungan,
            'dukunganTeknis' => $dukunganTeknis,
        ]);
    }

    /**
     * Binfung — pembinaan fungsi: Administrasi Personel (Data Personel,
     * Tambah/Edit Personel, Mutasi, Pangkat, Jabatan, Satuan, Upload
     * Dokumen, Riwayat) dan Laporan. Seluruh data di bawah ini sudah
     * ditarik langsung dari database (tabel personels, personel_mutasis,
     * personel_dokumens, pangkats, jabatans, satuans) — tidak ada lagi
     * data dummy untuk modul ini.
     */
    private function binfung($user, $satuan): View
    {
        $semuaPersonel = Personel::with(['pangkat', 'jabatan', 'satuan'])
            ->orderByDesc('created_at')
            ->get();

        $semuaPangkat = Pangkat::withCount('personels')->orderBy('urutan')->get();
        $semuaJabatan = Jabatan::withCount('personels')->orderBy('nama')->get();
        $semuaSatuanRef = Satuan::withCount('personels')->orderBy('urutan')->get();

        $semuaMutasi = PersonelMutasi::with(['personel', 'satuanAsal', 'satuanTujuan', 'jabatanAsal', 'jabatanTujuan'])
            ->orderByDesc('created_at')
            ->get();

        $mutasiMenunggu = $semuaMutasi->where('status', PersonelMutasi::STATUS_MENUNGGU);

        $semuaDokumen = PersonelDokumen::with('personel')
            ->orderByDesc('created_at')
            ->get();

        // ===== Riwayat gabungan: personel baru, mutasi, dan upload dokumen =====
        $riwayat = collect()
            ->concat($semuaPersonel->map(fn (Personel $p) => [
                'jenis' => 'Personel Baru',
                'nama' => $p->nama,
                'keterangan' => 'Data personel baru dicatat'.($p->satuan ? ' di '.$p->satuan->nama : ''),
                'tanggal' => $p->created_at,
                'status_class' => 'ok',
            ]))
            ->concat($semuaMutasi->map(fn (PersonelMutasi $m) => [
                'jenis' => 'Mutasi',
                'nama' => $m->personel->nama ?? '-',
                'keterangan' => 'Mutasi ke '.($m->satuanTujuan->nama ?? '-').' — '.$m->status,
                'tanggal' => $m->created_at,
                'status_class' => match ($m->status) {
                    PersonelMutasi::STATUS_DISETUJUI => 'ok',
                    PersonelMutasi::STATUS_DITOLAK => 'bad',
                    default => 'warn',
                },
            ]))
            ->concat($semuaDokumen->map(fn (PersonelDokumen $d) => [
                'jenis' => 'Dokumen',
                'nama' => $d->personel->nama ?? '-',
                'keterangan' => 'Unggah dokumen '.$d->jenis_dokumen.' ('.$d->nama_file.')',
                'tanggal' => $d->created_at,
                'status_class' => 'ok',
            ]))
            ->sortByDesc('tanggal')
            ->values();

        // ===== Grafik 1: Status personel (Aktif / Mutasi / Purna) =====
        $statusPenempatan = collect([Personel::STATUS_AKTIF, Personel::STATUS_MUTASI, Personel::STATUS_PURNA])
            ->map(fn ($s) => ['label' => $s, 'jumlah' => $semuaPersonel->where('status', $s)->count()]);

        // ===== Grafik 2: Personel per satuan =====
        $penempatanSatuan = $semuaSatuanRef
            ->map(fn (Satuan $s) => ['satuan' => $s->nama, 'jumlah' => $s->personels_count])
            ->filter(fn ($row) => $row['jumlah'] > 0)
            ->values();

        // ===== Grafik 3: Distribusi jabatan =====
        $distribusiJabatan = $semuaJabatan
            ->map(fn (Jabatan $j) => ['jabatan' => $j->nama, 'jumlah' => $j->personels_count])
            ->filter(fn ($row) => $row['jumlah'] > 0)
            ->values();

        return view('siberad.dashboards.binfung', [
            'user' => $user,
            'satuan' => $satuan,
            'semuaPersonel' => $semuaPersonel,
            'semuaPangkat' => $semuaPangkat,
            'semuaJabatan' => $semuaJabatan,
            'semuaSatuanRef' => $semuaSatuanRef,
            'semuaMutasi' => $semuaMutasi,
            'mutasiMenunggu' => $mutasiMenunggu,
            'semuaDokumen' => $semuaDokumen,
            'riwayat' => $riwayat,
            'stats' => [
                'personel_masuk_bulan_ini' => $semuaPersonel->where('created_at', '>=', now()->startOfMonth())->count(),
                'menunggu_sk' => $mutasiMenunggu->count(),
                'satuan_terisi' => $semuaSatuanRef->where('personels_count', '>', 0)->count(),
                'total_personel' => $semuaPersonel->count(),
            ],
            'statusPenempatanChart' => $statusPenempatan,
            'penempatanPerSatuanChart' => $penempatanSatuan,
            'jabatanChart' => $distribusiJabatan,
            'laporanPiket' => $mutasiMenunggu->map(fn (PersonelMutasi $m) => [
                'nama' => $m->personel->nama ?? '-',
                'perihal' => 'Pengajuan mutasi ke '.($m->satuanTujuan->nama ?? '-'),
                'pelapor' => 'Piket Binfung',
                'tanggal' => $m->created_at->translatedFormat('d M Y'),
                'prioritas' => 'Sedang',
                'prioritas_class' => 'warn',
            ])->values(),
        ]);
    }

    /**
     * Binum — pembinaan umum: pengawasan satuan, lomba internal, personel baru.
     */
    private function binum($user, $satuan): View
    {
        $pengawasanSatuan = [
            ['satuan' => 'Satlak Penindakan', 'aspek' => 'Kepatuhan SOP penanganan insiden', 'hasil' => 'Baik', 'hasil_class' => 'ok', 'tanggal' => '01 Agu 2026'],
            ['satuan' => 'Satlak Sibersos', 'aspek' => 'Disiplin pelaporan harian', 'hasil' => 'Perlu Perbaikan', 'hasil_class' => 'warn', 'tanggal' => '30 Jul 2026'],
            ['satuan' => 'Binmat', 'aspek' => 'Administrasi inventaris', 'hasil' => 'Baik', 'hasil_class' => 'ok', 'tanggal' => '28 Jul 2026'],
            ['satuan' => 'satlak Duktek (Dukungan Teknologi)', 'aspek' => 'Keamanan dokumen riset', 'hasil' => 'Pelanggaran Ringan', 'hasil_class' => 'bad', 'tanggal' => '25 Jul 2026'],
        ];

        return view('siberad.dashboards.binum', [
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
                ['satuan' => 'satlak Duktek (Dukungan Teknologi)', 'kegiatan' => 'Temuan pelanggaran ringan keamanan dokumen riset', 'waktu' => '3 hari lalu', 'status' => 'Ditindaklanjuti', 'status_class' => 'warn'],
                ['satuan' => 'Satlak Sibersos', 'kegiatan' => 'Evaluasi disiplin pelaporan harian', 'waktu' => '5 hari lalu', 'status' => 'Selesai', 'status_class' => 'ok'],
            ],
            'lombaInternal' => [
                ['nama' => 'Lomba Satuan Terbaik Triwulan III', 'peserta' => '11 Satuan', 'periode' => 'Jul – Sep 2026', 'status' => 'Berlangsung', 'status_class' => 'amber'],
                ['nama' => 'Lomba Kedisiplinan Pelaporan', 'peserta' => '11 Satuan', 'periode' => 'Apr – Jun 2026', 'status' => 'Selesai', 'status_class' => 'green'],
            ],
            'laporanPiket' => [
                ['satuan' => 'satlak Duktek (Dukungan Teknologi)', 'perihal' => 'Temuan pelanggaran ringan keamanan dokumen riset', 'pelapor' => 'Piket Binum', 'tanggal' => '25 Jul 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn'],
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
            ['nama' => 'Drone Uji satlak Duktek (Dukungan Teknologi)', 'kategori' => 'Alat Khusus', 'jumlah' => 4, 'kondisi' => 'Perlu Perawatan', 'kondisi_class' => 'warn', 'update' => 'Kemarin'],
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
                ['item' => 'Drone Uji satlak Duktek (Dukungan Teknologi)', 'kegiatan' => 'Jadwal perawatan berkala unit drone', 'waktu' => 'Kemarin', 'status' => 'Perlu Perawatan', 'status_class' => 'warn'],
            ],
            'permintaanPengadaan' => [
                ['item' => 'Suku cadang Server Rack Pusat Data', 'diajukan_oleh' => 'Binmat', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Tinggi', 'prioritas_class' => 'bad', 'status' => 'Menunggu Persetujuan', 'status_class' => 'amber'],
                ['item' => 'Komponen gimbal kamera drone', 'diajukan_oleh' => 'satlak Duktek (Dukungan Teknologi)', 'tanggal' => '02 Agu 2026', 'prioritas' => 'Sedang', 'prioritas_class' => 'warn', 'status' => 'Menunggu Persetujuan', 'status_class' => 'amber'],
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
            ['satuan' => 'satlak Duktek (Dukungan Teknologi)', 'perihal' => 'Permintaan pengiriman 2 personel untuk riset AI', 'jenis' => 'Permintaan Personel', 'tanggal' => '30 Jul 2026', 'status' => 'Menunggu Binfung', 'status_class' => 'amber'],
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
                ['satuan' => 'satlak Duktek (Dukungan Teknologi)', 'kegiatan' => 'Meneruskan permintaan personel ke Binfung', 'waktu' => 'Kemarin', 'status' => 'Menunggu', 'status_class' => 'amber'],
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