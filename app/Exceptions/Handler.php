<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof TokenMismatchException && $request->is('logout')) {
            return redirect('/login');
        }

        if ($exception instanceof PostTooLargeException) {
            if ($request->is('mhs/kelengkapan_kontak')) {
                return redirect('/mhs/profil')->withInput()->withErrors([
                    'foto' => 'Ukuran file foto terlalu besar untuk diproses server. Coba gunakan file yang lebih kecil dari 5 MB.',
                ])->with('mhs_contact_error', 'Upload foto mahasiswa gagal diproses karena ukuran file terlalu besar.');
            }

            if ($request->is('dsn/kelengkapan_profil')) {
                return redirect('/dsn/profil')->withInput()->withErrors([
                    'foto_dosen' => 'Ukuran file foto terlalu besar untuk diproses server. Coba gunakan file yang lebih kecil dari 2 MB.',
                ])->with('dosen_profile_error', 'Upload foto dosen gagal diproses karena ukuran file terlalu besar.');
            }
        }

        return parent::render($request, $exception);
    }
}
