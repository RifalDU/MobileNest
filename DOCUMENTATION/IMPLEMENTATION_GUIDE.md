# 🔐 PANDUAN IMPLEMENTASI: ROLE-BASED ACCESS CONTROL

---

## 🌟 PERMASALAHAN

❌ User bisa akses halaman admin dengan ubah URL
❌ Admin bisa akses halaman user dengan ubah URL
❌ Tidak ada proteksi di level server side

---

## 🔍 SOLUSI: RBAC SYSTEM

✅ User HANYA bisa akses /user/* pages
✅ Admin HANYA bisa akses /admin/* pages
✅ Server side protection (tidak bisa di-bypass)

---

## 📅 CHECKLIST IMPLEMENTASI

### STEP 1: BUAT FILE BARU
- [ ] `MobileNest/includes/auth-check.php` (sudah di GitHub)

### STEP 2: UPDATE FILE USER (Tambah 2 baris di awal)
- [ ] `MobileNest/user/pesanan.php`
- [ ] `MobileNest/user/profil.php`

Tambahkan:
```php
<?php
require_once '../includes/auth-check.php';
require_user_login();

Session_start();
// ... rest of code
?>
```

Hapus:
```php
// ❌ HAPUS:
// Cek user sudah login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
```

### STEP 3: UPDATE FILE ADMIN (Tambah 2 baris di awal)
- [ ] `MobileNest/admin/dashboard.php`
- [ ] `MobileNest/admin/kelola-pesanan.php`

Tambahkan:
```php
<?php
require_once '../includes/auth-check.php';
require_admin_login();

session_start();
// ... rest of code
?>
```

Hapus:
```php
// ❌ HAPUS:
// Cek admin sudah login
if (!isset($_SESSION['admin'])) {
    header('Location: ../user/login.php');
    exit;
}
```

### STEP 4: VERIFIKASI LOGIN FILES

Pastikan `login.php` set session dengan benar:
```php
// ✅ USER LOGIN
$_SESSION['user'] = $user['id_user'];
unset($_SESSION['admin']);

// ✅ ADMIN LOGIN
$_SESSION['admin'] = $admin['id_admin'];
unset($_SESSION['user']);
```

---

## 🧪 TESTING

### Test 1: User Login & User Pages
```
1. Login sebagai USER
2. Akses /user/pesanan.php
3. ✅ Harusnya BERHASIL
```

### Test 2: User Coba Admin Pages
```
1. Login sebagai USER
2. Akses /admin/dashboard.php
3. ❌ Harusnya REDIRECT ke /user/dashboard.php
```

### Test 3: Admin Login & Admin Pages
```
1. Logout
2. Login sebagai ADMIN
3. Akses /admin/dashboard.php
4. ✅ Harusnya BERHASIL
```

### Test 4: Admin Coba User Pages
```
1. Login sebagai ADMIN
2. Akses /user/pesanan.php
3. ❌ Harusnya REDIRECT ke /admin/dashboard.php
```

### Test 5: Belum Login
```
1. Logout
2. Akses /user/pesanan.php
3. ❌ Harusnya REDIRECT ke /user/login.php
```

---

## 🏆 AKSES MATRIX

```
┌────────────────────┬─────────────┬─────────────┐
│ Halaman            │ User Biasa  │ Admin       │
├────────────────────┼─────────────┼─────────────┤
│ /user/pesanan     │ ✅ AKSES    │ ❌ REDIRECT │
│ /user/profil      │ ✅ AKSES    │ ❌ REDIRECT │
│ /admin/dashboard  │ ❌ REDIRECT │ ✅ AKSES    │
│ /admin/kelola-*   │ ❌ REDIRECT │ ✅ AKSES    │
└────────────────────┴─────────────┴─────────────┘
```

---

## 🚀 HASIL AKHIR

**SISTEM 100% AMAN!** 🔐

- User tidak bisa akses admin pages
- Admin tidak bisa akses user pages
- Server-side protection
- Tidak bisa di-bypass
