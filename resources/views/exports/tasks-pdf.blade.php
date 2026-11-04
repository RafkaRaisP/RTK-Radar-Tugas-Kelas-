<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Daftar Tugas — Radar Tugas Kelas</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #292524;
            font-size: 9pt;
            margin: 0;
        }

        /* ---- Kop sekolah (gaya buku agenda: garis tebal merah + tipis hitam) ---- */
        .kop { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .kop td { vertical-align: middle; padding: 0; }
        .kop .logo { width: 64px; padding-right: 12px; }
        .kop .logo img { width: 58px; height: 58px; }
        .school-name {
            font-size: 15pt;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #1c1917;
            margin: 0;
            line-height: 1.1;
        }
        .school-meta {
            font-family: "Courier New", Courier, monospace;
            font-size: 7pt;
            color: #57534e;
            line-height: 1.45;
            margin: 3px 0 0;
        }
        .rule-red { height: 3px; background: #be1836; margin-top: 2px; }
        .rule-black { height: 1px; background: #1c1917; margin-top: 1.5px; }

        /* ---- Judul laporan ---- */
        .title-block { margin: 14px 0 10px; }
        .eyebrow {
            font-family: "Courier New", Courier, monospace;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #be1836;
        }
        h1 { font-size: 16pt; font-weight: 800; margin: 3px 0 4px; letter-spacing: -0.4px; }
        .meta-table { width: 100%; border-collapse: collapse; font-size: 8pt; color: #44403c; }
        .meta-table td { padding: 1.5px 0; vertical-align: top; }
        .meta-table .label {
            font-family: "Courier New", Courier, monospace;
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #78716c;
            width: 78px;
        }
        .meta-table .value { font-weight: bold; color: #292524; }
        .title-rule { border-top: 2px solid #1c1917; margin-top: 8px; }

        /* ---- Tabel tugas ---- */
        table.tasks { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.tasks th {
            background: #be1836;
            color: #ffffff;
            font-family: "Courier New", Courier, monospace;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
            padding: 6px 6px;
            border: 1px solid #9c1229;
        }
        table.tasks td {
            padding: 5px 6px;
            border: 1px solid #ddd3c1;
            font-size: 8pt;
            vertical-align: top;
        }
        table.tasks tr:nth-child(even) td { background: #faf6ee; }
        table.tasks .no {
            text-align: center;
            font-family: "Courier New", Courier, monospace;
            color: #78716c;
            width: 24px;
        }
        .col-title { width: 24%; font-weight: bold; color: #1c1917; }
        .col-subject { width: 14%; }
        .col-desc { width: 22%; color: #57534e; }
        .col-class {
            width: 13%;
            font-family: "Courier New", Courier, monospace;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .col-deadline { width: 13%; font-family: "Courier New", Courier, monospace; font-size: 7.5pt; white-space: nowrap; }
        .col-status { width: 10%; text-align: center; }
        .badge {
            display: inline-block;
            font-family: "Courier New", Courier, monospace;
            font-size: 6.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 2px 5px;
            border: 1px solid;
        }
        .badge-active { color: #047857; border-color: #047857; background: #ecfdf5; }
        .badge-late { color: #be1836; border-color: #be1836; background: #fdf2f4; }
        .empty { text-align: center; padding: 24px 0; color: #78716c; font-style: italic; }

        /* ---- Tanda tangan ---- */
        .sign-area { width: 100%; margin-top: 26px; border-collapse: collapse; }
        .sign-area td { width: 50%; vertical-align: top; font-size: 8.5pt; }
        .sign-area .place-date { margin-bottom: 42px; color: #44403c; }
        .sign-area .sign-name { font-weight: bold; color: #1c1917; text-decoration: underline; }
        .sign-area .sign-role {
            font-family: "Courier New", Courier, monospace;
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #78716c;
            margin-top: 2px;
        }
        .footer-note {
            margin-top: 18px;
            padding-top: 5px;
            border-top: 1px solid #ddd3c1;
            font-family: "Courier New", Courier, monospace;
            font-size: 6.5pt;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #a8a29e;
        }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            <td class="logo"><img src="{{ public_path('images/logo.png') }}" alt="Logo"></td>
            <td>
                <p class="school-name">SMK TELEKOMUNIKASI TELESANDI BEKASI</p>
                <p class="school-meta">
                    Jl. Mekarsari Raya, KH. Mochammad Mekarsari — Tambun Selatan, Kab. Bekasi, Jawa Barat 17510<br>
                    Telp/Fax: 021-88332404 / 021-88323429 &nbsp;&bull;&nbsp; telesandismk@gmail.com &nbsp;&bull;&nbsp; www.smkstelekomunikasitelesandi.sch.id
                </p>
            </td>
        </tr>
    </table>
    <div class="rule-red"></div>
    <div class="rule-black"></div>

    <div class="title-block">
        <span class="eyebrow">Laporan agenda / RTK-EXP</span>
        <h1>Daftar Tugas Kelas</h1>
        <table class="meta-table">
            <tr>
                <td class="label">Cakupan</td>
                <td class="value">{{ $scopeLabel }}</td>
                <td class="label">Tahun Ajaran</td>
                <td class="value">{{ $year?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Dicetak</td>
                <td class="value">{{ $printedAt->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</td>
                <td class="label">Oleh</td>
                <td class="value">{{ $user->name }} ({{ \Illuminate\Support\Str::ucfirst(str_replace('_', ' ', $user->role)) }})</td>
            </tr>
            <tr>
                <td class="label">Jumlah</td>
                <td class="value" colspan="3">{{ $tasks->count() }} tugas</td>
            </tr>
        </table>
        <div class="title-rule"></div>
    </div>

    <table class="tasks">
        <thead>
            <tr>
                <th class="no">#</th>
                <th>Judul</th>
                <th>Mapel</th>
                <th>Deskripsi</th>
                <th>Kelas</th>
                <th>Deadline</th>
                <th style="text-align:center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $i => $task)
                @php $late = now()->gt($task->deadline); @endphp
                <tr>
                    <td class="no">{{ $i + 1 }}</td>
                    <td class="col-title">{{ $task->title }}</td>
                    <td class="col-subject">{{ $task->subject }}</td>
                    <td class="col-desc">{{ \Illuminate\Support\Str::limit($task->description ?? '—', 70) }}</td>
                    <td class="col-class">{{ $task->class_name }}</td>
                    <td class="col-deadline">{{ $task->deadline->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                    <td class="col-status">
                        <span class="badge {{ $late ? 'badge-late' : 'badge-active' }}">{{ $late ? 'Terlewat' : 'Aktif' }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Belum ada tugas untuk cakupan ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="sign-area">
        <tr>
            <td></td>
            <td>
                <div class="place-date">Bekasi, {{ $printedAt->timezone('Asia/Jakarta')->format('d F Y') }}</div>
                <div>{{ $user->name }}</div>
                <div class="sign-role">{{ \Illuminate\Support\Str::ucfirst(str_replace('_', ' ', $user->role)) }} — Radar Tugas Kelas</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">Radar Tugas Kelas &bull; RTK-EXP &bull; {{ $printedAt->timezone('Asia/Jakarta')->format('Y-m-d H:i') }} WIB</div>
</body>
</html>
