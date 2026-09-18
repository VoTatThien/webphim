<?php
/**
 * QR Code Generator - Enhanced Version
 * Tạo mã QR lớn hơn, dễ quét hơn
 */

// Lấy dữ liệu từ URL
$data = isset($_GET['data']) ? $_GET['data'] : 'No data';
$size = isset($_GET['size']) ? (int)$_GET['size'] : 150; // Kích thước QR code (pixel)
$level = isset($_GET['level']) ? $_GET['level'] : 0; // Error correction level

if (extension_loaded('gd') && function_exists('imagecreate')) {
    // Tải thư viện QR code
    require_once __DIR__ . '/../model/phpqrcode/qrlib.php';
    try {
        // Thêm headers
        header('Content-Type: image/png');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $moduleSize = max(1, (int)($size / 20)); // Tính module size từ kích thước mong muốn
        QRcode::png($data, false, $level, $moduleSize, 2);
        exit;
    } catch (\Throwable $e) {
        // Fallback
    }
}

// Fallback sang online QR service
header('Location: https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($data));
exit;
