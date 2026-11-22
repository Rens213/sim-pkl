{{-- Alur visual: Pengajuan dikirim -> Hubin -> Industri. Butuh variabel $status. --}}
@php
    $keadaan = [
        'dikirim' => ['done', 'current', 'todo'],
        'disetujui_hubin' => ['done', 'done', 'current'],
        'ditolak_hubin' => ['done', 'fail', 'skip'],
        'diterima_industri' => ['done', 'done', 'done'],
        'ditolak_industri' => ['done', 'done', 'fail'],
    ][$status];

    $langkah = [
        'Pengajuan dikirim',
        $status === 'ditolak_hubin' ? 'Ditolak Hubin' : 'Disetujui Hubin',
        $status === 'ditolak_industri' ? 'Ditolak industri' : 'Diterima industri',
    ];

    $gaya = [
        'done' => ['border-brand', 'Selesai', 'text-brand-deep'],
        'current' => ['border-warn', 'Menunggu', 'text-warn'],
        'fail' => ['border-bad', 'Ditolak', 'text-bad'],
        'todo' => ['border-line', 'Belum', 'text-muted'],
        'skip' => ['border-line', 'Tidak dilanjutkan', 'text-muted'],
    ];
@endphp
<ol class="grid gap-4 sm:grid-cols-3" aria-label="Status pengajuan">
    @foreach ($langkah as $i => $nama)
        @php [$garis, $teks, $warna] = $gaya[$keadaan[$i]]; @endphp
        <li class="border-t-4 {{ $garis }} pt-3">
            <p class="text-xs font-semibold text-muted">Langkah {{ $i + 1 }}</p>
            <p class="font-semibold">{{ $nama }}</p>
            <p class="text-sm font-medium {{ $warna }}">{{ $teks }}</p>
        </li>
    @endforeach
</ol>
