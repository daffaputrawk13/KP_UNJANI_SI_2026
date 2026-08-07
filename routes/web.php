<?php

use App\Http\Controllers\AkunMedsosController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PangkatController;
use App\Http\Controllers\PersonelController;
use App\Http\Controllers\PersonelDokumenController;
use App\Http\Controllers\PersonelMutasiController;
use App\Http\Controllers\PostinganController;
use App\Models\Satuan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Satuan dikelompokkan per kategori untuk ditampilkan di dropdown login.
    $satuans = Satuan::orderBy('urutan')->get()->groupBy('kategori');

    return view('siberad.landing.welcome', ['satuans' => $satuans]);
});

// Form login berada di landing page (modal), bukan halaman tersendiri.
// Route GET ini mencegah error 405 kalau ada yang membuka /login langsung
// atau saat middleware auth mengarahkan pengguna yang belum login ke sini.
Route::get('/login', function () {
    return redirect('/');
});

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

// Kirim laporan dari satuan pengirim (mis. Satlok Duktek/Bangtek) ke DANPUS,
// sekaligus memicu notifikasi database ke seluruh akun DANPUS.
Route::post('/laporan', [LaporanController::class, 'store'])
    ->middleware('auth')
    ->name('laporan.store');

// Hapus laporan dari riwayat — hanya satuan tujuan (mis. DANPUS) yang boleh
// menghapus laporan yang ditujukan kepadanya (dicek di controller).
Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])
    ->middleware('auth')
    ->name('laporan.destroy');

Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])
    ->middleware('auth')
    ->name('notifikasi.baca-semua');

// ===== Manajemen Akun Media Sosial =====
// Akun resmi yang dikelola satuan (mis. Instagram resmi Satlak Sibersos),
// dipakai fitur "Manajemen Akun Media Sosial".
Route::post('/akun-medsos', [AkunMedsosController::class, 'store'])
    ->middleware('auth')
    ->name('akun-medsos.store');

Route::patch('/akun-medsos/{akunMedsos}', [AkunMedsosController::class, 'update'])
    ->middleware('auth')
    ->name('akun-medsos.update');

Route::delete('/akun-medsos/{akunMedsos}', [AkunMedsosController::class, 'destroy'])
    ->middleware('auth')
    ->name('akun-medsos.destroy');

// ===== Postingan Media Sosial =====
// Mencakup fitur "Membuat Posting", "Menjadwalkan Posting", "Kalender
// Konten", "Upload Foto/Video", "Monitoring Engagement", "Statistik
// Performa", dan "Arsip Seluruh Posting".
Route::post('/posting', [PostinganController::class, 'store'])
    ->middleware('auth')
    ->name('posting.store');

Route::post('/posting/{posting}/terbitkan', [PostinganController::class, 'terbitkan'])
    ->middleware('auth')
    ->name('posting.terbitkan');

Route::patch('/posting/{posting}/engagement', [PostinganController::class, 'updateEngagement'])
    ->middleware('auth')
    ->name('posting.engagement');

Route::delete('/posting/{posting}', [PostinganController::class, 'destroy'])
    ->middleware('auth')
    ->name('posting.destroy');

// ===== Administrasi Personel (Binfung) =====
// Mencakup fitur "Data Personel", "Tambah/Edit Personel", "Mutasi",
// "Pangkat", "Jabatan", "Satuan" (referensi), "Upload Dokumen", dan
// "Riwayat" pada dashboard Binfung.
Route::post('/personel', [PersonelController::class, 'store'])
    ->middleware('auth')
    ->name('personel.store');

Route::patch('/personel/{personel}', [PersonelController::class, 'update'])
    ->middleware('auth')
    ->name('personel.update');

Route::delete('/personel/{personel}', [PersonelController::class, 'destroy'])
    ->middleware('auth')
    ->name('personel.destroy');

Route::post('/pangkat', [PangkatController::class, 'store'])
    ->middleware('auth')
    ->name('pangkat.store');

Route::patch('/pangkat/{pangkat}', [PangkatController::class, 'update'])
    ->middleware('auth')
    ->name('pangkat.update');

Route::delete('/pangkat/{pangkat}', [PangkatController::class, 'destroy'])
    ->middleware('auth')
    ->name('pangkat.destroy');

Route::post('/jabatan', [JabatanController::class, 'store'])
    ->middleware('auth')
    ->name('jabatan.store');

Route::patch('/jabatan/{jabatan}', [JabatanController::class, 'update'])
    ->middleware('auth')
    ->name('jabatan.update');

Route::delete('/jabatan/{jabatan}', [JabatanController::class, 'destroy'])
    ->middleware('auth')
    ->name('jabatan.destroy');

Route::post('/personel-mutasi', [PersonelMutasiController::class, 'store'])
    ->middleware('auth')
    ->name('personel-mutasi.store');

Route::patch('/personel-mutasi/{mutasi}', [PersonelMutasiController::class, 'update'])
    ->middleware('auth')
    ->name('personel-mutasi.update');

Route::delete('/personel-mutasi/{mutasi}', [PersonelMutasiController::class, 'destroy'])
    ->middleware('auth')
    ->name('personel-mutasi.destroy');

Route::post('/personel-dokumen', [PersonelDokumenController::class, 'store'])
    ->middleware('auth')
    ->name('personel-dokumen.store');

Route::delete('/personel-dokumen/{dokumen}', [PersonelDokumenController::class, 'destroy'])
    ->middleware('auth')
    ->name('personel-dokumen.destroy');