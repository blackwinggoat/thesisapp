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
                <li><a href="{{ url('/sk_pembimbing')}}">Surat Pengusulan Pembimbing TIM Ujian TA</a></li>
                <li class="active">Set Surat Pengusulan TIM Ujian TA</li>
            </ol>


            <h3 class="page-heading">Set Surat Pengusulan TIM Ujian TA</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <form method="post" action="{{url('prodi/surat_pengusulan_ujian_ta')}}" target="_blank">
                    {{ csrf_field() }}
                    <fieldset>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Nomor Surat</label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control bold-border" name="nomor" placeholder=""/>
                                <font color="red" size="2px">*000/H.20/TI/FIK-UMI/III/2019</font>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Perihal</label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control bold-border" name="perihal" value="Surat Penunjukan">
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Tanggal</label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control datepicker bold-border" data-date-format="dd-mm-yyyy" placeholder="dd-mm-yyyy" name="tgl" value="<?=date('d-m-Y');?>">
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Daftar Ujian TA</label>
                            <div class="table-responsive col-lg-6">


                                <table class="table table-th-block">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Periode</th>
                                        <th>Jumlah Peserta</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $a = 0 ?>
                                    @foreach($datax as $key => $value)
                                        <input type="hidden" name="data[{{$key}}]" value="{{$value->pendaftaran_id}}">
                                        <tr>
                                            <td>{{++$key}}</td>
                                            <td>{{$value->nama_periode}}</td>
                                            <td>{{$value->jml_peserta}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div><!-- /.table-responsive -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-xs-8" align="right">
                                <a href="{{url('prodi/sk_pembimbing')}}" class="btn btn-danger btn-perspective">Batal</a>
                                <button id="tombol_satu" class="btn btn-primary btn-perspective" type="button" onclick="showPostModal(this)" data-target="#modalPrimary" data-toggle="modal" data-formaction="{{url('prodi/surat_pengusulan_ujian_ta')}}">Simpan dan Cetak</button>
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
    Tambah Pengumuman
@endsection
@section("modalPrimaryBody")
    Apakah Anda yakin ingin menyimpan data?
        <br>
    <span id="status" class="badge badge-danger"></span>
@endsection
@section("modalPrimaryFooter")
    <button onclick="submit(this)" id="tombol_dua" class="btn btn-default">Tambah</button>
@endsection
@section("script")
    <script>

        $('#tombol_satu').on('click', function () {
            console.log("Selamat Datang di Bagian Satu");
            var nomor = $('input[name="nomor"]').val();
            var nomor_new = nomor.replace(/\//g, "");

            axios.get(`/api/cek_nomor_sk_ujian_ta/${nomor_new}`).then(res => {
                console.log(res.data);
                if (res.data == "tidak") {
                    $('#status').html("");
                    $("#tombol_dua").removeAttr("disabled");
                } else {
                    console.log("Ini Bagian Satu");
                    $('#tombol_dua').attr("disabled", "disabled");
                    $('#status').html("Nomor Sudah ada dalam database");
                }
            });

            if (nomor == "") {
                console.log("Ini Bagian Satu");
                $('#tombol_dua').attr("disabled", "disabled");
                $('#status').html("Nomor SK Belum Dimasukkan");
            } else if (nomor != "") {
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
            modal.querySelector(".modal-backdrop").click();
            window.location.href = "https://thesis.fikom.app/";
        }
    </script>
@endsection





