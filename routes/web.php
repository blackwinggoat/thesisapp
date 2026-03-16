<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Auth::routes();


Route::get('/', 'HomeController@index');


Route::group(['middleware' => 'admin'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    // Make a route group master data
    Route::group(['prefix' => 'data-master'], function () {
        Route::get('/periode-jabatan', 'Admin@periode_jabatan')->name('get_master_periode_jabatan');
        Route::post('/periode-jabatan', 'Admin@periode_jabatan_update')->name('update_master_periode_jabatan');
    });
    //AKADEMIK-PRODI
    /*
	Route::get('/prodi/dosen_pembimbing', 'Prodi@dosen_pembimbing');
	Route::get('/prodi/detail_pembimbing/{id}', 'Prodi@detail_pembimbing');
	Route::get('/prodi/mahasiswa', 'Prodi@mahasiswa');
	Route::get('/prodi/detail_mahasiswa/{id}', 'Prodi@detail_mahasiswa');
	Route::get('/prodi/topik', 'Prodi@topik');
	Route::get('/prodi/detail_topikusulan/{id}', 'Prodi@detail_topikusulan');
	Route::get('/prodi/usulan_pembimbing', 'Prodi@usulan_pembimbing');
    Route::get('/prodi/usulan_timujianta', 'Prodi@usulan_timujianta');
	Route::get('/prodi/set_pembimbing/{id}', 'Prodi@set_pembimbing');
	Route::get('/prodi/sk_pembimbing', 'Prodi@sk_pembimbing');
	Route::get('/prodi/peserta_proposal', 'Prodi@peserta_proposal');
	Route::get('/prodi/peserta_seminarhasil', 'Prodi@peserta_seminarhasil');
	Route::get('/prodi/peserta_ujianmeja', 'Prodi@peserta_ujianmeja');
	Route::get('/prodi/syarat_ujian', 'Prodi@syarat_ujian');
	Route::get('/prodi/jadwal', 'Prodi@jadwal');
	Route::get('/prodi/sk_ujian', 'Prodi@sk_ujian');
	Route::get('/prodi/pengumuman', 'Prodi@pengumuman');
	Route::get('/prodi/pengumumandel/{id}', 'Prodi@pengumumandel');
	Route::get('/prodi/pendaftarandel/{id}', 'Prodi@pendaftarandel');
    Route::get('/prodi/daftar_peserta/{id}', 'Prodi@daftar_peserta');
    Route::get('/prodi/set_penguji', 'Prodi@set_penguji');
    Route::get('/prodi/cetakskpenguji/', 'Prodi@cetakskpenguji');

	Route::post('/prodi/usulan_pembimbing', 'Prodi@usulan_pembimbingpostadd');
	Route::post('/prodi/sk_pengusulan', 'Prodi@sk_pengusulanpost');
	Route::post('/prodi/jadwal', 'Prodi@jadwalpostadd');
	Route::post('/prodi/pengumuman', 'Prodi@pengumumanpost');
	Route::post('/prodi/topik', 'Prodi@topikpost');
    Route::post('/prodi/surat_pengusulan', 'Prodi@surat_pengusulan');
    Route::post('/prodi/suratpengusulanold/', 'Prodi@surat_pengusulanold');
    Route::post('/prodi/surat_pengusulanujianta/', 'Prodi@surat_pengusulanujianta');
    */
    //MAHASISWA
    /*
	Route::get('/mhs/pengajuan_topik', 'mhs@pengajuan_topik');
    Route::get('/mhs/pengajuan_topikdel/{id}', 'mhs@pengajuan_topikdel');
	Route::get('/mhs/riwayat_ujian', 'mhs@riwayat_ujian');
	Route::get('/mhs/mail_inbox', 'mhs@mail_inbox');
	Route::get('/mhs/mail_sent', 'mhs@mail_sent');
	Route::get('/mhs/mail_new', 'mhs@mail_new');
	Route::get('/mhs/mail_read/{id}', 'mhs@mail_read');
	Route::get('/mhs/detail_ujian', 'mhs@detail_ujian');
	Route::get('/mhs/signup_proposal', 'mhs@signup_proposal');
	Route::get('/mhs/signup_seminarhasil', 'mhs@signup_seminarhasil');
	Route::get('/mhs/signup_ujianmeja', 'mhs@signup_ujianmeja');


	Route::post('/mhs/pengajuan_topik', 'mhs@pengajuan_topikpost');
    Route::post('/mhs/registrasi', 'mhs@registrasi');
    Route::post('/mhs/pesanpost', 'mhs@pesanpost');
    */

    //FAKULTAS
    /*
    Route::get('/fakultas/sk_pembimbing', 'fakultas@sk_pembimbing');

    Route::get('/fakultas/chart_morris', 'fakultas@chart_morris');
    Route::get('/fakultas/chart_c3', 'fakultas@chart_c3');
    Route::get('/fakultas/chart_flot', 'fakultas@chart_flot');
    Route::get('/fakultas/chart_easy_knob', 'fakultas@chart_easy_knob');

	Route::post('/fakultas/sk_penetapan/', 'fakultas@sk_penetapan');
 	Route::post('/fakultas/cetakskpembimbing/', 'fakultas@cetakskpembimbing');
    */
});




