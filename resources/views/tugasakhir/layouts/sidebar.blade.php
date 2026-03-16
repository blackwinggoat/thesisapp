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

        <li class="static">MENU KETUA PRODI</li>
        <li>
            <a href="{{ url('prodi/dosen_pembimbing')}}">
                <i class="fa  fa-institution icon-sidebar"></i>
                Dosen Pembimbing
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/mahasiswa')}}">
                <i class="fa fa-user icon-sidebar"></i>
                Mahasiswa
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/scope_ta')}}">
                <i class="fa fa-stack-overflow icon-sidebar"></i>
                Bidang Ilmu TA
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/topik')}}">
                <i class="fa fa-files-o icon-sidebar"></i>
                Topik Penelitian
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/usulan_pembimbing')}}">
                <i class="fa fa-stack-overflow icon-sidebar"></i>
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
            <a href="{{ url('prodi/usulan_timujianta')}}">
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
            <a href="{{ url('prodi/syarat_ujian')}}">
                <i class="fa fa-file-text icon-sidebar"></i>
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
            <a href="{{ url('prodi/sk_ujian')}}">
                <i class="fa fa-paperclip icon-sidebar"></i>
                SK Ujian
            </a>
        </li>
        <li>
            <a href="{{url('prodi/pengumuman')}}">
                <i class="fa fa-bullhorn icon-sidebar"></i>
                Pengumuman
            </a>
        </li>




        <li class="static">MENU AKADEMIK-PRODI</li>
        <li>
            <a href="{{ url('prodi/dosen_pembimbing')}}">
                <i class="fa  fa-institution icon-sidebar"></i>
                Dosen Pembimbing
            </a>
        </li>
        <li>
            <a href="{{ url('prodi/mahasiswa')}}">
                <i class="fa fa-user icon-sidebar"></i>
                Mahasiswa
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
                <i class="fa fa-users icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Peserta
            </a>
            <ul class="submenu">
                <li><a href="{{ url('prodi/peserta_proposal')}}">Proposal</a></li>
                <li><a href="{{ url('prodi/peserta_ujianmeja')}}">Ujian Meja</a></li>
            </ul>
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


        <li class="static">MENU DOSEN</li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-table icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Jadwal Ujian
            </a>
            <ul class="submenu">
                <li><a href="{{url('dosen/jadwal_proposal')}}">Proposal</a></li>
                <li><a href="{{url('dosen/jadwal_ujianmeja')}}">Ujian Meja</a></li>
            </ul>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-envelope icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Bimbingan
            </a>
            <ul class="submenu">
                <li><a href="{{url('mhs/mail_new')}}">Pesan baru</a></li>
                <li><a href="{{url('mhs/mail_inbox')}}">Pesan Masuk <span class="badge badge-success span-sidebar">6</span></a></li>
                <li><a href="{{url('mhs/mail_sent')}}">Pesan Keluar</a></li>
            </ul>
        </li>


        <li class="static">MENU MAHASISWA</li>
        <li>
            <a href="{{url('mhs/Download')}}">
                <i class="fa fa-paperclip icon-sidebar"></i>
                Download
            </a>
        </li>
        <li>
            <a href="{{url('xxx/pengajuan_topik')}}">
                <i class="fa fa-files-o icon-sidebar"></i>
                Topik Penelitian
            </a>
        </li>
        <li>
            <a href="{{url('mhs/riwayat_ujian')}}">
                <i class="fa fa-history icon-sidebar"></i>
                Riwayat Ujian
            </a>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-envelope icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Bimbingan
            </a>
            <ul class="submenu">
                <li><a href="{{url('mhs/mail_new')}}">Pesan baru</a></li>
                <li><a href="{{url('mhs/mail_inbox')}}">Pesan Masuk <span class="badge badge-success span-sidebar">6</span></a></li>
                <li><a href="{{url('mhs/mail_sent')}}">Pesan Keluar</a></li>
            </ul>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-clipboard icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Proposal
            </a>
            <ul class="submenu">
                <li><a href="{{url('mhs/signup_proposal')}}">Daftar Ujian</a></li>
                <li><a href="{{url('mhs/beritaacara_proposal')}}">Berita Acara</a></li>
            </ul>
        </li>
        <li>
            <ul class="submenu">
                <li><a href="{{url('mhs/signup_seminarhasil')}}">Daftar Ujian</a></li>
                <li><a href="{{url('mhs/beritaacara_seminarhasil')}}">Berita Acara</a></li>
            </ul>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa  fa-clipboard icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Ujian Meja
            </a>
            <ul class="submenu">
                <li><a href="{{url('mhs/signup_ujianmeja')}}">Daftar Ujian</a></li>
                <li><a href="{{url('mhs/beritaacara_ujianmeja')}}">Berita Acara</a></li>
            </ul>
        </li>
        <li class="static">MASTER DATA</li>
        <li>
            <a href="{{url('/data-master/periode-jabatan')}}">
                <i class="fa fa-paperclip icon-sidebar"></i>
                Periode Jabatan
            </a>
        </li>
    </ul>
</div><!-- /.sidebar-left -->
<!-- END SIDEBAR LEFT -->
