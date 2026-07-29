<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Radar Tugas Kelas</title>
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

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .flash-msg {
            animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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

        .input-field {
            background: rgba(24, 24, 27, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.07);
            transition: border-color 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .input-field:focus {
            outline: none;
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.06);
        }

        .btn-primary {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-primary:active {
            transform: scale(0.98) translateY(1px);
        }

        .btn-ghost {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-ghost:active {
            transform: scale(0.98);
        }

        .modal-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 100;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
        .modal-overlay.is-open {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-panel {
            animation: fadeSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .edit-panel {
            display: none;
            animation: slideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .edit-panel.is-open {
            display: block;
        }

        .glass-surface {
            background: rgba(24, 24, 27, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
        }
    </style>
</head>
<body class="min-h-[100dvh] bg-zinc-950 text-zinc-100">
    <div class="grain"></div>

    <div class="max-w-7xl mx-auto px-5 sm:px-8 pt-10 pb-16">

        {{-- Header: sambutan user, tombol tambah tugas, radar publik, logout --}}
        <header class="flex flex-col sm:flex-row sm:items-end justify-between gap-5 mb-10 anim-in" style="animation-delay: 100ms">
            <div>
                <p class="font-mono text-[10px] text-emerald-400/70 tracking-widest uppercase mb-2">Dashboard</p>
                <h1 class="font-display text-3xl sm:text-4xl font-bold tracking-tight leading-none">
                    Halo, {{ Str::before(Auth::user()->name, ' ') }}
                </h1>
                <p class="mt-1.5 flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-sm text-zinc-400">{{ Auth::user()->class_name }}</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide {{ match(Auth::user()->role) { 'ketua_kelas' => 'bg-emerald-500/15 text-emerald-300', 'sekretaris' => 'bg-violet-500/15 text-violet-300', default => 'bg-zinc-500/15 text-zinc-300' } }}">
                        {{ match(Auth::user()->role) { 'ketua_kelas' => 'Ketua Kelas', 'sekretaris' => 'Sekretaris', default => 'Siswa' } }}
                    </span>
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @if(Auth::user()->role !== 'siswa')
                    <button onclick="openModal()"
                        class="btn-primary bg-emerald-500 hover:bg-emerald-400 text-zinc-950 w-11 h-11 rounded-xl flex items-center justify-center text-lg font-bold">
                        +
                    </button>
                @endif
                <a href="{{ route('radar') }}"
                    class="btn-ghost glass-surface text-zinc-400 hover:text-zinc-200 px-4 py-3 sm:py-2.5 rounded-xl text-sm inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5"/>
                    </svg>
                    Radar Publik
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="btn-ghost bg-red-500/10 hover:bg-red-500/20 text-red-400 px-4 py-3 sm:py-2.5 rounded-xl text-sm font-medium transition-colors">
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        @if(session('success'))
            <div class="flash-msg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-5 py-3 rounded-xl mb-8 text-sm font-medium flex items-center gap-2.5">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="h-px bg-white/[0.04] mb-10"></div>

        {{-- Daftar Tugas: looping semua tugas, ada countdown real-time + tombol edit/hapus untuk pengurus kelas --}}
        <section class="anim-in" style="animation-delay: 200ms">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                </div>
                <h2 class="font-display text-lg font-bold tracking-tight">Daftar Tugas</h2>
                <span class="font-mono text-xs text-zinc-600">{{ $tasks->count() }} tugas</span>
            </div>

            @php $ownClass = Auth::user()->class_name; $canEdit = Auth::user()->role !== 'siswa'; @endphp

            @if($tasks->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 sm:py-24">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-zinc-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                        </svg>
                    </div>
                    <p class="font-display text-base font-semibold text-zinc-400 mb-1">Belum ada tugas</p>
                    <p class="text-zinc-600 text-sm">Tugas yang ditambahkan pengurus kelas akan muncul di sini.</p>
                </div>
            @else
                <div id="taskGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                    @foreach($tasks as $task)
                        @php $completed = in_array($task->id, $completedIds); @endphp
                        <div class="task-card rounded-[1.25rem] p-4 sm:p-6 border relative overflow-hidden task-card-item
                            {{ $completed
                                ? 'bg-zinc-900/30 border-zinc-700/30 task-completed'
                                : 'bg-zinc-900/60 border-white/[0.04]' }}"
                             data-deadline="{{ $task->deadline->toIso8601String() }}"
                             data-completed="{{ $completed ? '1' : '0' }}"
                             style="animation-delay: {{ 150 + ($loop->index * 70) }}ms">
                            <div class="task-accent-line absolute top-0 left-0 right-0 h-[2px] {{ $completed ? 'bg-zinc-600/40' : 'bg-emerald-500/40' }} rounded-full"></div>

                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="status-dot w-1.5 h-1.5 rounded-full task-status-dot {{ $completed ? 'bg-zinc-500' : 'bg-emerald-400' }}"></span>
                                    <span class="text-[11px] font-medium {{ $completed ? 'text-zinc-500' : (strtolower($task->class_name) === strtolower($ownClass) ? 'text-emerald-400/80' : 'text-zinc-500') }}">{{ $task->class_name }}</span>
                                </div>
                                @if($completed)
                                    <span class="inline-flex items-center gap-1 text-xs text-zinc-500 font-medium">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        Selesai
                                    </span>
                                @else
                                    <span class="countdown font-mono text-xs font-medium text-emerald-400/80 tabular-nums">--</span>
                                @endif
                            </div>

                            <h3 class="font-display text-base font-bold tracking-tight mb-1 leading-snug {{ $completed ? 'text-zinc-400 line-through' : '' }}">{{ $task->title }}</h3>
                            <p class="text-sm {{ $completed ? 'text-zinc-600' : 'text-zinc-500' }} mb-2">{{ $task->subject }}</p>

                            @if($task->description)
                                <p class="text-xs {{ $completed ? 'text-zinc-600' : 'text-zinc-600' }} mb-4 leading-relaxed">{{ Str::limit($task->description, 72) }}</p>
                            @else
                                <div class="mb-4"></div>
                            @endif

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-[11px] {{ $completed ? 'text-zinc-600' : 'text-zinc-600' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                                    </svg>
                                    <span class="font-mono tabular-nums {{ $completed ? 'line-through' : '' }}">{{ $task->deadline->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <form method="POST" action="{{ route('tasks.complete', $task) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="btn-ghost {{ $completed ? 'text-emerald-500/70 hover:text-emerald-400' : 'text-zinc-500 hover:text-emerald-400' }} px-3 py-2 rounded-lg text-xs font-medium transition-colors min-h-[36px]">
                                            {{ $completed ? 'Batal' : 'Tandai' }}
                                        </button>
                                    </form>
                                    @if($canEdit && strtolower($task->class_name) === strtolower($ownClass) && !$completed)
                                        <button onclick="startEdit({{ $task->id }}, @js($task->title), @js($task->subject), '{{ $task->deadline->format('Y-m-d\TH:i') }}', @js($task->description ?? ''))"
                                            class="btn-ghost text-zinc-500 hover:text-emerald-400 px-3 py-2 rounded-lg text-xs font-medium transition-colors min-h-[36px]">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline"
                                            onsubmit="return confirm('Hapus tugas ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="btn-ghost text-zinc-500 hover:text-red-400 px-3 py-2 rounded-lg text-xs font-medium transition-colors min-h-[36px]">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($canEdit && strtolower($task->class_name) === strtolower($ownClass) && !$completed)
                            <div class="edit-panel col-span-1 md:col-span-2 lg:col-span-3" id="edit-row-{{ $task->id }}">
                                <form method="POST" action="{{ route('tasks.update', $task) }}" class="bg-zinc-900/60 rounded-xl p-4 border border-white/[0.04] mb-5">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">
                                        <input type="text" name="title" id="edit-title-{{ $task->id }}" required
                                            class="input-field w-full px-3 py-2.5 rounded-lg text-sm text-white">
                                        <input type="text" name="subject" id="edit-subject-{{ $task->id }}" required
                                            class="input-field w-full px-3 py-2.5 rounded-lg text-sm text-white">
                                        <input type="datetime-local" name="deadline" id="edit-deadline-{{ $task->id }}" required
                                            class="input-field w-full px-3 py-2.5 rounded-lg text-sm text-white">
                                        <input type="text" name="description" id="edit-description-{{ $task->id }}"
                                            class="input-field w-full px-3 py-2.5 rounded-lg text-sm text-white placeholder-zinc-600">
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="btn-primary bg-emerald-500 hover:bg-emerald-400 text-zinc-950 px-4 py-2.5 sm:py-1.5 rounded-lg text-xs font-semibold">Simpan</button>
                                        <button type="button" onclick="cancelEdit({{ $task->id }})" class="btn-ghost bg-zinc-800 hover:bg-zinc-700 text-zinc-400 px-4 py-2.5 sm:py-1.5 rounded-lg text-xs">Batal</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    {{-- Modal Tambah Tugas: muncul saat tombol + diklik (hanya untuk ketua kelas & sekretaris) --}}
    @if(Auth::user()->role !== 'siswa')
        <div class="modal-overlay" id="addModal">
            <div class="modal-panel w-full max-w-lg mx-4 bg-zinc-900 rounded-2xl border border-white/[0.06] p-5 sm:p-6 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-display text-lg font-bold tracking-tight">Tambah Tugas Baru</h3>
                    <button onclick="closeModal()" class="text-zinc-500 hover:text-zinc-300 transition-colors">
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
                                    class="input-field w-full px-4 py-3 sm:py-2.5 rounded-xl text-sm text-white placeholder-zinc-600">
                                @error('title') <p class="text-red-400/80 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-zinc-500 mb-1.5 tracking-wide uppercase">Mata Pelajaran</label>
                                <input type="text" name="subject" required value="{{ old('subject') }}"
                                    class="input-field w-full px-4 py-3 sm:py-2.5 rounded-xl text-sm text-white placeholder-zinc-600">
                            @error('subject') <p class="text-red-400/80 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-medium text-zinc-500 mb-1.5 tracking-wide uppercase">Deadline</label>
                                <input type="datetime-local" name="deadline" required value="{{ old('deadline') }}"
                                    class="input-field w-full px-4 py-3 sm:py-2.5 rounded-xl text-sm text-white placeholder-zinc-600">
                                @error('deadline') <p class="text-red-400/80 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-medium text-zinc-500 mb-1.5 tracking-wide uppercase">Deskripsi <span class="text-zinc-700 normal-case">(opsional)</span></label>
                                <input type="text" name="description" value="{{ old('description') }}"
                                    class="input-field w-full px-4 py-3 sm:py-2.5 rounded-xl text-sm text-white placeholder-zinc-600">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" onclick="closeModal()"
                            class="btn-ghost bg-zinc-800 hover:bg-zinc-700 text-zinc-400 px-5 py-3 sm:py-2.5 rounded-xl text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn-primary inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 px-6 py-3 sm:py-2.5 rounded-xl text-sm font-semibold tracking-wide">
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

        function startEdit(id, title, subject, deadline, description) {
            document.getElementById('edit-title-' + id).value = title;
            document.getElementById('edit-subject-' + id).value = subject;
            document.getElementById('edit-deadline-' + id).value = deadline;
            document.getElementById('edit-description-' + id).value = description;
            document.getElementById('edit-row-' + id).classList.add('is-open');
        }
        function cancelEdit(id) {
            document.getElementById('edit-row-' + id).classList.remove('is-open');
        }

        function updateCountdowns() {
            const now = Date.now();
            document.querySelectorAll('.task-card-item').forEach(card => {
                if (card.dataset.completed === '1') return;

                const deadline = new Date(card.dataset.deadline).getTime();
                const diff = deadline - now;
                const el = card.querySelector('.countdown');
                const line = card.querySelector('.task-accent-line');
                const dot = card.querySelector('.task-status-dot');

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

        updateCountdowns();
        setInterval(updateCountdowns, 1000);
    </script>
</body>
</html>
