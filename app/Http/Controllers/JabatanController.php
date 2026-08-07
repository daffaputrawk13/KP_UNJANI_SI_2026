<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:jabatans,nama'],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        Jabatan::create($validated);

        return back()->with('status', 'Data jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, Jabatan $jabatan): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:jabatans,nama,'.$jabatan->id],
            'deskripsi' => ['nullable', 'string', 'max:255'],
        ]);

        $jabatan->update($validated);

        return back()->with('status', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy(Jabatan $jabatan): RedirectResponse
    {
        if ($jabatan->personels()->exists()) {
            return back()->with('error', 'Jabatan masih dipakai oleh data personel dan tidak bisa dihapus.');
        }

        $jabatan->delete();

        return back()->with('status', 'Data jabatan berhasil dihapus.');
    }
}
