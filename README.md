# CodeIgniter 4 Application Starter

## Tentang Proyek – TapKasir (POS)

TapKasir adalah aplikasi Point of Sale (POS) sederhana berbasis web untuk kebutuhan kasir minimarket/warung, dibangun dengan CodeIgniter 4 dan Alpine.js. Aplikasi ini mendukung peran Admin dan Kasir, pengelolaan produk & kategori, manajemen shift, transaksi dengan pemindaian barcode, permintaan restock, serta dashboard ringkasan penjualan.

### Fitur Utama

- Manajemen Produk & Kategori: tambah/edit/hapus produk, unggah foto, cetak/generate barcode, filter & pencarian.
- Transaksi Kasir: pencarian/scan barcode, keranjang belanja, validasi stok, pembayaran dan cetak struk.
- Permintaan Restock: kasir mengirim permintaan restock, admin menyetujui/menolak, stok produk diperbarui setelah disetujui.
- Manajemen Pengguna & Role: pendaftaran, persetujuan pengguna baru, pengaturan role (Admin/Kasir), hapus, dan ubah informasi pengguna.
- Manajemen Shift: buat/edit/hapus shift, assign shift ke kasir, peringatan akhir shift dan auto-logout.
- Log Transaksi & Detail Item: filter berdasarkan tanggal dan shift, lihat item per transaksi.
- Dashboard Admin: ringkasan penjualan harian, kasir aktif, user pending, produk butuh restock, serta grafik (Chart.js).

### Perilaku Auto-Refresh (hemat & terukur)

- Dashboard: refresh data setiap 60 detik.
- Admin – Log Transaksi: refresh data setiap 20 detik.
- Kasir – Log Transaksi: refresh data setiap 15 detik.
- Kasir – Status Shift: polling setiap 30 detik; jika sisa waktu ≤ 60 detik, tampilkan hitung mundur per detik dan logout otomatis saat habis.

### Timezone

- Seluruh pengolahan tanggal/filter diarahkan ke WITA (Asia/Makassar), termasuk default tanggal “hari ini” di sisi klien. Hal ini memastikan data “hari ini” konsisten antara server dan klien.

### Teknologi

- Backend: CodeIgniter 4 (PHP 8.1+)
- Frontend: Alpine.js, Chart.js
- Aset: CSS kustom (utility-like) dan Font Awesome
- Database: MySQL/MariaDB (via konfigurasi CI4)

### Struktur Direktori (ringkas)

```
app/            # Kode aplikasi CI4 (Controllers, Models, Views, Config)
public/         # Dokumen publik (index.php, css, js, images, uploads)
public/js/      # Skrip frontend (admin & kasir)
tests/          # Pengujian (PHPUnit)
vendor/         # Dependensi Composer
writable/       # Cache, logs, session, uploads runtime
```

### Persiapan & Instalasi

1. Pastikan prasyarat terpasang: PHP 8.1+, Composer, dan MySQL/MariaDB.
2. Salin `env` menjadi `.env` lalu atur `app.baseURL` dan koneksi database.
3. Install dependensi Composer: `composer install`.
4. Jalankan migrasi database: `php spark migrate`.
5. Jalankan server: `php spark serve` lalu akses `http://localhost:8080`.

### Cara Pakai (Alur Singkat)

- Pendaftaran Pengguna: user melakukan register, status awal pending; Admin menyetujui dan memberikan role.
- Admin:
  - Kelola Produk & Kategori, generate barcode, pantau permintaan restock, setujui/tolak, lihat log transaksi & detail item.
  - Kelola Pengguna & Role, serta atur Shift (buat/edit/hapus, assign ke kasir).
  - Pantau Dashboard untuk ringkasan penjualan dan metrik cepat.
- Kasir:
  - Transaksi dengan scan/pencarian barcode, tambahkan ke keranjang, lakukan pembayaran, cetak struk.
  - Ajukan permintaan restock dari halaman produk jika stok menipis.
  - Lihat Log Transaksi sendiri dengan filter tanggal/shift.

### Catatan Penting

- Validasi UI: tombol-tombol aksi dinonaktifkan saat input belum lengkap dan menampilkan indikator loading saat proses berjalan untuk mencegah double-submit/flicker.
- Filter Tanggal: default ke tanggal “hari ini” di WITA (Asia/Makassar) agar log transaksi tampil konsisten.
- Keamanan Basic: endpoint JSON menggunakan header `X-Requested-With` di beberapa aksi; autentikasi/otorisasi mengacu pada role & status user.

## Tim Proyek – Kelompok 3

Proyek ini dikerjakan oleh Kelompok 3 (4 anggota):

- Komang Desta Wahyu Kurniawan (3)
- Syahfrino Rezky Oktaviant (9)
- Komang Dwipayana Mahayoga (15)
- I Gusti Agung Gede Davu Bhayanaka Asmara Dana (21)

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the _public_ folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's _public_ folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter _public/..._, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
>
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
