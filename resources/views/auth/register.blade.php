<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Radar Tugas Kelas</title>
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
            pointer-events: none; opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
            background-repeat: repeat;
            background-size: 200px 200px;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .anim-in {
            opacity: 0;
            animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes meshFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -20px) scale(1.05); }
            66% { transform: translate(-20px, 15px) scale(0.95); }
        }
        .mesh-blob {
            animation: meshFloat 12s ease-in-out infinite;
            filter: blur(80px);
        }
        .mesh-blob-2 {
            animation: meshFloat 15s ease-in-out infinite reverse;
            filter: blur(100px);
        }

        .glass-form {
            background: rgba(24, 24, 27, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.05),
                0 20px 60px -15px rgba(0, 0, 0, 0.5);
        }

        .input-field {
            background: rgba(24, 24, 27, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: border-color 0.25s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .input-field:focus {
            outline: none;
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.08);
        }

        .select-field {
            -webkit-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2371717a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 32px;
        }

        .btn-primary {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-primary:active {
            transform: scale(0.98) translateY(1px);
        }
    </style>
</head>
<body class="min-h-[100dvh] bg-zinc-950 text-zinc-100 flex">
    <div class="grain"></div>

    {{-- Sisi kiri: tampilan brand (hanya muncul di layar besar) --}}
    <div class="hidden lg:flex lg:w-[55%] relative overflow-hidden items-end p-12">
        <div class="absolute inset-0 bg-zinc-900">
            <div class="mesh-blob absolute top-1/4 left-1/4 w-[500px] h-[500px] rounded-full bg-emerald-500/10"></div>
            <div class="mesh-blob-2 absolute bottom-1/3 right-1/4 w-[400px] h-[400px] rounded-full bg-emerald-400/5"></div>
        </div>
        <div class="relative z-10 anim-in" style="animation-delay: 200ms">
            <p class="font-mono text-xs text-emerald-400/70 tracking-widest uppercase mb-4">Deadline Tracker</p>
            <h1 class="font-display text-5xl xl:text-6xl font-bold tracking-tight leading-[0.95] mb-6">
                Radar<br>Tugas<br>Kelas
            </h1>
            <p class="text-zinc-400 text-base leading-relaxed max-w-[40ch]">
                Pantau semua deadline tugas kelas dalam satu tampilan real-time. Tidak ada tugas yang terlewat.
            </p>
        </div>
    </div>

    {{-- Sisi kanan: form pendaftaran --}}
        <div class="w-full lg:w-[45%] flex items-center justify-center px-6 pt-20 pb-8 sm:pt-24 sm:pb-12 relative">
        {{-- Mobile header --}}
        <div class="lg:hidden absolute top-8 left-6 right-6 anim-in" style="animation-delay: 100ms">
            <p class="font-mono text-[10px] text-emerald-400/70 tracking-widest uppercase mb-1">Deadline Tracker</p>
            <p class="font-display text-lg font-bold tracking-tight">Radar Tugas Kelas</p>
        </div>

        <div class="w-full max-w-[380px]">
            <div class="mb-10 anim-in" style="animation-delay: 150ms">
                <h2 class="font-display text-2xl font-bold tracking-tight mb-1.5">Daftar</h2>
                <p class="text-zinc-500 text-sm">Buat akun untuk melihat tugas kelas</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="glass-form rounded-[1.5rem] p-5 sm:p-8 space-y-5">
                @csrf

                <div class="anim-in" style="animation-delay: 250ms">
                    <label for="name" class="block text-xs font-medium text-zinc-400 mb-1.5 tracking-wide uppercase">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="input-field w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-600">
                    @error('name')
                        <p class="text-red-400/90 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="anim-in" style="animation-delay: 300ms">
                    <label for="username" class="block text-xs font-medium text-zinc-400 mb-1.5 tracking-wide uppercase">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                        class="input-field w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-600">
                    @error('username')
                        <p class="text-red-400/90 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="anim-in" style="animation-delay: 350ms">
                    <label for="class_name" class="block text-xs font-medium text-zinc-400 mb-1.5 tracking-wide uppercase">Kelas</label>
                    <select id="class_name" name="class_name" required
                        class="input-field select-field w-full px-4 py-3 rounded-xl text-sm text-white">
                        <option value="" disabled {{ old('class_name') ? '' : 'selected' }}>Pilih kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}" {{ old('class_name') === $class ? 'selected' : '' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                    @error('class_name')
                        <p class="text-red-400/90 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="anim-in" style="animation-delay: 400ms">
                    <label for="password" class="block text-xs font-medium text-zinc-400 mb-1.5 tracking-wide uppercase">Password</label>
                    <input type="password" id="password" name="password" required
                        class="input-field w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-600">
                    @error('password')
                        <p class="text-red-400/90 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="anim-in" style="animation-delay: 450ms">
                    <label for="password_confirmation" class="block text-xs font-medium text-zinc-400 mb-1.5 tracking-wide uppercase">Ulangi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="input-field w-full px-4 py-3 rounded-xl text-sm text-white placeholder-zinc-600">
                </div>

                <div class="pt-1 anim-in" style="animation-delay: 500ms">
                    <button type="submit"
                        class="btn-primary w-full py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-sm font-semibold tracking-wide">
                        Daftar
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center anim-in" style="animation-delay: 550ms">
                <p class="text-zinc-500 text-xs tracking-wide">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-emerald-400/80 hover:text-emerald-300 transition-colors duration-200">Masuk</a>
                </p>
            </div>

            <div class="mt-3 text-center anim-in" style="animation-delay: 600ms">
                <a href="{{ route('radar') }}" class="text-zinc-500 hover:text-zinc-300 text-xs tracking-wide transition-colors duration-200">
                    Lihat Radar Publik
                </a>
            </div>
        </div>
    </div>
</body>
</html>
