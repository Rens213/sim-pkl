@extends('layouts.app')
@section('title', 'Pengajuan PKL')

@section('content')
<div class="wrap py-8">
    <h1 class="text-3xl">Pengajuan PKL siswa</h1>

    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-5">
        @foreach (\App\Models\PengajuanPkl::LABEL as $kode => $label)
            <a href="{{ route('admin.pengajuan.index', ['status' => $kode]) }}" class="card card-link no-underline {{ request('status') === $kode ? 'border-brand' : '' }}">
                <p class="text-3xl font-bold">{{ $hitung[$kode] ?? 0 }}</p>
                <p class="text-sm text-muted">{{ $label }}</p>
            </a>
        @endforeach
    </div>

    <form method="GET" class="mt-6 flex flex-wrap items-end gap-3">
        <div>
            <label for="q" class="label">Cari kode, ketua, atau perusahaan</label>
            <input id="q" name="q" value="{{ request('q') }}" class="field sm:w-72">
        </div>
        <div>
            <label for="status" class="label">Status</label>
            <select id="status" name="status" class="field">
                <option value="">Semua status</option>
                @foreach (\App\Models\PengajuanPkl::LABEL as $kode => $label)
                    <option value="{{ $kode }}" @selected(request('status') === $kode)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-ghost" type="submit">Terapkan filter</button>
    </form>

    <div class="card mt-6 overflow-x-auto p-0">
        <table class="tbl">
            <thead><tr><th>Kode</th><th>Ketua</th><th>Perusahaan</th><th>Periode</th><th>Status</th><th><span class="sr-only">Aksi</span></th></tr></thead>
            <tbody>
            @forelse ($daftar as $p)
                <tr>
                    <td class="font-semibold">{{ $p->kode_pengajuan }}</td>
                    <td>{{ $p->ketua->nama_lengkap }}<br><span class="text-muted">{{ $p->anggota_count + 1 }} siswa</span></td>
                    <td>{{ $p->perusahaan->nama_perusahaan }}</td>
                    <td class="whitespace-nowrap">{{ \App\Models\PengajuanPkl::tgl($p->tgl_mulai_pkl) }}<br><span class="text-muted">s.d. {{ \App\Models\PengajuanPkl::tgl($p->tgl_selesai_pkl) }}</span></td>
                    <td>@include('partials.status', ['status' => $p->status])</td>
                    <td><a class="link" href="{{ route('admin.pengajuan.show', $p) }}">Periksa pengajuan</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-muted">Belum ada pengajuan yang cocok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
