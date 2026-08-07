<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Satuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Simpan hak akses modul untuk satu satuan (role) — fitur "Manajemen
     * Role & Hak Akses". Checkbox yang tidak dicentang tidak dikirim
     * browser, jadi modul yang tidak ada di request dianggap nonaktif.
     */
    public function update(Request $request, Satuan $satuan): RedirectResponse
    {
        $validated = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'in:'.implode(',', array_keys(Satuan::MODUL_HAK_AKSES))],
        ]);

        $satuan->update(['permissions' => $validated['permissions'] ?? []]);

        ActivityLog::catat('role.update', "Memperbarui hak akses modul untuk satuan \"{$satuan->nama}\".");

        return back()->with('status', "Hak akses \"{$satuan->nama}\" berhasil disimpan.");
    }
}
