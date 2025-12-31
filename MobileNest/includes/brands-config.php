<?php
/**
 * FILE: brands-config.php
 * PURPOSE: Menyimpan konfigurasi logo brand smartphone dari CDN
 * USAGE: include 'includes/brands-config.php'; kemudian gunakan $brands array
 */

// Array berisi semua brand smartphone dengan logo dari CDN
$brands = [
    'Apple' => [
        'name' => 'Apple',
        'logo' => 'https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/apple.svg',
        'color' => '#000000',
        'bg_color' => '#f5f5f5'
    ],
    'Samsung' => [
        'name' => 'Samsung',
        'logo' => 'https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/samsung.svg',
        'color' => '#1428A0',
        'bg_color' => '#e8f1ff'
    ],
    'Xiaomi' => [
        'name' => 'Xiaomi',
        'logo' => 'https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/xiaomi.svg',
        'color' => '#FF6900',
        'bg_color' => '#fff5f0'
    ],
    'Vivo' => [
        'name' => 'Vivo',
        'logo' => 'https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/vivo.svg',
        'color' => '#1FB2E3',
        'bg_color' => '#f0f8fc'
    ],
    'Realme' => [
        'name' => 'Realme',
        'logo' => 'https://www.realme.com/favicon.ico',
        'color' => '#FFB700',
        'bg_color' => '#fffbf0'
    ],
    'Oppo' => [
        'name' => 'Oppo',
        'logo' => 'https://cdn.jsdelivr.net/npm/simple-icons@v13/icons/oppo.svg',
        'color' => '#31A555',
        'bg_color' => '#f0fdf4'
    ]
];

/**
 * DOKUMENTASI PENGGUNAAN:
 * 
 * 1. Include file ini di awal file Anda:
 *    include 'includes/brands-config.php';
 * 
 * 2. Loop melalui brands:
 *    foreach ($brands as $key => $brand) {
 *        echo '<img src="' . $brand['logo'] . '" alt="' . $brand['name'] . '">';
 *    }
 * 
 * 3. Akses data brand tertentu:
 *    $apple = $brands['Apple'];
 *    echo $apple['logo'];  // URL logo
 *    echo $apple['color']; // Brand color
 */
?>
