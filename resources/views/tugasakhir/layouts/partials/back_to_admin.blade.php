@if ((int) session('login_as_source_user_level') === 1 && !empty(session('login_as_source_user_id')))
    <form action="{{ route('admin.back_to_admin') }}" method="POST" style="display: block; margin: 0 0 5px;">
        @csrf
        <button type="submit" class="btn btn-primary btn-xs" title="Kembali ke sesi Admin">
            <i class="fa fa-reply"></i> Kembali ke Admin
        </button>
    </form>
@endif
