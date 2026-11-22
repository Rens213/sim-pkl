@extends('layouts.app')
@section('title', 'Verifikasi surat')

@section('content')
<div class="wrap py-12">
    <div class="card mx-auto max-w-xl" data-enter>
        @if ($p)
            <span class="badge badge-done">Surat sah</span>
            <h1 class="mt-3 text-3xl">Surat pengantar terverifikasi</h1>
            <p class="mt-2 text-muted">Surat ini diterbitkan oleh Hubin SMK ICB Cinta Niaga Bandung.</p>
            <dl class="mt-6 space-y-3 text-sm">
                <div><dt class="text-muted">Nomor surat</dt><dd class="font-semibold">{{ $p->nomor_surat }}</dd></div>
                <div><dt class="text-muted">Ditujukan kepada</dt><dd>{{ $p->perusahaan->nama_perusahaan }}</dd></div>
                <div><dt class="text-muted">Periode PKL</dt><dd>{{ \App\Models\PengajuanPkl::tgl($p->tgl_mulai_pkl) }} s.d. {{ \App\Models\PengajuanPkl::tgl($p->tgl_selesai_pkl) }}</dd></div>
                <div>
                    <dt class="text-muted">Siswa</dt>
                    <dd><ul class="mt-1 space-y-1">
                        @foreach ($p->semuaSiswa() as $s)
                            <li>{{ $s->nama_lengkap }} <span class="text-muted">(NIS {{ \Illuminate\Support\Str::mask($s->nis_nip, '*', -3) }})</span></li>
                        @endforeach
                    </ul></dd>
                </div>
            </dl>
        @else
            <span class="badge badge-no">Tidak ditemukan</span>
            <h1 class="mt-3 text-3xl">Surat tidak terdaftar</h1>
            <p class="mt-2 text-muted">Tidak ada surat pengantar dengan kode <strong>{{ $kode }}</strong>. Periksa kembali QR Code atau hubungi Hubin sekolah.</p>
        @endif
    </div>
</div>
@endsection
