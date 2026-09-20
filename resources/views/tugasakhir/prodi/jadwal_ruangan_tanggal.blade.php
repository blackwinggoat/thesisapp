@extends('tugasakhir.index')

@section('isi')
@php
    $boardHeight = (int) round($timeline['duration'] * $timeline['pixels_per_minute']);
    $roomCount = max(1, $ruangan->count());
    $unscheduledCount = $peserta->count() - $scheduledCount;
    $roomPalette = [
        ['header' => '#366f8a', 'track' => '#f2f8fb', 'line' => '#d8e7ee', 'border' => '#a4c7d7'],
        ['header' => '#4e7658', 'track' => '#f3f9f4', 'line' => '#dbe9dd', 'border' => '#aecdb4'],
        ['header' => '#956927', 'track' => '#fdf8ee', 'line' => '#eee0c6', 'border' => '#dbc18f'],
        ['header' => '#36756f', 'track' => '#f1f9f8', 'line' => '#d6e9e7', 'border' => '#a7cdc9'],
        ['header' => '#8a5360', 'track' => '#fbf4f6', 'line' => '#eadbe0', 'border' => '#d5aeb8'],
        ['header' => '#586d89', 'track' => '#f4f6fa', 'line' => '#dce2ea', 'border' => '#b7c2d1'],
        ['header' => '#725f84', 'track' => '#f8f5fa', 'line' => '#e4ddea', 'border' => '#c8b8d4'],
        ['header' => '#687347', 'track' => '#f7f9f1', 'line' => '#e2e7d3', 'border' => '#c4cea6'],
    ];
@endphp

