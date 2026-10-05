@extends('layouts.app')

@section('title', 'Register - Radar Tugas Kelas')
@section('meta-description', 'Daftar akun Radar Tugas Kelas untuk memantau tugas kelasmu.')
@section('bodyClass', 'bg-[#efe7d8] dark:bg-zinc-950 lg:flex')

@section('body')
    <aside class="book-cover relative hidden lg:flex lg:w-[40%] xl:w-[43%] min-h-[100dvh] overflow-hidden p-12 xl:p-16 text-white">
        <div class="absolute top-0 right-8 bottom-0 w-px bg-white/20"></div>
        <div class="absolute top-0 right-11 bottom-0 w-px bg-black/15"></div>
        <div class="relative z-10 flex w-full flex-col justify-between">
            <a href="{{ route('radar') }}" class="inline-flex w-fit items-center gap-3 rounded-sm border border-white/25 bg-black/10 px-4 py-3 backdrop-blur-sm">
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-10 w-10 object-contain brightness-0 invert">
                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.22em]">Radar Tugas Kelas</span>
            </a>
            <div class="max-w-lg pb-10">
                <p class="font-mono mb-6 text-xs uppercase tracking-[0.3em] text-red-100">Pendaftaran / Siswa</p>
                <h1 class="font-display text-6xl xl:text-7xl font-extrabold leading-[.88] tracking-[-0.04em]">Mulai lebih<br>rapi hari<br>ini.</h1>
                <div class="mt-9 h-1 w-24 bg-white"></div>
                <p class="mt-6 max-w-md text-base leading-7 text-red-50/80">Buat akun sesuai kelasmu. Tugas dari pengurus kelas akan langsung masuk ke agenda.</p>
            </div>
            <div class="flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.2em] text-red-100/70"><span>Akun siswa</span><span>RTK-02</span></div>
        </div>
    </aside>

    <main class="notebook-page relative flex min-h-[100dvh] flex-1 items-center justify-center px-5 py-24 sm:px-10 lg:py-12">
        <a href="{{ route('radar') }}" class="absolute left-5 top-5 inline-flex items-center gap-2 text-stone-600 hover:text-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400 sm:left-10 sm:top-8 lg:hidden">
            <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-8 object-contain">
            <span class="font-display text-lg font-bold">Radar Tugas Kelas</span>
        </a>

        <section class="page-in w-full max-w-[620px]">
            <div class="mb-8 pl-1">
                <div class="mb-3 flex items-center gap-3">
                    <span class="font-mono text-[10px] font-bold uppercase tracking-[0.24em] text-[#be1836] dark:text-rose-400">Lembar pendaftaran</span>
                    <span class="h-px flex-1 bg-stone-300 dark:bg-white/10"></span>
                    <span class="font-mono text-[10px] text-stone-400">02</span>
                </div>
                <h2 class="font-display text-4xl font-extrabold tracking-tight">Tulis identitasmu.</h2>
                <p class="mt-2 text-sm text-stone-600 dark:text-zinc-400">Akun baru otomatis terdaftar sebagai siswa di kelas pilihanmu.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="paper-card relative rounded-sm px-6 pb-7 pt-9 sm:px-8 sm:pb-8">
                @csrf
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-zinc-400">01 / Nama lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Nama lengkap">
                        @error('name') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-zinc-400">02 / Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="namakamu@gmail.com">
                        @error('email') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="username" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-zinc-400">03 / Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autocomplete="username" class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Nama pengguna">
                        @error('username') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="class_name" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-zinc-400">04 / Kelas</label>
                        <select id="class_name" name="class_name" required class="field select-field w-full rounded-sm px-4 py-3 text-sm">
                            <option value="" disabled {{ old('class_name') ? '' : 'selected' }}>Pilih kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class }}" {{ old('class_name') === $class ? 'selected' : '' }}>{{ $class }}</option>
                            @endforeach
                        </select>
                        @error('class_name') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-zinc-400">05 / Password</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password" class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Minimal 6 karakter">
                        @error('password') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] text-stone-500 dark:text-zinc-400">06 / Ulangi password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Ketik ulang password">
                    </div>
                </div>

                <div class="mt-7 flex flex-col-reverse items-center gap-4 sm:flex-row sm:justify-between">
                    <p class="text-center text-xs leading-5 text-stone-500 dark:text-zinc-400 sm:text-left">Pastikan kelas sudah benar<br class="hidden sm:block"> sebelum mendaftar.</p>
                    <button type="submit" class="stamp-button w-full rounded-sm bg-[#be1836] px-8 py-3.5 text-sm font-bold text-white hover:bg-[#a30f2b] sm:w-auto">Daftar Sekarang</button>
                </div>
            </form>

            <div class="mt-8 flex flex-col items-center justify-between gap-3 text-sm sm:flex-row">
                <p class="text-stone-600 dark:text-zinc-400">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-[#be1836] underline decoration-[#be1836]/30 underline-offset-4 hover:decoration-[#be1836] dark:text-rose-400">Masuk</a></p>
                <a href="{{ route('radar') }}" class="font-mono text-[10px] font-bold uppercase tracking-widest text-stone-500 hover:text-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400">Radar publik &rarr;</a>
            </div>
        </section>
    </main>
@endsection