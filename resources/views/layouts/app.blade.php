<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Radar Tugas Kelas — pantau deadline tugas sekolah secara real-time.">
    <title>@yield('title', 'Radar Tugas Kelas')</title>

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
    </style>

    @stack('styles')
</head>
<body class="min-h-[100dvh] flex flex-col bg-putih-kertas dark:bg-hitam-pekat text-hitam-pekat dark:text-putih-kertas">

    {{-- Navbar --}}
    <header class="border-b-4 border-hitam-pekat dark:border-hijau-aksen">
        <div class="max-w-7xl mx-auto px-5 sm:px-8">
            <div class="flex items-center justify-between py-5 gap-4">
                <a href="{{ route('radar') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Radar Tugas Kelas" class="w-9 h-9 sm:w-10 sm:h-10 object-contain shrink-0" style="filter:hue-rotate(158deg)">
                    <span class="font-display text-lg sm:text-2xl font-bold uppercase tracking-tight">Radar Tugas Kelas</span>
                </a>
                <nav class="flex items-center gap-4 sm:gap-8">
                    <a href="{{ route('radar') }}"
                        class="font-mono text-[10px] sm:text-xs uppercase tracking-widest transition-colors {{ request()->routeIs('radar') ? 'text-merah-elegan border-b-2 border-merah-elegan pb-0.5' : 'hover:text-merah-elegan' }}">
                        Radar
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="font-mono text-[10px] sm:text-xs uppercase tracking-widest transition-colors {{ request()->routeIs('dashboard') ? 'text-merah-elegan border-b-2 border-merah-elegan pb-0.5' : 'hover:text-merah-elegan' }}">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="font-mono text-[10px] sm:text-xs uppercase tracking-widest transition-colors {{ request()->routeIs('login') ? 'text-merah-elegan border-b-2 border-merah-elegan pb-0.5' : 'hover:text-merah-elegan' }}">
                            Masuk
                        </a>
                    @endauth
                    <button id="theme-toggle" aria-label="Ganti tema" title="Ganti tema"
                        class="w-10 h-10 sm:w-11 sm:h-11 border-2 border-hitam-pekat dark:border-hijau-aksen flex items-center justify-center hover:bg-merah-elegan hover:border-merah-elegan hover:text-putih-kertas transition-colors">
                        <svg id="icon-moon" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                        </svg>
                        <svg id="icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                        </svg>
                    </button>
                </nav>
            </div>
        </div>
    </header>

    {{-- Konten halaman --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="border-t-4 border-hitam-pekat dark:border-hijau-aksen">
        <div class="max-w-7xl mx-auto px-5 sm:px-8 py-8 flex items-center justify-between">
            <span class="font-mono text-[10px] uppercase tracking-widest text-zinc-500">Radar Tugas Kelas</span>
            <span class="font-mono text-[10px] uppercase tracking-widest text-zinc-500">{{ date('Y') }}</span>
        </div>
    </footer>

    {{-- Toggle dark/light mode --}}
    <script>
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

            if (btn) {
                btn.addEventListener('click', function () {
                    var isDark = !html.classList.contains('dark');
                    apply(isDark);
                    try { localStorage.setItem('rtk-theme', isDark ? 'dark' : 'light'); } catch (e) {}
                });
            }

            apply(html.classList.contains('dark'));
        })();
    </script>

    @stack('scripts')
</body>
</html>
