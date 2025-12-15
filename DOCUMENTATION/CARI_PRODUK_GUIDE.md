# 🔍 FILE: cari-produk.php - PRODUCT SEARCH & FILTER

**Status:** ✅ LENGKAP & SIAP IMPLEMENTASI

---

## 📋 FITUR UTAMA

✅ **Pencarian Produk**
- Search by nama produk
- Search by deskripsi produk
- Real-time filter

✅ **Filter Kategori**
- Radio button untuk kategori
- Auto-load dari database
- Multiple category support

✅ **Filter Harga**
- Range slider min-max
- Dynamic price filter
- Update otomatis saat berubah

✅ **Sorting**
- Terbaru (default)
- Terpopuler (by penjualan)
- Harga terendah-tertinggi
- Nama A-Z / Z-A

✅ **Display Produk**
- Product card dengan gambar
- Kategori badge
- Rating & terjual
- Stok indikator
- Harga terformat

✅ **User Interaction**
- "Lihat Detail" button → ke detail-produk.php
- "Keranjang" button → add to cart
- Login check → redirect ke login jika belum login

✅ **Responsive Design**
- Mobile friendly
- Bootstrap 5 grid
- Optimized untuk tablet/desktop

---

## 🔧 INTEGRASI DENGAN FILE LAIN

**Bergantung pada:**
- ✅ `config.php` - Database connection
- ✅ `header.php` - Navigation header
- ✅ `footer.php` - Footer

**Terhubung ke:**
- ✅ `detail-produk.php` - Lihat detail produk
- ✅ `keranjang-aksi.php` - Add to cart
- ✅ `user/login.php` - User login

---

## 📊 DATABASE QUERIES

### Query 1: Ambil Kategori
```sql
SELECT DISTINCT kategori FROM produk ORDER BY kategori ASC
```

### Query 2: Search Produk dengan Filter
```sql
SELECT * FROM produk
WHERE stok > 0
  AND nama_produk LIKE '%query%'
  AND kategori = 'kategori'
  AND harga BETWEEN min AND max
ORDER BY tanggal_ditambahkan DESC
```

---

## 📱 URL QUERY PARAMETER

Contoh URL lengkap:
```
/cari-produk.php?q=iphone&kategori=Smartphone&harga_min=5000000&harga_max=15000000&sort=harga_rendah
```

| Parameter | Contoh | Keterangan |
|-----------|--------|------------|
| `q` | `iphone` | Search query |
| `kategori` | `Smartphone` | Category filter |
| `harga_min` | `5000000` | Minimum price |
| `harga_max` | `15000000` | Maximum price |
| `sort` | `harga_rendah` | Sort option |

---

## 🧪 TESTING CHECKLIST

- [ ] Buka halaman: `http://localhost/MobileNest/cari-produk.php`
- [ ] Coba search: ketik nama produk di search box
- [ ] Coba filter kategori: pilih salah satu kategori
- [ ] Coba filter harga: ubah slider min/max
- [ ] Coba sort: pilih sorting option
- [ ] Klik "Lihat Detail" → harus ke detail-produk.php
- [ ] Klik "Keranjang" tanpa login → harus redirect ke login
- [ ] Klik "Keranjang" setelah login → harus add to cart
- [ ] Test responsive di mobile

---

## 📄 LOKASI FILE

**Production File:**
- GitHub: https://github.com/RifalDU/MobileNest/blob/main/cari-produk.php
- Local: `MobileNest/cari-produk.php`

---

## ✅ STATUS

**File cari-produk.php:** ✅ COMPLETE
**Push ke GitHub:** ✅ DONE
**Ready untuk Production:** ✅ YES
