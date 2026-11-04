@extends('layouts.app')

@section('title', 'Radar Tugas Kelas')
@section('meta-description', 'Pantau deadline tugas sekolah dari semua kelas secara real-time.')
@section('bodyClass', 'notebook-bg')

@section('body')
    <header class="cover-bar border-b-4 border-[#760b20] text-white shadow-lg shadow-red-950/10">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('radar') }}" class="flex min-w-0 items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-sm border border-white/25 bg-white/10">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-8 object-contain brightness-0 invert">
                </span>
                <span class="min-w-0">
                    <span class="font-display block truncate text-lg font-extrabold leading-none sm:text-xl">Radar Tugas Kelas</span>
                    <span class="font-mono mt-1 hidden text-[9px] uppercase tracking-[.24em] text-red-100 sm:block">Agenda deadline sekolah</span>
                </span>
            </a>

            <nav aria-label="Navigasi utama" class="flex shrink-0 items-center gap-2">
                <button id="theme-toggle" type="button" aria-label="Ganti tema" title="Ganti tema"
                    class="pressable flex h-11 w-11 items-center justify-center rounded-sm border border-white/25 bg-black/10 hover:bg-white/15">
                    <svg id="icon-moon" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                    <svg id="icon-sun" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                </button>
                @auth
                    <a href="{{ route('dashboard') }}" class="pressable hidden min-h-11 items-center rounded-sm bg-white px-4 text-sm font-bold text-[#a30f2b] hover:bg-red-50 sm:inline-flex">Dashboard</a>
                    <a href="{{ route('dashboard') }}" aria-label="Dashboard" class="pressable flex h-11 w-11 items-center justify-center rounded-sm bg-white text-[#a30f2b] sm:hidden">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 15.75a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 15.75V18A2.25 2.25 0 0118 20.25h-2.25a2.25 2.25 0 01-2.25-2.25v-2.25z"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="pressable inline-flex min-h-11 items-center rounded-sm bg-white px-4 text-sm font-bold text-[#a30f2b] hover:bg-red-50">Masuk</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-5 pb-20 pt-10 sm:px-8 sm:pt-14">
        <section class="anim-in grid items-end gap-8 border-b-2 border-stone-800/80 pb-8 dark:border-zinc-100/80 lg:grid-cols-[1fr_auto]">
            <div class="max-w-3xl">
                <div class="mb-4 flex items-center gap-3">
                    <span class="rounded-sm border-2 border-[#be1836] px-3 py-1 font-mono text-[10px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:border-rose-400 dark:text-rose-400">Terbuka untuk umum</span>
                    <span class="font-mono text-[10px] uppercase tracking-[.18em] txt-label">Diperbarui tiap detik</span>
                </div>
                <h1 class="font-display text-5xl font-extrabold leading-[.9] tracking-[-.04em] sm:text-6xl lg:text-7xl">Agenda tugas<br><span class="text-[#be1836] dark:text-rose-400">semua kelas.</span></h1>
                <p class="mt-5 max-w-xl text-sm leading-6 text-stone-600 dark:text-zinc-400 sm:text-base">Baca deadline seperti membuka buku agenda. Hijau masih aman, kuning mulai dekat, dan merah perlu dikerjakan sekarang.</p>
            </div>

            <div class="paper-sheet min-w-[220px] rounded-sm py-5 pl-12 pr-5">
                <p class="font-mono text-[10px] font-bold uppercase tracking-[.2em] txt-label">Catatan terdaftar</p>
                <p class="font-display mt-1 text-4xl font-extrabold leading-none">{{ $tasks->total() }}</p>
                <p class="mt-2 text-xs txt-label">tugas dalam radar</p>
            </div>
        </section>

        <section aria-label="Filter tugas" class="anim-in mt-7 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between" style="animation-delay: 80ms">
            <div class="flex items-center gap-2">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[.2em] txt-label">Saring agenda</span>
                <span class="hidden h-px w-12 bg-stone-300 dark:bg-white/10 sm:block"></span>
            </div>
            <div class="relative w-full sm:w-auto">
                <label for="classFilter" class="sr-only">Pilih kelas</label>
                <select id="classFilter" class="field min-h-11 w-full appearance-none rounded-sm py-2.5 pl-4 pr-11 text-sm font-semibold sm:min-w-[210px]">
                    <option value="">Semua Kelas</option>
                    @foreach(\App\Models\User::classGroups() as $group)
                        <optgroup label="{{ $group['label'] }}">
                            @foreach($group['classes'] as $class)
                                <option value="{{ $class }}" {{ request('class_name') === $class ? 'selected' : '' }}>{{ $class }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-4 top-1/2 h-4 w-4 -translate-y-1/2 txt-label" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
            </div>
        </section>

        @if($tasks->isEmpty())
            <section class="paper-sheet anim-in mx-auto mt-12 max-w-xl rounded-sm px-8 py-16 text-center" style="animation-delay: 140ms">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-stone-300 txt-muted dark:border-zinc-700">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="font-display mt-5 text-2xl font-bold">Halaman ini masih kosong.</p>
                <p class="mt-2 text-sm txt-label">Tugas yang ditambahkan pengurus kelas akan muncul di sini.</p>
            </section>
        @else
            <section id="taskGrid" aria-label="Daftar tugas" class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                @foreach($tasks as $task)
                    <article class="paper-sheet task-card task-card-item rounded-sm px-5 pb-5 pt-4"
                         data-deadline="{{ $task->deadline->toIso8601String() }}"
                         data-task-id="{{ $task->id }}"
                         data-urgency="green">
                        <div class="mb-5 flex items-start justify-between gap-4 border-b border-dashed border-stone-300 pb-3 dark:border-white/10">
                            <div class="flex min-w-0 items-center gap-2.5">
                                <span class="status-dot h-2 w-2 shrink-0 rounded-full" aria-hidden="true"></span>
                                <span class="truncate font-mono text-[10px] font-bold uppercase tracking-[.16em] txt-label">{{ $task->class_name }}</span>
                            </div>
                            <span class="urgency-stamp cd-count shrink-0 rounded-sm border px-2 py-1 font-mono text-[10px] font-bold">--</span>
                        </div>

                        <div class="pl-5">
                            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] text-[#be1836] dark:text-rose-400">{{ $task->subject }}</p>
                            <h2 class="font-display mt-1.5 text-xl font-extrabold leading-tight tracking-tight">{{ $task->title }}</h2>
                            @if($task->description)
                                <p class="mt-3 text-sm leading-6 text-stone-600 dark:text-zinc-400">{{ Str::limit($task->description, 72) }}</p>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center justify-between gap-3 border-t border-stone-200 pt-3 pl-5 dark:border-white/10">
                            <span class="font-mono text-[9px] uppercase tracking-[.18em] txt-muted">Tenggat</span>
                            <span class="dl-label text-right font-mono text-[10px] font-medium tabular-nums text-stone-600 dark:text-zinc-400" data-iso="{{ $task->deadline->toIso8601String() }}">{{ $task->deadline->format('d M Y, H:i') }}</span>
                        </div>
                    </article>
                @endforeach
            </section>

            @if($tasks->hasPages())
                <div class="mt-10 flex justify-center">{{ $tasks->onEachSide(1)->links() }}</div>
            @endif
        @endif
    </main>

    <footer class="border-t border-stone-300 bg-[#eee5d6]/80 dark:border-white/10 dark:bg-zinc-950/80">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-6 sm:px-8">
            <span class="font-mono text-[9px] font-bold uppercase tracking-[.2em] txt-label">Radar Tugas Kelas</span>
            <span class="font-mono text-[9px] uppercase tracking-[.2em] txt-muted">Jangan lewatkan tenggat</span>
        </div>
    </footer>
@endsection

@section('scripts')
    <script>
        document.getElementById('classFilter').addEventListener('change', function () {
            const val = this.value;
            window.location.href = val ? '/radar?class_name=' + encodeURIComponent(val) : '/radar';
        });
    </script>
@endsection