<?php
/**
 * ============================================
 * FILE: auth-check.php
 * PURPOSE: Centralized Authentication & Authorization
 * LOCATION: MobileNest/includes/auth-check.php
 * ============================================
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========================================
// 1️⃣ FUNGSI: CEK USER SUDAH LOGIN
// ========================================
function is_user_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

// ========================================
// 2️⃣ FUNGSI: CEK ADMIN SUDAH LOGIN
// ========================================
function is_admin_logged_in() {
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']);
}

// ========================================
// 3️⃣ FUNGSI: CEK USER ROLE (USER BIASA)
// ========================================
function is_user_only() {
    return is_user_logged_in() && !is_admin_logged_in();
}

// ========================================
// 4️⃣ FUNGSI: CEK ADMIN ROLE (ADMIN)
// ========================================
function is_admin_only() {
    return is_admin_logged_in() && !is_user_logged_in();
}

// ========================================
// 5️⃣ FUNGSI: PROTEKSI HALAMAN USER
// ========================================
function require_user_login() {
    if (!is_user_logged_in()) {
        $_SESSION['error'] = 'Anda harus login sebagai user untuk mengakses halaman ini!';
        header('Location: ' . dirname(__DIR__) . '/user/login.php');
        exit;
    }
    
    // Pastikan bukan admin yang akses halaman user
    if (is_admin_logged_in()) {
        $_SESSION['error'] = 'Admin tidak bisa mengakses halaman user!';
        header('Location: ' . dirname(__DIR__) . '/admin/dashboard.php');
        exit;
    }
}

// ========================================
// 6️⃣ FUNGSI: PROTEKSI HALAMAN ADMIN
// ========================================
function require_admin_login() {
    if (!is_admin_logged_in()) {
        $_SESSION['error'] = 'Anda harus login sebagai admin untuk mengakses halaman ini!';
        header('Location: ' . dirname(__DIR__) . '/user/login.php');
        exit;
    }
    
    // Pastikan bukan user biasa yang akses halaman admin
    if (is_user_logged_in() && !is_admin_logged_in()) {
        $_SESSION['error'] = 'User biasa tidak punya akses ke panel admin!';
        header('Location: ' . dirname(__DIR__) . '/user/dashboard.php');
        exit;
    }
}

// ========================================
// 7️⃣ FUNGSI: GET CURRENT USER ID
// ========================================
function get_current_user_id() {
    return $_SESSION['user'] ?? null;
}

// ========================================
// 8️⃣ FUNGSI: GET CURRENT ADMIN ID
// ========================================
function get_current_admin_id() {
    return $_SESSION['admin'] ?? null;
}

// ========================================
// 9️⃣ FUNGSI: GET CURRENT ROLE
// ========================================
function get_current_role() {
    if (is_admin_logged_in()) {
        return 'admin';
    } elseif (is_user_logged_in()) {
        return 'user';
    }
    return 'guest';
}

// ========================================
// 🔟 FUNGSI: FORBIDDEN ACCESS
// ========================================
function forbidden_access($message = null) {
    http_response_code(403);
    
    if ($message === null) {
        $message = 'Anda tidak memiliki akses ke halaman ini!';
    }
    
    $_SESSION['error'] = $message;
    
    // Redirect ke halaman sesuai role
    if (is_admin_logged_in()) {
        header('Location: ' . dirname(__DIR__) . '/admin/dashboard.php');
    } elseif (is_user_logged_in()) {
        header('Location: ' . dirname(__DIR__) . '/user/dashboard.php');
    } else {
        header('Location: ' . dirname(__DIR__) . '/user/login.php');
    }
    exit;
}

// ========================================
// 1️⃣11️⃣ FUNGSI: CEK KEPEMILIKAN DATA
// ========================================
function user_owns_data($data_user_id) {
    if (!is_user_logged_in()) {
        return false;
    }
    
    return get_current_user_id() == $data_user_id;
}

// ========================================
// 1️⃣12️⃣ FUNGSI: SAFE REDIRECT BY ROLE
// ========================================
function redirect_by_role() {
    if (is_admin_logged_in()) {
        header('Location: ' . dirname(__DIR__) . '/admin/dashboard.php');
        exit;
    } elseif (is_user_logged_in()) {
        header('Location: ' . dirname(__DIR__) . '/user/dashboard.php');
        exit;
    } else {
        header('Location: ' . dirname(__DIR__) . '/index.php');
        exit;
    }
}

?>
