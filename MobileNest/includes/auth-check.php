<?php
/**
 * ============================================
 * FILE: auth-check.php
 * PURPOSE: Role-Based Access Control (RBAC)
 * LOCATION: MobileNest/includes/auth-check.php
 * ============================================
 */

// Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========================================
// PROTEKSI LOGIN - USER
// ========================================
function require_user_login() {
    if (!isset($_SESSION['user'])) {
        $_SESSION['error'] = 'Anda harus login terlebih dahulu!';
        header('Location: ' . str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 2) . 'user/login.php');
        exit;
    }
    
    // Jika yang login adalah admin, redirect ke admin panel
    if (isset($_SESSION['admin'])) {
        header('Location: ' . str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 3) . 'admin/dashboard.php');
        exit;
    }
}

// ========================================
// PROTEKSI LOGIN - ADMIN
// ========================================
function require_admin_login() {
    if (!isset($_SESSION['admin'])) {
        $_SESSION['error'] = 'Anda harus login sebagai admin terlebih dahulu!';
        header('Location: ' . str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 2) . 'admin/index.php');
        exit;
    }
    
    // Jika yang login adalah user biasa, redirect ke user dashboard
    if (isset($_SESSION['user']) && !isset($_SESSION['admin'])) {
        header('Location: ' . str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/') - 3) . 'user/pesanan.php');
        exit;
    }
}

// ========================================
// CEK STATUS LOGIN - USER
// ========================================
function is_user_logged_in() {
    return isset($_SESSION['user']);
}

// ========================================
// CEK STATUS LOGIN - ADMIN
// ========================================
function is_admin_logged_in() {
    return isset($_SESSION['admin']);
}

// ========================================
// GET USER ID
// ========================================
function get_user_id() {
    return $_SESSION['user'] ?? null;
}

// ========================================
// GET ADMIN ID
// ========================================
function get_admin_id() {
    return $_SESSION['admin'] ?? null;
}

// ========================================
// LOGOUT
// ========================================
function user_logout() {
    unset($_SESSION['user']);
    session_destroy();
    header('Location: login.php');
    exit;
}

function admin_logout() {
    unset($_SESSION['admin']);
    session_destroy();
    header('Location: index.php');
    exit;
}

// ========================================
// REDIRECT BERDASARKAN ROLE
// ========================================
function redirect_based_on_role() {
    if (isset($_SESSION['admin'])) {
        header('Location: admin/dashboard.php');
        exit;
    } elseif (isset($_SESSION['user'])) {
        header('Location: user/pesanan.php');
        exit;
    }
}

// ========================================
// CSRF TOKEN GENERATION
// ========================================
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// ========================================
// VERIFY CSRF TOKEN
// ========================================
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>
