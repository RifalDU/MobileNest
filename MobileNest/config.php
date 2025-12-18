<?php
/**
 * MobileNest - Database Configuration & Global Setup
 * Koneksi MySQLi dan konfigurasi umum untuk seluruh aplikasi
 * Updated: Database name changed to mobilenest_db
 * 
 * NOTE: Authentication functions sudah dipindah ke includes/auth-check.php
 * Jangan duplikasi fungsi di sini!
 */

// Prevent direct access
if (basename($_SERVER['PHP_SELF']) === 'config.php') {
    header('HTTP/1.1 403 Forbidden');
    exit('Access Denied');
}

// ===== DATABASE CONNECTION =====
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';  // Password kosong jika default XAMPP
$db_name = 'mobilenest_db';  // ✅ Updated to match actual database name

// Create MySQLi connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_errno) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset('utf8mb4');

// ===== SESSION CONFIGURATION =====
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // set to 1 if using HTTPS
    session_start();
}

// ===== GLOBAL CONSTANTS =====
define('SITE_NAME', 'MobileNest');
define('SITE_URL', 'http://localhost/MobileNest');
define('ADMIN_PATH', __DIR__ . '/admin');
define('UPLOADS_PATH', __DIR__ . '/uploads');

// ===== HELPER FUNCTIONS - UTILITY ONLY =====
// NOTE: Auth, Log, and CSRF token functions are in includes/auth-check.php

/**
 * Sanitize input to prevent XSS
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Format currency to Rupiah
 */
function format_rupiah($amount) {
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
}

/**
 * Escape SQL input (additional safety)
 */
function escape_input($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

/**
 * Execute query and return result
 */
function execute_query($sql) {
    global $conn;
    $result = $conn->query($sql);
    if (!$result) {
        error_log('Query Error: ' . $conn->error);
        return false;
    }
    return $result;
}

/**
 * Fetch single row as associative array
 */
function fetch_single($sql) {
    $result = execute_query($sql);
    return $result ? $result->fetch_assoc() : null;
}

/**
 * Fetch all rows as array
 */
function fetch_all($sql) {
    $result = execute_query($sql);
    if (!$result) return [];
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Handle file upload securely
 */
function upload_file($file_input, $allowed_types = ['jpg', 'jpeg', 'png', 'gif'], $max_size = 5242880) {
    if (!isset($_FILES[$file_input])) {
        return ['success' => false, 'message' => 'File not found'];
    }

    $file = $_FILES[$file_input];
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_error = $file['error'];

    // Check for upload errors
    if ($file_error !== 0) {
        return ['success' => false, 'message' => 'Upload error'];
    }

    // Check file size
    if ($file_size > $max_size) {
        return ['success' => false, 'message' => 'File too large'];
    }

    // Get file extension
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_types)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }

    // Generate unique filename
    $new_filename = uniqid() . '.' . $file_ext;
    $upload_path = UPLOADS_PATH . '/' . $new_filename;

    // Create uploads directory if not exists
    if (!is_dir(UPLOADS_PATH)) {
        mkdir(UPLOADS_PATH, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file_tmp, $upload_path)) {
        return [
            'success' => true,
            'filename' => $new_filename,
            'path' => $upload_path,
            'url' => SITE_URL . '/uploads/' . $new_filename
        ];
    }

    return ['success' => false, 'message' => 'Upload failed'];
}

/**
 * Destroy session and logout
 */
function logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies') && !headers_sent()) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
}

// ===== ERROR HANDLING =====
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Custom error handler (optional)
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile on line $errline");
    return true;
});

// Shutdown handler untuk mencegah white screen of death
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error) {
        error_log("Fatal Error: " . print_r($error, true));
    }
});

// Close connection on script end
register_shutdown_function(function() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
});

?>
