# XAMPP MySQL Setup untuk MobileNest

## 📋 Daftar Isi
1. [Verifikasi XAMPP](#verifikasi-xampp)
2. [Membuat Database](#membuat-database)
3. [Membuat Tabel](#membuat-tabel)
4. [Insert Sample Data](#insert-sample-data)
5. [Testing Connection PHP](#testing-connection-php)
6. [Troubleshooting](#troubleshooting)

---

## 1. Verifikasi XAMPP

### 1.1 Start XAMPP Services

**Windows:**
1. Buka `XAMPP Control Panel` (biasanya di `C:\xampp\xampp-control.exe`)
2. Pastikan **Apache** dan **MySQL** berstatus "Running" (warna hijau)
   - Jika belum, klik tombol "Start" masing-masing

**Mac/Linux:**
```bash
# Buka terminal di folder XAMPP
cd /Applications/XAMPP/xamppfiles/bin

# Start Apache
sudo ./apachectl start

# Start MySQL
sudo ./mysql.server start
```

### 1.2 Verifikasi Services Berjalan

Buka browser dan buka URL berikut:
```
http://localhost/
```

Jika muncul XAMPP Dashboard = semuanya berjalan dengan baik ✅

---

## 2. Membuat Database

### 2.1 Akses phpMyAdmin

Buka browser:
```
http://localhost/phpmyadmin/
```

Tampilannya seperti ini:
```
┌─────────────────────────────────────────────┐
│         phpMyAdmin Dashboard                │
│  [Databases] [SQL] [Export] [Import] ...    │
│                                             │
│  - Left sidebar: list databases             │
│  - Main area: database management tools     │
└─────────────────────────────────────────────┘
```

### 2.2 Buat Database "mobilenest"

**Cara 1: Via Menu Databases**
1. Klik tab "Databases" di navbar
2. Kolom input: "Create database"
3. Isi nama: `mobilenest`
4. Klik tombol "Create"

Hasil:
```
✅ Database mobilenest created successfully.
```

**Cara 2: Via SQL Query**
1. Klik tab "SQL"
2. Paste query:
```sql
CREATE DATABASE IF NOT EXISTS mobilenest;
```
3. Klik tombol "Go" (atau tekan Ctrl+Enter)

Hasil:
```
✅ 1 row affected.
```

### 2.3 Verify Database Dibuat

Di sidebar kiri, Anda akan melihat:
```
📁 Databases
  📄 information_schema
  📄 mysql
  📄 performance_schema
  📄 phpmyadmin
  📄 test
  ✨ mobilenest  ← Database baru
```

Klik nama database untuk masuk.

---

## 3. Membuat Tabel

### 3.1 Tabel Users

Setelah memilih database `mobilenest`:

1. Klik tab "SQL"
2. Paste query:

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

3. Klik "Go"

**Hasil:**
```
✅ Table users created successfully.
```

### 3.2 Tabel Produk

Paste query:

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

Klik "Go"

**Hasil:**
```
✅ Table produk created successfully.
```

### 3.3 Verify Tabel

Di phpMyAdmin, Anda akan melihat:

```
📁 mobilenest
  📊 users (6 columns)
  📊 produk (12 columns)
```

Klik nama tabel untuk lihat struktur & data.

---

## 4. Insert Sample Data

### 4.1 Insert Sample Users

1. Di phpMyAdmin, pilih database `mobilenest`
2. Klik tab "SQL"
3. Paste:

```sql
INSERT INTO users (nama_lengkap, email, username, password) VALUES
('John Doe', 'john@example.com', 'johndoe', '$2y$10$1234567890abcdefghijklmnopqr'),
('Jane Smith', 'jane@example.com', 'janesmith', '$2y$10$1234567890abcdefghijklmnopqr');
```

Klik "Go"

**Catatan:** Password di atas adalah hash placeholder. Saat register, PHP akan generate hash baru dengan `password_hash()`.

### 4.2 Insert Sample Produk

Paste:

```sql
INSERT INTO produk (nama_produk, merek, deskripsi, harga, stok, kategori, status_produk) VALUES
('iPhone 15 Pro', 'Apple', 'Smartphone flagship terbaru dengan chip A17 Pro yang powerful', 15999000, 10, 'Flagship', 'Tersedia'),
('Samsung Galaxy S24', 'Samsung', 'Flagship smartphone dengan prosesor Snapdragon 8 Gen 3', 13999000, 8, 'Flagship', 'Tersedia'),
('Xiaomi 14', 'Xiaomi', 'Smartphone mid-range dengan kamera berkualitas tinggi', 8999000, 15, 'Mid-Range', 'Tersedia'),
('POCO X6', 'Xiaomi', 'Budget smartphone dengan performa gaming mumpuni', 5999000, 20, 'Budget', 'Tersedia'),
('Samsung Galaxy A54', 'Samsung', 'Smartphone mid-range dengan baterai besar', 7499000, 12, 'Mid-Range', 'Tersedia'),
('Realme 12 Pro', 'Realme', 'Smartphone dengan kamera zoom 3x', 6999000, 18, 'Mid-Range', 'Tersedia');
```

Klik "Go"

**Hasil:**
```
✅ 6 rows inserted.
```

### 4.3 Verify Data

Untuk lihat data yang sudah di-insert:

1. Klik tabel di sidebar (misal: `produk`)
2. Klik tab "Browse"
3. Lihat data dalam format table

Atau gunakan SQL query:

```sql
SELECT * FROM produk;
```

Hasil:
```
id_produk | nama_produk      | merek   | harga     | stok | kategori
1         | iPhone 15 Pro    | Apple   | 15999000  | 10   | Flagship
2         | Samsung Galaxy.. | Samsung | 13999000  | 8    | Flagship
3         | Xiaomi 14        | Xiaomi  | 8999000   | 15   | Mid-Range
...
```

---

## 5. Testing Connection PHP

### 5.1 File: `test-connection.php` (SUDAH ADA)

File ini sudah ada di repo:
```
MobileNest/test-connection.php
```

Buka di browser:
```
http://localhost/MobileNest/test-connection.php
```

Hasil jika berhasil:
```
✅ Connection successful to database: mobilenest
```

Jika error, lihat pesan error & ikuti troubleshooting di bawah.

### 5.2 Buat Test File Sendiri

Jika ingin membuat test file custom, buat file `MobileNest/test-db.php`:

```php
<?php
// Test database connection

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';  // Password kosong (default XAMPP)
$db_name = 'mobilenest';

echo "<h2>Testing Database Connection</h2>";
echo "<p>Host: $db_host</p>";
echo "<p>User: $db_user</p>";
echo "<p>Database: $db_name</p>";
echo "<hr>";

// Test MySQLi Connection
try {
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    
    if ($conn->connect_error) {
        die('<span style="color:red;">❌ Connection failed: ' . $conn->connect_error . '</span>');
    }
    
    echo '<span style="color:green;">✅ Connection successful!</span><br>';
    echo '<p>Database selected: <strong>' . $db_name . '</strong></p>';
    
    // Test query
    $result = $conn->query("SELECT COUNT(*) as total FROM produk");
    if ($result) {
        $row = $result->fetch_assoc();
        echo '<p>Total produk di database: <strong>' . $row['total'] . '</strong></p>';
    }
    
    // List tables
    echo '<h3>Tabel di database:</h3>';
    $tables = $conn->query("SHOW TABLES");
    if ($tables && $tables->num_rows > 0) {
        echo '<ul>';
        while ($table = $tables->fetch_row()) {
            echo '<li>' . $table[0] . '</li>';
        }
        echo '</ul>';
    }
    
    $conn->close();
} catch (Exception $e) {
    echo '<span style="color:red;">❌ Error: ' . $e->getMessage() . '</span>';
}
?>
```

Simpan sebagai `test-db.php`, buka di browser:
```
http://localhost/MobileNest/test-db.php
```

---

## 6. Troubleshooting

### Error: "Connection refused" atau "Can't connect to MySQL"

**Penyebab:** MySQL service tidak berjalan

**Solusi:**
1. Buka XAMPP Control Panel
2. Cari "MySQL"
3. Klik tombol "Start"
4. Tunggu status menjadi "Running" (hijau)
5. Coba refresh browser

### Error: "Access denied for user 'root'@'localhost'"

**Penyebab:** Password salah atau tidak sesuai

**Solusi:**
1. Edit `config.php` di project:
   ```php
   $db_user = 'root';  // Default XAMPP
   $db_pass = '';      // Kosong jika tidak ada password
   ```
2. Atau set password MySQL di XAMPP:
   - phpMyAdmin → User accounts → root → Change password

### Error: "Unknown database 'mobilenest'"

**Penyebab:** Database belum dibuat

**Solusi:**
1. Buka phpMyAdmin: `http://localhost/phpmyadmin/`
2. Buat database bernama `mobilenest` (lihat step 2.2)
3. Coba lagi

### Error: "No such table: 'produk'"

**Penyebab:** Tabel belum dibuat atau nama tabel salah

**Solusi:**
1. Di phpMyAdmin, pilih database `mobilenest`
2. Buat tabel `produk` (lihat step 3.2)
3. Verify struktur tabel benar

### Error: "Headers already sent"

**Penyebab:** Ada output sebelum session_start()

**Solusi:**
1. Pastikan `<?php` di awal file (sebelum output apapun)
2. Pastikan `session_start()` di baris pertama (setelah `<?php`)
3. Hapus whitespace atau BOM di awal file

### Checking MySQL Status di Command Line

**Windows (CMD):**
```bash
# Cek apakah MySQL running
netstat -ano | findstr :3306

# Jika ada output = MySQL running
# Jika tidak ada = MySQL tidak jalan
```

**Mac/Linux:**
```bash
# Cek status
sudo /usr/local/mysql/support-files/mysql.server status

# Start jika belum
sudo /usr/local/mysql/support-files/mysql.server start
```

### Reset MySQL Root Password (XAMPP)

Jika lupa password:

1. Stop MySQL di XAMPP Control Panel
2. Buka Command Prompt / Terminal
3. Navigate ke folder XAMPP:
   ```bash
   cd C:\xampp\mysql\bin  # Windows
   # atau
   cd /Applications/XAMPP/xamppfiles/bin  # Mac
   ```
4. Run:
   ```bash
   mysqld --skip-grant-tables
   ```
5. Buka terminal lain, jalankan:
   ```bash
   mysql -u root
   FLUSH PRIVILEGES;
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'newpassword';
   ```
6. Update `config.php` dengan password baru

---

## 7. Best Practices XAMPP + MySQL

### ✅ Security

1. **Set MySQL Root Password** (Production)
   ```bash
   mysqladmin -u root password "newpassword"
   ```

2. **Create Separate DB User** (Recommended)
   ```sql
   CREATE USER 'mobilenest_user'@'localhost' IDENTIFIED BY 'password123';
   GRANT ALL PRIVILEGES ON mobilenest.* TO 'mobilenest_user'@'localhost';
   FLUSH PRIVILEGES;
   ```
   Kemudian update `config.php`:
   ```php
   $db_user = 'mobilenest_user';
   $db_pass = 'password123';
   ```

3. **Always Use Prepared Statements** (Di config.php sudah ada)
   ```php
   $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
   $stmt->bind_param('s', $email);
   $stmt->execute();
   ```

4. **Escape Input**
   ```php
   $safe_input = mysqli_real_escape_string($conn, $user_input);
   ```

### 📊 Database Maintenance

**Backup Database:**
```bash
# Via command line
mysqldump -u root -p mobilenest > backup_mobilenest.sql

# Via phpMyAdmin:
# 1. Select database "mobilenest"
# 2. Tab "Export"
# 3. Klik "Go"
```

**Restore Database:**
```bash
mysql -u root -p mobilenest < backup_mobilenest.sql
```

**Optimize Tables:**
```sql
OPTIMIZE TABLE users, produk;
```

---

## 8. Useful MySQL Commands

### Via phpMyAdmin SQL Tab atau MySQL CLI

```sql
-- Lihat semua database
SHOW DATABASES;

-- Lihat semua tabel di database
SHOW TABLES;

-- Lihat struktur tabel
DESCRIBE produk;
-- atau
SHOW COLUMNS FROM produk;

-- Count data
SELECT COUNT(*) FROM produk;
SELECT COUNT(*) FROM users;

-- List users
SELECT id_user, nama_lengkap, email FROM users;

-- List produk
SELECT id_produk, nama_produk, harga, stok FROM produk;

-- Update data
UPDATE produk SET stok = stok - 1 WHERE id_produk = 1;

-- Delete data
DELETE FROM produk WHERE id_produk = 1;

-- Delete all data dari table
DELETE FROM produk;
DELETE FROM users;

-- Truncate table (delete all + reset auto_increment)
TRUNCATE TABLE produk;
TRUNCATE TABLE users;

-- Drop table
DROP TABLE produk;

-- Drop database
DROP DATABASE mobilenest;
```

---

## Checklist Setup XAMPP + MySQL

- [ ] XAMPP terinstall
- [ ] Apache running (hijau di Control Panel)
- [ ] MySQL running (hijau di Control Panel)
- [ ] phpMyAdmin accessible di `http://localhost/phpmyadmin/`
- [ ] Database "mobilenest" dibuat
- [ ] Tabel "users" dibuat
- [ ] Tabel "produk" dibuat
- [ ] Sample data inserted
- [ ] `test-connection.php` dapat diakses & menunjukkan koneksi berhasil
- [ ] Project MobileNest dapat di-akses di `http://localhost/MobileNest/`

---

## 🎉 Semua Ready!

Setelah semua checklist selesai, Anda siap untuk:
- ✅ Testing authentication (register, login, logout)
- ✅ Testing CRUD produk (create, read, update, delete)
- ✅ Menjalankan aplikasi MobileNest di browser

Lanjut ke file: **IMPLEMENTATION_SUMMARY.md** untuk testing lebih detail! 🚀
