<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'service' => 'thesisapps-api',
                'version' => 'v1',
                'status' => 'ok',
            ],
        ]);
    });

    Route::post('/auth/login', 'Api\\AuthController@login')->middleware('throttle:10,1');

    Route::middleware(['api.token', 'throttle:60,1'])->group(function () {
        Route::get('/auth/me', 'Api\\AuthController@me');
        Route::post('/auth/logout', 'Api\\AuthController@logout');
    });
});


Route::get("/status_bimbingan_all/", "Prodi@statusBimbinganAll");
Route::get("/status_bimbingan/{nim}", "Prodi@statusBimbingan");
Route::get("/getjumlahpeserta/{pendaftaran_id}", "Prodi@getJumlahPeserta");
Route::get("/cek_jamujian/{tipe_ujian}/{ruangan}/{nim}/{pendaftaran_id}", "Prodi@cekJamUjian");
Route::get("/gettipeujian/{pendaftaran_id}", "Prodi@getTipeUjian");
Route::get("/cek_nomor_sk_pembimbing/{nomor}", "Prodi@cek_nomor_sk_pembimbing");
Route::get("/cek_nomor_sk_ujian_ta/{nomor}", "Prodi@cek_nomor_sk_ujian_ta");
Route::get("/simpan_sementara_set_pembimbing/{nim}/{pembimbing1}/{pembimbing2}", "Prodi@set_pembimbing_sementara");
