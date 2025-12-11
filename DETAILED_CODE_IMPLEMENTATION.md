# Detailed Backend Implementation Code for MobileNest

## 1. FILE: `produk/detail-produk.php` (BUAT FILE BARU)

Buat file baru dengan nama `detail-produk.php` di folder `produk/` dan copy-paste code berikut:

```php
<?php
require_once __DIR__ . '/../config.php';

// Validasi ID produk
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: list-produk.php');
    exit;
}

$id_produk = (int)escape_input($_GET['id']);

// Ambil detail produk
$query = "SELECT id_produk, nama_produk, merek, deskripsi, spesifikasi, harga, 
                 stok, gambar, kategori, status_produk, tanggal_ditambahkan
          FROM produk 
          WHERE id_produk = $id_produk";

$product = fetch_single($query);

if (!$product) {
    $_SESSION['error'] = 'Produk tidak ditemukan.';
    header('Location: list-produk.php');
    exit;
}

// Ambil produk sejenis (kategori sama, max 4)
$kategori = !empty($product['kategori']) ? escape_input($product['kategori']) : '';
$relatedProducts = [];

if (!empty($kategori)) {
    $relatedQuery = "SELECT id_produk, nama_produk, harga, gambar, kategori
                     FROM produk 
                     WHERE status_produk = 'Tersedia' 
                     AND kategori = '$kategori' 
                     AND id_produk != $id_produk 
                     LIMIT 4";
    $relatedProducts = fetch_all($relatedQuery);
}

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= htmlspecialchars($product['nama_produk']) ?> - MobileNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-image { max-height: 500px; object-fit: cover; }
        .badge-status { font-size: 0.9rem; }
        .price-tag { font-size: 2rem; color: #28a745; font-weight: bold; }
        .specifications { background-color: #f8f9fa; padding: 15px; border-radius: 8px; }
        .related-products { margin-top: 40px; }
        .product-card { transition: transform 0.3s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="../index.php">MobileNest</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="list-produk.php">Produk</a></li>
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="../user/profil.php">Profil</a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="../user/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="../user/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="../user/register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="py-5">
  <div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
        <li class="breadcrumb-item"><a href="list-produk.php">Produk</a></li>
        <?php if (!empty($product['kategori'])): ?>
          <li class="breadcrumb-item"><a href="list-produk.php?kategori=<?= urlencode($product['kategori']) ?>"><?= htmlspecialchars($product['kategori']) ?></a></li>
        <?php endif; ?>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['nama_produk']) ?></li>
      </ol>
    </nav>

    <!-- Product Detail -->
    <div class="row mb-5">
      <!-- Image -->
      <div class="col-lg-5 mb-4">
        <?php if (!empty($product['gambar'])): ?>
          <img src="<?= htmlspecialchars($product['gambar']) ?>" alt="<?= htmlspecialchars($product['nama_produk']) ?>" class="img-fluid rounded product-image border">
        <?php else: ?>
          <div class="bg-light rounded d-flex align-items-center justify-content-center product-image border" style="height: 500px;">
            <span class="text-muted">No Image</span>
          </div>
        <?php endif; ?>
      </div>

      <!-- Info -->
      <div class="col-lg-7">
        <h1 class="mb-2"><?= htmlspecialchars($product['nama_produk']) ?></h1>
        
        <?php if (!empty($product['merek'])): ?>
          <p class="text-muted mb-3"><strong>Merek:</strong> <?= htmlspecialchars($product['merek']) ?></p>
        <?php endif; ?>

        <div class="mb-4">
          <div class="price-tag"><?= format_rupiah($product['harga']) ?></div>
          <span class="badge badge-status bg-<?= $product['status_produk'] === 'Tersedia' ? 'success' : 'danger' ?>">
            <?= htmlspecialchars($product['status_produk']) ?>
          </span>
        </div>

        <!-- Stock -->
        <div class="alert alert-info mb-4">
          <strong>Stok:</strong> 
          <?php if ((int)$product['stok'] > 0): ?>
            <span class="text-success"><?= (int)$product['stok'] ?> unit</span>
          <?php else: ?>
            <span class="text-danger">Habis</span>
          <?php endif; ?>
        </div>

        <!-- Action -->
        <div class="mb-4">
          <?php if ($product['status_produk'] === 'Tersedia' && (int)$product['stok'] > 0): ?>
            <button class="btn btn-primary btn-lg">Tambah ke Keranjang</button>
          <?php else: ?>
            <button class="btn btn-secondary btn-lg" disabled>Tidak Tersedia</button>
          <?php endif; ?>
        </div>

        <!-- Description -->
        <?php if (!empty($product['deskripsi'])): ?>
          <div class="mb-4">
            <h5>Deskripsi</h5>
            <p><?= nl2br(htmlspecialchars($product['deskripsi'])) ?></p>
          </div>
        <?php endif; ?>

        <!-- Category -->
        <?php if (!empty($product['kategori'])): ?>
          <div class="mb-4">
            <p>
              <strong>Kategori:</strong> 
              <a href="list-produk.php?kategori=<?= urlencode($product['kategori']) ?>" class="btn btn-sm btn-outline-secondary">
                <?= htmlspecialchars($product['kategori']) ?>
              </a>
            </p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Specifications -->
    <?php if (!empty($product['spesifikasi'])): ?>
      <div class="row mb-5">
        <div class="col-lg-12">
          <h4 class="mb-3">Spesifikasi</h4>
          <div class="specifications"><?= nl2br(htmlspecialchars($product['spesifikasi'])) ?></div>
        </div>
      </div>
    <?php endif; ?>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
      <div class="related-products">
        <h4 class="mb-4">Produk Sejenis</h4>
        <div class="row g-4">
          <?php foreach ($relatedProducts as $related): ?>
            <div class="col-md-6 col-lg-3">
              <div class="card product-card h-100 border-0 shadow-sm">
                <a href="detail-produk.php?id=<?= (int)$related['id_produk'] ?>" class="text-decoration-none text-dark">
                  <?php if (!empty($related['gambar'])): ?>
                    <img src="<?= htmlspecialchars($related['gambar']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;" alt="<?= htmlspecialchars($related['nama_produk']) ?>">
                  <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                      <span class="text-muted">No Image</span>
                    </div>
                  <?php endif; ?>
                  <div class="card-body">
                    <h6 class="card-title text-truncate">{{= htmlspecialchars($related['nama_produk']) }}</h6>
                    <p class="card-text text-success fw-bold"><?= format_rupiah($related['harga']) ?></p>
                  </div>
                </a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<footer class="bg-light border-top mt-5 py-4">
  <div class="container text-center">
    <p class="text-muted mb-0">© 2025 MobileNest</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## 2. UPDATE FILE: `produk/list-produk.php`

Ganti isi `list-produk.php` dengan code berikut (sudah ada, tinggal update):

```php
<?php
require_once __DIR__ . '/../config.php';

