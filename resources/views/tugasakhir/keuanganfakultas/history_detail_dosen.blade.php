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
                <li class="active">History Honorarium</li>
            </ol>
            <!-- End breadcrumb -->

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Paid Honorarium List</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table" id="datatable-example">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Nim</th>
                                <th>Student Name</th>
                                <th>Act As</th>
                                <th>Honorarium</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalReceived = 0;
                                $totalUnpaid = 0;
                            @endphp
                            @foreach ($data as $honorarium)
                                @php
                                    if ($honorarium->status == 3) {
                                        $totalReceived += $honorarium->amount;
                                    } elseif ($honorarium->status == 1 || $honorarium->status == 0) {
                                        $totalUnpaid += $honorarium->amount;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $honorarium->date }}</td>
                                    <td>{{ $honorarium->C_NPM }}</td>
                                    <td>{{ helper::getNamaMhs($honorarium->C_NPM) }}</td>
                                    <td>{{ $honorarium->role }}</td>
                                    <td>
                                        @if ($honorarium->tipe_ujian == '0' || $honorarium->tipe_ujian == '2')
                                            <span class="badge badge-danger">Unset</span>
                                        @elseif ($honorarium->status == 1)
                                            <span class="badge badge-success amount-display">Rp
                                                {{ number_format($honorarium->amount, 0, ',', '.') }}
                                            </span>
                                        @elseif ($honorarium->status == 0)
                                            <span class="badge badge-warning amount-display">Rp
                                                {{ number_format($honorarium->amount, 0, ',', '.') }}
                                            </span>
                                        @elseif ($honorarium->status == 3)
                                            <span class="badge badge-primary amount-display">Rp
                                                {{ number_format($honorarium->amount, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="badge badge-danger">Tidak Diketahui</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
                <div style="margin-top: 20px; text-align: right;">
                    <table style="display: inline-table; border-collapse: separate;border: 1px solid gray;">
                        <tr>
                            <td style="border: 1px solid gray; padding: 10px"><strong>Paid Honorarium:</strong></td>
                            <td style="border: 1px solid gray; padding: 10px">Rp <span
                                    id="totalReceived">{{ number_format($totalReceived, 0, ',', '.') }}</span>,-</td>
                        </tr>
                    </table>
                </div>


                <div class="row">
                    <div class="col-md-3">
                        <div style="border: 2px dashed #007bff; background-color: #e7f1ff; padding: 10px;">
                            <small>
                                <p>KS: Ketua Sidang</p>
                                <p>PU: Pembimbing Utama</p>
                                <p>PP: Pembimbing Pendamping</p>
                                <p>P1: Penguji I</p>
                                <p>P2: Penguji II</p>
                                <p>P3: Penguji III</p>
                            </small>
                        </div>
                    </div>
                </div>

            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div><!-- /.page-content -->
@endsection

@section('script')
    <script>
        $(function() {
            $('input[data-toggle="toggle"]').bootstrapToggle();

            let totalReceived = parseFloat('{{ $totalReceived }}');
            let totalUnpaid = parseFloat('{{ $totalUnpaid }}');

            $('input[data-toggle="toggle"]').change(function() {
                let isChecked = $(this).prop('checked');
                let amount = parseFloat($(this).data('amount'));
                let index = $(this).closest('tr').index(); // Get the index of the row

                // Update the hidden input with the current status
                $('#status-' + index).val(isChecked ? 3 : 1);

                if (isChecked) {
                    totalReceived += amount;
                    totalUnpaid -= amount;
                } else {
                    totalReceived -= amount;
                    totalUnpaid += amount;
                }

                // Update displayed totals
                $('#totalReceived').text(totalReceived.toLocaleString('id-ID'));
                $('#totalUnpaid').text(totalUnpaid.toLocaleString('id-ID'));
            });
        });
    </script>
@endsection
