@extends('layouts.app')
@section('title', 'Katalog PKL')

@section('content')
<div class="wrap py-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl">Katalog tempat PKL</h1>
            <p class="mt-1 text-muted">{{ $perusahaan->count() }} mitra aktif. Tombol ajukan mati jika kuota habis.</p>
        </div>
        <form method="GET" class="flex items-end gap-2">
            <div>
                <label for="q" class="label">Cari perusahaan atau alamat</label>
                <input id="q" name="q" value="{{ request('q') }}" class="field sm:w-64">
            </div>
            <button class="btn btn-ghost" type="submit">Cari tempat</button>
        </form>
    </div>

    @if ($sibuk)
        <p class="mt-6 rounded-lg border border-warn bg-warn-soft px-4 py-3 text-sm font-medium text-warn">
            Anda masih punya pengajuan aktif, jadi pengajuan baru dinonaktifkan. <a class="link" href="{{ route('siswa.pengajuan.index') }}">Lihat pengajuan saya</a>
        </p>
    @endif

    <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($perusahaan as $p)
            <article class="card flex flex-col">
                <h2 class="text-lg">{{ $p->nama_perusahaan }}</h2>
                <p class="mt-2 text-sm text-muted">{{ $p->alamat_lengkap }}</p>
                <p class="mt-1 text-sm text-muted">HRD: {{ $p->kontak_hrd }}</p>
                <div class="mt-4">
                    @if ($p->kuota_tersedia > 0)
                        <span class="badge badge-ok">Sisa kuota {{ $p->kuota_tersedia }} siswa</span>
                    @else
                        <span class="badge badge-no">Kuota penuh</span>
                    @endif
                </div>
                <div class="mt-5 flex-1 content-end">
                    @if ($p->kuota_tersedia > 0 && ! $sibuk)
                        <a href="{{ route('siswa.ajukan', $p) }}" class="btn btn-primary w-full">Ajukan PKL</a>
                    @else
                        <button type="button" class="btn btn-primary w-full" disabled aria-disabled="true">Ajukan PKL</button>
                    @endif
                </div>
            </article>
        @empty
            <p class="text-muted">Tidak ada perusahaan yang cocok dengan pencarian Anda.</p>
        @endforelse
    </div>
</div>
@endsection
