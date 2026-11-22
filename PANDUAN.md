# SIM-PKL: ringkasan pemasangan (Laragon)

1. Laragon > Start All. Buka Terminal Laragon.
2. `cd C:\laragon\www` lalu `composer create-project laravel/laravel:^12.0 sim-pkl` lalu `cd sim-pkl`
3. `mysql -u root -e "CREATE DATABASE db_sim_pkl CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"`
4. `composer require barryvdh/laravel-dompdf chillerlan/php-qrcode`
5. `npm install` lalu `npm install -D tailwindcss @tailwindcss/vite`
6. Ekstrak isi sim-pkl-overlay.zip ke folder sim-pkl, pilih Replace untuk semua berkas yang sama.
7. Edit `.env`: DB_CONNECTION=mysql, DB_HOST=127.0.0.1, DB_PORT=3306, DB_DATABASE=db_sim_pkl,
   DB_USERNAME=root, DB_PASSWORD= (kosong), APP_URL=http://127.0.0.1:8000, APP_LOCALE=id
8. Edit `config/app.php`: 'timezone' => 'Asia/Jakarta'. Edit `config/sim.php` (alamat sekolah, nama Ketua Hubin).
9. `php artisan migrate:fresh --seed` lalu `php artisan storage:link`
10. Terminal 1: `php artisan serve`. Terminal 2: `npm run dev`. Buka http://127.0.0.1:8000

Akun uji (kata sandi semua: password123)
- Hubin: HUBIN001
- Siswa: 2526001 sampai 2526008
- Mitra: MITRA001, MITRA002, MITRA003
