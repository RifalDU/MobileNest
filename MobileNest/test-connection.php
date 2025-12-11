<?php
// Include file konfigurasi
require_once 'config.php';

// Test koneksi
if ($conn) {
    echo "<h2>✅ Koneksi Database Berhasil!</h2>";
    echo "<p>Server: " . DB_HOST . "</p>";
    echo "<p>Database: " . DB_NAME . "</p>";
    
    // Test query - ambil data produk
    $sql = "SELECT COUNT(*) as total FROM produk";
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "<p>Total Produk di Database: " . $row['total'] . "</p>";
    }
    
    // Test query - ambil data admin
    $sql_admin = "SELECT COUNT(*) as total_admin FROM admin";
    $result_admin = mysqli_query($conn, $sql_admin);
    
    if ($result_admin) {
        $row_admin = mysqli_fetch_assoc($result_admin);
        echo "<p>Total Admin: " . $row_admin['total_admin'] . "</p>";
    }
    
    echo "<hr>";
    echo "<h3>✅ Setup Development Selesai!</h3>";
    echo "<p>Aplikasi MobileNest siap untuk dikembangkan.</p>";
} else {
    echo "<h2>❌ Koneksi Database Gagal!</h2>";
    echo "<p>Error: " . mysqli_connect_error() . "</p>";
}

// Tutup koneksi
mysqli_close($conn);
?>
