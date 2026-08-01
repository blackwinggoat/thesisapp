<?php

namespace App\Http\Middleware;

use App\Helper;
use Closure;

class mhs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!(auth()->check() && ($request->user()->level == 8))) {
            return redirect()->guest('/');
        }

        $kontakBelumLengkap = Helper::shouldShowCurrentMahasiswaContactPopup($request->user());

        if (!$kontakBelumLengkap) {
            return $next($request);
        }

        if ($request->is('home') || $request->is('mhs/kelengkapan_kontak')) {
            return $next($request);
        }

        return redirect('/home')->with('mhs_contact_error', 'Nomor WhatsApp wajib diisi terlebih dahulu sebelum menggunakan menu mahasiswa lainnya.');
    }
}
