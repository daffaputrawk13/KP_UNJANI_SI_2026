<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Proses login: username + password + satuan yang dipilih dari dropdown.
     * Pengguna hanya bisa masuk jika satuan yang dipilih sesuai dengan
     * satuan tempat akun tersebut terdaftar.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'satuan_id' => ['required', 'exists:satuans,id'],
        ], [
            'satuan_id.required' => 'Silakan pilih satuan Anda terlebih dahulu.',
            'satuan_id.exists' => 'Satuan yang dipilih tidak valid.',
        ]);

        $request->session()->put('satuan_id_input', $credentials['satuan_id']);

        if (! Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'satuan_id' => $credentials['satuan_id'], // wajib cocok dengan satuan akun
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => 'NIP/Username, password, atau satuan yang dipilih salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
