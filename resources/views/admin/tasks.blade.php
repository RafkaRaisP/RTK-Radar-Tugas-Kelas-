@extends('layouts.dashboard')

@section('title', 'Kelola Tugas')

@section('content')
    <header class="anim-in grid items-end gap-6 border-b-2 border-stone-800/80 pb-7 dark:border-zinc-100/80 sm:grid-cols-[1fr_auto]">
        <div>
            <div class="mb-3 flex items-center gap-3">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[.22em] text-[#be1836] dark:text-rose-400">Panel admin / 03</span>
                <span class="h-px w-12 bg-stone-300 dark:bg-white/10"></span>
                <span class="font-mono text-[10px] text-stone-400">{{ $tasks->total() }} tugas</span>
            </div>
            <h1 class="font-display text-4xl font-extrabold leading-none tracking-[-.035em] sm:text-5xl">Kelola tugas.</h1>
            <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-zinc-400">
                <span>Semua tugas lintas kelas</span>
                <span aria-hidden="true">/</span>
                <span class="font-bold text-[#be1836] dark:text-rose-400">CRUD penuh</span>
            </div>
        </div>
        <button type="button" onclick="openModal('addModal')" class="primary-button pressable inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b] sm:w-auto">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Tulis Tugas Baru
        </button>
    </header>

    <section class="anim-in mt-7">
        <form method="GET" action="{{ route('admin.tasks') }}" class="paper-sheet grid gap-3 rounded-sm p-3 sm:grid-cols-2 lg:grid-cols-[minmax(220px,1fr)_auto_auto_auto]">
            <label class="relative block">
                <span class="sr-only">Cari tugas</span>
                <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-stone-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari judul tugas..." class="field min-h-11 w-full rounded-sm py-2.5 pl-10 pr-4 text-sm">
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
                <span class="sr-only">Filter mapel</span>
                <select name="subject" class="field select-field min-h-11 w-full rounded-sm px-4 py-2.5 text-sm lg:min-w-[145px]">
                    <option value="">Semua Mapel</option>
                    @foreach($subjects as $sub)
                        <option value="{{ $sub }}" {{ request('subject') === $sub ? 'selected' : '' }}>{{ $sub }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex gap-2">
                @if(request('search') || request('class_name') || request('subject'))
                    <a href="{{ route('admin.tasks') }}" class="pressable inline-flex min-h-11 flex-1 items-center justify-center rounded-sm border border-stone-300 px-4 text-sm font-bold text-stone-600 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">Reset</a>
                @endif
                <button type="submit" class="pressable inline-flex min-h-11 flex-1 items-center justify-center rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b]">Cari</button>
            </div>
        </form>
    </section>

    <section class="anim-in mt-7 space-y-3" style="animation-delay: 80ms">
        @if($tasks->isEmpty())
            <div class="paper-sheet mx-auto max-w-xl rounded-sm px-8 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-stone-300 text-stone-400 dark:border-zinc-700 dark:text-zinc-500">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <p class="font-display mt-5 text-2xl font-bold">Tidak ada tugas.</p>
                <p class="mt-2 text-sm text-stone-500 dark:text-zinc-400">Coba ubah kata kunci pencarian atau filter.</p>
            </div>
        @else
            @foreach($tasks as $task)
                @php $expired = now()->gt($task->deadline); @endphp
                <article class="paper-sheet rounded-sm border-l-4 {{ $expired ? '!border-l-stone-400' : '!border-l-[#be1836]' }} px-4 py-4 sm:px-5">
                    <div class="flex flex-col gap-3 border-b border-dashed border-stone-300 pb-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 flex-wrap items-center gap-2">
                            <span class="font-mono text-[9px] font-bold uppercase tracking-[.18em] text-[#be1836] dark:text-rose-400">{{ $task->class_name }}</span>
                            <span class="text-stone-300 dark:text-zinc-700">/</span>
                            <span class="truncate font-mono text-[9px] uppercase tracking-[.14em] text-stone-500 dark:text-zinc-400">{{ $task->subject }}</span>
                            <span class="font-mono text-[9px] text-stone-400">oleh {{ $task->user?->name ?? '—' }}</span>
                        </div>
                        <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-sm border px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wider {{ $expired ? 'border-stone-400/50 bg-stone-400/10 text-stone-500 dark:text-zinc-400' : 'border-emerald-500/50 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' }}">
                            @if($expired) Terlewat @else Aktif @endif
                        </span>
                    </div>

                    <div class="py-3">
                        <h3 class="font-display text-lg font-extrabold leading-tight tracking-tight">{{ $task->title }}</h3>
                        @if($task->description)
                            <p class="mt-1 text-sm leading-6 text-stone-600 dark:text-zinc-400">{{ Str::limit($task->description, 100) }}</p>
                        @endif
                    </div>

                    <div class="flex flex-col gap-3 border-t border-stone-200 pt-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-2 text-stone-500 dark:text-zinc-400">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            <span class="font-mono text-[10px] font-medium tabular-nums">{{ $task->deadline->format('d M Y, H:i') }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1 sm:flex sm:items-center sm:justify-end">
                            <button type="button" onclick="openEditTask({{ $task->id }}, @js($task->title), @js($task->subject), @js($task->description ?? ''), @js($task->deadline->toIso8601String()))" class="pressable min-h-10 rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-[#be1836] dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-rose-400">Edit</button>
                            <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}" onsubmit="return confirm('Hapus tugas ini? Semua data selesai & foto ikut terhapus.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="pressable min-h-10 w-full rounded-sm px-3 text-xs font-bold text-stone-500 hover:bg-red-500/10 hover:text-red-600 dark:text-zinc-500 dark:hover:text-red-400">Hapus</button>
                            </form>
                        </div>
                    </div>

                    <div class="edit-panel mt-3 hidden" id="edit-row-{{ $task->id }}">
                        <form method="POST" action="{{ route('admin.tasks.update', $task) }}" class="paper-sheet rounded-sm border-l-4 !border-l-[#be1836] p-4">
                            @csrf @method('PUT')
                            <div class="mb-4 flex items-center gap-3">
                                <span class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Koreksi catatan</span>
                                <span class="h-px flex-1 bg-stone-200 dark:bg-white/10"></span>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                <label><span class="sr-only">Judul</span><input type="text" name="title" id="etitle-{{ $task->id }}" required placeholder="Judul" class="field w-full rounded-sm px-3 py-2.5 text-sm"></label>
                                <label><span class="sr-only">Mapel</span>
                                    <select name="subject" id="esubject-{{ $task->id }}" required class="field select-field w-full rounded-sm px-3 py-2.5 text-sm">
                                        <option value="">Pilih mapel</option>
                                        @foreach($subjects as $s)
                                            <option value="{{ $s }}">{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label><span class="sr-only">Deadline</span><input type="datetime-local" name="deadline" id="edeadline-{{ $task->id }}" required class="field w-full rounded-sm px-3 py-2.5 text-sm"></label>
                                <label><span class="sr-only">Deskripsi</span><textarea name="description" id="edescription-{{ $task->id }}" rows="1" placeholder="Deskripsi (opsional)" class="field w-full rounded-sm px-3 py-2.5 text-sm"></textarea></label>
                            </div>
                            <div class="mt-4 flex gap-2">
                                <button type="submit" class="pressable min-h-10 rounded-sm bg-[#be1836] px-5 text-xs font-bold text-white hover:bg-[#a30f2b]">Simpan</button>
                                <button type="button" onclick="cancelEdit({{ $task->id }})" class="pressable min-h-10 rounded-sm border border-stone-300 px-5 text-xs font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                            </div>
                        </form>
                    </div>
                </article>
            @endforeach

            @if($tasks->hasPages())
                <div class="mt-6 flex justify-center">{{ $tasks->onEachSide(1)->links() }}</div>
            @endif
        @endif
    </section>
@endsection

@section('modals')
    <div class="modal-overlay p-4" id="addModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-2xl rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="addModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Lembar tugas baru</p>
                    <h2 id="addModalTitle" class="font-display mt-1 text-2xl font-extrabold">Tulis ke dalam agenda.</h2>
                </div>
                <button type="button" onclick="closeModal('addModal')" aria-label="Tutup" class="pressable flex h-10 w-10 shrink-0 items-center justify-center rounded-sm border border-stone-300 text-stone-500 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.tasks.store') }}">
                @csrf
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">01 / Judul tugas</label>
                        <input type="text" name="title" required value="{{ old('title') }}" class="field w-full rounded-sm px-4 py-3 text-sm">
                        @error('title') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">02 / Mata pelajaran</label>
                        <select name="subject" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Pilih mapel</option>
                            @foreach($subjects as $s)
                                <option value="{{ $s }}" {{ old('subject') === $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('subject') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">03 / Deadline</label>
                        <input type="datetime-local" name="deadline" required value="{{ old('deadline') }}" class="field w-full rounded-sm px-4 py-3 text-sm">
                        @error('deadline') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">04 / Kelas</label>
                        <select name="class_name" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled {{ old('class_name') ? '' : 'selected' }}>Pilih kelas</option>
                            @foreach($classOptions as $c)
                                <option value="{{ $c }}" {{ old('class_name') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                        @error('class_name') <p class="mt-2 text-xs text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">05 / Deskripsi <span class="normal-case">(opsional)</span></label>
                        <textarea name="description" rows="2" class="field w-full rounded-sm px-4 py-3 text-sm">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-stone-200 pt-5 dark:border-white/10 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeModal('addModal')" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-sm font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                    <button type="submit" class="primary-button pressable min-h-11 rounded-sm bg-[#be1836] px-7 text-sm font-bold text-white hover:bg-[#a30f2b]">Simpan Tugas</button>
                </div>
            </form>
        </section>
    </div>
@endsection

@section('scripts')
<script>
    function openModal(id) { var m = document.getElementById(id); if (!m) return; m.classList.add('is-open'); m.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; }
    function closeModal(id) { var m = document.getElementById(id); if (!m) return; m.classList.remove('is-open'); m.setAttribute('aria-hidden','true'); document.body.style.overflow=''; }
    document.querySelectorAll('.modal-overlay').forEach(function (ov) { ov.addEventListener('click', function (e) { if (e.target === this) closeModal(this.id); }); });

    function openEditTask(id, title, subject, description, iso) {
        var row = document.getElementById('edit-row-' + id);
        if (!row) return;
        row.classList.toggle('hidden');
        document.getElementById('etitle-' + id).value = title;
        document.getElementById('esubject-' + id).value = subject;
        document.getElementById('edescription-' + id).value = description || '';
        document.getElementById('edeadline-' + id).value = iso.slice(0, 16);
    }
    function cancelEdit(id) {
        var row = document.getElementById('edit-row-' + id);
        if (row) row.classList.add('hidden');
    }
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { document.querySelectorAll('.modal-overlay.is-open').forEach(function (m) { closeModal(m.id); }); } });
</script>
@endsection
