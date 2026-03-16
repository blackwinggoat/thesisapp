<body class="tooltips">
<div id="Ocontainer">

    <div id="Omain">
        <div id="outer-circle">
            <div id="inner-circle">
                <div id="center-circle">
                    <div id="content"></div>
                </div>
            </div>
        </div>

    </div>

</div>

<!--
===========================================================
BEGIN PAGE
===========================================================
-->
<div class="wrapper">
    <!-- BEGIN TOP NAV -->
    <div class="top-navbar">
        <div class="top-navbar-inner">

            <!-- Begin Logo brand -->
            <div class="logo-brand">
                <a href="index.html"><img src="{{ asset('master/assets/img/logo_primary.png')}}" alt="Sentir logo"></a>
            </div><!-- /.logo-brand -->
            <!-- End Logo brand -->

            <div class="top-nav-content">

                <!-- Begin button sidebar left toggle -->
                <div class="btn-collapse-sidebar-left">
                    <i class="fa fa-bars"></i>
                </div><!-- /.btn-collapse-sidebar-left -->
                <!-- End button sidebar left toggle -->


                <!-- Begin button nav toggle -->
                <div class="btn-collapse-nav" data-toggle="collapse" data-target="#main-fixed-nav">
                    <i class="fa fa-plus icon-plus"></i>
                </div><!-- /.btn-collapse-sidebar-right -->
                <!-- End button nav toggle -->


                <!-- Begin user session nav -->
                <ul class="nav-user navbar-right">
                    <li class="dropdown">
                        <a href="#fakelink" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ asset('master/assets/img/avatar/avatar-1.jpg')}}" class="avatar img-circle" alt="Avatar">
                            @if(Auth::user()->level==1)
                            @elseif(Auth::user()->level==2)
                             Hi, <strong>Dekan Fakultas Ilmu Komputer</strong>
                            @elseif(Auth::user()->level==3)
                             Hi, <strong>Wakil Dekan Fakultas Ilmu Komputer</strong>
                            @elseif(Auth::user()->level==4)
                                Hi, <strong>Akademik Fakultas</strong>
                            @elseif(Auth::user()->level==5)
                                @if (Auth::user()->name == 'proditi')
                                    Hi, <strong>Teknik Informatika</strong>
                                @else
                                    Hi, <strong>Sistem Informasi</strong>
                                @endif
                            @elseif(Auth::user()->level==6)
                                Hi, <strong>Akademik Prodi</strong>
                            @elseif(Auth::user()->level==7)
                                Hi, <strong>{{helper::getDeskripsi(auth()->user()->name)}}</strong>
                            @elseif(Auth::user()->level==8)
                                Hi, <strong>{{helper::getNamaMhs(auth()->user()->name)}}</strong>
                            @elseif(Auth::user()->level==9)
                                Hi, <strong>Keuangan Fakultas</strong>
                            @endif
                        </a>
                        <ul class="dropdown-menu square primary margin-list-rounded with-triangle">
                            @if(Auth::user()->level==1)
                            @elseif(Auth::user()->level==2)
                            @elseif(Auth::user()->level==3)
                            @elseif(Auth::user()->level==4)
                                <li><a href="{{ url('fakultas/ubah_password/')}}">Change password</a></li>
                            @elseif(Auth::user()->level==5)
                                <li><a href="{{ url('prodi/ubah_password')}}">Change password</a></li>
                            @elseif(Auth::user()->level==6)
                                <li><a href="{{ url('mhs/ubah_password/')}}">Change password</a></li>
                            @elseif(Auth::user()->level==7)
                                <li><a href="{{ url('dsn/ubah_password/')}}">Change password</a></li>
                            @elseif(Auth::user()->level==8)
                                <li><a href="{{ url('mhs/ubah_password/')}}">Change password</a></li>
                            @endif
                            <li class="divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                            </li>
                        </ul>
                    </li>
                </ul>
                <!-- End user session nav -->
                        <!-- End nav notification -->
                        <!-- Begin nav task -->
                        <!-- End nav task -->
                        <!-- Begin nav message -->
                        <!-- End nav friend requuest -->
                    </ul>
                </div><!-- /.navbar-collapse -->
                <!-- End Collapse menu nav -->
            </div><!-- /.top-nav-content -->
        </div><!-- /.top-navbar-inner -->
    </div><!-- /.top-navbar -->
    <!-- END TOP NAV -->



