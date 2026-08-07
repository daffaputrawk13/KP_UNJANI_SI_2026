<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Batasi akses hanya untuk akun dengan satuan berkode ADMIN — dipakai
     * pada seluruh route "Kelola Sistem" di dashboard Admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $kode = $request->user()?->satuan?->kode;

        if (strtoupper(trim((string) $kode)) !== 'ADMIN') {
            abort(403, 'Halaman ini khusus untuk Admin.');
        }

        return $next($request);
    }
}
