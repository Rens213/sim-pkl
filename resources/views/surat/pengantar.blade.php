<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Surat Pengantar {{ $p->kode_pengajuan }}</title>
<style>
    @page { margin: 2cm 2.2cm; }
    body { font-family: 'Times New Roman', serif; font-size: 12pt; color: #000; line-height: 1.45; }
    .kop { width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 16px; text-align: center; }
    .kop h1 { font-size: 16pt; margin: 0; letter-spacing: 1px; }
    .kop p { margin: 2px 0 0; font-size: 10pt; }
    table.meta td { padding: 1px 6px 1px 0; vertical-align: top; }
    table.siswa { width: 100%; border-collapse: collapse; margin: 10px 0; }
    table.siswa th, table.siswa td { border: 1px solid #000; padding: 4px 6px; font-size: 11pt; }
    table.siswa th { background: #eee; }
    p { margin: 8px 0; text-align: justify; }
    table.ttd { width: 100%; margin-top: 18px; }
    table.ttd td { vertical-align: top; }
    .stempel { display: inline-block; border: 2px solid #0a6a55; color: #0a6a55; padding: 3px 8px; font-size: 9pt; font-weight: bold; text-align: center; }
    .kecil { font-size: 9pt; }
</style>
</head>
<body>
<div class="kop">
    <h1>{{ strtoupper($s['nama_sekolah']) }}</h1>
    <p>{{ $s['bidang'] }}</p>
    <p>{{ $s['alamat'] }} | {{ $s['kontak'] }}</p>
</div>

<table class="meta">
    <tr><td>Nomor</td><td>:</td><td>{{ $p->nomor_surat }}</td></tr>
    <tr><td>Lampiran</td><td>:</td><td>-</td></tr>
    <tr><td>Perihal</td><td>:</td><td><strong>Permohonan Praktik Kerja Lapangan (PKL)</strong></td></tr>
</table>

<p style="margin-top:14px;">Yth. Pimpinan / HRD<br><strong>{{ $p->perusahaan->nama_perusahaan }}</strong><br>{{ $p->perusahaan->alamat_lengkap }}</p>

<p>Dengan hormat,</p>
<p>Sehubungan dengan program Praktik Kerja Lapangan (PKL) bagi peserta didik {{ $s['nama_sekolah'] }}, kami mengajukan permohonan agar siswa berikut dapat melaksanakan PKL di perusahaan yang Bapak/Ibu pimpin pada periode <strong>{{ \App\Models\PengajuanPkl::tgl($p->tgl_mulai_pkl) }}</strong> sampai dengan <strong>{{ \App\Models\PengajuanPkl::tgl($p->tgl_selesai_pkl) }}</strong>:</p>

<table class="siswa">
    <thead><tr><th style="width:30px;">No</th><th>Nama Siswa</th><th style="width:80px;">NIS</th><th style="width:80px;">Kelas</th><th style="width:70px;">Peran</th></tr></thead>
    <tbody>
    @foreach ($p->semuaSiswa() as $siswa)
        <tr>
            <td style="text-align:center;">{{ $loop->iteration }}</td>
            <td>{{ $siswa->nama_lengkap }}</td>
            <td>{{ $siswa->nis_nip }}</td>
            <td>{{ $siswa->kelas }}</td>
            <td>{{ $loop->first ? 'Ketua' : 'Anggota' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@if ($p->guru_pembimbing)
    <p>Guru pembimbing dari sekolah: <strong>{{ $p->guru_pembimbing }}</strong>.</p>
@endif

<p>Besar harapan kami atas kesediaan Bapak/Ibu menerima siswa tersebut. Mohon balasan tertulis atas permohonan ini agar dapat kami tindak lanjuti. Atas perhatian dan kerja sama yang baik, kami ucapkan terima kasih.</p>

<table class="ttd">
    <tr>
        <td style="width:50%;">
            <img src="{{ $qr }}" width="95" height="95" alt="QR verifikasi"><br>
            <span class="kecil">Pindai untuk memverifikasi keaslian surat.<br>Kode: {{ $p->kode_pengajuan }}</span>
        </td>
        <td style="width:50%; text-align:center;">
            {{ $s['kota_surat'] }}, {{ \App\Models\PengajuanPkl::tgl($p->tgl_surat) }}<br>
            Ketua Hubungan Industri<br>
            <div class="stempel" style="margin:6px 0;">DISAHKAN SECARA DIGITAL<br>{{ strtoupper($s['nama_sekolah']) }}</div><br>
            <strong><u>{{ $s['ketua_hubin'] }}</u></strong>
        </td>
    </tr>
</table>
</body>
</html>
