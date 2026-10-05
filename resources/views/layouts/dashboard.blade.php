@extends('layouts.app')

@section('bodyClass', 'notebook-bg')

@section('body')
    <div id="sidebarOverlay" class="fixed inset-0 z-40 hidden bg-stone-950/60 backdrop-blur-sm lg:hidden"></div>

    <aside id="sidebar" class="book-cover fixed inset-y-0 left-0 z-50 flex w-[260px] -translate-x-full flex-col overflow-y-auto text-white transition-transform duration-300 ease-out lg:translate-x-0">
        <div class="border-b border-white/15 px-5 py-6">
            <a href="{{ route('radar') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-sm border border-white/25 bg-white/10">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-8 object-contain brightness-0 invert">
                </span>
                <span>
                    <span class="font-display block text-lg font-extrabold leading-none">Radar Tugas</span>
                    <span class="font-mono mt-1 block text-[9px] uppercase tracking-[.22em] text-red-100/70 dark:text-zinc-400">Buku kelas</span>
                </span>
            </a>
        </div>

        <nav aria-label="Navigasi dashboard" class="flex-1 px-4 py-7">
            <p class="font-mono mb-3 px-3 text-[9px] font-bold uppercase tracking-[.24em] text-red-100/60 dark:text-zinc-500">Daftar isi</p>
            @php
                $isAdmin = Auth::user()->role === 'admin';
                $isPj = Auth::user()->isPjMapel();
                $icons = [
                    'dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>',
                    'admin.index' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>',
                    'admin.users' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>',
                    'admin.pj' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
                    'admin.tasks' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'admin.subjects' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>',
                    'admin.verifications' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-1.043-3.296 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.746 3.746 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>',
                    'admin.academic-years' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>',
                    'pj.dashboard' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
                    'radar' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418"/>',
                ];
                $items = [];
                $items[] = ['route' => 'dashboard', 'label' => 'Agenda Saya', 'active' => request()->routeIs('dashboard')];
                if ($isAdmin) {
                    foreach ([
                        'admin.index' => 'Ringkasan',
                        'admin.users' => 'Kelola User',
                        'admin.pj' => 'Kelola PJ',
                        'admin.tasks' => 'Kelola Tugas',
                        'admin.subjects' => 'Mata Pelajaran',
                        'admin.verifications' => 'Verifikasi',
                        'admin.academic-years' => 'Tahun Ajaran',
                    ] as $r => $l) {
                        $items[] = ['route' => $r, 'label' => $l, 'active' => request()->routeIs($r)];
                    }
                }
                if ($isPj) {
                    $items[] = ['route' => 'pj.dashboard', 'label' => 'Verifikasi PJ', 'active' => request()->routeIs('pj.dashboard')];
                }
                $items[] = ['route' => 'radar', 'label' => 'Radar Publik', 'active' => request()->routeIs('radar')];
            @endphp
            <div class="space-y-2">
                @foreach($items as $i => $item)
                    @php
                        $cls = $item['active']
                            ? 'bg-white font-bold text-[#a30f2b] shadow-md dark:bg-[#be1836] dark:text-white'
                            : 'border border-white/15 font-semibold text-white/80 hover:bg-white/10 hover:text-white';
                    @endphp
                    <a href="{{ route($item['route']) }}" {{ $item['active'] ? 'aria-current="page"' : '' }} class="flex min-h-11 items-center gap-3 rounded-sm px-3 py-2.5 text-sm {{ $cls }}">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">{!! $icons[$item['route']] ?? '' !!}</svg>
                        <span class="font-mono text-[9px] opacity-60">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="h-px w-4 bg-current/30"></span>
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <div class="mt-9 border-t border-white/15 pt-6">
                <p class="font-mono mb-3 px-3 text-[9px] font-bold uppercase tracking-[.24em] text-red-100/60 dark:text-zinc-500">Identitas</p>
                <div class="mx-1 rotate-[-1deg] rounded-sm bg-[#fffaf0] p-4 text-stone-900 shadow-lg">
                    <div class="flex items-center gap-3">
                        <span class="font-display flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-[#be1836] text-lg font-extrabold text-[#be1836]">{{ strtoupper(Str::substr(Auth::user()->name, 0, 1)) }}</span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-bold">{{ Auth::user()->name }}</span>
                            <span class="block truncate font-mono text-[9px] text-stone-500">{{ Auth::user()->username }}</span>
                        </span>
                    </div>
                    <div class="mt-3 flex items-center justify-between border-t border-dashed border-stone-300 pt-3 font-mono text-[9px] uppercase tracking-wider text-stone-500">
                        <span>{{ Auth::user()->class_name }}</span>
                        <span>{{ match(Auth::user()->role) { 'admin' => 'Admin', 'ketua_kelas' => 'Ketua', 'sekretaris' => 'Sekretaris', 'pj_mapel' => 'PJ ' . Auth::user()->pj_subject, default => 'Siswa' } }}</span>
                    </div>
                </div>
            </div>
        </nav>

        <div class="border-t border-white/15 p-4">
            <div class="flex gap-2">
                <button id="theme-toggle" type="button" aria-label="Ganti tema" title="Ganti tema" class="pressable flex h-11 w-11 shrink-0 items-center justify-center rounded-sm border border-white/25 bg-black/10 hover:bg-white/10">
                    <svg id="icon-moon" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/></svg>
                    <svg id="icon-sun" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="pressable min-h-11 w-full rounded-sm border border-white/25 bg-black/10 px-4 text-sm font-bold hover:bg-white hover:text-[#a30f2b] dark:hover:text-zinc-900">Keluar</button>
                </form>
            </div>
        </div>
    </aside>

    <div id="pageContent" class="lg:pl-[260px]">
        <main class="mx-auto max-w-6xl px-5 pb-20 pt-5 sm:px-8 sm:pt-8">
            <div class="mb-7 flex items-center justify-between lg:hidden">
                <button id="sidebarToggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="sidebar" class="pressable flex h-11 w-11 items-center justify-center rounded-sm border border-stone-400/50 bg-[#fffaf0] dark:border-white/15 dark:bg-zinc-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
                <a href="{{ route('radar') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-8 object-contain">
                    <span class="font-display text-lg font-extrabold">Radar Tugas</span>
                </a>
                <span class="h-11 w-11"></span>
            </div>

            @yield('content')
        </main>
    </div>

    @yield('modals')

    <script>
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebarOverlay');
        var sidebarToggle = document.getElementById('sidebarToggle');
        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            sidebarToggle?.setAttribute('aria-expanded', 'true');
        }
        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            sidebarToggle?.setAttribute('aria-expanded', 'false');
        }
        sidebarToggle?.addEventListener('click', openSidebar);
        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                if (addModal?.classList.contains('is-open')) closeModal();
                else closeSidebar();
                return;
            }
            if (e.key !== 'Tab' || !addModal?.classList.contains('is-open')) return;
            var focusable = addModal.querySelectorAll('button, [href], input:not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])');
            var first = focusable[0];
            var last = focusable[focusable.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        });

        var addModal = document.getElementById('addModal');
        var pageContent = document.getElementById('pageContent');
        var addTrigger = null;
        function openModal() {
            if (!addModal) return;
            addTrigger = document.activeElement;
            addModal.classList.add('is-open');
            addModal.setAttribute('aria-hidden', 'false');
            sidebar.inert = true;
            pageContent.inert = true;
            document.body.style.overflow = 'hidden';
            setTimeout(function () { addModal.querySelector('input:not([type="hidden"]), select, textarea')?.focus(); }, 0);
        }
        function closeModal() {
            if (!addModal) return;
            addModal.classList.remove('is-open');
            addModal.setAttribute('aria-hidden', 'true');
            sidebar.inert = false;
            pageContent.inert = false;
            document.body.style.overflow = '';
            addTrigger?.focus();
        }
        addModal?.addEventListener('click', function (e) { if (e.target === this) closeModal(); });

        // Proof upload modal
        var proofModal = document.getElementById('proofModal');
        var proofForm = document.getElementById('proofForm');
        var proofInput = document.getElementById('proofInput');
        var proofPreview = document.getElementById('proofPreview');
        var proofError = document.getElementById('proofError');
        var proofTaskTitle = document.getElementById('proofTaskTitle');
        var proofTrigger = null;

        function openProofModal(taskId, taskTitle) {
            proofTrigger = document.activeElement;
            proofForm.action = '/tasks/' + taskId + '/done';
            proofTaskTitle.textContent = taskTitle;
            proofInput.value = '';
            proofPreview.innerHTML = '';
            proofError.classList.add('hidden');
            proofModal.classList.add('is-open');
            proofModal.setAttribute('aria-hidden', 'false');
            sidebar.inert = true;
            pageContent.inert = true;
            document.body.style.overflow = 'hidden';
        }
        function closeProofModal() {
            proofModal.classList.remove('is-open');
            proofModal.setAttribute('aria-hidden', 'true');
            sidebar.inert = false;
            pageContent.inert = false;
            document.body.style.overflow = '';
            proofTrigger?.focus();
        }
        proofModal?.addEventListener('click', function (e) { if (e.target === this) closeProofModal(); });

        proofInput?.addEventListener('change', function () {
            proofPreview.innerHTML = '';
            proofError.classList.add('hidden');
            var files = Array.from(this.files);
            if (files.length === 0) return;
            files.forEach(function (file) {
                if (file.size > 5 * 1024 * 1024) {
                    proofError.textContent = 'File "' + file.name + '" melebihi 5MB.';
                    proofError.classList.remove('hidden');
                    return;
                }
                var reader = new FileReader();
                reader.onload = function (e) {
                    var div = document.createElement('div');
                    div.className = 'relative aspect-square overflow-hidden rounded-sm border border-stone-200 dark:border-zinc-700';
                    div.innerHTML = '<img src="' + e.target.result + '" class="h-full w-full object-cover" alt="Preview">';
                    proofPreview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        proofForm?.addEventListener('submit', function (e) {
            if (!proofInput.files || proofInput.files.length === 0) {
                e.preventDefault();
                proofError.textContent = 'Wajib upload minimal satu foto bukti.';
                proofError.classList.remove('hidden');
            }
        });

        function startEdit(id, title, subject, deadline, description) {
            document.getElementById('edit-title-' + id).value = title;
            document.getElementById('edit-subject-' + id).value = subject;
            document.getElementById('edit-deadline-' + id).value = isoToLocalInput(deadline);
            document.getElementById('edit-description-' + id).value = description;
            document.getElementById('edit-row-' + id).classList.add('is-open');
            document.getElementById('edit-title-' + id).focus();
        }
        function cancelEdit(id) { document.getElementById('edit-row-' + id).classList.remove('is-open'); }

        function pad2(n) { return String(n).padStart(2, '0'); }
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

        if (!window._gpsDone && 'geolocation' in navigator) {
            window._gpsDone = true;
            navigator.geolocation.getCurrentPosition(
                function (pos) { window._userCoord = pos.coords; },
                function () {},
                { timeout: 5000, maximumAge: 600000 }
            );
        }

        var exportLink = document.getElementById('exportLink');
        if (exportLink) {
            var tz = getUserTZ();
            if (tz) exportLink.href += (exportLink.href.indexOf('?') >= 0 ? '&' : '?') + 'tz=' + encodeURIComponent(tz);
        }

        document.querySelectorAll('form[action*="/tasks"]').forEach(function (f) {
            var input = f.querySelector('input[name="deadline"]');
            if (input && input.type === 'datetime-local') {
                if (input.value && input.value.includes('T') && input.value.length > 16) {
                    var d = new Date(input.value);
                    if (!isNaN(d.getTime())) {
                        input.value = d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()) +
                            'T' + pad2(d.getHours()) + ':' + pad2(d.getMinutes());
                    }
                }
            }
        });
    </script>
@endsection