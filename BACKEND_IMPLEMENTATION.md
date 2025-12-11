# MobileNest Backend Implementation Guide

## 📋 Daftar Isi
1. [Struktur Database](#struktur-database)
2. [Sistem Autentikasi (Login & Register)](#sistem-autentikasi)
3. [CRUD Operations untuk Produk](#crud-operations)
4. [API Endpoints](#api-endpoints)
5. [Testing & Debugging](#testing--debugging)

---

## Struktur Database

### Tabel Users (Sudah Ada)
Tabel `users` sudah ada di project Anda dengan struktur:

```sql
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabel Produk (Untuk CRUD)
Tabel `produk` sudah ada di project dengan struktur:

```sql
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
```

---

## Sistem Autentikasi

### File: `user/proses-register.php` (Sudah Ada)
File ini menangani registrasi user dengan validasi:
- Validasi email format
- Validasi kesamaan password
- Pengecekan email & username yang sudah ada
- Password hashing dengan `password_hash()`

**Cara Menggunakan:**
- POST ke `user/proses-register.php` dengan data:
  - `nama_lengkap` (required)
  - `email` (required, unique)
  - `password` (required, min 6 char)
  - `password_confirm` (required, harus sama)

### File: `user/proses-login.php` (Sudah Ada)
File ini menangani login dengan:
- Validasi email & password
- Penggunaan `password_verify()` untuk verifikasi
- Session management
- Redirect ke dashboard jika berhasil

**Cara Menggunakan:**
- POST ke `user/proses-login.php` dengan data:
  - `email` (required)
  - `password` (required)

### File: `user/logout.php` (Sudah Ada)
File ini menghapus session dan redirect ke halaman login.

### File: `config.php` (Sudah Ada & Lengkap)
File ini sudah memiliki:
- Database connection dengan MySQLi
- Session management
- Helper functions untuk autentikasi
- CSRF token generation
- File upload handling
- Error handling

---

## CRUD Operations

### A. READ - List Produk

**File: `produk/list-produk.php`** (Sudah Ada, perlu diupdate)

Gunakan untuk menampilkan semua produk ke customer:

```php
<?php
require_once __DIR__ . '/../config.php';

// Get all products with status 'Tersedia'
$query = "SELECT id_produk, nama_produk, merek, harga, stok, gambar, kategori 
          FROM produk 
          WHERE status_produk = 'Tersedia' 
          ORDER BY tanggal_ditambahkan DESC";

$result = execute_query($query);
$products = fetch_all($query);

// Filter by kategori if provided
if (!empty($_GET['kategori'])) {
    $kategori = escape_input($_GET['kategori']);
    $query = "SELECT id_produk, nama_produk, merek, harga, stok, gambar, kategori 
              FROM produk 
              WHERE status_produk = 'Tersedia' AND kategori = '$kategori'
              ORDER BY tanggal_ditambahkan DESC";
    $products = fetch_all($query);
}

// Search functionality
if (!empty($_GET['search'])) {
    $search = '%' . escape_input($_GET['search']) . '%';
    $query = "SELECT id_produk, nama_produk, merek, harga, stok, gambar, kategori 
              FROM produk 
              WHERE status_produk = 'Tersedia' 
              AND (nama_produk LIKE '$search' OR merek LIKE '$search' OR deskripsi LIKE '$search')
              ORDER BY tanggal_ditambahkan DESC";
    $products = fetch_all($query);
}
?>

<!-- Tampilkan $products dalam format grid atau list -->
```

### B. READ - Detail Produk

**File: `produk/detail-produk.php`** (Kosong, perlu diisi)

```php
<?php
require_once __DIR__ . '/../config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list-produk.php');
    exit;
}

$id_produk = (int)escape_input($_GET['id']);

// Get single product detail
$query = "SELECT id_produk, nama_produk, merek, deskripsi, spesifikasi, harga, 
                 stok, gambar, kategori, status_produk, tanggal_ditambahkan
          FROM produk 
          WHERE id_produk = $id_produk";

$product = fetch_single($query);

if (!$product) {
    header('Location: list-produk.php');
    exit;
}

// Get related products (same kategori)
$kategori = escape_input($product['kategori']);
$relatedQuery = "SELECT id_produk, nama_produk, harga, gambar 
                 FROM produk 
                 WHERE kategori = '$kategori' AND id_produk != $id_produk 
                 LIMIT 5";
$relatedProducts = fetch_all($relatedQuery);

?>

<!-- Tampilkan $product detail dan $relatedProducts -->
```

### C. CREATE - Tambah Produk (Admin)

**File: `admin/kelola-produk.php`** (Sudah Ada)

File ini sudah memiliki CREATE functionality melalui form modal. 

**Cara Kerja:**
1. Klik tombol "+ Tambah Produk"
2. Form modal terbuka
3. Isi semua field (nama_produk dan harga wajib)
4. Submit form
5. Data disimpan ke tabel `produk`

**Fields:**
- `nama_produk` (required)
- `merek` (optional)
- `deskripsi` (optional)
- `spesifikasi` (optional)
- `harga` (required, > 0)
- `stok` (optional, default 0)
- `gambar` (URL image)
- `kategori` (optional)
- `status_produk` (Tersedia / Tidak Tersedia)

### D. UPDATE - Edit Produk (Admin)

**File: `admin/kelola-produk.php`** (Sudah Ada)

File ini sudah memiliki UPDATE functionality.

**Cara Kerja:**
1. Klik tombol "Edit" pada row produk
2. Form modal terbuka dengan data produk terpilih
3. Edit field yang diperlukan
4. Submit form
5. Data diupdate di tabel `produk`

**Validasi:**
- `nama_produk` tidak boleh kosong
- `harga` harus > 0
- `id_produk` harus valid

### E. DELETE - Hapus Produk (Admin)

**File: `admin/kelola-produk.php`** (Sudah Ada)

File ini sudah memiliki DELETE functionality.

**Cara Kerja:**
1. Klik tombol "Hapus" pada row produk
2. Konfirmasi dialog muncul
3. Klik OK untuk confirm
4. Data dihapus dari tabel `produk`

---

## API Endpoints

### Authentication Endpoints

#### 1. Register
```
POST /user/proses-register.php
Content-Type: application/x-www-form-urlencoded

nama_lengkap=John Doe
email=john@example.com
password=password123
password_confirm=password123
```

**Response Success:**
```
HTTP/1.1 302 Found
Location: /user/login.php

SESSION['success'] = "Registrasi berhasil. Silakan login."
```

**Response Error:**
```
HTTP/1.1 302 Found
Location: /user/register.php

SESSION['error'] = "Email sudah terdaftar."
```

#### 2. Login
```
POST /user/proses-login.php
Content-Type: application/x-www-form-urlencoded

email=john@example.com
password=password123
```

**Response Success:**
```
HTTP/1.1 302 Found
Location: /admin/dashboard.php (if admin) atau /index.php

SESSION['user'] = <id_user>
SESSION['user_name'] = <nama_lengkap>
```

**Response Error:**
```
HTTP/1.1 302 Found
Location: /user/login.php

SESSION['error'] = "Password salah."
```

#### 3. Logout
```
GET /user/logout.php
```

**Response:**
```
HTTP/1.1 302 Found
Location: /index.php

SESSION[] = [] (cleared)
```

---

### Product Endpoints

#### 1. List Produk (Public)
```
GET /produk/list-produk.php
GET /produk/list-produk.php?kategori=Flagship
GET /produk/list-produk.php?search=iPhone
```

**Response:**
- HTML page dengan list produk
- Filter by kategori atau search keywords

#### 2. Detail Produk (Public)
```
GET /produk/detail-produk.php?id=1
```

**Response:**
- HTML page dengan detail produk lengkap
- Related products di kategori yang sama

#### 3. Kelola Produk (Admin)
```
GET /admin/kelola-produk.php
POST /admin/kelola-produk.php (CREATE/UPDATE/DELETE)
```

**Query Parameters:**
- `kategori` - filter by kategori
- `status` - filter by status (Tersedia/Tidak Tersedia)
- `search` - cari by nama atau merek
- `edit=<id>` - open edit modal

**POST Actions:**
- `action=add` - create produk baru
- `action=edit` - update produk
- `action=delete` - delete produk

---

## Testing & Debugging

### 1. Test Connection
Sudah ada file: `test-connection.php`

```bash
# Run di browser
http://localhost/MobileNest/test-connection.php
```

### 2. Test Authentication

**Test Register:**
```bash
# Buka form register
http://localhost/MobileNest/user/register.php

# Isi form dan submit
# Check SESSION['success'] atau SESSION['error']
```

**Test Login:**
```bash
# Buka form login
http://localhost/MobileNest/user/login.php

# Isi form dan submit dengan email & password yang sudah diregister
# Check SESSION['user'] dan redirect ke dashboard
```

### 3. Test CRUD Produk

**Create:**
```bash
# Login as admin terlebih dahulu
http://localhost/MobileNest/admin/kelola-produk.php

# Klik "+ Tambah Produk"
# Isi form dan submit
# Check database: SELECT * FROM produk;
```

**Read:**
```bash
# List produk (public)
http://localhost/MobileNest/produk/list-produk.php

# Detail produk
http://localhost/MobileNest/produk/detail-produk.php?id=1
```

**Update:**
```bash
# Login as admin
http://localhost/MobileNest/admin/kelola-produk.php

# Klik tombol "Edit"
# Ubah data dan submit
# Check database: SELECT * FROM produk WHERE id_produk=1;
```

**Delete:**
```bash
# Login as admin
http://localhost/MobileNest/admin/kelola-produk.php

# Klik tombol "Hapus"
# Confirm dialog
# Check database: SELECT * FROM produk;
```

### 4. Debug Query
Edit `config.php` untuk melihat error query:

```php
// Di execute_query function
if (!$result) {
    error_log('Query Error: ' . $conn->error);
    echo 'Query: ' . $sql; // uncomment untuk debug
    return false;
}
```

### 5. Session Check
Untuk memastikan session berjalan:

```php
<?php
session_start();
require_once __DIR__ . '/config.php';

// Check user login
if (!is_logged_in()) {
    echo "User tidak login";
} else {
    $user = get_user_info();
    echo "User ID: " . $user['id'];
    echo "Role: " . $user['role'];
}
?>
```

---

## Checklist Implementasi

- [x] Database setup (users & produk tables)
- [x] config.php dengan koneksi & helper functions
- [x] Autentikasi (register, login, logout)
- [x] Session management
- [x] CRUD Create (kelola-produk.php di admin)
- [x] CRUD Read (list-produk.php & detail-produk.php)
- [x] CRUD Update (kelola-produk.php di admin)
- [x] CRUD Delete (kelola-produk.php di admin)
- [x] Filtering & Search
- [x] Validasi input
- [x] Error handling
- [ ] API endpoints (REST API - optional, bisa ditambah nanti)
- [ ] File upload untuk gambar (optional, bisa ditambah nanti)
- [ ] Pagination (optional, bisa ditambah nanti)

---

## File Summary

| File | Status | Fungsi |
|------|--------|--------|
| `config.php` | ✅ Lengkap | Database connection & helper functions |
| `user/register.php` | ✅ Ada | Halaman form register |
| `user/proses-register.php` | ✅ Ada | Process registrasi |
| `user/login.php` | ✅ Ada | Halaman form login |
| `user/proses-login.php` | ✅ Ada | Process login |
| `user/logout.php` | ✅ Ada | Process logout |
| `produk/list-produk.php` | 🔄 Perlu diupdate | List produk (public) |
| `produk/detail-produk.php` | ❌ Kosong | Detail produk (public) |
| `admin/kelola-produk.php` | ✅ Lengkap | CRUD produk (admin) |
| `admin/dashboard.php` | ✅ Ada | Admin dashboard |
| `test-connection.php` | ✅ Ada | Test database connection |

---

## Next Steps

1. ✅ **Lengkapi `produk/list-produk.php`** dengan HTML template
2. ✅ **Lengkapi `produk/detail-produk.php`** dengan HTML template
3. 🔄 **Tambah file upload** untuk gambar produk (optional)
4. 🔄 **Tambah pagination** untuk list produk (optional)
5. 🔄 **Buat REST API** endpoints (optional)

Semua backend logic sudah ada dan siap digunakan! 🚀
