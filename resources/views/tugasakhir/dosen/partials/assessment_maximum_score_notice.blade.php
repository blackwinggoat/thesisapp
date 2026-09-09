@php
    $nilaiMaksimalLabel = rtrim(rtrim(number_format((float) $batasNilai->nilai_maksimal, 2, '.', ''), '0'), '.');
@endphp
<div class="form-group assessment-maximum-score">
    <label class="col-lg-4 control-label">Batas Nilai</label>
    <div class="col-lg-8">
        <div class="alert alert-info" style="margin-bottom: 8px; padding: 10px 12px;">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            Nilai maksimal <strong>{{ $batasNilai->kode_jenis_tugas_akhir }}</strong> adalah
            <strong>{{ $nilaiMaksimalLabel }}</strong>.
        </div>
        <div id="assessment-score-limit-warning" class="alert alert-danger" role="alert"
            aria-live="assertive" style="display: none; margin-bottom: 8px; padding: 10px 12px;"></div>
    </div>
</div>
<div class="clearfix"></div>
