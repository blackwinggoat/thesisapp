<!-- BEGIN SIDEBAR LEFT -->
<div class="sidebar-left sidebar-nicescroller">
    <ul class="sidebar-menu">
        <li class="static left-profile-summary">
            <div class="media">
                <p class="pull-left">

                    <img src="{{ asset('master/assets/img/avatar/avatar-1.jpg')}}" class="avatar img-circle media-object" alt="Avatar">
                </p>
                <div class="media-body">
                    <h4>Welcome, <br /><strong>{{helper::getNamaMhs(auth()->user()->name)}}</strong></h4>
                    <a style="display: inline-block; width: 30px; padding: 0px; height: 30px; color: white; line-height: 2.3; border-radius: 5px;" href="{{url('mhs/ubah_password')}}" class="btn btn-success btn-xs"><i class="fa fa-cog"></i></a>
                    <a style="display: inline-block; width: 80px; padding: 0px; height: 30px; color: white; line-height: 2.3; border-radius: 5px;" class="btn btn-danger btn-xs" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                </div>
            </div>
        </li>
        <li>
            <a href="{{ url('/')}}">
                <i class="fa fa-home icon-sidebar"></i>
                Home
            </a>
        </li>
        <li class="static">MENU MAHASISWA</li>
        <li>
            <a href="{{url('mhs/download')}}">
                <i class="fa fa-paperclip icon-sidebar"></i>
                Download
            </a>
        </li>
        <li>
            <a href="{{url('mhs/pengajuan_topik')}}">
                <i class="fa fa-files-o icon-sidebar"></i>
                Topik Penelitian
            </a>
        </li>
        <li>
            <a href="{{url('mhs/riwayat_ujian')}}/{{auth()->user()->name}}">
                <i class="fa fa-history icon-sidebar"></i>
                Riwayat Ujian
            </a>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-envelope icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Usulan Judul
            </a>
            <ul class="submenu">
                <li><a href="{{url('mhs/usulan_judul_calon_pembimbing')}}">Usulan Judul Calon Pembimbing</a></li>
                {{-- <li><a href="{{url('mhs/usulan_judul_anak_bimbingan')}}">Usulan Dosen Pembimbing</a></li> --}}
                <li><a href="{{url('mhs/usulan_judul_semua_mahasiswa')}}">Usulan Judul Semua Mahasiswa</a></li>
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
                <li><a href="{{url('mhs/mail_inbox')}}">Pesan Masuk</a></li>
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
                <li><a href="{{url('mhs/beritaacara_proposal')}}/{{auth()->user()->name}}">Berita Acara</a></li>
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
                <li><a href="{{url('mhs/beritaacara_ujian')}}/{{auth()->user()->name}}">Berita Acara</a></li>
            </ul>
        </li>
    </ul>
</div><!-- /.sidebar-left -->
<!-- END SIDEBAR LEFT -->



