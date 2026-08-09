<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radar Tugas Kelas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;800&family=Outfit:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        html { -webkit-tap-highlight-color: transparent; -webkit-text-size-adjust: 100%; }
        body { font-family: 'Outfit', sans-serif; overscroll-behavior: none; }

        .font-display { font-family: 'Cabinet Grotesk', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        .grain {
            position: fixed; inset: 0; z-index: 50;
            pointer-events: none; opacity: 0.025;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 200px 200px;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .anim-in {
            opacity: 0;
            animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes urgentPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.55; }
        }
        .urgent-card {
            animation: urgentPulse 1.2s ease-in-out infinite;
        }

        @keyframes statusPulse {
            0%, 100% { box-shadow: 0 0 0 0 currentColor; }
            50% { box-shadow: 0 0 0 4px transparent; }
        }
        .status-dot {
            animation: statusPulse 2s ease-in-out infinite;
        }

        .task-card {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1),
                        border-color 0.4s ease;
        }
        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px -10px rgba(0, 0, 0, 0.4);
        }

        .glass-surface {
            background: rgba(24, 24, 27, 0.5);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
        }

        .select-field {
            background: rgba(24, 24, 27, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: border-color 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            -webkit-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2371717a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 32px;
        }
        .select-field:focus {
            outline: none;
            border-color: rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body class="min-h-[100dvh] bg-zinc-950 text-zinc-100">
    <div class="grain"></div>

    <div class="max-w-7xl mx-auto px-5 sm:px-8 pt-10 pb-16">
        {{-- Header --}}
        <header class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-12 anim-in" style="animation-delay: 100ms">
            <div>
                <p class="font-mono text-[10px] text-emerald-400/70 tracking-widest uppercase mb-2">Deadline Tracker</p>
                <h1 class="font-display text-4xl sm:text-5xl md:text-6xl font-bold tracking-tight leading-[0.95]">
                    Radar Tugas<br>Kelas
                </h1>
                <p class="text-zinc-500 text-sm mt-3 max-w-[40ch] leading-relaxed">
                    Pantau deadline tugas secara real-time. Waktu tersisa diperbarui setiap detik.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0 flex-wrap">
                <div class="relative">
                    <select id="classFilter" class="select-field bg-zinc-900 text-zinc-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none pr-10">
                        <option value="">Semua Kelas</option>
                        @foreach($classOptions as $class)
                            <option value="{{ $class }}" {{ request('class_name') === $class ? 'selected' : '' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </div>
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="glass-surface text-emerald-400 hover:text-emerald-300 px-4 py-3 sm:py-2.5 rounded-xl text-sm transition-colors duration-200">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="glass-surface text-zinc-400 hover:text-zinc-200 px-4 py-3 sm:py-2.5 rounded-xl text-sm transition-colors duration-200">
                        Masuk
                    </a>
                @endauth
            </div>
        </header>

        {{-- Konten utama: grid kartu tugas dengan filter kelas --}}
        @if($tasks->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 sm:py-32 anim-in" style="animation-delay: 200ms">
                <div class="w-16 h-16 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-5">
                    <svg class="w-7 h-7 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="font-display text-lg font-semibold text-zinc-400 mb-1">Belum ada tugas</p>
                <p class="text-zinc-600 text-sm">Tugas yang ditambahkan pengurus kelas akan muncul di sini.</p>
            </div>
        @else
            <div id="taskGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @foreach($tasks as $task)
                    <div class="task-card task-card-item bg-zinc-900/60 rounded-xl sm:rounded-[1.25rem] p-3.5 sm:p-6 border border-white/[0.04] relative overflow-hidden"
                         data-deadline="{{ $task->deadline->toIso8601String() }}"
                         data-task-id="{{ $task->id }}"
                         style="animation-delay: {{ 150 + ($loop->index * 70) }}ms">
                        {{-- Garis aksen atas sebagai hiasan kartu --}}
                        <div class="task-accent-line absolute top-0 left-0 right-0 h-[2px] bg-emerald-500/40 rounded-full"></div>

                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="status-dot w-1.5 h-1.5 rounded-full bg-emerald-400 task-status-dot"></span>
                                <span class="text-[11px] font-medium text-zinc-500 tracking-wide">{{ $task->class_name }}</span>
                            </div>
                            <span class="countdown font-mono text-xs font-medium text-emerald-400/80 tabular-nums">--</span>
                        </div>

                        <h3 class="font-display text-base font-bold tracking-tight mb-1 leading-snug">{{ $task->title }}</h3>
                        <p class="text-sm text-zinc-500 mb-2">{{ $task->subject }}</p>

                        @if($task->description)
                            <p class="text-xs text-zinc-600 mb-4 leading-relaxed">{{ Str::limit($task->description, 72) }}</p>
                        @else
                            <div class="mb-4"></div>
                        @endif

                        <div class="flex items-center gap-1.5 text-[11px] text-zinc-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
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
                const el = card.querySelector('.countdown');
                const line = card.querySelector('.task-accent-line');
                const dot = card.querySelector('.task-status-dot');

                // ponytail: notification once per card color change
                if ('Notification' in window && Notification.permission === 'granted') {
                    var band = colorBand(diff);
                    var prev = card.dataset.colorState;
                    if (!prev) {
                        card.dataset.colorState = band;
                    } else if (prev !== band) {
                        let t = card.querySelector('h3')?.textContent || 'Tugas';
                        new Notification(t, { body: BAND_MESSAGES[band] });
                        card.dataset.colorState = band;
                    }
                }

                if (diff <= 0) {
                    el.textContent = 'HABIS';
                    el.className = 'countdown font-mono text-xs font-semibold text-red-400/90 tabular-nums';
                    card.className = 'task-card task-card-item bg-zinc-900/30 rounded-[1.25rem] p-6 border border-white/[0.03] relative overflow-hidden opacity-50';
                    if (line) line.className = 'task-accent-line absolute top-0 left-0 right-0 h-[2px] bg-red-500/30 rounded-full';
                    if (dot) { dot.className = 'status-dot w-1.5 h-1.5 rounded-full bg-red-400/60 task-status-dot'; dot.style.animation = 'none'; }
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

                if (days >= 3) {
                    card.className = 'task-card task-card-item bg-zinc-900/60 rounded-[1.25rem] p-6 border border-emerald-500/[0.08] relative overflow-hidden';
                    el.className = 'countdown font-mono text-xs font-medium text-emerald-400/80 tabular-nums';
                    if (line) line.className = 'task-accent-line absolute top-0 left-0 right-0 h-[2px] bg-emerald-500/40 rounded-full';
                    if (dot) { dot.className = 'status-dot w-1.5 h-1.5 rounded-full bg-emerald-400 task-status-dot'; dot.style.animation = ''; }
                } else if (days >= 1) {
                    card.className = 'task-card task-card-item bg-zinc-900/60 rounded-[1.25rem] p-6 border border-amber-500/[0.1] relative overflow-hidden';
                    el.className = 'countdown font-mono text-xs font-medium text-amber-400/80 tabular-nums';
                    if (line) line.className = 'task-accent-line absolute top-0 left-0 right-0 h-[2px] bg-amber-500/30 rounded-full';
                    if (dot) { dot.className = 'status-dot w-1.5 h-1.5 rounded-full bg-amber-400 task-status-dot'; dot.style.animation = ''; }
                } else {
                    card.className = 'task-card task-card-item bg-zinc-900/60 rounded-[1.25rem] p-6 border border-red-500/[0.12] relative overflow-hidden urgent-card';
                    el.className = 'countdown font-mono text-xs font-semibold text-red-400/90 tabular-nums';
                    if (line) line.className = 'task-accent-line absolute top-0 left-0 right-0 h-[2px] bg-red-500/40 rounded-full';
                    if (dot) { dot.className = 'status-dot w-1.5 h-1.5 rounded-full bg-red-400 task-status-dot'; dot.style.animation = ''; }
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
