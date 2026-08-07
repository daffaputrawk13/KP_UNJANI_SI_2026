<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tab "Laporan Pengguna & Aktivitas" — rekap pengguna per satuan dan
     * aktivitas terakhir, sumber untuk tombol export di bawah.
     */
    public function index(Request $request): View
    {
        $dariTanggal = $request->date('dari');
        $sampaiTanggal = $request->date('sampai');

        $log = ActivityLog::with('user')
            ->when($dariTanggal, fn ($q) => $q->whereDate('created_at', '>=', $dariTanggal))
            ->when($sampaiTanggal, fn ($q) => $q->whereDate('created_at', '<=', $sampaiTanggal))
            ->latest('created_at')
            ->limit(500)
            ->get();

        return view('admin.laporan', [
            'user' => $request->user()->load('satuan'),
            'satuan' => $request->user()->satuan,
            'pengaturan' => Pengaturan::current(),
            'semuaPengguna' => User::with('satuan')->orderBy('name')->get(),
            'log' => $log,
            'dari' => $dariTanggal?->format('Y-m-d'),
            'sampai' => $sampaiTanggal?->format('Y-m-d'),
        ]);
    }

    /**
     * Export daftar pengguna ke CSV (dibuka Excel/Sheets tanpa perlu
     * library tambahan).
     */
    public function exportUsersExcel()
    {
        $users = User::with('satuan')->orderBy('name')->get();

        $callback = function () use ($users) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nama', 'Username', 'Email', 'Satuan', 'Jabatan', 'Dibuat']);
            foreach ($users as $u) {
                fputcsv($out, [$u->name, $u->username, $u->email, $u->satuan->nama ?? '-', $u->jabatan, $u->created_at?->format('Y-m-d H:i')]);
            }
            fclose($out);
        };

        return ResponseFacade::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-pengguna-'.now()->format('Ymd_His').'.csv"',
        ]);
    }

    /**
     * Export log aktivitas ke CSV.
     */
    public function exportActivityExcel(Request $request)
    {
        $log = ActivityLog::with('user')->latest('created_at')->limit(2000)->get();

        $callback = function () use ($log) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Waktu', 'Pengguna', 'Aksi', 'Deskripsi', 'IP Address']);
            foreach ($log as $l) {
                fputcsv($out, [$l->created_at?->format('Y-m-d H:i:s'), $l->nama_pengguna, $l->aksi, $l->deskripsi, $l->ip_address]);
            }
            fclose($out);
        };

        return ResponseFacade::stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="log-aktivitas-'.now()->format('Ymd_His').'.csv"',
        ]);
    }

    /**
     * Versi cetak (untuk disimpan sebagai PDF lewat dialog "Print" browser)
     * dari laporan pengguna & aktivitas — tanpa perlu library PDF tambahan.
     */
    public function printView(Request $request): View
    {
        return view('admin.laporan-cetak', [
            'pengaturan' => Pengaturan::current(),
            'semuaPengguna' => User::with('satuan')->orderBy('name')->get(),
            'log' => ActivityLog::with('user')->latest('created_at')->limit(200)->get(),
            'dicetakOleh' => $request->user(),
            'dicetakPada' => now(),
        ]);
    }
}
