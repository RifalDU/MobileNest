<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/auth-check.php';
require_user_login();
require_once '../config.php';
$user_id = $_SESSION['user'];
$user_data = [];
$errors = [];
$message = '';

$sql = "SELECT id_user, nama_lengkap, email, no_telepon, alamat FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) { die("Prepare failed: " . $conn->error); }
$stmt->bind_param('i', $user_id);
if (!$stmt->execute()) { die("Execute failed: " . $stmt->error); }
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
}
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    if ($action === 'edit_profil') {
        $nama = isset($_POST['nama_lengkap']) ? trim($_POST['nama_lengkap']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $telepon = isset($_POST['no_telepon']) ? trim($_POST['no_telepon']) : '';
        $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : '';
        
        if (empty($nama)) { $errors[] = 'Nama lengkap tidak boleh kosong'; }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Email tidak valid'; }
        
        $email_check_sql = "SELECT id_user FROM users WHERE email = ? AND id_user != ?";
        $email_check = $conn->prepare($email_check_sql);
        $email_check->bind_param('si', $email, $user_id);
        $email_check->execute();
        if ($email_check->get_result()->num_rows > 0) {
            $errors[] = 'Email sudah digunakan oleh user lain';
        }
        $email_check->close();
        
        if (empty($errors)) {
            $update_sql = "UPDATE users SET nama_lengkap = ?, email = ?, no_telepon = ?, alamat = ? WHERE id_user = ?";
            $update_stmt = $conn->prepare($update_sql);
            if (!$update_stmt) {
                $errors[] = 'Prepare failed: ' . $conn->error;
            } else {
                $update_stmt->bind_param('ssssi', $nama, $email, $telepon, $alamat, $user_id);
                if ($update_stmt->execute()) {
                    $message = '✅ Profil berhasil diperbarui!';
                    $user_data['nama_lengkap'] = $nama;
                    $user_data['email'] = $email;
                    $user_data['no_telepon'] = $telepon;
                    $user_data['alamat'] = $alamat;
                } else {
                    $errors[] = 'Error: ' . $update_stmt->error;
                }
                $update_stmt->close();
            }
        }
    } elseif ($action === 'ubah_password') {
        $password_lama = isset($_POST['password_lama']) ? $_POST['password_lama'] : '';
        $password_baru = isset($_POST['password_baru']) ? $_POST['password_baru'] : '';
        $password_konfirm = isset($_POST['password_konfirm']) ? $_POST['password_konfirm'] : '';
        
        if (empty($password_lama)) { $errors[] = 'Password lama tidak boleh kosong'; }
        if (empty($password_baru) || strlen($password_baru) < 6) { $errors[] = 'Password baru minimal 6 karakter'; }
        if ($password_baru !== $password_konfirm) { $errors[] = 'Password baru tidak sama dengan konfirmasi'; }
        
        if (empty($errors)) {
            $check_pwd_sql = "SELECT password FROM users WHERE id_user = ?";
            $check_pwd = $conn->prepare($check_pwd_sql);
            $check_pwd->bind_param('i', $user_id);
            $check_pwd->execute();
            $pwd_result = $check_pwd->get_result();
            $user = $pwd_result->fetch_assoc();
            $check_pwd->close();
            
            if (password_verify($password_lama, $user['password'])) {
                $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                $pwd_update_sql = "UPDATE users SET password = ? WHERE id_user = ?";
                $pwd_update = $conn->prepare($pwd_update_sql);
                $pwd_update->bind_param('si', $password_hash, $user_id);
                if ($pwd_update->execute()) {
                    $message = '✅ Password berhasil diubah!';
                } else {
                    $errors[] = 'Error: ' . $pwd_update->error;
                }
                $pwd_update->close();
            } else {
                $errors[] = 'Password lama tidak sesuai';
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
    <title>Profil Saya - MobileNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-pills .nav-link { color: #333; border-radius: 0; border-bottom: 3px solid transparent; }
        .nav-pills .nav-link.active { background-color: transparent; color: #007bff; border-bottom: 3px solid #007bff; }
        .profile-section { min-height: 400px; }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>
    <div class="container py-5">
        <h1 class="mb-4">👤 Profil Saya</h1>
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p class="mb-1">❌ <?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <ul class="nav nav-pills mb-4 border-bottom">
            <li class="nav-item">
                <a class="nav-link active" href="#data-pribadi" data-bs-toggle="tab">📋 Data Pribadi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#ubah-password" data-bs-toggle="tab">🔐 Ubah Password</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active profile-section" id="data-pribadi">
                <form method="POST">
                    <input type="hidden" name="action" value="edit_profil">
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Nama Lengkap</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="nama_lengkap" value="<?php echo htmlspecialchars($user_data['nama_lengkap'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Email</label>
                        <div class="col-md-6">
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">No. Telepon</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="no_telepon" value="<?php echo htmlspecialchars($user_data['no_telepon'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Alamat</label>
                        <div class="col-md-6">
                            <textarea class="form-control" name="alamat" rows="3"><?php echo htmlspecialchars($user_data['alamat'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100">💾 Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="tab-pane fade profile-section" id="ubah-password">
                <form method="POST">
                    <input type="hidden" name="action" value="ubah_password">
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Password Lama</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" name="password_lama" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Password Baru</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" name="password_baru" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label">Konfirmasi Password</label>
                        <div class="col-md-6">
                            <input type="password" class="form-control" name="password_konfirm" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <button type="submit" class="btn btn-danger btn-lg w-100">🔐 Ubah Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include '../footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
