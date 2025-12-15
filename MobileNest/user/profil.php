<?php
// Mulai session
session_start();

// Cek user sudah login
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Include config database
require_once '../config.php';

$user_id = $_SESSION['user'];
$user_data = [];
$message = '';
$message_type = '';

// Query user data
$sql = "SELECT id_user, username, nama_lengkap, email, no_telepon, alamat, tanggal_daftar, password
        FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
} else {
    header('Location: login.php');
    exit;
}

// Process UPDATE PROFIL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    // Ambil data dari form
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $no_telepon = trim($_POST['no_telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    
    // Validasi data
    $errors = [];
    
    if (empty($nama_lengkap)) {
        $errors[] = 'Nama lengkap tidak boleh kosong';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid';
    }
    
    // Cek email sudah digunakan user lain
    if ($email !== $user_data['email']) {
        $check_sql = "SELECT id_user FROM users WHERE email = ? AND id_user != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('si', $email, $user_id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $errors[] = 'Email sudah terdaftar atas nama lain';
        }
    }
    
    // Jika tidak ada error, update data
    if (empty($errors)) {
        $update_sql = "UPDATE users SET nama_lengkap = ?, email = ?, 
                      no_telepon = ?, alamat = ? WHERE id_user = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('ssssi', $nama_lengkap, $email, $no_telepon, $alamat, $user_id);
        
        if ($update_stmt->execute()) {
            // Update session
            $_SESSION['user_name'] = $nama_lengkap;
            $user_data['nama_lengkap'] = $nama_lengkap;
            $user_data['email'] = $email;
            $user_data['no_telepon'] = $no_telepon;
            $user_data['alamat'] = $alamat;
            
            $message = 'Profil berhasil diperbarui!';
            $message_type = 'success';
        } else {
            $message = 'Error: ' . $update_stmt->error;
            $message_type = 'danger';
        }
    } else {
        $message = implode(', ', $errors);
        $message_type = 'danger';
    }
}

// Process UBAH PASSWORD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ubah_password') {
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    $errors = [];
    
    // Verifikasi password lama
    if (!password_verify($password_lama, $user_data['password'])) {
        $errors[] = 'Password lama tidak sesuai';
    }
    
    // Validasi password baru
    if (empty($password_baru) || strlen($password_baru) < 6) {
        $errors[] = 'Password baru minimal 6 karakter';
    }
    
    if ($password_baru !== $password_confirm) {
        $errors[] = 'Password baru tidak cocok';
    }
    
    // Update password
    if (empty($errors)) {
        $hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = ? WHERE id_user = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param('si', $hash_baru, $user_id);
        
        if ($update_stmt->execute()) {
            $message = 'Password berhasil diubah!';
            $message_type = 'success';
        } else {
            $message = 'Error: ' . $update_stmt->error;
            $message_type = 'danger';
        }
    } else {
        $message = implode(', ', $errors);
        $message_type = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - MobileNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .nav-pills .nav-link {
            color: #495057;
            border-radius: 10px;
            margin-bottom: 0.5rem;
        }
        
        .nav-pills .nav-link.active {
            background-color: #007bff;
        }
        
        .form-section {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            display: none;
        }
        
        .form-section.active {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../header.php'; ?>

    <div class="container py-5">
        <!-- Profile Card -->
        <div class="profile-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2><?php echo htmlspecialchars($user_data['nama_lengkap']); ?></h2>
                    <p class="mb-0">📧 <?php echo htmlspecialchars($user_data['email']); ?></p>
                    <p class="mb-0">📱 <?php echo htmlspecialchars($user_data['no_telepon'] ?? 'Belum diisi'); ?></p>
                </div>
                <div class="col-md-4 text-end">
                    <small class="text-white-50">Bergabung sejak:</small><br>
                    <span><?php echo date('d M Y', strtotime($user_data['tanggal_daftar'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Notifikasi -->
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Menu Sidebar -->
            <div class="col-md-3">
                <div class="nav flex-column nav-pills">
                    <button class="nav-link active" onclick="showSection('data-pribadi')">
                        👤 Data Pribadi
                    </button>
                    <button class="nav-link" onclick="showSection('ubah-password')">
                        🔐 Ubah Password
                    </button>
                    <button class="nav-link" onclick="showSection('pesanan')">
                        📦 Pesanan
                    </button>
                    <a href="logout.php" class="nav-link mt-3">
                        🛞 Logout
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-md-9">
                <!-- DATA PRIBADI -->
                <div id="data-pribadi" class="form-section active">
                    <h4 class="mb-4">📝 Data Pribadi</h4>
                    <form method="POST">
                        <input type="hidden" name="action" value="update">
                        
                        <div class="mb-3">
                            <label class="form-label">Username (tidak bisa diubah)</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_data['username']); ?>" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama_lengkap" class="form-control" 
                                   value="<?php echo htmlspecialchars($user_data['nama_lengkap']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="tel" name="no_telepon" class="form-control" 
                                   value="<?php echo htmlspecialchars($user_data['no_telepon'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="4"><?php echo htmlspecialchars($user_data['alamat'] ?? ''); ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                            <button type="reset" class="btn btn-secondary">❌ Batal</button>
                        </div>
                    </form>
                </div>

                <!-- UBAH PASSWORD -->
                <div id="ubah-password" class="form-section">
                    <h4 class="mb-4">🔐 Ubah Password</h4>
                    <form method="POST">
                        <input type="hidden" name="action" value="ubah_password">
                        
                        <div class="mb-3">
                            <label class="form-label">Password Lama *</label>
                            <input type="password" name="password_lama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru (minimal 6 karakter) *</label>
                            <input type="password" name="password_baru" class="form-control" minlength="6" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru *</label>
                            <input type="password" name="password_confirm" class="form-control" minlength="6" required>
                        </div>

                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary">🔐 Ubah Password</button>
                            <button type="reset" class="btn btn-secondary">❌ Batal</button>
                        </div>
                    </form>
                </div>

                <!-- PESANAN -->
                <div id="pesanan" class="form-section">
                    <h4 class="mb-4">📦 Pesanan Saya</h4>
                    <p class="text-muted">Lihat riwayat pesanan Anda</p>
                    <a href="pesanan.php" class="btn btn-primary">➡️ Lihat Semua Pesanan</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showSection(id) {
            // Sembunyikan semua section
            document.querySelectorAll('.form-section').forEach(el => {
                el.classList.remove('active');
            });
            
            // Tampilkan section yang dipilih
            document.getElementById(id).classList.add('active');
            
            // Update active nav link
            document.querySelectorAll('.nav-link').forEach(el => {
                el.classList.remove('active');
            });
            event.target.classList.add('active');
        }
    </script>
</body>
</html>