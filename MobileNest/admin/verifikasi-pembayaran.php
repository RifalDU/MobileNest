<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/../config.php';

// autentikasi
if (!isset($_SESSION['admin'])) {
    header('Location: ../user/login.php');
    exit;
}

// handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id_transaksi = (int)($_POST['id_transaksi'] ?? 0);

    if ($action === 'approve' && $id_transaksi > 0) {
        $stmt = $conn->prepare("UPDATE transaksi SET status_pesanan = 'Verified', tanggal_diperabarui = NOW() WHERE id_transaksi = ? AND status_pesanan = 'Menunggu Verifikasi'");
        if ($stmt) {
            $stmt->bind_param('i', $id_transaksi);
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION['msg'] = 'Pembayaran berhasil disetujui!';
        $_SESSION['msg_type'] = 'success';
        header('Location: verifikasi-pembayaran.php');
        exit;
    }

    if ($action === 'reject' && $id_transaksi > 0) {
        $stmt = $conn->prepare("UPDATE transaksi SET status_pesanan = 'Dibatalkan', tanggal_diperabarui = NOW() WHERE id_transaksi = ? AND status_pesanan = 'Menunggu Verifikasi'");
        if ($stmt) {
            $stmt->bind_param('i', $id_transaksi);
            $stmt->execute();
            $stmt->close();
        }
        $_SESSION['msg'] = 'Pembayaran ditolak!';
        $_SESSION['msg_type'] = 'warning';
        header('Location: verifikasi-pembayaran.php');
        exit;
    }
}

// get pending payments - gunakan status_pesanan = 'Menunggu Verifikasi'
$result = $conn->query("SELECT id_transaksi, kode_transaksi, id_user, total_harga, tanggal_transaksi, bukti_pembayaran FROM transaksi WHERE status_pesanan = 'Menunggu Verifikasi' ORDER BY tanggal_transaksi DESC");

if (!$result) {
    die('Query error: ' . $conn->error);
}

$pending_count = $result->num_rows;

