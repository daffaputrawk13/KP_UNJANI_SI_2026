<?php

namespace App\Http\Controllers;

use App\Models\ProyekRiset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProyekRisetController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'progres' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'in:Riset Awal,Berjalan,Selesai'],
            'target_selesai' => ['nullable', 'string', 'max:50'],
        ]);

        $satuan = $request->user()->load('satuan')->satuan;
        abort_unless($satuan, 403, 'Akun ini belum terhubung ke satuan manapun.');

        $validated['satuan_id'] = $satuan->id;

        ProyekRiset::create($validated);

        return back()->with('status', 'Proyek riset berhasil ditambahkan.');
    }

    public function update(Request $request, ProyekRiset $proyekRiset): RedirectResponse
    {
        $satuan = $request->user()->load('satuan')->satuan;
        abort_unless($satuan && $proyekRiset->satuan_id === $satuan->id, 403);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'progres' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => ['required', 'in:Riset Awal,Berjalan,Selesai'],
            'target_selesai' => ['nullable', 'string', 'max:50'],
        ]);

        $proyekRiset->update($validated);

        return back()->with('status', 'Proyek riset berhasil diperbarui.');
    }

    public function destroy(Request $request, ProyekRiset $proyekRiset): RedirectResponse
    {
        $satuan = $request->user()->load('satuan')->satuan;
        abort_unless($satuan && $proyekRiset->satuan_id === $satuan->id, 403);

        $proyekRiset->delete();

        return back()->with('status', 'Proyek riset berhasil dihapus.');
    }
}
