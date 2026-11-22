@extends('layouts.app')
@section('title', 'Periksa '.$p->kode_pengajuan)

@section('content')
<div class="wrap py-8">
    <a href="{{ route('admin.pengajuan.index') }}" class="link text-sm">Semua pengajuan</a>
    <div class="mt-3 flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl">{{ $p->kode_pengajuan }}</h1>
        @include('partials.status', ['status' => $p->status])
    </div>

    <section class="card mt-6">
        <h2 class="mb-4 text-lg">Status pengajuan</h2>
        @include('partials.tracker', ['status' => $p->status])
        @if ($p->catatan)
            <p class="mt-5 rounded-lg bg-bad-soft px-4 py-3 text-sm text-bad"><strong>Catatan:</strong> {{ $p->catatan }}</p>
        @endif
    </section>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="card">
                <h2 class="mb-3 text-lg">Perusahaan dan periode</h2>
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-muted">Perusahaan</dt><dd class="font-semibold">{{ $p->perusahaan->nama_perusahaan }}</dd></div>
                    <div><dt class="text-muted">Sisa kuota</dt><dd>{{ $p->perusahaan->kuota_tersedia }} siswa (kelompok ini {{ $p->ukuran() }})</dd></div>
                    <div><dt class="text-muted">Alamat</dt><dd>{{ $p->perusahaan->alamat_lengkap }}</dd></div>
                    <div><dt class="text-muted">Kontak HRD</dt><dd>{{ $p->perusahaan->kontak_hrd }}</dd></div>
                    <div><dt class="text-muted">Diajukan</dt><dd>{{ \App\Models\PengajuanPkl::tgl($p->tgl_pengajuan) }}</dd></div>
                    <div><dt class="text-muted">Periode PKL</dt><dd>{{ \App\Models\PengajuanPkl::tgl($p->tgl_mulai_pkl) }} s.d. {{ \App\Models\PengajuanPkl::tgl($p->tgl_selesai_pkl) }}</dd></div>
                </dl>
            </section>

            <section class="card overflow-x-auto">
                <h2 class="mb-3 text-lg">Daftar siswa</h2>
                <table class="tbl">
                    <thead><tr><th>No</th><th>Nama</th><th>NIS</th><th>Kelas</th><th>Peran</th></tr></thead>
                    <tbody>
                    @foreach ($p->semuaSiswa() as $s)
                        <tr><td>{{ $loop->iteration }}</td><td>{{ $s->nama_lengkap }}</td><td>{{ $s->nis_nip }}</td><td>{{ $s->kelas }}</td><td>{{ $loop->first ? 'Ketua' : 'Anggota' }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
            </section>
        </div>

        <div class="space-y-6">
            @if ($p->status === 'dikirim')
                <section class="card">
                    <h2 class="mb-3 text-lg">Keputusan Hubin</h2>
                    <form method="POST" action="{{ route('admin.pengajuan.setujui', $p) }}" onsubmit="return confirm('Setujui pengajuan ini dan terbitkan nomor surat?')">
                        @csrf
                        <button class="btn btn-primary w-full" type="submit">Setujui pengajuan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.pengajuan.tolak', $p) }}" class="mt-5 border-t border-line pt-5">
                        @csrf
                        <label for="catatan" class="label">Alasan penolakan</label>
                        <textarea id="catatan" name="catatan" rows="3" class="field" required>{{ old('catatan') }}</textarea>
                        @error('catatan')<p class="err">{{ $message }}</p>@enderror
                        <button class="btn btn-danger mt-3 w-full" type="submit">Tolak pengajuan</button>
                    </form>
                </section>
            @endif

            @if ($p->nomor_surat)
                <section class="card">
                    <h2 class="mb-3 text-lg">Surat dan pembimbing</h2>
                    <p class="text-sm text-muted">Nomor surat</p>
                    <p class="mb-4 font-semibold">{{ $p->nomor_surat }}</p>
                    <a href="{{ route('admin.pengajuan.surat', $p) }}" target="_blank" class="btn btn-primary w-full">Unduh surat pengantar</a>

                    <form method="POST" action="{{ route('admin.pengajuan.guru', $p) }}" class="mt-5 border-t border-line pt-5">
                        @csrf
                        <label for="guru_pembimbing" class="label">Guru pembimbing</label>
                        <input id="guru_pembimbing" name="guru_pembimbing" list="saran-guru" value="{{ old('guru_pembimbing', $p->guru_pembimbing) }}" class="field" required>
                        <datalist id="saran-guru">@foreach ($saranGuru as $g)<option value="{{ $g }}">@endforeach</datalist>
                        @error('guru_pembimbing')<p class="err">{{ $message }}</p>@enderror
                        <button class="btn btn-ghost mt-3 w-full" type="submit">Simpan guru pembimbing</button>
                    </form>

                    @if ($p->file_surat_balasan)
                        <a href="{{ asset('storage/'.$p->file_surat_balasan) }}" target="_blank" class="link mt-5 inline-block">Lihat surat balasan industri</a>
                    @endif
                </section>
            @endif
        </div>
    </div>
</div>
@endsection
