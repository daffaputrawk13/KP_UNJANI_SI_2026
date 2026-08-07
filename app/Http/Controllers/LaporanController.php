<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Satuan;
use App\Models\User;
use App\Notifications\LaporanBaruDiterima;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    /**
     * Simpan laporan baru dari satuan pengirim (mis. Satuan Pelaksanaan Dukungan Teknologi)
     * dan kirim notifikasi database ke seluruh user yang terdaftar di DANPUS.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'proyek' => ['nullable', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'prioritas' => ['required', 'in:Tinggi,Sedang,Rendah'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        $user = $request->user()->load('satuan');
        $satuanAsal = $user->satuan;

        abort_unless($satuanAsal, 403, 'Akun ini belum terhubung ke satuan manapun.');

        $danpus = Satuan::where('kode', 'DANPUS')->firstOrFail();

        $lampiranPath = $request->hasFile('lampiran')
            ? $request->file('lampiran')->store('lampiran-laporan', 'public')
            : null;

        $laporan = Laporan::create([
            'satuan_id' => $satuanAsal->id,
            'user_id' => $user->id,
            'tujuan_satuan_id' => $danpus->id,
            'proyek' => $validated['proyek'] ?? null,
            'perihal' => $validated['perihal'],
            'deskripsi' => $validated['deskripsi'],
            'prioritas' => $validated['prioritas'],
            'lampiran_path' => $lampiranPath,
            'status' => 'Menunggu',
        ]);

        // Kirim notifikasi ke seluruh akun yang terdaftar di satuan DANPUS,
        // bukan cuma satu user — jaga-jaga kalau DANPUS punya lebih dari satu akun.
        $penerima = User::where('satuan_id', $danpus->id)->get();
        foreach ($penerima as $u) {
            $u->notify(new LaporanBaruDiterima($laporan));
        }

        return back()->with('status', 'Laporan berhasil dikirim ke DANPUS.');
    }

    /**
     * Hapus laporan dari riwayat (dipakai tab "Riwayat Laporan" di dashboard
     * DANPUS). Hanya user dari satuan tujuan laporan tersebut yang boleh
     * menghapusnya — mencegah satuan lain menghapus laporan yang bukan
     * ditujukan kepada mereka.
     */
    public function destroy(Request $request, Laporan $laporan): RedirectResponse
    {
        $user = $request->user()->load('satuan');
        $satuan = $user->satuan;

        abort_unless($satuan && $laporan->tujuan_satuan_id === $satuan->id, 403);

        if ($laporan->lampiran_path) {
            Storage::disk('public')->delete($laporan->lampiran_path);
        }

        $laporan->delete();

        return back()->with('status', 'Laporan berhasil dihapus dari riwayat.');
    }
}