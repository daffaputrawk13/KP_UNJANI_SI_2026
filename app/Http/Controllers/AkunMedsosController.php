<?php

namespace App\Http\Controllers;

use App\Models\AkunMedsos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AkunMedsosController extends Controller
{
    /**
     * Tambah akun media sosial resmi baru milik satuan yang sedang login
     * (mis. akun Instagram resmi Satlak Sibersos). Dipakai tab "Manajemen
     * Akun".
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_akun' => ['required', 'string', 'max:255'],
            'platform' => ['required', 'in:Instagram,Facebook,X (Twitter),TikTok,YouTube'],
            'username_platform' => ['required', 'string', 'max:255'],
            'url_profil' => ['nullable', 'url', 'max:255'],
            'foto_profil' => ['nullable', 'image', 'max:5120'],
        ]);

        $satuan = $request->user()->load('satuan')->satuan;
        abort_unless($satuan, 403, 'Akun ini belum terhubung ke satuan manapun.');

        $fotoPath = $request->hasFile('foto_profil')
            ? $request->file('foto_profil')->store('akun-medsos', 'public')
            : null;

        AkunMedsos::create([
            'satuan_id' => $satuan->id,
            'nama_akun' => $validated['nama_akun'],
            'platform' => $validated['platform'],
            'username_platform' => $validated['username_platform'],
            'url_profil' => $validated['url_profil'] ?? null,
            'foto_profil_path' => $fotoPath,
            'status' => 'Aktif',
        ]);

        return back()->with('status', 'Akun media sosial berhasil ditambahkan.');
    }

    /**
     * Ubah status akun (Aktif/Nonaktif) atau data dasarnya. Hanya satuan
     * pemilik akun yang boleh mengubahnya.
     */
    public function update(Request $request, AkunMedsos $akunMedsos): RedirectResponse
    {
        $satuan = $request->user()->load('satuan')->satuan;
        abort_unless($satuan && $akunMedsos->satuan_id === $satuan->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        $akunMedsos->update($validated);

        return back()->with('status', 'Status akun berhasil diperbarui.');
    }

    /**
     * Hapus akun media sosial. Seluruh postingan yang terkait ikut terhapus
     * (cascade di level database) karena tanpa akun, postingan jadi yatim.
     */
    public function destroy(Request $request, AkunMedsos $akunMedsos): RedirectResponse
    {
        $satuan = $request->user()->load('satuan')->satuan;
        abort_unless($satuan && $akunMedsos->satuan_id === $satuan->id, 403);

        if ($akunMedsos->foto_profil_path) {
            Storage::disk('public')->delete($akunMedsos->foto_profil_path);
        }

        foreach ($akunMedsos->postingan as $postingan) {
            if ($postingan->media_path) {
                Storage::disk('public')->delete($postingan->media_path);
            }
        }

        $akunMedsos->delete();

        return back()->with('status', 'Akun media sosial berhasil dihapus.');
    }
}