Route::group(['middleware' => 'kaprodi'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    //KA-PRODI
    Route::get('/prodi/dosen_pembimbing', 'Prodi@dosen_pembimbing');
    Route::get('/prodi/report', 'Prodi@report');
    Route::get('/prodi/detail_pembimbing/{id}', 'Prodi@detail_pembimbing');
    Route::get('/prodi/mahasiswa', 'Prodi@mahasiswa');
    Route::get('/prodi/detail_mahasiswa/{id}', 'Prodi@detail_mahasiswa');
    Route::get('/prodi/make_user/{id}', 'Prodi@make_user');
    Route::get('/prodi/reset_user/{id}', 'Prodi@reset_user');
    Route::get('/prodi/make_userx/{id}', 'Prodi@make_userx');
    Route::get('/prodi/reset_userx/{id}', 'Prodi@reset_userx');
    Route::get('/prodi/scope_ta', 'Prodi@scope_ta');
    Route::get('/prodi/scope_del/{id}', 'Prodi@scope_del');
    Route::get('/prodi/master/jenis_tugas_akhir', 'Prodi@master_jenis_tugas_akhir');
    Route::get('/prodi/master/jenis_tugas_akhir/delete/{id}', 'Prodi@master_jenis_tugas_akhir_delete');
    Route::get('/prodi/topik', 'Prodi@topik');
    Route::get('/prodi/detail_topikusulan/{id}', 'Prodi@detail_topikusulan');
    Route::get('/prodi/usulan_pembimbing', 'Prodi@usulan_pembimbing');
    Route::get('/prodi/set_pembimbing_sementara/{nim}', 'Prodi@set_pembimbing_sementara');
    Route::get('/prodi/usulan_timujianta/{id}', 'Prodi@usulan_timujianta');
    Route::get('/prodi/set_pembimbing/{id}/{status}', 'Prodi@set_pembimbing');
    Route::get('/prodi/sk_pembimbing', 'Prodi@sk_pembimbing');
    Route::get('/prodi/peserta_proposal', 'Prodi@peserta_proposal');
    Route::get('/prodi/peserta_seminarhasil', 'Prodi@peserta_seminarhasil');
    Route::get('/prodi/peserta_ujianmeja', 'Prodi@peserta_ujianmeja');
    Route::get('/prodi/syarat_ujian', 'Prodi@syarat_ujian');
    Route::get('/prodi/jadwal', 'Prodi@jadwal');
    Route::get('/prodi/sk_ujian', 'Prodi@sk_ujian');
    Route::get('/prodi/sk_ujian_ta', 'Prodi@sk_ujian_ta');
    Route::get('/prodi/detail_skujian/{id}', 'Prodi@detail_skujian');
    Route::get('/prodi/pengumuman', 'Prodi@pengumuman');
    Route::get('/prodi/pengumuman/{id}', 'Prodi@edit_pengumuman');
    Route::post('/prodi/edit_pengumuman_post/', 'Prodi@edit_pengumuman_post');


    Route::get('/prodi/pengumumandel/{id}', 'Prodi@pengumumandel');
    Route::get('/prodi/pendaftarandel/{id}', 'Prodi@pendaftarandel');
    Route::get('/prodi/syaratdel/{id}', 'Prodi@syaratdel');
    Route::get('/prodi/daftar_peserta/{id}', 'Prodi@daftar_peserta');
    Route::get('/prodi/set_penguji/{pendaftaran_id}/{nim}/{tipe_ujian}', 'Prodi@set_penguji');
    Route::get('/prodi/cetakskpenguji/{pendaftaran_id}/{nim}', 'Prodi@cetakskpenguji');
    Route::get('/prodi/setlevelpembimbing/{dosen}/{level}', 'Prodi@setlevelpembimbing');
    Route::get("/prodi/usulan_tmp/pembimbing/getstatus/{index}/{id}/{mahasiswa}", "Prodi@getPembimbingStatus");
    Route::get("/prodi/persyaratan_proposal", "Prodi@persyaratan_proposal");
    Route::get("/prodi/persyaratan_seminarhasil", "Prodi@persyaratan_seminarhasil");
    Route::get("/prodi/persyaratan_ujianmeja", "Prodi@persyaratan_ujianmeja");
    Route::get("/prodi/detail_persyaratan_proposal/{id}", "Prodi@detail_persyaratan_proposal");
    Route::get("/prodi/detail_persyaratan_ujianmeja/{id}", "Prodi@detail_persyaratan_ujianmeja");
    Route::get("/prodi/konfirmasi_persyaratan_ujian/{status}/{id}/{nim}", "Prodi@konfirmasi_persyaratan_ujian");
    Route::get('/prodi/jadwalujiandel/{id}', 'Prodi@jadwalUjianDel');
    Route::get('/prodi/jadwalpermhs/{tipe_ujian}', 'Prodi@jadwalPerMhs');
    Route::get('/prodi/detail_jadwalpermhs/{pendaftaran_id}', 'Prodi@detailJadwalPermhs');
    Route::get('/prodi/set_jadwalpermhs/{pendaftaran_id}/{nim}', 'Prodi@set_jadwalujianpermhs');
    Route::get('/prodi/cetak_berita_acara/{pendaftaran_id}/{nim}', 'Prodi@cetakBeritaAcara');
    Route::get('/prodi/selesai_konfirmasi/{nim}/{type}', 'Prodi@selesaiKonfirmasi');

    Route::post('/prodi/usulan_pembimbing', 'Prodi@usulan_pembimbingpostadd');
    Route::post('/prodi/sk_pengusulan', 'Prodi@sk_pengusulanpost');
    Route::post('/prodi/sk_pengusulan_tim_ujian_ta', 'Prodi@sk_pengusulan_tim_ujian_tapost');
    Route::post('/prodi/jadwal', 'Prodi@jadwalpostadd');
    Route::post('/prodi/scope_add', 'Prodi@scope_add');
    Route::post('/prodi/master/jenis_tugas_akhir', 'Prodi@master_jenis_tugas_akhir_store');
    Route::post('/prodi/pengumuman', 'Prodi@pengumumanpost');
    Route::post('/prodi/topik', 'Prodi@topikpost');
    Route::post('/prodi/surat_pengusulan', 'Prodi@surat_pengusulan');
    Route::get('/prodi/surat_pengusulan/{nomor}', 'Prodi@get_surat_pengusulan');
    Route::get('/prodi/cetak_riwayat_sk_pengusulan_tim_ujian_ta/{nomor}', 'Prodi@cetak_riwayat_sk_pengusulan_tim_ujian_ta');
    Route::post('/prodi/surat_pengusulan_ujian_ta', 'Prodi@surat_pengusulan_ujian_ta');
    Route::post('/prodi/suratpengusulanold/', 'Prodi@surat_pengusulanold');
    Route::post('/prodi/surat_pengusulanujianta/', 'Prodi@surat_pengusulanujianta');
    Route::post('/prodi/syaratadd', 'Prodi@syaratadd');
    Route::post('/prodi/set_penguji/{pendaftaran_id}', 'Prodi@set_pengujipost');
    Route::post('/prodi/jadwalujian', 'Prodi@jadwalUjianPost');
    Route::post('/prodi/set_jadwalpermhs/{pendaftaran_id}', 'Prodi@set_jadwalujianpermhspost');
    Route::get("/prodi/konfirmasi_persyaratan_ujian_by_nim/{status}/{nim}", "Prodi@konfirmasi_persyaratan_ujian_by_nim");
    Route::get("/prodi/ubah_password/", "Prodi@ubah_password");

    Route::post("/prodi/ubah_password/", "Prodi@ubah_password_post");

    // Hasil Ujian
    Route::get("/prodi/approve_hasilujian_proposal/", "Prodi@approve_hasilujian_proposal");
    Route::get("/prodi/detail_hasilujian_proposal/{id}", "Prodi@detail_hasilujian_proposal");
    Route::get("/prodi/approve_hasilujian_proposal_post/{id}/{nim}/{pendaftaran_id}", "Prodi@approve_hasilujian_proposal_post");
    Route::get("/prodi/tolak_hasilujian_proposal_post/{id}/{nim}/{pendaftaran_id}", "Prodi@tolak_hasilujian_proposal_post");
    Route::get("/prodi/lembaran_hasilujian_proposal/{id}/{nim}/{regid}", "Prodi@lembaran_hasilujian_proposal");

    // Approve Semua Hasil Ujian
    Route::get("/prodi/approve_hasilujian_proposal_all_post", "Prodi@approve_hasilujian_proposal_all_post");
    Route::get("/prodi/approve_hasilujian_ta_all_post", "Prodi@approve_hasilujian_ta_all_post");

    Route::get("/prodi/approve_hasilujian_ta/", "Prodi@approve_hasilujian_ta");
    Route::get("/prodi/detail_hasilujian_ta/{id}", "Prodi@detail_hasilujian_ta");
    Route::get("/prodi/approve_hasilujian_ta_post/{id}/{nim}/{pendaftaran_id}", "Prodi@approve_hasilujian_ta_post");
    Route::get("/prodi/tolak_hasilujian_ta_post/{id}/{nim}/{pendaftaran_id}", "Prodi@tolak_hasilujian_ta_post");
    Route::get("/prodi/lembaran_hasilujian_ta/{id}/{nim}/{regid}", "Prodi@lembaran_hasilujian_ta");

    Route::get("/prodi/make_user_all", "Prodi@make_user_all");

    Route::get('/prodi/detail_note/{id}', 'Prodi@detail_note');
    Route::post('/prodi/detail_note/{id}', 'Prodi@note_update');

    Route::get('/prodi/tolak_topik_penelitian/{id}', 'Prodi@tolak_topik_penelitian');

    Route::get('/prodi/detail_status_bimbingan_mahasiswa/{status}', 'Prodi@detail_status_bimbingan_mahasiswa');

    Route::get('/prodi/set_sk/{id}', 'Prodi@set_sk');
    Route::post('/prodi/add_sk_pembimbing', 'Prodi@add_sk_pembimbing');

    // Catatan
    Route::get('/prodi/detail_persyaratan_proposal/catatan/{id}/{nim}', 'Prodi@detail_persyaratan_proposal_catatan');
    Route::post('/prodi/detail_persyaratan_proposal_catatan_post/', 'Prodi@detail_persyaratan_proposal_catatan_post');

    // Catatan
    Route::get('/prodi/detail_persyaratan_ujianmeja/catatan/{id}/{nim}', 'Prodi@detail_persyaratan_ujianmeja_catatan');
    Route::post('/prodi/detail_persyaratan_ujianmeja_catatan_post/', 'Prodi@detail_persyaratan_ujianmeja_catatan_post');

    Route::get('/prodi/batal_set_pembimbing/{nim}', 'Prodi@batal_set_pembimbing');

    Route::get('/prodi/temp_daftar_peserta/{id}', 'Prodi@temp_daftar_peserta');

    Route::get('/prodi/edit_judul_detail_mahasiswa/{nim}', 'Prodi@edit_judul_detail_mahasiswa');
    Route::post('/prodi/ubah_judul/{nim}', 'Prodi@ubah_judul');
    Route::match(['get', 'post'], '/prodi/update_status_bimbingan', function (Request $request) {
        $nim = $request->input('nim');
        $bimbinganId = $request->input('bimbingan_id');
        $statusBimbingan = (string) $request->input('status_bimbingan');
        $redirectUrl = url('prodi/detail_mahasiswa/' . $nim);

        if (empty($nim) || empty($bimbinganId) || !in_array($statusBimbingan, ['0', '2', '3', '4'], true)) {
            return redirect($redirectUrl)->with('error', 'Parameter perubahan status tidak valid');
        }

        try {
            $updated = DB::table('trt_bimbingan')
                ->where('bimbingan_id', $bimbinganId)
                ->where('C_NPM', $nim)
                ->update([
                    'status_bimbingan' => $statusBimbingan,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'last_update' => date('Y-m-d H:i:s'),
                ]);

            if (!$updated) {
                return redirect($redirectUrl)->with('error', 'Data bimbingan mahasiswa tidak ditemukan / tidak berubah');
            }

            return redirect($redirectUrl)->with('success', 'Tahapan bimbingan berhasil diubah');
        } catch (\Exception $e) {
            return redirect($redirectUrl)->with('error', 'Gagal mengubah tahapan bimbingan');
        }
    });

    Route::get('/prodi/ubah_pembimbing_per_mahasiswa/{nim}', 'Prodi@ubah_pembimbing_per_mahasiswa');
    Route::post('/prodi/ubah_pembimbing_per_mahasiswa/', 'Prodi@ubah_pembimbing_per_mahasiswa_post');


    Route::post('/prodi/ubah_periode_pendaftaran/', 'Prodi@ubah_periode_pendaftaran');

    Route::get('/prodi/riwayat_sk_pengusulan/', 'Prodi@riwayat_sk_pengusulan');
    Route::get('/prodi/riwayat_sk_pengusulan_tim_ujian_ta/', 'Prodi@riwayat_sk_pengusulan_tim_ujian_ta');

    Route::get('/prodi/detail_riwayat_sk_pengusulan/{nomor}', 'Prodi@detail_riwayat_sk_pengusulan');
    Route::get('/prodi/detail_riwayat_sk_pengusulan_tim_ujian_ta/{nomor}', 'Prodi@detail_riwayat_sk_pengusulan_tim_ujian_ta');

    // Flter By Tanggal Detail Status Bimbimngan
    Route::get('/detail_status_bimbingan_mahasiswa', 'Prodi@tampilDetailStatusBimbinganDenganFilterTanggal')->name('tampilDetailStatusBimbinganDenganFilterTanggal');
    // Menu Download
    Route::get('/prodi/download', "Prodi@tampilDownload")->name('tampilDownload');
    Route::post('/prodi/download', "Prodi@kirimDownload")->name('kirimDownload');
    Route::get('/prodi/download/hapus/{id}', "Prodi@hapusDownload")->name('hapusDownload');
    // Menu Jadwal Ujian
    Route::get('/prodi/hapus-jadwal-ujian-per-mahasiswa/{C_NPM}/{pendaftaran_id}', "Prodi@hapusJadwalUjianPerMahasiswa")->name('hapusJadwalUjianPerMahasiswa');
    // Surat Keputusan
    Route::get('/prodi/surat_keputusan_pembimbing', "Prodi@surat_keputusan_pembimbing")->name("surat_keputusan_pembimbing");
    Route::get('/prodi/surat_penugasan_ujian_tugas_akhir', "Prodi@surat_penugasan_ujian_tugas_akhir")->name('surat_penugasan_ujian_tugas_akhir');
});

