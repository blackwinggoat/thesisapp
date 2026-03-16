<!-- BEGIN SIDEBAR LEFT -->
<div class="sidebar-left sidebar-nicescroller">
    <ul class="sidebar-menu">
        <li class="static left-profile-summary">
            <div class="media">
                <p class="pull-left">
                    <img src="{{ asset('master/assets/img/avatar/avatar-1.jpg')}}"
                        class="avatar img-circle media-object" alt="Avatar">
                </p>
                <div class="media-body">
                    <h4>Welcome, <br /><strong>
                    
                        @if (Auth::user()->name == 'akademikproditi')
                            Akademik Program Studi Teknik Informatika
                        @else
                            Akademik Program Studi Sistem Informasi
                        @endif
                        
                    </strong></h4>
                    <a style="display: inline-block; width: 30px; padding: 0px; height: 30px; color: white; line-height: 2.3; border-radius: 5px;"
                        href="{{url('akademikprodi/ubah_password')}}" class="btn btn-success btn-xs"><i
                            class="fa fa-cog"></i></a>
                    <a style="display: inline-block; width: 80px; padding: 0px; height: 30px; color: wh ite; line-height: 2.3; border-radius: 5px;"
                        class="btn btn-danger btn-xs" href="{{ route('logout') }}" onclick="event.preventDefault();
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

        <li class="static">MENU AKADEMIK-PRODI</li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-file-text icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Konfirmasi Persyaratan Ujian
            </a>
            <ul class="submenu">
                <li><a href="{{ url('akademikprodi/persyaratan_proposal')}}">Proposal</a></li>
                <li><a href="{{ url('akademikprodi/persyaratan_ujianmeja')}}">Ujian Meja</a></li>
            </ul>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-table icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Rekap Nilai Ujian
            </a>
            <ul class="submenu">
                <li><a href="{{url('akademikprodi/rekap_nilai_proposal')}}">Proposal</a></li>
                <li><a href="{{url('akademikprodi/rekap_nilai_ujian_ta')}}">Ujian Meja</a></li>
            </ul>
        </li>
        <li>
            <a href="#fakelink">
                <i class="fa fa-paperclip icon-sidebar"></i>
                <i class="fa fa-angle-right chevron-icon-sidebar"></i>
                Surat Keputusan
            </a>
            <ul class="submenu">
                <li><a href="{{ url('akademikprodi/sk_ujian')}}">SK Ujian</a></li>
            </ul>
        </li>
        {{-- <li>
            <a href="{{ url('akademikprodi/dosen_pembimbing')}}">
        <i class="fa  fa-institution icon-sidebar"></i>
        Dosen Pembimbing
        </a>
        </li> --}}
        <li>
            <a href="{{ url('akademikprodi/mahasiswa')}}">
                <i class="fa fa-user icon-sidebar"></i>
                Mahasiswa
            </a>
        </li>
    </ul>
</div><!-- /.sidebar-left -->
<!-- END SIDEBAR LEFT -->



<!-- BEGIN SIDEBAR RIGHT HEADING -->
<div class="sidebar-right-heading">
    <ul class="nav nav-tabs square nav-justified">
        <li class="active"><a href="#online-user-sidebar" data-toggle="tab"><i class="fa fa-comments"></i></a></li>
        <li><a href="#notification-sidebar" data-toggle="tab"><i class="fa fa-bell"></i></a></li>
        <li><a href="#task-sidebar" data-toggle="tab"><i class="fa fa-tasks"></i></a></li>
        <li><a href="#setting-sidebar" data-toggle="tab"><i class="fa fa-cogs"></i></a></li>
    </ul>
</div><!-- /.sidebar-right-heading -->
<!-- END SIDEBAR RIGHT HEADING -->