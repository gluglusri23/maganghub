# GameTopUp — PHP Native + MySQL

Website demo top up game untuk tugas praktik. Stack: PHP Native (PHP 8.0+), MySQL/MariaDB, HTML, CSS, JavaScript ringan, PDO.

## Fitur
- Register dan login dengan `password_hash()` / `password_verify()`.
- Role `user` dan `admin`.
- Katalog game dan produk.
- Pemesanan top up dengan Player ID dan Server ID opsional.
- Pembayaran simulasi (tidak memproses uang sungguhan).
- Riwayat transaksi pengguna.
- Dashboard admin dan CRUD game, produk, metode pembayaran, pengguna, serta pengelolaan status transaksi.
- 6 tabel relasional: `users`, `games`, `products`, `payment_methods`, `transactions`, `transaction_details`.
- Proteksi dasar: PDO prepared statements, escaping output, CSRF token, session ID regeneration, validasi input.

## 1. Persiapan
Install salah satu:
- XAMPP (Apache + MySQL + PHP), atau
- MAMP, atau
- PHP dan MySQL secara terpisah.

Gunakan PHP 8.0+ dan MySQL 5.7+/MariaDB yang mendukung InnoDB.

## 2. Instalasi di VS Code (XAMPP)
1. Ekstrak folder `gametopup` ke `C:\xampp\htdocs\gametopup` (Windows) atau `/Applications/XAMPP/htdocs/gametopup` (macOS, sesuai lokasi instalasi).
2. Buka folder `gametopup` melalui VS Code.
3. Jalankan Apache dan MySQL dari XAMPP Control Panel.
4. Buka `http://localhost/phpmyadmin`.
5. Pilih tab **Import**, pilih `database/gametopup.sql`, lalu klik **Import/Go**.
6. Buka `config/database.php`. Sesuaikan host, nama database, username, dan password MySQL. Default XAMPP umumnya `root` dengan password kosong.
7. Buka `config/app.php`. Default `BASE_URL` adalah `/gametopup`. Jika nama folder berbeda, sesuaikan.
8. Buka `http://localhost/gametopup`.

## 3. Membuat akun admin
Jangan mendaftarkan akun admin melalui form Register karena form tersebut selalu membuat role `user`.

Cara yang disarankan:
1. Buka Terminal VS Code di folder project.
2. Jalankan:
   ```bash
   php -r "echo password_hash('Admin123!', PASSWORD_DEFAULT), PHP_EOL;"
   ```
3. Salin hash yang tampil.
4. Di phpMyAdmin, buka database `gametopup`, tab SQL, jalankan query berikut setelah mengganti placeholder:
   ```sql
   INSERT INTO users (name,email,password,role)
   VALUES ('Administrator','admin@gametopup.test','PASTE_HASH_DI_SINI','admin');
   ```
5. Login melalui `http://localhost/gametopup/login.php` dengan:
   - Email: `admin@gametopup.test`
   - Password: `Admin123!`
6. Segera ganti password admin dengan mekanisme penggantian password yang Anda tambahkan sebelum aplikasi dipakai sungguhan.

## 4. Alur penggunaan
### User
1. Register, kemudian login.
2. Pilih game pada halaman Beranda.
3. Klik Top Up.
4. Masukkan Player ID dan Server ID (jika diperlukan).
5. Pilih produk dan metode pembayaran.
6. Klik Buat Pesanan.
7. Pada halaman pembayaran, klik **Simulasikan Pembayaran Berhasil**.
8. Lihat status pesanan pada menu Pesanan Saya.

### Admin
1. Login dengan akun admin.
2. Buka menu Admin.
3. Kelola game, produk, dan metode pembayaran.
4. Lihat pengguna dan transaksi.
5. Ubah status transaksi sesuai kebutuhan simulasi.

## 5. Struktur folder
```text
gametopup/
├── admin/
│   ├── games.php
│   ├── index.php
│   ├── payments.php
│   ├── products.php
│   ├── transactions.php
│   └── users.php
├── assets/style.css
├── config/
│   ├── app.php
│   └── database.php
├── database/gametopup.sql
├── includes/
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── index.php
├── login.php
├── logout.php
├── orders.php
├── payment.php
├── register.php
└── topup.php
```

## 6. Dokumentasi database (6 tabel)
- `users`: akun, email unik, password hash, role.
- `games`: master katalog game.
- `products`: nominal/harga; FK `game_id` ke `games`.
- `payment_methods`: master metode pembayaran.
- `transactions`: header pesanan; FK ke `users` dan `payment_methods`.
- `transaction_details`: detail barang yang dipesan; FK ke `transactions`, `products`, dan `games`. Nama game dan produk disimpan sebagai snapshot agar riwayat transaksi tetap terbaca jika nama katalog berubah.

Relasi:
- `games` 1 — N `products`
- `users` 1 — N `transactions`
- `payment_methods` 1 — N `transactions`
- `transactions` 1 — N `transaction_details`
- `products` 1 — N `transaction_details`
- `games` 1 — N `transaction_details`

## 7. Daftar CRUD
| Modul | Create | Read | Update | Delete |
|---|---|---|---|---|
| Game | Tambah | Daftar | Edit | Hapus jika belum direferensikan |
| Produk | Tambah | Daftar | Edit | Hapus jika belum direferensikan |
| Metode pembayaran | Tambah | Daftar | Edit | Hapus jika belum digunakan |
| Pengguna | Register | Daftar | Ubah role | Hapus jika belum punya transaksi |
| Transaksi | Buat pesanan | Riwayat/admin | Ubah status | Tidak disediakan agar audit transaksi terjaga |
| Detail transaksi | Dibuat saat order | Ditampilkan pada detail | Tidak diedit terpisah | Terhapus saat transaksi dihapus (cascade; UI tidak menyediakan hapus) |

## 8. Catatan penting
- Ini adalah proyek pembelajaran, bukan sistem top up komersial siap produksi.
- Pembayaran sepenuhnya simulasi. Tidak ada QRIS, bank, atau e-wallet sungguhan yang dihubungkan.
- Belum ada integrasi API publisher game untuk mengirim item secara otomatis.
- Untuk produksi, tambahkan verifikasi email, reset password, rate limiting login, audit log, konfigurasi HTTPS, secret di environment variables, backup, dan gateway pembayaran resmi.
- Penghapusan data yang sudah direferensikan dibatasi agar foreign key dan riwayat transaksi tidak rusak.
