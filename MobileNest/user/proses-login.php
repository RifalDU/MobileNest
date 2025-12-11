<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $password          = $_POST['password'] ?? '';

    if ($username_or_email === '' || $password === '') {
        $_SESSION['error'] = "Username/email dan password wajib diisi.";
        header('Location: login.php');
        exit;
    }

    $sql = "SELECT * FROM users 
            WHERE username='$username_or_email' OR email='$username_or_email' 
            LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) { // cek hash[web:190][web:201]
            $_SESSION['id_user']      = $user['id_user'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['email']        = $user['email'];
            $_SESSION['role']         = $user['status'] ?? 'user';

            $_SESSION['success'] = "Login berhasil. Selamat datang, " . $user['nama_lengkap'] . "!";
            header('Location: ../index.php');
            exit;
        } else {
            $_SESSION['error'] = "Password salah.";
            header('Location: login.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "Akun tidak ditemukan.";
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}
