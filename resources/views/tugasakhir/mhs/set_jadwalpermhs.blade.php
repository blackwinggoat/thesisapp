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
                                <div class="form-control bold-border" disabled>{{$info->judul}}</div>
                            </div>
                        </div>
                        <br><br>
                        @php
                            $pembimbing1 = \App\Dosen::where("C_KODE_DOSEN",$info->pembimbing_I_id)->first();
                            $pembimbing2 = \App\Dosen::where("C_KODE_DOSEN",$info->pembimbing_II_id)->first();
                            $penguji1 = \App\Dosen::where("C_KODE_DOSEN",$info->penguji_I_id)->first();
                            $penguji2 = \App\Dosen::where("C_KODE_DOSEN",$info->penguji_II_id)->first();
                            $penguji3 = \App\Dosen::where("C_KODE_DOSEN",$info->penguji_III_id)->first();
                            $ketua_sidang = \App\Dosen::where("C_KODE_DOSEN",$info->ketua_sidang_id)->first();
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
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Penguji I</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$penguji1->NAMA_DOSEN}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Penguji II</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$penguji2->NAMA_DOSEN}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Peenguji III</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$penguji3->NAMA_DOSEN}}</div>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Ketua Sidang</label>
                            <div class="col-xs-5">
                                <div class="form-control bold-border" disabled>{{$ketua_sidang->NAMA_DOSEN}}</div>
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
                                <select class="form-control bold-border" required name="jam_ujian">
                                </select>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-xs-7" align="right">
                                <button class="btn btn-danger btn-perspective" type="button" onclick="showPostModal(this)" data-formaction="{{url('prodi/set_jadwalpermhs/'.$pendaftaran_id)}}" data-target="#modalPrimary" data-toggle="modal">Simpan</button>
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
@endsection
@section("modalPrimaryFooter")
    <button onclick="submit(this)" class="btn btn-default">Simpan</button>
@endsection

@section("script")
    <script>
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

        const ruanganChange = (e, selected = initJamSelected()) => {
            axios.get(`/api/cek_jamujian/${e.getAttribute("tipe-ujian")}/${e.value}/${e.getAttribute("nim")}/${e.getAttribute("pendaftaran-id")}`).then(res => {
                let x;
                let r;
                @if(!empty($jadwal))
                    r = "{{$jadwal->ruangan}}";
                @endif
                if (selected && r === e.value) {
                    x = `<option value="${selected}" selected>${selected}</option>`;
                    x += "<option disabled>--</option>"
                }
                res.data.map(d =>
                    x += `<option value="${d}">${d}</option>`
                );
                document.querySelector("select[name=jam_ujian]").innerHTML = x;
            });
        };

        const initJamSelected = () => {
            let jam;
            let tipeUjian = "{{$info->tipe_ujian}}";
            let pad = "00";
            let jamSelected;
            @if(!empty($jadwal))
                jamSelected = "{{$jadwal->jam_ujian}}";
            if (tipeUjian === "0") {
                let x = parseInt(jamSelected.substr(0, 2)) + 1;
                jam = `${jamSelected}-${pad.substr(0, pad.length - x.toString().length) + x}:30`
            } else if (tipeUjian === "1" || tipeUjian === "2") {
                let x = parseInt(jamSelected.substr(0, 2)) + 2;
                jam = `${jamSelected}-${pad.substr(0, pad.length - x.length) + x}:30`
            }
            @endif
                return jam;
        };

        (() => {
            let ruanganSelect = document.querySelector("select[name=ruangan]");

            ruanganChange(ruanganSelect, initJamSelected());
        })()
    </script>
@endsection





