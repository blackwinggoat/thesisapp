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
                <li class="active">Honorarium</li>
            </ol>
            <!-- End breadcrumb -->

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Honorarium List</h3>
            <div class="the-box">
                <div style="margin-bottom: 20px; text-align: right;">
                    <a href="{{route('history_honorarium')}}" type="button" class="btn btn-primary">
                        <i class="fa fa-history"></i> History
                    </a>
                </div>

                <form action="{{ route('honorarium_save_all_dosen') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table" id="datatable-example">
                            <thead class="the-box dark full">
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Nim</th>
                                    <th>Student Name</th>
                                    <th>Act As</th>
                                    <th>Exam Type</th>
                                    <th>Availability of Honorarium</th>
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
                                        <td>{{ $honorarium->tipe_ujian }}</td>
                                        <td>
                                            @if ($honorarium->tipe_ujian == '0' || $honorarium->tipe_ujian == '2')
                                                <span class="badge badge-danger">Unset</span>
                                            @else
                                                <input type="hidden" name="honorariums[{{ $loop->index }}][id]"
                                                    value="{{ $honorarium->id }}">
                                                <input type="hidden" name="honorariums[{{ $loop->index }}][C_NPM]"
                                                    value="{{ $honorarium->C_NPM }}">
                                                <input type="hidden" name="honorariums[{{ $loop->index }}][role]"
                                                    value="{{ $honorarium->role }}">
                                                <input type="checkbox" name="honorariums[{{ $loop->index }}][status]"
                                                    data-toggle="toggle" data-on="Yes" data-off="No"
                                                    data-honorarium-id="{{ $honorarium->id }}"
                                                    data-amount="{{ $honorarium->amount }}"
                                                    {{ $honorarium->status == 3 ? 'disabled checked' : '' }}
                                                    {{ $honorarium->status == 0 ? 'disabled' : '' }}
                                                    {{ $honorarium->status == 1 ? '' : '' }}>
                                            @endif
                                        </td>
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
                                <td style="border: 1px solid gray; padding: 10px"><strong>Honorarium to be
                                        received:</strong></td>
                                <td style="border: 1px solid gray; padding: 10px">Rp <span
                                        id="totalReceived">{{ number_format($totalReceived, 0, ',', '.') }}</span>,-</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid gray; padding: 10px"><strong>Unpaid Honorarium:</strong></td>
                                <td style="border: 1px solid gray; padding: 10px">Rp <span
                                        id="totalUnpaid">{{ number_format($totalUnpaid, 0, ',', '.') }}</span>,-</td>
                            </tr>
                        </table>
                    </div>
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                    </div>
                </form>


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
                    <div class="col-md-9" style="text-align: left;">
                        <div>
                            <div
                                style="display: inline-block; border: 2px solid #8cc152; background-color: #e1f7f4; padding: 5px 15px; margin-bottom: 10px;">
                                Available</div><br>
                            <div
                                style="display: inline-block; border: 2px solid #f6bb42; background-color: #fff5e1; padding: 5px 15px;">
                                Unavailable</div>
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
                let index = $(this).closest('tr').index();

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
