@extends('layouts.dashboard')

@section('title', 'Verifikasi PJ - Radar Tugas Kelas')
@section('meta-description', 'Dashboard verifikasi tugas untuk PJ Mapel.')

@section('content')
    <header class="anim-in grid items-end gap-6 border-b-2 border-stone-800/80 pb-7 dark:border-zinc-100/80 sm:grid-cols-[1fr_auto]">
        <div>
            <div class="mb-3 flex items-center gap-3">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[.22em] text-[#be1836] dark:text-rose-400">Panel verifikasi</span>
                <span class="h-px w-12 bg-stone-300 dark:bg-white/10"></span>
                <span class="font-mono text-[10px] text-stone-400">{{ now()->format('d.m.Y') }}</span>
            </div>
            <h1 class="font-display text-4xl font-extrabold leading-none tracking-[-.035em] sm:text-5xl">Verifikasi {{ Auth::user()->pj_subject }}.</h1>
            <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-zinc-400">
                <span>{{ Auth::user()->class_name }}</span>
                <span aria-hidden="true">/</span>
                <span class="font-bold text-[#be1836] dark:text-rose-400">PJ Mapel</span>
            </div>
        </div>
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

    {{-- Ringkasan --}}
    <section aria-label="Ringkasan verifikasi" class="anim-in mt-7 grid grid-cols-2 gap-3 lg:grid-cols-3" style="animation-delay: 70ms">
        <article class="index-card rounded-sm border-t-4 !border-t-amber-500 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">01 / Pending</p>
            <p class="font-display mt-1 text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ $tasks->total() }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-emerald-500 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">02 / Terverifikasi</p>
            <p class="font-display mt-1 text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $confirmedTasks->total() }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-[#be1836] px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">03 / Total</p>
            <p class="font-display mt-1 text-3xl font-extrabold">{{ $tasks->total() + $confirmedTasks->total() }}</p>
        </article>
    </section>

    {{-- Daftar Pending --}}
    <section class="anim-in mt-9" style="animation-delay: 120ms">
        <div class="mb-5 flex items-end justify-between gap-4 border-b border-stone-300 pb-3 dark:border-white/10">
            <div>
                <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-amber-600 dark:text-amber-400">Menunggu verifikasi</p>
                <h2 class="font-display mt-1 text-2xl font-extrabold">Perlu Dikonfirmasi</h2>
            </div>
            <span class="font-mono text-[9px] uppercase tracking-[.16em] text-stone-400">{{ $tasks->total() }} tugas</span>
        </div>

        @if($tasks->isEmpty())
            <div class="paper-sheet mx-auto max-w-xl rounded-sm px-8 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-stone-300 text-stone-400 dark:border-zinc-700 dark:text-zinc-500">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="font-display mt-5 text-2xl font-bold">Semua sudah terverifikasi.</p>
                <p class="mt-2 text-sm text-stone-500 dark:text-zinc-400">Tidak ada tugas yang perlu dikonfirmasi saat ini.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($tasks as $task)
                    @foreach($task->completedBy as $siswa)
                        <article class="paper-sheet rounded-sm px-4 pb-4 pt-4 sm:px-5 sm:pb-5">
                            <div class="flex flex-col gap-3 border-b border-dashed border-stone-300 pb-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="font-mono text-[9px] font-bold uppercase tracking-[.18em] text-[#be1836] dark:text-rose-400">{{ $task->class_name }}</span>
                                    <span class="text-stone-300 dark:text-zinc-700">/</span>
                                    <span class="truncate font-mono text-[9px] uppercase tracking-[.14em] text-stone-500 dark:text-zinc-400">{{ $task->subject }}</span>
                                </div>
                                <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-sm border border-amber-500/50 bg-amber-500/10 px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Menunggu
                                </span>
                            </div>

                            <div class="py-4">
                                <h3 class="font-display text-xl font-extrabold leading-tight tracking-tight">{{ $task->title }}</h3>
                                @if($task->description)
                                    <p class="mt-2 text-sm leading-6 text-stone-600 dark:text-zinc-400">{{ Str::limit($task->description, 90) }}</p>
                                @endif
                            </div>

                            <div class="flex flex-col gap-3 border-t border-stone-200 pt-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2 text-stone-500 dark:text-zinc-400">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                        <span class="font-mono text-xs font-medium">{{ $siswa->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-stone-500 dark:text-zinc-400">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                        <span class="font-mono text-[10px] font-medium tabular-nums">{{ \Carbon\Carbon::parse($siswa->pivot->created_at)->format('d M Y, H:i') }}</span>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('pj.confirm', $task) }}" onsubmit="return confirm('Konfirmasi tugas ini sebagai selesai?')">
                                    @csrf
                                    <button type="submit" class="primary-button pressable inline-flex min-h-11 items-center gap-2 rounded-sm bg-emerald-600 px-5 text-sm font-bold text-white hover:bg-emerald-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        Konfirmasi
                                    </button>
                                </form>
                            </div>

                            @php
                                $taskPhotos = $task->proofPhotos->where('user_id', $siswa->id);
                            @endphp
                            @if($taskPhotos->isNotEmpty())
                                <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
                                    @foreach($taskPhotos as $photo)
                                        <a href="{{ $photo->url }}" target="_blank" class="block shrink-0">
                                            <img src="{{ $photo->url }}" alt="Bukti tugas" class="h-20 w-20 rounded-sm border border-stone-200 object-cover transition-transform hover:scale-105 dark:border-zinc-700">
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-3 flex items-center gap-2 text-xs text-stone-400 dark:text-zinc-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    Tidak ada bukti foto
                                </div>
                            @endif
                        </article>
                    @endforeach
                @endforeach
            </div>

            @if($tasks->hasPages())
                <div class="mt-6 flex justify-center">{{ $tasks->onEachSide(1)->links() }}</div>
            @endif
        @endif
    </section>

    {{-- Daftar Sudah Dikonfirmasi --}}
    @if($confirmedTasks->isNotEmpty())
        <section class="anim-in mt-12" style="animation-delay: 170ms">
            <div class="mb-5 flex items-end justify-between gap-4 border-b border-stone-300 pb-3 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-emerald-600 dark:text-emerald-400">Sudah dikonfirmasi</p>
                    <h2 class="font-display mt-1 text-2xl font-extrabold">Riwayat Verifikasi</h2>
                </div>
                <span class="font-mono text-[9px] uppercase tracking-[.16em] text-stone-400">{{ $confirmedTasks->total() }} tugas</span>
            </div>

            <div class="space-y-4">
                @foreach($confirmedTasks as $task)
                    @foreach($task->completedBy as $siswa)
                        <article class="paper-sheet rounded-sm px-4 pb-4 pt-4 sm:px-5 sm:pb-5 opacity-70">
                            <div class="flex flex-col gap-3 border-b border-dashed border-stone-300 pb-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="font-mono text-[9px] font-bold uppercase tracking-[.18em] text-stone-500 dark:text-zinc-400">{{ $task->class_name }}</span>
                                    <span class="text-stone-300 dark:text-zinc-700">/</span>
                                    <span class="truncate font-mono text-[9px] uppercase tracking-[.14em] text-stone-500 dark:text-zinc-400">{{ $task->subject }}</span>
                                </div>
                                <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-sm border border-emerald-500/50 bg-emerald-500/10 px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>Terverifikasi
                                </span>
                            </div>
                            <div class="py-3">
                                <h3 class="font-display text-lg font-extrabold leading-tight tracking-tight line-through text-stone-400 dark:text-zinc-500">{{ $task->title }}</h3>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-stone-500 dark:text-zinc-400">
                                <span>{{ $siswa->name }}</span>
                                <span>•</span>
                                <span>{{ \Carbon\Carbon::parse($siswa->pivot->created_at)->format('d M Y, H:i') }}</span>
                            </div>
                        </article>
                    @endforeach
                @endforeach
            </div>

            @if($confirmedTasks->hasPages())
                <div class="mt-6 flex justify-center">{{ $confirmedTasks->onEachSide(1)->links() }}</div>
            @endif
        </section>
    @endif
@endsection