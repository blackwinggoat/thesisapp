@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">

        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading">SIMPRODI <small>Tugas Akhir</small></h1>
            <!-- End page heading -->
            @if (Auth::user()->level == 5)
                @php
                    $semesterRange = helper::getCurrentSemesterDateRange();
                @endphp
                <h4>{{ helper::getPeriodeSemester() }}</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-success tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Proposal</p>
                                @if (Auth::user()->name == 'proditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(0, true)) }}</h1>
                                @elseif (Auth::user()->name == 'prodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(0, true)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(0, true)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-success -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 0]) }}"
                                    class="btn btn-success btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-primary tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Ujian TA</p>
                                @if (Auth::user()->name == 'proditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(2, true)) }}</h1>
                                @elseif (Auth::user()->name == 'prodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(2, true)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(2, true)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-primary -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 2]) }}"
                                    class="btn btn-primary btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-danger tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Lulusan</p>
                                @if (Auth::user()->name == 'proditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(3, true)) }}</h1>
                                @elseif (Auth::user()->name == 'prodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(3, true)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(3, true)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-danger -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 3]) }}"
                                    class="btn btn-danger btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                </div>
                <h4>Semua Periode</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-success tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Proposal</p>
                                @if (Auth::user()->name == 'proditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(0, false)) }}</h1>
                                @elseif (Auth::user()->name == 'prodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(0, false)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(0, false)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-success -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('prodi/detail_status_bimbingan_mahasiswa/0') }}"
                                    class="btn btn-success btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-primary tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Ujian TA</p>
                                @if (Auth::user()->name == 'proditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(2, false)) }}</h1>
                                @elseif (Auth::user()->name == 'prodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(2, false)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(2, false)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-primary -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('prodi/detail_status_bimbingan_mahasiswa/2') }}"
                                    class="btn btn-primary btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-danger tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Lulusan</p>
                                @if (Auth::user()->name == 'proditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(3, false)) }}</h1>
                                @elseif (Auth::user()->name == 'prodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(3, false)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(3, false)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-danger -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('prodi/detail_status_bimbingan_mahasiswa/3') }}"
                                    class="btn btn-danger btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                </div><!-- /.row -->
                @php
                    $homeScopeLulusanPeriode = helper::getScopeTaLulusanPeriodeChartByAuthUser();
                    $homeScopeLulusanBidang = helper::getScopeTaLulusanBidangChartByAuthUser();
                    $homeStatusBimbinganProdi = helper::getStatusBimbinganSummaryProdiByUsername(auth()->user()->name);
                    $homeRataLamaBimbinganProdiPerAngkatan = helper::getRataLamaProsesBimbinganProdiPerAngkatanByUsername(auth()->user()->name);
                    $homeScopeLulusanPeriode = is_array($homeScopeLulusanPeriode) ? $homeScopeLulusanPeriode : [];
                    $homeScopeLulusanBidang = is_array($homeScopeLulusanBidang) ? $homeScopeLulusanBidang : [];
                    $homeStatusBimbinganProdi = is_object($homeStatusBimbinganProdi) ? $homeStatusBimbinganProdi : (object) ['y' => '', 'PP' => 0, 'PUM' => 0, 'L' => 0];
                    $homeRataLamaBimbinganProdiPerAngkatan = is_array($homeRataLamaBimbinganProdiPerAngkatan) ? $homeRataLamaBimbinganProdiPerAngkatan : [];
                @endphp
                <div class="row">
                    <div class="col-sm-6">
                        <div class="the-box">
                            <h4 class="small-title">STATUS BIMBINGAN</h4>
                            <div id="home-prodi-status-bimbingan" style="height: 280px;"></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="the-box">
                            <h4 class="small-title">RATA-RATA LAMA PROSES BIMBINGAN PER ANGKATAN (BULAN)</h4>
                            <div id="home-prodi-lama-bimbingan-angkatan" style="height: 280px;"></div>
                            <div style="margin-top: 10px; display: flex; gap: 16px; flex-wrap: wrap;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#3BAFDA;"></span>
                                    <strong>TI (bulan)</strong>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#F6BB42;"></span>
                                    <strong>SI (bulan)</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="the-box">
                            <h4 class="small-title">GRAFIK JUMLAH LULUSAN PER TAHUN AJARAN</h4>
                            <div id="home-prodi-lulusan-periode" style="height: 280px;"></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="the-box">
                            <h4 class="small-title">GRAFIK JUMLAH LULUSAN BERDASARKAN BIDANG ILMU</h4>
                            <div id="home-prodi-lulusan-bidang" style="height: 280px;"></div>
                        </div>
                    </div>
                </div>
            @elseif(Auth::user()->level == 6)
                @php
                    $semesterRange = helper::getCurrentSemesterDateRange();
                @endphp
                <h4>{{ helper::getPeriodeSemester() }}</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-success tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Proposal</p>
                                @if (Auth::user()->name == 'akademikproditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(0, true)) }}</h1>
                                @elseif (Auth::user()->name == 'akademikprodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(0, true)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(0, true)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-success -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('akademikprodi.tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 0]) }}"
                                    class="btn btn-success btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-primary tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Ujian TA</p>
                                @if (Auth::user()->name == 'akademikproditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(2, true)) }}</h1>
                                @elseif (Auth::user()->name == 'akademikprodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(2, true)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(2, true)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-primary -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('akademikprodi.tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 2]) }}"
                                    class="btn btn-primary btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-danger tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Lulusan</p>
                                @if (Auth::user()->name == 'akademikproditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(3, true)) }}</h1>
                                @elseif (Auth::user()->name == 'akademikprodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(3, true)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(3, true)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-danger -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('akademikprodi.tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 3]) }}"
                                    class="btn btn-danger btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                </div>
                <h4>Semua Periode</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-success tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Proposal</p>
                                @if (Auth::user()->name == 'akademikproditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(0, false)) }}</h1>
                                @elseif (Auth::user()->name == 'akademikprodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(0, false)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(0, false)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-success -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('akademikprodi/detail_status_bimbingan_mahasiswa/0') }}"
                                    class="btn btn-success btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-primary tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Ujian TA</p>
                                @if (Auth::user()->name == 'akademikproditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(2, false)) }}</h1>
                                @elseif (Auth::user()->name == 'akademikprodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(2, false)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(2, false)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-primary -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('akademikprodi/detail_status_bimbingan_mahasiswa/2') }}"
                                    class="btn btn-primary btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-danger tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Lulusan</p>
                                @if (Auth::user()->name == 'akademikproditi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusTi(3, false)) }}</h1>
                                @elseif (Auth::user()->name == 'akademikprodisi')
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatusSi(3, false)) }}</h1>
                                @else
                                    <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(3, false)) }}</h1>
                                @endif
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-danger -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('akademikprodi/detail_status_bimbingan_mahasiswa/3') }}"
                                    class="btn btn-danger btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                </div>
            @elseif(Auth::user()->level == 3)
                @php
                    $semesterRange = helper::getCurrentSemesterDateRange();
                @endphp
                <h4>{{ helper::getPeriodeSemester() }}</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-success tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Proposal</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(0, true)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-success -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('wakildekan.tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 0]) }}"
                                    class="btn btn-success btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-primary tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Ujian TA</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(2, true)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-primary -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('wakildekan.tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 2]) }}"
                                    class="btn btn-primary btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-danger tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Lulusan</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(3, true)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-danger -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('wakildekan.tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 3]) }}"
                                    class="btn btn-danger btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                </div><!-- /.row -->
                <h4>Semua Periode</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-success tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Proposal</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(0, false)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-success -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('wakildekan/detail_status_bimbingan_mahasiswa/0') }}"
                                    class="btn btn-success btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-primary tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Ujian TA</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(2, false)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-primary -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('wakildekan/detail_status_bimbingan_mahasiswa/2') }}"
                                    class="btn btn-primary btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-danger tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Lulusan</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(3, false)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-danger -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('wakildekan/detail_status_bimbingan_mahasiswa/3') }}"
                                    class="btn btn-danger btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                </div><!-- /.row -->
            @elseif(Auth::user()->level == 2)
                @php
                    $semesterRange = helper::getCurrentSemesterDateRange();
                @endphp
                <h4>{{ helper::getPeriodeSemester() }}</h4>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-success tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Proposal</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(0, true)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-success -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('dekan.tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 0]) }}"
                                    class="btn btn-success btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-primary tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Ujian TA</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(2, true)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-primary -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('dekan.tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 2]) }}"
                                    class="btn btn-primary btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-danger tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Lulusan</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(3, true)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-danger -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ route('dekan.tampilDetailStatusBimbinganDenganFilterTanggal', ['tanggal_dari' => $semesterRange->start_date, 'tanggal_sampai' => $semesterRange->end_date, 'status' => 3]) }}"
                                    class="btn btn-danger btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-success tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Proposal</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(0, false)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-success -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('dekan/detail_status_bimbingan_mahasiswa/0') }}"
                                    class="btn btn-success btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-primary tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Persiapan Ujian TA</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(2, false)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-primary -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('dekan/detail_status_bimbingan_mahasiswa/2') }}"
                                    class="btn btn-primary btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                    <div class="col-sm-4">
                        <div class="the-box no-border bg-danger tiles-information">
                            <i class="fa fa-users icon-bg"></i>
                            <div class="tiles-inner text-center">
                                <p>Lulusan</p>
                                <h1 class="bolded">{{ count(helper::getStatusBimbinganByStatus(3, false)) }}</h1>
                                <div class="progress no-rounded progress-xs">
                                    <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80"
                                        aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                    </div><!-- /.progress-bar .progress-bar-danger -->
                                </div><!-- /.progress .no-rounded -->
                                <a href="{{ url('dekan/detail_status_bimbingan_mahasiswa/3') }}"
                                    class="btn btn-danger btn-perspective">Lihat Detail</a>
                            </div><!-- /.tiles-inner -->
                        </div><!-- /.the-box no-border -->
                    </div><!-- /.col-sm-3 -->
                </div><!-- /.row -->
            @endif
            @if (Auth::user()->level == 8)
                @php
                    $kontakMahasiswa = helper::getCurrentMahasiswaContactByAuthUser();
                    $kontakMahasiswaKurang = helper::getCurrentMahasiswaContactMissingFields();
                    $showPopupKelengkapanMahasiswa = helper::shouldShowCurrentMahasiswaContactPopup();
                @endphp
                '
                <!-- BEGIN EXAMPLE ALERT -->
                <div class="alert alert-warning alert-bold-border fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><strong>Welcome! {{ helper::getNamaMhs(auth()->user()->name) }}</strong></p>

                    @if (helper::getStatusBimbinganByNim(auth()->user()->name) == 0)
                        <p class="text-mute visible-lg-inlined">Informasi bimbingan anda saat ini ialah <a
                                class="alert-link" href="#fakelink">
                                <h4 class="visible-lg-inline">Persiapan Ujian Proposal</h4>
                            @elseif(helper::getStatusBimbinganByNim(auth()->user()->name) == 2)
                                <p class="text-muted visible-lg-inline">Informasi bimbingan anda saat ini ialah <a
                                        class="alert-link" href="#fakelink">
                                        <h4 class="visible-lg-inline">Persiapan Ujian Meja</h4>
                                    @elseif(helper::getStatusBimbinganByNim(auth()->user()->name) == 3)
                                        <p class="text-muted visible-lg-inline">Selamat Anda Telah Menyelesaikan Bimbingan
                                            dan dinyatakan sebagai<a class="alert-link" href="#fakelink">
                                                <h4 class="visible-lg-inline">Lulusan</h4>
                                            @else
                                                <p class="text-muted visible-lg-inline">Anda belum sampai ke tahap
                                                    bimbingan<a class="alert-link" href="#fakelink">
                    @endif
                    </a><i class="fa fa-smile-o"></i></p>
                </div>
                <!-- END EXAMPLE ALERT -->'
                @if (session('mhs_contact_success'))
                    <div class="alert alert-success" role="alert">
                        <strong>Berhasil! </strong>{{ session('mhs_contact_success') }}
                    </div>
                @endif
                @if (session('mhs_contact_error'))
                    <div class="alert alert-danger" role="alert">
                        <strong>Gagal! </strong>{{ session('mhs_contact_error') }}
                    </div>
                @endif
                <div class="the-box">
                    <div class="row">
                        <div class="col-md-6">
                            <h3>Pengumuman Terbaru</h3>
                        </div>
                        <!--
                        <div class="col-md-6 text-right">
                            <h3><a href="{{ url('/mhs/pengumuman/') }}">Lainnya</a></h3>
                        </div>
                        -->
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="the-box no-border full">
                                <button class="btn btn-block btn-primary btn-square"></button>
                                <ul class="widget-newsticker media-list">
                                   <!-- @foreach (helper::get5Pengumuman() as $value)
                                        <li class="media">
                                            <div class="media-left">
                                                <img class="media-object"
                                                    src="{{ \App\Helper::announcementImageUrl($value->gambar) }}" alt="Image">
                                            </div>
                                            <div class="media-body">
                                                <h4 class="media-heading"><a
                                                        href="{{ url('mhs/pengumuman/show/' . $value->pengumuman_id) }}">{{ $value->judul }}</a>
                                                </h4>
                                                <p class="text-muted"><small>Terbit : {{ $value->last_update }}</small>
                                                </p>
                                                <p>
                                                    {{ mb_strimwidth(strip_tags($value->isi), 0, 100, '...') }}
                                                </p>
                                            </div>
                                        </li>
                                    @endforeach
                                    -->
                                </ul>
                                <button class="btn btn-block btn-primary btn-square"></button>
                            </div><!-- /.the-box no-border -->
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalKelengkapanMahasiswa" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false"
                    aria-labelledby="modalKelengkapanMahasiswaLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post" action="{{ url('/mhs/kelengkapan_kontak') }}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modalKelengkapanMahasiswaLabel">Lengkapi Data Mahasiswa</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Silakan lengkapi data Anda terlebih dahulu. Nomor WhatsApp dan foto wajib diisi sebelum Anda dapat menggunakan fitur lain di sisi mahasiswa.</p>
                                    @if ($errors->any())
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Periksa kembali data foto/kontak:</strong>
                                            <ul style="margin: 8px 0 0 18px; padding: 0;">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if (!empty($kontakMahasiswaKurang))
                                        <p><strong>Data yang masih kosong:</strong> {{ implode(', ', $kontakMahasiswaKurang) }}</p>
                                    @endif
                                    <div class="form-group">
                                        <label>NIM</label>
                                        <input type="text" class="form-control" value="{{ $kontakMahasiswa->C_NPM ?? auth()->user()->name }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Mahasiswa</label>
                                        <input type="text" class="form-control" value="{{ $kontakMahasiswa->NAMA_MAHASISWA ?? helper::getNamaMhs(auth()->user()->name) }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label>Nomor WhatsApp</label>
                                        <input type="text" class="form-control" name="no_wa"
                                            value="{{ old('no_wa', $kontakMahasiswa->no_wa ?? '') }}"
                                            placeholder="Contoh: 6281234567890">
                                        <small class="text-muted">Wajib diisi. Boleh ketik `0812...`, `62812...`, atau `+62812...`. Sistem akan menyimpan dalam format `62...`.</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Foto</label>
                                        <input type="file" class="form-control" name="foto" accept="image/jpeg,image/png,image/webp"
                                            @if (empty($kontakMahasiswa->D_FOTO_MAHASISWA)) required @endif>
                                        <small class="text-muted">Wajib diisi. Format JPEG, PNG, atau WebP, ukuran maksimal 5 MB.</small>
                                    </div>
                                    <div class="form-group">
                                        <label>ID Telegram</label>
                                        <input type="text" class="form-control" name="id_telegram"
                                            value="{{ old('id_telegram', $kontakMahasiswa->id_telegram ?? '') }}"
                                            placeholder="Contoh: @username_telegram">
                                        <small class="text-muted">Opsional. Gunakan format seragam seperti `@username_telegram`.</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <div class="the-box">
                @if (Auth::user()->level == 7)
                    @php
                        $statusBimbinganDosen = helper::getStatusBimbinganSummaryByDosen(auth()->user()->name);
                        $ringkasanPeranPenguji = helper::getRingkasanPeranPengujiAktifByDosen(auth()->user()->name);
                        $komposisiPeranPembimbing = helper::getKomposisiPeranPembimbingAktifByDosen(auth()->user()->name);
                        $rataLamaBimbinganPerAngkatan = helper::getRataLamaProsesBimbinganDosenPerAngkatan(auth()->user()->name);
                        $pieProposal = [
                            ['label' => 'Pembimbing Utama', 'value' => $komposisiPeranPembimbing['proposal_utama'] ?? 0],
                            ['label' => 'Pembimbing Pendamping', 'value' => $komposisiPeranPembimbing['proposal_pendamping'] ?? 0],
                        ];
                        $totalPieProposal = collect($pieProposal)->sum('value');
                        $legendProposal = collect($pieProposal)->map(function ($item, $index) use ($totalPieProposal) {
                            $warna = $index === 0 ? '#3BAFDA' : '#8CC152';
                            $persen = $totalPieProposal > 0 ? round(($item['value'] / $totalPieProposal) * 100, 1) : 0;
                            return [
                                'label' => $item['label'],
                                'value' => $item['value'],
                                'persen' => $persen,
                                'warna' => $warna,
                            ];
                        })->all();

                        $pieUjianMeja = [
                            ['label' => 'Pembimbing Utama', 'value' => $komposisiPeranPembimbing['ujian_utama'] ?? 0],
                            ['label' => 'Pembimbing Pendamping', 'value' => $komposisiPeranPembimbing['ujian_pendamping'] ?? 0],
                        ];
                        $totalPieUjianMeja = collect($pieUjianMeja)->sum('value');
                        $legendUjianMeja = collect($pieUjianMeja)->map(function ($item, $index) use ($totalPieUjianMeja) {
                            $warna = $index === 0 ? '#F6BB42' : '#37BC9B';
                            $persen = $totalPieUjianMeja > 0 ? round(($item['value'] / $totalPieUjianMeja) * 100, 1) : 0;
                            return [
                                'label' => $item['label'],
                                'value' => $item['value'],
                                'persen' => $persen,
                                'warna' => $warna,
                            ];
                        })->all();

                        $profilDosen = helper::getCurrentDosenProfileByAuthUser();
                        $profilDosenKurang = helper::getCurrentDosenProfileMissingFields();
                        $showPopupKelengkapanDosen = count($profilDosenKurang) > 0;
                    @endphp
                    @if (session('dosen_profile_success'))
                        <div class="alert alert-success" role="alert">
                            <strong>Berhasil! </strong>{{ session('dosen_profile_success') }}
                        </div>
                    @endif
                    @if (session('dosen_profile_error'))
                        <div class="alert alert-danger" role="alert">
                            <strong>Gagal! </strong>{{ session('dosen_profile_error') }}
                        </div>
                    @endif
                    <div style="margin-bottom: 8px;">
                        <span class="label label-primary" style="font-size: 11px;">
                            Quick Link
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ url('dsn/hasil_proposal') }}" style="text-decoration: none;">
                                <div class="the-box" style="background: #3BAFDA; color: #fff; min-height: 130px;">
                                    <div style="font-size: 13px; font-weight: 600; text-transform: uppercase;">Menguji Proposal Aktif</div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top: 10px;">
                                        <div style="font-size: 34px; font-weight: 700; line-height: 1.2;">
                                            {{ $ringkasanPeranPenguji['proposal'] ?? 0 }}
                                        </div>
                                        <i class="fa fa-file-text-o" style="font-size: 34px; opacity: 0.95;"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ url('dsn/hasil_ujianmeja') }}" style="text-decoration: none;">
                                <div class="the-box" style="background: #48CFAD; color: #fff; min-height: 130px;">
                                    <div style="font-size: 13px; font-weight: 600; text-transform: uppercase;">Menguji Ujian TA Aktif</div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top: 10px;">
                                        <div style="font-size: 34px; font-weight: 700; line-height: 1.2;">
                                            {{ $ringkasanPeranPenguji['ujian_ta'] ?? 0 }}
                                        </div>
                                        <i class="fa fa-graduation-cap" style="font-size: 34px; opacity: 0.95;"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ url('dsn/rekap_nilai_proposal') }}" style="text-decoration: none;">
                                <div class="the-box" style="background: #F6BB42; color: #fff; min-height: 130px;">
                                    <div style="font-size: 13px; font-weight: 600; text-transform: uppercase;">Ketua Sidang Proposal Aktif</div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top: 10px;">
                                        <div style="font-size: 34px; font-weight: 700; line-height: 1.2;">
                                            {{ $ringkasanPeranPenguji['ketua_sidang_proposal'] ?? 0 }}
                                        </div>
                                        <i class="fa fa-gavel" style="font-size: 34px; opacity: 0.95;"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ url('dsn/rekap_nilai_ujian_ta') }}" style="text-decoration: none;">
                                <div class="the-box" style="background: #AC92EC; color: #fff; min-height: 130px;">
                                    <div style="font-size: 13px; font-weight: 600; text-transform: uppercase;">Ketua Sidang Ujian TA Aktif</div>
                                    <div style="display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top: 10px;">
                                        <div style="font-size: 34px; font-weight: 700; line-height: 1.2;">
                                            {{ $ringkasanPeranPenguji['ketua_sidang_ujian_ta'] ?? 0 }}
                                        </div>
                                        <i class="fa fa-balance-scale" style="font-size: 34px; opacity: 0.95;"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="the-box">
                                <h4 class="small-title">STATUS BIMBINGAN DOSEN</h4>
                                <div id="morris-bar" style="height: 360px;"></div>
                            </div><!-- .the-box -->
                        </div>
                        <div class="col-md-6">
                            <div class="the-box">
                                <h4 class="small-title">RATA-RATA LAMA PROSES BIMBINGAN PER ANGKATAN (BULAN)</h4>
                                <div id="morris-dosen-lama-bimbingan-angkatan" style="height: 360px;"></div>
                                <div style="margin-top: 10px; display: flex; gap: 16px; flex-wrap: wrap;">
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#3BAFDA;"></span>
                                        <strong>TI (bulan)</strong>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#F6BB42;"></span>
                                        <strong>SI (bulan)</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="the-box">
                                <h4 class="small-title">KOMPOSISI PERSIAPAN PROPOSAL</h4>
                                <div class="row text-center" style="margin-top: 10px;">
                                    @foreach ($legendProposal as $item)
                                        <div class="col-xs-6" style="margin-bottom: 10px;">
                                            <div class="js-easypie js-easypie-proposal"
                                                data-percent="{{ $item['persen'] }}"
                                                data-bar-color="{{ $item['warna'] }}"
                                                style="margin: 0 auto 8px auto;">
                                                <span style="font-size: 18px; font-weight: 700;">
                                                    {{ number_format($item['persen'], 1) }}%
                                                </span>
                                            </div>
                                            <div style="font-weight: 600;">{{ $item['label'] }}</div>
                                            <div><strong>{{ $item['value'] }}</strong> mahasiswa</div>
                                        </div>
                                    @endforeach
                                </div>
                                <div style="margin-top: 10px;">
                                    @foreach ($legendProposal as $item)
                                        <div style="display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #f1f1f1;">
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:{{ $item['warna'] }};"></span>
                                                <span>{{ $item['label'] }}</span>
                                            </div>
                                            <div>
                                                <strong>{{ $item['value'] }}</strong> mahasiswa
                                                ({{ number_format($item['persen'], 1) }}%)
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center" style="margin-top: 12px;">
                                    <a href="{{ url('dsn/detail_pembimbing') }}/{{ auth()->user()->name }}" class="btn btn-primary btn-sm">
                                        Lihat Detail Pembimbing
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="the-box">
                                <h4 class="small-title">KOMPOSISI PERSIAPAN UJIAN MEJA</h4>
                                <div class="row text-center" style="margin-top: 10px;">
                                    @foreach ($legendUjianMeja as $item)
                                        <div class="col-xs-6" style="margin-bottom: 10px;">
                                            <div class="js-easypie js-easypie-ujian"
                                                data-percent="{{ $item['persen'] }}"
                                                data-bar-color="{{ $item['warna'] }}"
                                                style="margin: 0 auto 8px auto;">
                                                <span style="font-size: 18px; font-weight: 700;">
                                                    {{ number_format($item['persen'], 1) }}%
                                                </span>
                                            </div>
                                            <div style="font-weight: 600;">{{ $item['label'] }}</div>
                                            <div><strong>{{ $item['value'] }}</strong> mahasiswa</div>
                                        </div>
                                    @endforeach
                                </div>
                                <div style="margin-top: 10px;">
                                    @foreach ($legendUjianMeja as $item)
                                        <div style="display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #f1f1f1;">
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:{{ $item['warna'] }};"></span>
                                                <span>{{ $item['label'] }}</span>
                                            </div>
                                            <div>
                                                <strong>{{ $item['value'] }}</strong> mahasiswa
                                                ({{ number_format($item['persen'], 1) }}%)
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="text-center" style="margin-top: 12px;">
                                    <a href="{{ url('dsn/detail_pembimbing') }}/{{ auth()->user()->name }}" class="btn btn-primary btn-sm">
                                        Lihat Detail Pembimbing
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modalKelengkapanDosen" tabindex="-1" role="dialog"
                        aria-labelledby="modalKelengkapanDosenLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" action="{{ url('/dsn/kelengkapan_profil') }}">
                                    {{ csrf_field() }}
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="modalKelengkapanDosenLabel">Lengkapi Profil Dosen</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p>Silakan lengkapi data profil dosen. Popup ini akan berhenti muncul setelah data wajib terisi lengkap.</p>
                                        @if (!empty($profilDosenKurang))
                                            <p><strong>Data yang masih kosong:</strong> {{ implode(', ', $profilDosenKurang) }}</p>
                                        @endif
                                        <div class="form-group">
                                            <label>Kode Dosen / NIDN</label>
                                            <input type="text" class="form-control" value="{{ $profilDosen->C_KODE_DOSEN ?? auth()->user()->name }}" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Dosen</label>
                                            <input type="text" class="form-control" value="{{ $profilDosen->NAMA_DOSEN ?? auth()->user()->name }}" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Program Studi</label>
                                            @php
                                                $selectedProdiDosen = old('C_KODE_PRODI', $profilDosen->C_KODE_PRODI ?? '');
                                            @endphp
                                            <select class="form-control" name="C_KODE_PRODI" required>
                                                <option value="">- Pilih Program Studi -</option>
                                                <option value="55201" {{ (string) $selectedProdiDosen === '55201' ? 'selected' : '' }}>
                                                    Teknik Informatika
                                                </option>
                                                <option value="57201" {{ (string) $selectedProdiDosen === '57201' ? 'selected' : '' }}>
                                                    Sistem Informasi
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Jenis Kelamin</label>
                                            <select class="form-control" name="JENIS_KELAMIN">
                                                <option value="">- Pilih Jenis Kelamin -</option>
                                                <option value="Pria" {{ old('JENIS_KELAMIN', $profilDosen->JENIS_KELAMIN ?? '') == 'Pria' ? 'selected' : '' }}>Pria</option>
                                                <option value="Wanita" {{ old('JENIS_KELAMIN', $profilDosen->JENIS_KELAMIN ?? '') == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>No. HP</label>
                                            <input type="text" class="form-control" name="NO_HP"
                                                value="{{ old('NO_HP', $profilDosen->NO_HP ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="EMAIL"
                                                value="{{ old('EMAIL', $profilDosen->EMAIL ?? auth()->user()->email) }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Pangkat</label>
                                            <input type="text" class="form-control" name="pangkat"
                                                value="{{ old('pangkat', $profilDosen->website ?? '') }}">
                                        </div>
                                        <div class="form-group">
                                            <label>Jabatan Fungsional</label>
                                            <select class="form-control" name="jabatan_fungsional">
                                                <option value="">- Pilih Jabatan Fungsional -</option>
                                                @foreach (['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'] as $jabatan)
                                                    <option value="{{ $jabatan }}"
                                                        {{ old('jabatan_fungsional', $profilDosen->jabatan_fungsional ?? '') == $jabatan ? 'selected' : '' }}>
                                                        {{ $jabatan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Nanti</button>
                                        <button type="submit" class="btn btn-primary">Simpan Kelengkapan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div><!-- /.container-fluid -->
    @endsection
    @section('script')
        <script>
            const renderHomeChartNoData = (elementId, text) => {
                const el = document.getElementById(elementId);
                if (!el) {
                    return;
                }

                el.innerHTML =
                    `<div style="height:100%;display:flex;align-items:center;justify-content:center;color:#777;text-align:center;padding:0 16px;">${text}</div>`;
            };

            @if (Auth::user()->level == 5)
                const homeScopeLulusanPeriode = @json($homeScopeLulusanPeriode);
                const homeScopeLulusanBidang = @json($homeScopeLulusanBidang);
                const homeStatusBimbinganProdi = @json($homeStatusBimbinganProdi);
                const homeRataLamaBimbinganProdiPerAngkatan = @json($homeRataLamaBimbinganProdiPerAngkatan);

                if (document.getElementById('home-prodi-status-bimbingan')) {
                    Morris.Bar({
                        element: 'home-prodi-status-bimbingan',
                        data: [homeStatusBimbinganProdi],
                        xkey: 'y',
                        ykeys: ['PP', 'PUM', 'L'],
                        labels: ['Persiapan Proposal', 'Persiapan Ujian TA', 'Lulusan'],
                        barColors: ['#3BAFDA', '#8CC152', '#c1b755'],
                        hideHover: 'auto',
                        resize: true
                    });
                }

                if (document.getElementById('home-prodi-lama-bimbingan-angkatan')) {
                    if (homeRataLamaBimbinganProdiPerAngkatan.length > 0) {
                        Morris.Line({
                            element: 'home-prodi-lama-bimbingan-angkatan',
                            data: homeRataLamaBimbinganProdiPerAngkatan,
                            xkey: 'y',
                            ykeys: ['ti_bulan', 'si_bulan'],
                            labels: ['TI (bulan)', 'SI (bulan)'],
                            lineColors: ['#3BAFDA', '#F6BB42'],
                            parseTime: false,
                            xLabelAngle: 35,
                            pointSize: 4,
                            hideHover: 'auto',
                            resize: true
                        });
                    } else {
                        renderHomeChartNoData(
                            'home-prodi-lama-bimbingan-angkatan',
                            'Belum ada data lama proses bimbingan per angkatan'
                        );
                    }
                }

                if (document.getElementById('home-prodi-lulusan-periode')) {
                    if (homeScopeLulusanPeriode.length > 0) {
                        Morris.Line({
                            element: 'home-prodi-lulusan-periode',
                            data: homeScopeLulusanPeriode,
                            xkey: 'y',
                            ykeys: ['total'],
                            labels: ['Jumlah Lulusan'],
                            lineColors: ['#3BAFDA'],
                            parseTime: false,
                            hideHover: 'auto',
                            resize: true
                        });
                    } else {
                        renderHomeChartNoData('home-prodi-lulusan-periode', 'Belum ada data lulusan untuk ditampilkan');
                    }
                }

                if (document.getElementById('home-prodi-lulusan-bidang')) {
                    if (homeScopeLulusanBidang.length > 0) {
                        Morris.Bar({
                            element: 'home-prodi-lulusan-bidang',
                            data: homeScopeLulusanBidang,
                            xkey: 'y',
                            ykeys: ['total'],
                            labels: ['Jumlah Lulusan'],
                            barColors: ['#F6BB42'],
                            xLabelAngle: 35,
                            hideHover: 'auto',
                            resize: true
                        });
                    } else {
                        renderHomeChartNoData('home-prodi-lulusan-bidang', 'Belum ada data bidang ilmu lulusan');
                    }
                }
            @endif

            @if (Auth::user()->level == 7)
                Morris.Bar({
                    element: 'morris-bar',
                    data: [@json($statusBimbinganDosen)],
                    xkey: 'y',
                    ykeys: ['PP', 'PUM', 'L'],
                    labels: ['Persiapan Proposal', 'Persiapan Ujian Meja', 'Lulusan'],
                    barColors: ['#3BAFDA', '#8CC152', '#c1b755']
                });

                const dosenRataLamaBimbinganPerAngkatan = @json($rataLamaBimbinganPerAngkatan ?? []);
                if (document.getElementById('morris-dosen-lama-bimbingan-angkatan')) {
                    if (dosenRataLamaBimbinganPerAngkatan.length > 0) {
                        Morris.Line({
                            element: 'morris-dosen-lama-bimbingan-angkatan',
                            data: dosenRataLamaBimbinganPerAngkatan,
                            xkey: 'y',
                            ykeys: ['ti_bulan', 'si_bulan'],
                            labels: ['TI (bulan)', 'SI (bulan)'],
                            lineColors: ['#3BAFDA', '#F6BB42'],
                            parseTime: false,
                            xLabelAngle: 35,
                            pointSize: 4,
                            hideHover: 'auto',
                            resize: true
                        });
                    } else {
                        renderHomeChartNoData(
                            'morris-dosen-lama-bimbingan-angkatan',
                            'Belum ada data lama proses bimbingan per angkatan'
                        );
                    }
                }

                $('.js-easypie').each(function() {
                    const percent = parseFloat($(this).data('percent')) || 0;
                    const barColor = $(this).data('bar-color') || '#3BAFDA';
                    $(this).easyPieChart({
                        barColor: barColor,
                        trackColor: '#eceff4',
                        scaleColor: false,
                        lineCap: 'round',
                        lineWidth: 10,
                        size: 120,
                        animate: 600
                    });
                    $(this).data('easyPieChart').update(percent);
                });

                const showPopupKelengkapanDosen = @json($showPopupKelengkapanDosen ?? false);
                const hasPopupKelengkapanErrors = @json($errors->any() || session()->has('dosen_profile_error'));
                if (showPopupKelengkapanDosen || hasPopupKelengkapanErrors) {
                    $(window).on('load', function() {
                        $('#modalKelengkapanDosen').modal('show');
                    });
                }
            @elseif (Auth::user()->level == 8)
                const showPopupKelengkapanMahasiswa = @json($showPopupKelengkapanMahasiswa ?? false);
                const hasPopupKelengkapanMahasiswaErrors = @json($errors->any() || session()->has('mhs_contact_error'));
                if (showPopupKelengkapanMahasiswa || hasPopupKelengkapanMahasiswaErrors) {
                    $(window).on('load', function() {
                        $('#modalKelengkapanMahasiswa').modal({
                            backdrop: 'static',
                            keyboard: false,
                            show: true
                        });
                        $('#modalKelengkapanMahasiswa input[name="no_wa"]').focus();
                    });
                }
            @endif
        </script>
    @endsection
