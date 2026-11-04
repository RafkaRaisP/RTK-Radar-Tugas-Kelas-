@extends('layouts.dashboard')

@section('title', 'Mata Pelajaran')

@section('content')
    <header class="anim-in grid items-end gap-6 border-b-2 border-stone-800/80 pb-7 dark:border-zinc-100/80 sm:grid-cols-[1fr_auto]">
        <div>
            <div class="mb-3 flex items-center gap-3">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[.22em] text-[#be1836] dark:text-rose-400">Panel admin / mapel</span>
                <span class="h-px w-12 bg-stone-300 dark:bg-white/10"></span>
                <span class="font-mono text-[10px] txt-muted">{{ $subjects->total() }} mapel</span>
            </div>
            <h1 class="font-display text-4xl font-extrabold leading-none tracking-[-.035em] sm:text-5xl">Mata pelajaran.</h1>
            <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-zinc-400">
                <span>Basis dropdown PJ & tugas</span>
                <span aria-hidden="true">/</span>
                <span class="font-bold text-[#be1836] dark:text-rose-400">CRUD penuh</span>
            </div>
        </div>
        <button type="button" onclick="openModal('addModal')" class="primary-button pressable inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b] sm:w-auto">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tambah Mapel
        </button>
    </header>

    <section class="anim-in mt-7 space-y-3" style="animation-delay: 80ms">
        @if($subjects->isEmpty())
            <div class="paper-sheet mx-auto max-w-xl rounded-sm px-8 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-stone-300 txt-muted dark:border-zinc-700">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                </div>
                <p class="font-display mt-5 text-2xl font-bold">Belum ada mapel.</p>
                <p class="mt-2 text-sm txt-label">Tambahkan mapel untuk dipakai di dropdown.</p>
            </div>
        @else
            @foreach($subjects as $i => $subject)
                <article class="paper-sheet rounded-sm px-4 py-4 sm:px-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span class="font-display flex h-11 w-11 shrink-0 items-center justify-center rounded-full border-2 border-[#be1836] text-base font-extrabold text-[#be1836]">{{ strtoupper(Str::substr($subject->name, 0, 1)) }}</span>
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-bold">{{ $subject->name }}</span>
                                <span class="block font-mono text-[9px] uppercase tracking-wider txt-muted">Mapel / mata pelajaran</span>
                            </span>
                        </div>
                        <div class="flex items-center gap-1 sm:justify-end">
                            <button type="button" onclick="openEditSubject({{ $subject->id }}, @js($subject->name))" class="pressable min-h-11 rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-[#be1836] dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-rose-400">Edit</button>
                            <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}" onsubmit="return confirm('Hapus mapel {{ $subject->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="pressable min-h-11 rounded-sm px-3 text-xs font-bold txt-label hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach

            @if($subjects->hasPages())
                <div class="mt-6 flex justify-center">{{ $subjects->onEachSide(1)->links() }}</div>
            @endif
        @endif
    </section>
@endsection

@section('modals')
    <div class="modal-overlay p-4" id="addModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-md rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="addModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Lembar baru</p>
                    <h2 id="addModalTitle" class="font-display mt-1 text-2xl font-extrabold">Tambah mapel.</h2>
                </div>
                <button type="button" onclick="closeModal('addModal')" aria-label="Tutup" class="pressable flex h-11 w-11 shrink-0 items-center justify-center rounded-sm border border-stone-300 txt-label hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.subjects.store') }}">
                @csrf
                <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] txt-label">Nama mapel</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="contoh: Matematika" class="field w-full rounded-sm px-4 py-3 text-sm">
                @error('name') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-stone-200 pt-5 dark:border-white/10 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeModal('addModal')" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-sm font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                    <button type="submit" class="primary-button pressable min-h-11 rounded-sm bg-[#be1836] px-7 text-sm font-bold text-white hover:bg-[#a30f2b]">Simpan Mapel</button>
                </div>
            </form>
        </section>
    </div>

    <div class="modal-overlay p-4" id="editModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-md rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Koreksi data</p>
                    <h2 id="editModalTitle" class="font-display mt-1 text-2xl font-extrabold">Edit mapel.</h2>
                </div>
                <button type="button" onclick="closeModal('editModal')" aria-label="Tutup" class="pressable flex h-11 w-11 shrink-0 items-center justify-center rounded-sm border border-stone-300 txt-label hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" id="editForm" action="">
                @csrf @method('PUT')
                <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] txt-label">Nama mapel</label>
                <input type="text" name="name" id="edit-name" required class="field w-full rounded-sm px-4 py-3 text-sm">
                @error('name') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
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

    function openEditSubject(id, name) {
        document.getElementById('editForm').action = '{{ url('admin/subjects') }}/' + id;
        document.getElementById('edit-name').value = name;
        openModal('editModal');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.is-open').forEach(function (m) { closeModal(m.id); });
        }
    });
</script>
@endsection
