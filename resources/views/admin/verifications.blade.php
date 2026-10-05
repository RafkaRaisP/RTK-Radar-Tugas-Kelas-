@extends('layouts.dashboard')

@section('title', 'Verifikasi')

@section('content')
    <header class="anim-in grid items-end gap-6 border-b-2 border-stone-800/80 pb-7 dark:border-zinc-100/80 sm:grid-cols-[1fr_auto]">
        <div>
            <div class="mb-3 flex items-center gap-3">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[.22em] text-[#be1836] dark:text-rose-400">Panel admin / 04</span>
                <span class="h-px w-12 bg-stone-300 dark:bg-white/10"></span>
                <span class="font-mono text-[10px] text-stone-400">{{ $items->total() }} catatan</span>
            </div>
            <h1 class="font-display text-4xl font-extrabold leading-none tracking-[-.035em] sm:text-5xl">Verifikasi & bukti.</h1>
            <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-zinc-400">
                <span>Pantau siapa yang selesai</span>
                <span aria-hidden="true">/</span>
                <span class="font-bold text-[#be1836] dark:text-rose-400">Konfirmasi & foto</span>
            </div>
        </div>
    </header>

    <section class="anim-in mt-7">
        <form method="GET" action="{{ route('admin.verifications') }}" class="paper-sheet grid gap-3 rounded-sm p-3 sm:grid-cols-2 lg:grid-cols-[auto_auto_auto]">
            <nav class="flex gap-1 rounded-sm bg-stone-100 p-1 dark:bg-zinc-800 sm:col-span-2 lg:col-span-1">
                @foreach(['pending' => 'Pending', 'confirmed' => 'Terverifikasi', 'all' => 'Semua'] as $val => $label)
                    <a href="{{ route('admin.verifications', array_merge(request()->query(), ['status' => $val])) }}"
                       class="flex-1 rounded-sm px-4 py-2 text-center text-xs font-bold transition-colors {{ $status === $val ? 'bg-[#be1836] text-white' : 'text-stone-600 hover:bg-white dark:text-zinc-400 dark:hover:bg-zinc-700' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>
            <label>
                <span class="sr-only">Filter kelas</span>
                <select name="class_name" class="field select-field min-h-11 w-full rounded-sm px-4 py-2.5 text-sm lg:min-w-[160px]">
                    <option value="">Semua Kelas</option>
                    @foreach($classOptions as $c)
                        <option value="{{ $c }}" {{ request('class_name') === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex gap-2">
                @if(request('class_name'))
                    <a href="{{ route('admin.verifications', ['status' => $status]) }}" class="pressable inline-flex min-h-11 flex-1 items-center justify-center rounded-sm border border-stone-300 px-4 text-sm font-bold text-stone-600 hover:border-[#be1836] hover:text-[#be1836] dark:border-white/15 dark:text-zinc-400">Reset</a>
                @endif
                <button type="submit" class="pressable inline-flex min-h-11 flex-1 items-center justify-center rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b]">Cari</button>
            </div>
        </form>
    </section>

    <section class="anim-in mt-7 space-y-3" style="animation-delay: 80ms">
        @if($items->isEmpty())
            <div class="paper-sheet mx-auto max-w-xl rounded-sm px-8 py-16 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border-2 border-dashed border-stone-300 text-stone-400 dark:border-zinc-700 dark:text-zinc-500">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="font-display mt-5 text-2xl font-bold">Tidak ada catatan.</p>
                <p class="mt-2 text-sm text-stone-500 dark:text-zinc-400">Tidak ada data pada tab & filter ini.</p>
            </div>
        @else
            @foreach($items as $item)
                @php
                    $isConfirmed = $item->status === 'confirmed';
                    $photos = $photosByTask[$item->task_id][$item->user_id] ?? [];
                @endphp
                <article class="paper-sheet rounded-sm border-l-4 {{ $isConfirmed ? '!border-l-emerald-500 opacity-75' : '!border-l-amber-500' }} px-4 py-4 sm:px-5">
                    <div class="flex flex-col gap-3 border-b border-dashed border-stone-300 pb-3 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 flex-wrap items-center gap-2">
                            <span class="font-mono text-[9px] font-bold uppercase tracking-[.18em] text-[#be1836] dark:text-rose-400">{{ $item->class_name }}</span>
                            <span class="text-stone-300 dark:text-zinc-700">/</span>
                            <span class="truncate font-mono text-[9px] uppercase tracking-[.14em] text-stone-500 dark:text-zinc-400">{{ $item->subject }}</span>
                        </div>
                        <span class="inline-flex w-fit shrink-0 items-center gap-1.5 rounded-sm border px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wider {{ $isConfirmed ? 'border-emerald-500/50 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'border-amber-500/50 bg-amber-500/10 text-amber-600 dark:text-amber-400' }}">
                            {{ $isConfirmed ? 'Terverifikasi' : 'Pending' }}
                        </span>
                    </div>

                    <div class="py-3">
                        <h3 class="font-display text-lg font-extrabold leading-tight tracking-tight {{ $isConfirmed ? 'line-through text-stone-400 dark:text-zinc-500' : '' }}">{{ $item->task_title }}</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-stone-600 dark:text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                <span class="font-bold">{{ $item->user_name }}</span>
                                <span class="font-mono text-[10px] text-stone-400">{{ $item->user_username }}</span>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                <span class="font-mono text-[10px] tabular-nums">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}</span>
                            </span>
                            @if($isConfirmed && $item->confirmer_name)
                                <span class="font-mono text-[10px] text-emerald-600 dark:text-emerald-400">✓ oleh {{ $item->confirmer_name }}</span>
                            @endif
                        </div>
                    </div>

                    @if(!empty($photos))
                        <div class="mt-2 mb-3 flex gap-2 overflow-x-auto pb-1">
                            @foreach($photos as $photo)
                                <a href="{{ $photo->url }}" target="_blank" class="block shrink-0">
                                    <img src="{{ $photo->url }}" alt="Bukti tugas" class="h-20 w-20 rounded-sm border border-stone-200 object-cover transition-transform hover:scale-105 dark:border-zinc-700">
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="mb-3 text-xs text-stone-400 dark:text-zinc-500">Tidak ada foto bukti.</p>
                    @endif

                    <div class="flex flex-wrap gap-2 border-t border-stone-200 pt-3 dark:border-white/10">
                        @if(!$isConfirmed)
                            <form method="POST" action="{{ route('admin.confirm', $item->task_id) }}">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $item->user_id }}">
                                <button type="submit" class="primary-button pressable inline-flex min-h-9 items-center gap-2 rounded-sm bg-emerald-600 px-4 text-xs font-bold text-white hover:bg-emerald-700">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Konfirmasi
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.unconfirm', $item->task_id) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="user_id" value="{{ $item->user_id }}">
                                <button type="submit" class="pressable inline-flex min-h-9 items-center gap-2 rounded-sm border border-amber-500/50 px-4 text-xs font-bold text-amber-600 hover:bg-amber-500/10 dark:text-amber-400">Batalkan</button>
                            </form>
                        @endif
                        <span class="self-center font-mono text-[10px] text-stone-400">{{ $item->task_id !== null ? 'Deadline: '.\Carbon\Carbon::parse($item->deadline)->format('d M Y') : '' }}</span>
                    </div>
                </article>
            @endforeach

            @if($items->hasPages())
                <div class="mt-6 flex justify-center">{{ $items->onEachSide(1)->links() }}</div>
            @endif
        @endif
    </section>
@endsection
