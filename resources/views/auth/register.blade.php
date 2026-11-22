@extends('layouts.app')
@section('title', 'Buat akun siswa')

@section('content')
<div class="wrap py-12">
    <div class="card mx-auto max-w-xl" data-enter>
        <h1 class="text-3xl">Buat akun siswa</h1>
        <p class="mt-2 text-muted">Gunakan NIS dan nama sesuai data sekolah.</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 grid gap-4 sm:grid-cols-2">
            @csrf
            @php
                $kolom = [
                    ['nis_nip', 'NIS', 'text', 'username'],
                    ['kelas', 'Kelas (contoh: XI AKL 1)', 'text', 'off'],
                    ['nama_lengkap', 'Nama lengkap', 'text', 'name'],
                    ['email', 'Email', 'email', 'email'],
                    ['no_hp', 'No. HP (opsional)', 'tel', 'tel'],
                ];
            @endphp
            @foreach ($kolom as [$nama, $label, $tipe, $auto])
                <div class="{{ $nama === 'nama_lengkap' ? 'sm:col-span-2' : '' }}">
                    <label for="{{ $nama }}" class="label">{{ $label }}</label>
                    <input id="{{ $nama }}" name="{{ $nama }}" type="{{ $tipe }}" value="{{ old($nama) }}" class="field" autocomplete="{{ $auto }}" {{ $nama === 'no_hp' ? '' : 'required' }}>
                    @error($nama)<p class="err">{{ $message }}</p>@enderror
                </div>
            @endforeach
            <div>
                <label for="password" class="label">Kata sandi (min. 8 karakter)</label>
                <input id="password" type="password" name="password" class="field" autocomplete="new-password" required>
                @error('password')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="label">Ulangi kata sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="field" autocomplete="new-password" required>
            </div>
            <div class="sm:col-span-2"><button type="submit" class="btn btn-primary w-full">Buat akun siswa</button></div>
        </form>
    </div>
</div>
@endsection
