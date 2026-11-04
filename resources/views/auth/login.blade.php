@extends('layouts.app')

@section('title', 'Login - Radar Tugas Kelas')
@section('meta-description', 'Masuk ke Radar Tugas Kelas untuk mengelola dan memantau tugas kelas.')
@section('bodyClass', 'bg-[#efe7d8] dark:bg-zinc-950 lg:flex')

@section('body')
    <aside class="book-cover relative hidden lg:flex lg:w-[43%] xl:w-[46%] min-h-[100dvh] overflow-hidden p-12 xl:p-16 text-white">
        <div class="absolute top-0 right-8 bottom-0 w-px bg-white/20"></div>
        <div class="absolute top-0 right-11 bottom-0 w-px bg-black/15"></div>
        <div class="relative z-10 flex w-full flex-col justify-between">
            <a href="{{ route('radar') }}" class="inline-flex w-fit items-center gap-3 rounded-sm border border-white/25 bg-black/10 px-4 py-3 backdrop-blur-sm">
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-10 w-10 object-contain brightness-0 invert">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.22em]">Radar Tugas Kelas</span>
            </a>

            <div class="max-w-lg pb-10">
                <p class="font-mono mb-6 text-xs uppercase tracking-[0.3em] text-red-100">Buku Agenda / 2026</p>
                <h1 class="font-display text-6xl xl:text-7xl font-extrabold leading-[.88] tracking-[-0.04em]">
                    Semua tugas.<br>Dalam satu<br>catatan.
                </h1>
                <div class="mt-9 h-1 w-24 bg-white"></div>
                <p class="mt-6 max-w-md text-base leading-7 text-red-50/80">
                    Pantau deadline kelas secara real-time dan beri tanda saat tugasmu sudah selesai.
                </p>
            </div>

            <div class="flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.2em] text-red-100">
                <span>Deadline tracker</span>
                <span>RTK-01</span>
            </div>
        </div>
    </aside>

    <main class="notebook-page relative flex min-h-[100dvh] flex-1 items-center justify-center px-5 py-24 sm:px-10 lg:py-12">
        <a href="{{ route('radar') }}" class="absolute left-5 top-5 inline-flex items-center gap-2 text-stone-600 transition-colors hover:text-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400 sm:left-10 sm:top-8 lg:hidden">
            <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-8 object-contain">
            <span class="font-display text-lg font-bold">Radar Tugas Kelas</span>
        </a>

        <section class="page-in w-full max-w-[430px]">
            <div class="mb-8 pl-1">
                <div class="mb-3 flex items-center gap-3">
                    <span class="font-mono text-[10px] font-bold uppercase tracking-[0.24em] text-[#be1836] dark:text-rose-400">Halaman masuk</span>
                    <span class="h-px flex-1 bg-stone-300 dark:bg-white/10"></span>
                    <span class="font-mono text-[10px] txt-muted">01</span>
                </div>
                <h2 class="font-display text-4xl font-extrabold tracking-tight">Buka agendamu.</h2>
                <p class="mt-2 text-sm text-stone-600 dark:text-zinc-400">Masukkan username dan password akunmu.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="paper-card relative space-y-5 rounded-sm px-6 pb-7 pt-9 sm:px-8 sm:pb-8">
                @csrf

                @if (session('status'))
                    <div class="rounded-sm border border-green-600/30 bg-green-600/10 px-4 py-3 text-sm text-green-700 dark:text-green-400">{{ session('status') }}</div>
                @endif

                <div>
                    <label for="username" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] txt-label">01 / Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus autocomplete="username"
                        class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Nama pengguna">
                    @error('username')
                        <p class="mt-2 flex items-center gap-2 text-xs font-medium text-red-600 dark:text-rose-400"><span aria-hidden="true">!</span>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] txt-label">02 / Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            class="field w-full rounded-sm px-4 py-3 pr-12 text-sm" placeholder="Kata sandi">
                        <button type="button" id="togglePassword" aria-label="Tampilkan kata sandi" aria-pressed="false"
                            class="pressable absolute right-1 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-sm txt-label hover:text-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400">
                            <svg id="eyeOpen" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg id="eyeOff" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 flex items-center gap-2 text-xs font-medium text-red-600 dark:text-rose-400"><span aria-hidden="true">!</span>{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex w-fit cursor-pointer items-center gap-2.5 text-sm text-stone-600 dark:text-zinc-400">
                    <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded-sm border-stone-400 text-[#be1836] focus:ring-[#be1836]/20">
                    <span>Ingat saya di perangkat ini</span>
                </label>

                <button type="submit" class="stamp-button w-full rounded-sm bg-[#be1836] px-5 py-3.5 text-sm font-bold text-white hover:bg-[#a30f2b]">
                    Masuk ke Dashboard
                </button>

                <div class="text-center">
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-stone-600 underline decoration-[#be1836]/30 underline-offset-4 transition-colors hover:text-[#be1836] hover:decoration-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400">Lupa password?</a>
                </div>
            </form>

            <div class="mt-8 flex flex-col items-center justify-between gap-3 text-sm sm:flex-row">
                <p class="text-stone-600 dark:text-zinc-400">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-[#be1836] underline decoration-[#be1836]/30 underline-offset-4 hover:decoration-[#be1836] dark:text-rose-400">Daftar</a></p>
                <a href="{{ route('radar') }}" class="font-mono text-[10px] font-bold uppercase tracking-widest txt-label hover:text-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400">Radar publik &rarr;</a>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        (function () {
            var btn = document.getElementById('togglePassword');
            var input = document.getElementById('password');
            if (!btn || !input) return;
            btn.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.setAttribute('aria-pressed', String(show));
                btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                document.getElementById('eyeOpen').classList.toggle('hidden', show);
                document.getElementById('eyeOff').classList.toggle('hidden', !show);
                input.focus();
            });
        })();
    </script>
@endsection