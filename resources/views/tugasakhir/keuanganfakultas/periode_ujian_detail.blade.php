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

        /* Style for the table cells */
        .table td,
        .table th {
            text-align: center;
            vertical-align: middle;
        }

        /* Style for the colored boxes (Complete, Partially Complete, Not Complete) */
        .complete-status {
            background-color: #d3f3f2;
        }

        .partial-status {
            background-color: #fee8d9;
        }

        .not-complete-status {
            background-color: #f9cfd5;
        }

        /* Align the Download icon to center */
        .download-icon {
            text-align: center;
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

            <h3 class="page-heading">List of Honorarium</h3>

            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Nim</th>
                                <th>Student Name</th>
                                <th>Type</th>
                                <th>KS</th>
                                <th>PU</th>
                                <th>PP</th>
                                <th>P1</th>
                                <th>P2</th>
                                <th>P3</th>
                                <th>Download</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $index => $honorarium)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $honorarium->C_NPM }}</td>
                                    <td>{{ helper::getNamaMhs($honorarium->C_NPM) }}</td>
                                    <td>{{ $honorarium->tipe_ujian }}</td>

                                    <!-- Status cells (use conditional formatting to assign colors based on the status) -->
                                    <td
                                        class="@if ($honorarium->KS_Stat == 3) complete-status @elseif($honorarium->KS_Stat == 2) partial-status @else not-complete-status @endif">
                                    </td>
                                    <td
                                        class="@if ($honorarium->PU_Stat == 3) complete-status @elseif($honorarium->PU_Stat == 2) partial-status @else not-complete-status @endif">
                                    </td>
                                    <td
                                        class="@if ($honorarium->PP_Stat == 3) complete-status @elseif($honorarium->PP_Stat == 2) partial-status @else not-complete-status @endif">
                                    </td>
                                    <td
                                        class="@if ($honorarium->P1_Stat == 3) complete-status @elseif($honorarium->P1_Stat == 2) partial-status @else not-complete-status @endif">
                                    </td>
                                    <td
                                        class="@if ($honorarium->P2_Stat == 3) complete-status @elseif($honorarium->P2_Stat == 2) partial-status @else not-complete-status @endif">
                                    </td>
                                    <td
                                        class="@if ($honorarium->P3_Stat == 3) complete-status @elseif($honorarium->P3_Stat == 2) partial-status @else not-complete-status @endif">
                                    </td>

                                    <td class="download-icon">
                                        <a href="{{ route('cetak_honorarium_per_mahasiswa', ['id' => $honorarium->id]) }}"
                                            class="btn btn-danger">
                                            <i class="fa fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Buttons for downloading reports -->
            </div>
        </div>
    </div>
@endsection
