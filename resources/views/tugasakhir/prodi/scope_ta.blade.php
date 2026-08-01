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
                <li><a href="#fakelink">Home</a></li>
                <li class="active"> Bidang Ilmu TA</li>
            </ol>

            <!-- Begin breadcrumb -->
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="index.html"><i class="fa fa-home"></i></a></li>
                <li><a href="#fakelink">Chart or graph</a></li>
                <li class="active">Morris chart</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN MORRIS CHART -->
            <div class="row">
                <div class="col-sm-6">
                    <div class="the-box">
                        <h4 class="small-title">Grafik Jumlah Lulusan pertahun Ajaran</h4>
                        <div id="scope-ta-lulusan-periode" style="height: 250px;"></div>
                    </div><!-- .the-box -->
                </div><!-- /.col-sm-6 -->
                <div class="col-sm-6">
                    <div class="the-box">
                        <h4 class="small-title">Grafik Jumlah Lulusan berdasarkan Bidang Ilmu</h4>
                        <div id="scope-ta-lulusan-bidang" style="height: 250px;"></div>
                    </div><!-- .the-box  -->
                </div><!-- /.col-sm-6 -->
            </div>
            <!-- END MORRIS CHART -->

            <h3 class="page-heading">Form Bidang Ilmu Tugas Akhir</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <form method="post" action="{{url('prodi/scope_add')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Bidang Ilmu</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="bidang_ilmu"/>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Status</label>
                            <div class="col-lg-5">
                                <select class="form-control bold-border" name="status_aktif">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>

                        <br><br>
                        <div class="form-group">
                            <div class="col-xs-7" align="right">
                                <button class="btn btn-primary btn-perspective" type="button" onclick="showPostModal(this)" data-formaction="{{url('prodi/scope_add')}}" data-target="#modalPrimary" data-toggle="modal">Simpan</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div><!-- /.the-box -->
            <!-- End breadcrumb -->
            <h3 class="page-heading">Daftar Bidang Ilmu Tugas Akhir</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nama Bidang Ilmu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->bidang_ilmu}}</td>
                                <td>
                                    @if(($value->status_aktif ?? 1) == 1)
                                        <span class="label label-success">Aktif</span>
                                    @else
                                        <span class="label label-default">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <button
                                        class="btn btn-warning"
                                        type="button"
                                        data-toggle="modal"
                                        data-target="#modalEditBidangIlmu"
                                        data-id="{{ $value->bidangilmu_id }}"
                                        data-bidang_ilmu="{{ $value->bidang_ilmu }}"
                                        data-status_aktif="{{ $value->status_aktif ?? 1 }}"
                                        onclick="showEditModal(this)">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{ url('prodi/scope_del/'.$value->bidangilmu_id)}}"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->

            <div class="modal fade" id="modalEditBidangIlmu" tabindex="-1" role="dialog" aria-labelledby="modalEditBidangIlmuLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="formEditBidangIlmu" method="post">
                            {{ csrf_field() }}
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="modalEditBidangIlmuLabel">Edit Bidang Ilmu</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nama Bidang Ilmu</label>
                                    <input type="text" id="edit_bidang_ilmu" name="bidang_ilmu" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select id="edit_status_aktif" name="status_aktif" class="form-control">
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div><!-- /.container-fluid -->
    </div>
@endsection

{{--ModalTambah--}}
@section("modalPrimaryTitle")
    Tambah Bidang Ilmu
@endsection
@section("modalPrimaryBody")
    Apakah Anda yakin ingin menambah bidang ilmu?
@endsection
@section("modalPrimaryFooter")
    <button onclick="submit(this)" class="btn btn-default">Tambah</button>
@endsection

{{--ModalHapus--}}
@section("modalDangerTitle")
    Hapus Bidang Ilmu
@endsection
@section("modalDangerBody")
    Apakah Anda yakin ingin menghapus data?
@endsection
@section("modalDangerFooter")
    <button onclick="goOn(this)" class="btn btn-default">Hapus</button>
@endsection

@section("script")
    <script>
        const lulusanPeriodeChart = @json($lulusanPeriodeChart ?? []);
        const lulusanBidangChart = @json($lulusanBidangChart ?? []);

        const renderNoData = (elementId, text) => {
            const el = document.getElementById(elementId);
            if (!el) {
                return;
            }

            el.innerHTML = `<div style="height:100%;display:flex;align-items:center;justify-content:center;color:#777;text-align:center;padding:0 16px;">${text}</div>`;
        };

        if (document.getElementById('scope-ta-lulusan-periode')) {
            if (lulusanPeriodeChart.length > 0) {
                Morris.Line({
                    element: 'scope-ta-lulusan-periode',
                    data: lulusanPeriodeChart,
                    xkey: 'y',
                    ykeys: ['total'],
                    labels: ['Jumlah Lulusan'],
                    lineColors: ['#3BAFDA'],
                    parseTime: false,
                    hideHover: 'auto',
                    resize: true
                });
            } else {
                renderNoData('scope-ta-lulusan-periode', 'Belum ada data lulusan untuk ditampilkan');
            }
        }

        if (document.getElementById('scope-ta-lulusan-bidang')) {
            if (lulusanBidangChart.length > 0) {
                Morris.Bar({
                    element: 'scope-ta-lulusan-bidang',
                    data: lulusanBidangChart,
                    xkey: 'y',
                    ykeys: ['total'],
                    labels: ['Jumlah Lulusan'],
                    barColors: ['#F6BB42'],
                    xLabelAngle: 35,
                    hideHover: 'auto',
                    resize: true
                });
            } else {
                renderNoData('scope-ta-lulusan-bidang', 'Belum ada data bidang ilmu lulusan');
            }
        }

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
            window.location.href = link;
        };

        const submit = () => {
            form = document.querySelector(`form[action="${formaction}"]`);
            form.submit();
        };

        const showEditModal = (button) => {
            const id = button.getAttribute('data-id');
            const bidangIlmu = button.getAttribute('data-bidang_ilmu') || '';
            const statusAktif = button.getAttribute('data-status_aktif') || '1';

            const formEdit = document.getElementById('formEditBidangIlmu');
            const inputBidangIlmu = document.getElementById('edit_bidang_ilmu');
            const selectStatusAktif = document.getElementById('edit_status_aktif');

            formEdit.setAttribute('action', `{{ url('prodi/scope_update') }}/${id}`);
            inputBidangIlmu.value = bidangIlmu;
            selectStatusAktif.value = statusAktif;
        };
    </script>
@endsection
