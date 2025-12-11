# MobileNest - Database Structure (Sesuai Database Actual)

## 📊 Database Info

**Database Name:** `mobilenest_db`

**Total Tables:** 8

---

## 📋 Struktur Tabel yang Ada

### 1. **users** (Tabel User/Akun)

Untuk menyimpan data pengguna (customer & admin)

```sql
SHOW COLUMNS FROM users;
```

**Fields yang kemungkinan ada:**
- `id_user` - Primary key
- `nama_lengkap` - Nama lengkap user
- `email` - Email (unique)
- `username` - Username (unique)
- `password` - Password (hashed)
- `created_at` - Timestamp pembuatan akun
- (kemungkinan ada field lain)

**Verifikasi di phpMyAdmin:**
1. Pilih database: `mobilenest_db`
2. Klik tabel: `users`
3. Klik tab: "Structure"
4. Lihat semua fields

---

### 2. **produk** (Tabel Produk)

Untuk menyimpan data produk/merchandise

**Fields yang kemungkinan ada:**
- `id_produk` - Primary key
- `nama_produk` - Nama produk
- `merek` - Merek/Brand
- `deskripsi` - Deskripsi produk
- `spesifikasi` - Spesifikasi teknis
- `harga` - Harga produk
- `stok` - Jumlah stok
- `gambar` - URL gambar
- `kategori` - Kategori produk
- `status_produk` - Status (Tersedia/Tidak Tersedia)
- `tanggal_ditambahkan` - Timestamp
- `updated_at` - Timestamp update

---

### 3. **keranjang** (Tabel Shopping Cart)

Untuk menyimpan item di keranjang belanja user

**Fields yang kemungkinan ada:**
- `id_keranjang` - Primary key
- `id_user` - Foreign key ke users
- `id_produk` - Foreign key ke produk
- `jumlah` - Quantity
- `tanggal_ditambahkan` - Timestamp

---

### 4. **transaksi** (Tabel Transaksi/Order)

Untuk menyimpan data transaksi/pemesanan

**Fields yang kemungkinan ada:**
- `id_transaksi` - Primary key
- `id_user` - Foreign key ke users
- `total_harga` - Total harga order
- `status_transaksi` - Status order (Pending/Confirmed/Shipped/Done)
- `tanggal_transaksi` - Timestamp order
- `tanggal_dikirim` - Timestamp pengiriman
- (kemungkinan ada field lain)

---

### 5. **detail_transaksi** (Tabel Detail Order)

Untuk menyimpan detail item dalam setiap transaksi

**Fields yang kemungkinan ada:**
- `id_detail` - Primary key
- `id_transaksi` - Foreign key ke transaksi
- `id_produk` - Foreign key ke produk
- `jumlah` - Quantity
- `harga_satuan` - Harga per item saat order
- `subtotal` - Jumlah * Harga

---

### 6. **promo** (Tabel Promo/Diskon)

Untuk menyimpan data promosi dan diskon

**Fields yang kemungkinan ada:**
- `id_promo` - Primary key
- `nama_promo` - Nama promosi
- `kode_promo` - Kode promo unik
- `diskon_persen` - Persentase diskon
- `diskon_nominal` - Nominal diskon
- `tanggal_mulai` - Mulai promo
- `tanggal_akhir` - Akhir promo
- `status` - Active/Inactive

---

### 7. **ulasan** (Tabel Review/Rating)

Untuk menyimpan ulasan/review produk dari user

**Fields yang kemungkinan ada:**
- `id_ulasan` - Primary key
- `id_produk` - Foreign key ke produk
- `id_user` - Foreign key ke users
- `rating` - Rating 1-5 bintang
- `komentar` - Text review
- `tanggal_ulasan` - Timestamp

---

### 8. **admin** (Tabel Admin/Staff)

Untuk menyimpan data admin/staff

**Fields yang kemungkinan ada:**
- `id_admin` - Primary key
- `nama` - Nama admin
- `email` - Email admin
- `password` - Password hashed
- `role` - Role (Admin/Manager/Staff)
- `tanggal_dibuat` - Timestamp

---

## 🔍 Verifikasi Database Anda di phpMyAdmin

### Cara 1: Lihat semua tabel

1. Buka phpMyAdmin: `http://localhost/phpmyadmin/`
2. Klik database: `mobilenest_db` (di sidebar kiri)
3. Lihat daftar tabel di tengah
4. Catat nama-nama tabel yang ada

### Cara 2: Cek struktur tabel

1. Pilih tabel (misal: `users`)
2. Klik tab: "Structure"
3. Lihat semua field & tipe data
4. Catat field apa saja yang ada

