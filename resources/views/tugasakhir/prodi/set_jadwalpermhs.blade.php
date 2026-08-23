@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
            <!-- End page heading -->

            <!-- Begin breadcrumb -->
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/')}}">Home</a></li>
                <li><a href="{{ url('/prodi/usulan_pembimbing')}}">Set Penguji</a></li>
                <li class="active">Set Penguji</li>
            </ol>


            <h3 class="page-heading">Detail Mahasiswa</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <form method="post" action="{{url('prodi/set_jadwalpermhs/'.$pendaftaran_id)}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">NIM</label>
                            <div class="col-lg-5">
                                <input type="hidden" class="form-control bold-border" name="C_NPM" value="{{$info->C_NPM}}"/>
                                <div class="form-control bold-border" disabled>{{$info->C_NPM}}</div>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Nama</label>
                            <div class="col-lg-5">
                                <div class="form-control bold-border" disabled>{{$info->NAMA_MAHASISWA}}</div>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Judul</label>
                            <div class="col-lg-5">
                                <div class="form-control bold-border" disabled>{!! helper::jenisTugasAkhirBadge($info->jenis_tugas_akhir_id ?? null) !!} {{$info->judul}}</div>
                                
                            </div>
                        </div>
                        <br><br>
                        @php
                            $pembimbing1 = helper::getNamaDosenByKode($info->pembimbing_I_id);
                            $pembimbing2 = helper::getNamaDosenByKode($info->pembimbing_II_id);
                            $penguji1 = helper::getNamaDosenByKode($info->penguji_I_id);
                            $penguji2 = helper::getNamaDosenByKode($info->penguji_II_id);
                            $penguji3 = helper::getNamaDosenByKode($info->penguji_III_id);
                            $ketua_sidang = helper::getNamaDosenByKode($info->ketua_sidang_id);
                        @endphp
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Pembimbing Utama</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$pembimbing1}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Pembimbing Pendamping</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$pembimbing2}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Penguji I</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$penguji1 == '--' ? '-' : $penguji1}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Penguji II</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$penguji2 == '--' ? '-' : $penguji2}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Penguji III</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$penguji3 == '--' ? '-' : $penguji3}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Ketua Sidang</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$ketua_sidang == '--' ? '-' : $ketua_sidang}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Ruangan Ujian</label>
                            <div class="col-xs-5">
                                <select class="form-control bold-border" required name="ruangan" onchange="ruanganChange(this)" tipe-ujian="{{$info->tipe_ujian}}" nim="{{$info->C_NPM}}" pendaftaran-id="{{$pendaftaran_id}}">
                                    <option value="">Pilih ruangan...</option>
                                    @php
                                        $ruangan = \App\MstRuangan::all();
                                    @endphp
                                    @foreach($ruangan as $d)
                                        @php
                                            $selected= "";
                                                if(!empty($jadwal) && $jadwal->ruangan == $d->id){
                                                $selected = "selected";
                                            }
                                        @endphp
                                        <option value="{{$d->id}}" {{$selected}}>{{$d->nama_ruangan}}</option>
                                    @endforeach
                                </select>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Jam Ujian</label>
                            <div class="col-xs-5">
                                {{-- <select class="form-control bold-border" required name="jam_ujian">
                                </select> --}}
                                <input type="text" class="form-control bold-border" name="jam_ujian" value="{{!empty($jadwal) ? $jadwal->jam_ujian : ''}}" required>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-xs-7" align="right">
                                <button id="tombol_satu" class="btn btn-danger btn-perspective" type="button" onclick="showPostModal(this)" data-formaction="{{url('prodi/set_jadwalpermhs/'.$pendaftaran_id)}}" data-target="#modalPrimary" data-toggle="modal">Simpan</button>
                            </div>
                        </div>
                    </fieldset>
                </form>

            </div><!-- /.the-box -->
        </div>
    </div>
@endsection

{{--ModalSetUser--}}
@section("modalPrimaryTitle")
    Set Penguji
@endsection
@section("modalPrimaryBody")
    Apakah Anda yakin ingin menyimpan data?
    <br>
<span id="status" class="badge badge-danger"></span>
@endsection
@section("modalPrimaryFooter")
    <button onclick="submit(this)" id="tombol_dua" class="btn btn-default">Simpan</button>
@endsection

@section("script")
    <script>
        $('#tombol_satu').on('click', function () {
            console.log("Selamat Datang di Bagian Satu");
            var ruangan = $('select[name="ruangan"]').val();
            var jam_ujian = $('input[name="jam_ujian"]').val();
            console.log(ruangan);
            console.log(jam_ujian);
            

        if (ruangan == "" || jam_ujian == "") {
                console.log("Ini Bagian Satu");
                $('#tombol_dua').attr("disabled", "disabled");
                $('#status').html("Data Pada Form Belum Lengkap");
            } else {
                $('#status').html("");
                $("#tombol_dua").removeAttr("disabled");
            }      
        });
        let modal, modalId, modalFooter, link, form, formaction;
        const showPostModal = e => {
            formaction = e.getAttribute("data-formaction");
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
        };

        const submit = () => {
            form = document.querySelector(`form[action="${formaction}"]`);
            form.submit();
        };

        const ruanganChange = (e) => {
            axios.get(`/api/cek_jamujian/${e.getAttribute("tipe-ujian")}/${e.value}/${e.getAttribute("nim")}/${e.getAttribute("pendaftaran-id")}`).then(res => {
                // Tetap panggil endpoint validasi agar perilaku lama tidak berubah,
                // tapi jangan lagi overwrite input jam_ujian dengan format rentang.
                return res;
            });
        };

        const initJamSelected = () => {
            @if(!empty($jadwal))
                return "{{$jadwal->jam_ujian}}";
            @endif
            return "";
        };

        (() => {
            let ruanganSelect = document.querySelector("select[name=ruangan]");
            if (ruanganSelect && ruanganSelect.value !== "") {
                ruanganChange(ruanganSelect);
            }
        })()
    </script>
@endsection


