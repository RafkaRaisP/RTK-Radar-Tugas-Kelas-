@extends('layouts.dashboard')

@section('title', 'Tahun Ajaran')

@section('content')
    <header class="anim-in grid items-end gap-6 border-b-2 border-stone-800/80 pb-7 dark:border-zinc-100/80 sm:grid-cols-[1fr_auto]">
        <div>
            <div class="mb-3 flex items-center gap-3">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[.22em] text-[#be1836] dark:text-rose-400">Panel admin</span>
                <span class="h-px w-12 bg-stone-300 dark:bg-white/10"></span>
                <span class="font-mono text-[10px] text-stone-400">{{ $academicYears->total() }} tahun ajaran</span>
            </div>
            <h1 class="font-display text-4xl font-extrabold leading-none tracking-[-.035em] sm:text-5xl">Tahun ajaran.</h1>
            <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-zinc-400">
                <span>Periode akademik</span>
                <span aria-hidden="true">/</span>
                <span class="font-bold text-[#be1836] dark:text-rose-400">1 aktif</span>
            </div>
        </div>
        <button type="button" onclick="openModal('addModal')" class="primary-button pressable inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b] sm:w-auto">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Tahun
        </button>
    </header>

    <section class="anim-in mt-7 space-y-3" style="animation-delay: 80ms">
        @if($academicYears->isEmpty())
            <div class="paper-sheet mx-auto max-w-xl rounded-sm px-8 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-stone-300 text-stone-400 dark:border-zinc-700 dark:text-zinc-500">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </div>
                <p class="font-display mt-5 text-2xl font-bold">Belum ada tahun ajaran.</p>
                <p class="mt-2 text-sm text-stone-500 dark:text-zinc-400">Buat tahun ajaran dulu sebelum menetapkan PJ.</p>
            </div>
        @else
            @foreach($academicYears as $ay)
                <article class="paper-sheet rounded-sm px-4 py-4 sm:px-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="font-display flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 {{ $ay->is_active ? 'border-[#be1836] text-[#be1836]' : 'border-stone-300 text-stone-400' }} text-base font-extrabold">{{ substr($ay->name, 0, 1) }}</span>
                            <span class="min-w-0">
                                <span class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-bold">{{ $ay->name }}</span>
                                    @if($ay->is_active)
                                        <span class="rounded-sm bg-[#be1836]/10 px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wider text-[#be1836] dark:bg-rose-500/10 dark:text-rose-400">Aktif</span>
                                    @endif
                                </span>
                                <span class="block truncate font-mono text-[10px] text-stone-500 dark:text-zinc-400">{{ $ay->pj_assignments_count }} assignment PJ</span>
                            </span>
                        </div>
                        <div class="flex items-center gap-1 sm:justify-end">
                            <a href="{{ route('admin.pj', ['academic_year_id' => $ay->id]) }}" class="pressable min-h-10 rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-[#be1836] dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-rose-400">Lihat PJ</a>
                            <a href="{{ route('admin.tasks', ['academic_year_id' => $ay->id]) }}" class="pressable min-h-10 rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-[#be1836] dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-rose-400">Lihat Tugas</a>
                            <button type="button" onclick='openEditAy(@json($ay))' class="pressable min-h-10 rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-[#be1836] dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-rose-400">Edit</button>
                            <form method="POST" action="{{ route('admin.academic-years.destroy', $ay) }}" onsubmit="return confirm('Hapus tahun ajaran {{ $ay->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="pressable min-h-10 rounded-sm px-3 text-xs font-bold text-stone-500 hover:bg-red-500/10 hover:text-red-600 dark:text-zinc-500 dark:hover:text-red-400">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach

            @if($academicYears->hasPages())
                <div class="mt-6 flex justify-center">{{ $academicYears->onEachSide(1)->links() }}</div>
            @endif
        @endif
    </section>
@endsection

@section('modals')
    {{-- Tambah Tahun Ajaran --}}
    <div class="modal-overlay p-4" id="addModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-md rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="addModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Periode baru</p>
                    <h2 id="addModalTitle" class="font-display mt-1 text-2xl font-extrabold">Tambah tahun ajaran.</h2>
                </div>
                <button type="button" onclick="closeModal('addModal')" aria-label="Tutup" class="pressable flex h-10 w-10 shrink-0 items-center justify-center rounded-sm border border-stone-300 text-stone-500 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.academic-years.store') }}">
                @csrf
                <div>
                    <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">01 / Nama tahun ajaran</label>
                    <input type="text" name="name" required placeholder="cth: 2025/2026" value="{{ old('name') }}" class="field w-full rounded-sm px-4 py-3 text-sm">
                    @error('name') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                </div>
                <label class="mt-5 flex cursor-pointer items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" class="h-4 w-4 accent-[#be1836]">
                    <span class="text-sm font-bold text-stone-700 dark:text-zinc-300">Jadikan tahun aktif</span>
                </label>
                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-stone-200 pt-5 dark:border-white/10 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeModal('addModal')" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-sm font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                    <button type="submit" class="primary-button pressable min-h-11 rounded-sm bg-[#be1836] px-7 text-sm font-bold text-white hover:bg-[#a30f2b]">Simpan</button>
                </div>
            </form>
        </section>
    </div>

    {{-- Edit Tahun Ajaran --}}
    <div class="modal-overlay p-4" id="editModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-md rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Koreksi data</p>
                    <h2 id="editModalTitle" class="font-display mt-1 text-2xl font-extrabold">Edit tahun ajaran.</h2>
                </div>
                <button type="button" onclick="closeModal('editModal')" aria-label="Tutup" class="pressable flex h-10 w-10 shrink-0 items-center justify-center rounded-sm border border-stone-300 text-stone-500 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" id="editForm" action="">
                @csrf @method('PUT')
                <div>
                    <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">01 / Nama tahun ajaran</label>
                    <input type="text" name="name" id="edit-name" required placeholder="cth: 2025/2026" class="field w-full rounded-sm px-4 py-3 text-sm">
                </div>
                <label class="mt-5 flex cursor-pointer items-center gap-3">
                    <input type="checkbox" name="is_active" id="edit-active" value="1" class="h-4 w-4 accent-[#be1836]">
                    <span class="text-sm font-bold text-stone-700 dark:text-zinc-300">Jadikan tahun aktif</span>
                </label>
                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-stone-200 pt-5 dark:border-white/10 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeModal('editModal')" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-sm font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                    <button type="submit" class="primary-button pressable min-h-11 rounded-sm bg-[#be1836] px-7 text-sm font-bold text-white hover:bg-[#a30f2b]">Simpan</button>
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

    function openEditAy(ay) {
        document.getElementById('editForm').action = '{{ url('admin/academic-years') }}/' + ay.id;
        document.getElementById('edit-name').value = ay.name;
        document.getElementById('edit-active').checked = !!ay.is_active;
        openModal('editModal');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.is-open').forEach(function (m) { closeModal(m.id); });
        }
    });
</script>
@endsection
