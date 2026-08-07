<?php

namespace App\Http\Controllers;

use App\Models\PersonelDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonelDokumenController extends Controller
{
    /**
     * Unggah dokumen administrasi personel (mis. SK, KTP, Ijazah) — dipakai
     * fitur "Upload Dokumen" pada Administrasi Personel Binfung.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'personel_id' => ['required', 'exists:personels,id'],
            'jenis_dokumen' => ['required', 'string', 'max:255'],
            'dokumen' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $file = $request->file('dokumen');
        $path = $file->store('dokumen-personel', 'public');

        PersonelDokumen::create([
            'personel_id' => $validated['personel_id'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'nama_file' => $file->getClientOriginalName(),
            'path' => $path,
            'diunggah_oleh' => $request->user()->id,
        ]);

        return back()->with('status', 'Dokumen personel berhasil diunggah.');
    }

    public function destroy(PersonelDokumen $dokumen): RedirectResponse
    {
        Storage::disk('public')->delete($dokumen->path);
        $dokumen->delete();

        return back()->with('status', 'Dokumen personel berhasil dihapus.');
    }
}
