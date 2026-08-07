<?php

namespace App\Http\Controllers;

use App\Models\Pangkat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PangkatController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:pangkats,kode'],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:Tamtama,Bintara,Perwira'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        Pangkat::create($validated);

        return back()->with('status', 'Data pangkat berhasil ditambahkan.');
    }

    public function update(Request $request, Pangkat $pangkat): RedirectResponse
    {
        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:20', 'unique:pangkats,kode,'.$pangkat->id],
            'nama' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:Tamtama,Bintara,Perwira'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);

        $pangkat->update($validated);

        return back()->with('status', 'Data pangkat berhasil diperbarui.');
    }

    public function destroy(Pangkat $pangkat): RedirectResponse
    {
        if ($pangkat->personels()->exists()) {
            return back()->with('error', 'Pangkat masih dipakai oleh data personel dan tidak bisa dihapus.');
        }

        $pangkat->delete();

        return back()->with('status', 'Data pangkat berhasil dihapus.');
    }
}
