<style>
    .assessment-card-grid { margin: 0 -8px; }
    .assessment-card-column { padding: 8px; }
    .assessment-card { background: #fff; border: 1px solid #d9e0e7; border-radius: 6px; box-shadow: 0 1px 2px rgba(20, 35, 50, .08); min-height: 100%; overflow: hidden; }
    .assessment-card__header { background: #186a8c; color: #fff; display: flex; font-size: 12px; font-weight: 600; gap: 8px; justify-content: space-between; line-height: 1.35; min-height: 42px; padding: 12px 14px; }
    .assessment-card__role { min-width: 0; }
    .assessment-card__status { border-radius: 12px; flex: 0 0 auto; font-size: 11px; font-weight: 700; padding: 3px 7px; white-space: nowrap; }
    .assessment-card__status--pending { background: #f5c542; color: #3e3512; }
    .assessment-card__status--incomplete { background: #e67e22; color: #fff; }
    .assessment-card__status--complete { background: #2d9b61; color: #fff; }
    .assessment-card__body { padding: 16px; }
    .assessment-card__identity { align-items: center; display: flex; margin-bottom: 14px; }
    .assessment-card__photo { background: #f1f4f6; border: 1px solid #d9e0e7; border-radius: 50%; flex: 0 0 auto; height: 64px; margin-right: 12px; object-fit: cover; width: 64px; }
    .assessment-card__name { color: #253746; font-size: 16px; font-weight: 700; line-height: 1.3; margin: 0; }
    .assessment-card__nim { color: #607382; font-size: 13px; margin: 4px 0 0; }
    .assessment-card__label { color: #607382; display: block; font-size: 11px; font-weight: 700; margin-bottom: 4px; text-transform: uppercase; }
    .assessment-card__title { color: #34495e; font-size: 13px; line-height: 1.5; margin: 0 0 14px; min-height: 58px; }
    .assessment-card__schedule { border-top: 1px solid #e7ecf0; color: #34495e; font-size: 13px; padding-top: 12px; }
    .assessment-card__actions { border-top: 1px solid #e7ecf0; display: flex; gap: 8px; margin-top: 14px; padding-top: 14px; }
    .assessment-card__actions .btn { flex: 1; }
    .assessment-card-search { margin: 0 0 14px; max-width: 380px; position: relative; }
    .assessment-card-search .form-control { padding-left: 34px; }
    .assessment-card-search .fa-search { color: #8293a0; left: 12px; position: absolute; top: 10px; }
    .assessment-card-empty { background: #fff; border: 1px dashed #b7c4ce; border-radius: 6px; color: #607382; padding: 36px 20px; text-align: center; }
    .assessment-card-no-result { display: none; margin: 8px; }
    .assessment-detail-list { list-style: none; margin: 0; padding: 0; }
    .assessment-detail-list li { border-bottom: 1px solid #edf0f2; padding: 10px 0; }
    .assessment-detail-list li:last-child { border-bottom: 0; }
    .assessment-detail-list__role { color: #607382; display: block; font-size: 12px; font-weight: 700; }
    .assessment-detail-list__name { color: #34495e; display: block; margin-top: 2px; }
    .assessment-detail-list__status { margin-left: 5px; }
    .assessment-detail-list__status--complete { color: #2d9b61; }
    .assessment-detail-list__status--incomplete { color: #e67e22; }
    .assessment-detail-list__status--pending { color: #d64545; }
    .assessment-card-date { border-bottom: 2px solid #186a8c; color: #253746; font-size: 17px; font-weight: 700; margin: 24px 8px 4px; padding-bottom: 7px; }
    .assessment-card-date:first-child { margin-top: 0; }
    @media (max-width: 767px) {
        .assessment-card__actions { flex-direction: column; }
        .assessment-card__actions .btn { width: 100%; }
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Sistem Informasi Program Studi <small>TUGAS AKHIR</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">{{ $assessmentTitle }}</li>
        </ol>

        <div class="clearfix" style="margin-bottom: 12px;">
            <h3 class="page-heading pull-left" style="margin: 0;">{{ $assessmentTitle }}</h3>
            <div class="pull-right">
                <a href="{{ url($recapPath) }}" class="btn btn-success btn-sm"><i class="fa fa-table"></i> Rekap</a>
                <a href="{{ url($historyPath) }}" class="btn btn-primary btn-sm"><i class="fa fa-history"></i> History</a>
            </div>
        </div>

        <div class="assessment-card-search">
            <i class="fa fa-search"></i>
            <input type="search" class="form-control assessment-card-search-input" placeholder="Cari nama atau NIM mahasiswa" aria-label="Cari nama atau NIM mahasiswa">
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($data->isEmpty())
            <div class="assessment-card-empty"><i class="fa fa-calendar-o fa-2x" style="display: block; margin-bottom: 10px;"></i>{{ $emptyMessage }}</div>
        @else
            <div class="alert alert-info assessment-card-no-result">Tidak ada mahasiswa yang sesuai dengan pencarian.</div>
            @php
                $assessmentGroups = $data->groupBy(function ($item) {
                    return $item->tanggal_ujian ?: 'tanpa-jadwal';
                });
            @endphp
            @foreach($assessmentGroups as $tanggal => $assessmentGroup)
                <div class="assessment-card-date">
                    {{ $tanggal === 'tanpa-jadwal' ? 'Jadwal Ujian Belum Tersedia' : 'Jadwal Ujian: ' . helper::tgl_indo_lengkap($tanggal) }}
                </div>
                <div class="row assessment-card-grid">
                @foreach($assessmentGroup as $d)
                    <div class="col-xs-12 col-sm-6 col-lg-4 assessment-card-column" data-search="{{ $d->NAMA_MAHASISWA }} {{ $d->C_NPM }}">
                        <article class="assessment-card">
                            <div class="assessment-card__header">
                                <span class="assessment-card__role"><i class="fa fa-user-md"></i> {{ implode(' &bull; ', $d->peran_login) ?: 'Tim Ujian' }}</span>
                                <span class="assessment-card__status assessment-card__status--{{ $d->status_penilaian_class }}"><i class="fa {{ $d->status_penilaian_icon }}"></i> {{ $d->status_penilaian }}</span>
                            </div>
                            <div class="assessment-card__body">
                                <div class="assessment-card__identity">
                                    <img class="assessment-card__photo" src="{{ $d->foto_url }}" alt="Foto {{ $d->NAMA_MAHASISWA }}">
                                    <div><h4 class="assessment-card__name">{{ $d->NAMA_MAHASISWA }}</h4><p class="assessment-card__nim">NIM {{ $d->C_NPM }}</p></div>
                                </div>
                                <span class="assessment-card__label">Judul Tugas Akhir</span>
                                <p class="assessment-card__title">{!! helper::jenisTugasAkhirBadge($d->jenis_tugas_akhir_id ?? null) !!} {{ $d->judul ?: '-' }}</p>
                                <div class="assessment-card__schedule"><i class="fa fa-calendar"></i> <strong>Jadwal Ujian:</strong> {{ $d->jadwal_ujian_label }}</div>
                                <div class="assessment-card__actions">
                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#assessment-detail-{{ $d->reg_id }}"><i class="fa fa-info-circle"></i> Detail</button>
                                    @if($d->boleh_menilai)
                                        <a href="{{ url($detailPath . '/' . $d->reg_id) }}" class="btn btn-info"><i class="fa fa-file-text"></i> Penilaian</a>
                                    @else
                                        <button type="button" class="btn btn-default" disabled title="Tim penguji belum ditetapkan"><i class="fa fa-lock"></i> Penilaian</button>
                                    @endif
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="modal fade" id="assessment-detail-{{ $d->reg_id }}" tabindex="-1" role="dialog" aria-labelledby="assessment-detail-label-{{ $d->reg_id }}">
                        <div class="modal-dialog" role="document"><div class="modal-content">
                            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button><h4 class="modal-title" id="assessment-detail-label-{{ $d->reg_id }}">Detail {{ $examLabel }}</h4></div>
                            <div class="modal-body">
                                <p><strong>{{ $d->NAMA_MAHASISWA }}</strong><br>NIM {{ $d->C_NPM }}</p>
                                <p><strong>Judul Tugas Akhir</strong><br>{!! helper::jenisTugasAkhirBadge($d->jenis_tugas_akhir_id ?? null) !!} {{ $d->judul ?: '-' }}</p>
                                <p><strong>Jadwal Ujian</strong><br>{{ $d->jadwal_ujian_label }}</p>
                                <p><strong>Status Penilaian Anda</strong><br><i class="fa {{ $d->status_penilaian_icon }}"></i> {{ $d->status_penilaian }}</p>
                                <hr><strong>Tim Ujian dan Pembimbing</strong>
                                <ul class="assessment-detail-list" style="margin-top: 8px;">
                                    @foreach($d->tim_ujian as $tim)
                                        <li>
                                            <span class="assessment-detail-list__role">{{ $tim['peran'] }}</span>
                                            <span class="assessment-detail-list__name">
                                                {{ $tim['nama'] }} <small>({{ $tim['kode'] }})</small>
                                                @php($statusPenilaian = $d->penilaian_status_by_dosen[$tim['kode']] ?? 'pending')
                                                @php($statusIcon = ['complete' => 'fa-check-circle', 'incomplete' => 'fa-exclamation-circle', 'pending' => 'fa-times-circle'][$statusPenilaian])
                                                @php($statusLabel = ['complete' => 'Sudah menilai lengkap', 'incomplete' => 'Penilaian belum lengkap', 'pending' => 'Belum menilai'][$statusPenilaian])
                                                <i class="fa {{ $statusIcon }} assessment-detail-list__status assessment-detail-list__status--{{ $statusPenilaian }}" title="{{ $statusLabel }}" aria-label="{{ $statusLabel }}"></i>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>@if($d->boleh_menilai)<a href="{{ url($detailPath . '/' . $d->reg_id) }}" class="btn btn-info"><i class="fa fa-file-text"></i> Penilaian</a>@endif</div>
                        </div></div>
                    </div>
                @endforeach
                </div>
            @endforeach
        @endif
    </div>
</div>
