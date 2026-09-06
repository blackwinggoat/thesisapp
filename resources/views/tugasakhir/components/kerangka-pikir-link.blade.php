@php
    $kerangkaPikirUrl = helper::getKerangkaPikirUrl($kerangka ?? null);
@endphp
@if ($kerangkaPikirUrl)
    <a href="{{ $kerangkaPikirUrl }}" target="_blank" rel="noopener noreferrer"
        class="btn btn-primary" title="Buka Kerangka Pikir">
        <i class="fa fa-external-link" aria-hidden="true"></i>
        <span class="sr-only">Buka Kerangka Pikir</span>
    </a>
@else
    <span class="label label-danger">Belum Ada</span>
@endif
