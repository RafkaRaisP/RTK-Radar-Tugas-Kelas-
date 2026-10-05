@extends('layouts.dashboard')

@section('title', 'Kelola PJ')

@section('content')
    <header class="anim-in grid items-end gap-6 border-b-2 border-stone-800/80 pb-7 dark:border-zinc-100/80 sm:grid-cols-[1fr_auto]">
        <div>
            <div class="mb-3 flex items-center gap-3">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[.22em] text-[#be1836] dark:text-rose-400">Panel admin / 03</span>
                <span class="h-px w-12 bg-stone-300 dark:bg-white/10"></span>
                <span class="font-mono text-[10px] text-stone-400">{{ $assignments->total() }} assignment</span>
            </div>
            <h1 class="font-display text-4xl font-extrabold leading-none tracking-[-.035em] sm:text-5xl">Kelola PJ.</h1>
            <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-zinc-400">
                <span>Penanggung jawab per mapel & kelas</span>
                <span aria-hidden="true">/</span>
                <span class="font-bold text-[#be1836] dark:text-rose-400">Per tahun ajaran</span>
            </div>
        </div>
        <button type="button" onclick="openModal('addModal')" class="primary-button pressable inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b] sm:w-auto">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah PJ
        </button>
    </header>

    <section class="anim-in mt-7">
        <form method="GET" action="{{ route('admin.pj') }}" class="paper-sheet grid gap-3 rounded-sm p-3 sm:grid-cols-2 lg:grid-cols-[minmax(180px,1fr)_auto_auto_auto_auto]">
            <label class="relative block">
                <span class="sr-only">Cari user</span>
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama user..." class="field min-h-11 w-full rounded-sm py-2.5 pl-10 pr-4 text-sm">
            </label>
            <label>
                <span class="sr-only">Filter tahun ajaran</span>
                <select name="academic_year_id" class="field select-field min-h-11 w-full rounded-sm px-4 py-2.5 text-sm lg:min-w-[150px]">
                    <option value="">Semua Tahun</option>
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}{{ $ay->is_active ? ' (aktif)' : '' }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="sr-only">Filter mapel</span>
                <select name="subject_id" class="field select-field min-h-11 w-full rounded-sm px-4 py-2.5 text-sm lg:min-w-[150px]">
                    <option value="">Semua Mapel</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
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
            <div class="flex gap-2">
                @if(request('search') || request('academic_year_id') || request('subject_id') || request('class_name'))
                    <a href="{{ route('admin.pj') }}" class="pressable inline-flex min-h-11 flex-1 items-center justify-center rounded-sm border border-stone-300 px-4 text-sm font-bold text-stone-600 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">Reset</a>
                @endif
                <button type="submit" class="pressable inline-flex min-h-11 flex-1 items-center justify-center rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b]">Cari</button>
            </div>
        </form>
    </section>

    <section class="anim-in mt-7 space-y-3" style="animation-delay: 80ms">
        @if($assignments->isEmpty())
            <div class="paper-sheet mx-auto max-w-xl rounded-sm px-8 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-stone-300 text-stone-400 dark:border-zinc-700 dark:text-zinc-500">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                </div>
                <p class="font-display mt-5 text-2xl font-bold">Belum ada PJ.</p>
                <p class="mt-2 text-sm text-stone-500 dark:text-zinc-400">Klik "Tambah PJ" untuk menugaskan penanggung jawab.</p>
            </div>
        @else
            @foreach($assignments as $a)
                <article class="paper-sheet rounded-sm px-4 py-4 sm:px-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="font-display flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 border-emerald-500 text-base font-extrabold text-emerald-600 dark:text-emerald-400">{{ strtoupper(Str::substr($a->user->name, 0, 1)) }}</span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-bold">{{ $a->user->name }}</span>
                                <span class="block truncate font-mono text-[10px] text-stone-500 dark:text-zinc-400">{{ $a->user->username }}</span>
                                <span class="mt-1 flex flex-wrap items-center gap-1.5">
                                    <span class="rounded-sm border border-emerald-500/50 bg-emerald-500/10 px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">{{ $a->subject->name }}</span>
                                    <span class="rounded-sm border border-stone-300 px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wider text-stone-600 dark:border-white/15 dark:text-zinc-300">{{ $a->class_name }}</span>
                                    <span class="rounded-sm border border-sky-500/50 bg-sky-500/10 px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wider text-sky-700 dark:text-sky-400">{{ $a->academicYear->name }}{{ $a->academicYear->is_active ? ' *' : '' }}</span>
                                </span>
                            </span>
                        </div>
                        <div class="flex items-center gap-1 sm:justify-end">
                            <button type="button" onclick='openEditPj(@json($a))' class="pressable min-h-10 rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-[#be1836] dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-rose-400">Edit</button>
                            <form method="POST" action="{{ route('admin.pj.destroy', $a) }}" onsubmit="return confirm('Hapus PJ assignment untuk {{ $a->user->name }} ({{ $a->subject->name }})?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="pressable min-h-10 rounded-sm px-3 text-xs font-bold text-stone-500 hover:bg-red-500/10 hover:text-red-600 dark:text-zinc-500 dark:hover:text-red-400">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach

            @if($assignments->hasPages())
                <div class="mt-6 flex justify-center">{{ $assignments->onEachSide(1)->links() }}</div>
            @endif
        @endif
    </section>
@endsection

@section('modals')
    {{-- Tambah PJ --}}
    <div class="modal-overlay p-4" id="addModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-2xl rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="addModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Penugasan baru</p>
                    <h2 id="addModalTitle" class="font-display mt-1 text-2xl font-extrabold">Tambah PJ baru.</h2>
                </div>
                <button type="button" onclick="closeModal('addModal')" aria-label="Tutup" class="pressable flex h-10 w-10 shrink-0 items-center justify-center rounded-sm border border-stone-300 text-stone-500 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.pj.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">01 / Tahun Ajaran</label>
                        <select name="academic_year_id" id="add-year" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled selected>Pilih tahun ajaran</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}{{ $ay->is_active ? ' (aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        @error('academic_year_id') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">02 / Kelas</label>
                        <select name="class_name" id="add-class" required disabled class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled selected>Pilih tahun ajaran dulu</option>
                        </select>
                        @error('class_name') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">03 / Nama Anak</label>
                        <select name="user_id" id="add-user" required disabled class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled selected>Pilih kelas dulu</option>
                        </select>
                        @error('user_id') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">04 / Mata Pelajaran</label>
                        <select name="subject_id" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled selected>Pilih mapel</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                        @error('subject_id') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-stone-200 pt-5 dark:border-white/10 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeModal('addModal')" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-sm font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                    <button type="submit" class="primary-button pressable min-h-11 rounded-sm bg-[#be1836] px-7 text-sm font-bold text-white hover:bg-[#a30f2b]">Simpan PJ</button>
                </div>
            </form>
        </section>
    </div>

    {{-- Edit PJ --}}
    <div class="modal-overlay p-4" id="editModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-2xl rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Koreksi data</p>
                    <h2 id="editModalTitle" class="font-display mt-1 text-2xl font-extrabold">Edit PJ.</h2>
                </div>
                <button type="button" onclick="closeModal('editModal')" aria-label="Tutup" class="pressable flex h-10 w-10 shrink-0 items-center justify-center rounded-sm border border-stone-300 text-stone-500 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" id="editForm" action="">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">01 / Tahun Ajaran</label>
                        <select name="academic_year_id" id="edit-year" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled selected>Pilih tahun ajaran</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}{{ $ay->is_active ? ' (aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">02 / Kelas</label>
                        <select name="class_name" id="edit-class" required disabled class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled selected>Pilih tahun ajaran dulu</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">03 / Nama Anak</label>
                        <select name="user_id" id="edit-user" required disabled class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled selected>Pilih kelas dulu</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">04 / Mata Pelajaran</label>
                        <select name="subject_id" id="edit-subject" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            @foreach($subjects as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
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

    function openEditPj(a) {
        var pj = a;
        if (a.user) pj.user_id = a.user.id;
        if (a.subject) pj.subject_id = a.subject.id;
        if (a.academic_year) pj.academic_year_id = a.academic_year.id;
        document.getElementById('editForm').action = '{{ url('admin/pj') }}/' + a.id;
        document.getElementById('edit-year').value = pj.academic_year_id;
        rebuildPjCascade('edit');
        document.getElementById('edit-class').value = a.class_name || '';
        cascadePjStudents('edit', pj.user_id);
        var uEl = document.getElementById('edit-user');
        if (uEl.value !== String(pj.user_id) && uEl.value === '') {
            // data lama yang tak cocok: tetap tampilkan anak ini biar submit valid
            var o = document.createElement('option');
            o.value = pj.user_id;
            o.textContent = (a.user ? a.user.name : 'Siswa #' + pj.user_id) + ' (bukan di kelas ini)';
            uEl.appendChild(o);
            uEl.value = pj.user_id;
        }
        document.getElementById('edit-subject').value = pj.subject_id;
        openModal('editModal');
    }

    // -- Cascading: tahun ajaran -> kelas -> nama anak -- \\
    var PJ_USERS = {!! $usersJson !!};
    var classesByYear = {};      // { yearId: { className: true } }
    var studentsByYearClass = {}; // { yearId: { className: [user...] } }
    PJ_USERS.forEach(function (u) {
        if (!u.academic_year_id) return;
        var y = String(u.academic_year_id);
        (classesByYear[y] = classesByYear[y] || {});
        (studentsByYearClass[y] = studentsByYearClass[y] || {});
        (studentsByYearClass[y][u.class_name] = studentsByYearClass[y][u.class_name] || []).push(u);
        classesByYear[y][u.class_name] = true;
    });

    function fillSelect(sel, options, placeholder) {
        sel.innerHTML = '';
        var ph = document.createElement('option');
        ph.value = '';
        ph.textContent = placeholder;
        ph.disabled = true;
        sel.appendChild(ph);
        options.forEach(function (o) {
            var opt = document.createElement('option');
            opt.value = o.value;
            opt.textContent = o.label;
            sel.appendChild(opt);
        });
        sel.selectedIndex = 0;
    }

    function classOptions(yearId) {
        return Object.keys(classesByYear[yearId] || {}).sort().map(function (c) {
            return { value: c, label: c };
        });
    }

    // Ganti tahun: rebuild kelas + reset siswa
    function rebuildPjCascade(prefix) {
        var yEl = document.getElementById(prefix + '-year');
        var cEl = document.getElementById(prefix + '-class');
        var uEl = document.getElementById(prefix + '-user');
        var y = yEl.value;
        if (!y) {
            fillSelect(cEl, [], 'Pilih tahun ajaran dulu');
            fillSelect(uEl, [], 'Pilih kelas dulu');
            cEl.disabled = true;
            uEl.disabled = true;
            return;
        }
        fillSelect(cEl, classOptions(y), 'Pilih kelas');
        cEl.disabled = false;
        cascadePjStudents(prefix);
    }

    // Ganti kelas: rebuild daftar nama anak (keepUserId = pertahankan pilihan saat edit)
    function cascadePjStudents(prefix, keepUserId) {
        var yEl = document.getElementById(prefix + '-year');
        var cEl = document.getElementById(prefix + '-class');
        var uEl = document.getElementById(prefix + '-user');
        var y = yEl.value;
        var c = cEl.value;
        if (!c) {
            fillSelect(uEl, [], 'Pilih kelas dulu');
            uEl.disabled = true;
            return;
        }
        var students = (studentsByYearClass[y] || {})[c] || [];
        fillSelect(uEl, students.map(function (u) {
            return { value: u.id, label: u.name };
        }), students.length ? 'Pilih siswa' : 'Tidak ada siswa di kelas ini');
        uEl.disabled = students.length === 0;
        if (keepUserId !== undefined && students.some(function (s) { return String(s.id) === String(keepUserId); })) {
            uEl.value = String(keepUserId);
        }
    }

    document.getElementById('add-year').addEventListener('change', function () { rebuildPjCascade('add'); });
    document.getElementById('add-class').addEventListener('change', function () { cascadePjStudents('add'); });
    document.getElementById('edit-year').addEventListener('change', function () { rebuildPjCascade('edit'); });
    document.getElementById('edit-class').addEventListener('change', function () { cascadePjStudents('edit'); });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.is-open').forEach(function (m) { closeModal(m.id); });
        }
    });
</script>
@endsection
