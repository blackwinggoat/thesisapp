@extends('tugasakhir.index')

@section('isi')
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li><a href="{{ url('/data-master/users') }}">Master Data</a></li>
            <li class="active">Manajemen User</li>
        </ol>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('danger'))
            <div class="alert alert-danger">{{ session('danger') }}</div>
        @endif
        @if (session('status') === 'password_reset')
            <div class="alert alert-success">
                Password user <strong>{{ session('target_user_name') }}</strong> berhasil diperbarui.
            </div>
        @elseif (session('status') === 'user_deleted')
            <div class="alert alert-success">
                User <strong>{{ session('target_user_name') }}</strong> berhasil dihapus. Data master dosen/mahasiswa tidak ikut dihapus.
            </div>
        @elseif (session('status') === 'user_not_found')
            <div class="alert alert-danger">User tujuan tidak ditemukan.</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Periksa kembali data berikut:</strong>
                <ul style="margin: 8px 0 0 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="the-box">
            <div class="clearfix" style="margin-bottom: 14px;">
                <div class="pull-left">
                    <h3 class="page-heading" style="margin: 0;">Manajemen User</h3>
                    <p class="text-muted" style="margin: 6px 0 0;">
                        Kelola akun seluruh role, reset password, dan Login As untuk pemeriksaan fitur.
                    </p>
                </div>
            </div>

            <form method="get" action="{{ route('admin.users.index') }}" class="form-horizontal" style="margin-bottom: 18px;">
                <div class="row">
                    <div class="col-md-4">
                        <label style="display:block;">Cari Username / Email / Nama</label>
                        <input type="text" name="q" class="form-control" value="{{ $q ?? '' }}" placeholder="Contoh: proditi, NIDN, NIM, atau nama">
                    </div>
                    <div class="col-md-3">
                        <label style="display:block;">Role</label>
                        <select name="level" class="form-control">
                            <option value="semua" {{ ($level ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Role</option>
                            @foreach ($roleLabels as $roleLevel => $roleLabel)
                                <option value="{{ $roleLevel }}" {{ (string) ($level ?? 'semua') === (string) $roleLevel ? 'selected' : '' }}>
                                    {{ $roleLabel }} ({{ (int) ($roleCounts[$roleLevel] ?? 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label style="display:block;">Per Halaman</label>
                        <select name="per_page" class="form-control">
                            @foreach ([25, 50, 100, 200] as $size)
                                <option value="{{ $size }}" {{ (int) ($perPage ?? 50) === $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label style="display:block;">Aksi</label>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-default">Reset</a>
                    </div>
                </div>
            </form>

            <div style="margin-bottom: 10px;">
                Menampilkan <strong>{{ $data->count() }}</strong> dari <strong>{{ $data->total() }}</strong> user
                @if (($level ?? 'semua') !== 'semua')
                    <span class="label label-info" style="margin-left: 8px;">{{ $roleLabels[(int) $level] ?? 'Role tidak dikenal' }}</span>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="the-box dark full">
                    <tr>
                        <th style="width: 55px;">No</th>
                        <th>Username</th>
                        <th>Nama / Identitas</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th style="width: 250px;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if ($data->count() === 0)
                        <tr>
                            <td colspan="6" class="text-center">Data user tidak ditemukan.</td>
                        </tr>
                    @endif
                    @foreach ($data as $key => $user)
                        @php
                            $roleLabel = $roleLabels[(int) $user->level] ?? 'Role tidak dikenal';
                            $canLoginAs = array_key_exists((int) $user->level, $roleLabels)
                                && (int) $user->level !== 1
                                && (int) $user->id !== (int) Auth::id();
                            $canDeleteUser = (int) $user->level !== 1
                                && (int) $user->id !== (int) Auth::id();
                        @endphp
                        <tr>
                            <td align="center">{{ $data->firstItem() + $key }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->display_name ?: '-' }}</td>
                            <td>{{ $user->email ?: '-' }}</td>
                            <td><span class="label label-primary">{{ $roleLabel }}</span></td>
                            <td style="white-space: nowrap;">
                                <button type="button"
                                        class="btn btn-default btn-sm js-reset-password"
                                        title="Reset password"
                                        data-toggle="modal"
                                        data-target="#resetPasswordModal"
                                        data-action="{{ route('admin.users.reset_password', $user->id) }}"
                                        data-user-name="{{ $user->name }}"
                                        data-display-name="{{ $user->display_name ?: $user->name }}">
                                    <i class="fa fa-key"></i>
                                </button>

                                @if ($canLoginAs)
                                    <form action="{{ route('admin.users.login_as', $user->id) }}" method="post" style="display: inline-block;" onsubmit="return confirm('Login sebagai {{ $user->display_name ?: $user->name }}?');">
                                        {{ csrf_field() }}
                                        <button type="submit" class="btn btn-info btn-sm" title="Login As">
                                            <i class="fa fa-user"></i> Login As
                                        </button>
                                    </form>
                                @else
                                    <span class="label label-default">Login As tidak tersedia</span>
                                @endif

                                @if ($canDeleteUser)
                                    <form action="{{ route('admin.users.delete', $user->id) }}" method="post" style="display: inline-block;" onsubmit="return confirm('Hapus user {{ $user->display_name ?: $user->name }}? Data master dosen/mahasiswa tidak ikut dihapus.');">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus User">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                {{ $data->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="resetPasswordModal" tabindex="-1" role="dialog" aria-labelledby="resetPasswordModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="" id="resetPasswordForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="resetPasswordModalLabel">Reset Password User</h4>
                </div>
                <div class="modal-body">
                    <p>
                        Password untuk <strong id="resetPasswordTargetName">user</strong> akan diganti.
                    </p>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control" minlength="6" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label>Ulangi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" minlength="6" required autocomplete="new-password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(function () {
        $('.js-reset-password').on('click', function () {
            var button = $(this);
            $('#resetPasswordForm').attr('action', button.data('action'));
            $('#resetPasswordTargetName').text(button.data('display-name') + ' (' + button.data('user-name') + ')');
            $('#resetPasswordForm input[type="password"]').val('');
        });
    });
</script>
@endsection
