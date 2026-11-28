# 🍔 FoodHub

Platform pemesanan makanan online (food delivery marketplace) yang terinspirasi dari GoFood dan GrabFood. Dibangun dengan Laravel sebagai proyek mata kuliah **Pemrograman Web Lanjut**.

> **Status:** sedang dikembangkan secara bertahap (stage by stage). Lihat bagian [Roadmap](#-roadmap).

## 📌 Tentang Proyek

FoodHub mempertemukan pelanggan dengan restoran. Pelanggan dapat menelusuri restoran dan menu, memasukkan makanan ke keranjang, lalu melakukan checkout. Pemilik restoran mengelola menu dan memproses pesanan, sedangkan admin memantau seluruh aktivitas aplikasi.

## 👥 Peran Pengguna

| Peran                | Kemampuan utama                                                                                              |
| -------------------- | ------------------------------------------------------------------------------------------------------------ |
| **Guest**            | Melihat homepage, daftar restoran, kategori, menu, dan detail restoran; registrasi dan login                 |
| **Customer**         | Keranjang belanja, checkout, riwayat dan detail pesanan, memantau status pesanan, kelola profil              |
| **Restaurant Owner** | Dashboard sendiri, kelola restoran, kategori menu, makanan (harga dan ketersediaan), memproses pesanan masuk |
| **Admin**            | Dashboard administrator, kelola pengguna, restoran, kategori, memantau seluruh transaksi                     |

## 🛠️ Teknologi

- **Backend:** Laravel 12, PHP 8.3
- **Database:** MySQL / MariaDB
- **Frontend:** Blade Template Engine, Bootstrap 5, Bootstrap Icons, JavaScript
- **Build tool:** Vite (npm)
- **ORM & Otorisasi:** Eloquent ORM, Laravel Middleware

## 🗄️ Rancangan Database

Tabel utama: `users`, `restaurants`, `categories`, `menus`, `carts`, `cart_items`, `orders`, `order_items`.

Catatan desain:

- Menggunakan foreign key dan relasi Eloquent.
- `order_items` menyimpan **harga saat pembelian**, sehingga riwayat pesanan tidak berubah ketika harga menu diperbarui.
- Total harga dihitung ulang di server dari data database, bukan dari data yang dikirim browser.

## 🔒 Keamanan

- Password di-hash.
- Validasi input dengan Laravel Validation.
- Proteksi CSRF.
- Pembatasan akses per peran menggunakan middleware.
- Restaurant Owner hanya dapat mengelola restoran miliknya sendiri.
- Customer hanya dapat melihat pesanannya sendiri.
- Pembuatan pesanan memakai database transaction.

## ⚙️ Persyaratan

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm
- MySQL atau MariaDB (contoh: XAMPP / Laragon)

## 🗺️ Roadmap

- [x] Stage 1 – Project Setup & Architecture
- [x] Stage 2 – Database Design & Migration
- [x] Stage 3 – Authentication & Authorization
- [x] Stage 4 – Public Website
- [ ] Stage 5 – Restaurant Management (Owner Area)
- [x] Stage 6 – Customer Features & Shopping Cart
- [x] Stage 7 – Checkout & Transaction
- [x] Stage 8 – Order Management (Status & Cancel)
- [x] Stage 9 – Admin Dashboard (Categories, Menus, & Transactions)
- [x] Stage 10 – UI/UX Enhancement (Green Fresh Style)
- [ ] Stage 11 – Testing & Finalization

## 👤 Pengembang

- Alphantius **Vincent** - 71230967
- **Benyamin** Prasetya Putra Ramayana - 71230984
- **Ivan** Roberto Halim - 71230986
- **Edrian** Sepriadi Irawan - 71231011

## 📄 Lisensi

Proyek ini dibuat untuk keperluan tugas dan pembelajaran.
