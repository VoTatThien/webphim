<?php
/**
 * API Endpoint: Lấy cấu hình website từ admin
 * Được gọi bởi header.php, footer.php để hiển thị thông tin từ cấu hình
 */

header('Content-Type: application/json; charset=utf-8');

try {
    // Kết nối database
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
        $ports = ['3306', '3307'];
        $pdo = null;
        $last_err = null;
        foreach ($ports as $port) {
            try {
                $dsn = "mysql:host=127.0.0.1;port=$port;dbname=cinepass;charset=utf8mb4";
                $pdo = new PDO($dsn, 'root', '', [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                break;
            } catch (PDOException $e) {
                $last_err = $e;
            }
        }
        if (!$pdo) {
            throw $last_err;
        }
    } else {
        $dsn = "mysql:host=localhost;port=3306;dbname=u508775056_cinepass;charset=utf8mb4";
        $pdo = new PDO($dsn, 'u508775056_cinepass', 'Kpy123456@@', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
    
    // Lấy cấu hình
    $stmt = $pdo->prepare("SELECT * FROM thong_tin_website WHERE id = 1");
    $stmt->execute();
    $config = $stmt->fetch();
    
    if (!$config) {
        // Default nếu chưa có cấu hình
        $config = [
            'id' => 1,
            'ten_website' => 'Galaxy Studio',
            'logo' => 'Galaxy_Studio_2003_(Wordmark)_(Grey).webp',
            'dia_chi' => '',
            'so_dien_thoai' => '',
            'email' => '',
            'facebook' => '',
            'instagram' => '',
            'youtube' => '',
            'mo_ta' => 'Nền tảng mua vé xem phim hàng đầu',
            'ngay_cap_nhat' => date('Y-m-d H:i:s')
        ];
    }
    
    // Xử lý logo path
    if (!empty($config['logo'])) {
        // Nếu không có http, thêm imgavt/
        if (strpos($config['logo'], 'http') === false && strpos($config['logo'], 'imgavt/') === false) {
            $config['logo'] = 'imgavt/' . $config['logo'];
        }
    } else {
        $config['logo'] = 'imgavt/Galaxy_Studio_2003_(Wordmark)_(Grey).webp';
    }
    
    // Xử lý video_banner path
    if (empty($config['video_banner'])) {
        $config['video_banner'] = 'video/OFFICIAL TRAILER.mp4'; // Video mặc định
    } else {
        // Nếu không có video/, thêm vào
        if (strpos($config['video_banner'], 'http') === false && strpos($config['video_banner'], 'video/') === false) {
            $config['video_banner'] = 'video/' . $config['video_banner'];
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $config
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