Route::group(['middleware' => 'akademik_prodi'], function () {

    Route::get('/akademikprodi/make_user/{id}', 'AkademikProdi@make_user');
    Route::get('/akademikprodi/reset_user/{id}', 'AkademikProdi@reset_user');
    Route::get('/akademikprodi/make_userx/{id}', 'AkademikProdi@make_userx');
    Route::get('/akademikprodi/reset_userx/{id}', 'AkademikProdi@reset_userx');

    Route::get('/home', 'HomeController@index')->name('home');
    Route::get("/akademikprodi/persyaratan_proposal", "AkademikProdi@persyaratan_proposal");
    Route::get("/akademikprodi/persyaratan_ujianmeja", "AkademikProdi@persyaratan_ujianmeja");
    Route::get("/akademikprodi/detail_persyaratan_proposal/{id}", "AkademikProdi@detail_persyaratan_proposal");
    Route::get("/akademikprodi/detail_persyaratan_ujianmeja/{id}", "AkademikProdi@detail_persyaratan_ujianmeja");
    Route::get("/akademikprodi/konfirmasi_persyaratan_ujian_by_nim/{status}/{nim}", "AkademikProdi@konfirmasi_persyaratan_ujian_by_nim");
    Route::get("/akademikprodi/konfirmasi_persyaratan_ujian/{status}/{id}/{nim}", "AkademikProdi@konfirmasi_persyaratan_ujian");
    Route::get('/akademikprodi/dosen_pembimbing', 'AkademikProdi@dosen_pembimbing');
    Route::get('/akademikprodi/detail_pembimbing/{id}', 'AkademikProdi@detail_pembimbing');
    Route::get('/akademikprodi/mahasiswa', 'AkademikProdi@mahasiswa');
    Route::get('/akademikprodi/selesai_konfirmasi/{nim}/{type}', 'AkademikProdi@selesaiKonfirmasi');

    Route::get('/akademikprodi/detail_status_bimbingan_mahasiswa/{status}/', 'AkademikProdi@detail_status_bimbingan_mahasiswa');

    // Catatan
    Route::get('/akademikprodi/detail_persyaratan_proposal/catatan/{id}/{nim}', 'AkademikProdi@detail_persyaratan_proposal_catatan');
    Route::post('/akademikprodi/detail_persyaratan_proposal_catatan_post/', 'AkademikProdi@detail_persyaratan_proposal_catatan_post');

    // Catatan
    Route::get('/akademikprodi/detail_persyaratan_ujianmeja/catatan/{id}/{nim}', 'AkademikProdi@detail_persyaratan_ujianmeja_catatan');
    Route::post('/akademikprodi/detail_persyaratan_ujianmeja_catatan_post/', 'AkademikProdi@detail_persyaratan_ujianmeja_catatan_post');

    // Rekap Nilai Hasil Proposal
    Route::get("/akademikprodi/rekap_nilai_proposal/", "AkademikProdi@rekap_nilai_proposal");
    Route::get("/akademikprodi/detail_rekap_nilai_proposal/{id}", "AkademikProdi@detail_rekap_nilai_proposal");
    Route::get("/akademikprodi/lembaran_hasilujian_proposal/{id}/{nim}/{regid}", "AkademikProdi@lembaran_hasilujian_proposal");

    // Rekap Nilai Ujian TA
    Route::get("/akademikprodi/rekap_nilai_ujian_ta/", "AkademikProdi@rekap_nilai_ujian_ta");
    Route::get("/akademikprodi/detail_rekap_nilai_ujian_ta/{id}", "AkademikProdi@detail_rekap_nilai_ujian_ta");
    Route::get("/akademikprodi/lembaran_hasilujian_ujian_ta/{id}/{nim}/{regid}", "AkademikProdi@lembaran_hasilujian_ujian_ta");

    Route::get('/akademikprodi/detail_ujian/{nim}/{tipe_ujian}', 'akademikprodi@detail_ujian');

    // Surat Keputusan
    Route::get('/akademikprodi/sk_ujian', 'Prodi@sk_ujian');
    Route::get('/akademikprodi/set_sk/{id}', 'Prodi@set_sk');
    Route::get('/akademikprodi/detail_skujian/{id}', 'Prodi@detail_skujian');
    Route::post('/akademikprodi/add_sk_pembimbing', 'Prodi@add_sk_pembimbing');
    Route::get('/akademikprodi/cetakskpenguji/{pendaftaran_id}/{nim}', 'Prodi@cetakskpenguji');
    Route::get('/akademikprodi/cetak_berita_acara/{pendaftaran_id}/{nim}', 'Prodi@cetakBeritaAcara');

});

