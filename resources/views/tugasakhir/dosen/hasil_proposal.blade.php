@extends('tugasakhir.index')

@section('isi')
@include('tugasakhir.dosen.partials.assessment_cards', [
    'assessmentTitle' => 'Penilaian Proposal',
    'examLabel' => 'Proposal',
    'historyPath' => 'dsn/hasil_proposal_history',
    'detailPath' => 'dsn/detailhasil_proposal',
    'emptyMessage' => 'Tidak ada peserta seminar proposal yang menunggu penilaian Anda.',
])
@endsection

@section('script')
@include('tugasakhir.dosen.partials.assessment_cards_script')
@endsection
