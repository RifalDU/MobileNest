<?php
/**
 * ============================================
 * FILE: pesanan.php
 * PURPOSE: User View Order History
 * LOCATION: MobileNest/user/pesanan.php
 * ============================================
 */

// Session start FIRST
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security: Require user login (DARI auth-check.php)
require_once '../includes/auth-check.php';
require_user_login();

// Include config database
require_once '../config.php';

// Ambil user ID dari session
$user_id = $_SESSION['user'];

// Inisialisasi variabel
$transaksi = [];
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'semua';
$message = '';

// ========================================
// QUERY TRANSAKSI BERDASARKAN FILTER
// ========================================
if ($filter_status === 'semua') {
    $sql = "SELECT t.id_transaksi, t.tanggal_transaksi, t.total_harga, 
            t.status_pesanan, t.metode_pembayaran, t.no_resi,
            GROUP_CONCAT(p.nama_produk SEPARATOR ', ') as produk_list,
            COUNT(dt.id_detail) as jumlah_item
            FROM transaksi t
            LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
            LEFT JOIN produk p ON dt.id_produk = p.id_produk
            WHERE t.id_user = ?
            GROUP BY t.id_transaksi
            ORDER BY t.tanggal_transaksi DESC";
} else {
    $sql = "SELECT t.id_transaksi, t.tanggal_transaksi, t.total_harga, 
            t.status_pesanan, t.metode_pembayaran, t.no_resi,
            GROUP_CONCAT(p.nama_produk SEPARATOR ', ') as produk_list,
            COUNT(dt.id_detail) as jumlah_item
            FROM transaksi t
            LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
            LEFT JOIN produk p ON dt.id_produk = p.id_produk
            WHERE t.id_user = ? AND t.status_pesanan = ?
            GROUP BY t.id_transaksi
            ORDER BY t.tanggal_transaksi DESC";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameter
if ($filter_status === 'semua') {
    $stmt->bind_param('i', $user_id);
} else {
    $stmt->bind_param('is', $user_id, $filter_status);
}

// Execute query
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

// Ambil semua data
while ($row = $result->fetch_assoc()) {
    $transaksi[] = $row;
}

$stmt->close();

// ========================================
// PROCESS BATAL PESANAN (HANYA PENDING)
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'batal') {
    $id_transaksi = intval($_POST['id_transaksi']);
    
    // Cek apakah pesanan milik user ini
    $check_sql = "SELECT id_transaksi, status_pesanan FROM transaksi 
                  WHERE id_transaksi = ? AND id_user = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param('ii', $id_transaksi, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $pesanan = $check_result->fetch_assoc();
        
        // Hanya bisa batal jika status Pending
        if ($pesanan['status_pesanan'] === 'Pending') {
            // Update status menjadi Dibatalkan
            $update_sql = "UPDATE transaksi SET status_pesanan = 'Dibatalkan' 
                          WHERE id_transaksi = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param('i', $id_transaksi);
            
            if ($update_stmt->execute()) {
                $_SESSION['success'] = 'Pesanan berhasil dibatalkan!';
                header('Location: pesanan.php');
                exit;
            } else {
                $message = 'Error: ' . $update_stmt->error;
            }
            $update_stmt->close();
        } else {
            $message = 'Pesanan tidak bisa dibatalkan karena sudah diproses!';
        }
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - MobileNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-badge {
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pending { background-color: #ffc107; color: #000; }
        .status-diproses { background-color: #17a2b8; color: #fff; }
        .status-dikirim { background-color: #007bff; color: #fff; }
        .status-selesai { background-color: #28a745; color: #fff; }
        .status-dibatalkan { background-color: #dc3545; color: #fff; }
        
        .card-pesanan {
            border-left: 4px solid #007bff;
            transition: box-shadow 0.3s;
        }
        
        .card-pesanan:hover {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../header.php'; ?>

    <div class="container py-5">
        <h1 class="mb-4">📦 Pesanan Saya</h1>

        <!-- Notifikasi -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                ✅ <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                ❌ <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filter Status -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <a href="pesanan.php?status=semua" class="btn btn-outline-primary <?php echo $filter_status === 'semua' ? 'active' : ''; ?>">
                        Semua
                    </a>
                    <a href="pesanan.php?status=Pending" class="btn btn-outline-warning <?php echo $filter_status === 'Pending' ? 'active' : ''; ?>">
                        ⏳ Pending
                    </a>
                    <a href="pesanan.php?status=Diproses" class="btn btn-outline-info <?php echo $filter_status === 'Diproses' ? 'active' : ''; ?>">
                        ⚙️ Diproses
                    </a>
                    <a href="pesanan.php?status=Dikirim" class="btn btn-outline-primary <?php echo $filter_status === 'Dikirim' ? 'active' : ''; ?>">
                        🚚 Dikirim
                    </a>
                    <a href="pesanan.php?status=Selesai" class="btn btn-outline-success <?php echo $filter_status === 'Selesai' ? 'active' : ''; ?>">
                        ✅ Selesai
                    </a>
                </div>
            </div>
        </div>

        <!-- List Pesanan -->
        <div class="row">
            <?php if (!empty($transaksi)): ?>
                <?php foreach ($transaksi as $item): ?>
                    <div class="col-md-12 mb-3">
                        <div class="card card-pesanan">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="card-title">ID Pesanan: #<?php echo $item['id_transaksi']; ?></h5>
                                        <p class="text-muted mb-1">
                                            📅 <?php echo date('d M Y H:i', strtotime($item['tanggal_transaksi'])); ?>
                                        </p>
                                        <p class="text-muted mb-0">
                                            📦 <?php echo $item['jumlah_item']; ?> item | 
                                            💳 <?php echo ucfirst($item['metode_pembayaran']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex justify-content-center">
                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '', $item['status_pesanan'])); ?>">
                                                <?php echo htmlspecialchars($item['status_pesanan']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-end">
                                        <h6 class="mb-2">Rp <?php echo number_format($item['total_harga'], 0, ',', '.'); ?></h6>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-info" data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal" onclick="showDetail(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                                👁️ Detail
                                            </button>
                                            <?php if ($item['status_pesanan'] === 'Pending'): ?>
                                                <form method="POST" style="display:inline;" 
                                                      onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                                                    <input type="hidden" name="action" value="batal">
                                                    <input type="hidden" name="id_transaksi" value="<?php echo $item['id_transaksi']; ?>">
                                                    <button type="submit" class="btn btn-danger">❌ Batal</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        🛒 Anda belum memiliki pesanan. <a href="../cari-produk.php">Mulai belanja sekarang!</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Detail Pesanan -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📋 Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detailContent"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showDetail(pesanan) {
            const statusClass = 'status-' + pesanan.status_pesanan.toLowerCase().replace(' ', '');
            const html = `
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>ID Pesanan:</strong><br> #${pesanan.id_transaksi}
                    </div>
                    <div class="col-6">
                        <strong>Tanggal:</strong><br> ${new Date(pesanan.tanggal_transaksi).toLocaleDateString('id-ID')}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <strong>Status:</strong><br> <span class="status-badge ${statusClass}">${pesanan.status_pesanan}</span>
                    </div>
                    <div class="col-6">
                        <strong>Metode Pembayaran:</strong><br> ${pesanan.metode_pembayaran}
                    </div>
                </div>
                ${pesanan.no_resi ? `
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>No. Resi:</strong><br> ${pesanan.no_resi}
                    </div>
                </div>
                ` : ''}
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Produk:</strong><br> ${pesanan.produk_list || 'N/A'}
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>Total:</strong><br> <h5>Rp ${pesanan.total_harga.toLocaleString('id-ID')}</h5>
                    </div>
                </div>
            `;
            document.getElementById('detailContent').innerHTML = html;
        }
    </script>
</body>
</html>
