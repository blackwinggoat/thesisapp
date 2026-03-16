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

Auth::routes();

Route::get('/', 'HomeController@index');


Route::group(['middleware' => 'admin'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
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
    Route::get('/prodi/detail_pembimbing/{id}', 'Prodi@detail_pembimbing');
    Route::get('/prodi/mahasiswa', 'Prodi@mahasiswa');
    Route::get('/prodi/detail_mahasiswa/{id}', 'Prodi@detail_mahasiswa');
    Route::get('/prodi/make_user/{id}', 'Prodi@make_user');
    Route::get('/prodi/reset_user/{id}', 'Prodi@reset_user');
    Route::get('/prodi/make_userx/{id}', 'Prodi@make_userx');
    Route::get('/prodi/reset_userx/{id}', 'Prodi@reset_userx');
    Route::get('/prodi/scope_ta', 'Prodi@scope_ta');
    Route::get('/prodi/scope_del/{id}', 'Prodi@scope_del');
    Route::get('/prodi/topik', 'Prodi@topik');
    Route::get('/prodi/detail_topikusulan/{id}', 'Prodi@detail_topikusulan');
    Route::get('/prodi/usulan_pembimbing', 'Prodi@usulan_pembimbing');
    Route::get('/prodi/usulan_timujianta/{id}', 'Prodi@usulan_timujianta');
    Route::get('/prodi/set_pembimbing/{id}', 'Prodi@set_pembimbing');
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
    Route::get('/prodi/pengumumandel/{id}', 'Prodi@pengumumandel');
    Route::get('/prodi/pendaftarandel/{id}', 'Prodi@pendaftarandel');
    Route::get('/prodi/syaratdel/{id}', 'Prodi@syaratdel');
    Route::get('/prodi/daftar_peserta/{id}', 'Prodi@daftar_peserta');
    Route::get('/prodi/set_penguji/{pendaftaran_id}/{nim}', 'Prodi@set_penguji');
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
    Route::post('/prodi/pengumuman', 'Prodi@pengumumanpost');
    Route::post('/prodi/topik', 'Prodi@topikpost');
    Route::post('/prodi/surat_pengusulan', 'Prodi@surat_pengusulan');
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
    Route::get("/prodi/lembaran_hasilujian_proposal/{id}/{nim}/{regid}", "Prodi@lembaran_hasilujian_proposal");



    Route::get("/prodi/approve_hasilujian_ta/", "Prodi@approve_hasilujian_ta");
    Route::get("/prodi/detail_hasilujian_ta/{id}", "Prodi@detail_hasilujian_ta");
    Route::get("/prodi/approve_hasilujian_ta_post/{id}/{nim}/{pendaftaran_id}", "Prodi@approve_hasilujian_ta_post");
    Route::get("/prodi/lembaran_hasilujian_ta/{id}/{nim}/{regid}", "Prodi@lembaran_hasilujian_ta");

    Route::get("/prodi/make_user_all", "Prodi@make_user_all");

    Route::get('/prodi/detail_note/{id}', 'Prodi@detail_note');
    Route::post('/prodi/detail_note/{id}', 'Prodi@note_update');

});

Route::group(['middleware' => 'akademik_prodi'], function () {

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
});

Route::group(['middleware' => 'dekan'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    //DEKAN
    Route::get('/fakultas/chart_morris', 'fakultas@chart_morris');
    Route::get('/fakultas/chart_c3', 'fakultas@chart_c3');
    Route::get('/fakultas/chart_flot', 'fakultas@chart_flot');
    Route::get('/fakultas/chart_easy_knob', 'fakultas@chart_easy_knob');
});

Route::group(['middleware' => 'wakil_dekan'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    //WAKIL DEKAN

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
});

Route::group(['middleware' => 'dosen'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    //DOSEN
    Route::get('/dsn/mail_inbox', 'dosen@mail_inbox');
    Route::get('/dsn/mail_sent', 'dosen@mail_sent');
    Route::get('/dsn/mail_new', 'dosen@mail_new');
    Route::get('/dsn/mail_read/{id}', 'dosen@mail_read');
    Route::get('/dsn/request_pembimbing', 'dosen@request_pembimbing');
    Route::get('/dsn/request_konfirmasi/{status}/{mahasiswa}', 'dosen@request_konfirmasi');
    Route::post('/dsn/pesanpost', 'dosen@pesanpost');
    Route::get('/dsn/detail_pembimbing/{id}', 'dosen@detail_pembimbing');
    Route::get('/dsn/detail_note/{id}', 'dosen@detail_note');
    Route::post('/dsn/detail_note/{id}', 'dosen@note_update');
    Route::get("/dsn/ubah_password/", "dsn@ubah_password");
    Route::post("/dsn/ubah_password/", "dsn@ubah_password_post");
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
    Route::get('/mhs/mail_read/{id}', 'mhs@mail_read');
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

    Route::get('/mhs/usulan_judul', 'mhs@usul_judul');

    Route::get('/mhs/detailhasil_proposal/{kode_dosen}/{reg_id}/{pendaftaran_id}/{nim}', 'mhs@detail_hasil_proposal');
    
    Route::get("/mhs/lembaran_hasilujian_proposal/{id}/{nim}/{regid}", "Prodi@lembaran_hasilujian_proposal");
});
