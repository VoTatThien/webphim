<?php
$data = isset($_GET['data']) ? $_GET['data'] : 'No data';

if (extension_loaded('gd') && function_exists('imagecreate')) {
    require_once __DIR__ . '/../model/phpqrcode/qrlib.php';
    try {
        QRcode::png($data, false, QR_ECLEVEL_L, 5);
        exit;
    } catch (\Throwable $e) {
        // Fallback
    }
}

// Fallback trực tiếp sang online QR service nếu không có GD
header('Location: https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($data));
exit;
?>