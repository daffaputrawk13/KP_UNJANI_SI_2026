<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Perbarui identitas instansi & (opsional) logo — fitur "Pengaturan
     * Sistem". Logo disimpan di storage/app/public/pengaturan.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_instansi' => ['required', 'string', 'max:255'],
            'singkatan' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'email_kontak' => ['nullable', 'email', 'max:255'],
            'telepon_kontak' => ['nullable', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $pengaturan = Pengaturan::current();

        if ($request->hasFile('logo')) {
            if ($pengaturan->logo_path) {
                Storage::disk('public')->delete($pengaturan->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('pengaturan', 'public');
        }
        unset($validated['logo']);

        $pengaturan->update($validated);

        ActivityLog::catat('pengaturan.update', 'Memperbarui pengaturan umum sistem (identitas instansi/logo).');

        return back()->with('status', 'Pengaturan sistem berhasil disimpan.');
    }
}
