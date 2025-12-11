# MobileNest - Quick Start Guide 🚀

## 🊆 Start Here - Panduan Cepat Menjalankan MobileNest

Panduan ini akan membawa Anda dari 0 hingga aplikasi MobileNest berjalan penuh dalam **15-20 menit**.

---

## 📋 Prerequisite

Pastikan sudah terinstall:
- [x] **XAMPP** (Apache + MySQL + PHP) - [Download](https://www.apachefriends.org/)
- [x] **Git** (untuk clone repo) - [Download](https://git-scm.com/)
- [x] **Text Editor** (VS Code, Sublime, etc.) - Optional
- [x] **Repo MobileNest** sudah di-clone (atau download ZIP)

---

## Phase 1: Setup XAMPP & Database (⏱️ ~5 menit)

### Step 1.1: Start XAMPP Services

**Windows:**
1. Buka file: `C:\xampp\xampp-control.exe` (atau cari di Start Menu)
2. Klik tombol "Start" untuk:
   - Apache
   - MySQL
3. Tunggu sampai berstatus "Running" (warna hijau)

**Mac/Linux:**
```bash
# Terminal
sudo /Applications/XAMPP/xamppfiles/bin/apachectl start
sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start
```

### Step 1.2: Verify XAMPP Running

Buka browser:
```
http://localhost/
```

Hasil: Akan muncul XAMPP Dashboard ✅

### Step 1.3: Akses phpMyAdmin

Buka di browser:
```
http://localhost/phpmyadmin/
```

Hasil: Tampil interface phpMyAdmin ✅

### Step 1.4: Setup Database

Lihat file: **XAMPP_MYSQL_SETUP.md** (di repo) untuk:
- [x] Membuat database "mobilenest"
- [x] Membuat tabel "users"
- [x] Membuat tabel "produk"
- [x] Insert sample data

**Quick SQL untuk langsung copas:**

1. Di phpMyAdmin, klik tab "SQL"
2. Copy-paste semua kode di bawah sekaligus:

```sql
-- Buat database
CREATE DATABASE IF NOT EXISTS mobilenest;
USE mobilenest;

-- Buat tabel users
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buat tabel produk
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

-- Insert sample data produk
INSERT INTO produk (nama_produk, merek, deskripsi, harga, stok, kategori, status_produk) VALUES
('iPhone 15 Pro', 'Apple', 'Smartphone flagship terbaru dengan chip A17 Pro', 15999000, 10, 'Flagship', 'Tersedia'),
('Samsung Galaxy S24', 'Samsung', 'Flagship dengan Snapdragon 8 Gen 3', 13999000, 8, 'Flagship', 'Tersedia'),
('Xiaomi 14', 'Xiaomi', 'Smartphone mid-range dengan kamera berkualitas', 8999000, 15, 'Mid-Range', 'Tersedia'),
('POCO X6', 'Xiaomi', 'Budget smartphone dengan performa gaming', 5999000, 20, 'Budget', 'Tersedia'),
('Samsung Galaxy A54', 'Samsung', 'Mid-range dengan baterai besar', 7499000, 12, 'Mid-Range', 'Tersedia'),
('Realme 12 Pro', 'Realme', 'Smartphone dengan kamera zoom 3x', 6999000, 18, 'Mid-Range', 'Tersedia');
```

3. Klik tombol "Go"
4. Jika sukses, akan muncul pesan hijau ✅

---

## Phase 2: Setup Project Files (⏱️ ~3 menit)

### Step 2.1: Copy Project ke XAMPP

**Windows:**
```bash
# Folder XAMPP default
C:\xampp\htdocs\

# Copy/paste folder MobileNest ke sini
# Hasilnya:
C:\xampp\htdocs\MobileNest\
```

**Mac:**
```bash
# Folder XAMPP default
/Applications/XAMPP/htdocs/

# Copy folder MobileNest ke sini
```

**Linux:**
```bash
# Folder XAMPP default
/opt/lampp/htdocs/

# Copy folder MobileNest ke sini
```

### Step 2.2: Verify Project Files

Buka explorer/file manager, navigate ke folder project:
```
htdocs/MobileNest/
  ├── admin/
  ├── assets/
  ├── user/
  ├── produk/
  ├── config.php
  ├── index.php
  └── test-connection.php
```

Jika ada, berarti sudah benar ✅

---

## Phase 3: Testing Connection (⏱️ ~2 menit)

### Step 3.1: Test Database Connection

Buka di browser:
```
http://localhost/MobileNest/test-connection.php
```

**Expected Result:**
```
✅ Connection successful
✅ Database: mobilenest
✅ Tables: users, produk
```

Jika ada error, lihat **XAMPP_MYSQL_SETUP.md** bagian "Troubleshooting".

### Step 3.2: Verify Database Data

Buka phpMyAdmin:
```
http://localhost/phpmyadmin/
```

1. Di sidebar kiri, klik database "mobilenest"
2. Klik tabel "produk"
3. Klik tab "Browse"
4. Harusnya muncul 6 produk sample ✅

---

## Phase 4: Test Authentication (⏱️ ~5 menit)

### Step 4.1: Test Register

1. Buka di browser:
   ```
   http://localhost/MobileNest/user/register.php
   ```

2. Isi form:
   - Nama Lengkap: `Test User`
   - Email: `testuser@example.com`
   - Password: `password123`
   - Konfirmasi Password: `password123`

3. Klik tombol "Daftar"

4. **Expected Result:**
   - Redirect ke halaman login
   - Muncul pesan: "Registrasi berhasil. Silakan login."
   - Data tersimpan di tabel `users` ✅

### Step 4.2: Verify User Tersimpan

1. Buka phpMyAdmin
2. Pilih database "mobilenest"
3. Klik tabel "users"
4. Klik tab "Browse"
5. Harusnya ada 1 row dengan email "testuser@example.com" ✅

### Step 4.3: Test Login

1. Buka:
   ```
   http://localhost/MobileNest/user/login.php
   ```

2. Isi form:
   - Email: `testuser@example.com`
   - Password: `password123`

3. Klik tombol "Masuk"

4. **Expected Result:**
   - Berhasil login
   - Session aktif (bisa lihat data user)
   - Redirect ke dashboard atau homepage ✅

### Step 4.4: Test Logout

1. Jika sudah login, klik link "Logout" (biasanya di navbar/header)
2. Session akan dihapus
3. Redirect ke halaman login ✅

---

## Phase 5: Test CRUD Produk (⏱️ ~5 menit)

### Step 5.1: Test READ (List Produk)

Buka:
```
http://localhost/MobileNest/produk/list-produk.php
```

**Expected Result:**
- Tampil grid/list 6 produk sample
- Setiap produk menampilkan: nama, merek, harga, gambar
- Ada filter kategori (sidebar)
- Ada search box ✅

### Step 5.2: Test READ (Detail Produk)

1. Di list-produk, klik salah satu produk (misal: iPhone 15 Pro)
2. Buka detail produk:
   ```
   http://localhost/MobileNest/produk/detail-produk.php?id=1
   ```

**Expected Result:**
- Tampil detail lengkap produk
- Gambar produk
- Harga, stok, kategori
- Deskripsi & spesifikasi
- Related products (kategori sama) ✅

### Step 5.3: Test CREATE (Tambah Produk)

**Note:** Fitur ini hanya untuk Admin

1. Login dengan akun yang punya akses admin (atau buat user baru)
2. Buka:
   ```
   http://localhost/MobileNest/admin/kelola-produk.php
   ```

3. Klik button "+ Tambah Produk"
4. Form modal terbuka
5. Isi data produk:
   - Nama: `Test Product`
   - Merek: `Test Brand`
   - Harga: `999000`
   - Stok: `5`
   - Kategori: `Test`

6. Klik "Simpan"

**Expected Result:**
- Produk berhasil ditambah
- Muncul pesan sukses (hijau)
- Data tersimpan di tabel `produk`
- Produk muncul di list ✅

### Step 5.4: Test UPDATE (Edit Produk)

1. Di halaman kelola-produk.php
2. Cari produk yang baru ditambah ("Test Product")
3. Klik button "Edit"
4. Form modal terbuka dengan data produk
5. Ubah nama: `Test Product Updated`
6. Ubah harga: `1999000`
7. Klik "Simpan Perubahan"

**Expected Result:**
- Produk berhasil diupdate
- Muncul pesan sukses (hijau)
- Data terupdate di tabel `produk`
- List menampilkan data terbaru ✅

### Step 5.5: Test DELETE (Hapus Produk)

1. Di halaman kelola-produk.php
2. Cari produk yang baru diupdate ("Test Product Updated")
3. Klik button "Hapus"
4. Dialog konfirmasi muncul: "Hapus produk ini?"
5. Klik "OK"

**Expected Result:**
- Produk berhasil dihapus
- Muncul pesan sukses (hijau)
- Produk tidak ada di list
- Data hilang dari tabel `produk` ✅

---

## 🌟 Aplikasi Siap! (Opsional: Extra Testing)

### Extra: Test Filter & Search

1. Buka: `http://localhost/MobileNest/produk/list-produk.php`

**Test Filter Kategori:**
- Sidebar kiri, klik kategori "Flagship"
- Harusnya hanya tampil produk kategori Flagship ✅

**Test Search:**
- Search box, ketik: "iPhone"
- Tekan Enter atau klik Cari
- Harusnya tampil produk yang mengandung kata "iPhone" ✅

### Extra: Test Pagination

Jika ada banyak produk (>20), harusnya ada tombol pagination (Previous/Next) ✅

---

## 📄 File Documentation

Jika ada masalah atau ingin deep dive, baca file-file di repo:

| File | Tujuan |
|------|--------|
| **XAMPP_MYSQL_SETUP.md** | Panduan setup database XAMPP+MySQL |
| **IMPLEMENTATION_SUMMARY.md** | Ringkasan implementasi backend |
| **DETAILED_CODE_IMPLEMENTATION.md** | Code snippets siap copy-paste |
| **BACKEND_IMPLEMENTATION.md** | Dokumentasi lengkap backend |
| **README.md** | Intro project MobileNest |

---

## 🚠 Troubleshooting Quick Fix

### "Connection refused" atau "Can't connect to MySQL"
```
→ Solusi: Start MySQL di XAMPP Control Panel
```

### "Unknown database 'mobilenest'"
```
→ Solusi: Buat database (lihat Phase 1 Step 1.4)
```

### "No such table: 'produk'"
```
→ Solusi: Buat tabel (lihat Phase 1 Step 1.4)
```

### "404 Not Found" atau "This page doesn't exist"
```
→ Solusi: Copy MobileNest folder ke C:\xampp\htdocs\
```

### Produk tidak tampil di list-produk.php
```
→ Solusi: Insert sample data produk (lihat Phase 1 Step 1.4)
```

---

## ✅ Final Checklist

- [ ] XAMPP running (Apache + MySQL hijau)
- [ ] phpMyAdmin accessible
- [ ] Database "mobilenest" dibuat
- [ ] Tabel "users" & "produk" dibuat
- [ ] Sample data produk inserted
- [ ] test-connection.php bisa diakses
- [ ] Register page berfungsi
- [ ] Login page berfungsi
- [ ] List produk menampilkan data
- [ ] Detail produk berfungsi
- [ ] CRUD (C/R/U/D) semua berfungsi

Semua checklist selesai? **Selamat! MobileNest siap digunakan!** 🎉

---

## 🚀 Next Steps

Setelah aplikasi berjalan, Anda bisa:

1. **Customize tampilan:**
   - Edit CSS di `assets/css/`
   - Edit HTML di file-file PHP

2. **Tambah fitur:**
   - Add to cart
   - Checkout page
   - Payment gateway
   - User profile
   - Order history

3. **Deploy ke server:**
   - Gunakan cPanel/Hosting panel
   - Upload ke server
   - Update config database

4. **Maintenance:**
   - Regular database backup
   - Monitor error logs
   - Update security patches

---

## 📞 Support

Jika ada pertanyaan atau error yang tidak ada di troubleshooting:
1. Cek file dokumentasi di repo
2. Google error message + "PHP MySQL XAMPP"
3. Check Stack Overflow

**Good Luck! 🌟**
