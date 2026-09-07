@if(empty($studentProgramLabel))
<div class="alert alert-danger">
    Program studi akun mahasiswa belum dikenali. Silakan hubungi Program Studi.
</div>
@elseif(session('registration_status') == 'invalid_period')
<div class="alert alert-danger">
    Periode pendaftaran tidak tersedia untuk program studi Anda.
</div>
@elseif(session('registration_status') == 'program_unmapped')
<div class="alert alert-danger">
    Program studi akun mahasiswa belum dikenali. Silakan hubungi Program Studi.
</div>
@elseif(session('registration_status') == 'registration_error')
<div class="alert alert-danger">
    Pendaftaran belum berhasil diproses. Silakan coba kembali.
</div>
@elseif(session('registration_status') == 'cancel_success')
<div class="alert alert-success alert-block square fade in alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    Pendaftaran berhasil dibatalkan. Anda dapat memilih periode lain yang tersedia.
</div>
@elseif(session('registration_status') == 'cancel_scheduled')
<div class="alert alert-warning alert-block square fade in alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    Pendaftaran tidak dapat dibatalkan karena jadwal ujian sudah ditetapkan.
</div>
@elseif(session('registration_status') == 'cancel_not_found')
<div class="alert alert-warning alert-block square fade in alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    Data pendaftaran tidak ditemukan atau tidak sesuai dengan program studi Anda.
</div>
@elseif(session('registration_status') == 'cancel_error')
<div class="alert alert-danger alert-block square fade in alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    Pendaftaran gagal dibatalkan. Silakan coba kembali.
</div>
@endif

@if(!empty($currentRegistration))
<div class="alert alert-info" style="overflow: hidden;">
    <div class="row">
        <div class="col-sm-8">
            <strong><i class="fa fa-calendar-check-o"></i> Terdaftar pada {{$examLabel}}</strong><br>
            <span>{{$currentRegistration->nama_periode}}</span>
            <span class="text-muted">
                ({{empty($currentRegistration->tgl_start) ? '-' : date('d/m/Y', strtotime($currentRegistration->tgl_start))}} -
                {{empty($currentRegistration->tgl_end) ? '-' : date('d/m/Y', strtotime($currentRegistration->tgl_end))}})
            </span>
        </div>
        <div class="col-sm-4 text-right">
            @if($currentRegistrationScheduled)
            <span class="label label-success"><i class="fa fa-lock"></i> Jadwal sudah ditetapkan</span>
            <p class="small" style="margin: 6px 0 0;">Periode tidak dapat diubah.</p>
            @else
            <form method="post" action="{{url('mhs/registrasi/batalkan')}}"
                onsubmit="return confirm('Keluar dari periode ini dan memilih periode lain?');">
                {{csrf_field()}}
                <input type="hidden" name="tipe_ujian" value="{{$examType}}">
                <input type="hidden" name="pendaftaran_id" value="{{$currentRegistration->pendaftaran_id}}">
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fa fa-sign-out"></i> Keluar dari Periode
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endif
