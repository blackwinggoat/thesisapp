<!-- BEGIN SIDEBAR LEFT -->
<div class="sidebar-left sidebar-nicescroller">
    <ul class="sidebar-menu">
        <li class="static left-profile-summary">
            <div class="media">
                <p class="pull-left">

                    <img src="{{ asset('master/assets/img/avatar/avatar-1.jpg')}}" class="avatar img-circle media-object" alt="Avatar">
                </p>
                <div class="media-body">
                    <h4>Welcome, <br /><strong>HUZAIN AZIS</strong></h4>
                    <button class="btn btn-success btn-xs"><i class="fa fa-cog"></i></button>
                    <button class="btn btn-danger btn-xs" >Log out</button>
                </div>
            </div>
        </li>
        <li>
            <a href="{{ url('/')}}">
                <i class="fa fa-home icon-sidebar"></i>
                Home
            </a>
        </li>

        <li class="static">MENU PROGRAM STUDI (ADMIN)</li>
        <li>
            <a href="{{ route('tampilDownload')}}">
                <i class="fa  fa-download icon-sidebar"></i>
                Download
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/dosen_pembimbing')}}">
                <i class="fa fa-graduation-cap icon-sidebar"></i>
                Dosen Pembimbing
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/laporan_mahasiswa')}}">
                <i class="fa fa-comments icon-sidebar"></i>
                Laporan Mahasiswa
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/mahasiswa')}}">
                <i class="fa fa-user icon-sidebar"></i>
                Mahasiswa
            </a>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-bar-chart-o icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Report
            </a>
            <ul class="submenu">
                <li><a href="{{ url('prodi/report') }}">Dashboard</a></li>
                <li><a href="{{ url('prodi/report/laporan') }}">Distribusi Bimbingan</a></li>
                <li><a href="{{ route('prodi.report_jenis_tugas_akhir') }}">Persebaran Jenis TA</a></li>
            </ul>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-database icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Master
            </a>
            <ul class="submenu">
                <li><a href="{{ url('prodi/master/dosen') }}">Dosen</a></li>
                <li><a href="{{ url('prodi/master/jenis_tugas_akhir') }}">Jenis Tugas Akhir</a></li>
                <li><a href="{{ url('prodi/scope_ta') }}">Bidang Ilmu TA</a></li>
            </ul>
        </li>
        <li>
            <a href="{{ url('prodi/topik')}}">
                <i class="fa fa-lightbulb-o icon-sidebar"></i>
                Topik Penelitian
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/usulan_pembimbing')}}">
                <i class="fa fa-user-plus icon-sidebar"></i>
                Usulan Pembimbing
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/sk_pembimbing')}}">
                <i class="fa fa-file-text icon-sidebar"></i>
                Surat Usulan Pembimbing
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/sk_ujian_ta')}}">
                <i class="fa fa-file-text icon-sidebar"></i>
                Surat Usulan Tim Ujian TA
            </a>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-users icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Peserta
            </a>
            <ul class="submenu">
                <li><a href="{{ url('prodi/peserta_proposal')}}">Proposal</a></li>
                <li><a href="{{ url('prodi/peserta_ujianmeja')}}">Ujian Meja</a></li>
            </ul>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-file-text icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Konfirmasi Persyaratan Ujian
            </a>
            <ul class="submenu">
                <li><a href="{{ url('prodi/persyaratan_proposal')}}">Proposal</a></li>
                <li><a href="{{ url('prodi/persyaratan_ujianmeja')}}">Ujian Meja</a></li>
            </ul>
        </li>
        <li>
            <a href="{{ url('prodi/syarat_ujian')}}">
                <i class="fa fa-list-ul icon-sidebar"></i>
                Persyaratan Ujian
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/jadwal')}}">
                <i class="fa fa-calendar icon-sidebar"></i>
                Jadwal Ujian
            </a>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-calendar icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Jadwal Ujian Per Mahasiswa
            </a>
            <ul class="submenu">
                <li><a href="{{ url('prodi/jadwalpermhs/proposal')}}">Proposal</a></li>
                <li><a href="{{ url('prodi/jadwalpermhs/ujianmeja')}}">Ujian Meja</a></li>
            </ul>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-gavel icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Surat Keputusan
            </a>
            <ul class="submenu">
                <li><a href="{{ url('prodi/sk_ujian')}}">SK Ujian</a></li>
                <li><a href="{{ url('prodi/surat_keputusan_pembimbing')}}">Surat Pembimbing</a></li>
                <li><a href="{{ url('prodi/surat_penugasan_ujian_tugas_akhir')}}">Surat Penugasan Ujian TA</a></li>
            </ul>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-check-square-o icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Approve Hasil Ujian
            </a>
            <ul class="submenu">
                <li><a href="{{ url('prodi/approve_hasilujian_proposal')}}">Proposal</a></li>
                <li><a href="{{ url('prodi/approve_hasilujian_ta')}}">Ujian Meja</a></li>
            </ul>
        </li>
        <li>
            <a href="{{url('prodi/pengumuman')}}">
                <i class="fa fa-bullhorn icon-sidebar"></i>
                Pengumuman
            </a>
        </li>




        <li class="static">MENU AKADEMIK-FAKULTAS</li>
        <li>
            <a href="{{url('fakultas/sk_pembimbing')}}" >
                <i class="fa fa-paperclip icon-sidebar"></i>
                SK Pembimbing
            </a>
        </li>
        <li>
            <a href="{{url('fakultas/sk_pembimbing')}}" >
                <i class="fa fa-paperclip icon-sidebar"></i>
                Surat Penugasan Ujian TA
            </a>
        </li>
        <li>
            <a href="{{ route('fakultas.rekap_ujian_selesai') }}">
                <i class="fa fa-list-alt icon-sidebar"></i>
                SK Yudisium
            </a>
        </li>


        <li class="static">MENU WAKIL DEKAN</li>
        <li>
            <a href="{{url('fakultas/usulan_pembimbing')}}">
                <i class="fa fa-stack-overflow icon-sidebar"></i>
                Usulan Pembimbing
            </a>
        </li>
        <li>
            <a href="{{url('fakultas/sk_ujian')}}">
                <i class="fa fa-paperclip icon-sidebar"></i>
                SK Ujian Meja
            </a>
        </li>


        <li class="static">MENU DEKAN</li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-bar-chart-o icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Laporan
            </a>
            <ul class="submenu">
                <li><a href="{{url('fakultas/chart_morris')}}">Morris chart</a></li>
                <!-- <li><a href="{{url('fakultas/chart_c3')}}">C3 chart</a></li>
                <li><a href="{{url('fakultas/chart_flot')}}">Flot chart</a></li> -->
                <li><a href="{{url('fakultas/chart_easy_knob')}}">Easy pie chart &amp; knob</a></li>
            </ul>
        </li>


        <li class="static">MENU KEUANGAN FAKULTAS</li>
        <li>
            <a href="{{ route('master_pembayaran_home') }}">
                <i class="fa fa-money icon-sidebar"></i>
                Master Pembayaran
            </a>
        </li>
        <li>
            <a href="{{ route('sanksi_pembayaran_home') }}">
                <i class="fa fa-exclamation-circle icon-sidebar"></i>
                Sanksi Pembayaran
            </a>
        </li>
        <li>
            <a href="{{ route('honorarium_home') }}">
                <i class="fa fa-google-wallet icon-sidebar"></i>
                Honorarium
            </a>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-envelope icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Report
            </a>
            <ul class="submenu">
                <li><a href="{{ route('report_periode_ujian_home') }}">Periode Ujian</a></li>
                <li><a href="{{ route('report_dosen_home') }}">Dosen</a></li>
            </ul>
        </li>


        <li class="static">MASTER DATA</li>
        <li>
            <a href="{{ route('admin.users.index') }}">
                <i class="fa fa-users icon-sidebar"></i>
                Manajemen User
            </a>
        </li>
        <li>
            <a href="{{url('/data-master/periode-jabatan')}}">
                <i class="fa fa-paperclip icon-sidebar"></i>
                Periode Jabatan
            </a>
        </li>
        <li>
            <a href="{{ route('admin.mail_settings') }}">
                <i class="fa fa-envelope icon-sidebar"></i>
                Email Sistem
            </a>
        </li>
    </ul>
</div><!-- /.sidebar-left -->
<!-- END SIDEBAR LEFT -->
