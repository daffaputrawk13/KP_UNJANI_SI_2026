<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    /**
     * Paksa logout satu sesi (satu perangkat/browser) dengan menghapus
     * baris session-nya di tabel `sessions` (SESSION_DRIVER=database).
     * Menghapus baris ini membuat sesi itu langsung tidak valid di sisi
     * server, walau cookie di browser pengguna masih ada.
     */
    public function destroy(string $id): RedirectResponse
    {
        $sesi = DB::table('sessions')->where('id', $id)->first();

        if (! $sesi) {
            return back()->with('status', 'Sesi tidak ditemukan (mungkin sudah berakhir).');
        }

        $namaPengguna = $sesi->user_id
            ? DB::table('users')->where('id', $sesi->user_id)->value('name')
            : null;

        DB::table('sessions')->where('id', $id)->delete();

        ActivityLog::catat(
            'session.force_logout',
            'Memaksa logout sesi milik ' . ($namaPengguna ?? 'pengguna tidak dikenal') . " (IP: {$sesi->ip_address})."
        );

        return back()->with('status', 'Sesi berhasil dipaksa logout.');
    }
}
