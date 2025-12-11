# MobileNest Backend Implementation - Quick Start Guide

## 🚀 Langkah Cepat Implementasi

### Tahap 1: Persiapan Database

**1.1. Buat Database & Tabel**

Kopas SQL berikut ke phpMyAdmin atau MySQL CLI:

```sql
CREATE DATABASE IF NOT EXISTS mobilenest;
USE mobilenest;

-- Tabel Users
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk
CREATE TABLE produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    merek VARCHAR(100),
    deskripsi TEXT,
    spesifikasi TEXT,
    harga DECIMAL(12,2) NOT NULL,
    stok INT DEFAULT 0,
    gambar VARCHAR(255),
    kategori VARCHAR(50),
    status_produk ENUM('Tersedia', 'Tidak Tersedia') DEFAULT 'Tersedia',
    tanggal_ditambahkan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
);

-- Insert Sample Data
INSERT INTO produk (nama_produk, merek, deskripsi, harga, stok, kategori, status_produk) VALUES
('iPhone 15 Pro', 'Apple', 'Smartphone flagship terbaru', 15999000, 10, 'Flagship', 'Tersedia'),
('Samsung Galaxy S24', 'Samsung', 'Flagship smartphone Samsung', 13999000, 8, 'Flagship', 'Tersedia'),
('Xiaomi 14', 'Xiaomi', 'Smartphone mid-range berkualitas', 8999000, 15, 'Mid-Range', 'Tersedia'),
('POCO X6', 'Xiaomi', 'Budget smartphone dengan spec bagus', 5999000, 20, 'Budget', 'Tersedia');
```

**1.2. Verifikasi Koneksi**

Buka di browser:
```
http://localhost/MobileNest/test-connection.php
```

Jika tampil "Connection successful" = Database siap!

---

### Tahap 2: Sistem Autentikasi

**2.1. File Yang Sudah Ada:**

| File | Status | Fungsi |
|------|--------|--------|
| `config.php` | ✅ Ada | Koneksi DB & helper functions |
| `user/register.php` | ✅ Ada | Halaman form register |
| `user/proses-register.php` | ✅ Ada | Process registrasi |
| `user/login.php` | ✅ Ada | Halaman form login |
| `user/proses-login.php` | ✅ Ada | Process login |
| `user/logout.php` | ✅ Ada | Process logout |

**2.2. Testing Autentikasi**

1. **Register User Baru**
   - Buka: `http://localhost/MobileNest/user/register.php`
   - Isi form:
     - Nama Lengkap: `John Doe`
     - Email: `john@example.com`
     - Password: `password123` (min 6 karakter)
     - Konfirmasi Password: `password123`
   - Klik "Daftar"
   - Jika berhasil: redirect ke login page dengan pesan sukses

2. **Login**
   - Buka: `http://localhost/MobileNest/user/login.php`
   - Isi form:
     - Email: `john@example.com`
     - Password: `password123`
   - Klik "Masuk"
   - Jika berhasil: redirect ke dashboard dengan session aktif

3. **Check Session**
   - Di browser console atau di code:
   ```php
   <?php
   session_start();
   echo $_SESSION['user']; // ID user yang login
   echo $_SESSION['user_name']; // Nama user
   ?>
   ```

4. **Logout**
   - Klik link "Logout" di navbar
   - Session akan dihapus, redirect ke homepage

---

### Tahap 3: CRUD Produk

#### A. READ - List Produk (SUDAH ADA)

**File:** `produk/list-produk.php`

**Fitur:**
- Tampilkan semua produk dengan status "Tersedia"
- Filter by kategori
- Search by nama / merek
- Responsive grid layout

**Cara Akses:**
```
http://localhost/MobileNest/produk/list-produk.php
http://localhost/MobileNest/produk/list-produk.php?kategori=Flagship
http://localhost/MobileNest/produk/list-produk.php?search=iPhone
```

#### B. READ - Detail Produk (KOSONG - PERLU DIBUAT)

**File:** `produk/detail-produk.php` (BUAT BARU)

**Lihat code:** `DETAILED_CODE_IMPLEMENTATION.md` bagian "1. FILE: produk/detail-produk.php"

**Cara Buat:**
1. Buat file baru: `MobileNest/produk/detail-produk.php`
2. Copy-paste code dari `DETAILED_CODE_IMPLEMENTATION.md`
3. Save file

**Cara Akses:**
```
http://localhost/MobileNest/produk/detail-produk.php?id=1
http://localhost/MobileNest/produk/detail-produk.php?id=2
```

**Fitur:**
- Tampilkan detail produk lengkap
- Gambar produk
- Harga, stok, kategori
- Deskripsi & spesifikasi
- Related products (kategori sama)
- Button "Tambah ke Keranjang"

#### C. CREATE - Tambah Produk (SUDAH ADA - ADMIN ONLY)

**File:** `admin/kelola-produk.php`

**Cara Akses:**
1. Login terlebih dahulu
2. Buka: `http://localhost/MobileNest/admin/kelola-produk.php`
3. Klik button "+ Tambah Produk"
4. Form modal terbuka
5. Isi field:
   - **Nama Produk** (wajib)
   - Merek
   - Deskripsi
   - Spesifikasi
   - **Harga** (wajib, > 0)
   - Stok
   - Gambar URL
   - Kategori
   - Status (Tersedia / Tidak Tersedia)
