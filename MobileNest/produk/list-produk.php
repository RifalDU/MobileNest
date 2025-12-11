<?php
require_once '../config.php';

$page_title = "Produk";
$css_path = "../assets/css/style.css";
$js_path = "../assets/js/script.js";
$logo_path = "../assets/images/logo.jpg";
$home_url = "../index.php";
$produk_url = "list-produk.php";
$login_url = "../user/login.php";
$register_url = "../user/register.php";
$keranjang_url = "../transaksi/keranjang.php";

include '../includes/header.php';
?>

    <!-- PAGE TITLE -->
    <div class="bg-light py-4 mb-5">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Daftar Produk</h1>
            <p class="text-muted">Temukan smartphone pilihan terbaik di MobileNest</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <!-- Sidebar Filter -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="bi bi-funnel"></i> Filter
                    </div>
                    <div class="card-body">
                        <!-- Search -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Cari Produk</h6>
                            <input type="text" id="searchInput" class="form-control" placeholder="Ketik nama produk...">
                        </div>

                        <!-- Filter by Brand -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Merek</h6>
                            <div class="form-check">
                                <input class="form-check-input brand-filter" type="checkbox" value="Samsung" id="samsung">
                                <label class="form-check-label" for="samsung">Samsung</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input brand-filter" type="checkbox" value="Apple" id="apple">
                                <label class="form-check-label" for="apple">Apple</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input brand-filter" type="checkbox" value="Xiaomi" id="xiaomi">
                                <label class="form-check-label" for="xiaomi">Xiaomi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input brand-filter" type="checkbox" value="Oppo" id="oppo">
                                <label class="form-check-label" for="oppo">Oppo</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input brand-filter" type="checkbox" value="Realme" id="realme">
                                <label class="form-check-label" for="realme">Realme</label>
                            </div>
                        </div>
                        
                        <!-- Filter by Price -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Harga</h6>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price" value="0-3000000" id="price1">
                                <label class="form-check-label" for="price1">Rp 1 - 3 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price" value="3000000-7000000" id="price2">
                                <label class="form-check-label" for="price2">Rp 3 - 7 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price" value="7000000-15000000" id="price3">
                                <label class="form-check-label" for="price3">Rp 7 - 15 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input price-filter" type="radio" name="price" value="15000000" id="price4">
                                <label class="form-check-label" for="price4">Rp 15+ Juta</label>
                            </div>
                        </div>

                        <!-- Reset Filter Button -->
                        <button class="btn btn-secondary btn-sm w-100" id="resetFilter">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Product List -->
            <div class="col-lg-9">
                <!-- Sorting & Info -->
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <p class="mb-0 text-muted">Menampilkan <strong id="productCount">0</strong> produk</p>
                    <select class="form-select form-select-sm w-auto" id="sortSelect">
                        <option value="terbaru" selected>Terbaru</option>
                        <option value="termurah">Harga Terendah</option>
                        <option value="termahal">Harga Tertinggi</option>
                        <option value="populer">Paling Populer</option>
                    </select>
                </div>

                <!-- Product Grid -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5" id="productGrid">
                    <!-- Produk akan dimuat via JavaScript -->
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Loading produk...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>

<script>
// Store semua produk dari database
let allProducts = [];

// Load produk dari server saat halaman dibuka
document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    
    // Event listener untuk filter
    document.querySelectorAll('.brand-filter').forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters);
    });
    
    document.querySelectorAll('.price-filter').forEach(radio => {
        radio.addEventListener('change', applyFilters);
    });
    
    document.getElementById('searchInput').addEventListener('keyup', applyFilters);
    document.getElementById('sortSelect').addEventListener('change', applyFilters);
    document.getElementById('resetFilter').addEventListener('click', resetFilters);
});

// Load semua produk dari server
function loadProducts() {
    fetch('get-produk.php')
        .then(response => response.json())
        .then(data => {
            allProducts = data;
            applyFilters();
        })
        .catch(error => {
            console.error('Error loading products:', error);
            document.getElementById('productGrid').innerHTML = '<div class="col-12"><p class="text-danger">Gagal memuat produk</p></div>';
        });
}

// Apply filter ke produk
function applyFilters() {
    // Get filter values
    const selectedBrands = Array.from(document.querySelectorAll('.brand-filter:checked')).map(cb => cb.value);
    const selectedPrice = document.querySelector('.price-filter:checked')?.value || '';
    const searchText = document.getElementById('searchInput').value.toLowerCase();
    const sortBy = document.getElementById('sortSelect').value;
    
    // Filter produk
    let filtered = allProducts.filter(product => {
        // Filter by search
        if (searchText && !product.nama_produk.toLowerCase().includes(searchText) && 
            !product.merek.toLowerCase().includes(searchText)) {
            return false;
        }
        
        // Filter by brand
        if (selectedBrands.length > 0 && !selectedBrands.includes(product.merek)) {
            return false;
        }
        
        // Filter by price
        if (selectedPrice) {
            const [minPrice, maxPrice] = selectedPrice.split('-').map(p => parseInt(p) || 0);
            if (maxPrice === 0) { // "15000000" means 15+ juta
                if (product.harga < minPrice) return false;
            } else {
                if (product.harga < minPrice || product.harga > maxPrice) return false;
            }
        }
        
        return true;
    });
    
    // Sort produk
    if (sortBy === 'termurah') {
        filtered.sort((a, b) => a.harga - b.harga);
    } else if (sortBy === 'termahal') {
        filtered.sort((a, b) => b.harga - a.harga);
    } else if (sortBy === 'terbaru') {
        filtered.sort((a, b) => new Date(b.tanggal_ditambahkan) - new Date(a.tanggal_ditambahkan));
    }
    
    // Display produk
    displayProducts(filtered);
}

// Display produk di halaman
function displayProducts(products) {
    const productGrid = document.getElementById('productGrid');
    const productCount = document.getElementById('productCount');
    
    if (products.length === 0) {
        productGrid.innerHTML = '<div class="col-12"><p class="text-center text-muted py-5">Tidak ada produk yang sesuai dengan filter.</p></div>';
        productCount.textContent = '0';
        return;
    }
    
    productCount.textContent = products.length;
    
    productGrid.innerHTML = products.map(product => `
        <div class="col">
            <div class="card h-100 shadow-sm border-0 transition-card">
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center position-relative" style="height: 200px;">
                    <i class="bi bi-phone text-muted" style="font-size: 3rem;"></i>
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">-15%</span>
                </div>
                
                <div class="card-body">
                    <h5 class="card-title fw-bold">${escapeHtml(product.nama_produk)}</h5>
                    <p class="text-muted small mb-2">${escapeHtml(product.merek)}</p>
                    
                    <div class="mb-2">
                        <span class="text-warning">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                        </span>
                        <span class="text-muted small">(152)</span>
                    </div>
                    
                    <h6 class="text-primary fw-bold mb-3">Rp ${formatCurrency(product.harga)}</h6>
                    
                    <div class="d-grid gap-2">
                        <a href="detail-produk.php?id=${product.id_produk}" class="btn btn-primary btn-sm">
                            <i class="bi bi-cart-plus"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Reset filter
function resetFilters() {
    document.querySelectorAll('.brand-filter').forEach(cb => cb.checked = false);
    document.querySelectorAll('.price-filter').forEach(rb => rb.checked = false);
    document.getElementById('searchInput').value = '';
    document.getElementById('sortSelect').value = 'terbaru';
    applyFilters();
}

// Format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value).replace('Rp', '').trim();
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
