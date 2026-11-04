@extends('layouts.dashboard')

@section('title', 'Agenda Saya')
@section('meta-description', 'Agenda tugas pribadi Anda di Radar Tugas Kelas.')

@section('content')
            <header class="anim-in grid items-end gap-6 border-b-2 border-stone-800/80 pb-7 dark:border-zinc-100/80 sm:grid-cols-[1fr_auto]">
                <div>
                    <div class="mb-3 flex items-center gap-3">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-[.22em] text-[#be1836] dark:text-rose-400">Agenda pribadi</span>
                        <span class="h-px w-12 bg-stone-300 dark:bg-white/10"></span>
                        <span class="font-mono text-[10px] txt-muted">{{ now()->format('d.m.Y') }}</span>
                    </div>
                    <h1 class="font-display text-4xl font-extrabold leading-none tracking-[-.035em] sm:text-5xl">Halo, {{ Str::before(Auth::user()->name, ' ') }}.</h1>
                    <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-zinc-400">
                        <span>{{ Auth::user()->class_name }}</span>
                        <span aria-hidden="true">/</span>
                        <span class="font-bold text-[#be1836] dark:text-rose-400">{{ match(Auth::user()->role) { 'admin' => 'Admin', 'ketua_kelas' => 'Ketua Kelas', 'sekretaris' => 'Sekretaris', 'pj_mapel' => 'PJ Mapel', default => 'Siswa' } }}</span>
                    </div>
                </div>
                @if(Auth::user()->role !== 'siswa')
                    <button type="button" onclick="openModal()" class="primary-button pressable inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b] sm:w-auto">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Tulis Tugas Baru
                    </button>
                @endif
            </header>

            @if(session('success'))
                <div role="status" class="flash-msg paper-sheet mt-6 flex items-center gap-3 rounded-sm border-l-4 !border-l-emerald-500 px-5 py-3.5 text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div role="alert" class="flash-msg paper-sheet mt-6 flex items-center gap-3 rounded-sm border-l-4 !border-l-red-500 px-5 py-3.5 text-sm font-semibold text-red-700 dark:text-red-400">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div role="status" class="flash-msg paper-sheet mt-6 flex items-center gap-3 rounded-sm border-l-4 !border-l-amber-500 px-5 py-3.5 text-sm font-semibold text-amber-700 dark:text-amber-400">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                    {{ session('info') }}
                </div>
            @endif

            @if($errors->any())
                <div role="alert" class="flash-msg paper-sheet mt-6 rounded-sm border-l-4 !border-l-red-500 px-5 py-3.5 text-sm font-semibold text-red-700 dark:text-red-400">
                    <p class="mb-1 font-bold">Terjadi kesalahan validasi:</p>
                    <ul class="list-inside list-disc space-y-0.5 text-xs font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $isAdmin = Auth::user()->role === 'admin';
                $ownClass = Auth::user()->class_name;
                $canEdit = Auth::user()->role !== 'siswa';
            @endphp

            <section aria-label="Ringkasan tugas" class="anim-in mt-7 grid grid-cols-2 gap-3 lg:grid-cols-4" style="animation-delay: 70ms">
                <article class="index-card rounded-sm border-t-4 !border-t-[#be1836] px-4 pb-5 pt-4">
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">01 / Total</p>
                    <p class="font-display mt-1 text-3xl font-extrabold">{{ $tasks->total() }}</p>
                </article>
                <article class="index-card rounded-sm border-t-4 !border-t-emerald-500 px-4 pb-5 pt-4">
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">02 / Terverifikasi</p>
                    <p class="font-display mt-1 text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ count(array_filter($taskStatuses, fn ($s) => $s === 'confirmed')) }}</p>
                </article>
                <article class="index-card rounded-sm border-t-4 !border-t-amber-500 px-4 pb-5 pt-4">
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">03 / Pending</p>
                    <p class="font-display mt-1 text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ count(array_filter($taskStatuses, fn ($s) => $s === 'pending')) }}</p>
                </article>
                <article class="index-card rounded-sm border-t-4 !border-t-stone-400 px-4 pb-5 pt-4">
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">04 / Belum Dikerjakan</p>
                    <p class="font-display mt-1 text-3xl font-extrabold txt-label">{{ $tasks->total() - count($taskStatuses) }}</p>
                </article>
            </section>

            <section class="anim-in mt-7" style="animation-delay: 120ms">
                <div class="mb-3 flex items-center gap-3">
                    <h2 class="font-mono text-[10px] font-bold uppercase tracking-[.2em] txt-label">Cari dalam agenda</h2>
                    <span class="h-px flex-1 bg-stone-300 dark:bg-white/10"></span>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" class="paper-sheet grid gap-3 rounded-sm p-3 sm:grid-cols-2 lg:grid-cols-[minmax(220px,1fr)_auto_auto_auto_auto]">
                    <label class="relative block">
                        <span class="sr-only">Cari judul tugas</span>
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 txt-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari judul tugas..." class="field min-h-11 w-full rounded-sm py-2.5 pl-10 pr-4 text-sm">
                    </label>
                    @if($isAdmin)
                        <label>
                            <span class="sr-only">Filter kelas</span>
                            <select name="class_name" onchange="this.form.submit()" class="field min-h-11 w-full rounded-sm px-4 py-2.5 text-sm lg:min-w-[150px]">
                                <option value="">Semua Kelas</option>
                                @foreach(\App\Models\User::classGroups() as $group)
                                    <optgroup label="{{ $group['label'] }}">
                                        @foreach($group['classes'] as $c)
                                            <option value="{{ $c }}" {{ request('class_name') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </label>
                    @endif
                    <label>
                        <span class="sr-only">Filter mata pelajaran</span>
                        <select name="subject" onchange="this.form.submit()" class="field min-h-11 w-full rounded-sm px-4 py-2.5 text-sm lg:min-w-[145px]">
                            <option value="">Semua Mapel</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub }}" {{ request('subject') === $sub ? 'selected' : '' }}>{{ $sub }}</option>
                            @endforeach
                        </select>
                    </label>
                    @if(request('search') || request('subject') || request('class_name'))
                        <a href="{{ route('dashboard') }}" class="pressable inline-flex min-h-11 items-center justify-center rounded-sm border border-stone-300 px-4 text-sm font-bold text-stone-600 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400 dark:hover:border-rose-400 dark:hover:text-rose-400">Reset</a>
                    @endif
                    <a id="exportLink" href="{{ route('tasks.export') }}" class="pressable inline-flex min-h-11 items-center justify-center gap-2 rounded-sm border border-stone-300 px-4 text-sm font-bold text-stone-600 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400 dark:hover:border-rose-400 dark:hover:text-rose-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        Export PDF
                    </a>
                </form>
            </section>

            <section aria-labelledby="agenda-title" class="anim-in mt-9" style="animation-delay: 170ms">
                <div class="mb-5 flex items-end justify-between gap-4 border-b border-stone-300 pb-3 dark:border-white/10">
                    <div>
                        <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Daftar catatan</p>
                        <h2 id="agenda-title" class="font-display mt-1 text-2xl font-extrabold">Urutan deadline</h2>
                    </div>
                    <span class="font-mono text-[9px] uppercase tracking-[.16em] txt-muted">Terdekat lebih dulu</span>
                </div>

                @if($tasks->isEmpty())
                    <div class="paper-sheet mx-auto max-w-xl rounded-sm px-8 py-16 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-stone-300 txt-muted dark:border-zinc-700">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        </div>
                        <p class="font-display mt-5 text-2xl font-bold">Belum ada catatan tugas.</p>
                        <p class="mt-2 text-sm txt-label">Tugas yang ditambahkan pengurus kelas akan muncul di sini.</p>
                    </div>
                @else
                    <div id="taskTimeline" class="relative">
                        <div class="absolute bottom-5 left-[11px] top-3 w-px bg-stone-300 dark:bg-white/10 sm:left-[15px]"></div>
                        @foreach($tasks as $task)
                            @php
                                $taskStatus = $taskStatuses[$task->id] ?? null;
                                $completed = $taskStatus !== null;
                                $isPending = $taskStatus === 'pending';
                                $isConfirmed = $taskStatus === 'confirmed';
                            @endphp
                            <article class="task-card-item relative pb-6 pl-8 sm:pl-11"
                                 data-deadline="{{ $task->deadline->toIso8601String() }}"
                                 data-completed="{{ $completed ? '1' : '0' }}"
                                 data-urgency="{{ $completed ? '' : 'green' }}"
                                 data-task-id="{{ $task->id }}">
                                <span class="timeline-dot absolute left-[7px] top-6 h-2.5 w-2.5 rounded-full sm:left-[11px]"></span>

                                <div class="paper-sheet task-sheet rounded-sm px-4 pb-4 pt-4 sm:px-5 sm:pb-5">
                                    <div class="flex flex-col gap-3 border-b border-dashed border-stone-300 pb-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex min-w-0 items-center gap-2">
                                            <span class="font-mono text-[9px] font-bold uppercase tracking-[.18em] {{ strtolower($task->class_name) === strtolower($ownClass) ? 'text-[#be1836] dark:text-rose-400' : 'txt-label' }}">{{ $task->class_name }}</span>
                                            <span class="text-stone-300 dark:text-zinc-700">/</span>
                                            <span class="truncate font-mono text-[9px] uppercase tracking-[.14em] txt-label">{{ $task->subject }}</span>
                                        </div>
                                        @if($isConfirmed)
                                            <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-sm border border-emerald-500/50 bg-emerald-500/10 px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>Terverifikasi
                                            </span>
                                        @elseif($isPending)
                                            <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-sm border border-amber-500/50 bg-amber-500/10 px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Menunggu Verifikasi
                                            </span>
                                        @else
                                            <span class="tl-count w-fit shrink-0 rounded-sm border px-2.5 py-1 font-mono text-[10px] font-bold">--</span>
                                        @endif
                                    </div>

                                    <div class="py-4">
                                        <h3 class="font-display text-xl font-extrabold leading-tight tracking-tight {{ $completed ? 'line-through txt-muted' : '' }}">{{ $task->title }}</h3>
                                        @if($task->description)
                                            <p class="mt-2 text-sm leading-6 text-stone-600 dark:text-zinc-400 {{ $completed ? 'line-through opacity-70' : '' }}">{{ Str::limit($task->description, 90) }}</p>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-3 border-t border-stone-200 pt-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                                        <div class="flex items-center gap-2 txt-label">
                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                            <span class="dl-label font-mono text-[10px] font-medium tabular-nums {{ $completed ? 'line-through' : '' }}" data-iso="{{ $task->deadline->toIso8601String() }}">{{ $task->deadline->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="grid grid-cols-3 gap-1 sm:flex sm:justify-end">
                                            @if($isConfirmed)
                                                <span class="inline-flex min-h-11 items-center px-3 text-xs font-bold text-emerald-600 dark:text-emerald-400">Selesai ✓</span>
                                            @elseif($isPending)
                                                <form method="POST" action="{{ route('tasks.complete', $task) }}">
                                                    @csrf
                                                    <button type="submit" class="pressable min-h-11 w-full rounded-sm px-3 text-xs font-bold text-amber-600 hover:bg-amber-500/10 dark:text-amber-400">Batal</button>
                                                </form>
                                            @else
                                                <button type="button" onclick="openProofModal({{ $task->id }}, @js($task->title))" class="pressable min-h-11 w-full rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-[#be1836] dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-rose-400">Tandai</button>
                                            @endif
                                            @if(($canEdit && strtolower($task->class_name) === strtolower($ownClass) && !$completed) || $isAdmin)
                                                <button type="button" onclick="startEdit({{ $task->id }}, @js($task->title), @js($task->subject), @js($task->deadline->toIso8601String()), @js($task->description ?? ''), @js($task->class_name))" class="pressable min-h-11 rounded-sm px-3 text-xs font-bold text-stone-600 hover:bg-stone-100 hover:text-[#be1836] dark:text-zinc-400 dark:hover:bg-white/5 dark:hover:text-rose-400">Edit</button>
                                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Hapus tugas ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="pressable min-h-11 w-full rounded-sm px-3 text-xs font-bold txt-label hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400">Hapus</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if(($canEdit && strtolower($task->class_name) === strtolower($ownClass) && !$completed) || $isAdmin)
                                    <div class="edit-panel mt-3" id="edit-row-{{ $task->id }}">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="paper-sheet rounded-sm border-l-4 !border-l-[#be1836] p-4">
                                            @csrf @method('PUT')
                                            <div class="mb-4 flex items-center gap-3">
                                                <span class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Koreksi catatan</span>
                                                <span class="h-px flex-1 bg-stone-200 dark:bg-white/10"></span>
                                            </div>
                                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                                <label><span class="sr-only">Judul tugas</span><input type="text" name="title" id="edit-title-{{ $task->id }}" required placeholder="Judul" class="field w-full rounded-sm px-3 py-2.5 text-sm"></label>
                                                <label><span class="sr-only">Mata pelajaran</span>
                                                    <select name="subject" id="edit-subject-{{ $task->id }}" required class="field select-field w-full rounded-sm px-3 py-2.5 text-sm">
                                                        <option value="">Pilih mapel</option>
                                                        @foreach($subjects as $s)
                                                            <option value="{{ $s }}">{{ $s }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                                <label><span class="sr-only">Deadline</span><input type="datetime-local" name="deadline" id="edit-deadline-{{ $task->id }}" required class="field w-full rounded-sm px-3 py-2.5 text-sm"></label>
                                                <label><span class="sr-only">Deskripsi</span><textarea name="description" id="edit-description-{{ $task->id }}" rows="1" placeholder="Deskripsi (opsional)" class="field w-full rounded-sm px-3 py-2.5 text-sm"></textarea></label>
                                                @if($isAdmin)
                                                    <label class="sm:col-span-2"><span class="sr-only">Kelas</span>
                                                        <select name="class_name" id="edit-class-{{ $task->id }}" class="field select-field w-full rounded-sm px-3 py-2.5 text-sm">
                                                            <option value="">Kelas saat ini</option>
                                                            @foreach(\App\Models\User::classGroups() as $group)
                                                                <optgroup label="{{ $group['label'] }}">
                                                                    @foreach($group['classes'] as $c)
                                                                        <option value="{{ $c }}">{{ $c }}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                    </label>
                                                @endif
                                            </div>
                                            <div class="mt-4 flex gap-2">
                                                <button type="submit" class="pressable min-h-11 rounded-sm bg-[#be1836] px-5 text-xs font-bold text-white hover:bg-[#a30f2b]">Simpan</button>
                                                <button type="button" onclick="cancelEdit({{ $task->id }})" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-xs font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>

                    @if($tasks->hasPages())
                        <div class="mt-6 flex justify-center">{{ $tasks->onEachSide(1)->links() }}</div>
                    @endif
                @endif
            </section>
@endsection

@section('modals')
    @if(Auth::user()->role !== 'siswa')
        <div class="modal-overlay p-4" id="addModal" aria-hidden="true">
            <section class="modal-panel paper-sheet relative w-full max-w-2xl rounded-sm border-t-8 !border-t-[#be1836] px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="addModalTitle">
                <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                    <div>
                        <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Lembar tugas baru</p>
                        <h2 id="addModalTitle" class="font-display mt-1 text-2xl font-extrabold">Tulis ke dalam agenda.</h2>
                    </div>
                    <button type="button" onclick="closeModal()" aria-label="Tutup formulir" class="pressable flex h-11 w-11 shrink-0 items-center justify-center rounded-sm border border-stone-300 txt-label hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('tasks.store') }}" id="addTaskForm">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label for="new-title" class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] txt-label">01 / Judul tugas</label>
                            <input type="text" id="new-title" name="title" required value="{{ old('title') }}" class="field w-full rounded-sm px-4 py-3 text-sm">
                            @error('title') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                        </div>
                        @if(Auth::user()->isPjMapel())
                            <input type="hidden" name="subject" value="{{ Auth::user()->pj_subject }}">
                            <div class="sm:col-span-2">
                                <label class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] txt-label">02 / Mata pelajaran</label>
                                <input type="text" disabled value="{{ Auth::user()->pj_subject }}" class="field w-full rounded-sm px-4 py-3 text-sm opacity-60 cursor-not-allowed">
                                <p class="mt-1 font-mono text-[9px] txt-muted">Mapel otomatis sesuai PJ Anda.</p>
                            </div>
                        @else
                            <div>
                                <label for="new-subject" class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] txt-label">02 / Mata pelajaran</label>
                                <select id="new-subject" name="subject" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                                    <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Pilih mapel</option>
                                    @foreach($subjects as $s)
                                        <option value="{{ $s }}" {{ old('subject') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                @error('subject') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                            </div>
                        @endif
                        <div>
                            <label for="new-deadline" class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] txt-label">03 / Deadline</label>
                            @php $dl = old('deadline'); if ($dl && strlen($dl) > 16) { try { $dl = \Carbon\Carbon::parse($dl)->format('Y-m-d\TH:i'); } catch (\Exception $e) {} } @endphp
                            <input type="datetime-local" id="new-deadline" name="deadline" required value="{{ $dl }}" class="field w-full rounded-sm px-4 py-3 text-sm">
                            @error('deadline') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="new-description" class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] txt-label">04 / Deskripsi <span class="normal-case tracking-normal">(opsional)</span></label>
                            <textarea id="new-description" name="description" rows="2" class="field w-full rounded-sm px-4 py-3 text-sm">{{ old('description') }}</textarea>
                        </div>
                        @if(Auth::user()->role === 'admin')
                            <div class="sm:col-span-2">
                                <label for="new-class" class="font-mono mb-2 block text-[9px] font-bold uppercase tracking-[.18em] txt-label">05 / Kelas</label>
                                <select id="new-class" name="class_name" required class="field w-full rounded-sm px-4 py-3 text-sm">
                                    <option value="" disabled {{ old('class_name') ? '' : 'selected' }}>Pilih kelas</option>
                                    @foreach(\App\Models\User::classGroups() as $group)
                                        <optgroup label="{{ $group['label'] }}">
                                            @foreach($group['classes'] as $c)
                                                <option value="{{ $c }}" {{ old('class_name') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('class_name') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                    <div class="mt-7 flex flex-col-reverse gap-3 border-t border-stone-200 pt-5 dark:border-white/10 sm:flex-row sm:justify-end">
                        <button type="button" onclick="closeModal()" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-sm font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                        <button type="submit" class="primary-button pressable min-h-11 rounded-sm bg-[#be1836] px-7 text-sm font-bold text-white hover:bg-[#a30f2b]">Simpan Tugas</button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    <div class="modal-overlay p-4" id="proofModal" aria-hidden="true">
        <section class="modal-panel paper-sheet relative w-full max-w-lg rounded-sm border-t-8 !border-t-amber-500 px-5 pb-6 pt-5 sm:px-7" role="dialog" aria-modal="true" aria-labelledby="proofModalTitle">
            <div class="mb-6 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-4 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-amber-600 dark:text-amber-400">Upload bukti</p>
                    <h2 id="proofModalTitle" class="font-display mt-1 text-2xl font-extrabold">Lampirkan foto bukti.</h2>
                </div>
                <button type="button" onclick="closeProofModal()" aria-label="Tutup" class="pressable flex h-11 w-11 shrink-0 items-center justify-center rounded-sm border border-stone-300 txt-label hover:border-amber-500 hover:text-amber-600 dark:border-white/15 dark:text-zinc-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <p class="mb-4 text-sm text-stone-600 dark:text-zinc-400">Tugas: <span class="font-bold" id="proofTaskTitle"></span></p>
            <form method="POST" action="" id="proofForm" enctype="multipart/form-data">
                @csrf
                <div id="proofDropZone" class="relative rounded-sm border-2 border-dashed border-stone-300 bg-stone-50/50 p-8 text-center transition-colors hover:border-amber-400 hover:bg-amber-50/30 dark:border-zinc-600 dark:bg-zinc-800/50 dark:hover:border-amber-500/50 dark:hover:bg-amber-950/20">
                    <input type="file" name="photos[]" id="proofInput" multiple accept="image/*" class="absolute inset-0 cursor-pointer opacity-0">
                    <svg class="mx-auto h-10 w-10 txt-muted" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                    <p class="mt-2 text-sm font-semibold text-stone-600 dark:text-zinc-300">Klik atau seret foto ke sini</p>
                    <p class="mt-1 font-mono text-[9px] txt-muted">JPG, PNG, WEBP • Dikompres otomatis maks 2MB</p>
                </div>
                <div id="proofPreview" class="mt-4 grid grid-cols-3 gap-2"></div>
                <p id="proofError" class="mt-2 hidden text-xs font-medium text-red-600 dark:text-rose-400"></p>
                <div class="mt-6 flex flex-col-reverse gap-3 border-t border-stone-200 pt-5 dark:border-white/10 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeProofModal()" class="pressable min-h-11 rounded-sm border border-stone-300 px-5 text-sm font-bold text-stone-600 hover:border-stone-500 dark:border-white/15 dark:text-zinc-400">Batal</button>
                    <button type="submit" class="primary-button pressable min-h-11 rounded-sm bg-amber-600 px-7 text-sm font-bold text-white hover:bg-amber-700">Kirim Bukti</button>
                </div>
            </form>
        </section>
    </div>
@endsection