Route::group(['middleware' => 'dekan'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    //DEKAN
    //WAKIL DEKAN
    Route::get('/dekan/sk_pembimbing', 'Dekan@sk_pembimbing');
    Route::get('/dekan/appove_sk_pembimbing/{id}', 'Dekan@approve_sk_pembimbing');

    Route::get('/dekan/sk_ujian_ta', 'Dekan@sk_ujian_ta');
    Route::get('/dekan/appove_sk_ujian_ta/{id}', 'Dekan@approve_sk_ujian_ta');

    Route::get('/dekan/detail_status_bimbingan_mahasiswa/{status}/', 'Dekan@detail_status_bimbingan_mahasiswa');
});

Route::group(['middleware' => 'wakil_dekan'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    //WAKIL DEKAN
    Route::get('/wakildekan/sk_pembimbing', 'WakilDekan@sk_pembimbing');
    Route::get('/wakildekan/appove_sk_pembimbing/{id}', 'WakilDekan@approve_sk_pembimbing');

    Route::get('/wakildekan/sk_ujian_ta', 'WakilDekan@sk_ujian_ta');
    Route::get('/wakildekan/appove_sk_ujian_ta/{id}', 'WakilDekan@approve_sk_ujian_ta');

    Route::get('/wakildekan/detail_status_bimbingan_mahasiswa/{status}/', 'WakilDekan@detail_status_bimbingan_mahasiswa');
});

