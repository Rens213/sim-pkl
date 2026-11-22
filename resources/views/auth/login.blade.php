@extends('layouts.app')
@section('title', 'Masuk')

@section('content')
<div class="wrap py-12">
    <div class="card mx-auto max-w-md" data-enter>
        <h1 class="text-3xl">Masuk ke SIM-PKL</h1>
        <p class="mt-2 text-muted">Siswa memakai NIS. Hubin dan mitra memakai NIP atau kode akun.</p>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label for="nis_nip" class="label">NIS / NIP</label>
                <input id="nis_nip" name="nis_nip" value="{{ old('nis_nip') }}" class="field" autocomplete="username" required autofocus>
                @error('nis_nip')<p class="err">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="label">Kata sandi</label>
                <input id="password" type="password" name="password" class="field" autocomplete="current-password" required>
                @error('password')<p class="err">{{ $message }}</p>@enderror
            </div>
            <label class="flex min-h-11 items-center gap-2 text-sm"><input type="checkbox" name="ingat" value="1" class="size-4 accent-brand"> Ingat saya di perangkat ini</label>
            <button type="submit" class="btn btn-primary w-full">Masuk ke portal</button>
        </form>

        <p class="mt-5 text-sm text-muted">Belum punya akun siswa? <a href="{{ route('register') }}" class="link">Buat akun</a></p>
    </div>
</div>
@endsection
