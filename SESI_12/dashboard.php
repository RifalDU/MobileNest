<?php
/**
 * ============================================
 * FILE: dashboard.php
 * PURPOSE: Admin Dashboard
 * LOCATION: MobileNest/admin/dashboard.php
 * SESI: 12 - INTEGRASI FRONTEND-BACKEND
 * ============================================
 */

// Security: Require admin login
require_once '../includes/auth-check.php';
require_admin_login();

// Mulai session
session_start();

// Include config database
require_once '../config.php';

// ========================================
// 1️⃣ TOTAL ORDERS
// ========================================
$sql_total_orders = "SELECT COUNT(*) as total FROM transaksi";
$result = $conn->query($sql_total_orders);
$total_orders = $result->fetch_assoc()['total'];

// ========================================
// 2️⃣ TOTAL PENJUALAN (BULAN INI)
// ========================================
$sql_sales = "SELECT SUM(total_harga) as total_sales FROM transaksi 
              WHERE MONTH(tanggal_transaksi) = MONTH(CURDATE())
              AND YEAR(tanggal_transaksi) = YEAR(CURDATE())";
$result = $conn->query($sql_sales);
$total_sales = $result->fetch_assoc()['total_sales'] ?? 0;

// ========================================
// 3️⃣ TOTAL USERS
// ========================================
$sql_users = "SELECT COUNT(*) as total FROM users";
$result = $conn->query($sql_users);
$total_users = $result->fetch_assoc()['total'];

// ========================================
// 4️⃣ TOTAL PRODUCTS
// ========================================
$sql_products = "SELECT COUNT(*) as total FROM produk";
$result = $conn->query($sql_products);
$total_products = $result->fetch_assoc()['total'];

// ========================================
// 5️⃣ RECENT ORDERS (5 TERBARU)
// ========================================
$sql_recent = "SELECT t.id_transaksi, t.tanggal_transaksi, t.total_harga,
               t.status_pesanan, u.nama_lengkap
               FROM transaksi t
               JOIN users u ON t.id_user = u.id_user
               ORDER BY t.tanggal_transaksi DESC
               LIMIT 5";
$recent_orders = [];
$result = $conn->query($sql_recent);
while ($row = $result->fetch_assoc()) {
    $recent_orders[] = $row;
}

// ========================================
// 6️⃣ STATUS BREAKDOWN
// ========================================
$sql_status = "SELECT status_pesanan, COUNT(*) as count 
               FROM transaksi
               GROUP BY status_pesanan";
$status_breakdown = [];
$result = $conn->query($sql_status);
while ($row = $result->fetch_assoc()) {
    $status_breakdown[] = $row;
}

// ========================================
// 7️⃣ STOK RENDAH (<=5)
// ========================================
$sql_stok = "SELECT id_produk, nama_produk, stok, kategori
             FROM produk
             WHERE stok <= 5
             ORDER BY stok ASC";
$low_stock = [];
$result = $conn->query($sql_stok);
while ($row = $result->fetch_assoc()) {
    $low_stock[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - MobileNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card.danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .alert-stok {
            border-left: 4px solid #ff6b6b;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../header.php'; ?>

    <div class="container-fluid py-5">
        <h1 class="mb-5">📊 Dashboard Admin</h1>

        <!-- Statistics Cards -->
        <div class="row mb-5">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_orders; ?></div>
                    <div class="stat-label">📦 Total Pesanan</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card success">
                    <div class="stat-number">Rp <?php echo number_format($total_sales / 1000000, 1); ?>M</div>
                    <div class="stat-label">💰 Penjualan (Bulan Ini)</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card warning">
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">👥 Total User</div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card danger">
                    <div class="stat-number"><?php echo $total_products; ?></div>
                    <div class="stat-label">📱 Total Produk</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">📋 Pesanan Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id_transaksi']; ?></td>
                                            <td><?php echo htmlspecialchars(substr($order['nama_lengkap'], 0, 15)); ?></td>
                                            <td>Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></td>
                                            <td>
                                                <span class="status-badge" style="
                                                    background: <?php 
                                                        if ($order['status_pesanan'] === 'Pending') echo '#ffc107';
                                                        elseif ($order['status_pesanan'] === 'Diproses') echo '#17a2b8';
                                                        elseif ($order['status_pesanan'] === 'Dikirim') echo '#007bff';
                                                        elseif ($order['status_pesanan'] === 'Selesai') echo '#28a745';
                                                        else echo '#dc3545';
                                                    ?>;
                                                    color: white;
                                                ">
                                                    <?php echo $order['status_pesanan']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M', strtotime($order['tanggal_transaksi'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="kelola-pesanan.php" class="btn btn-primary btn-sm w-100 mt-3">
                            Lihat Semua Pesanan →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Status Breakdown -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">📊 Status Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($status_breakdown as $status): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo $status['status_pesanan']; ?></span>
                                    <strong><?php echo $status['count']; ?> pesanan</strong>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar" style="
                                        width: <?php echo ($status['count'] / $total_orders * 100); ?>%;
                                        background: <?php 
                                            if ($status['status_pesanan'] === 'Pending') echo '#ffc107';
                                            elseif ($status['status_pesanan'] === 'Diproses') echo '#17a2b8';
                                            elseif ($status['status_pesanan'] === 'Dikirim') echo '#007bff';
                                            elseif ($status['status_pesanan'] === 'Selesai') echo '#28a745';
                                            else echo '#dc3545';
                                        ?>;
                                    ">
                                        <?php echo round(($status['count'] / $total_orders * 100), 1); ?>%
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <?php if (!empty($low_stock)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-stok alert-warning">
                        <h4 class="alert-heading">⚠️ Alert Stok Rendah</h4>
                        <p>Produk berikut memiliki stok kurang dari 5 unit:</p>
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($low_stock as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['nama_produk']); ?></td>
                                        <td><?php echo htmlspecialchars($item['kategori']); ?></td>
                                        <td>
                                            <span class="badge bg-danger"><?php echo $item['stok']; ?></span>
                                        </td>
                                        <td>
                                            <a href="kelola-produk.php?edit=<?php echo $item['id_produk']; ?>" class="btn btn-sm btn-primary">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