Route::group(['middleware' => 'akademik_fakultas'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    //AKADEMIK FAKULTAS
    Route::get('/fakultas/sk_pembimbing', 'fakultas@sk_pembimbing');
    Route::get('/fakultas/surat_penugasan_ujian_ta', 'fakultas@surat_penugasan_ujian_ta');

    Route::post('/fakultas/sk_penetapan/', 'fakultas@sk_penetapan');
    Route::post('/fakultas/sk_penetapan_tim_ujian_ta/', 'fakultas@sk_penetapan_tim_ujian_ta');
    Route::post('/fakultas/addskpembimbing/', 'fakultas@addskpembimbing');
    Route::post('/fakultas/add_sk_penugasan_per_mahasiswa/', 'fakultas@add_sk_penugasan_per_mahasiswa');
    Route::post('/fakultas/cetakskpembimbing/', 'fakultas@cetakskpembimbing');
    Route::post('/fakultas/cetakskpenugasan/', 'fakultas@cetakskpenugasan');
    Route::get('/fakultas/usulan_timujianta/{id}', 'fakultas@usulan_timujianta');
    Route::get("/fakultas/ubah_password/", "fakultas@ubah_password");
    Route::post("/fakultas/ubah_password/", "fakultas@ubah_password_post");

    // Rekap Nilai Hasil Proposal
    Route::get("/fakultas/rekap_nilai_proposal/", "fakultas@rekap_nilai_proposal");
    Route::get("/fakultas/detail_rekap_nilai_proposal/{id}", "fakultas@detail_rekap_nilai_proposal");
    Route::get("/fakultas/lembaran_hasilujian_proposal/{id}/{nim}/{regid}", "fakultas@lembaran_hasilujian_proposal");

    // Rekap Nilai Ujian TA
    Route::get("/fakultas/rekap_nilai_ujian_ta/", "fakultas@rekap_nilai_ujian_ta");
    Route::get("/fakultas/detail_rekap_nilai_ujian_ta/{id}", "fakultas@detail_rekap_nilai_ujian_ta");
    Route::get("/fakultas/lembaran_hasilujian_ujian_ta/{id}/{nim}/{regid}", "fakultas@lembaran_hasilujian_ujian_ta");

    Route::get('/fakultas/detail_ujian/{nim}/{tipe_ujian}', 'fakultas@detail_ujian');
    // Surat Keputusan
    Route::get('/fakultas/sk_ujian', "fakultas@sk_ujian")->name('tampilSKUjianFakultas');
    Route::get('/fakultas/detail_skujian/{id}', 'Prodi@detail_skujian');
    Route::get('/fakultas/cetakskpenguji/{pendaftaran_id}/{nim}', 'Prodi@cetakskpenguji');
    Route::get('/fakultas/cetak_berita_acara/{pendaftaran_id}/{nim}', 'Prodi@cetakBeritaAcara');
});

