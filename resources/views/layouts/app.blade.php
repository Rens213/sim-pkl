<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIM-PKL') | SMK ICB Cinta Niaga Bandung</title>
    <meta name="description" content="Portal pengajuan dan penempatan PKL siswa SMK ICB Cinta Niaga Bandung.">
    <meta name="theme-color" content="#0a6a55">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='7' fill='%230a6a55'/%3E%3Ctext x='16' y='23' font-size='18' font-family='sans-serif' font-weight='700' text-anchor='middle' fill='white'%3EP%3C/text%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-paper font-sans text-base text-ink antialiased">
<a href="#isi" class="skip">Lewati ke konten</a>

@php
    $user = auth()->user();
    $menu = match ($user?->role) {
        'siswa' => [['siswa.katalog', 'Katalog PKL', 'siswa.katalog,siswa.ajukan*'], ['siswa.pengajuan.index', 'Pengajuan saya', 'siswa.pengajuan.*']],
        'admin_hubin' => [['admin.pengajuan.index', 'Pengajuan', 'admin.pengajuan.*'], ['admin.perusahaan.index', 'Perusahaan', 'admin.perusahaan.*']],
        'perusahaan' => [['mitra.pelamar', 'Pelamar', 'mitra.*']],
        default => [],
    };
@endphp

<header class="border-b border-line">
    <div class="wrap flex flex-wrap items-center justify-between gap-x-6 gap-y-1 py-2">
        <a href="{{ route('home') }}" class="flex min-h-11 items-center gap-3 no-underline">
            <span class="grid size-9 place-items-center rounded-lg bg-brand text-sm font-bold text-white" aria-hidden="true">P</span>
            <span class="leading-tight"><strong class="block">SIM-PKL</strong><span class="text-xs text-muted">SMK ICB Cinta Niaga Bandung</span></span>
        </a>

        <nav class="flex flex-wrap items-center gap-x-5" aria-label="Menu utama">
            @foreach ($menu as [$rute, $label, $pola])
                <a href="{{ route($rute) }}" class="link inline-flex min-h-11 items-center {{ request()->routeIs(...explode(',', $pola)) ? 'underline underline-offset-8' : '' }}">{{ $label }}</a>
            @endforeach

            @auth
                <span class="hidden text-sm text-muted sm:inline">{{ $user->nama_lengkap }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-ghost" type="submit">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="link inline-flex min-h-11 items-center">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Buat akun siswa</a>
            @endauth
        </nav>
    </div>
</header>

@if (session('ok') || session('err') || $errors->any())
    <div class="wrap pt-6" role="status">
        @if (session('ok'))
            <p class="rounded-lg border border-brand bg-brand-soft px-4 py-3 text-sm font-medium text-brand-deep">{{ session('ok') }}</p>
        @endif
        @if (session('err'))
            <p class="rounded-lg border border-bad bg-bad-soft px-4 py-3 text-sm font-medium text-bad">{{ session('err') }}</p>
        @endif
        @if ($errors->any() && ! session('err'))
            <p class="rounded-lg border border-bad bg-bad-soft px-4 py-3 text-sm font-medium text-bad">Ada isian yang perlu diperbaiki. Lihat pesan di bawah tiap kolom.</p>
        @endif
    </div>
@endif

<main id="isi" class="flex-1">
    @yield('content')
</main>

<footer class="border-t border-line">
    <div class="wrap py-6 text-sm text-muted">SMK ICB Cinta Niaga Bandung, Bidang Hubungan Industri. Sistem Informasi PKL.</div>
</footer>
</body>
</html>
