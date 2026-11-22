<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PengajuanPkl;
use App\Models\Perusahaan;
use App\Models\User;
use App\Services\SuratPengantar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PklController extends Controller
{
    // ------------------------------------------------------------ Katalog
    public function katalog(Request $request)
    {
        $perusahaan = Perusahaan::aktif()
            ->when($request->q, fn ($q, $s) => $q->where(
                fn ($w) => $w->where('nama_perusahaan', 'like', "%$s%")->orWhere('alamat_lengkap', 'like', "%$s%")
            ))
            ->orderBy('nama_perusahaan')
            ->get();

        $sibuk = in_array($request->user()->id, $this->idsSiswaSibuk());

        return view('siswa.katalog', compact('perusahaan', 'sibuk'));
    }

    // ------------------------------------------------------------ Form pengajuan
    public function create(Request $request, Perusahaan $perusahaan)
    {
        if ($pesan = $this->halangan($request->user(), $perusahaan)) {
            return redirect()->route('siswa.katalog')->with('err', $pesan);
        }

        $kandidat = User::where('role', 'siswa')
            ->where('id', '!=', $request->user()->id)
            ->whereNotIn('id', $this->idsSiswaSibuk())
            ->orderBy('nama_lengkap')
            ->get();

        return view('siswa.ajukan', compact('perusahaan', 'kandidat'));
    }

    public function store(Request $request, Perusahaan $perusahaan)
    {
        $user = $request->user();

        if ($pesan = $this->halangan($user, $perusahaan)) {
            return redirect()->route('siswa.katalog')->with('err', $pesan);
        }

        $maks = (int) config('sim.maks_anggota', 2);

        $data = $request->validate([
            'tgl_mulai_pkl' => ['required', 'date', 'after_or_equal:today'],
            'tgl_selesai_pkl' => ['required', 'date', 'after:tgl_mulai_pkl'],
            'anggota' => ['nullable', 'array', "max:$maks"],
            'anggota.*' => ['nullable', Rule::exists('users', 'id')->where('role', 'siswa')],
        ], [
            'tgl_mulai_pkl.after_or_equal' => 'Tanggal mulai tidak boleh sebelum hari ini.',
            'tgl_selesai_pkl.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'anggota.max' => "Satu pengajuan maksimal 1 ketua + $maks anggota.",
            'anggota.*.exists' => 'Anggota yang dipilih tidak ditemukan.',
        ]);

        $ids = array_values(array_filter($request->input('anggota', [])));

        if (count($ids) !== count(array_unique($ids))) {
            throw ValidationException::withMessages(['anggota' => 'Satu siswa tidak boleh dipilih dua kali.']);
        }

        $anggota = collect($ids)->map(fn ($v) => (int) $v);

        if ($anggota->contains($user->id)) {
            throw ValidationException::withMessages(['anggota' => 'Anda sudah menjadi ketua, jangan pilih diri sendiri.']);
        }

        if ($anggota->intersect($this->idsSiswaSibuk())->isNotEmpty()) {
            throw ValidationException::withMessages(['anggota' => 'Ada anggota yang masih punya pengajuan aktif.']);
        }

        $ukuran = 1 + $anggota->count();

        $pengajuan = DB::transaction(function () use ($perusahaan, $ukuran, $user, $data, $anggota) {
            $p = Perusahaan::lockForUpdate()->findOrFail($perusahaan->id);

            if ($p->status_mitra !== 'aktif' || $p->kuota_tersedia < $ukuran) {
                throw ValidationException::withMessages([
                    'anggota' => "Sisa kuota {$p->kuota_tersedia} siswa, sedangkan kelompok Anda $ukuran siswa.",
                ]);
            }

            $pengajuan = PengajuanPkl::create([
                'kode_pengajuan' => $this->buatKode(),
                'ketua_siswa_id' => $user->id,
                'perusahaan_id' => $p->id,
                'tgl_pengajuan' => now()->toDateString(),
                'tgl_mulai_pkl' => $data['tgl_mulai_pkl'],
                'tgl_selesai_pkl' => $data['tgl_selesai_pkl'],
                'status' => 'dikirim',
            ]);

            $pengajuan->anggota()->attach($anggota->all());

            return $pengajuan;
        });

        return redirect()->route('siswa.pengajuan.show', $pengajuan)
            ->with('ok', 'Pengajuan terkirim. Hubin akan memeriksanya.');
    }

    // ------------------------------------------------------------ Tracking
    public function index(Request $request)
    {
        $id = $request->user()->id;

        $daftar = PengajuanPkl::with('perusahaan', 'ketua', 'anggota')
            ->where(fn ($q) => $q->where('ketua_siswa_id', $id)
                ->orWhereHas('anggota', fn ($a) => $a->where('users.id', $id)))
            ->latest()
            ->get();

        return view('siswa.pengajuan', compact('daftar'));
    }

    public function show(Request $request, PengajuanPkl $pengajuan)
    {
        $p = $this->ambil($request, $pengajuan);

        return view('siswa.detail', ['p' => $p, 'adalahKetua' => $p->ketua_siswa_id == $request->user()->id]);
    }

    // ------------------------------------------------------------ Surat balasan & surat pengantar
    public function unggahBalasan(Request $request, PengajuanPkl $pengajuan)
    {
        $p = $this->ambil($request, $pengajuan);
        abort_unless($p->ketua_siswa_id == $request->user()->id, 403, 'Hanya ketua kelompok yang bisa mengunggah.');

        if (! in_array($p->status, ['disetujui_hubin', 'diterima_industri'], true)) {
            return back()->with('err', 'Surat balasan baru bisa diunggah setelah Hubin menyetujui pengajuan.');
        }

        $request->validate([
            'surat_balasan' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'surat_balasan.required' => 'Pilih file surat balasan dulu.',
            'surat_balasan.mimes' => 'Format file harus PDF, JPG, atau PNG.',
            'surat_balasan.max' => 'Ukuran file maksimal 2 MB.',
        ]);

        if ($p->file_surat_balasan) {
            Storage::disk('public')->delete($p->file_surat_balasan);
        }

        $p->update(['file_surat_balasan' => $request->file('surat_balasan')->store('surat-balasan', 'public')]);

        return back()->with('ok', 'Surat balasan tersimpan.');
    }

    public function surat(Request $request, PengajuanPkl $pengajuan)
    {
        $p = $this->ambil($request, $pengajuan);
        abort_unless($p->nomor_surat, 404, 'Surat pengantar belum diterbitkan.');

        return SuratPengantar::pdf($p)->stream(SuratPengantar::namaFile($p));
    }

    // ------------------------------------------------------------ Helper
    private function ambil(Request $request, PengajuanPkl $pengajuan): PengajuanPkl
    {
        $pengajuan->load('perusahaan', 'ketua', 'anggota');

        abort_unless(
            $pengajuan->ketua_siswa_id == $request->user()->id || $pengajuan->anggota->contains('id', $request->user()->id),
            403
        );

        return $pengajuan;
    }

    /** ID semua siswa (ketua + anggota) yang masih terikat pengajuan aktif. */
    private function idsSiswaSibuk(): array
    {
        return PengajuanPkl::with('anggota')
            ->whereIn('status', PengajuanPkl::AKTIF)
            ->get()
            ->flatMap(fn ($p) => $p->anggota->pluck('id')->push($p->ketua_siswa_id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function halangan(User $user, Perusahaan $perusahaan): ?string
    {
        if ($perusahaan->status_mitra !== 'aktif') {
            return 'Perusahaan ini bukan mitra aktif.';
        }
        if ($perusahaan->kuota_tersedia < 1) {
            return 'Kuota perusahaan ini sudah penuh.';
        }
        if (in_array($user->id, $this->idsSiswaSibuk())) {
            return 'Anda masih punya pengajuan aktif. Pengajuan baru bisa dibuat setelah pengajuan itu ditolak.';
        }

        return null;
    }

    private function buatKode(): string
    {
        do {
            $kode = 'PKL-'.now()->format('ymd').'-'.Str::upper(Str::random(4));
        } while (PengajuanPkl::where('kode_pengajuan', $kode)->exists());

        return $kode;
    }
}
