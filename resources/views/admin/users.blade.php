@extends('layouts.dashboard')

@section('title', 'Kelola User')

@section('content')
    <header class="anim-in grid items-end gap-6 border-b-2 border-stone-800/80 pb-7 dark:border-zinc-100/80 sm:grid-cols-[1fr_auto]">
        <div>
            <div class="mb-3 flex items-center gap-3">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[.22em] text-[#be1836] dark:text-rose-400">Panel admin / 02</span>
                <span class="h-px w-12 bg-stone-300 dark:bg-white/10"></span>
                <span class="font-mono text-[10px] text-stone-400">{{ $users->total() }} user</span>
            </div>
            <h1 class="font-display text-4xl font-extrabold leading-none tracking-[-.035em] sm:text-5xl">Kelola user.</h1>
            <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-zinc-400">
                <span>Akun, email, password, status</span>
                <span aria-hidden="true">/</span>
                <span class="font-bold text-[#be1836] dark:text-rose-400">Ban & suspend</span>
            </div>
        </div>
        <button type="button" onclick="openModal('addModal')" class="primary-button pressable inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b] sm:w-auto">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah User
        </button>
    </header>

    <section class="anim-in mt-7">
        <form method="GET" action="{{ route('admin.users') }}" class="paper-sheet grid gap-3 rounded-sm p-3 sm:grid-cols-2 lg:grid-cols-[minmax(220px,1fr)_auto_auto_auto_auto]">
            <label class="relative block">
                <span class="sr-only">Cari user</span>
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama, username, email..." class="field min-h-11 w-full rounded-sm py-2.5 pl-10 pr-4 text-sm">
            </label>
            <label>
                <span class="sr-only">Filter kelas</span>
                <select name="class_name" class="field select-field min-h-11 w-full rounded-sm px-4 py-2.5 text-sm lg:min-w-[150px]">
                    <option value="">Semua Kelas</option>
                    @foreach($classOptions as $c)
                        <option value="{{ $c }}" {{ request('class_name') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="sr-only">Filter role</span>
                <select name="role" class="field select-field min-h-11 w-full rounded-sm px-4 py-2.5 text-sm lg:min-w-[140px]">
                    <option value="">Semua Role</option>
                    @foreach($roleOptions as $r)
                        <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $r)) }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="sr-only">Filter status</span>
                <select name="status" class="field select-field min-h-11 w-full rounded-sm px-4 py-2.5 text-sm lg:min-w-[140px]">
                    <option value="">Semua Status</option>
                    @foreach($statusOptions as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex gap-2">
                @if(request('search') || request('class_name') || request('role') || request('status'))
                    <a href="{{ route('admin.users') }}" class="pressable inline-flex min-h-11 flex-1 items-center justify-center rounded-sm border border-stone-300 px-4 text-sm font-bold text-stone-600 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">Reset</a>
                @endif
                <button type="submit" class="pressable inline-flex min-h-11 flex-1 items-center justify-center rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b]">Cari</button>
            </div>
        </form>
    </section>

    <section class="anim-in mt-7 space-y-3" style="animation-delay: 80ms">
        @if($users->isEmpty())
            <div class="paper-sheet mx-auto max-w-xl rounded-sm px-8 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-stone-300 text-stone-400 dark:border-zinc-700 dark:text-zinc-500">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </div>
                <p class="font-display mt-5 text-2xl font-bold">Tidak ada user.</p>
                <p class="mt-2 text-sm text-stone-500 dark:text-zinc-400">Coba ubah kata kunci pencarian atau filter.</p>
            </div>
        @else
            @foreach($users as $user)
                <article class="paper-sheet rounded-sm px-4 py-4 sm:px-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="font-display flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 {{ match($user->status) { 'banned' => 'border-red-500 text-red-500', 'suspended' => 'border-amber-500 text-amber-500', default => 'border-[#be1836] text-[#be1836]' } }} text-base font-extrabold">{{ strtoupper(Str::substr($user->name, 0, 1)) }}</span>
                            <span class="min-w-0">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="truncate text-sm font-bold">{{ $user->name }}</span>
                                    @if($user->id === Auth::id())
                                        <span class="rounded-sm bg-[#be1836]/10 px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wider text-[#be1836] dark:bg-rose-500/10 dark:text-rose-400">Anda</span>
                                    @endif
                                </span>
                                <span class="block truncate font-mono text-[10px] text-stone-500 dark:text-zinc-400">{{ $user->username }} • {{ $user->email ?: 'tanpa email' }}</span>
                                <span class="mt-1 flex flex-wrap items-center gap-1.5">
                                    <span class="rounded-sm border border-stone-300 px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wider text-stone-600 dark:border-white/15 dark:text-zinc-300">{{ $user->class_name }}</span>
                                    <span class="rounded-sm border px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wider {{ match($user->role) { 'admin' => 'border-[#be1836]/50 bg-[#be1836]/10 text-[#be1836]', 'pj_mapel' => 'border-emerald-500/50 bg-emerald-500/10 text-emerald-700 dark:text-emerald-400', 'ketua_kelas', 'sekretaris' => 'border-amber-500/50 bg-amber-500/10 text-amber-700 dark:text-amber-400', default => 'border-sky-500/50 bg-sky-500/10 text-sky-700 dark:text-sky-400' } }}">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
                                    @if($user->status === 'banned')
                                        <span class="rounded-sm border border-red-500/50 bg-red-500/10 px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400">Banned</span>
                                    @elseif($user->status === 'suspended')
                                        <span class="rounded-sm border border-amber-500/50 bg-amber-500/10 px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">Suspended</span>
                                    @endif
                                </span>
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-1 sm:flex sm:items-center sm:justify-end">
                            <button type="button" onclick='openEditUser(@json($user))' class="pressable min-h-10 rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-[#be1836] dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-rose-400">Edit</button>
                            <form method="POST" action="{{ route('admin.users.reset', $user) }}" onsubmit="return confirm('Kirim link reset password ke {{ $user->email ?: 'email ' . $user->name }}?')">
                                @csrf
                                <button type="submit" class="pressable min-h-10 w-full rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-amber-600 dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-amber-400">Reset PW</button>
                            </form>
                            @if($user->id !== Auth::id())
                                @if($user->status === 'banned')
                                    <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                        @csrf
                                        <button type="submit" class="pressable min-h-10 w-full rounded-sm px-3 text-xs font-bold text-emerald-600 hover:bg-emerald-500/10 dark:text-emerald-400">Aktifkan</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.ban', $user) }}" onsubmit="return confirm('Ban user {{ $user->name }}? Mereka tidak akan bisa login.')">
                                        @csrf
                                        <button type="submit" class="pressable min-h-10 w-full rounded-sm px-3 text-xs font-bold text-red-500 hover:bg-red-500/10 dark:text-red-400">Ban</button>
                                    </form>
                                @endif
                                @if($user->status === 'suspended')
                                    <form method="POST" action="{{ route('admin.users.activate', $user) }}">
                                        @csrf
                                        <button type="submit" class="pressable min-h-10 w-full rounded-sm px-3 text-xs font-bold text-emerald-600 hover:bg-emerald-500/10 dark:text-emerald-400">Aktifkan</button>
                                    </form>
                                @elseif($user->status !== 'banned')
                                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" onsubmit="return confirm('Suspend user {{ $user->name }}? Mereka tidak akan bisa login.')">
                                        @csrf
                                        <button type="submit" class="pressable min-h-10 w-full rounded-sm px-3 text-xs font-bold text-amber-600 hover:bg-amber-500/10 dark:text-amber-400">Suspend</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus user {{ $user->name }}? Data tugas & relasi ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="pressable min-h-10 w-full rounded-sm px-3 text-xs font-bold text-stone-500 hover:bg-red-500/10 hover:text-red-600 dark:text-zinc-500 dark:hover:text-red-400">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach

            @if($users->hasPages())
                <div class="mt-6 flex justify-center">{{ $users->onEachSide(1)->links() }}</div>
            @endif
        @endif
    </section>
@endsection

@section('modals')
    {{-- Tambah User --}}
    <div class="modal-overlay p-4" id="addModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-2xl rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="addModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Lembar baru</p>
                    <h2 id="addModalTitle" class="font-display mt-1 text-2xl font-extrabold">Tambah user baru.</h2>
                </div>
                <button type="button" onclick="closeModal('addModal')" aria-label="Tutup" class="pressable flex h-10 w-10 shrink-0 items-center justify-center rounded-sm border border-stone-300 text-stone-500 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">01 / Nama lengkap</label>
                        <input type="text" name="name" required value="{{ old('name') }}" class="field w-full rounded-sm px-4 py-3 text-sm">
                        @error('name') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">02 / Username</label>
                        <input type="text" name="username" required value="{{ old('username') }}" class="field w-full rounded-sm px-4 py-3 text-sm">
                        @error('username') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">03 / Email</label>
                        <input type="email" name="email" required value="{{ old('email') }}" class="field w-full rounded-sm px-4 py-3 text-sm">
                        @error('email') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">04 / Password awal</label>
                        <input type="text" name="password" required value="{{ old('password') }}" class="field w-full rounded-sm px-4 py-3 text-sm">
                        @error('password') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">05 / Kelas</label>
                        <select name="class_name" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled {{ old('class_name') ? '' : 'selected' }}>Pilih kelas</option>
                            @foreach($classOptions as $c)
                                <option value="{{ $c }}" {{ old('class_name') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                        @error('class_name') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">06 / Role</label>
                        <select name="role" class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            @foreach($roleOptions as $r)
                                <option value="{{ $r }}" {{ old('role') === $r ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $r)) }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-stone-200 pt-5 dark:border-white/10 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeModal('addModal')" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-sm font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                    <button type="submit" class="primary-button pressable min-h-11 rounded-sm bg-[#be1836] px-7 text-sm font-bold text-white hover:bg-[#a30f2b]">Simpan User</button>
                </div>
            </form>
        </section>
    </div>

    {{-- Edit User --}}
    <div class="modal-overlay p-4" id="editModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-2xl rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Koreksi data</p>
                    <h2 id="editModalTitle" class="font-display mt-1 text-2xl font-extrabold">Edit user.</h2>
                </div>
                <button type="button" onclick="closeModal('editModal')" aria-label="Tutup" class="pressable flex h-10 w-10 shrink-0 items-center justify-center rounded-sm border border-stone-300 text-stone-500 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" id="editForm" action="">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">01 / Nama lengkap</label>
                        <input type="text" name="name" id="edit-name" required class="field w-full rounded-sm px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">02 / Username</label>
                        <input type="text" name="username" id="edit-username" required class="field w-full rounded-sm px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">03 / Email</label>
                        <input type="email" name="email" id="edit-email" required class="field w-full rounded-sm px-4 py-3 text-sm">
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">04 / Kelas</label>
                        <select name="class_name" id="edit-class" class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            @foreach($classOptions as $c)
                                <option value="{{ $c }}">{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">05 / Role</label>
                        <select name="role" id="edit-role" class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            @foreach($roleOptions as $r)
                                <option value="{{ $r }}">{{ ucwords(str_replace('_', ' ', $r)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-stone-200 pt-5 dark:border-white/10 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeModal('editModal')" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-sm font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                    <button type="submit" class="primary-button pressable min-h-11 rounded-sm bg-[#be1836] px-7 text-sm font-bold text-white hover:bg-[#a30f2b]">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        var m = document.getElementById(id);
        if (!m) return;
        m.classList.add('is-open');
        m.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        var m = document.getElementById(id);
        if (!m) return;
        m.classList.remove('is-open');
        m.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }
    document.querySelectorAll('.modal-overlay').forEach(function (ov) {
        ov.addEventListener('click', function (e) { if (e.target === this) closeModal(this.id); });
    });

    function openEditUser(user) {
        document.getElementById('editForm').action = '{{ url('admin/users') }}/' + user.id;
        document.getElementById('edit-name').value = user.name;
        document.getElementById('edit-username').value = user.username;
        document.getElementById('edit-email').value = user.email || '';
        document.getElementById('edit-class').value = user.class_name || '';
        document.getElementById('edit-role').value = user.role;
        openModal('editModal');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.is-open').forEach(function (m) { closeModal(m.id); });
        }
    });
</script>
@endsection
