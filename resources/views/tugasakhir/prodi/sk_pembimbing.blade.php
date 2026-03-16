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
                <li><a href="{{ url('/')}}">Home</a></li>
                <li class="active">SK Pengusulan Pembimbing</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Penetapan Pengusulan Pembimbing</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <div class="row" style="margin-bottom: 10px">
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{url('prodi/riwayat_sk_pengusulan')}}" class="btn btn-info">Riwayat</a>
                        </div>
                    </div>
                    <form method="post" action="{{url('prodi/sk_pengusulan')}}">
                        {{ csrf_field() }}
                        <table id="table" class="table table-th-block">
                            <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th><input type="checkbox" onClick="toggle(this)"></th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Pembimbing Utama</th>
                                <th>Pembimbing Pendamping</th>
                                <th>Set Pembimbing</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($penetapan_pengusulan as $key => $value)
                                <tr class="odd gradeX">
                                    <td width="1%" align="center">{{++$key}}</td>
                                    <td><input type="checkbox" class="data_usulan" name="data[{{$key-1}}]" value="{{$value->C_NPM}}" onchange="checking(this)"></td>
                                    <td>{{$value->C_NPM}}</td>
                                    <td>{{helper::getNamaMhs($value->C_NPM)}}</td>
                                    <td>{{helper::getDeskripsi($value->pembimbing_I_id)}}</td>
                                    <td>{{helper::getDeskripsi($value->pembimbing_II_id)}}</td>
                                    <td class="text-center"><a href="{{ url('prodi/set_pembimbing')}}/{{$value->C_NPM}}/2"><i class="fa fa-pencil icon-square icon-xs icon-primary"></i></a></td>
                                    <td><a class="btn btn-danger" href="{{ url('prodi/batal_set_pembimbing')}}/{{$value->C_NPM}}"><i class="fa fa-trash"></i>Batal</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <p align="left">
                            <button id="save" class="btn btn-primary btn-perspective" type="submit" disabled>Set Sk Pengusulan Pembimbing</button>
                        </p>
                    </form>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->


        </div><!-- /.container-fluid -->
    </div>
@endsection
@section("script")
    <script>
        const checking = e => {
            let table = document.getElementById("table");
            let tr = table.querySelectorAll("tbody tr");
            let btn = document.getElementById("save");
            let arr = [];
            Array.prototype.map.call(tr,t => {
                arr.push(t.querySelector("input[type=checkbox]").checked)
            });
            if(arr.includes(true)){
                btn.removeAttribute("disabled")
            }else{
                btn.setAttribute("disabled", "disabled")
            }
        }
    </script>
    <script>
    function toggle(source) {
            checkboxes = document.getElementsByClassName('data_usulan');
            let btn = document.getElementById("save");
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
            btn.removeAttribute("disabled")
    }
    </script>
@endsection




