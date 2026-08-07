<?php

namespace App\Http\Controllers;

use App\Models\DukunganTeknisLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DukunganTeknisController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $satuan = $request->user()->load('satuan')->satuan;
        abort_unless($satuan, 403, 'Akun ini belum terhubung ke satuan manapun.');

        $validated = $request->validate([
            'satuan_tujuan_id' => ['required', 'exists:satuans,id'],
            'jenis_bantuan' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['satuan_id'] = $satuan->id;
        $validated['user_id'] = $request->user()->id;

        DukunganTeknisLog::create($validated);

        return back()->with('status', 'Log dukungan teknis berhasil dicatat.');
    }

    public function destroy(Request $request, DukunganTeknisLog $dukunganTeknisLog): RedirectResponse
    {
        $satuan = $request->user()->load('satuan')->satuan;
        abort_unless($satuan && $dukunganTeknisLog->satuan_id === $satuan->id, 403);

        $dukunganTeknisLog->delete();

        return back()->with('status', 'Log dukungan teknis berhasil dihapus.');
    }
}
