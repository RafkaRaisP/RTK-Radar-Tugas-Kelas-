<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radar Tugas Kelas</title>

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

        /* water-drop theme reveal (grow): new theme clipped by a circle expanding from the click point */
        ::view-transition-old(root) { animation: none; }
        ::view-transition-new(root) { animation: rtk-reveal 850ms cubic-bezier(0.65, 0, 0.35, 1); }
        @keyframes rtk-reveal {
            from { clip-path: circle(2px at var(--rtk-x) var(--rtk-y)); }
            to { clip-path: circle(var(--rtk-maxr) at var(--rtk-x) var(--rtk-y)); }
        }

        .task-card {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px -14px rgba(0, 0, 0, 0.2);
        }
        .dark .task-card:hover {
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

        /* ===== Warna urgensi kartu (grid) ===== */
        .task-card { border-color: rgba(0, 0, 0, 0.07); }
        .dark .task-card { border-color: rgba(255, 255, 255, 0.08); }
        .task-card-item[data-urgency="green"] { border-color: rgba(16, 185, 129, 0.35); }
        .task-card-item[data-urgency="yellow"] { border-color: rgba(245, 158, 11, 0.45); }
        .task-card-item[data-urgency="red"] { border-color: rgba(239, 68, 68, 0.5); }
        .task-card-item[data-urgency="expired"] { opacity: 0.65; border-color: rgba(0, 0, 0, 0.05); }
        .dark .task-card-item[data-urgency="expired"] { border-color: rgba(255, 255, 255, 0.05); }

        .accent-line { background: rgba(0, 0, 0, 0.08); }
        .dark .accent-line { background: rgba(255, 255, 255, 0.08); }
        .task-card-item[data-urgency="green"] .accent-line { background: rgba(16, 185, 129, 0.55); }
        .task-card-item[data-urgency="yellow"] .accent-line { background: rgba(245, 158, 11, 0.6); }
        .task-card-item[data-urgency="red"] .accent-line { background: rgba(239, 68, 68, 0.7); }

        .status-dot { background: #a1a1aa; }
        .task-card-item[data-urgency="green"] .status-dot { background: #10b981; }
        .task-card-item[data-urgency="yellow"] .status-dot { background: #f59e0b; }
        .task-card-item[data-urgency="red"] .status-dot { background: #ef4444; }
        .task-card-item[data-urgency="expired"] .status-dot { background: #a1a1aa; }

        .cd-count { color: #a1a1aa; font-variant-numeric: tabular-nums; }
        .task-card-item[data-urgency="green"] .cd-count { color: #059669; }
        .dark .task-card-item[data-urgency="green"] .cd-count { color: #34d399; }
        .task-card-item[data-urgency="yellow"] .cd-count { color: #d97706; font-weight: 600; }
        .dark .task-card-item[data-urgency="yellow"] .cd-count { color: #fbbf24; }
        .task-card-item[data-urgency="red"] .cd-count { color: #dc2626; font-weight: 700; }
        .dark .task-card-item[data-urgency="red"] .cd-count { color: #f87171; }
        .task-card-item[data-urgency="expired"] .cd-count { color: #dc2626; font-weight: 700; }
        .dark .task-card-item[data-urgency="expired"] .cd-count { color: #f87171; }
    </style>
</head>
<body class="min-h-[100dvh] bg-putih-kertas dark:bg-hitam-pekat text-hitam-pekat dark:text-putih-kertas">

    <div class="max-w-7xl mx-auto px-5 sm:px-8 pt-10 pb-16">
        {{-- Header --}}
        <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-12 anim-in" style="animation-delay: 100ms">
            <div>
                <a href="{{ route('radar') }}" class="inline-flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Radar Tugas Kelas" class="w-9 h-9 object-contain shrink-0">
                    <span class="font-mono text-[11px] uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Radar Tugas Kelas</span>
                </a>
                <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight leading-[0.95]">
                    Radar Tugas<br>Kelas
                </h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-3 max-w-[40ch] leading-relaxed">
                    Pantau deadline tugas secara real-time. Waktu tersisa diperbarui setiap detik.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <div class="relative">
                    <select id="classFilter" class="input-field bg-white dark:bg-zinc-900 rounded-xl px-4 py-2.5 text-sm pr-10 -webkit-appearance-none appearance-none">
                        <option value="">Semua Kelas</option>
                        @foreach($classOptions as $class)
                            <option value="{{ $class }}" {{ request('class_name') === $class ? 'selected' : '' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </div>
                <button id="theme-toggle" aria-label="Ganti tema" title="Ganti tema"
                    class="btn-ghost w-11 h-11 rounded-xl border-2 border-black/10 dark:border-white/10 flex items-center justify-center hover:bg-merah-elegan hover:border-merah-elegan hover:text-white transition-colors">
                    <svg id="icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                    <svg id="icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    </svg>
                </button>
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="btn-primary inline-flex items-center gap-2 bg-merah-elegan hover:bg-red-700 text-white px-5 py-3 sm:py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-red-900/20">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="btn-ghost inline-flex items-center px-5 py-3 sm:py-2.5 rounded-xl text-sm font-semibold border-2 border-merah-elegan text-merah-elegan hover:bg-merah-elegan hover:text-white transition-colors">
                        Masuk
                    </a>
                @endauth
            </div>
        </header>

        {{-- Konten utama: grid kartu tugas dengan filter kelas --}}
        @if($tasks->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 sm:py-32 text-center anim-in" style="animation-delay: 200ms">
                <div class="w-16 h-16 rounded-2xl bg-white dark:bg-zinc-900 border-2 border-black/10 dark:border-white/10 flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-display text-lg font-semibold text-zinc-500 dark:text-zinc-400 mb-1">Belum ada tugas</p>
                <p class="text-zinc-400 text-sm">Tugas yang ditambahkan pengurus kelas akan muncul di sini.</p>
            </div>
        @else
            <div id="taskGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @foreach($tasks as $task)
                    <div class="task-card task-card-item bg-white dark:bg-zinc-900 rounded-xl sm:rounded-[1.25rem] p-4 sm:p-6 border-2 relative overflow-hidden shadow-sm"
                         data-deadline="{{ $task->deadline->toIso8601String() }}"
                         data-task-id="{{ $task->id }}"
                         data-urgency="green"
                         style="animation-delay: {{ 150 + ($loop->index * 70) }}ms">
                        {{-- Garis aksen atas sebagai hiasan kartu --}}
                        <div class="accent-line absolute top-0 left-0 right-0 h-1 rounded-full"></div>

                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="status-dot w-1.5 h-1.5 rounded-full shrink-0"></span>
                                <span class="text-[11px] font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400 truncate">{{ $task->class_name }}</span>
                            </div>
                            <span class="cd-count font-mono text-xs shrink-0">--</span>
                        </div>

                        <h3 class="font-display text-base font-bold tracking-tight leading-snug">{{ $task->title }}</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $task->subject }}</p>

                        @if($task->description)
                            <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-2 leading-relaxed">{{ Str::limit($task->description, 72) }}</p>
                        @endif

                        <div class="flex items-center gap-1.5 text-[11px] text-zinc-500 dark:text-zinc-400 mt-4 pt-3 border-t border-black/5 dark:border-white/10">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                            </svg>
                            <span class="font-mono tabular-nums dl-label" data-iso="{{ $task->deadline->toIso8601String() }}">{{ $task->deadline->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($tasks->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $tasks->onEachSide(1)->links() }}
                </div>
            @endif
        @endif
    </div>

    <script>
        document.getElementById('classFilter').addEventListener('change', function () {
            const val = this.value;
            window.location.href = val ? '/radar?class_name=' + encodeURIComponent(val) : '/radar';
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

        if (!window._gpsDone && 'geolocation' in navigator) {
            window._gpsDone = true;
            navigator.geolocation.getCurrentPosition(
                function (pos) { window._userCoord = pos.coords; },
                function () {},
                { timeout: 5000, maximumAge: 600000 }
            );
        }

        // ponytail: format deadline in the user's own timezone
        document.querySelectorAll('.dl-label').forEach(function (el) {
            var d = new Date(el.dataset.iso);
            if (!isNaN(d.getTime())) {
                el.textContent = d.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
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
                const deadline = new Date(card.dataset.deadline).getTime();
                const diff = deadline - now;
                const el = card.querySelector('.cd-count');
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

        // Stagger card reveal
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
