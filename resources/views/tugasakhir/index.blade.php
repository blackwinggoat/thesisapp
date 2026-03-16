@include('tugasakhir.layouts.header')
@include('tugasakhir.layouts.navigation')



@if (Auth::user()->level == 1)
    @include('tugasakhir.layouts.sidebar')
@elseif(Auth::user()->level == 2)
    @include('tugasakhir.layouts.sidebardekan')
@elseif(Auth::user()->level == 3)
    @include('tugasakhir.layouts.sidebarwakildekan')
@elseif(Auth::user()->level == 4)
    @include('tugasakhir.layouts.sidebarakademikfakultas')
@elseif(Auth::user()->level == 5)
    @include('tugasakhir.layouts.sidebarkaprodi')
@elseif(Auth::user()->level == 6)
    @include('tugasakhir.layouts.sidebarakademikprodi')
@elseif(Auth::user()->level == 7)
    @include('tugasakhir.layouts.sidebardosen')
@elseif(Auth::user()->level == 8)
    @include('tugasakhir.layouts.sidebarmhs')
@elseif(Auth::user()->level == 9)
    @include('tugasakhir.layouts.sidebarkeuanganfakultas')
@endif

@yield('isi')
@include('tugasakhir.layouts.footer')
