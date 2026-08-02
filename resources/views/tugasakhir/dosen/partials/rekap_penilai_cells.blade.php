@php
    $namaDosen = function ($kodeDosen) use ($dosenByKode) {
        $dosen = $dosenByKode->get($kodeDosen);

        return $dosen ? $dosen->NAMA_DOSEN : '--';
    };
    $statusPenilaian = function ($kodeDosen) use ($d, $penilaianLengkap) {
        return $penilaianLengkap->has($d->reg_id . ':' . $kodeDosen)
            ? '<i class="fa fa-check-circle text-success"></i>'
            : '<i class="fa fa-times-circle text-danger"></i>';
    };
@endphp
<td>{{ $namaDosen($d->pembimbing_I_id) }} {!! $statusPenilaian($d->pembimbing_I_id) !!}</td>
<td>{{ $namaDosen($d->pembimbing_II_id) }} {!! $statusPenilaian($d->pembimbing_II_id) !!}</td>
@foreach (['penguji_I_id', 'penguji_II_id', 'penguji_III_id', 'ketua_sidang_id'] as $kolomDosen)
    <td>
        @if (empty($d->{$kolomDosen}))
            -
        @else
            {{ $namaDosen($d->{$kolomDosen}) }} {!! $statusPenilaian($d->{$kolomDosen}) !!}
        @endif
    </td>
@endforeach
