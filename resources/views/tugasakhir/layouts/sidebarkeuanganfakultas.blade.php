<!-- BEGIN SIDEBAR LEFT -->
<div class="sidebar-left sidebar-nicescroller">
    <ul class="sidebar-menu">

        <li class="static left-profile-summary">
            <div class="media">
                <p class="pull-left">

                    <img src="{{ asset('master/assets/img/avatar/avatar-1.jpg') }}" class="avatar img-circle media-object"
                        alt="Avatar">
                </p>
                <div class="media-body">
                    <h4>Welcome,
                        <br /><strong>Keuangan Fakultas</strong>
                    </h4>
                    <a style="display: inline-block; width: 30px; padding: 0px; height: 30px; color: white; line-height: 2.3; border-radius: 5px;"
                        href="{{ url('dsn/ubah_password') }}" class="btn btn-success btn-xs"><i
                            class="fa fa-cog"></i></a>
                    <a style="display: inline-block; width: 80px; padding: 0px; height: 30px; color: white; line-height: 2.3; border-radius: 5px;"
                        class="btn btn-danger btn-xs" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </li>

        <li>
            <a href="{{ url('/') }}">
                <i class="fa fa-home icon-sidebar"></i>
                Home
            </a>
        </li>
        <li class="static">MENU DOSEN</li>
        <li>
            <a href="{{ route('master_pembayaran_home') }}">
                <i class="fa fa-money icon-sidebar"></i>
                Master Pembayaran
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
