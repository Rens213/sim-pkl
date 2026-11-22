@extends('layouts.app')
@section('title', 'Perusahaan mitra')

@section('content')
<div class="wrap py-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <h1 class="text-3xl">Perusahaan mitra</h1>
        <a href="{{ route('admin.perusahaan.create') }}" class="btn btn-primary">Tambah perusahaan</a>
    </div>

    <form method="GET" class="mt-6 flex items-end gap-2">
        <div>
            <label for="q" class="label">Cari nama perusahaan</label>
            <input id="q" name="q" value="{{ request('q') }}" class="field sm:w-72">
        </div>
        <button class="btn btn-ghost" type="submit">Cari perusahaan</button>
    </form>

    <div class="card mt-6 overflow-x-auto p-0">
        <table class="tbl min-w-[52rem]">
            <thead><tr><th>Perusahaan</th><th>Kontak HRD</th><th>Status</th><th>Kuota</th><th>Pengajuan</th><th><span class="sr-only">Aksi</span></th></tr></thead>
            <tbody>
            @forelse ($daftar as $p)
                <tr>
                    <td><strong>{{ $p->nama_perusahaan }}</strong><br><span class="text-muted">{{ $p->alamat_lengkap }}</span></td>
                    <td>{{ $p->kontak_hrd }}</td>
                    <td><span class="badge {{ $p->status_mitra === 'aktif' ? 'badge-ok' : 'badge-wait' }}">{{ ucfirst($p->status_mitra) }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.perusahaan.kuota', $p) }}" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <label class="sr-only" for="kuota-{{ $p->id }}">Kuota {{ $p->nama_perusahaan }}</label>
                            <input id="kuota-{{ $p->id }}" type="number" name="kuota_tersedia" min="0" max="500" value="{{ $p->kuota_tersedia }}" class="field w-20">
                            <button class="btn btn-ghost" type="submit">Simpan kuota</button>
                        </form>
                    </td>
                    <td>{{ $p->pengajuan_count }}</td>
                    <td class="whitespace-nowrap">
                        <a href="{{ route('admin.perusahaan.edit', $p) }}" class="link mr-3">Ubah data</a>
                        <form method="POST" action="{{ route('admin.perusahaan.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus {{ $p->nama_perusahaan }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="min-h-11 text-sm font-semibold text-bad underline-offset-4 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="py-8 text-center text-muted">Belum ada perusahaan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
