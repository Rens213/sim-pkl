@extends('layouts.app')
@section('title', 'Portal pengajuan PKL')

@section('content')
<section class="wrap grid gap-10 py-12 md:grid-cols-2 md:items-center md:py-20">
    <div data-enter>
        <p class="text-sm font-semibold text-brand">SIM-PKL untuk siswa SMK ICB Cinta Niaga Bandung</p>
        <h1 class="mt-3">Ajukan tempat PKL, pantau persetujuannya, unduh surat pengantar.</h1>
        <p class="mt-4 max-w-xl text-lg text-muted">Satu kelompok maksimal 3 siswa. Surat pengantar bernomor resmi terbit otomatis begitu Hubin menyetujui pengajuan.</p>
        <div class="mt-7 flex flex-wrap gap-3">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Buka dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Masuk ke portal</a>
                <a href="{{ route('register') }}" class="btn btn-ghost">Buat akun siswa</a>
            @endauth
        </div>
    </div>

    <div class="card" data-enter>
        <div class="grid grid-cols-2 gap-4 border-b border-line pb-5">
            <div><p class="text-3xl font-bold">{{ $jumlahMitra }}</p><p class="text-sm text-muted">mitra industri aktif</p></div>
            <div><p class="text-3xl font-bold">{{ $totalKuota }}</p><p class="text-sm text-muted">sisa kuota siswa</p></div>
        </div>
        <h2 class="mb-4 mt-5 text-base">Alur setiap pengajuan</h2>
        @include('partials.tracker', ['status' => 'disetujui_hubin'])
    </div>
</section>

<section class="border-t border-line bg-white">
    <div class="wrap py-14">
        <h2 class="max-w-2xl">Tiga langkah dari katalog sampai penerimaan</h2>
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            <div class="card"><p class="text-sm font-semibold text-brand">1. Siswa</p><h3 class="mt-1">Memilih tempat dan mengajukan</h3><p class="mt-2 text-muted">Cari perusahaan di katalog, lihat sisa kuota, lalu kirim pengajuan sendiri atau bersama dua teman.</p></div>
            <div class="card"><p class="text-sm font-semibold text-brand">2. Hubin</p><h3 class="mt-1">Memvalidasi dan menerbitkan surat</h3><p class="mt-2 text-muted">Hubin memeriksa pengajuan, menyetujui atau menolak, dan menunjuk guru pembimbing untuk kelompok.</p></div>
            <div class="card"><p class="text-sm font-semibold text-brand">3. Industri</p><h3 class="mt-1">Menjawab dan membalas</h3><p class="mt-2 text-muted">Perusahaan menerima atau menolak kelompok. Siswa mengunggah surat balasan dari industri.</p></div>
        </div>
    </div>
</section>
@endsection
