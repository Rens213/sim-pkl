@extends('layouts.app')
@section('title', 'Pelamar PKL')

@section('content')
<div class="wrap py-8">
    <h1 class="text-3xl">Siswa yang melamar ke {{ $perusahaan->nama_perusahaan }}</h1>
    <p class="mt-1 text-muted">Sisa kuota Anda: <strong>{{ $perusahaan->kuota_tersedia }} siswa</strong>. Kuota berkurang saat Anda menerima kelompok.</p>

    <div class="mt-8 grid gap-5">
        @forelse ($daftar as $p)
            <article class="card">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="text-sm text-muted">{{ $p->kode_pengajuan }} | Kelompok {{ $p->ukuran() }} siswa</p>
                        <h2 class="text-lg">Ketua: {{ $p->ketua->nama_lengkap }}</h2>
                        <p class="mt-1 text-sm text-muted">PKL {{ \App\Models\PengajuanPkl::tgl($p->tgl_mulai_pkl) }} s.d. {{ \App\Models\PengajuanPkl::tgl($p->tgl_selesai_pkl) }}</p>
                    </div>
                    @include('partials.status', ['status' => $p->status])
                </div>

                <ul class="mt-4 divide-y divide-line text-sm">
                    @foreach ($p->semuaSiswa() as $s)
                        <li class="flex justify-between gap-3 py-2"><span>{{ $s->nama_lengkap }}</span><span class="text-muted">{{ $s->nis_nip }} | {{ $s->kelas }}</span></li>
                    @endforeach
                </ul>

                @if ($p->file_surat_balasan)
                    <a href="{{ asset('storage/'.$p->file_surat_balasan) }}" target="_blank" class="link mt-4 inline-block text-sm">Lihat surat balasan</a>
                @endif

                @if ($p->status === 'disetujui_hubin')
                    <div class="mt-5 grid gap-3 border-t border-line pt-5 sm:grid-cols-2">
                        <form method="POST" action="{{ route('mitra.terima', $p) }}" onsubmit="return confirm('Terima kelompok ini? Kuota akan berkurang {{ $p->ukuran() }}.')">
                            @csrf
                            <button class="btn btn-primary w-full" type="submit">Terima kelompok</button>
                        </form>
                        <form method="POST" action="{{ route('mitra.tolak', $p) }}" class="flex gap-2">
                            @csrf
                            <label class="sr-only" for="catatan-{{ $p->id }}">Alasan penolakan (opsional)</label>
                            <input id="catatan-{{ $p->id }}" name="catatan" class="field" placeholder="Alasan (opsional)">
                            <button class="btn btn-danger" type="submit">Tolak kelompok</button>
                        </form>
                    </div>
                @elseif ($p->status === 'ditolak_industri' && $p->catatan)
                    <p class="mt-4 text-sm text-muted">Catatan Anda: {{ $p->catatan }}</p>
                @endif
            </article>
        @empty
            <div class="card"><p class="text-muted">Belum ada siswa yang melamar. Pengajuan muncul di sini setelah disetujui Hubin.</p></div>
        @endforelse
    </div>
</div>
@endsection
