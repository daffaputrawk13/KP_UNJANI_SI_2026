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

        // Duktek hanya mengirim laporan dukungan kepada tiga Satlak operasional.
        $validated = $request->validate([
            'satuan_tujuan_id' => [
                'required',
                'exists:satuans,id',
                function ($attribute, $value, $fail) {
                    $kode = \App\Models\Satuan::whereKey($value)->value('kode');
                    if (! in_array(strtoupper(trim((string) $kode)), ['SATLAKKAL', 'SATLAKSISOS', 'SATLAKDAK'], true)) {
                        $fail('Tujuan laporan Duktek harus Penangkalan, Siber Sosial, atau Penindakan.');
                    }
                },
            ],
            'jenis_bantuan' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ]);

        abort_if((int) $validated['satuan_tujuan_id'] === (int) $satuan->id, 422, 'Tujuan laporan tidak boleh sama dengan satuan pengirim.');

        $validated['satuan_id'] = $satuan->id;
        $validated['user_id'] = $request->user()->id;

        DukunganTeknisLog::create($validated);

        return back()->with('status', 'Laporan dukungan teknis berhasil dicatat.');
    }

    public function destroy(Request $request, DukunganTeknisLog $dukunganTeknisLog): RedirectResponse
    {
        $satuan = $request->user()->load('satuan')->satuan;
        abort_unless($satuan && $dukunganTeknisLog->satuan_id === $satuan->id, 403);

        $dukunganTeknisLog->delete();

        return back()->with('status', 'Laporan dukungan teknis berhasil dihapus.');
    }
}
