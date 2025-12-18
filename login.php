<?php
session_start();
require_once 'includes/config.php';

// Redirect jika sudah login
if (isset($_SESSION['user']) || isset($_SESSION['admin'])) {
    if (isset($_SESSION['admin'])) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/pesanan.php');
    }
    exit;
}

$error = '';
$success = '';

// PROSES LOGIN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validasi input
    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong!';
    } else {
        // 1️⃣ QUERY TABEL USERS
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                
                // 2️⃣ VERIFIKASI PASSWORD
                if (password_verify($password, $user_data['password'])) {
                    
                    // 3️⃣ CEK TABEL ADMIN (KUNCI PERBEDAAN ADMIN & USER)
                    $admin_check_sql = "SELECT id_admin FROM admin WHERE id_user = ?";
                    $admin_stmt = $conn->prepare($admin_check_sql);
                    
                    if (!$admin_stmt) {
                        $error = 'Database error: ' . $conn->error;
                    } else {
                        $admin_stmt->bind_param('i', $user_data['id_user']);
                        $admin_stmt->execute();
                        $admin_result = $admin_stmt->get_result();
                        
                        if ($admin_result->num_rows > 0) {
                            // ✅ ADALAH ADMIN
                            $_SESSION['admin'] = $user_data['id_user'];
                            $_SESSION['admin_name'] = $user_data['username'];
                            $_SESSION['admin_email'] = $user_data['email'];
                            
                            // Redirect ke admin dashboard
                            header('Location: admin/dashboard.php');
                            exit;
                        } else {
                            // ✅ ADALAH REGULAR USER
                            $_SESSION['user'] = $user_data['id_user'];
                            $_SESSION['user_name'] = $user_data['username'];
                            $_SESSION['user_email'] = $user_data['email'];
                            
                            // Redirect ke user dashboard
                            header('Location: user/pesanan.php');
                            exit;
                        }
                        
                        $admin_stmt->close();
                    }
                } else {
                    $error = '❌ Password salah!';
                }
            } else {
                $error = '❌ Username tidak ditemukan!';
            }
            
            $stmt->close();
        }
    }
}

// PROSES LOGOUT
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MobileNest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 40px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }

        .alert {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .login-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .info-box {
            background: #f0f4ff;
            border-left: 4px solid #667eea;
            padding: 12px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13px;
            color: #444;
            line-height: 1.6;
        }

        .info-box strong {
            display: block;
            margin-bottom: 8px;
            color: #667eea;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>
                <i class="fas fa-mobile-alt"></i>
                MobileNest
            </h1>
            <p>Platform E-Commerce Smartphone Terpercaya</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="login-form">
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i>
                    Username
                </label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Masukkan username Anda" 
                    required
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                    Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Masukkan password Anda" 
                    required
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" name="login" class="btn-login">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>

        <div class="info-box">
            <strong>📌 Demo Account:</strong>
            <div><strong>User:</strong> username: "budi", password: "pass123"</div>
            <div><strong>Admin:</strong> username: "admin1", password: "admin123"</div>
        </div>

        <div class="login-footer">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
