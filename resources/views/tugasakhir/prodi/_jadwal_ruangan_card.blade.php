@php
    $examClass = (int) $item->tipe_ujian === 0 ? 'exam-proposal' : ((int) $item->tipe_ujian === 2 ? 'exam-meja' : 'exam-other');
    $cardClass = $unscheduled ? 'is-unscheduled' : $examClass;
    $cardStyle = '';
    if (!$unscheduled) {
        $top = ($item->jam_mulai_menit - $timeline['start']) * $timeline['pixels_per_minute'] + 2;
        $height = max(42, $item->durasi_menit * $timeline['pixels_per_minute'] - 4);
        $cardStyle = 'top: '.$top.'px; height: '.$height.'px; left: 4px; width: calc(100% - 8px);';
    }
@endphp
<div class="schedule-card {{ $cardClass }}"
     data-event-key="{{ $item->event_key }}"
     data-schedule-id="{{ $item->jadwal_ujian_id }}"
     data-nim="{{ $item->C_NPM }}"
     data-student-name="{{ $item->NAMA_MAHASISWA }}"
     data-exam-type="{{ $item->tipe_ujian }}"
     data-exam-label="{{ $item->tipe_ujian_label }}"
     data-room-id="{{ $item->is_scheduled ? $item->ruangan : '' }}"
     data-start-minute="{{ $item->is_scheduled ? $item->jam_mulai_menit : '' }}"
     data-duration="{{ $item->durasi_menit }}"
     style="{{ $cardStyle }}"
     title="{{ $unscheduled ? 'Seret ke jadwal atau klik untuk mengatur' : 'Atur ruangan, jam, dan durasi ujian' }}">
    @if($unscheduled)
        <span class="schedule-card-nim">{{ $item->C_NPM }}</span>
        <span class="schedule-card-name">{{ $item->NAMA_MAHASISWA }}</span>
        <span class="schedule-card-meta">{{ $item->tipe_ujian_label }} | {{ $item->prodi_label }}</span>
        <span class="schedule-queue-action" aria-hidden="true"><i class="fa fa-calendar"></i></span>
    @else
        <span class="schedule-card-name">{{ $item->NAMA_MAHASISWA }}</span>
        <span class="schedule-card-meta">{{ $item->C_NPM }} | {{ $item->tipe_ujian_label }}</span>
    @endif
    <span class="schedule-card-time">{{ $item->jam_label }}</span>
</div>
