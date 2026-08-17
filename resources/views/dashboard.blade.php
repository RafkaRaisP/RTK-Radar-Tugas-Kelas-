<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Radar Tugas Kelas</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'putih-kertas': '#F9F9F9',
                        'merah-elegan': '#C8102E',
                        'hitam-pekat': '#0D0D0D',
                        'hijau-rimba': '#0B3B24',
                        'hijau-aksen': '#1B5E20',
                    },
                },
            },
        };
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Spectral:wght@600;700;800&family=JetBrains+Mono:wght@400;500;700&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script>
        (function () {
            var stored = null;
            try { stored = localStorage.getItem('rtk-theme'); } catch (e) {}
            if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html { -webkit-tap-highlight-color: transparent; -webkit-text-size-adjust: 100%; }
        body { font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; overscroll-behavior: none; }
        .font-display { font-family: 'Spectral', Georgia, serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .anim-in {
            opacity: 0;
            animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .flash-msg {
            animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* water-drop theme reveal (grow): new theme clipped by a circle expanding from the click point */
        ::view-transition-old(root) { animation: none; }
        ::view-transition-new(root) { animation: rtk-reveal 850ms cubic-bezier(0.65, 0, 0.35, 1); }
        @keyframes rtk-reveal {
            from { clip-path: circle(2px at var(--rtk-x) var(--rtk-y)); }
            to { clip-path: circle(var(--rtk-maxr) at var(--rtk-x) var(--rtk-y)); }
        }

        .tl-card {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .tl-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px -14px rgba(0, 0, 0, 0.2);
        }
        .dark .tl-card:hover {
            box-shadow: 0 10px 30px -14px rgba(0, 0, 0, 0.6);
        }

        .input-field {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.09);
            transition: border-color 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dark .input-field {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .input-field:focus {
            outline: none;
            border-color: rgba(200, 16, 46, 0.5);
            box-shadow: 0 0 0 3px rgba(200, 16, 46, 0.08);
        }

        .btn-primary { transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
        .btn-primary:active { transform: scale(0.98) translateY(1px); }

        .btn-ghost { transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
        .btn-ghost:active { transform: scale(0.98); }

        .modal-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 100;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        .modal-overlay.is-open { display: flex; align-items: center; justify-content: center; }
        .modal-panel { animation: fadeSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        .edit-panel { display: none; animation: slideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .edit-panel.is-open { display: block; }

        /* ===== Timeline rail & dot ===== */
        .tl-dot {
            background: #d4d4d8;
            border: 3px solid #fff;
            box-shadow: 0 0 0 1px #e4e4e7;
        }
        .dark .tl-dot {
            background: #52525b;
            border-color: #18181b;
            box-shadow: 0 0 0 1px #3f3f46;
        }
        .task-card-item[data-urgency="green"] .tl-dot { background: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25); }
        .task-card-item[data-urgency="yellow"] .tl-dot { background: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25); }
        .task-card-item[data-urgency="red"] .tl-dot { background: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.3); }
        .task-card-item[data-urgency="expired"] .tl-dot { background: #a1a1aa; box-shadow: none; }
        .task-card-item[data-completed="1"] .tl-dot { background: #a1a1aa; box-shadow: none; }

        .tl-card { border-color: rgba(0, 0, 0, 0.07); }
        .dark .tl-card { border-color: rgba(255, 255, 255, 0.08); }
        .task-card-item[data-urgency="green"] .tl-card { border-color: rgba(16, 185, 129, 0.35); }
        .task-card-item[data-urgency="yellow"] .tl-card { border-color: rgba(245, 158, 11, 0.45); }
        .task-card-item[data-urgency="red"] .tl-card { border-color: rgba(239, 68, 68, 0.5); }
        .task-card-item[data-urgency="expired"] .tl-card { opacity: 0.65; border-color: rgba(0, 0, 0, 0.05); }
        .dark .task-card-item[data-urgency="expired"] .tl-card { border-color: rgba(255, 255, 255, 0.05); }

        .tl-count { color: #a1a1aa; font-variant-numeric: tabular-nums; }
        .task-card-item[data-urgency="green"] .tl-count { color: #059669; }
        .dark .task-card-item[data-urgency="green"] .tl-count { color: #34d399; }
        .task-card-item[data-urgency="yellow"] .tl-count { color: #d97706; font-weight: 600; }
        .dark .task-card-item[data-urgency="yellow"] .tl-count { color: #fbbf24; }
        .task-card-item[data-urgency="red"] .tl-count { color: #dc2626; font-weight: 700; }
        .dark .task-card-item[data-urgency="red"] .tl-count { color: #f87171; }
        .task-card-item[data-urgency="expired"] .tl-count { color: #dc2626; font-weight: 700; }
        .dark .task-card-item[data-urgency="expired"] .tl-count { color: #f87171; }
    </style>
</head>
<body class="min-h-[100dvh] bg-putih-kertas dark:bg-hitam-pekat text-hitam-pekat dark:text-putih-kertas">

    {{-- Overlay mobile --}}
    <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm hidden lg:hidden"></div>

    {{-- Sidebar --}}
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 z-50 w-72 flex flex-col bg-white dark:bg-zinc-900 border-r-4 border-merah-elegan -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out">

        <div class="px-6 pt-6 pb-5 border-b-2 border-black/5 dark:border-white/10">
            <a href="{{ route('radar') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Radar Tugas Kelas" class="w-10 h-10 object-contain shrink-0">
                <span class="font-display text-lg font-bold uppercase tracking-tight leading-tight">Radar Tugas<br>Kelas</span>
            </a>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1">
            <p class="font-mono text-[10px] uppercase tracking-widest text-zinc-400 px-3 mb-3">Menu</p>
            <a href="{{ route('radar') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/5 hover:text-merah-elegan dark:hover:text-red-400 transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0c0-3 1.5-6 3-9M12 21c0-3-1.5-6-3-9m3 0h.01"/>
                </svg>
                Radar Publik
            </a>
            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold bg-merah-elegan text-white shadow-md shadow-red-900/20">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                </svg>
                Dashboard
            </a>
        </nav>

        <div class="px-4 py-5 border-t-2 border-black/5 dark:border-white/10">
            <div class="flex items-center gap-3 px-3">
                <div class="w-10 h-10 rounded-full bg-merah-elegan text-white flex items-center justify-center font-display font-bold text-lg shrink-0">
                    {{ strtoupper(Str::substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">@{{ Auth::user()->username }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 mt-4">
                <button id="theme-toggle" aria-label="Ganti tema" title="Ganti tema"
                    class="btn-ghost w-11 h-11 shrink-0 rounded-lg border-2 border-black/10 dark:border-white/10 flex items-center justify-center hover:bg-merah-elegan hover:border-merah-elegan hover:text-white transition-colors">
                    <svg id="icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                    <svg id="icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    </svg>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="btn-primary w-full px-4 py-2.5 rounded-lg bg-merah-elegan hover:bg-red-700 text-white text-sm font-semibold">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Konten utama --}}
    <div class="lg:pl-72">
        <main class="max-w-5xl mx-auto px-5 sm:px-8 pt-6 sm:pt-8 pb-16">

            {{-- Bar atas mobile: hamburger + logo --}}
            <div class="flex items-center justify-between lg:hidden mb-6 anim-in">
                <button id="sidebarToggle" aria-label="Buka menu"
                    class="btn-ghost w-11 h-11 rounded-lg border-2 border-black/10 dark:border-white/10 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>
                <a href="{{ route('radar') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Radar Tugas Kelas" class="w-8 h-8 object-contain shrink-0">
                    <span class="font-display text-base font-bold uppercase tracking-tight">Radar Tugas Kelas</span>
                </a>
                <span class="w-11"></span>
            </div>

            {{-- Header sapaan --}}
            <header class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-8 anim-in" style="animation-delay: 100ms">
                <div>
                    <p class="font-mono text-[10px] text-merah-elegan dark:text-red-400 tracking-widest uppercase mb-2">Dashboard</p>
                    <h1 class="font-display text-3xl sm:text-4xl font-bold tracking-tight leading-none">
                        Halo, {{ Str::before(Auth::user()->name, ' ') }}
                    </h1>
                    <p class="mt-2 flex items-center gap-2 flex-wrap">
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ Auth::user()->class_name }}</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide {{ match(Auth::user()->role) { 'admin' => 'bg-merah-elegan text-white', 'ketua_kelas' => 'bg-merah-elegan/80 text-white', 'sekretaris' => 'bg-merah-elegan/60 text-white', default => 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400' } }}">
                            {{ match(Auth::user()->role) { 'admin' => 'Admin', 'ketua_kelas' => 'Ketua Kelas', 'sekretaris' => 'Sekretaris', default => 'Siswa' } }}
                        </span>
                    </p>
                </div>
                <div class="shrink-0">
                    @if(Auth::user()->role !== 'siswa')
                        <button onclick="openModal()"
                            class="btn-primary inline-flex items-center gap-2 bg-merah-elegan hover:bg-red-700 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-red-900/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Tambah Tugas
                        </button>
                    @endif
                </div>
            </header>

            @if(session('success'))
                <div class="flash-msg bg-emerald-500/10 border border-emerald-500/25 text-emerald-700 dark:text-emerald-400 px-5 py-3 rounded-xl mb-8 text-sm font-medium flex items-center gap-2.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Daftar Tugas: timeline real-time --}}
            <section class="anim-in" style="animation-delay: 200ms">
                @php
                    $isAdmin = Auth::user()->role === 'admin';
                    $ownClass = Auth::user()->class_name;
                    $canEdit = Auth::user()->role !== 'siswa';
                @endphp

                {{-- Stats Cards --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                    <div class="bg-white dark:bg-zinc-900 rounded-xl px-4 py-3.5 border-2 border-black/5 dark:border-white/10 border-t-merah-elegan dark:border-t-merah-elegan">
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Total</p>
                        <p class="font-display text-xl font-bold text-hitam-pekat dark:text-white mt-1">{{ $tasks->total() }}</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-900 rounded-xl px-4 py-3.5 border-2 border-black/5 dark:border-white/10 border-t-emerald-500 dark:border-t-emerald-500">
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Selesai</p>
                        <p class="font-display text-xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ count($completedIds) }}</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-900 rounded-xl px-4 py-3.5 border-2 border-black/5 dark:border-white/10 border-t-amber-500 dark:border-t-amber-500">
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Pending</p>
                        <p class="font-display text-xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ $tasks->total() - count($completedIds) }}</p>
                    </div>
                    <div class="bg-white dark:bg-zinc-900 rounded-xl px-4 py-3.5 border-2 border-black/5 dark:border-white/10 border-t-zinc-400 dark:border-t-zinc-400">
                        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Sisa Waktu</p>
                        <p class="font-display text-xl font-bold text-zinc-500 dark:text-zinc-300 mt-1">{{ $tasks->total() - count($completedIds) }}</p>
                    </div>
                </div>

                {{-- Filter & Search --}}
                <form method="GET" action="{{ route('dashboard') }}" class="mb-8">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tugas..."
                                class="input-field w-full px-4 py-2.5 rounded-xl text-sm pl-10">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                        </div>
                        @if($isAdmin)
                            <select name="class_name" onchange="this.form.submit()"
                                class="input-field px-4 py-2.5 rounded-xl text-sm min-w-[150px]">
                                <option value="">Semua Kelas</option>
                                @foreach($classOptions as $c)
                                    <option value="{{ $c }}" {{ request('class_name') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        @endif
                        <select name="subject" onchange="this.form.submit()"
                            class="input-field px-4 py-2.5 rounded-xl text-sm min-w-[130px]">
                            <option value="">Semua Mapel</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub }}" {{ request('subject') === $sub ? 'selected' : '' }}>{{ $sub }}</option>
                            @endforeach
                        </select>
                        @if(request('search') || request('subject') || request('class_name'))
                            <a href="{{ route('dashboard') }}"
                                class="btn-ghost inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm border-2 border-black/10 dark:border-white/10 text-zinc-500 dark:text-zinc-400 hover:border-merah-elegan hover:text-merah-elegan whitespace-nowrap">
                                Reset
                            </a>
                        @endif
                        <a id="exportLink" href="{{ route('tasks.export') }}"
                            class="btn-ghost inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm border-2 border-black/10 dark:border-white/10 text-zinc-500 dark:text-zinc-400 hover:border-merah-elegan hover:text-merah-elegan">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                            </svg>
                            <span class="hidden sm:inline">Export</span>
                        </a>
                    </div>
                </form>

                @if($tasks->isEmpty())
                    <div class="flex flex-col items-center justify-center py-20 sm:py-28 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-white dark:bg-zinc-900 border-2 border-black/10 dark:border-white/10 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                        </div>
                        <p class="font-display text-base font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Belum ada tugas</p>
                        <p class="text-zinc-400 text-sm">Tugas yang ditambahkan pengurus kelas akan muncul di sini.</p>
                    </div>
                @else
                    <div id="taskTimeline" class="relative">
                        <div class="absolute left-[15px] top-2 bottom-4 w-0.5 bg-black/10 dark:bg-white/10 rounded-full"></div>
                        @foreach($tasks as $task)
                            @php $completed = in_array($task->id, $completedIds); @endphp
                            <div class="relative pl-10 pb-7 task-card-item"
                                 data-deadline="{{ $task->deadline->toIso8601String() }}"
                                 data-completed="{{ $completed ? '1' : '0' }}"
                                 data-urgency="{{ $completed ? '' : 'green' }}"
                                 data-task-id="{{ $task->id }}"
                                 style="animation-delay: {{ 150 + ($loop->index * 70) }}ms">
                                <span class="tl-dot absolute left-[9px] top-2 w-3.5 h-3.5 rounded-full"></span>

                                <div class="tl-card bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border-2 p-4 sm:p-5 shadow-sm {{ $completed ? 'bg-zinc-50 dark:bg-zinc-900/60' : '' }}">
                                    <div class="flex items-start justify-between gap-3 mb-2.5">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400 truncate {{ strtolower($task->class_name) === strtolower($ownClass) ? 'text-merah-elegan dark:text-red-400' : '' }}">{{ $task->class_name }}</span>
                                        </div>
                                        @if($completed)
                                            <span class="inline-flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400 font-medium shrink-0">
                                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                Selesai
                                            </span>
                                        @else
                                            <span class="tl-count font-mono text-xs">--</span>
                                        @endif
                                    </div>

                                    <h3 class="font-display text-base font-bold tracking-tight leading-snug {{ $completed ? 'text-zinc-400 line-through' : '' }}">{{ $task->title }}</h3>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $task->subject }}</p>

                                    @if($task->description)
                                        <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-2 leading-relaxed {{ $completed ? 'line-through' : '' }}">{{ Str::limit($task->description, 90) }}</p>
                                    @endif

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-4 pt-3 border-t border-black/5 dark:border-white/10">
                                        <div class="flex items-center gap-1.5 text-[11px] text-zinc-500 dark:text-zinc-400">
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                            </svg>
                                            <span class="font-mono tabular-nums dl-label {{ $completed ? 'line-through' : '' }}" data-iso="{{ $task->deadline->toIso8601String() }}">{{ $task->deadline->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 flex-wrap justify-end">
                                            <form method="POST" action="{{ route('tasks.complete', $task) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn-ghost {{ $completed ? 'text-emerald-600/70 hover:text-emerald-500' : 'text-zinc-500 hover:text-merah-elegan' }} px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs font-medium min-h-[32px] sm:min-h-[36px]">
                                                    {{ $completed ? 'Batal' : 'Tandai' }}
                                                </button>
                                            </form>
                                            @if(($canEdit && strtolower($task->class_name) === strtolower($ownClass) && !$completed) || $isAdmin)
                                                <button onclick="startEdit({{ $task->id }}, @js($task->title), @js($task->subject), @js($task->deadline->toIso8601String()), @js($task->description ?? ''))"
                                                    class="btn-ghost text-zinc-500 hover:text-merah-elegan px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs font-medium min-h-[32px] sm:min-h-[36px]">
                                                    Edit
                                                </button>
                                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline"
                                                    onsubmit="return confirm('Hapus tugas ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="btn-ghost text-zinc-500 hover:text-red-500 px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs font-medium min-h-[32px] sm:min-h-[36px]">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if(($canEdit && strtolower($task->class_name) === strtolower($ownClass) && !$completed) || $isAdmin)
                                    <div class="edit-panel mt-3" id="edit-row-{{ $task->id }}">
                                        <form method="POST" action="{{ route('tasks.update', $task) }}" class="bg-white dark:bg-zinc-900 rounded-xl border-2 border-black/5 dark:border-white/10 p-4">
                                            @csrf @method('PUT')
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                                                <input type="text" name="title" id="edit-title-{{ $task->id }}" required placeholder="Judul"
                                                    class="input-field w-full px-3 py-2.5 rounded-lg text-sm">
                                                <input type="text" name="subject" id="edit-subject-{{ $task->id }}" required placeholder="Mata Pelajaran"
                                                    class="input-field w-full px-3 py-2.5 rounded-lg text-sm">
                                                <input type="datetime-local" name="deadline" id="edit-deadline-{{ $task->id }}" required
                                                    class="input-field w-full px-3 py-2.5 rounded-lg text-sm">
                                                <textarea name="description" id="edit-description-{{ $task->id }}" rows="1" placeholder="Deskripsi (opsional)"
                                                    class="input-field w-full px-3 py-2.5 rounded-lg text-sm"></textarea>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="submit" class="btn-primary bg-merah-elegan hover:bg-red-700 text-white px-4 py-2 sm:py-1.5 rounded-lg text-xs font-semibold">Simpan</button>
                                                <button type="button" onclick="cancelEdit({{ $task->id }})" class="btn-ghost border border-black/10 dark:border-white/10 text-zinc-500 px-4 py-2 sm:py-1.5 rounded-lg text-xs">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($tasks->hasPages())
                        <div class="mt-6 flex justify-center">
                            {{ $tasks->onEachSide(1)->links() }}
                        </div>
                    @endif
                @endif
            </section>
        </main>
    </div>

    {{-- Modal Tambah Tugas --}}
    @if(Auth::user()->role !== 'siswa')
        <div class="modal-overlay" id="addModal">
            <div class="modal-panel w-full max-w-lg mx-4 bg-white dark:bg-zinc-900 rounded-2xl border-4 border-merah-elegan p-5 sm:p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-display text-lg font-bold tracking-tight">Tambah Tugas Baru</h3>
                    <button onclick="closeModal()" class="text-zinc-400 hover:text-merah-elegan transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('tasks.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-500 mb-1.5 tracking-wide uppercase">Judul Tugas</label>
                            <input type="text" name="title" required value="{{ old('title') }}"
                                class="input-field w-full px-4 py-3 sm:py-2.5 rounded-xl text-sm">
                            @error('title') <p class="text-red-500/90 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-500 mb-1.5 tracking-wide uppercase">Mata Pelajaran</label>
                            <input type="text" name="subject" required value="{{ old('subject') }}"
                                class="input-field w-full px-4 py-3 sm:py-2.5 rounded-xl text-sm">
                            @error('subject') <p class="text-red-500/90 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-500 mb-1.5 tracking-wide uppercase">Deadline</label>
                            <input type="datetime-local" name="deadline" required value="{{ old('deadline') }}"
                                class="input-field w-full px-4 py-3 sm:py-2.5 rounded-xl text-sm">
                            @error('deadline') <p class="text-red-500/90 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-500 mb-1.5 tracking-wide uppercase">Deskripsi <span class="text-zinc-400 normal-case">(opsional)</span></label>
                            <textarea name="description" rows="2"
                                class="input-field w-full px-4 py-3 sm:py-2.5 rounded-xl text-sm">{{ old('description') }}</textarea>
                        </div>
                    </div>
                    @if(Auth::user()->role === 'admin')
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-500 mb-1.5 tracking-wide uppercase">Kelas</label>
                            <select name="class_name" required
                                class="input-field w-full px-4 py-3 sm:py-2.5 rounded-xl text-sm">
                                <option value="" disabled {{ old('class_name') ? '' : 'selected' }}>Pilih kelas</option>
                                @foreach($classOptions as $c)
                                    <option value="{{ $c }}" {{ old('class_name') === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                            @error('class_name') <p class="text-red-500/90 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeModal()"
                            class="btn-ghost border border-black/10 dark:border-white/10 text-zinc-500 px-5 py-3 sm:py-2.5 rounded-xl text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn-primary inline-flex items-center gap-2 bg-merah-elegan hover:bg-red-700 text-white px-6 py-3 sm:py-2.5 rounded-xl text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        function openModal() {
            document.getElementById('addModal').classList.add('is-open');
        }
        function closeModal() {
            document.getElementById('addModal').classList.remove('is-open');
        }
        document.getElementById('addModal')?.addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });

        // Sidebar (mobile)
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        }
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
        document.getElementById('sidebarToggle')?.addEventListener('click', openSidebar);
        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });

        // Theme toggle with water-drop reveal (old theme outside the circle, new theme inside)
        (function () {
            var html = document.documentElement;
            var btn = document.getElementById('theme-toggle');
            var moon = document.getElementById('icon-moon');
            var sun = document.getElementById('icon-sun');

            function apply(isDark) {
                html.classList.toggle('dark', isDark);
                if (moon) moon.classList.toggle('hidden', isDark);
                if (sun) sun.classList.toggle('hidden', !isDark);
            }

            function transitionTheme(x, y, isDark) {
                function commit() {
                    apply(isDark);
                    try { localStorage.setItem('rtk-theme', isDark ? 'dark' : 'light'); } catch (e) {}
                }
                // ponytail: instant switch if View Transitions unsupported or reduced motion
                if (!document.startViewTransition || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { commit(); return; }

                var maxR = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
                var rs = document.documentElement.style;
                rs.setProperty('--rtk-x', x + 'px');
                rs.setProperty('--rtk-y', y + 'px');
                rs.setProperty('--rtk-maxr', maxR + 'px');
                document.startViewTransition(commit);
            }

            if (btn) {
                btn.addEventListener('click', function (e) {
                    transitionTheme(e.clientX, e.clientY, !html.classList.contains('dark'));
                });
            }
            apply(html.classList.contains('dark'));
        })();

        function startEdit(id, title, subject, deadline, description) {
            document.getElementById('edit-title-' + id).value = title;
            document.getElementById('edit-subject-' + id).value = subject;
            document.getElementById('edit-deadline-' + id).value = isoToLocalInput(deadline);
            document.getElementById('edit-description-' + id).value = description;
            document.getElementById('edit-row-' + id).classList.add('is-open');
        }
        function cancelEdit(id) {
            document.getElementById('edit-row-' + id).classList.remove('is-open');
        }

        function pad2(n) { return String(n).padStart(2, '0'); }

        // convert <input type="datetime-local"> (user's local wall clock) → ISO with device offset
        function localToISO(value) {
            if (!value) return value;
            var d = new Date(value);
            var off = -d.getTimezoneOffset();
            var sign = off >= 0 ? '+' : '-';
            var abs = Math.abs(off);
            return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()) +
                'T' + pad2(d.getHours()) + ':' + pad2(d.getMinutes()) + ':00' +
                sign + pad2(Math.floor(abs / 60)) + ':' + pad2(abs % 60);
        }

        // convert ISO → local wall clock for datetime-local prefill
        function isoToLocalInput(iso) {
            var d = new Date(iso);
            if (isNaN(d.getTime())) return '';
            return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()) +
                'T' + pad2(d.getHours()) + ':' + pad2(d.getMinutes());
        }

        function getUserTZ() {
            if (typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
                try {
                    var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
                    if (tz) return tz;
                } catch (e) {}
            }
            if (window._userCoord) {
                var off = Math.round(window._userCoord.longitude / 15) * 60;
                var sign = off >= 0 ? '+' : '-';
                return sign + pad2(Math.floor(Math.abs(off) / 60)) + ':' + pad2(Math.abs(off) % 60);
            }
            return null;
        }

        // ponytail: browser prompts for GPS permission only once per origin
        if (!window._gpsDone && 'geolocation' in navigator) {
            window._gpsDone = true;
            navigator.geolocation.getCurrentPosition(
                function (pos) { window._userCoord = pos.coords; },
                function () {},
                { timeout: 5000, maximumAge: 600000 }
            );
        }

        // ponytail: attach user's timezone to the export link
        var exportLink = document.getElementById('exportLink');
        if (exportLink) {
            var tz = getUserTZ();
            if (tz) exportLink.href += (exportLink.href.indexOf('?') >= 0 ? '&' : '?') + 'tz=' + encodeURIComponent(tz);
        }

        // ponytail: format deadlines in the user's own timezone
        document.querySelectorAll('.dl-label').forEach(function (el) {
            var d = new Date(el.dataset.iso);
            if (!isNaN(d.getTime())) {
                el.textContent = d.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            }
        });

        // ponytail: submit deadline as ISO with device offset so it stores the correct instant
        document.querySelectorAll('form[action*="/tasks"]').forEach(function (f) {
            var input = f.querySelector('input[name="deadline"]');
            if (input) {
                f.addEventListener('submit', function () {
                    input.value = localToISO(input.value);
                });
            }
        });

        function colorBand(diff) {
            if (diff <= 0) return 'expired';
            var days = Math.floor(diff / 86400000);
            if (days >= 3) return 'green';
            if (days >= 1) return 'yellow';
            return 'red';
        }
        var BAND_MESSAGES = {
            yellow: 'Warna berubah: tersisa kurang dari 3 hari',
            red: 'Warna berubah: tersisa kurang dari 1 hari!',
            expired: 'Warna berubah: deadline sudah habis'
        };
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        function updateCountdowns() {
            const now = Date.now();
            document.querySelectorAll('.task-card-item').forEach(card => {
                if (card.dataset.completed === '1') return;

                const deadline = new Date(card.dataset.deadline).getTime();
                const diff = deadline - now;
                const el = card.querySelector('.tl-count');
                const band = colorBand(diff);

                // ponytail: notification once per card color change
                if ('Notification' in window && Notification.permission === 'granted') {
                    var prev = card.dataset.colorState;
                    if (!prev) {
                        card.dataset.colorState = band;
                    } else if (prev !== band) {
                        let t = card.querySelector('h3')?.textContent || 'Tugas';
                        new Notification(t, { body: BAND_MESSAGES[band] });
                        card.dataset.colorState = band;
                    }
                }

                card.dataset.urgency = band;

                if (diff <= 0) {
                    el.textContent = 'HABIS';
                    return;
                }

                const days = Math.floor(diff / 86400000);
                const hours = Math.floor((diff % 86400000) / 3600000);
                const minutes = Math.floor((diff % 3600000) / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);

                if (days > 0) {
                    el.textContent = days + 'h ' + hours + 'j ' + minutes + 'm';
                } else {
                    el.textContent = hours + 'j ' + minutes + 'm ' + seconds + 'd';
                }
            });
        }

        // Stagger reveal
        document.querySelectorAll('.task-card-item').forEach((card, i) => {
            card.style.opacity = '0';
            card.style.animation = 'fadeSlideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards';
            card.style.animationDelay = (150 + i * 70) + 'ms';
        });

        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    </script>
</body>
</html>
