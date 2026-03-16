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
            <li><a href="{{ url('/prodi/usulan_pembimbing')}}">Usulan Pembimbing</a></li>
            <li class="active">Set Pembimbing</li>
        </ol>


        <h3 class="page-heading">Detail Mahasiswa</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <form method="post" action="{{url('prodi/ubah_pembimbing_per_mahasiswa')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
                <fieldset>
                    <input type="hidden" name="nim" value="{{$data_mahasiswa->C_NPM}}">
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Pembimbing Utama</label>
                        <div class="col-xs-5">
                            <select id="pembimbing1" class="form-control bold-border" name="pembimbing_I_id"
                                onchange="initStatusPembimbing(this)" index="0">
                                @foreach ($cek as $key => $value)
                                <option value="{{$value->pembimbing_I_id}}">
                                    {{helper::getDeskripsi($value->pembimbing_I_id) }}
                                </option>
                                @endforeach
                                <option disabled>--</option>
                                @foreach ($data as $key => $value)
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
                            <select id="pembimbing2" class="form-control bold-border" name="pembimbing_II_id"
                                onchange="initStatusPembimbing(this)" index="1">
                                @foreach ($cek as $key => $value)
                                <option value="{{$value->pembimbing_II_id}}">
                                    {{helper::getDeskripsi($value->pembimbing_II_id)}}
                                </option>
                                @endforeach
                                <option disabled>--</option>
                                @foreach ($data as $key => $value)
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
                            <button class="btn btn-danger btn-perspective" type="button" onclick="showPostModal(this)"
                                data-target="#modalPrimary" data-toggle="modal"
                                data-formaction="{{url('prodi/ubah_pembimbing_per_mahasiswa')}}">Simpan</button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div><!-- /.the-box -->
    </div>
</div>
@endsection
{{--ModalTambah--}}
@section("modalPrimaryTitle")
Simpan
@endsection
@section("modalPrimaryBody")
Apakah Anda yakin ingin menyimpan data?
@endsection
@section("modalPrimaryFooter")
<button onclick="submit(this)" class="btn btn-default">Simpan</button>
@endsection

{{-- Modal Simpan Sementara --}}
{{--ModalRequest--}}
@section("modalInfoTitle")
Simpan Sementara
@endsection
@section("modalInfoBody")
Apakah Anda yakin ingin menyimpan sementara?
<br>
<span id="status" class="badge badge-danger"></span>
@endsection
@section("modalInfoFooter")
<button onclick="goOn(this)" class="btn btn-default">Simpan Sementara</button>
@endsection

@section("script")
<script>
    $('#tombol_simpan_sementara').on('click', function () {
        var nim = $('#nim').val();
        var pembimbing1 = $('#pembimbing1').val();
        var pembimbing2 = $('#pembimbing2').val();
        console.log(nim);
        console.log(pembimbing1);
        console.log(pembimbing2);

        axios.get(`/api/simpan_sementara_set_pembimbing/${nim}/${pembimbing1}/${pembimbing2}`)
            .then(res => {
                console.log('Status Data : ' + res.data);
                if (res.data == 'berhasil') {
                    Swal.fire({
                        title: "Status!",
                        text: "Data Berhasil Disimpan Sementara!",
                        icon: "success"
                    }).then(function () {
                        window.location = "https://thesis.fikom.app/prodi/usulan_pembimbing";
                    });
                } else {
                    Swal.fire({
                        title: "Status!",
                        text: "Data Gagal Ditambahkan!",
                        icon: "error"
                    }).then(function () {
                        window.location = "https://thesis.fikom.app/prodi/usulan_pembimbing";
                    });

                }
            })
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
        axios.get(`/prodi/usulan_tmp/pembimbing/getstatus/${index}/${id}/{{$data_mahasiswa->C_NPM}}`)
            .then(res => {
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

    const goOnNewTab = () => {
        modal.querySelector(".modal-backdrop").click();
        window.open(link);
    };


    const goOn = () => {
        modal.querySelector(".modal-backdrop").click();
        window.location.href = link;
    };


    const submit = () => {
        form = document.querySelector(`form[action="${formaction}"]`);
        form.submit();
    }

    (function () {
        let selectPembimbingI = document.body.querySelector("[name=pembimbing_I_id]");
        let selectPembimbingII = document.body.querySelector("[name=pembimbing_II_id]");
        initStatusPembimbing(selectPembimbingI);
        initStatusPembimbing(selectPembimbingII);
    })();
</script>
@endsection