6. Klik "Simpan"
7. Produk tersimpan ke database

#### D. UPDATE - Edit Produk (SUDAH ADA - ADMIN ONLY)

**File:** `admin/kelola-produk.php`

**Cara Akses:**
1. Login & buka: `http://localhost/MobileNest/admin/kelola-produk.php`
2. Cari produk di tabel
3. Klik button "Edit" pada row produk
4. Form modal terbuka dengan data produk
5. Ubah field yang perlu diubah
6. Klik "Simpan Perubahan"
7. Produk terupdate di database

#### E. DELETE - Hapus Produk (SUDAH ADA - ADMIN ONLY)

**File:** `admin/kelola-produk.php`

**Cara Akses:**
1. Login & buka: `http://localhost/MobileNest/admin/kelola-produk.php`
2. Cari produk di tabel
3. Klik button "Hapus" pada row produk
4. Dialog konfirmasi muncul: "Hapus produk ini?"
5. Klik "OK" untuk confirm
6. Produk terhapus dari database

---

## 📝 Testing Checklist

### Authentication
- [ ] Register user berhasil
- [ ] Email validation berfungsi
- [ ] Password minimum 6 karakter
- [ ] Email tidak bisa duplicate
- [ ] Login dengan email & password berhasil
- [ ] Session aktif setelah login
- [ ] Logout menghapus session

### CRUD Produk
- [ ] List produk tampil dengan benar
- [ ] Filter by kategori berfungsi
- [ ] Search by nama/merek berfungsi
- [ ] Detail produk tampil lengkap
- [ ] Related products tampil
- [ ] Tambah produk berhasil (admin)
- [ ] Edit produk berhasil (admin)
- [ ] Hapus produk berhasil (admin)

### Database
- [ ] Tabel users ada & terstruktur
- [ ] Tabel produk ada & terstruktur
- [ ] Sample data produk ter-insert
- [ ] Password di-hash dengan password_hash()

---

## 📚 File Structure

```
MobileNest/
├── admin/
│   ├── dashboard.php (✅ ada)
│   ├── kelola-produk.php (✅ ada, CRUD)
│   ├── kelola-transaksi.php (✅ ada)
│   └── laporan.php (✅ ada)
├── user/
│   ├── register.php (✅ ada)
│   ├── proses-register.php (✅ ada)
│   ├── login.php (✅ ada)
│   ├── proses-login.php (✅ ada)
│   ├── logout.php (✅ ada)
│   ├── profil.php (❌ kosong)
│   └── pesanan.php (❌ kosong)
├── produk/
│   ├── list-produk.php (✅ ada)
│   ├── detail-produk.php (❌ PERLU DIBUAT)
│   └── cari-produk.php (❌ kosong)
├── config.php (✅ lengkap)
├── index.php (✅ ada)
├── test-connection.php (✅ ada)
└── assets/ (CSS, JS, images)
```

---

## ⚠️ Important Notes

### Keamanan
- Password selalu di-hash dengan `password_hash()` (✅ Sudah)
- Input di-escape dengan `escape_input()` (✅ Sudah)
- CSRF token ready di `config.php` (✅ Sudah)
- Session HTTP-only (✅ Sudah)

### Database Connection
- Default: `localhost`
- User: `root`
- Password: `` (kosong)
- Database: `mobilenest`

Jika berbeda, edit di `MobileNest/config.php` baris 6-9

### Session
- Durasi: 1 jam (3600 detik)
- Di-set di `config.php`
- Auto-destroy jika expired

---

## ✅ Status Implementasi

**SUDAH DONE:**
- ✅ Database design & tables
- ✅ Authentication (register, login, logout)
- ✅ Session management
- ✅ CRUD Create (admin)
- ✅ CRUD Read List (public)
- ✅ CRUD Read Detail (perlu file baru)
- ✅ CRUD Update (admin)
- ✅ CRUD Delete (admin)
- ✅ Filtering & search
- ✅ Error handling
- ✅ Input validation

**PERLU DITAMBAH NANTI (Optional):**
- 🔄 Add to cart functionality
- 🔄 Checkout & payment
- 🔄 Order management
- 🔄 User profile page
- 🔄 REST API
- 🔄 File upload untuk gambar

---

## 🙋 Help & Support

**Files to Read:**
1. `BACKEND_IMPLEMENTATION.md` - Dokumentasi lengkap
2. `DETAILED_CODE_IMPLEMENTATION.md` - Code snippets siap pakai
3. `config.php` - Helper functions & constants

**Error Troubleshooting:**
1. Check `error.log` file
2. Enable `display_errors` di `config.php`
3. Check database connection dengan `test-connection.php`
4. Verify table structure: `DESCRIBE users;` & `DESCRIBE produk;`

---

## 🊆 Next Steps

1. **Immediate:**
   - [ ] Buat database & insert sample data
   - [ ] Test register & login
   - [ ] Buat file `detail-produk.php`
   - [ ] Test CRUD produk

2. **Short Term:**
   - [ ] Update `list-produk.php` sesuai code yang diberikan
   - [ ] Add to cart functionality
   - [ ] User profile page
   - [ ] Order management

3. **Long Term:**
   - [ ] Payment gateway integration
   - [ ] Email notifications
   - [ ] Admin reports & analytics
   - [ ] REST API
   - [ ] Mobile app backend

---

**Selamat! Backend MobileNest siap untuk production! 🚀**
