@extends('tugasakhir.index')
@section('isi')
    <style>
        .modal-table td {
            vertical-align: middle;
        }

        .modal-table .label {
            display: inline-block;
            width: 100%;
            text-align: center;
            padding: 0.3em;
        }
    </style>
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
                <li class="active">Periode Ujian</li>
            </ol>
            <!-- End breadcrumb -->

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <h3 class="page-heading">List of Defense Period</h3>
            <div class="the-box">
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div
                        style="border: 2px solid teal; background-color: #d3f3f2; width: 100px; height: 30px; margin-right: 10px;">
                    </div>
                    <span>Complete</span>
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 10px;">
                    <div
                        style="border: 2px solid orange; background-color: #fee8d9; width: 100px; height: 30px; margin-right: 10px;">
                    </div>
                    <span>Partially Complete</span>
                </div>
                <div style="display: flex; align-items: center;">
                    <div
                        style="border: 2px solid red; background-color: #f9cfd5; width: 100px; height: 30px; margin-right: 10px;">
                    </div>
                    <span>Not Complete</span>
                </div>
                <br>


                <form action="" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table" id="datatable-example">
                            <thead class="the-box dark full">
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Completness</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $honorarium)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $honorarium->date }}</td>
                                        <td>
                                            @if ($honorarium->status_complete == 2)
                                                <div
                                                    style="border: 2px solid teal; background-color: #d3f3f2; width: 100px; height: 30px;">
                                                </div>
                                            @elseif ($honorarium->status_complete == 1)
                                                <div
                                                    style="border: 2px solid orange; background-color: #fee8d9; width: 100px; height: 30px;">
                                                </div>
                                            @else
                                                <div
                                                    style="border: 2px solid red; background-color: #f9cfd5; width: 100px; height: 30px;">
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('report_periode_ujian_by_date', $honorarium->date) }}" class="btn btn-info">
                                                <i class="fa fa-eye"></i> Detail</a>
                                        </td>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script></script>
@endsection
