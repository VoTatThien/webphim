<?php
/**
 * Sepay Payment Gateway Configuration
 * 配置文件 - Thay đổi thông tin tại đây
 */

// ====================================================
// DATABASE CONFIGURATION
// ====================================================
$host = $_SERVER['HTTP_HOST'] ?? '';
$server_addr = $_SERVER['SERVER_ADDR'] ?? '';

$is_local = false;
if (DIRECTORY_SEPARATOR === '\\' || strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $is_local = true;
} elseif (
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false ||
    strpos($host, '192.168.') !== false ||
    strpos($host, '10.') === 0 ||
    strpos($host, '172.16.') !== false ||
    strpos($host, '172.17.') !== false ||
    strpos($host, '172.18.') !== false ||
    strpos($host, '172.19.') !== false ||
    strpos($host, '172.20.') !== false ||
    strpos($host, '172.21.') !== false ||
    strpos($host, '172.22.') !== false ||
    strpos($host, '172.23.') !== false ||
    strpos($host, '172.24.') !== false ||
    strpos($host, '172.25.') !== false ||
    strpos($host, '172.26.') !== false ||
    strpos($host, '172.27.') !== false ||
    strpos($host, '172.28.') !== false ||
    strpos($host, '172.29.') !== false ||
    strpos($host, '172.30.') !== false ||
    strpos($host, '172.31.') !== false ||
    $server_addr === '127.0.0.1' ||
    $server_addr === '::1'
) {
    $is_local = true;
}

if ($is_local) {
    $active_port = '3306';
    foreach (['3306', '3307'] as $p) {
        try {
            $test_conn = new PDO("mysql:host=127.0.0.1;port=$p;dbname=cinepass;charset=utf8mb4", 'root', '', [PDO::ATTR_TIMEOUT => 1]);
            $active_port = $p;
            break;
        } catch (PDOException $e) {}
    }
    define('DB_HOST', "127.0.0.1;port=$active_port");
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'cinepass');
} else {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'u508775056_cinepass');
    define('DB_PASS', 'Kpy123456@@');
    define('DB_NAME', 'u508775056_cinepass');
}

// ====================================================
// SEPAY BANK ACCOUNT CONFIGURATION
// ====================================================
define('BANK_ACCOUNT_NAME', 'GALAXY STUDIO');
define('BANK_ACCOUNT_NUMBER', '0384104942');
define('BANK_CODE', 'MBBANK');
define('BANK_NAME', 'Ngân Hàng TMCP Quân Đội');

// ====================================================
// SEPAY WEBHOOK CONFIGURATION
// ====================================================
// Thay YOUR_DOMAIN bằng domain thực của bạn
// Ví dụ: https://webphim.gt.tc (cho production)
define('SEPAY_WEBHOOK_URL', 'https://webphim.gt.tc/Trang-nguoi-dung/sepay/sepay_webhook.php');
define('SEPAY_RETURN_URL', 'https://webphim.gt.tc/Trang-nguoi-dung/sepay/sepay_return.php');

// ====================================================
// APPLICATION CONFIGURATION
// ====================================================
define('ORDER_PREFIX', 'VE');  // Mã vé: VE123456
define('DOMAIN', 'https://webphim.gt.tc');

// ====================================================
// EMAIL CONFIGURATION (From ve.php)
// ====================================================
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'thanhbang0162@gmail.com');
define('MAIL_PASSWORD', 'cooh jnvf szck cwux');     // Gmail App Password từ ve.php
define('MAIL_FROM_NAME', 'Galaxy Studio');
define('MAIL_FROM_EMAIL', 'thanhbang0162@gmail.com');

// ====================================================
// POINT CONFIGURATION
// ====================================================
define('POINTS_PER_VND', 0.01);  // 1 VND = 0.01 điểm (100,000 VND = 1000 điểm)
define('POINTS_BONUS_RATE', 1.0); // 100% điểm thêm cho thanh toán online

?>
