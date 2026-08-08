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
     * Satu alur laporan untuk seluruh satuan. Tujuan laporan dibatasi pada
     * satuan yang terdaftar di sistem; ADMIN tidak dapat menjadi tujuan laporan.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tujuan_satuan_id' => ['required', 'integer', 'exists:satuans,id'],
            'proyek' => ['nullable', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:10000'],
            'prioritas' => ['required', 'in:Tinggi,Sedang,Rendah'],
            'lampiran' => ['nullable', 'file', 'mimes:pdf', 'max:20480'],
        ]);

        $user = $request->user()->load('satuan');
        $satuanAsal = $user->satuan;
        abort_unless($satuanAsal, 403, 'Akun ini belum terhubung ke satuan manapun.');

        $tujuan = Satuan::findOrFail($validated['tujuan_satuan_id']);
        abort_if(strtoupper((string) $tujuan->kode) === 'ADMIN', 422, 'Laporan tidak dapat ditujukan ke Admin.');
        abort_if((int) $tujuan->id === (int) $satuanAsal->id, 422, 'Tujuan laporan tidak boleh sama dengan satuan pengirim.');

        $lampiranPath = $request->hasFile('lampiran')
            ? $request->file('lampiran')->store('lampiran-laporan', 'public')
            : null;

        $laporan = Laporan::create([
            'satuan_id' => $satuanAsal->id,
            'user_id' => $user->id,
            'tujuan_satuan_id' => $tujuan->id,
            'proyek' => $validated['proyek'] ?? null,
            'perihal' => $validated['perihal'],
            'deskripsi' => $validated['deskripsi'],
            'prioritas' => $validated['prioritas'],
            'lampiran_path' => $lampiranPath,
            'status' => 'Menunggu',
        ]);

        foreach (User::where('satuan_id', $tujuan->id)->get() as $penerima) {
            $penerima->notify(new LaporanBaruDiterima($laporan));
        }

        return back()->with('status', 'Laporan berhasil dikirim ke '.$tujuan->nama.'.');
    }

    public function destroy(Request $request, Laporan $laporan): RedirectResponse
    {
        $user = $request->user()->load('satuan');
        $satuan = $user->satuan;
        abort_unless($satuan && $laporan->tujuan_satuan_id === $satuan->id, 403);

        if ($laporan->lampiran_path) {
            Storage::disk('public')->delete($laporan->lampiran_path);
        }
        $laporan->delete();

        return back()->with('status', 'Laporan berhasil dihapus dari riwayat penerimaan.');
    }
}