Route::group(['middleware' => 'dosen'], function () {

    // New
    Route::get('/dsn/usul_judul/delete/{usulan_judul_id}', 'dosen@hapus_usulan_judul');

    Route::get('/home', 'HomeController@index')->name('home');
    //DOSEN
    Route::get('/dsn/mail_inbox', 'dosen@mail_inbox');
    Route::get('/dsn/mail_sent', 'dosen@mail_sent');
    Route::get('/dsn/mail_new', 'dosen@mail_new');
    Route::get('/dsn/mail_read/{id}/{status}', 'dosen@mail_read');
    Route::post('/dsn/mail_reply/', 'dosen@mail_reply');



    Route::get('/dsn/request_pembimbing', 'dosen@request_pembimbing');
    Route::get('/dsn/request_konfirmasi/{status}/{mahasiswa}', 'dosen@request_konfirmasi');
    Route::post('/dsn/pesanpost', 'dosen@pesanpost');
    Route::get('/dsn/detail_pembimbing/{id}', 'dosen@detail_pembimbing');
    Route::get('/dsn/detail_note/{id}', 'dosen@detail_note');
    Route::post('/dsn/detail_note/{id}', 'dosen@note_update');
    Route::get("/dsn/ubah_password/", "dosen@ubah_password");
    Route::post("/dsn/ubah_password/", "dosen@ubah_password_post");
    Route::get("/dsn/hasil_proposal/", "dosen@hasil_proposal");
    Route::get("/dsn/detailhasil_proposal/{regid}", "dosen@detailhasil_proposal");
    Route::post("/dsn/detailhasil_proposalpost/", "dosen@detailhasil_proposalpost");
    Route::get("/dsn/hasil_ujianmeja/", "dosen@hasil_ujianmeja");
    Route::get("/dsn/detailhasil_ujianmeja/{regid}", "dosen@detailhasil_ujianmeja");
    Route::post("/dsn/detailhasil_ujianmejapost/", "dosen@detailhasil_ujianmejapost");
    Route::get("/dsn/jadwal_proposal/", "dosen@jadwal_proposal");
    Route::get("/dsn/jadwal_ujianmeja/", "dosen@jadwal_ujianmeja");

    Route::get('/dsn/usul_judul', 'dosen@usul_judul');
    Route::get('/dsn/add_usul_judul', 'dosen@add_usul_judul');
    Route::post('/dsn/usul_judul_post', 'dosen@usul_judul_post');

    // Rekap Nilai Hasil Proposal
    Route::get("/dsn/rekap_nilai_proposal/", "dosen@rekap_nilai_proposal");
    Route::get("/dsn/detail_rekap_nilai_proposal/{id}", "dosen@detail_rekap_nilai_proposal");
    Route::get("/dsn/lembaran_hasilujian_proposal/{id}/{nim}/{regid}", "dosen@lembaran_hasilujian_proposal");

    // Rekap Nilai Ujian TA
    Route::get("/dsn/rekap_nilai_ujian_ta/", "dosen@rekap_nilai_ujian_ta");
    Route::get("/dsn/detail_rekap_nilai_ujian_ta/{id}", "dosen@detail_rekap_nilai_ujian_ta");
    Route::get("/dsn/lembaran_hasilujian_ujian_ta/{id}/{nim}/{regid}", "dosen@lembaran_hasilujian_ujian_ta");

    // Pengumuman
    Route::get('/dsn/pengumuman/show/{id}', 'dosen@show_pengumuman');
    Route::get('/dsn/pengumuman/', 'dosen@pengumuman');

    // Sk Pembimbing
    Route::post('/dsn/cetak_sk_pembimbing/', 'dosen@cetak_sk_pembimbing');

    Route::get('/dsn/surat_sk_proposal/{pendaftaran_id}/{nim}', 'dosen@surat_sk_proposal');
    Route::get('/dsn/surat_sk_ujian_meja/{nim}', 'dosen@surat_sk_ujian_meja');

    Route::get('/dsn/detail_ujian/{nim}/{tipe_ujian}', 'dosen@detail_ujian');

    Route::get('/dsn/detailhasil_proposal/{kode_dosen}/{reg_id}/{pendaftaran_id}/{nim}/{status}', 'mhs@detail_hasil_proposal');

    Route::get('/dsn/detailhasil_ujianmeja/{kode_dosen}/{reg_id}/{pendaftaran_id}/{nim}/{status}', 'mhs@detail_hasil_ujianmeja');

    // Tanda Tangan
    Route::get('/dsn/tanda_tangan', 'dosen@tanda_tangan');
    Route::post('/dsn/upload_ttd', 'dosen@upload_ttd_post');

    // Honorarium
    Route::get('/dsn/honorarium', 'dosen@honorarium');
    Route::post('/dsn/honorarium', 'dosen@honorarium_save_all_dosen')->name('honorarium_save_all_dosen');
    Route::get('/dsn/history_honorarium', 'dosen@history_honorarium')->name('history_honorarium');

    // set_session_status
    Route::post('/set-session-status', 'dosen@set_session_status')->name('set_session_status');
});

