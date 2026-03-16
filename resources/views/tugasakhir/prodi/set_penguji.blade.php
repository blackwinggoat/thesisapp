@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading">Sistem Informasi Program Studi <small>Tugas Akhir</small></h1>
            <!-- End page heading -->

            <!-- Begin breadcrumb -->
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="index.html"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/')}}">Home</a></li>
                <li><a href="{{ url('/prodi/usulan_pembimbing')}}">Set Penguji</a></li>
                <li class="active">Set Penguji</li>
            </ol>


            <h3 class="page-heading">Detail Mahasiswa</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <form method="post" action="{{url('prodi/set_penguji/'.$pendaftaran_id)}}" enctype="multipart/form-data">
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
                                <div class="form-control bold-border" disabled>{{$info->judul}}</div>
                            </div>
                        </div>
                        <br><br>
                        @php
                            $pembimbing1 = \App\Dosen::where("C_KODE_DOSEN",$info->pembimbing_I_id)->first();
                            $pembimbing2 = \App\Dosen::where("C_KODE_DOSEN",$info->pembimbing_II_id)->first();
                        @endphp
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Pembimbing Utama</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$pembimbing1->NAMA_DOSEN}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Pembimbing Pendamping</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$pembimbing2->NAMA_DOSEN}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        @php
                            $mst_pendaftaran = \App\Model\mst_pendaftaran::where("pendaftaran_id",$pendaftaran_id)->first();
                        @endphp
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Penguji I</label>
                            <div class="col-xs-5">
                                <select class="form-control bold-border" name="penguji_I_id" required>
                                    <option disabled selected>--</option>
                                    @foreach ($dosen as $key => $value)
                                        @php
                                            $selected="";
                                            $trtpenguji = \App\TrtPenguji::where([
                                                "penguji_I_id" => $value->C_KODE_DOSEN,
                                                "tipe_ujian" => $mst_pendaftaran->tipe_ujian,
                                                "C_NPM" =>$info->C_NPM
                                              ])->count();
                                            if(!empty($trtpenguji)){
                                                $selected="selected";
                                            }
                                        @endphp
                                        <option value="{{$value->C_KODE_DOSEN}}" {{$selected}}>{{$value->NAMA_DOSEN}}</option>
                                    @endforeach
                                </select>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Penguji II</label>
                            <div class="col-xs-5">
                                <select class="form-control bold-border" name="penguji_II_id" required>
                                    <option disabled selected>--</option>
                                    @foreach ($dosen as $key => $value)
                                        @php
                                            $selected="";
                                            $trtpenguji = \App\TrtPenguji::where([
                                                "penguji_II_id" => $value->C_KODE_DOSEN,
                                                "tipe_ujian" => $mst_pendaftaran->tipe_ujian,
                                                "C_NPM" =>$info->C_NPM
                                              ])->count();
                                            if(!empty($trtpenguji)){
                                                $selected="selected";
                                            }
                                        @endphp
                                        <option value="{{$value->C_KODE_DOSEN}}" {{$selected}}>{{$value->NAMA_DOSEN}}</option>
                                    @endforeach
                                </select>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Peenguji III</label>
                            <div class="col-xs-5">
                                <select class="form-control bold-border" name="penguji_III_id" required>
                                    <option disabled selected>--</option>
                                    @foreach ($dosen as $key => $value)
                                        @php
                                            $selected="";
                                            $trtpenguji = \App\TrtPenguji::where([
                                                "penguji_III_id" => $value->C_KODE_DOSEN,
                                                "tipe_ujian" => $mst_pendaftaran->tipe_ujian,
                                                "C_NPM" =>$info->C_NPM
                                              ])->count();
                                            if(!empty($trtpenguji)){
                                                $selected="selected";
                                            }
                                        @endphp
                                            <option value="{{$value->C_KODE_DOSEN}}" {{$selected}}>{{$value->NAMA_DOSEN}}</option>
                                    @endforeach
                                </select>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        @if ($info->tipe_ujian == 0)
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Ketua Sidang</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$pembimbing1->NAMA_DOSEN}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <input type="hidden" name="ketua_sidang_id" value="{{$pembimbing1->C_KODE_DOSEN}}">
                        @else
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Ketua Sidang</label>
                            <div class="col-xs-5">
                                <select class="form-control bold-border" name="ketua_sidang_id" required>
                                    <option disabled selected>--</option>
                                    @foreach ($dosen as $key => $value)
                                        @php
                                            $selected="";
                                            $trtpenguji = \App\TrtPenguji::where([
                                                "ketua_sidang_id" => $value->C_KODE_DOSEN,
                                                "tipe_ujian" => $mst_pendaftaran->tipe_ujian,
                                                "C_NPM" =>$info->C_NPM
                                              ])->count();
                                            if(!empty($trtpenguji)){
                                                $selected="selected";
                                            }
                                        @endphp
                                        <option value="{{$value->C_KODE_DOSEN}}" {{$selected}}>{{$value->NAMA_DOSEN}}</option>
                                    @endforeach
                                </select>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        @endif
                        <br><br>
                        <div class="form-group">
                            <div class="col-xs-7" align="right">
                                <button id="tombol_satu" class="btn btn-danger btn-perspective" type="button" onclick="showPostModal(this)" data-formaction="{{url('prodi/set_penguji/'.$pendaftaran_id)}}" data-target="#modalPrimary" data-toggle="modal">Simpan</button>
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
            var penguji_I_id = $('select[name="penguji_I_id"]').val();
            var penguji_II_id = $('select[name="penguji_II_id"]').val();
            var penguji_III_id = $('select[name="penguji_III_id"]').val();
            console.log(penguji_I_id);
            console.log(penguji_II_id);
            console.log(penguji_III_id);
            

        if (penguji_I_id == null && penguji_II_id == null && penguji_III_id == null) {
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
        }
    </script>
@endsection





