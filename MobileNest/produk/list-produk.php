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
                        <!-- Filter by Brand -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Merek</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="samsung">
                                <label class="form-check-label" for="samsung">Samsung</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="apple">
                                <label class="form-check-label" for="apple">Apple</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="xiaomi">
                                <label class="form-check-label" for="xiaomi">Xiaomi</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="oppo">
                                <label class="form-check-label" for="oppo">Oppo</label>
                            </div>
                        </div>
                        
                        <!-- Filter by Price -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-2">Harga</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price1">
                                <label class="form-check-label" for="price1">Rp 1 - 3 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price2">
                                <label class="form-check-label" for="price2">Rp 3 - 7 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price3">
                                <label class="form-check-label" for="price3">Rp 7 - 15 Juta</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="price" id="price4">
                                <label class="form-check-label" for="price4">Rp 15+ Juta</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product List -->
            <div class="col-lg-9">
                <!-- Sorting -->
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    <p class="mb-0 text-muted">Menampilkan produk</p>
                    <select class="form-select form-select-sm w-auto">
                        <option selected>Urutkan</option>
                        <option value="1">Harga Terendah</option>
                        <option value="2">Harga Tertinggi</option>
                        <option value="3">Terbaru</option>
                        <option value="4">Paling Populer</option>
                    </select>
                </div>

                <!-- Product Grid -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">
                    <?php
                    $sql = "SELECT * FROM produk WHERE status_produk = 'Tersedia' ORDER BY tanggal_ditambahkan DESC";
                    $result = mysqli_query($conn, $sql);
                    
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <div class="col">
                                <div class="card h-100 shadow-sm border-0 transition-card">
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center position-relative" style="height: 200px;">
                                        <i class="bi bi-phone text-muted" style="font-size: 3rem;"></i>
                                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">-15%</span>
                                    </div>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold"><?php echo htmlspecialchars($row['nama_produk']); ?></h5>
                                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($row['merek']); ?></p>
                                        
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
                                        
                                        <h6 class="text-primary fw-bold mb-3">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></h6>
                                        
                                        <div class="d-grid gap-2">
                                            <a href="detail-produk.php?id=<?php echo $row['id_produk']; ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-cart-plus"></i> Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p class="text-center text-muted">Tidak ada produk tersedia.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

<?php include '../includes/footer.php'; ?>