Route::group(['middleware' => 'mhs'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    //MAHASISWA
    Route::get('/mhs/pengajuan_topik', 'mhs@pengajuan_topik');
    Route::get('/mhs/detail_note/{id}', 'mhs@detail_note');
    Route::post('/mhs/detail_note/{id}', 'mhs@note_update');
    Route::get('/mhs/ubah_judul/{id}', 'mhs@ubah_judul');
    Route::post('/mhs/ubah_judul/{id}', 'mhs@judul_update');
    Route::get('/mhs/pengajuan_topikdel/{id}', 'mhs@pengajuan_topikdel');
    Route::get('/mhs/riwayat_ujian/{nim}', 'mhs@riwayat_ujian');
    Route::get('/mhs/mail_inbox', 'mhs@mail_inbox');
    Route::get('/mhs/mail_sent', 'mhs@mail_sent');
    Route::get('/mhs/mail_new', 'mhs@mail_new');
    Route::get('/mhs/mail_read/{id}/{status}', 'mhs@mail_read');
    Route::get('/mhs/detail_ujian/{nim}/{tipe_uijan}', 'mhs@detail_ujian');
    Route::get('/mhs/signup_proposal', 'mhs@signup_proposal');
    Route::get('/mhs/signup_seminarhasil', 'mhs@signup_seminarhasil');
    Route::get('/mhs/signup_ujianmeja', 'mhs@signup_ujianmeja');
    Route::get('/mhs/download', 'mhs@download');
    Route::get("/mhs/usulan_tmp/pembimbing/getstatus/{index}/{id}", "mhs@getPembimbingStatus");
    Route::get('/mhs/syarat_ujiandel/{type}/{id}', 'mhs@syarat_ujiandel');
    Route::get('/mhs/ajukan_dokumen/{type}', 'mhs@ajukan_dokumen');
    Route::get('/mhs/beritaacara_proposal/{nim}', 'mhs@beritaacara_proposal');
    Route::get('/mhs/beritaacara_ujian/{nim}', 'mhs@beritaacara_ujian');
    Route::get('/mhs/cetak_beritaacara_proposal/{pendaftaran_id}/{nim}', 'mhs@cetak_beritaacara_proposal');

    Route::post('/mhs/pengajuan_topik', 'mhs@pengajuan_topikpost');
    Route::post('/mhs/registrasi', 'mhs@registrasi');
    Route::post('/mhs/pesanpost', 'mhs@pesanpost');
    Route::post('/mhs/usulan_tmp', 'mhs@usulan_tmp');
    Route::post('/mhs/syarat_ujianpost', 'mhs@syarat_ujianpost');
    Route::post('/mhs/syarat_ujianpost_all', 'mhs@syarat_ujianpost_all');
    Route::get("/mhs/ubah_password/", "mhs@ubah_password");
    Route::post("/mhs/ubah_password/", "mhs@ubah_password_post");

    Route::get('/mhs/usulan_judul_calon_pembimbing', 'mhs@usulan_judul_calon_pembimbing');
    Route::get('/mhs/usulan_judul_calon_pembimbing/{kode_dosen}', 'mhs@detail_usulan_judul_calon_pembimbing');
    Route::get('/mhs/usulan_judul_anak_bimbingan', 'mhs@usulan_judul_anak_bimbingan');
    Route::get('/mhs/usulan_judul_semua_mahasiswa', 'mhs@usulan_judul_semua_mahasiswa');

    Route::get('/mhs/detailhasil_proposal/{kode_dosen}/{reg_id}/{pendaftaran_id}/{nim}/{status}', 'mhs@detail_hasil_proposal');

    Route::get('/mhs/detailhasil_ujianmeja/{kode_dosen}/{reg_id}/{pendaftaran_id}/{nim}/{status}', 'mhs@detail_hasil_ujianmeja');

    Route::get("/mhs/lembaran_hasilujian_proposal/{id}/{nim}/{regid}", "Prodi@lembaran_hasilujian_proposal");

    Route::get("/mhs/chat/", "Prodi@chat");

    Route::get('/mhs/pengumuman/show/{id}', 'mhs@show_pengumuman');
    Route::get('/mhs/pengumuman/', 'mhs@pengumuman');

    Route::post('/mhs/mail_reply/', 'mhs@mail_reply');

    // Catatan
    Route::get('/mhs/signup_proposal/catatan/{id}', 'mhs@signup_proposal_catatan');
    Route::post('/mhs/signup_proposal_catatan/', 'mhs@signup_proposal_catatan_post');

    // Catatan
    Route::get('/mhs/signup_ujianmeja/catatan/{id}', 'mhs@signup_ujianmeja_catatan');
    Route::post('/mhs/signup_ujianmeja_catatan/', 'mhs@signup_ujianmeja_catatan_post');

    // Catatan
    Route::get('/mhs/surat_sk_pembimbing/{nomor}', 'mhs@surat_sk_pembimbing');
    Route::get('/mhs/surat_sk_proposal/{pendaftaran_id}', 'mhs@surat_sk_proposal');
    Route::get('/mhs/surat_sk_ujian_meja/{nomor}', 'mhs@surat_sk_ujian_meja');
});

