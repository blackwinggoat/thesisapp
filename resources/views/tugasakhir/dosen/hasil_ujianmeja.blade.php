@extends('tugasakhir.index')

@section('isi')
@include('tugasakhir.dosen.partials.assessment_cards', [
    'assessmentTitle' => 'Penilaian Ujian Akhir',
    'examLabel' => 'Ujian Akhir',
    'historyPath' => 'dsn/hasil_ujianmeja_history',
    'detailPath' => 'dsn/detailhasil_ujianmeja',
    'emptyMessage' => 'Tidak ada peserta ujian akhir yang menunggu penilaian Anda.',
])
@endsection

@section('script')
@include('tugasakhir.dosen.partials.assessment_cards_script')
@endsection