### Cara 3: Lihat data di tabel

1. Pilih tabel
2. Klik tab: "Browse"
3. Lihat berapa banyak row/data
4. Lihat isi data

---

## 🔗 Relasi Antar Tabel

```
users (1) ──────── (*) keranjang
  │                        │
  │                        └── produk
  │
  ├──── (*) transaksi
  │          │
  │          └── (*) detail_transaksi
  │                    │
  │                    └── produk
  │
  └──── (*) ulasan
           │
           └── produk

admin (independent)

promo (independent)
```

**Penjelasan:**
- `users (1)` : 1 user bisa punya banyak data
- `(*)` : relasi many
- Foreign Key: menghubungkan antar tabel

---

## 📝 SQL untuk Verifikasi Struktur

### Lihat semua tabel
```sql
SHOW TABLES FROM mobilenest_db;
```

### Lihat struktur tabel users
```sql
DESCRIBE users;
-- atau
SHOW COLUMNS FROM users;
```

### Lihat struktur semua tabel
```sql
DESCRIBE users;
DESCRIBE produk;
DESCRIBE keranjang;
DESCRIBE transaksi;
DESCRIBE detail_transaksi;
DESCRIBE promo;
DESCRIBE ulasan;
DESCRIBE admin;
```

### Count data di setiap tabel
```sql
SELECT 
  (SELECT COUNT(*) FROM users) as total_users,
  (SELECT COUNT(*) FROM produk) as total_produk,
  (SELECT COUNT(*) FROM keranjang) as total_keranjang,
  (SELECT COUNT(*) FROM transaksi) as total_transaksi,
  (SELECT COUNT(*) FROM promo) as total_promo,
  (SELECT COUNT(*) FROM ulasan) as total_ulasan,
  (SELECT COUNT(*) FROM admin) as total_admin;
```

---

## ✅ Checklist Verifikasi Database

- [ ] Database `mobilenest_db` ada
- [ ] Tabel `users` ada
- [ ] Tabel `produk` ada
- [ ] Tabel `keranjang` ada
- [ ] Tabel `transaksi` ada
- [ ] Tabel `detail_transaksi` ada
- [ ] Tabel `promo` ada
- [ ] Tabel `ulasan` ada
- [ ] Tabel `admin` ada
- [ ] Field di setiap tabel sesuai kebutuhan
- [ ] Sudah ada sample data

---

## 🔧 Update config.php (PENTING!)

File `config.php` di project Anda perlu diupdate dengan nama database yang benar:

```php
<?php
// MobileNest/config.php

// ===== DATABASE CONNECTION =====
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';  // Password kosong jika default XAMPP
$db_name = 'mobilenest_db';  // ← UBAH INI sesuai database Anda!

// Create MySQLi connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_errno) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Set charset
$conn->set_charset('utf8mb4');

// ... rest of config.php
?>
```

**PENTING:** Ubah baris ini dari:
```php
$db_name = 'mobilenest';  // ❌ Salah
```

Menjadi:
```php
$db_name = 'mobilenest_db';  // ✅ Benar
```

---

## 🎯 Testing Setelah Update config.php

### 1. Test Connection

Buka di browser:
```
http://localhost/MobileNest/test-connection.php
```

**Expected Result:**
```
✅ Connection successful
✅ Database: mobilenest_db
✅ Tables: users, produk, keranjang, transaksi, detail_transaksi, promo, ulasan, admin
```

### 2. Test Register

Buka:
```
http://localhost/MobileNest/user/register.php
```

Ku user baru, harusnya bisa menyimpan ke tabel `users`

### 3. Test Login

Buka:
```
http://localhost/MobileNest/user/login.php
```

Login dengan data yang baru diregister

### 4. Test CRUD Produk

Buka:
```
http://localhost/MobileNest/produk/list-produk.php
```

Harusnya tampil produk dari tabel `produk`

---

## 📌 Catatan Penting

1. **Database Name:** Gunakan `mobilenest_db` (bukan `mobilenest`)
2. **Update config.php:** Ubah `$db_name` di file config.php
3. **Restart:**  Jika sudah update config, refresh browser
4. **Test:** Verifikasi connection dengan test-connection.php

---

## 🚀 Selanjutnya

Setelah database structure terverifikasi & config.php diupdate:

1. Ikuti **QUICK_START_GUIDE.md** Phase 3 (Testing Connection)
2. Ikuti **QUICK_START_GUIDE.md** Phase 4 (Test Authentication)
3. Ikuti **QUICK_START_GUIDE.md** Phase 5 (Test CRUD Produk)

---

**Database structure Anda lebih lengkap dari yang disarankan! Bagus! ✅**
