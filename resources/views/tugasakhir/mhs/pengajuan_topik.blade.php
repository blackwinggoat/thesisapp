@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Pengajuan Topik</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Form Pengajuan Topik</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            @php
            $trtbimbingan = \App\Model\trt_bimbingan::where("C_NPM", auth()->user()->name)->get();
            @endphp
            @if($trtbimbingan->isEmpty())
            <form method="post" action="{{url('mhs/pengajuan_topik')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Stambuk</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border disabled" name="C_NPM"
                                value="{{auth()->user()->name}}" readonly />
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Judul Topik</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="topik" required/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Bidang Ilmu Peminatan</label>
                        <div class="col-lg-5">
                            <select class="form-control" name="bidang_ilmu_peminatan" id="bidang_ilmu_peminatan">
                                <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
                                <option value="Jaringan Komputer">Jaringan Komputer</option>
                                <option value="Industri">Industri</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Bidang Ilmu Topik</label>
                        <div class="col-lg-5">
                            <select name='bidang_ilmu[]' data-placeholder="Pilih Scope..."
                                class="form-control chosen-select" multiple tabindex="4" required>
                                <option value="" disabled>&nbsp;</option>
                                @foreach ($data as $key => $value)
                                <option value="{{$value->bidangilmu_id}}">{{$value->bidang_ilmu}}</option>
                                @endforeach
                            </select> 
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Kerangka Pikir</label>
                        <div class="col-lg-5">
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-default btn-file">
                                        Browse&hellip; <input type="file" name="kerangka" class="bold-border">
                                    </span>
                                </span>
                                <input type="text" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                            <label class="col-lg-2 control-label">Note</label>
                            <div class="col-lg-10 mb-5">
                                <textarea class="summernote-sm" name="note">Assalamualaikum, </textarea>
                            </div>
                    </div>
                    <br><br>
                    <div class="form-group mt-2">
                        <div class="col-lg-12" align="right">
                            <button class="btn btn-primary btn-perspective" type="submit">Simpan</button>
                        </div>
                    </div>
                </fieldset>
            </form>
            @else
            @php
            $topik = \App\Model\trt_topik::where(["C_NPM" => auth()->user()->name, "status" => 1])->first();
            $bidangilmuid = \App\RequestPembimbing::where(["C_NPM" => auth()->user()->name, "topik" =>
            $topik->topik_id])->get();
            @endphp
            <fieldset>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Stambuk</label>
                    <div class="col-xs-5">
                        <div class="form-control bold-border">{{auth()->user()->name}}</div>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Judul Topik</label>
                    <div class="col-xs-5">
                        <div class="form-control bold-border">{{$topik->topik}}</div>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Bidang Ilmu Topik</label>
                    <div class="col-xs-5">
                        @foreach($bidangilmuid as $key => $value)
                        @php
                        $bidangilmu = new \App\Model\mst_bidangilmu();
                        @endphp
                        <div class="form-control bold-border">{{++$key}}
                            {{$bidangilmu->where("bidangilmu_id", $value->bidang_ilmu)->first()->bidang_ilmu}}</div>
                        @endforeach
                    </div>
                </div>
            </fieldset>
            @endif
        </div><!-- /.the-box -->


        <h3 class="page-heading">Request Pembimbing</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            @if($trtbimbingan->isEmpty())
            @php
                $statusKonfirmasiTopikPenelitian = helper::getStatusKonfirmasiTopikPenelitian(Auth::user()->name);
            @endphp

            @if ($statusKonfirmasiTopikPenelitian == 0 || $statusKonfirmasiTopikPenelitian == 2)
                <div class="alert alert-danger" role="alert">
                    Status Belum Bisa Memilih Pembimbing <a href="#" class="alert-link">Belum Dikonfirmasi, Belum Ajukan Topik Penelitian atau Judul Ditolak</a>. Harap Perhatikan Yah.
                </div> 
            @endif
            <form method="post" action="{{url('mhs/usulan_tmp')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <fieldset>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Pembimbing Utama</label>
                        <div class="col-xs-5">
                            <select class="form-control bold-border" name="pembimbing_I_id"
                                onchange="initStatusPembimbing(this)" index="0" {{$statusKonfirmasiTopikPenelitian == 0 ? "disabled" : ""}}>
                                @foreach ($cek as $key => $value)
                                <option value="{{$value->pembimbing_I_id}}">
                                    {{helper::getDeskripsi($value->pembimbing_I_id)}}
                                </option>
                                @endforeach
                                <option value="">--</option>
                                @foreach ($listdosen as $key => $value)
                                @if($value->level == "1" || !isset($value->level) || $value->level == "3")
                                <option value="{{$value->C_KODE_DOSEN}}">{{$value->NAMA_DOSEN}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div><!-- /.col-xs-5 -->
                        <span class='col-sm-2 text-primary' id="status0" style="display: none;padding: 5px"></span>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Pembimbing Pendamping</label>
                        <div class="col-xs-5">
                            <select class="form-control bold-border" name="pembimbing_II_id"
                                onchange="initStatusPembimbing(this)" index="1" {{$statusKonfirmasiTopikPenelitian == 0 ? "disabled" : ""}}>
                                @foreach ($cek as $key => $value)
                                <option value="{{$value->pembimbing_II_id}}">
                                    {{helper::getDeskripsi($value->pembimbing_II_id)}}
                                </option>
                                @endforeach
                                <option value="">--</option>
                                @foreach ($listdosen as $key => $value)
                                @if($value->level == "2" || !isset($value->level) || $value->level == "3")
                                <option value="{{$value->C_KODE_DOSEN}}">{{$value->NAMA_DOSEN}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div><!-- /.col-xs-5 -->
                        <span class='col-sm-2 text-primary' id="status1" style="display: none;padding: 5px"></span>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <div class="col-xs-7" align="right">
                            @if ($statusKonfirmasiTopikPenelitian == 1)
                                <button id="tombol_request_satu" class="btn btn-info btn-perspective" type="button" onclick="showPostModal(this)"
                                data-formaction="{{url('mhs/usulan_tmp')}}" data-target="#modalInfo"
                                data-toggle="modal">Simpan</button>
                            @endif
                        </div>
                    </div>
                </fieldset>
            </form>
            @else
            @php
            $pembimbing1 =
            Illuminate\Support\Facades\DB::table("t_mst_dosen")->where("C_KODE_DOSEN",$trtbimbingan[0]->pembimbing_I_id)->pluck("NAMA_DOSEN")[0];
            $pembimbing2 =
            Illuminate\Support\Facades\DB::table("t_mst_dosen")->where("C_KODE_DOSEN",$trtbimbingan[0]->pembimbing_II_id)->pluck("NAMA_DOSEN")[0];
            @endphp
            <fieldset>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Pembimbing Utama</label>
                    <div class="col-xs-5">
                        <div class="form-control bold-border">{{$pembimbing1}}</div>
                    </div>
                    <span class='col-sm-2 text-primary' style="padding: 5px">Diterima</span>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Pembimbing Pendamping</label>
                    <div class="col-xs-5">
                        <div class="form-control bold-border">{{$pembimbing2}}</div>
                    </div>
                    <span class='col-sm-2 text-primary' style="padding: 5px">Diterima</span>
                </div>
            </fieldset>
            @endif
        </div><!-- /.the-box -->

        {{-- Modal Note --}}
        <div class="modal fade" id="modalNote" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-no-shadow modal-no-border bg-info">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Note</h4>
            </div>
            <div class="modal-body">
                <textarea class="summernote-sm" name="note"></textarea>
            </div>
        </div><!-- /.modal-content .modal-no-shadow .modal-no-border .the-box .info .full -->
    </div><!-- /.modal-dialog -->
</div><!-- /#InfoModalColor -->


        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Daftar Riwayat Usulan</h3>
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Judul</th>
                            <th>Note</th>
                            <th>Kerangka Pikir</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($datatopik as $key => $value)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$key}}</td>
                            <td>{{$value->created_at}}</td>
                            <td>{{$value->topik}}</td>
                            <td>
                                <a class="btn btn-info" href="{{url('mhs/detail_note')}}/{{$value->topik_id}}"><i class="fa fa-newspaper-o"></i></a>    
                            </td>
                            <td>
                                <button class="btn btn-primary" onclick="showModal(this)" data-target="#modalDefault"
                                    data-toggle="modal" data-href="{{asset('dokumen/'.$value->kerangka)}}"
                                    target="_blank"><i class="fa fa-paperclip"></i></button>
                            </td>

                            @if($value->status == 0)
                            <td><span class="label label-danger">Belum Diterima</span></td>
                            @elseif($value->status == 1)
                            <td><span class="label label-primary">Diterima</span></td>
                            @elseif($value->status == 2)
                            <td><span class="label label-danger">Ditolak</span></td>
                            @endif
                            @if($trtbimbingan->isEmpty())
                            <td>
                                <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger"
                                    data-toggle="modal"
                                    data-href="{{ url('mhs/pengajuan_topikdel/'.$value->topik_id)}}"><i
                                        class="fa fa-trash-o"></i></button>
                                <a class="btn btn-info" href="{{url('mhs/ubah_judul')}}/{{$value->topik_id}}"><i class="fa fa-edit"></i></a>    
                            </td>
                            @else
                            <td>
                                <a class="btn btn-info" href="{{url('mhs/ubah_judul')}}/{{$value->topik_id}}"><i class="fa fa-edit"></i></a>    
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->
</div>
@endsection

