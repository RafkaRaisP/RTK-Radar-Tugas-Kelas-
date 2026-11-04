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
                <p class="mt-6 max-w-md text-base leading-7 text-red-50/80">Cukup isi NIS. Nama dan kelasmu langsung terisi dari data sekolah.</p>
            </div>
            <div class="flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.2em] text-red-100"><span>Akun siswa</span><span>RTK-02</span></div>
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
                    <span class="font-mono text-[10px] txt-muted">02</span>
                </div>
                <h2 class="font-display text-4xl font-extrabold tracking-tight">Tulis identitasmu.</h2>
                <p class="mt-2 text-sm text-stone-600 dark:text-zinc-400">Isi NIS terdaftar — nama & kelas otomatis terisi dari data sekolah.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" id="registerForm" class="paper-card relative rounded-sm px-6 pb-7 pt-9 sm:px-8 sm:pb-8">
                @csrf
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="nis" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] txt-label">01 / NIS</label>
                        <input type="text" inputmode="numeric" pattern="[0-9]{9}" maxlength="9" id="nis" name="nis"
                            value="{{ old('nis') }}" required autofocus autocomplete="off" placeholder="9 digit angka"
                            class="field w-full rounded-sm px-4 py-3 text-sm tabular-nums tracking-[0.2em]">
                        @error('nis') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror

                        <div id="nisResult" class="mt-3 hidden">
                            <div id="nisOk" class="hidden rounded-sm border border-emerald-500/40 bg-emerald-500/10 px-4 py-3">
                                <p class="font-mono text-[9px] font-bold uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-400">Data ditemukan — terkunci</p>
                                <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                    <div>
                                        <p class="font-mono text-[9px] uppercase tracking-widest txt-muted">Nama lengkap</p>
                                        <p id="nisName" class="text-sm font-bold text-stone-700 dark:text-zinc-200"></p>
                                    </div>
                                    <div>
                                        <p class="font-mono text-[9px] uppercase tracking-widest txt-muted">Kelas / Angkatan</p>
                                        <p class="text-sm font-bold text-stone-700 dark:text-zinc-200"><span id="nisClass"></span> <span id="nisYear" class="font-mono text-[10px] txt-muted"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div id="nisErr" class="hidden rounded-sm border border-red-500/40 bg-red-500/10 px-4 py-3 text-xs font-semibold text-red-600 dark:text-rose-400">
                                <span id="nisErrText">NIS tidak ditemukan.</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="email" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] txt-label">02 / Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="namakamu@gmail.com">
                        @error('email') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="username" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] txt-label">03 / Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autocomplete="username" class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Nama pengguna">
                        @error('username') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] txt-label">04 / Password</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password" class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Minimal 6 karakter">
                        @error('password') <p class="mt-2 text-xs font-medium text-red-600 dark:text-rose-400">! {{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="font-mono mb-2 block text-[10px] font-bold uppercase tracking-[0.18em] txt-label">05 / Ulangi password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="field w-full rounded-sm px-4 py-3 text-sm" placeholder="Ketik ulang password">
                    </div>
                </div>

                <div class="mt-7 flex flex-col-reverse items-center gap-4 sm:flex-row sm:justify-between">
                    <p class="text-center text-xs leading-5 txt-label sm:text-left">Nama & kelas mengikuti data NIS<br class="hidden sm:block"> dan tidak bisa diubah manual.</p>
                    <button type="submit" id="registerBtn" class="stamp-button w-full rounded-sm bg-[#be1836] px-8 py-3.5 text-sm font-bold text-white hover:bg-[#a30f2b] sm:w-auto">Daftar Sekarang</button>
                </div>
            </form>

            <div class="mt-8 flex flex-col items-center justify-between gap-3 text-sm sm:flex-row">
                <p class="text-stone-600 dark:text-zinc-400">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-[#be1836] underline decoration-[#be1836]/30 underline-offset-4 hover:decoration-[#be1836] dark:text-rose-400">Masuk</a></p>
                <a href="{{ route('radar') }}" class="font-mono text-[10px] font-bold uppercase tracking-widest txt-label hover:text-[#be1836] dark:text-zinc-400 dark:hover:text-rose-400">Radar publik &rarr;</a>
            </div>
        </section>
    </main>
@endsection

@section('scripts')
    <script>
        (function () {
            var input = document.getElementById('nis');
            var result = document.getElementById('nisResult');
            var okBox = document.getElementById('nisOk');
            var errBox = document.getElementById('nisErr');
            var errText = document.getElementById('nisErrText');
            var form = document.getElementById('registerForm');
            if (!input || !form) return;

            var verified = false;
            var timer = null;
            var seq = 0;

            function showResult(state, data) {
                result.classList.remove('hidden');
                okBox.classList.toggle('hidden', state !== 'ok');
                errBox.classList.toggle('hidden', state !== 'err');
                if (state === 'ok') {
                    document.getElementById('nisName').textContent = data.name;
                    document.getElementById('nisClass').textContent = data.class_name;
                    document.getElementById('nisYear').textContent = data.year ? '/ ' + data.year : '';
                } else if (state === 'err') {
                    errText.textContent = data && data.message ? data.message : 'NIS tidak ditemukan.';
                }
            }

            function lookup() {
                var nis = input.value.trim();
                verified = false;
                if (!/^\d{9}$/.test(nis)) {
                    result.classList.add('hidden');
                    return;
                }
                var mySeq = ++seq;
                fetch('{{ url('students') }}/' + nis + '/check')
                    .then(function (r) {
                        if (r.status === 404 || r.status === 409) return r.json().then(function (d) { return { ok: false, data: d }; });
                        if (!r.ok) return { ok: false, data: { message: 'Gagal mengecek NIS.' } };
                        return r.json().then(function (d) { return { ok: true, data: d }; });
                    })
                    .then(function (res) {
                        if (mySeq !== seq) return;
                        if (res.ok && res.data.found) {
                            verified = true;
                            showResult('ok', res.data);
                        } else {
                            showResult('err', res.data);
                        }
                    })
                    .catch(function () {
                        if (mySeq === seq) showResult('err', { message: 'Gagal mengecek NIS. Coba lagi.' });
                    });
            }

            input.addEventListener('input', function () {
                input.value = input.value.replace(/\D/g, '').slice(0, 9);
                verified = false;
                clearTimeout(timer);
                if (input.value.length === 9) timer = setTimeout(lookup, 350);
                else result.classList.add('hidden');
            });

            if (/^\d{9}$/.test(input.value)) lookup();

            form.addEventListener('submit', function (e) {
                if (!verified && /^\d{9}$/.test(input.value.trim())) lookup();
                if (!verified) {
                    e.preventDefault();
                    input.focus();
                    showResult('err', { message: 'Tunggu hasil pencarian NIS atau periksa kembali NIS-nya.' });
                }
            });
        })();
    </script>
@endsection
