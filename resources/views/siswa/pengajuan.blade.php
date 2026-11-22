@extends('layouts.app')
@section('title', 'Pengajuan saya')

@section('content')
<div class="wrap py-8">
    <h1 class="text-3xl">Pengajuan saya</h1>

    <div class="mt-6 grid gap-4">
        @forelse ($daftar as $p)
            <a href="{{ route('siswa.pengajuan.show', $p) }}" class="card card-link flex flex-wrap items-center justify-between gap-4 no-underline">
                <div>
                    <p class="text-sm text-muted">{{ $p->kode_pengajuan }}</p>
                    <h2 class="text-lg">{{ $p->perusahaan->nama_perusahaan }}</h2>
                    <p class="mt-1 text-sm text-muted">
                        {{ \App\Models\PengajuanPkl::tgl($p->tgl_mulai_pkl) }} s.d. {{ \App\Models\PengajuanPkl::tgl($p->tgl_selesai_pkl) }}
                        | {{ $p->ukuran() }} siswa
                    </p>
                </div>
                @include('partials.status', ['status' => $p->status])
            </a>
        @empty
            <div class="card">
                <p class="text-muted">Belum ada pengajuan. Pilih tempat PKL di katalog untuk memulai.</p>
                <a href="{{ route('siswa.katalog') }}" class="btn btn-primary mt-4">Buka katalog PKL</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
