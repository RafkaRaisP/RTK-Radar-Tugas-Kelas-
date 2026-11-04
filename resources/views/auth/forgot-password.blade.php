@extends('layouts.app')

@section('title', 'Lupa Password - Radar Tugas Kelas')
@section('meta-description', 'Atur ulang password akun Radar Tugas Kelas melalui email.')
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
                <p class="font-mono mb-6 text-xs uppercase tracking-[0.3em] text-red-100">Pemulihan / 2026</p>
                <h1 class="font-display text-6xl xl:text-7xl font-extrabold leading-[.88] tracking-[-0.04em]">Kembali ke<br>agendamu.</h1>
                <div class="mt-9 h-1 w-24 bg-white"></div>
                <p class="mt-6 max-w-md text-base leading-7 text-red-50/80">Lupa password? Tenang, kami kirim tautan aman ke email terdaftar untuk membuat password baru.</p>
            </div>
            <div class="flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.2em] text-red-100"><span>Reset password</span><span>RTK-03</span></div>
        </div>
    </aside>

    <main class="notebook-page relative flex min-h-[100dvh] flex-1 items-center justify-center px-5 py-24 sm:px-10 lg:py-12">
        <a href="{{ route('radar') }}" class="absolute left-5 top-5 inline-flex items-center gap-2 text-stone-600 hover:text-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400 sm:left-10 sm:top-8 lg:hidden">
            <img src="{{ asset('images/logo.png') }}" alt="" class="h-8 w-8 object-contain">
            <span class="font-display text-lg font-bold">Radar Tugas Kelas</span>
        </a>

        <section class="page-in w-full max-w-[430px]">
            <div class="mb-8 pl-1">
                <div class="mb-3 flex items-center gap-3">
                    <span class="font-mono text-[10px] font-bold uppercase tracking-[0.24em] text-[#be1836] dark:text-rose-400">Pulihkan akun</span>
                    <span class="h-px flex-1 bg-stone-300 dark:bg-white/10"></span>
                    <span class="font-mono text-[10px] txt-muted">03</span>
                </div>
                <h2 class="font-display text-4xl font-extrabold tracking-tight">Lupa password?</h2>
                <p class="mt-2 text-sm text-stone-600 dark:text-zinc-400">Masukkan email terdaftar, kami kirim tautan untuk mengatur ulang.</p>
            </div>

            <form method="POST" action="{{ route('password.email') }}" class="paper-card relative space-y-5 rounded-sm px-6 pb-7 pt-9 sm:px-8 sm:pb-8">
                @csrf

                @if (session('status'))
                    <div class="rounded-sm border border-green-600/30 bg-green-600/10 px-4 py-3 text-sm text-green-700 dark:text-green-400">! {{ session('status') }}</div>
                @endif

                <div>
                    <label for="email" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] txt-label">01 / Email terdaftar</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                        class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="namakamu@gmail.com">
                    @error('email')
                        <p class="mt-2 flex items-center gap-2 text-xs font-medium text-red-600 dark:text-rose-400"><span aria-hidden="true">!</span>{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="stamp-button w-full rounded-sm bg-[#be1836] px-5 py-3.5 text-sm font-bold text-white hover:bg-[#a30f2b]">
                    Kirim Tautan Reset
                </button>
            </form>

            <div class="mt-8 flex flex-col items-center justify-between gap-3 text-sm sm:flex-row">
                <p class="text-stone-600 dark:text-zinc-400">Ingat password? <a href="{{ route('login') }}" class="font-bold text-[#be1836] underline decoration-[#be1836]/30 underline-offset-4 hover:decoration-[#be1836] dark:text-rose-400">Masuk</a></p>
                <a href="{{ route('radar') }}" class="font-mono text-[10px] font-bold uppercase tracking-widest txt-label hover:text-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400">Radar publik &rarr;</a>
            </div>
        </section>
    </main>
@endsection