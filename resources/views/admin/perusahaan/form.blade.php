@extends('layouts.app')
@section('title', $perusahaan->exists ? 'Ubah perusahaan' : 'Tambah perusahaan')

@section('content')
<div class="wrap py-8">
    <a href="{{ route('admin.perusahaan.index') }}" class="link text-sm">Semua perusahaan</a>
    <h1 class="mt-3 text-3xl">{{ $perusahaan->exists ? 'Ubah data perusahaan' : 'Tambah perusahaan mitra' }}</h1>

    <form method="POST" class="card mt-6 max-w-2xl space-y-5"
          action="{{ $perusahaan->exists ? route('admin.perusahaan.update', $perusahaan) : route('admin.perusahaan.store') }}">
        @csrf
        @if ($perusahaan->exists) @method('PUT') @endif

        <div>
            <label for="nama_perusahaan" class="label">Nama perusahaan</label>
            <input id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan', $perusahaan->nama_perusahaan) }}" class="field" required>
            @error('nama_perusahaan')<p class="err">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="alamat_lengkap" class="label">Alamat lengkap</label>
            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" class="field" required>{{ old('alamat_lengkap', $perusahaan->alamat_lengkap) }}</textarea>
            @error('alamat_lengkap')<p class="err">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="kontak_hrd" class="label">Kontak HRD</label>
            <input id="kontak_hrd" name="kontak_hrd" value="{{ old('kontak_hrd', $perusahaan->kontak_hrd) }}" class="field" required>
            @error('kontak_hrd')<p class="err">{{ $message }}</p>@enderror
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="kuota_tersedia" class="label">Kuota tersedia (siswa)</label>
                <input id="kuota_tersedia" type="number" min="0" max="500" name="kuota_tersedia" value="{{ old('kuota_tersedia', $perusahaan->kuota_tersedia) }}" class="field" required>
                @error('kuota_tersedia')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="status_mitra" class="label">Status mitra</label>
                <select id="status_mitra" name="status_mitra" class="field">
                    @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $v => $l)
                        <option value="{{ $v }}" @selected(old('status_mitra', $perusahaan->status_mitra) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
                @error('status_mitra')<p class="err">{{ $message }}</p>@enderror
            </div>
        </div>

        @unless ($perusahaan->exists)
            <fieldset class="rounded-lg border border-line p-4">
                <legend class="px-2 text-sm font-semibold">Akun login mitra (opsional)</legend>
                <p class="hint mb-3">Isi semua kolom agar perusahaan bisa masuk dan mengonfirmasi siswa.</p>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="akun_nip" class="label">Kode akun</label>
                        <input id="akun_nip" name="akun_nip" value="{{ old('akun_nip') }}" class="field">
                        @error('akun_nip')<p class="err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="akun_email" class="label">Email akun</label>
                        <input id="akun_email" type="email" name="akun_email" value="{{ old('akun_email') }}" class="field">
                        @error('akun_email')<p class="err">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="akun_password" class="label">Kata sandi</label>
                        <input id="akun_password" type="password" name="akun_password" class="field" autocomplete="new-password">
                        @error('akun_password')<p class="err">{{ $message }}</p>@enderror
                    </div>
                </div>
            </fieldset>
        @endunless

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $perusahaan->exists ? 'Simpan perubahan' : 'Simpan perusahaan' }}</button>
            <a href="{{ route('admin.perusahaan.index') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>
@endsection
