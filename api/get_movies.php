<?php
// get_movies.php
require_once 'cors_helper.php';

try {
    $sql = "SELECT p.id, p.tieu_de, p.daodien, p.dienvien, p.img, p.mo_ta, 
                   p.date_phat_hanh, p.thoi_luong_phim, lp.name AS genre, 
                   p.quoc_gia, p.gia_han_tuoi, p.link_trailer
            FROM phim p
            INNER JOIN loaiphim lp ON lp.id = p.id_loai
            ORDER BY p.id DESC";
            
    $movies = pdo_query($sql);
    
    // Map image paths to absolute URLs
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base_path = '';
    if (preg_match('/^(.*)\/api/', $_SERVER['SCRIPT_NAME'], $matches)) {
        $base_path = $matches[1];
    }
    $baseUrl = "$protocol://$host" . $base_path . "/Trang-nguoi-dung/imgavt/";
    
    foreach ($movies as &$movie) {
        if (!empty($movie['img']) && !filter_var($movie['img'], FILTER_VALIDATE_URL)) {
            $movie['img_url'] = $baseUrl . $movie['img'];
        } else {
            $movie['img_url'] = $movie['img'] ?: '';
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $movies
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()
    ]);
}
?>