<style>
    .schedule-toolbar {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .schedule-toolbar-main {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .schedule-date {
        color: #243447;
        font-size: 18px;
        font-weight: 700;
    }
    .schedule-stat {
        align-items: center;
        background: #f4f7fa;
        border: 1px solid #dfe6ed;
        border-radius: 4px;
        color: #435466;
        display: inline-flex;
        font-size: 12px;
        font-weight: 600;
        gap: 6px;
        min-height: 30px;
        padding: 5px 9px;
    }
    .schedule-unscheduled {
        background: #f8fafc;
        border: 1px solid #dfe6ed;
        border-radius: 4px;
        margin-bottom: 16px;
        padding: 12px;
        transition: border-color .15s ease, background .15s ease;
    }
    .schedule-unscheduled.is-drop-target {
        background: #fff8e6;
        border-color: #e4a11b;
    }
    .schedule-section-title {
        align-items: center;
        color: #263746;
        display: flex;
        font-size: 14px;
        font-weight: 700;
        justify-content: space-between;
        margin: 0 0 10px;
    }
    .schedule-pool {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        min-height: 54px;
    }
    .schedule-pool-empty {
        color: #7b8794;
        font-size: 12px;
        padding: 12px 4px;
    }
    .schedule-board-shell {
        border: 1px solid #d7dee5;
        border-radius: 4px;
        max-height: calc(100vh - 220px);
        min-height: 520px;
        overflow: auto;
        position: relative;
    }
    .schedule-grid {
        display: grid;
        grid-template-columns: 72px repeat({{ $roomCount }}, minmax(220px, 1fr));
        grid-template-rows: 58px {{ $boardHeight }}px;
        min-width: {{ 72 + ($roomCount * 220) }}px;
        position: relative;
    }
    .schedule-corner,
    .schedule-room-header {
        align-items: center;
        border-right: 1px solid rgba(255, 255, 255, .18);
        color: #fff;
        display: flex;
        font-size: 12px;
        font-weight: 700;
        justify-content: center;
        padding: 8px;
        position: sticky;
        text-align: center;
        top: 0;
        z-index: 8;
    }
    .schedule-corner {
        background: #263746;
        grid-column: 1;
        grid-row: 1;
        left: 0;
        z-index: 10;
    }
    .schedule-room-header {
        background: var(--room-header, #34495e);
    }
    .schedule-room-count {
        background: rgba(255, 255, 255, .18);
        border-radius: 10px;
        font-size: 10px;
        margin-left: 6px;
        min-width: 20px;
        padding: 2px 6px;
    }
    .schedule-time-axis {
        background: #f7f9fb;
        border-right: 1px solid #cad3dc;
        grid-column: 1;
        grid-row: 2;
        left: 0;
        position: sticky;
        z-index: 6;
    }
    .schedule-time-label {
        color: #52616f;
        font-size: 11px;
        font-weight: 600;
        left: 0;
        padding-right: 8px;
        position: absolute;
        text-align: right;
        transform: translateY(-7px);
        width: 100%;
    }
    .schedule-room-track {
        background-color: var(--room-track, #fff);
        background-image: repeating-linear-gradient(
            to bottom,
            transparent 0,
            transparent {{ (30 * $timeline['pixels_per_minute']) - 1 }}px,
            var(--room-line, #e8edf2) {{ (30 * $timeline['pixels_per_minute']) - 1 }}px,
            var(--room-line, #e8edf2) {{ 30 * $timeline['pixels_per_minute'] }}px
        );
        border-right: 2px solid var(--room-border, #dfe5eb);
        grid-row: 2;
        min-width: 0;
        position: relative;
    }
    .schedule-room-track.is-drop-target {
        background-color: #eef8ff;
        outline: 2px solid #2f86c7;
        outline-offset: -2px;
    }
    .schedule-card {
        background: #fff;
        border: 1px solid #9eacb8;
        border-left: 4px solid #607d8b;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(30, 45, 60, .16);
        color: #24313d;
        cursor: grab;
        font-size: 11px;
        line-height: 1.25;
        min-height: 42px;
        overflow: hidden;
        padding: 15px 7px 16px;
        position: absolute;
        transition: box-shadow .15s ease, opacity .15s ease;
        z-index: 3;
    }
    .schedule-card:hover,
    .schedule-card:focus {
        box-shadow: 0 3px 9px rgba(30, 45, 60, .24);
        outline: none;
        z-index: 5;
    }
    .schedule-card.is-saving {
        opacity: .55;
        pointer-events: none;
    }
    .schedule-card.is-dragging {
        opacity: .3;
    }
    .schedule-card.exam-proposal { border-left-color: #2980b9; }
    .schedule-card.exam-meja { border-left-color: #c0392b; }
    .schedule-card.exam-other { border-left-color: #7f8c8d; }
    .schedule-card.is-unscheduled {
        cursor: grab;
        min-height: 64px;
        padding: 6px 7px;
        position: relative;
        width: 220px;
    }
    .schedule-card-name {
        display: block;
        font-size: 11px;
        font-weight: 700;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .schedule-card-meta,
    .schedule-card-time {
        color: #5f6d79;
        display: block;
        font-size: 10px;
        margin-top: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .schedule-card-time {
        color: #263746;
        font-weight: 700;
    }
    .schedule-duration-handle {
        align-items: center;
        background: #34495e;
        color: #fff;
        cursor: ns-resize;
        display: flex;
        height: 11px;
        justify-content: center;
        left: 0;
        opacity: .86;
        position: absolute;
        touch-action: none;
        transition: background .15s ease, opacity .15s ease;
        user-select: none;
        width: 100%;
        z-index: 7;
    }
    .schedule-duration-handle.is-start {
        border-radius: 0 3px 0 0;
        top: 0;
    }
    .schedule-duration-handle.is-end {
        border-radius: 0 0 3px 0;
        bottom: 0;
    }
    .schedule-duration-handle:hover,
    .schedule-duration-handle:focus {
        background: #1f7a8c;
        opacity: 1;
        outline: none;
    }
    .schedule-duration-handle .fa {
        font-size: 8px;
        pointer-events: none;
    }
    .schedule-card.is-resizing {
        box-shadow: 0 4px 12px rgba(30, 45, 60, .28);
        cursor: ns-resize;
        z-index: 9;
    }
    .schedule-drop-preview {
        align-items: center;
        background: rgba(31, 122, 140, .16);
        border: 2px dashed #1f7a8c;
        border-radius: 4px;
        color: #16535f;
        display: flex;
        font-size: 10px;
        font-weight: 700;
        justify-content: center;
        left: 4px;
        min-height: 32px;
        padding: 4px;
        pointer-events: none;
        position: absolute;
        text-align: center;
        width: calc(100% - 8px);
        z-index: 8;
    }
    .schedule-time-preview {
        background: #263746;
        border: 1px solid rgba(255, 255, 255, .22);
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(18, 32, 44, .28);
        color: #fff;
        display: none;
        font-size: 11px;
        font-weight: 700;
        left: 0;
        line-height: 1.35;
        max-width: 230px;
        padding: 6px 9px;
        pointer-events: none;
        position: fixed;
        top: 0;
        white-space: nowrap;
        z-index: 1200;
    }
    .schedule-legend {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 0 0 10px;
    }
    .schedule-legend-item {
        align-items: center;
        color: #596875;
        display: inline-flex;
        font-size: 11px;
        gap: 5px;
    }
    .schedule-legend-mark {
        border-radius: 2px;
        display: inline-block;
        height: 12px;
        width: 4px;
    }
    .schedule-legend-mark.proposal { background: #2980b9; }
    .schedule-legend-mark.meja { background: #c0392b; }
    .schedule-legend-mark.other { background: #7f8c8d; }
    .schedule-no-room {
        background: #fff4e5;
        border: 1px solid #f1c27d;
        color: #805800;
        margin-bottom: 16px;
        padding: 12px;
    }
    .schedule-modal-student {
        background: #f6f8fa;
        border-left: 3px solid #34495e;
        margin-bottom: 15px;
        padding: 10px 12px;
    }
    @media (max-width: 767px) {
        .schedule-toolbar {
            align-items: stretch;
            flex-direction: column;
        }
        .schedule-toolbar-main {
            align-items: stretch;
        }
        .schedule-date {
            flex-basis: 100%;
        }
        .schedule-stat {
            flex: 1 1 auto;
            justify-content: center;
        }
        .schedule-card.is-unscheduled {
            width: 100%;
        }
        .schedule-board-shell {
            max-height: 68vh;
            min-height: 430px;
        }
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/prodi/jadwal') }}">Jadwal Ujian</a></li>
            <li class="active">Ruangan dan Jam</li>
        </ol>

        <div class="schedule-toolbar">
            <div class="schedule-toolbar-main">
                <span class="schedule-date"><i class="fa fa-calendar"></i> {{ $summary->tanggal_label }}</span>
                <span class="schedule-stat"><i class="fa fa-users"></i> <strong id="totalParticipantCount">{{ $peserta->count() }}</strong> peserta</span>
                <span class="schedule-stat"><i class="fa fa-check-circle"></i> <strong id="scheduledParticipantCount">{{ $scheduledCount }}</strong> terjadwal</span>
                <span class="schedule-stat"><i class="fa fa-clock-o"></i> <strong id="unscheduledParticipantCount">{{ $unscheduledCount }}</strong> belum dijadwalkan</span>
            </div>
            <a href="{{ url('/prodi/jadwal') }}" class="btn btn-default" title="Kembali ke jadwal ujian">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="schedule-unscheduled" id="unscheduledDropzone">
            <h3 class="schedule-section-title">
                <span><i class="fa fa-inbox"></i> Belum Dijadwalkan</span>
                <span class="badge" id="unscheduledBadge">{{ $unscheduledCount }}</span>
            </h3>
            <div class="schedule-pool" id="unscheduledPool">
                @foreach($peserta->filter(function ($item) { return !$item->is_scheduled; }) as $item)
                    @include('tugasakhir.prodi._jadwal_ruangan_card', ['item' => $item, 'unscheduled' => true])
                @endforeach
                <div class="schedule-pool-empty" id="unscheduledEmpty" style="{{ $unscheduledCount > 0 ? 'display:none;' : '' }}">Semua peserta telah dijadwalkan.</div>
            </div>
        </div>

        <div class="schedule-legend" aria-label="Legenda tipe ujian">
            <span class="schedule-legend-item"><span class="schedule-legend-mark proposal"></span> Proposal</span>
            <span class="schedule-legend-item"><span class="schedule-legend-mark meja"></span> Ujian Meja</span>
            <span class="schedule-legend-item"><span class="schedule-legend-mark other"></span> Tipe lain</span>
        </div>

        @if($ruangan->isEmpty())
            <div class="schedule-no-room"><i class="fa fa-exclamation-triangle"></i> Master ruangan belum tersedia.</div>
        @else
            <div class="schedule-board-shell" id="scheduleBoardShell">
                <div class="schedule-grid" id="scheduleGrid">
                    <div class="schedule-corner">Jam</div>
                    @foreach($ruangan as $roomIndex => $room)
                        @php $roomColor = $roomPalette[$roomIndex % count($roomPalette)]; @endphp
                        <div class="schedule-room-header" style="grid-column: {{ $roomIndex + 2 }}; grid-row: 1; --room-header: {{ $roomColor['header'] }};">
                            <span>{{ $room->nama_ruangan }}</span>
                            <span class="schedule-room-count" data-room-count="{{ $room->id }}">0</span>
                        </div>
                    @endforeach

                    <div class="schedule-time-axis" style="height: {{ $boardHeight }}px;">
                        @foreach($timeline['labels'] as $label)
                            @php
                                $labelTop = ($label['minute'] - $timeline['start']) * $timeline['pixels_per_minute'];
                                $labelTop = max(7, min($boardHeight - 7, $labelTop));
                            @endphp
                            <span class="schedule-time-label" style="top: {{ $labelTop }}px;">{{ $label['label'] }}</span>
                        @endforeach
                    </div>

                    @foreach($ruangan as $roomIndex => $room)
                        @php $roomColor = $roomPalette[$roomIndex % count($roomPalette)]; @endphp
                        <div class="schedule-room-track"
                             data-room-id="{{ $room->id }}"
                             data-room-name="{{ $room->nama_ruangan }}"
                             style="grid-column: {{ $roomIndex + 2 }}; height: {{ $boardHeight }}px; --room-track: {{ $roomColor['track'] }}; --room-line: {{ $roomColor['line'] }}; --room-border: {{ $roomColor['border'] }};">
                            @foreach($peserta->filter(function ($item) use ($room) { return $item->is_scheduled && (int) $item->ruangan === (int) $room->id; }) as $item)
                                @include('tugasakhir.prodi._jadwal_ruangan_card', ['item' => $item, 'unscheduled' => false])
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<div class="schedule-time-preview" id="scheduleTimePreview" role="status" aria-live="polite"></div>

<div class="modal fade" id="scheduleEditorModal" tabindex="-1" role="dialog" aria-labelledby="scheduleEditorTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="scheduleEditorTitle">Atur Ruangan dan Jam Ujian</h4>
            </div>
            <div class="modal-body">
                <div class="schedule-modal-student">
                    <strong id="editorStudentName">-</strong><br>
                    <small id="editorStudentMeta">-</small>
                </div>
                <div class="form-group">
                    <label for="editorRoom">Ruangan</label>
                    <select class="form-control" id="editorRoom" required>
                        <option value="">Pilih ruangan</option>
                        @foreach($ruangan as $room)
                            <option value="{{ $room->id }}">{{ $room->nama_ruangan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="editorStart">Jam Mulai</label>
                            <input class="form-control" id="editorStart" type="time" step="600" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="editorDuration">Durasi (menit)</label>
                            <input class="form-control" id="editorDuration" type="number" min="30" max="300" step="10" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-left" id="removeScheduleButton" style="display:none;" title="Kembalikan ke daftar belum dijadwalkan">
                    <i class="fa fa-undo"></i> Belum Dijadwalkan
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveScheduleButton"><i class="fa fa-save"></i> Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    'use strict';

    var updateUrl = {!! json_encode(url('prodi/jadwal/tanggal/'.$tanggal.'/ruangan')) !!};
    var timelineStart = {{ (int) $timeline['start'] }};
    var timelineEnd = {{ (int) $timeline['end'] }};
    var slotMinutes = {{ (int) $timeline['slot_minutes'] }};
    var pixelsPerMinute = {{ (float) $timeline['pixels_per_minute'] }};
    var draggedCard = null;
    var editorCard = null;
    var suppressClick = false;
    var sessionReloadPending = false;
    var dragGrabOffsetMinutes = 0;
    var dragPreviewStart = null;
    var dragPreviewRoomId = null;
    var dropPreview = null;
    var timePreview = document.getElementById('scheduleTimePreview');

    function examClass(type) {
        type = parseInt(type, 10);
        return type === 0 ? 'exam-proposal' : (type === 2 ? 'exam-meja' : 'exam-other');
    }

    function snapMinute(minute) {
        return Math.round(minute / slotMinutes) * slotMinutes;
    }

    function minuteToTime(minute) {
        minute = Math.max(0, Math.min(1440, parseInt(minute, 10) || 0));
        var hour = Math.floor(minute / 60);
        var minutes = minute % 60;
        return (hour < 10 ? '0' : '') + hour + ':' + (minutes < 10 ? '0' : '') + minutes;
    }

    function clampStartMinute(start, duration) {
        return Math.max(timelineStart, Math.min(timelineEnd - duration, snapMinute(start)));
    }

    function timeRangeLabel(start, duration) {
        return minuteToTime(start) + ' - ' + minuteToTime(start + duration) + ' (' + duration + ' menit)';
    }

    function showTimePreview(start, duration, clientX, clientY, roomName) {
        if (!timePreview) {
            return;
        }

        var label = timeRangeLabel(start, duration);
        timePreview.textContent = roomName ? roomName + ' | ' + label : label;
        timePreview.style.display = 'block';
        timePreview.style.left = Math.max(8, Math.min(window.innerWidth - 240, clientX + 14)) + 'px';
        timePreview.style.top = Math.max(8, Math.min(window.innerHeight - 48, clientY - 42)) + 'px';
    }

    function hideTimePreview() {
        if (timePreview) {
            timePreview.style.display = 'none';
        }
    }

    function showDropPreview(track, start, duration) {
        if (!dropPreview) {
            dropPreview = document.createElement('div');
            dropPreview.className = 'schedule-drop-preview';
        }

        dropPreview.style.top = ((start - timelineStart) * pixelsPerMinute + 2) + 'px';
        dropPreview.style.height = Math.max(32, duration * pixelsPerMinute - 4) + 'px';
        dropPreview.textContent = timeRangeLabel(start, duration);
        track.appendChild(dropPreview);
    }

    function hideDropPreview() {
        if (dropPreview && dropPreview.parentNode) {
            dropPreview.parentNode.removeChild(dropPreview);
        }
        dragPreviewStart = null;
        dragPreviewRoomId = null;
    }

    function getCardData(card) {
        return {
            scheduleId: parseInt(card.getAttribute('data-schedule-id'), 10),
            nim: card.getAttribute('data-nim'),
            roomId: parseInt(card.getAttribute('data-room-id'), 10) || 0,
            start: parseInt(card.getAttribute('data-start-minute'), 10),
            duration: parseInt(card.getAttribute('data-duration'), 10) || 100,
            type: parseInt(card.getAttribute('data-exam-type'), 10)
        };
    }

    function notifySuccess(message) {
        if (window.toastr) {
            toastr.success(message);
        }
    }

    function notifyError(message) {
        if (window.toastr) {
            toastr.error(message);
            return;
        }
        window.alert(message);
    }

    function errorMessage(error) {
        if (isExpiredSession(error)) {
            return 'Sesi login telah berakhir. Silakan masuk kembali untuk menyimpan perubahan jadwal.';
        }
        if (error && error.response && error.response.data) {
            if (error.response.data.message) {
                return error.response.data.message;
            }
            if (error.response.data.errors) {
                var keys = Object.keys(error.response.data.errors);
                if (keys.length) {
                    return error.response.data.errors[keys[0]][0];
                }
            }
        }
        return 'Perubahan jadwal belum dapat disimpan.';
    }

    function isExpiredSession(error) {
        return !!(error && error.response && parseInt(error.response.status, 10) === 419);
    }

    function recoverExpiredSession() {
        if (sessionReloadPending) {
            return;
        }
        sessionReloadPending = true;

        var message = 'Sesi login telah berakhir. Halaman akan dimuat ulang agar Anda dapat masuk kembali.';
        if (window.toastr) {
            toastr.error(message);
            window.setTimeout(function () {
                window.location.reload();
            }, 1400);
            return;
        }

        window.alert(message);
        window.location.reload();
    }

    function updateCounts() {
        var scheduled = document.querySelectorAll('.schedule-room-track .schedule-card').length;
        var unscheduled = document.querySelectorAll('#unscheduledPool .schedule-card').length;
        $('#scheduledParticipantCount').text(scheduled);
        $('#unscheduledParticipantCount, #unscheduledBadge').text(unscheduled);
        $('#unscheduledEmpty').toggle(unscheduled === 0);

        document.querySelectorAll('[data-room-count]').forEach(function (counter) {
            var roomId = counter.getAttribute('data-room-count');
            counter.textContent = document.querySelectorAll('.schedule-room-track[data-room-id="' + roomId + '"] .schedule-card').length;
        });
    }

    function setCardPosition(card) {
        var data = getCardData(card);
        if (!data.start && data.start !== 0) {
            return;
        }
        card.style.top = ((data.start - timelineStart) * pixelsPerMinute + 2) + 'px';
        card.style.height = Math.max(42, data.duration * pixelsPerMinute - 4) + 'px';
    }

    function layoutRoom(track) {
        var cards = Array.prototype.slice.call(track.querySelectorAll('.schedule-card'));
        cards.sort(function (a, b) {
            return getCardData(a).start - getCardData(b).start;
        });

        var groups = [];
        var current = [];
        var groupEnd = -1;
        cards.forEach(function (card) {
            var data = getCardData(card);
            var end = data.start + data.duration;
            if (current.length && data.start >= groupEnd) {
                groups.push(current);
                current = [];
                groupEnd = -1;
            }
            current.push(card);
            groupEnd = Math.max(groupEnd, end);
        });
        if (current.length) {
            groups.push(current);
        }

        groups.forEach(function (group) {
            var laneEnds = [];
            group.forEach(function (card) {
                var data = getCardData(card);
                var lane = 0;
                while (lane < laneEnds.length && laneEnds[lane] > data.start) {
                    lane++;
                }
                laneEnds[lane] = data.start + data.duration;
                card.setAttribute('data-lane', lane);
            });

            var laneCount = Math.max(1, laneEnds.length);
            group.forEach(function (card) {
                var lane = parseInt(card.getAttribute('data-lane'), 10) || 0;
                card.style.left = 'calc(' + (lane * 100 / laneCount) + '% + 4px)';
                card.style.width = 'calc(' + (100 / laneCount) + '% - 8px)';
                setCardPosition(card);
            });
        });
    }

    function layoutAllRooms() {
        document.querySelectorAll('.schedule-room-track').forEach(layoutRoom);
        updateCounts();
    }

    function applySavedSchedule(card, response) {
        if (response.jam_mulai_menit < timelineStart || response.jam_mulai_menit + response.durasi_menit > timelineEnd) {
            window.location.reload();
            return;
        }

        var track = document.querySelector('.schedule-room-track[data-room-id="' + response.ruangan + '"]');
        if (!track) {
            notifyError('Kolom ruangan tujuan tidak ditemukan.');
            return;
        }

        card.setAttribute('data-room-id', response.ruangan);
        card.setAttribute('data-start-minute', response.jam_mulai_menit);
        card.setAttribute('data-duration', response.durasi_menit);
        card.classList.remove('is-unscheduled');
        card.classList.remove('exam-proposal', 'exam-meja', 'exam-other');
        card.classList.add(examClass(card.getAttribute('data-exam-type')));
        card.querySelector('.schedule-card-time').textContent = response.jam_ujian;
        track.appendChild(card);
        enableResize(card);
        layoutAllRooms();
    }

    function applyRemovedSchedule(card) {
        card.querySelectorAll('.schedule-duration-handle').forEach(function (resizeHandle) {
            resizeHandle.parentNode.removeChild(resizeHandle);
        });
        card.removeAttribute('data-resize-bound');
        card.setAttribute('data-room-id', '');
        card.setAttribute('data-start-minute', '');
        card.setAttribute('data-duration', '100');
        card.style.top = '';
        card.style.left = '';
        card.style.width = '';
        card.style.height = '';
        card.classList.add('is-unscheduled');
        card.querySelector('.schedule-card-time').textContent = 'Belum diatur';
        document.getElementById('unscheduledPool').insertBefore(card, document.getElementById('unscheduledEmpty'));
        layoutAllRooms();
    }

    function saveSchedule(card, payload, onSuccess, onFailure) {
        if (card.getAttribute('data-saving') === '1') {
            return;
        }
        card.setAttribute('data-saving', '1');
        card.classList.add('is-saving');

        axios.post(updateUrl, payload).then(function (response) {
            onSuccess(response.data);
            notifySuccess(response.data.message);
        }).catch(function (error) {
            if (onFailure) {
                onFailure();
            }
            if (isExpiredSession(error)) {
                recoverExpiredSession();
                return;
            }
            notifyError(errorMessage(error));
        }).then(function () {
            card.removeAttribute('data-saving');
            card.classList.remove('is-saving');
        });
    }

    function moveCard(card, roomId, start, duration) {
        start = clampStartMinute(start, duration);
        saveSchedule(card, {
            jadwal_ujian_id: card.getAttribute('data-schedule-id'),
            C_NPM: card.getAttribute('data-nim'),
            ruangan: roomId,
            jam_mulai: minuteToTime(start),
            durasi_menit: duration
        }, function (response) {
            applySavedSchedule(card, response);
        });
    }

    function enableResize(card) {
        if (card.classList.contains('is-unscheduled') || card.getAttribute('data-resize-bound') === '1') {
            return;
        }
        card.setAttribute('data-resize-bound', '1');

        var pointerId = null;
        var startY = 0;
        var resizeEdge = null;
        var originalStart = 0;
        var originalEnd = 0;
        var originalDuration = 0;
        var previewStart = 0;
        var previewDuration = 0;
        var resizeData = null;

        function showResize(start, duration, event) {
            start = Math.max(timelineStart, Math.min(timelineEnd - 30, start));
            duration = Math.max(30, Math.min(300, timelineEnd - start, duration));
            previewStart = start;
            previewDuration = duration;
            card.setAttribute('data-start-minute', start);
            card.setAttribute('data-duration', duration);
            setCardPosition(card);
            card.querySelector('.schedule-card-time').textContent = minuteToTime(start) + ' - ' + minuteToTime(start + duration);
            if (event) {
                var track = card.closest('.schedule-room-track');
                showTimePreview(start, duration, event.clientX, event.clientY, track ? track.getAttribute('data-room-name') : '');
            }
        }

        function restoreOriginalSchedule() {
            showResize(originalStart, originalDuration);
            layoutAllRooms();
        }

        function finishResize(event, cancelled) {
            if (pointerId === null || (event.pointerId !== undefined && event.pointerId !== pointerId)) {
                return;
            }
            event.preventDefault();
            event.stopPropagation();
            card.setAttribute('draggable', 'true');
            card.classList.remove('is-resizing');
            pointerId = null;
            hideTimePreview();

            if (cancelled || (previewStart === originalStart && previewDuration === originalDuration)) {
                restoreOriginalSchedule();
                window.setTimeout(function () { suppressClick = false; }, 200);
                return;
            }

            saveSchedule(card, {
                jadwal_ujian_id: resizeData.scheduleId,
                C_NPM: resizeData.nim,
                ruangan: resizeData.roomId,
                jam_mulai: minuteToTime(previewStart),
                durasi_menit: previewDuration
            }, function (response) {
                applySavedSchedule(card, response);
            }, function () {
                restoreOriginalSchedule();
            });
            window.setTimeout(function () { suppressClick = false; }, 200);
        }

        ['start', 'end'].forEach(function (edge) {
            var handle = document.createElement('span');
            handle.className = 'schedule-duration-handle is-' + edge;
            handle.setAttribute('data-resize-edge', edge);
            handle.setAttribute('role', 'button');
            handle.setAttribute('aria-label', edge === 'start' ? 'Ubah jam mulai ujian' : 'Ubah jam selesai ujian');
            handle.setAttribute('title', edge === 'start' ? 'Tarik untuk mengubah jam mulai' : 'Tarik untuk mengubah jam selesai');
            handle.innerHTML = '<i class="fa fa-arrows-v" aria-hidden="true"></i>';
            card.appendChild(handle);

            handle.addEventListener('pointerdown', function (event) {
                if (card.getAttribute('data-saving') === '1') {
                    return;
                }
                event.preventDefault();
                event.stopPropagation();
                resizeData = getCardData(card);
                pointerId = event.pointerId;
                startY = event.clientY;
                resizeEdge = edge;
                originalStart = resizeData.start;
                originalDuration = resizeData.duration;
                originalEnd = originalStart + originalDuration;
                previewStart = originalStart;
                previewDuration = originalDuration;
                suppressClick = true;
                card.setAttribute('draggable', 'false');
                card.classList.add('is-resizing');
                showResize(previewStart, previewDuration, event);
                if (handle.setPointerCapture) {
                    handle.setPointerCapture(pointerId);
                }
            });

            handle.addEventListener('pointermove', function (event) {
                if (pointerId === null || event.pointerId !== pointerId) {
                    return;
                }
                event.preventDefault();
                var deltaMinutes = (event.clientY - startY) / pixelsPerMinute;

                if (resizeEdge === 'start') {
                    var minimumStart = Math.max(timelineStart, originalEnd - 300);
                    var maximumStart = originalEnd - 30;
                    var nextStart = Math.max(minimumStart, Math.min(maximumStart, snapMinute(originalStart + deltaMinutes)));
                    showResize(nextStart, originalEnd - nextStart, event);
                    return;
                }

                var minimumEnd = originalStart + 30;
                var maximumEnd = Math.min(timelineEnd, originalStart + 300);
                var nextEnd = Math.max(minimumEnd, Math.min(maximumEnd, snapMinute(originalEnd + deltaMinutes)));
                showResize(originalStart, nextEnd - originalStart, event);
            });

            handle.addEventListener('pointerup', function (event) {
                finishResize(event, false);
            });

            handle.addEventListener('pointercancel', function (event) {
                finishResize(event, true);
            });
        });
    }

    function openEditor(card) {
        editorCard = card;
        var data = getCardData(card);
        $('#editorStudentName').text(card.getAttribute('data-student-name'));
        $('#editorStudentMeta').text(data.nim + ' | ' + card.getAttribute('data-exam-label'));
        $('#editorRoom').val(data.roomId || '');
        $('#editorStart').val((data.start || data.start === 0) ? minuteToTime(data.start) : '08:00');
        $('#editorDuration').val(data.duration || 100);
        $('#removeScheduleButton').toggle(!card.classList.contains('is-unscheduled'));
        $('#scheduleEditorModal').modal('show');
    }

    function bindCards() {
        document.querySelectorAll('.schedule-card').forEach(function (card) {
            if (card.getAttribute('data-bound') === '1') {
                return;
            }
            card.setAttribute('data-bound', '1');
            card.setAttribute('draggable', 'true');
            card.setAttribute('tabindex', '0');

            card.addEventListener('dragstart', function (event) {
                draggedCard = card;
                suppressClick = true;
                card.classList.add('is-dragging');
                var data = getCardData(card);
                var cardRect = card.getBoundingClientRect();
                dragGrabOffsetMinutes = card.classList.contains('is-unscheduled')
                    ? 0
                    : Math.max(0, Math.min(data.duration, (event.clientY - cardRect.top) / pixelsPerMinute));
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', card.getAttribute('data-event-key'));
            });
            card.addEventListener('dragend', function () {
                card.classList.remove('is-dragging');
                document.querySelectorAll('.is-drop-target').forEach(function (target) {
                    target.classList.remove('is-drop-target');
                });
                hideDropPreview();
                hideTimePreview();
                dragGrabOffsetMinutes = 0;
                draggedCard = null;
                window.setTimeout(function () { suppressClick = false; }, 150);
            });
            card.addEventListener('click', function () {
                if (!suppressClick) {
                    openEditor(card);
                }
            });
            card.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openEditor(card);
                }
            });
            enableResize(card);
        });
    }

    document.querySelectorAll('.schedule-room-track').forEach(function (track) {
        track.addEventListener('dragover', function (event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';
            track.classList.add('is-drop-target');
            if (!draggedCard) {
                return;
            }

            var rect = track.getBoundingClientRect();
            var data = getCardData(draggedCard);
            var rawStart = timelineStart + ((event.clientY - rect.top) / pixelsPerMinute) - dragGrabOffsetMinutes;
            var start = clampStartMinute(rawStart, data.duration);
            dragPreviewStart = start;
            dragPreviewRoomId = parseInt(track.getAttribute('data-room-id'), 10);
            showDropPreview(track, start, data.duration);
            showTimePreview(start, data.duration, event.clientX, event.clientY, track.getAttribute('data-room-name'));
        });
        track.addEventListener('dragleave', function (event) {
            if (!track.contains(event.relatedTarget)) {
                track.classList.remove('is-drop-target');
                if (dropPreview && dropPreview.parentNode === track) {
                    hideDropPreview();
                    hideTimePreview();
                }
            }
        });
        track.addEventListener('drop', function (event) {
            event.preventDefault();
            track.classList.remove('is-drop-target');
            if (!draggedCard) {
                return;
            }
            var data = getCardData(draggedCard);
            var roomId = parseInt(track.getAttribute('data-room-id'), 10);
            var rect = track.getBoundingClientRect();
            var start = dragPreviewRoomId === roomId && dragPreviewStart !== null
                ? dragPreviewStart
                : clampStartMinute(timelineStart + ((event.clientY - rect.top) / pixelsPerMinute) - dragGrabOffsetMinutes, data.duration);
            hideDropPreview();
            hideTimePreview();
            moveCard(draggedCard, roomId, start, data.duration);
        });
    });

    var unscheduledDropzone = document.getElementById('unscheduledDropzone');
    unscheduledDropzone.addEventListener('dragover', function (event) {
        if (draggedCard && !draggedCard.classList.contains('is-unscheduled')) {
            event.preventDefault();
            unscheduledDropzone.classList.add('is-drop-target');
        }
    });
    unscheduledDropzone.addEventListener('dragleave', function (event) {
        if (!unscheduledDropzone.contains(event.relatedTarget)) {
            unscheduledDropzone.classList.remove('is-drop-target');
        }
    });
    unscheduledDropzone.addEventListener('drop', function (event) {
        event.preventDefault();
        unscheduledDropzone.classList.remove('is-drop-target');
        if (!draggedCard || draggedCard.classList.contains('is-unscheduled')) {
            return;
        }
        openEditor(draggedCard);
    });

    $('#saveScheduleButton').on('click', function () {
        if (!editorCard) {
            return;
        }
        var roomId = parseInt($('#editorRoom').val(), 10);
        var startTime = $('#editorStart').val();
        var duration = parseInt($('#editorDuration').val(), 10);
        if (!roomId || !startTime || duration < 30 || duration > 300) {
            notifyError('Lengkapi ruangan, jam mulai, dan durasi ujian.');
            return;
        }
        var startParts = startTime.split(':');
        var start = parseInt(startParts[0], 10) * 60 + parseInt(startParts[1], 10);
        if (start + duration > 1440) {
            notifyError('Rentang waktu ujian melewati batas hari.');
            return;
        }
        $('#scheduleEditorModal').modal('hide');
        moveCard(editorCard, roomId, start, duration);
    });

    $('#removeScheduleButton').on('click', function () {
        if (!editorCard) {
            return;
        }
        var card = editorCard;
        $('#scheduleEditorModal').modal('hide');
        saveSchedule(card, {
            jadwal_ujian_id: card.getAttribute('data-schedule-id'),
            C_NPM: card.getAttribute('data-nim'),
            hapus_jadwal: 1
        }, function () {
            applyRemovedSchedule(card);
        });
    });

    $('#scheduleEditorModal').on('hidden.bs.modal', function () {
        editorCard = null;
    });

    bindCards();
    layoutAllRooms();
})();
</script>
@endsection
