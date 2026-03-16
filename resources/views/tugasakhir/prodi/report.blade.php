@extends('tugasakhir.index')
@section('isi')
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Report Prodi <small>{{ $reportContext['label'] }}</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Report</li>
        </ol>

        <div class="alert alert-info square fade in">
            Dashboard report ini otomatis memfilter data untuk <strong>{{ $reportContext['label'] }}</strong> berdasarkan akun prodi yang sedang login.
        </div>

        <div class="row">
            @foreach ($summaryCards as $card)
                <div class="col-sm-3">
                    <div class="the-box no-border bg-{{ $card['class'] }} tiles-information">
                        <i class="fa {{ $card['icon'] }} icon-bg"></i>
                        <div class="tiles-inner text-center">
                            <p>{{ $card['label'] }}</p>
                            <h1 class="bolded">{{ $card['value'] }}</h1>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            @foreach ($queueCards as $card)
                <div class="col-sm-3">
                    <div class="the-box text-center" style="min-height: 130px;">
                        <div style="font-size: 28px; margin-bottom: 10px; color: #3bafda;">
                            <i class="fa {{ $card['icon'] }}"></i>
                        </div>
                        <div class="text-muted" style="font-size: 13px;">{{ $card['label'] }}</div>
                        <h2 style="margin-top: 10px; margin-bottom: 0;">{{ $card['value'] }}</h2>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="the-box">
                    <h4 class="small-title">Komposisi Tahapan Bimbingan</h4>
                    <div id="report-status-bimbingan" style="height: 320px;"></div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="the-box">
                    <h4 class="small-title">Status Usulan Topik</h4>
                    <div id="report-status-topik" style="height: 320px;"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="the-box">
                    <h4 class="small-title">Top 10 Beban Dosen Pembimbing</h4>
                    <p class="text-muted">Perbandingan mahasiswa aktif dibimbing dan mahasiswa yang sudah lulus.</p>
                    <div id="report-dosen-pembimbing" style="height: 360px;"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="the-box">
                    <h4 class="small-title">Peserta Ujian per Periode</h4>
                    <p class="text-muted">Proposal vs Ujian Meja berdasarkan periode pendaftaran.</p>
                    <div id="report-periode-peserta" style="height: 320px;"></div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="the-box">
                    <h4 class="small-title">Top 10 Bidang Ilmu Topik Disetujui</h4>
                    <p class="text-muted">Berdasarkan topik yang sudah diterima prodi.</p>
                    <div id="report-bidang-ilmu" style="height: 320px;"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="the-box">
                    <h4 class="small-title">Rerata Nilai per Komponen</h4>
                    <p class="text-muted">Rerata ini dihitung dari seluruh lembar penilaian dosen pada Proposal dan Ujian Meja.</p>
                    <div id="report-nilai-komponen" style="height: 340px;"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="the-box">
                    <h4 class="small-title">Status Verifikasi Berkas Proposal</h4>
                    <div id="report-dokumen-proposal" style="height: 300px;"></div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="the-box">
                    <h4 class="small-title">Status Verifikasi Berkas Ujian Meja</h4>
                    <div id="report-dokumen-ujianmeja" style="height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="the-box">
                    <h4 class="small-title">Status SK Pembimbing</h4>
                    <div id="report-sk-pembimbing" style="height: 300px;"></div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="the-box">
                    <h4 class="small-title">Status SK Penugasan Ujian TA</h4>
                    <div id="report-sk-penugasan" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const statusBimbinganChart = {!! json_encode($statusBimbinganChart) !!};
    const topikStatusChart = {!! json_encode($topikStatusChart) !!};
    const dosenPembimbingChart = {!! json_encode($dosenPembimbingChart) !!};
    const periodePesertaChart = {!! json_encode($periodePesertaChart) !!};
    const bidangIlmuChart = {!! json_encode($bidangIlmuChart) !!};
    const nilaiKomponenChart = {!! json_encode($nilaiKomponenChart) !!};
    const dokumenProposalChart = {!! json_encode($dokumenProposalChart) !!};
    const dokumenUjianMejaChart = {!! json_encode($dokumenUjianMejaChart) !!};
    const skPembimbingChart = {!! json_encode($skPembimbingChart) !!};
    const skPenugasanChart = {!! json_encode($skPenugasanChart) !!};

    const hasDonutData = data => Array.isArray(data) && data.some(item => Number(item.value || 0) > 0);
    const hasSeriesData = (data, keys) => Array.isArray(data) && data.some(item => keys.some(key => Number(item[key] || 0) > 0));

    const renderNoData = (id, message) => {
        document.getElementById(id).innerHTML = '<div class="text-center text-muted" style="padding-top: 120px;">' + message + '</div>';
    };

    const renderDonut = (id, data, colors) => {
        if (!hasDonutData(data)) {
            renderNoData(id, 'Belum ada data untuk ditampilkan');
            return;
        }

        Morris.Donut({
            element: id,
            data: data,
            colors: colors,
            resize: true,
            formatter: function(y) {
                return y;
            }
        });
    };

    if (hasSeriesData(dosenPembimbingChart, ['aktif', 'lulus'])) {
        Morris.Bar({
            element: 'report-dosen-pembimbing',
            data: dosenPembimbingChart,
            xkey: 'y',
            ykeys: ['aktif', 'lulus'],
            labels: ['Aktif', 'Lulus'],
            barColors: ['#3BAFDA', '#8CC152'],
            xLabelAngle: 35,
            hideHover: 'auto',
            resize: true
        });
    } else {
        renderNoData('report-dosen-pembimbing', 'Belum ada data dosen pembimbing');
    }

    if (hasSeriesData(periodePesertaChart, ['proposal', 'ujian_meja'])) {
        Morris.Line({
            element: 'report-periode-peserta',
            data: periodePesertaChart,
            xkey: 'y',
            ykeys: ['proposal', 'ujian_meja'],
            labels: ['Proposal', 'Ujian Meja'],
            lineColors: ['#3BAFDA', '#ED5565'],
            parseTime: false,
            hideHover: 'auto',
            resize: true
        });
    } else {
        renderNoData('report-periode-peserta', 'Belum ada data periode ujian');
    }

    if (hasSeriesData(bidangIlmuChart, ['total'])) {
        Morris.Bar({
            element: 'report-bidang-ilmu',
            data: bidangIlmuChart,
            xkey: 'y',
            ykeys: ['total'],
            labels: ['Total'],
            barColors: ['#F6BB42'],
            xLabelAngle: 35,
            hideHover: 'auto',
            resize: true
        });
    } else {
        renderNoData('report-bidang-ilmu', 'Belum ada data bidang ilmu yang disetujui');
    }

    if (hasSeriesData(nilaiKomponenChart, ['proposal', 'ujian_meja'])) {
        Morris.Area({
            element: 'report-nilai-komponen',
            data: nilaiKomponenChart,
            xkey: 'y',
            ykeys: ['proposal', 'ujian_meja'],
            labels: ['Proposal', 'Ujian Meja'],
            lineColors: ['#4FC1E9', '#EC87C0'],
            pointFillColors: ['#4FC1E9', '#EC87C0'],
            behaveLikeLine: true,
            parseTime: false,
            fillOpacity: 0.35,
            hideHover: 'auto',
            resize: true
        });
    } else {
        renderNoData('report-nilai-komponen', 'Belum ada data nilai untuk ditampilkan');
    }

    renderDonut('report-status-bimbingan', statusBimbinganChart, ['#8CC152', '#3BAFDA', '#ED5565', '#F6BB42']);
    renderDonut('report-status-topik', topikStatusChart, ['#F6BB42', '#8CC152', '#ED5565']);
    renderDonut('report-dokumen-proposal', dokumenProposalChart, ['#8CC152', '#ED5565', '#F6BB42']);
    renderDonut('report-dokumen-ujianmeja', dokumenUjianMejaChart, ['#8CC152', '#ED5565', '#F6BB42']);
    renderDonut('report-sk-pembimbing', skPembimbingChart, ['#F6BB42', '#4FC1E9', '#8CC152']);
    renderDonut('report-sk-penugasan', skPenugasanChart, ['#F6BB42', '#4FC1E9', '#8CC152']);
</script>
@endsection