{{--ModalTambah--}}
@section("modalPrimaryTitle")
Tambah
@endsection
@section("modalPrimaryBody")
Apakah Anda yakin ingin menambah data?
@endsection
@section("modalPrimaryFooter")
<button onclick="submit(this)" class="btn btn-default">Tambah</button>
@endsection

{{--ModalRequest--}}
@section("modalInfoTitle")
Request Pembimbing
@endsection
@section("modalInfoBody")
Apakah Anda yakin ingin me-request pembimbing?
<br>
<span id="status" class="badge badge-danger"></span>

@endsection
@section("modalInfoFooter")
<button onclick="submit(this)" id="tombol_request_dua" class="btn btn-default">Request</button>
@endsection

{{--ModalDownload--}}
@section("modalDefaultTitle")
Download Kerangka Pikir
@endsection
@section("modalDefaultBody")
Apakah Anda yakin ingin men-download kerangka pikir?
@endsection
@section("modalDefaultFooter")
<button onclick="goOnNewTab(this)" class="btn btn-primary">Download</button>
@endsection

{{--ModalHapus--}}
@section("modalDangerTitle")
Hapus
@endsection
@section("modalDangerBody")
Apakah Anda yakin ingin menghapus data?
@endsection
@section("modalDangerFooter")
<button onclick="goOn(this)" class="btn btn-default">Hapus</button>
@endsection