// Route Group untuk Keuangan Fakultas
Route::group(['middleware' => 'keuangan_fakultas'], function () {
    Route::group(['prefix' => 'master_pembayaran'], function () {
        Route::get('/', 'KeuanganFakultas@master_pembayaran_home')->name('master_pembayaran_home');
        Route::post('/add', 'KeuanganFakultas@master_pembayaran_store')->name('master_pembayaran_store');
        Route::post('/update', 'KeuanganFakultas@master_pembayaran_update')->name('master_pembayaran_update');
        Route::get('/delete/{id}', 'KeuanganFakultas@master_pembayaran_delete')->name('master_pembayaran_delete');
    });
    // Honorarium
    Route::group(['prefix' => 'honorarium'], function () {
        Route::get('/', 'KeuanganFakultas@honorarium_home')->name('honorarium_home');
        Route::post('/available_post_yes', 'KeuanganFakultas@honorarium_available_post_yes')->name('honorarium_available_post_yes');
        Route::post('/available_post_no', 'KeuanganFakultas@honorarium_available_post_no')->name('honorarium_available_post_no');
        Route::post('/save_all', 'KeuanganFakultas@honorarium_save_all')->name('honorarium_save_all');
        Route::get('/history', 'KeuanganFakultas@honorarium_history')->name('honorarium_history');
    });
    // Report
    Route::group(['prefix' => 'report'], function () {
        Route::get('/periode_ujian', 'KeuanganFakultas@report_periode_ujian_home')->name('report_periode_ujian_home');
        Route::get('/periode_ujian/{date}', 'KeuanganFakultas@report_periode_ujian_by_date')->name('report_periode_ujian_by_date');
        Route::get('/cetak_honorarium_per_mahasiswa/{id}', 'KeuanganFakultas@cetak_honorarium_per_mahasiswa')->name('cetak_honorarium_per_mahasiswa');
        Route::get('/dosen', 'KeuanganFakultas@report_dosen_home')->name('report_dosen_home');
        Route::get('/dosen/{id}', 'KeuanganFakultas@report_dosen_detail')->name('report_dosen_detail');
        Route::get('/dosen/history/{id}', 'KeuanganFakultas@report_dosen_history')->name('report_dosen_history');
        Route::get('/dosen/{id}/{start_date}/{end_date}', 'KeuanganFakultas@report_dosen_detail_by_date')->name('report_dosen_detail_by_date');
    });
});
