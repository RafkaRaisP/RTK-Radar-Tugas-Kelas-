<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta-description', 'Radar Tugas Kelas — pantau deadline tugas sekolah secara real-time.')">
    <title>@yield('title', 'Radar Tugas Kelas')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">

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

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-[100dvh] text-stone-900 dark:text-zinc-100 @yield('bodyClass')">
    @yield('body')

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

            function transitionTheme(x, y, isDark) {
                function commit() {
                    apply(isDark);
                    try { localStorage.setItem('rtk-theme', isDark ? 'dark' : 'light'); } catch (e) {}
                }
                if (!document.startViewTransition || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { commit(); return; }
                var maxR = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));
                var rs = document.documentElement.style;
                rs.setProperty('--rtk-x', x + 'px');
                rs.setProperty('--rtk-y', y + 'px');
                rs.setProperty('--rtk-maxr', maxR + 'px');
                document.startViewTransition(commit);
            }

            if (btn) btn.addEventListener('click', function (e) { transitionTheme(e.clientX, e.clientY, !html.classList.contains('dark')); });
            apply(html.classList.contains('dark'));
        })();

        // Format label tanggal lokal (id-ID); dipakai halaman radar & dashboard
        document.querySelectorAll('.dl-label').forEach(function (el) {
            var d = new Date(el.dataset.iso);
            if (!isNaN(d.getTime())) {
                el.textContent = d.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            }
        });

        // Countdown live + semaphore warna + notifikasi perubahan status
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
        if (document.querySelector('.task-card-item') && 'Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        function updateCountdowns() {
            const now = Date.now();
            document.querySelectorAll('.task-card-item').forEach(card => {
                if (card.dataset.completed === '1') return;
                const deadline = new Date(card.dataset.deadline).getTime();
                const diff = deadline - now;
                const el = card.querySelector('.cd-count, .tl-count');
                const band = colorBand(diff);

                if ('Notification' in window && Notification.permission === 'granted') {
                    var prev = card.dataset.colorState;
                    if (!prev) {
                        card.dataset.colorState = band;
                    } else if (prev !== band) {
                        let t = card.querySelector('h3, h2')?.textContent || 'Tugas';
                        new Notification(t, { body: BAND_MESSAGES[band] });
                        card.dataset.colorState = band;
                    }
                }

                card.dataset.urgency = band;
                if (!el) return;
                if (diff <= 0) { el.textContent = 'HABIS'; return; }

                const days = Math.floor(diff / 86400000);
                const hours = Math.floor((diff % 86400000) / 3600000);
                const minutes = Math.floor((diff % 3600000) / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                el.textContent = days > 0 ? days + 'h ' + hours + 'j ' + minutes + 'm' : hours + 'j ' + minutes + 'm ' + seconds + 'd';
            });
        }

        if (document.querySelector('.task-card-item')) {
            document.querySelectorAll('.task-card-item').forEach((card, i) => {
                card.style.opacity = '0';
                card.style.animation = 'pageIn .45s cubic-bezier(.16, 1, .3, 1) forwards';
                card.style.animationDelay = (120 + Math.min(i, 8) * 55) + 'ms';
            });
            updateCountdowns();
            setInterval(updateCountdowns, 1000);
        }
    </script>

    @yield('scripts')
</body>
</html>