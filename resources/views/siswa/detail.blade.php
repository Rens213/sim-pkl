@extends('layouts.app')
@section('title', 'Pengajuan '.$p->kode_pengajuan)

@section('content')
<div class="wrap py-8">
    <a href="{{ route('siswa.pengajuan.index') }}" class="link text-sm">Semua pengajuan</a>
    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl">{{ $p->kode_pengajuan }}</h1>
        @include('partials.status', ['status' => $p->status])
    </div>

    <section class="card mt-6">
        <h2 class="mb-4 text-lg">Status pengajuan</h2>
        @include('partials.tracker', ['status' => $p->status])
        @if ($p->catatan && in_array($p->status, ['ditolak_hubin', 'ditolak_industri']))
            <p class="mt-5 rounded-lg bg-bad-soft px-4 py-3 text-sm text-bad"><strong>Catatan penolakan:</strong> {{ $p->catatan }}</p>
        @endif
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="card">
            <h2 class="mb-3 text-lg">Rincian</h2>
            <dl class="space-y-3 text-sm">
                <div><dt class="text-muted">Perusahaan</dt><dd class="font-semibold">{{ $p->perusahaan->nama_perusahaan }}</dd></div>
                <div><dt class="text-muted">Alamat</dt><dd>{{ $p->perusahaan->alamat_lengkap }}</dd></div>
                <div><dt class="text-muted">Kontak HRD</dt><dd>{{ $p->perusahaan->kontak_hrd }}</dd></div>
                <div><dt class="text-muted">Periode PKL</dt><dd>{{ \App\Models\PengajuanPkl::tgl($p->tgl_mulai_pkl) }} s.d. {{ \App\Models\PengajuanPkl::tgl($p->tgl_selesai_pkl) }}</dd></div>
                <div><dt class="text-muted">Nomor surat pengantar</dt><dd>{{ $p->nomor_surat ?? 'Belum terbit' }}</dd></div>
                <div><dt class="text-muted">Guru pembimbing</dt><dd>{{ $p->guru_pembimbing ?? 'Belum ditentukan' }}</dd></div>
            </dl>
        </section>

        <section class="card">
            <h2 class="mb-3 text-lg">Anggota kelompok</h2>
            <ul class="divide-y divide-line text-sm">
                @foreach ($p->semuaSiswa() as $s)
                    <li class="flex items-center justify-between gap-3 py-2">
                        <span><strong>{{ $s->nama_lengkap }}</strong><br><span class="text-muted">{{ $s->nis_nip }} | {{ $s->kelas }}</span></span>
                        <span class="badge {{ $loop->first ? 'badge-ok' : 'badge-wait' }}">{{ $loop->first ? 'Ketua' : 'Anggota' }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>

    @if ($p->nomor_surat || in_array($p->status, ['disetujui_hubin', 'diterima_industri']))
        <section class="card mt-6">
            <h2 class="mb-3 text-lg">Dokumen</h2>
            <div class="flex flex-wrap items-start gap-3">
                @if ($p->nomor_surat)
                    <a href="{{ route('siswa.pengajuan.surat', $p) }}" target="_blank" class="btn btn-primary">Unduh surat pengantar</a>
                @endif
                @if ($p->file_surat_balasan)
                    <a href="{{ asset('storage/'.$p->file_surat_balasan) }}" target="_blank" class="btn btn-ghost">Lihat surat balasan</a>
                @endif
            </div>

            @if ($adalahKetua && in_array($p->status, ['disetujui_hubin', 'diterima_industri']))
                <form method="POST" action="{{ route('siswa.pengajuan.balasan', $p) }}" enctype="multipart/form-data" class="mt-6 max-w-md border-t border-line pt-5">
                    @csrf
                    <label for="surat_balasan" class="label">Surat balasan dari industri (PDF, JPG, PNG, maks. 2 MB)</label>
                    <input id="surat_balasan" type="file" name="surat_balasan" accept=".pdf,.jpg,.jpeg,.png" class="field" required>
                    @error('surat_balasan')<p class="err">{{ $message }}</p>@enderror
                    <button type="submit" class="btn btn-primary mt-3">Unggah surat balasan</button>
                </form>
            @endif
        </section>
    @endif
</div>
@endsection