@section("script")
<script>

    $('#tombol_request_satu').on('click', function () {
        console.log("Selamat Datang di Bagian Satu");
        
        var p1 = $('select[name="pembimbing_I_id"]').val();
        var p2 = $('select[name="pembimbing_II_id"]').val();

        if (p1 == "--" && p2 == "--") {
            console.log("Ini Bagian Satu");
            $('#tombol_request_dua').attr("disabled", "disabled");
            $('#status').html("Data Pembimbing Belum Lengkap");
        } else {
            $('#status').html("");
            $("#tombol_request_dua").removeAttr("disabled");
        }        
    });
    const initStatusPembimbing = e => {
        let id = e.value;
        let index = e.getAttribute("index");
        let status0 = document.getElementById("status0");
        let status1 = document.getElementById("status1");
        let status = [{
                value: "0",
                text: "Ditolak",
                class: "text-danger"
            },
            {
                value: "1",
                text: "Diterima",
                class: "text-primary"
            },
            {
                value: "2",
                text: "Menunggu...",
                class: "text-warning"
            }
        ];
        axios.get(`/mhs/usulan_tmp/pembimbing/getstatus/${index}/${id}`).then(res => {
            if (index === "0") {
                status.map(s => {
                    if (s.value === res.data) {
                        status0.classList = s.class;
                        status0.style.display = "initial";
                        status0.innerHTML = "<b>" + s.text + "</b>";
                    }
                });
            }
            if (index === "1") {
                status.map(s => {
                    if (s.value === res.data) {
                        status1.classList = s.class;
                        status1.style.display = "initial";
                        status1.innerHTML = "<b>" + s.text + "</b>";
                    }
                });
            }
        }).catch(() => {
            if (index === "0") {
                status0.style.display = "none";
            }
            if (index === "1") {
                status1.style.display = "none";
            }
        })
    };

    //Modal
    let modal, modalId, modalFooter, link, form, formaction;

    const showPostModal = e => {
        formaction = e.getAttribute("data-formaction");
        modalId = e.getAttribute("data-target");
        modal = document.querySelector(modalId);
        modalFooter = modal.querySelector(".modal-footer");
    };

    const showModal = e => {
        link = e.getAttribute("data-href");
        modalId = e.getAttribute("data-target");
        modal = document.querySelector(modalId);
        modalFooter = modal.querySelector(".modal-footer");
    };

    const goOn = () => {
        modal.querySelector(".modal-backdrop").click();
        window.location.href = link;
    };

    const submit = () => {
        form = document.querySelector(`form[action="${formaction}"]`);
        form.submit();
    };

    const goOnNewTab = () => {
        modal.querySelector(".modal-backdrop").click();
        window.open(link);
    };

    (function () {
        let selectPembimbingI = document.body.querySelector("[name=pembimbing_I_id]");
        let selectPembimbingII = document.body.querySelector("[name=pembimbing_II_id]");
        initStatusPembimbing(selectPembimbingI);
        initStatusPembimbing(selectPembimbingII);
    })();

</script>
@endsection
