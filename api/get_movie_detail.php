<?php
// get_movie_detail.php
require_once 'cors_helper.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu hoặc sai ID phim'
    ]);
    exit;
}

try {
    // 1. Load movie details
    $sql_phim = "SELECT p.id, p.tieu_de, p.daodien, p.dienvien, p.img, p.mo_ta, 
                        p.date_phat_hanh, p.thoi_luong_phim, lp.name AS genre, 
                        p.quoc_gia, p.gia_han_tuoi, p.link_trailer
                 FROM phim p
                 INNER JOIN loaiphim lp ON lp.id = p.id_loai
                 WHERE p.id = ?";
    $movie = pdo_query_one($sql_phim, $id);

    if (!$movie) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Phim không tồn tại'
        ]);
        exit;
    }

    // Map image path to absolute URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base_path = '';
    if (preg_match('/^(.*)\/api/', $_SERVER['SCRIPT_NAME'], $matches)) {
        $base_path = $matches[1];
    }
    $baseUrl = "$protocol://$host" . $base_path . "/Trang-nguoi-dung/imgavt/";
    
    if (!empty($movie['img']) && !filter_var($movie['img'], FILTER_VALIDATE_URL)) {
        $movie['img_url'] = $baseUrl . $movie['img'];
    } else {
        $movie['img_url'] = $movie['img'] ?: '';
    }

    // 2. Load theatres (rap) showing this movie
    $sql_rap = "SELECT DISTINCT r.id, r.ten_rap, r.dia_chi, r.so_dien_thoai
                FROM rap_chieu r
                JOIN lichchieu lc ON lc.id_rap = r.id
                WHERE lc.id_phim = ? 
                  AND lc.ngay_chieu >= CURDATE()
                  AND lc.trang_thai_duyet = 'Đã duyệt'
                  AND r.trang_thai = 1
                ORDER BY r.ten_rap";
    $theatres = pdo_query($sql_rap, $id);

    // 3. For each theatre, load dates and showtimes
    $showtimes_tree = [];
    foreach ($theatres as $theatre) {
        $id_rap = $theatre['id'];
        
        // Load dates
        $sql_dates = "SELECT DISTINCT lc.ngay_chieu, lc.id as id_lich_chieu
                      FROM lichchieu lc
                      WHERE lc.id_phim = ?
                        AND lc.id_rap = ?
                        AND lc.ngay_chieu >= CURDATE()
                        AND lc.trang_thai_duyet = 'Đã duyệt'
                      ORDER BY lc.ngay_chieu";
        $dates = pdo_query($sql_dates, $id, $id_rap);
        
        $dates_data = [];
        foreach ($dates as $date) {
            $ngay_chieu = $date['ngay_chieu'];
            
            // Load times/slots for this date
            $sql_slots = "SELECT kgc.id as id_slot, kgc.thoi_gian_chieu, 
                                 pc.name as ten_phong, pc.id as id_phong, pc.loai_phong,
                                 (SELECT COUNT(*) FROM phong_ghe pg WHERE pg.id_phong = pc.id AND pg.active = 1) as tong_ghe
                          FROM khung_gio_chieu kgc
                          JOIN phongchieu pc ON kgc.id_phong = pc.id
                          WHERE kgc.id_lich_chieu = ?
                          ORDER BY kgc.thoi_gian_chieu";
            $slots = pdo_query($sql_slots, $date['id_lich_chieu']);
            
            // For each slot, compute booked seats
            foreach ($slots as &$slot) {
                $sql_booked = "SELECT ghe FROM ve WHERE id_thoi_gian_chieu = ? AND trang_thai IN (1, 2, 4)";
                $booked_tickets = pdo_query($sql_booked, $slot['id_slot']);
                
                $booked_seats_count = 0;
                $booked_seats_list = [];
                foreach ($booked_tickets as $ticket) {
                    if (!empty($ticket['ghe'])) {
                        $seats = explode(',', $ticket['ghe']);
                        $booked_seats_count += count($seats);
                        $booked_seats_list = array_merge($booked_seats_list, $seats);
                    }
                }
                
                $slot['ghe_da_dat'] = $booked_seats_count;
                $slot['ghe_trong'] = $slot['tong_ghe'] - $booked_seats_count;
            }
            
            $dates_data[] = [
                'ngay' => $ngay_chieu,
                'id_lich_chieu' => $date['id_lich_chieu'],
                'suat_chieu' => $slots
            ];
        }
        
        $showtimes_tree[] = [
            'id_rap' => $id_rap,
            'ten_rap' => $theatre['ten_rap'],
            'dia_chi' => $theatre['dia_chi'],
            'lich_dien_ra' => $dates_data
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'movie' => $movie,
            'theatres' => $showtimes_tree
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi kết nối cơ sở dữ liệu: ' . $e->getMessage()
    ]);
}
?>
