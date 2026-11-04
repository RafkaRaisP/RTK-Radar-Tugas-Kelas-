@extends('layouts.dashboard')

@section('title', 'Ringkasan')

@section('content')
    <header class="anim-in grid items-end gap-6 border-b-2 border-stone-800/80 pb-7 dark:border-zinc-100/80 sm:grid-cols-[1fr_auto]">
        <div>
            <div class="mb-3 flex items-center gap-3">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[.22em] text-[#be1836] dark:text-rose-400">Panel admin</span>
                <span class="h-px w-12 bg-stone-300 dark:bg-white/10"></span>
                <span class="font-mono text-[10px] txt-muted">{{ now()->format('d.m.Y') }}</span>
            </div>
            <h1 class="font-display text-4xl font-extrabold leading-none tracking-[-.035em] sm:text-5xl">Ringkasan kelas.</h1>
            <div class="mt-3 flex items-center gap-2 text-sm text-stone-600 dark:text-zinc-400">
                <span>Seluruh data</span>
                <span aria-hidden="true">/</span>
                <span class="font-bold text-[#be1836] dark:text-rose-400">Administrator</span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users') }}" class="primary-button pressable inline-flex min-h-12 items-center justify-center gap-2 rounded-sm bg-[#be1836] px-5 text-sm font-bold text-white hover:bg-[#a30f2b]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
                Kelola User
            </a>
        </div>
    </header>

    <section aria-label="Ringkasan user" class="anim-in mt-7 grid grid-cols-2 gap-3 lg:grid-cols-5" style="animation-delay: 60ms">
        <article class="index-card rounded-sm border-t-4 !border-t-[#be1836] px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">01 / Total user</p>
            <p class="font-display mt-1 text-3xl font-extrabold">{{ $userCount }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-amber-500 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">02 / Pengurus</p>
            <p class="font-display mt-1 text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ ($roleCounts['ketua_kelas'] ?? 0) + ($roleCounts['sekretaris'] ?? 0) }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-emerald-500 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">03 / PJ Mapel</p>
            <p class="font-display mt-1 text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $roleCounts['pj_mapel'] ?? 0 }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-sky-500 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">04 / Siswa</p>
            <p class="font-display mt-1 text-3xl font-extrabold text-sky-600 dark:text-sky-400">{{ $roleCounts['siswa'] ?? 0 }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-stone-400 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">05 / Admin</p>
            <p class="font-display mt-1 text-3xl font-extrabold txt-label">{{ $roleCounts['admin'] ?? 0 }}</p>
        </article>
    </section>

    <section aria-label="Ringkasan tugas" class="anim-in mt-7 grid grid-cols-2 gap-3 lg:grid-cols-5" style="animation-delay: 110ms">
        <article class="index-card rounded-sm border-t-4 !border-t-[#be1836] px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">Total tugas</p>
            <p class="font-display mt-1 text-3xl font-extrabold">{{ $taskCount }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-emerald-500 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">Masih aktif</p>
            <p class="font-display mt-1 text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $taskActive }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-red-500 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">Terlewat</p>
            <p class="font-display mt-1 text-3xl font-extrabold text-red-600 dark:text-red-400">{{ $taskExpired }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-amber-500 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">Pending</p>
            <p class="font-display mt-1 text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ $pending }}</p>
        </article>
        <article class="index-card rounded-sm border-t-4 !border-t-stone-400 px-4 pb-5 pt-4">
            <p class="font-mono text-[9px] font-bold uppercase tracking-[.18em] txt-label">Belum dikerjakan</p>
            <p class="font-display mt-1 text-3xl font-extrabold txt-label">{{ $unworked }}</p>
        </article>
    </section>

    <div class="anim-in mt-9 grid gap-6 lg:grid-cols-2" style="animation-delay: 160ms">
        <section class="paper-sheet rounded-sm p-5">
            <div class="mb-4 flex items-end justify-between border-b border-stone-300 pb-3 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Distribusi</p>
                    <h2 class="font-display mt-1 text-2xl font-extrabold">User per kelas</h2>
                </div>
            </div>
            @if($classCounts->isEmpty())
                <p class="text-sm txt-label">Belum ada data.</p>
            @else
                <ul class="space-y-3">
                    @foreach($classCounts as $class => $total)
                        <li class="flex items-center gap-3">
                            <span class="w-40 shrink-0 truncate font-mono text-[10px] uppercase tracking-wider text-stone-600 dark:text-zinc-300">{{ $class }}</span>
                            <span class="h-2 flex-1 overflow-hidden rounded-full bg-stone-200 dark:bg-zinc-700">
                                <span class="block h-full rounded-full bg-[#be1836]" style="width: {{ $userCount ? round($total / $userCount * 100) : 0 }}%"></span>
                            </span>
                            <span class="w-8 text-right font-mono text-xs font-bold">{{ $total }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="paper-sheet rounded-sm p-5">
            <div class="mb-4 flex items-end justify-between border-b border-stone-300 pb-3 dark:border-white/10">
                <div>
                    <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Distribusi</p>
                    <h2 class="font-display mt-1 text-2xl font-extrabold">Tugas per kelas</h2>
                </div>
            </div>
            @if($taskByClass->isEmpty())
                <p class="text-sm txt-label">Belum ada data.</p>
            @else
                <ul class="space-y-3">
                    @foreach($taskByClass as $class => $total)
                        <li class="flex items-center gap-3">
                            <span class="w-40 shrink-0 truncate font-mono text-[10px] uppercase tracking-wider text-stone-600 dark:text-zinc-300">{{ $class }}</span>
                            <span class="h-2 flex-1 overflow-hidden rounded-full bg-stone-200 dark:bg-zinc-700">
                                <span class="block h-full rounded-full bg-[#be1836]" style="width: {{ $taskCount ? round($total / $taskCount * 100) : 0 }}%"></span>
                            </span>
                            <span class="w-8 text-right font-mono text-xs font-bold">{{ $total }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    <section class="anim-in mt-9 paper-sheet rounded-sm p-5" style="animation-delay: 200ms">
        <div class="mb-4 flex items-end justify-between border-b border-stone-300 pb-3 dark:border-white/10">
            <div>
                <p class="font-mono text-[9px] font-bold uppercase tracking-[.2em] text-[#be1836] dark:text-rose-400">Gambaran</p>
                <h2 class="font-display mt-1 text-2xl font-extrabold">Mapel x Kelas</h2>
            </div>
            <span class="font-mono text-[9px] uppercase tracking-[.16em] txt-muted">jumlah tugas</span>
        </div>
        @if($subjectByClass->isEmpty())
            <p class="text-sm txt-label">Belum ada data tugas.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead>
                        <tr class="border-b border-stone-300 dark:border-white/10 font-mono text-[10px] uppercase tracking-wider txt-label">
                            <th class="py-2 pr-4 font-bold">Kelas</th>
                            @foreach($subjectNames as $sn)
                                <th class="px-2 py-2 font-bold">{{ $sn }}</th>
                            @endforeach
                            <th class="py-2 pl-2 font-bold text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $byClass = $subjectByClass->groupBy('class_name'); @endphp
                        @foreach($byClass as $cls => $rows)
                            <tr class="border-b border-dashed border-stone-200 dark:border-white/5">
                                <td class="py-3 pr-4 font-mono text-[11px] font-bold uppercase tracking-wider text-stone-700 dark:text-zinc-200">{{ $cls }}</td>
                                @foreach($subjectNames as $sn)
                                    @php $row = $rows->firstWhere('subject', $sn); @endphp
                                    <td class="px-2 py-3 text-stone-600 dark:text-zinc-400">{{ $row ? $row->total : '—' }}</td>
                                @endforeach
                                <td class="py-3 pl-2 text-right font-bold">{{ $rows->sum('total') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
