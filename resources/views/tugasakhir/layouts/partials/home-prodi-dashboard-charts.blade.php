<div class="clearfix" style="margin-top: 8px;">
    <h4 style="margin-bottom: 14px;">
        Dashboard Akademik
        @if (!empty($homeDashboardScopeLabel))
            <small style="font-size: 13px; margin-left: 6px;">{{ $homeDashboardScopeLabel }}</small>
        @endif
    </h4>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="the-box" style="min-height: 490px;">
            <h4 class="small-title">PERSEBARAN JENIS TUGAS AKHIR PER ANGKATAN</h4>
            <p class="text-muted" style="min-height: 38px;">Jumlah lulusan setiap jenis tugas akhir berdasarkan angkatan mahasiswa.</p>
            <div class="home-jenis-ta-legend" style="min-height: 54px;">
                @foreach (($jenisTugasAkhirTrendCharts['series'] ?? []) as $series)
                    <span style="display: inline-block; margin: 0 10px 7px 0; white-space: nowrap;">
                        <span style="background: {{ $series['color'] }}; display: inline-block; height: 8px; margin-right: 4px; width: 18px;"></span>
                        <strong>{{ $series['code'] }}</strong>
                    </span>
                @endforeach
            </div>
            <div id="home-prodi-jenis-ta-angkatan" style="height: 330px;"></div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="the-box" style="min-height: 490px;">
            <h4 class="small-title">PERSEBARAN JENIS TUGAS AKHIR PER TAHUN AJARAN</h4>
            <p class="text-muted" style="min-height: 38px;">Jumlah lulusan setiap jenis tugas akhir berdasarkan tahun ajaran kelulusan.</p>
            <div class="home-jenis-ta-legend" style="min-height: 54px;">
                @foreach (($jenisTugasAkhirTrendCharts['series'] ?? []) as $series)
                    <span style="display: inline-block; margin: 0 10px 7px 0; white-space: nowrap;">
                        <span style="background: {{ $series['color'] }}; display: inline-block; height: 8px; margin-right: 4px; width: 18px;"></span>
                        <strong>{{ $series['code'] }}</strong>
                    </span>
                @endforeach
            </div>
            <div id="home-prodi-jenis-ta-tahun-ajaran" style="height: 330px;"></div>
        </div>
    </div>
</div>

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
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 12px; height: 12px; border-radius: 2px; background: #3BAFDA;"></span>
                    <strong>TI (bulan)</strong>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 12px; height: 12px; border-radius: 2px; background: #F6BB42;"></span>
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
