@extends('layouts.app')
@section('title', 'Ajukan PKL')

@section('content')
<div class="wrap grid gap-8 py-8 lg:grid-cols-3">
    <aside class="card h-fit lg:order-2">
        <p class="text-sm font-semibold text-brand">Tempat yang dipilih</p>
        <h2 class="mt-1 text-lg">{{ $perusahaan->nama_perusahaan }}</h2>
        <p class="mt-2 text-sm text-muted">{{ $perusahaan->alamat_lengkap }}</p>
        <p class="mt-3"><span class="badge badge-ok">Sisa kuota {{ $perusahaan->kuota_tersedia }} siswa</span></p>
    </aside>

    <div class="lg:col-span-2">
        <h1 class="text-3xl">Ajukan PKL</h1>
        <p class="mt-1 text-muted">Ajukan sendiri atau berkelompok: 1 ketua dan maksimal 2 anggota.</p>

        <form method="POST" action="{{ route('siswa.ajukan.store', $perusahaan) }}" class="card mt-6 space-y-5">
            @csrf
            <div>
                <p class="label">Ketua kelompok</p>
                <p class="field bg-paper">{{ auth()->user()->nama_lengkap }} ({{ auth()->user()->nis_nip }})</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="tgl_mulai_pkl" class="label">Tanggal mulai PKL</label>
                    <input id="tgl_mulai_pkl" type="date" name="tgl_mulai_pkl" value="{{ old('tgl_mulai_pkl') }}" min="{{ now()->toDateString() }}" class="field" required>
                    @error('tgl_mulai_pkl')<p class="err">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="tgl_selesai_pkl" class="label">Tanggal selesai PKL</label>
                    <input id="tgl_selesai_pkl" type="date" name="tgl_selesai_pkl" value="{{ old('tgl_selesai_pkl') }}" class="field" required>
                    @error('tgl_selesai_pkl')<p class="err">{{ $message }}</p>@enderror
                </div>
            </div>

            @foreach ([0, 1] as $i)
                <div>
                    <label for="anggota_{{ $i }}" class="label">Anggota {{ $i + 1 }} (opsional)</label>
                    <select id="anggota_{{ $i }}" name="anggota[]" class="field">
                        <option value="">Tidak ada</option>
                        @foreach ($kandidat as $k)
                            <option value="{{ $k->id }}" @selected(old("anggota.$i") == $k->id)>{{ $k->nama_lengkap }} ({{ $k->nis_nip }}, {{ $k->kelas }})</option>
                        @endforeach
                    </select>
                    @error("anggota.$i")<p class="err">{{ $message }}</p>@enderror
                </div>
            @endforeach
            @error('anggota')<p class="err">{{ $message }}</p>@enderror
            <p class="hint">Kosongkan kedua pilihan anggota untuk mengajukan sendiri. Hanya siswa tanpa pengajuan aktif yang muncul di daftar.</p>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="btn btn-primary">Kirim pengajuan</button>
                <a href="{{ route('siswa.katalog') }}" class="btn btn-ghost">Kembali ke katalog</a>
            </div>
        </form>
    </div>
</div>
@endsection