// Ambil semua produk dengan status tersedia
$where = "status_produk = 'Tersedia'";
$params = [];

// Filter by kategori
if (!empty($_GET['kategori'])) {
    $kategori = escape_input($_GET['kategori']);
    $where .= " AND kategori = '$kategori'";
}

// Search
if (!empty($_GET['search'])) {
    $search = '%' . escape_input($_GET['search']) . '%';
    $where .= " AND (nama_produk LIKE '$search' OR merek LIKE '$search' OR deskripsi LIKE '$search')";
}

// Query
$query = "SELECT id_produk, nama_produk, merek, harga, stok, gambar, kategori, status_produk
          FROM produk 
          WHERE $where
          ORDER BY tanggal_ditambahkan DESC
          LIMIT 100";

$products = fetch_all($query);

// Get unique categories
$categories = fetch_all("SELECT DISTINCT kategori FROM produk WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori");

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Produk - MobileNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-card { transition: transform 0.3s, box-shadow 0.3s; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 8px 16px rgba(0,0,0,0.15); }
        .product-image { height: 250px; object-fit: cover; }
        .filter-sidebar { background-color: #f8f9fa; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold" href="../index.php">MobileNest</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="list-produk.php">Produk</a></li>
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="../user/profil.php">Profil</a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="../user/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="../user/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="py-5 bg-light">
  <div class="container">
    <h2 class="mb-4">Daftar Produk</h2>

    <div class="row">
      <!-- Sidebar Filter -->
      <div class="col-lg-3 mb-4">
        <div class="filter-sidebar">
          <h5 class="mb-3">Filter Produk</h5>
          
          <!-- Search -->
          <form method="get" action="list-produk.php" class="mb-4">
            <div class="input-group">
              <input type="search" name="search" class="form-control" placeholder="Cari produk..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
              <button class="btn btn-primary" type="submit">Cari</button>
            </div>
          </form>

          <!-- Category Filter -->
          <?php if (!empty($categories)): ?>
            <div class="mb-3">
              <h6>Kategori</h6>
              <div class="list-group list-group-flush">
                <a href="list-produk.php" class="list-group-item list-group-item-action <?= empty($_GET['kategori']) ? 'active' : '' ?>">
                  Semua
                </a>
                <?php foreach ($categories as $cat): ?>
                  <a href="list-produk.php?kategori=<?= urlencode($cat['kategori']) ?>" class="list-group-item list-group-item-action <?= (isset($_GET['kategori']) && $_GET['kategori'] === $cat['kategori']) ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat['kategori']) ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Products Grid -->
      <div class="col-lg-9">
        <?php if (empty($products)): ?>
          <div class="alert alert-info text-center py-5">
            <h5>Produk tidak ditemukan</h5>
            <p class="text-muted">Coba ubah filter atau cari dengan kata kunci lain</p>
          </div>
        <?php else: ?>
          <div class="row g-4">
            <?php foreach ($products as $product): ?>
              <div class="col-md-6 col-lg-4">
                <div class="card product-card h-100 border-0 shadow-sm">
                  <a href="detail-produk.php?id=<?= (int)$product['id_produk'] ?>" class="text-decoration-none text-dark">
                    <?php if (!empty($product['gambar'])): ?>
                      <img src="<?= htmlspecialchars($product['gambar']) ?>" class="card-img-top product-image" alt="<?= htmlspecialchars($product['nama_produk']) ?>">
                    <?php else: ?>
                      <div class="bg-light product-image d-flex align-items-center justify-content-center">
                        <span class="text-muted">No Image</span>
                      </div>
                    <?php endif; ?>
                    <div class="card-body">
                      <h6 class="card-title text-truncate"><?= htmlspecialchars($product['nama_produk']) ?></h6>
                      <?php if (!empty($product['merek'])): ?>
                        <p class="text-muted small mb-2"><?= htmlspecialchars($product['merek']) ?></p>
                      <?php endif; ?>
                      <p class="card-text text-success fw-bold fs-5"><?= format_rupiah($product['harga']) ?></p>
                      <?php if ((int)$product['stok'] > 0): ?>
                        <span class="badge bg-success">Tersedia</span>
                      <?php else: ?>
                        <span class="badge bg-secondary">Habis</span>
                      <?php endif; ?>
                    </div>
                  </a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<footer class="bg-light border-top mt-5 py-4">
  <div class="container text-center">
    <p class="text-muted mb-0">© 2025 MobileNest</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

---

## 3. TESTING & CARA MENGGUNAKAN

### Step 1: Setup Database
```sql
CREATE DATABASE IF NOT EXISTS mobilenest;
USE mobilenest;

-- Tabel users
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel produk
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

-- Insert sample produk
INSERT INTO produk (nama_produk, merek, deskripsi, harga, stok, kategori, status_produk) VALUES
('iPhone 15 Pro', 'Apple', 'Smartphone flagship terbaru dari Apple', 15999000, 10, 'Flagship', 'Tersedia'),
('Samsung Galaxy S24', 'Samsung', 'Flagship smartphone dari Samsung', 13999000, 8, 'Flagship', 'Tersedia'),
('Xiaomi 14', 'Xiaomi', 'Smartphone mid-range terpercaya', 8999000, 15, 'Mid-Range', 'Tersedia');
```

### Step 2: Test Login
1. Register user baru di `user/register.php`
2. Login di `user/login.php`
3. Check session terbuat: `$_SESSION['user']` sudah ada

### Step 3: Test CRUD

**Lihat Produk:**
```
http://localhost/MobileNest/produk/list-produk.php
```

**Lihat Detail Produk:**
```
http://localhost/MobileNest/produk/detail-produk.php?id=1
```

**Tambah Produk (Admin):**
```
http://localhost/MobileNest/admin/kelola-produk.php
Klik "+ Tambah Produk"
```

**Edit Produk (Admin):**
```
http://localhost/MobileNest/admin/kelola-produk.php
Klik "Edit" pada produk
```

**Hapus Produk (Admin):**
```
http://localhost/MobileNest/admin/kelola-produk.php
Klik "Hapus" pada produk
```

---

## Summary

✅ **Sudah Diimplementasikan:**
- Database structure
- Authentication (register, login, logout)
- Session management
- CRUD Create (kelola-produk.php)
- CRUD Read (list-produk.php, detail-produk.php)
- CRUD Update (kelola-produk.php)
- CRUD Delete (kelola-produk.php)
- Filtering & Search
- Error handling

🔄 **Perlu Ditambahkan Nanti:**
- Add to cart functionality
- Checkout process
- Payment gateway integration
- User profile management
- Order history
- REST API endpoints

