<?php
// get_seats.php
require_once 'cors_helper.php';

$id_slot = isset($_GET['id_slot']) ? (int)$_GET['id_slot'] : 0;

if ($id_slot <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu hoặc sai ID suất chiếu (id_slot)'
    ]);
    exit;
}

try {
    // 1. Get room ID (id_phong) and ticket price from slot details
    $sql_slot = "SELECT kgc.id_phong, pc.name as ten_phong, pc.loai_phong,
                        lc.ngay_chieu, kgc.thoi_gian_chieu, p.tieu_de as ten_phim
                 FROM khung_gio_chieu kgc
                 JOIN phongchieu pc ON kgc.id_phong = pc.id
                 JOIN lichchieu lc ON kgc.id_lich_chieu = lc.id
                 JOIN phim p ON lc.id_phim = p.id
                 WHERE kgc.id = ?
                 LIMIT 1";
    $slot = pdo_query_one($sql_slot, $id_slot);

    if (!$slot) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Suất chiếu không tồn tại'
        ]);
        exit;
    }

    $id_phong = (int)$slot['id_phong'];

    // 2. Fetch seats in the room
    $sql_seats = "SELECT row_label, seat_number, code, tier, active 
                  FROM phong_ghe 
                  WHERE id_phong = ? 
                  ORDER BY row_label, seat_number";
    $seats = pdo_query($sql_seats, $id_phong);

    // Defensive: If no seats exist in the database for this room, generate default map (A-L, 1-18)
    if (empty($seats)) {
        require_once dirname(__DIR__) . '/Trang-admin/model/phong_ghe.php';
        pg_generate_default($id_phong);
        $seats = pdo_query($sql_seats, $id_phong);
    }

    // 3. Fetch booked seats for this slot
    $sql_booked = "SELECT ghe FROM ve WHERE id_thoi_gian_chieu = ? AND trang_thai IN (1, 2, 4)";
    $booked_tickets = pdo_query($sql_booked, $id_slot);

    $booked_seats = [];
    foreach ($booked_tickets as $ticket) {
        if (!empty($ticket['ghe'])) {
            $ticket_seats = explode(',', $ticket['ghe']);
            $booked_seats = array_merge($booked_seats, array_map('trim', $ticket_seats));
        }
    }

    // 4. Return combined seat data
    echo json_encode([
        'success' => true,
        'data' => [
            'slot_info' => [
                'id_slot' => $id_slot,
                'ten_phim' => $slot['ten_phim'],
                'ten_phong' => $slot['ten_phong'],
                'loai_phong' => $slot['loai_phong'],
                'ngay_chieu' => $slot['ngay_chieu'],
                'thoi_gian_chieu' => $slot['thoi_gian_chieu']
            ],
            'seats' => $seats,
            'booked_seats' => array_values(array_unique($booked_seats))
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
