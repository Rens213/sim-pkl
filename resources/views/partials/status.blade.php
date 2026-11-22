@php
    $kelas = [
        'dikirim' => 'badge-wait',
        'disetujui_hubin' => 'badge-ok',
        'ditolak_hubin' => 'badge-no',
        'diterima_industri' => 'badge-done',
        'ditolak_industri' => 'badge-no',
    ][$status] ?? 'badge-wait';
@endphp
<span class="badge {{ $kelas }}">{{ \App\Models\PengajuanPkl::LABEL[$status] ?? $status }}</span>
