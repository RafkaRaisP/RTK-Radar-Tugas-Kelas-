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

            <div class="flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.2em] text-red-100/70">
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
                    <span class="font-mono text-[10px] text-stone-400">01</span>
                </div>
                <h2 class="font-display text-4xl font-extrabold tracking-tight">Buka agendamu.</h2>
                <p class="mt-2 text-sm text-stone-600 dark:text-zinc-400">Masukkan identitas akun dan kelas yang terdaftar.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" autocomplete="off" class="paper-card relative space-y-5 rounded-sm px-6 pb-7 pt-9 sm:px-8 sm:pb-8">
                @csrf

                <div>
                    <label for="username" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-zinc-400">01 / Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus autocomplete="off"
                        class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Nama pengguna">
                    @error('username')
                        <p class="mt-2 flex items-center gap-2 text-xs font-medium text-red-600 dark:text-rose-400"><span aria-hidden="true">!</span>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-zinc-400">02 / Password</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Kata sandi">
                    @error('password')
                        <p class="mt-2 flex items-center gap-2 text-xs font-medium text-red-600 dark:text-rose-400"><span aria-hidden="true">!</span>{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="class_name" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-zinc-400">03 / Kelas</label>
                    <select id="class_name" name="class_name" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                        <option value="" disabled {{ old('class_name') ? '' : 'selected' }}>Pilih kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class }}" {{ old('class_name') === $class ? 'selected' : '' }}>{{ $class }}</option>
                        @endforeach
                    </select>
                    @error('class_name')
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
                <a href="{{ route('radar') }}" class="font-mono text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:text-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400">Radar publik &rarr;</a>
            </div>
        </section>
    </main>
@endsection