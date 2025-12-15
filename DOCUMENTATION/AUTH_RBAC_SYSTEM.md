# 🔐 SISTEM KEAMANAN: ROLE-BASED ACCESS CONTROL (RBAC)

---

## 📋 RINGKASAN SOLUSI

Dokumentasi lengkap sistem keamanan authentication dan authorization untuk MobileNest.

### Komponen Utama:
1. **auth-check.php** - Helper functions untuk cek role/login
2. **User protection** - Fungsi `require_user_login()`
3. **Admin protection** - Fungsi `require_admin_login()`

---

## 📁 FILE: auth-check.php

**Lokasi:** `MobileNest/includes/auth-check.php`

### 11 Fungsi Helper:

1. `is_user_logged_in()` - Cek user sudah login
2. `is_admin_logged_in()` - Cek admin sudah login
3. `is_user_only()` - Cek role user biasa
4. `is_admin_only()` - Cek role admin
5. `require_user_login()` - PROTEKSI halaman user
6. `require_admin_login()` - PROTEKSI halaman admin
7. `get_current_user_id()` - Ambil user ID
8. `get_current_admin_id()` - Ambil admin ID
9. `get_current_role()` - Ambil role saat ini
10. `forbidden_access()` - Handle akses terlarang
11. `user_owns_data()` - Cek kepemilikan data

---

## 🚀 IMPLEMENTASI

### Tambahkan ke Halaman User:

```php
<?php
require_once '../includes/auth-check.php';
require_user_login(); // 🔐 PROTEKSI

// ... rest of code
?>
```

### Tambahkan ke Halaman Admin:

```php
<?php
require_once '../includes/auth-check.php';
require_admin_login(); // 🔐 PROTEKSI

// ... rest of code
?>
```

---

## ✅ KEAMANAN YANG DITERAPKAN

✅ **Server-Side Protection** - Tidak bisa di-bypass dengan ubah URL
✅ **Session Separation** - User dan Admin session terpisah
✅ **Automatic Redirect** - Auto redirect ke halaman sesuai role
✅ **Tamper-Proof** - User tidak bisa manipulasi session
✅ **Error Messages** - User tahu kenapa akses ditolak

---

## 🧪 TESTING CHECKLIST

- [ ] User login → akses user pages ✅
- [ ] User coba admin pages ❌ REDIRECT
- [ ] Admin login → akses admin pages ✅
- [ ] Admin coba user pages ❌ REDIRECT
- [ ] Belum login → akses halaman ❌ REDIRECT ke login