// get selected payment details
$selected_payment = null;
if (!empty($_GET['id'])) {
    $selected_id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM transaksi WHERE id_transaksi = ? AND status_pesanan = 'Menunggu Verifikasi'");
    if ($stmt) {
        $stmt->bind_param('i', $selected_id);
        $stmt->execute();
        $selected_payment = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($selected_payment) {
            // get user info
            $user_stmt = $conn->prepare("SELECT id_user, nama_lengkap, email, nomor_hp FROM users WHERE id_user = ?");
            if ($user_stmt) {
                $user_stmt->bind_param('i', $selected_payment['id_user']);
                $user_stmt->execute();
                $selected_payment['user'] = $user_stmt->get_result()->fetch_assoc();
                $user_stmt->close();
            }

            // get pengiriman info jika id_pengiriman ada
            if ($selected_payment['id_pengiriman']) {
                $ship_stmt = $conn->prepare("SELECT * FROM pengiriman WHERE id_pengiriman = ?");
                if ($ship_stmt) {
                    $ship_stmt->bind_param('i', $selected_payment['id_pengiriman']);
                    $ship_stmt->execute();
                    $selected_payment['pengiriman'] = $ship_stmt->get_result()->fetch_assoc();
                    $ship_stmt->close();
                }
            }

            // get transaksi items - gunakan transaksi_detail (nama tabel sesuai schema)
            $items_stmt = $conn->prepare("SELECT id_produk, nama_produk, harga, jumlah FROM transaksi_detail WHERE id_transaksi = ?");
            if ($items_stmt) {
                $items_stmt->bind_param('i', $selected_payment['id_transaksi']);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                $selected_payment['items'] = [];
                while ($item = $items_result->fetch_assoc()) {
                    $selected_payment['items'][] = $item;
                }
                $items_stmt->close();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran - MobileNest Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background: #f5f7fa; }
        .navbar { background: linear-gradient(135deg, #667eea, #764ba2) !important; }
        .navbar-brand { font-weight: 700; font-size: 18px; }
        .nav-link { color: rgba(255,255,255,0.8) !important; transition: all 0.3s; }
        .nav-link:hover { color: white !important; }
        .container-main { display: grid; grid-template-columns: 350px 1fr; gap: 20px; margin-top: 20px; }
        @media (max-width: 768px) { .container-main { grid-template-columns: 1fr; } }
        .list-group-item { border: none; border-bottom: 1px solid #e0e0e0; cursor: pointer; transition: all 0.2s; }
        .list-group-item:hover { background: #f0f7ff; }
        .list-group-item.active { background: #667eea; color: white; border-color: #667eea; }
        .detail-card { background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .detail-header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px; border-radius: 10px 10px 0 0; }
        .payment-proof { max-width: 100%; max-height: 400px; object-fit: contain; border-radius: 8px; margin: 15px 0; }
        .info-row { display: grid; grid-template-columns: 150px 1fr; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-weight: 600; color: #667eea; }
        .items-table { font-size: 14px; }
        .items-table th { background: #f0f7ff; font-weight: 600; color: #2c3e50; border: none; }
        .action-btns { display: flex; gap: 10px; margin-top: 20px; }
        .action-btns button { flex: 1; }
        .empty-state { text-align: center; padding: 40px 20px; color: #999; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php"><i class="bi bi-credit-card"></i> MobileNest Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="kelola-produk.php"><i class="bi bi-phone"></i> Produk</a></li>
                    <li class="nav-item"><a class="nav-link active" href="verifikasi-pembayaran.php"><i class="bi bi-credit-card"></i> Verifikasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="kelola-transaksi.php"><i class="bi bi-receipt"></i> Transaksi</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="../user/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid">
        <div class="row mb-4" style="margin-top: 20px;">
            <div class="col">
                <h2 style="font-weight: 700; color: #2c3e50;">
                    <i class="bi bi-credit-card"></i> Verifikasi Pembayaran
                    <span class="badge bg-warning ms-2"><?= $pending_count ?> pending</span>
                </h2>
            </div>
        </div>

        <?php if (!empty($_SESSION['msg'])): ?>
            <div class="alert alert-<?= $_SESSION['msg_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
        <?php endif; ?>

        <div class="container-main" style="margin-bottom: 40px;">
            <!-- LEFT: List Pembayaran Pending -->
            <div>
                <h5 style="font-weight: 600; margin-bottom: 15px;"><i class="bi bi-list"></i> Pembayaran Pending</h5>
                <div class="list-group" style="border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <?php if ($pending_count === 0): ?>
                        <div class="empty-state" style="background: white;">
                            <i class="bi bi-check-circle" style="font-size: 48px; color: #10b981;"></i>
                            <p style="margin-top: 15px;">Tidak ada pembayaran pending!</p>
                        </div>
                    <?php else: ?>
                        <?php $result->data_seek(0); ?>
                        <?php while ($payment = $result->fetch_assoc()): ?>
                            <a href="?id=<?= urlencode($payment['id_transaksi']) ?>" class="list-group-item list-group-item-action <?= (!empty($_GET['id']) && $_GET['id'] == $payment['id_transaksi']) ? 'active' : '' ?>">
                                <div style="font-weight: 600;">#<?= htmlspecialchars($payment['kode_transaksi']) ?></div>
                                <small class="text-muted" style="display: block; margin-top: 5px;"><?= htmlspecialchars(date('d M Y H:i', strtotime($payment['tanggal_transaksi']))) ?></small>
                                <small style="display: block; margin-top: 3px;">Rp <?= number_format((float)$payment['total_harga'], 0, ',', '.') ?></small>
                            </a>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- RIGHT: Detail Pembayaran -->
            <div>
                <?php if ($selected_payment): ?>
                    <div class="detail-card">
                        <div class="detail-header">
                            <h5 style="margin: 0; font-weight: 700;">
                                <i class="bi bi-receipt-cutoff"></i> Detail Pembayaran
                            </h5>
                            <small style="opacity: 0.9;">ID: #<?= htmlspecialchars($selected_payment['id_transaksi']) ?></small>
                        </div>

                        <div style="padding: 20px;">
                            <!-- Bukti Pembayaran -->
                            <div style="margin-bottom: 25px;">
                                <h6 style="font-weight: 600; margin-bottom: 10px;"><i class="bi bi-image"></i> Bukti Pembayaran</h6>
                                <?php if (!empty($selected_payment['bukti_pembayaran'])): ?>
                                    <img src="../api/uploads/pembayaran/<?= urlencode($selected_payment['bukti_pembayaran']) ?>" alt="Bukti Pembayaran" class="payment-proof">
                                <?php else: ?>
                                    <p class="text-muted"><i class="bi bi-exclamation-circle"></i> Bukti pembayaran tidak ditemukan</p>
                                <?php endif; ?>
                            </div>

                            <hr>

                            <!-- Info Customer -->
                            <h6 style="font-weight: 600; margin-bottom: 15px;"><i class="bi bi-person"></i> Informasi Customer</h6>
                            <?php if (!empty($selected_payment['user'])): ?>
                                <div class="info-row">
                                    <span class="info-label">Nama:</span>
                                    <span><?= htmlspecialchars($selected_payment['user']['nama_lengkap']) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Email:</span>
                                    <span><?= htmlspecialchars($selected_payment['user']['email']) ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">No HP:</span>
                                    <span><?= htmlspecialchars($selected_payment['user']['nomor_hp']) ?></span>
                                </div>
                            <?php endif; ?>

                            <hr>

                            <!-- Info Pengiriman -->
                            <h6 style="font-weight: 600; margin-bottom: 15px;"><i class="bi bi-box-seam"></i> Alamat Pengiriman</h6>
                            <?php if (!empty($selected_payment['pengiriman'])): ?>
                                <div class="info-row">
                                    <span class="info-label">Penerima:</span>
                                    <span><?= htmlspecialchars($selected_payment['pengiriman']['nama_penerima'] ?? '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Alamat:</span>
                                    <span><?= htmlspecialchars($selected_payment['pengiriman']['alamat'] ?? '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Kota:</span>
                                    <span><?= htmlspecialchars($selected_payment['pengiriman']['kota'] ?? '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Provinsi:</span>
                                    <span><?= htmlspecialchars($selected_payment['pengiriman']['provinsi'] ?? '-') ?></span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Kode Pos:</span>
                                    <span><?= htmlspecialchars($selected_payment['pengiriman']['kode_pos'] ?? '-') ?></span>
                                </div>
                                <div class="info-row" style="border-bottom: none;">
                                    <span class="info-label">Ongkir:</span>
                                    <span><strong>Rp <?= number_format((float)($selected_payment['pengiriman']['ongkir'] ?? 0), 0, ',', '.') ?></strong></span>
                                </div>
                            <?php else: ?>
                                <p class="text-muted"><i class="bi bi-exclamation-circle"></i> Info pengiriman belum ada</p>
                            <?php endif; ?>

                            <hr>

                            <!-- Items -->
                            <h6 style="font-weight: 600; margin-bottom: 15px;"><i class="bi bi-box"></i> Rincian Produk</h6>
                            <?php if (!empty($selected_payment['items']) && count($selected_payment['items']) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table items-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th>Qty</th>
                                                <th>Harga</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($selected_payment['items'] as $item): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                                                    <td><?= (int)$item['jumlah'] ?></td>
                                                    <td>Rp <?= number_format((float)$item['harga'], 0, ',', '.') ?></td>
                                                    <td><strong>Rp <?= number_format((float)$item['harga'] * (int)$item['jumlah'], 0, ',', '.') ?></strong></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted"><i class="bi bi-exclamation-circle"></i> Tidak ada item dalam transaksi</p>
                            <?php endif; ?>

                            <hr>

                            <!-- Total -->
                            <div style="background: #f0f7ff; padding: 15px; border-radius: 8px; margin: 15px 0;">
                                <div style="display: grid; grid-template-columns: 150px 1fr; gap: 15px; font-weight: 600;">
                                    <span>Total Pembayaran:</span>
                                    <span style="color: #667eea; font-size: 18px;">Rp <?= number_format((float)$selected_payment['total_harga'], 0, ',', '.') ?></span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="action-btns">
                                <form method="post" class="w-100">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="id_transaksi" value="<?= (int)$selected_payment['id_transaksi'] ?>">
                                    <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui pembayaran ini?');">
                                        <i class="bi bi-check-circle"></i> Setujui Pembayaran
                                    </button>
                                </form>
                            </div>
                            <div class="action-btns">
                                <form method="post" class="w-100">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="id_transaksi" value="<?= (int)$selected_payment['id_transaksi'] ?>">
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Tolak pembayaran ini? Pesanan akan dibatalkan.');">
                                        <i class="bi bi-x-circle"></i> Tolak Pembayaran
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="detail-card">
                        <div style="padding: 60px 20px; text-align: center; color: #999;">
                            <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 15px;"></i>
                            <p>Pilih pembayaran dari daftar untuk melihat detail</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>