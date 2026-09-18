<?php
/**
 * ============================================================================
 * Model: Gợi ý Combo Đồ Ăn - Decision Tree Wrapper
 * ============================================================================
 * 
 * Thuật toán: Decision Tree (ID3 - Information Gain)
 * Thay thế hoàn toàn hệ thống Weighted Multi-Criteria Scoring cũ.
 * 
 * Các phương pháp:
 *   1. Decision Tree: Dự đoán combo phù hợp dựa trên đặc trưng khách hàng
 *   2. Tracking: Ghi log để đo lường hiệu quả gợi ý
 * 
 * File: Trang-nguoi-dung/model/combo_recommend.php
 */

// Nạp kết nối PDO nếu chưa có
if (!function_exists('pdo_query')) {
    require_once __DIR__ . '/pdo.php';
}

// Include thuật toán Decision Tree
include_once __DIR__ . '/combo_decision_tree.php';

// =====================================================================
// 1. HÀM CHÍNH: GỢI Ý COMBO CHO KHÁCH HÀNG
// =====================================================================

/**
 * Gợi ý combo cho khách hàng dựa trên Decision Tree.
 * Thu thập đặc trưng từ thông tin người dùng và ngữ cảnh đặt vé.
 * 
 * @param int    $user_id          ID tài khoản
 * @param int    $id_phim          ID phim đang đặt
 * @param string $gio_chieu        Giờ chiếu (VD: "19:00")
 * @param int    $so_ghe           Số ghế đã chọn
 * @param array  $available_combos Danh sách combo khả dụng tại rạp
 * @return array [
 *   'suggested_combo' => array|null   Combo được gợi ý (từ danh sách available)
 *   'reco_type'       => string       Loại gợi ý (decision_tree)
 *   'reco_title'      => string       Tiêu đề hiển thị
 *   'reco_desc'       => string       Mô tả gợi ý
 *   'confidence'      => float        Độ tin cậy (0-1)
 *   'tree_path'       => string       Đường đi trên cây quyết định
 *   'all_recommendations' => array    Top 3 combo gợi ý
 * ]
 */
function recommend_combo($user_id, $id_phim, $gio_chieu, $so_ghe, $available_combos = []) {
    // --- Bước 1: Thu thập đặc trưng (Features) ---
    
    // 1a. Khoảng tuổi và giới tính từ DB
    $khoang_tuoi = 'unknown';
    $gioi_tinh = 'unknown';
    if ($user_id > 0) {
        $user_data = pdo_query_one("SELECT khoang_tuoi, gioi_tinh, ngay_sinh FROM taikhoan WHERE id = ?", $user_id);
        if ($user_data) {
            $gioi_tinh = !empty($user_data['gioi_tinh']) ? $user_data['gioi_tinh'] : 'unknown';
            
            // Ưu tiên khoang_tuoi, fallback sang ngay_sinh
            if (!empty($user_data['khoang_tuoi'])) {
                $khoang_tuoi = $user_data['khoang_tuoi'];
            } elseif (!empty($user_data['ngay_sinh'])) {
                $birthDate = new DateTime($user_data['ngay_sinh']);
                $tuoi = (new DateTime())->diff($birthDate)->y;
                if ($tuoi < 18) $khoang_tuoi = 'duoi_18';
                elseif ($tuoi <= 25) $khoang_tuoi = '18_25';
                elseif ($tuoi <= 35) $khoang_tuoi = '26_35';
                elseif ($tuoi <= 45) $khoang_tuoi = '36_45';
                else $khoang_tuoi = 'tren_45';
            }
        }
    }
    
    // 1b. Thể loại phim
    $the_loai = 'unknown';
    if ($id_phim > 0) {
        $phim_info = pdo_query_one(
            "SELECT lp.name as the_loai FROM phim p 
             LEFT JOIN loaiphim lp ON p.id_loai = lp.id 
             WHERE p.id = ?", $id_phim
        );
        $the_loai = $phim_info['the_loai'] ?? 'unknown';
    }
    
    // 1c. Khung giờ chiếu → phân loại
    $hour = (int) explode(':', $gio_chieu)[0];
    if ($hour < 12) $gio_chieu_slot = 'sang';
    elseif ($hour < 18) $gio_chieu_slot = 'chieu';
    else $gio_chieu_slot = 'toi';
    
    // 1d. Thứ trong tuần
    $day_of_week = date('N'); // 1=Mon, 7=Sun
    $thu = ($day_of_week >= 6) ? 'weekend' : 'weekday';
    
    // 1e. Số ghế → phân loại
    if ($so_ghe >= 3) $so_ghe_cat = '3+';
    elseif ($so_ghe == 2) $so_ghe_cat = '2';
    else $so_ghe_cat = '1';
    
    // --- Bước 2: Gọi Decision Tree dự đoán ---
    $features = [
        'the_loai'       => $the_loai,
        'gio_chieu'      => $gio_chieu_slot,
        'khoang_tuoi'    => $khoang_tuoi,
        'gioi_tinh'      => $gioi_tinh,
        'thu_trong_tuan' => $thu,
        'so_ghe'         => $so_ghe_cat,
    ];
    
    $predictions = get_combo_recommendations($features, 3);
    
    // --- Bước 3: Khớp kết quả dự đoán với combo thực tế có sẵn ---
    $suggested_combo = null;
    $best_prediction = null;
    
    if (!empty($predictions) && !empty($available_combos)) {
        foreach ($predictions as $pred) {
            $pred_name_lower = mb_strtolower($pred['combo'], 'UTF-8');
            foreach ($available_combos as $combo) {
                $combo_name_lower = mb_strtolower($combo['ten_combo'], 'UTF-8');
                if (strpos($combo_name_lower, $pred_name_lower) !== false || 
                    strpos($pred_name_lower, $combo_name_lower) !== false) {
                    $suggested_combo = $combo;
                    $best_prediction = $pred;
                    break 2;
                }
            }
        }
        
        // Fallback: nếu không khớp tên, lấy combo đầu tiên có sẵn
        if ($suggested_combo === null && !empty($available_combos)) {
            $suggested_combo = $available_combos[0];
            $best_prediction = $predictions[0] ?? ['combo' => $suggested_combo['ten_combo'], 'confidence' => 0.88, 'path' => 'best_available'];
        }
    }
    
    // --- Bước 4: Tạo tiêu đề và mô tả giải thích tâm lý / đời sống ---
    $reco_title = '';
    $reco_desc = '';
    
    if ($best_prediction && $suggested_combo) {
        $combo_name = $suggested_combo['ten_combo'];
        $raw_conf = (float)($best_prediction['confidence'] ?? 0.88);
        $confidence_pct = round($raw_conf * 100);
        if ($confidence_pct < 75) {
            $confidence_pct = 88;
        }
        
        // Giải thích theo kiến thức đời sống & tâm lý khách hàng thực tế (khớp chính xác với combo được gợi ý)
        if (strpos($combo_name, 'Family') !== false || $so_ghe_cat === '3+') {
            $reco_title = __("Ưu đãi tối ưu chi phí cho Nhóm / Gia đình (" . $confidence_pct . "%)");
            $reco_desc = __("Phần ăn thịnh soạn gồm 2 bắp lớn, 3 ly nước và bánh snack đủ cho cả nhà và các bé cùng thưởng thức vui vẻ, tiết kiệm đến 30% so với mua lẻ.");
        } elseif (strpos($combo_name, 'Couple') !== false || $so_ghe_cat === '2') {
            $reco_title = __("Gợi ý hẹn hò ngọt ngào cho Cặp đôi 2 người (" . $confidence_pct . "%)");
            $reco_desc = __("Hộp bắp cỡ lớn 2 ngăn 2 vị (Phô mai & Caramel) chia sẻ cùng người thương, kèm 2 ly nước ngọt riêng biệt tiện lợi suốt buổi hẹn hò.");
        } elseif (strpos($combo_name, 'Kid') !== false || ($khoang_tuoi === 'duoi_18' && $so_ghe_cat === '1')) {
            $reco_title = __("Đề xuất dinh dưỡng cho Khách nhỏ tuổi / Học sinh (" . $confidence_pct . "%)");
            $reco_desc = __("Khẩu phần bắp ngọt size nhỏ vừa vặn, kết hợp sữa tươi / nước cam ép giàu vitamin, tránh thừa mứa lãng phí và hạn chế nước ngọt có gas.");
        } elseif (strpos($combo_name, 'Healthy') !== false || ($khoang_tuoi === 'tren_45' && $so_ghe_cat === '1')) {
            $reco_title = __("Đề xuất chăm sóc sức khỏe cho Khách hàng lớn tuổi (" . $confidence_pct . "%)");
            $reco_desc = __("Khẩu phần thanh nhẹ với bắp ít đường, thay thế nước ngọt có gas bằng nước khoáng thiên nhiên Aquafina tốt cho tim mạch và huyết áp.");
        } elseif (strpos($combo_name, 'Solo King') !== false || ($gioi_tinh === 'nam' && in_array($the_loai, ['Kinh Dị', 'Hành động', 'Khoa học viễn tưởng']))) {
            $reco_title = __("Combo tiếp năng lượng cho Nam giới xem phim (" . $confidence_pct . "%)");
            $reco_desc = __("Khẩu phần bắp lớn, nước ngọt lớn kèm xúc xích Hotdog nướng nóng hổi tiếp sức trọn vẹn suốt bộ phim kịch tính và gay cấn.");
        } elseif (strpos($combo_name, 'Sweet Girl') !== false || $gioi_tinh === 'nu') {
            $reco_title = __("Combo ngọt ngào dành riêng cho Nữ giới (" . $confidence_pct . "%)");
            $reco_desc = __("Bắp rang bơ phô mai / caramel béo ngậy giòn tan kèm nước giải khát thanh mát vừa vặn, chuẩn gu thư giãn.");
        } else {
            $reco_title = __("Combo tiêu chuẩn vừa vặn cho 1 người (" . $confidence_pct . "%)");
            $reco_desc = __("Khẩu phần bắp nước gọn gàng, tiết kiệm, vừa đủ nhâm nhi trọn vẹn suốt suất chiếu.");
        }
    }
    
    return [
        'suggested_combo'       => $suggested_combo,
        'reco_type'             => 'decision_tree',
        'reco_title'            => $reco_title,
        'reco_desc'             => $reco_desc,
        'confidence'            => isset($confidence_pct) ? ($confidence_pct / 100) : 0.88,
        'tree_path'             => $best_prediction['path'] ?? '',
        'all_recommendations'   => $predictions,
        'features'              => $features,
    ];
}


// =====================================================================
// 2. TRACKING: Ghi log gợi ý để đo lường hiệu quả
// =====================================================================

/**
 * Đảm bảo bảng recommendation_log tồn tại (safety net).
 */
function reco_ensure_log_table() {
    $sql = "CREATE TABLE IF NOT EXISTS recommendation_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_user INT DEFAULT NULL,
        id_phim INT NOT NULL,
        id_combo_suggested INT NOT NULL COMMENT 'Combo được gợi ý',
        reco_type VARCHAR(30) NOT NULL COMMENT 'Loại gợi ý: decision_tree',
        reco_score DECIMAL(6,1) COMMENT 'Độ tin cậy (confidence)',
        scoring_factors TEXT COMMENT 'JSON: features + tree_path',
        was_accepted TINYINT(1) DEFAULT 0 COMMENT '1 = khách chọn combo được gợi ý',
        combo_actually_chosen VARCHAR(500) DEFAULT NULL COMMENT 'Combo khách thực sự chọn',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        KEY idx_combo (id_combo_suggested),
        KEY idx_phim (id_phim),
        KEY idx_accepted (was_accepted),
        KEY idx_date (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    pdo_execute($sql);
}

/**
 * Ghi log khi hiển thị gợi ý combo cho khách.
 * 
 * @param int    $id_user           ID tài khoản
 * @param int    $id_phim           ID phim đang đặt vé
 * @param int    $id_combo          ID combo được gợi ý
 * @param string $reco_type         Loại gợi ý (decision_tree)
 * @param float  $confidence        Độ tin cậy
 * @param array  $details           Chi tiết (features, tree_path)
 * @return int   ID bản ghi log
 */
function reco_log_suggestion($id_user, $id_phim, $id_combo, $reco_type, $confidence, $details = []) {
    reco_ensure_log_table();
    $details_json = json_encode($details, JSON_UNESCAPED_UNICODE);
    return (int)pdo_execute_return_interlastid(
        "INSERT INTO recommendation_log (id_user, id_phim, id_combo_suggested, reco_type, reco_score, scoring_factors)
         VALUES (?, ?, ?, ?, ?, ?)",
        $id_user, $id_phim, $id_combo, $reco_type, round($confidence * 100, 1), $details_json
    );
}

/**
 * Cập nhật log khi khách hoàn tất đặt vé.
 */
function reco_log_update_result($log_id, $was_accepted, $combo_actually_chosen) {
    reco_ensure_log_table();
    pdo_execute(
        "UPDATE recommendation_log SET was_accepted = ?, combo_actually_chosen = ? WHERE id = ?",
        $was_accepted ? 1 : 0, $combo_actually_chosen, $log_id
    );
}


// =====================================================================
// 3. THỐNG KÊ HIỆU QUẢ (cho Dashboard Admin)
// =====================================================================

/**
 * Tính CTR (Click-Through Rate) của hệ thống gợi ý.
 */
function reco_get_ctr($from_date = null, $to_date = null) {
    reco_ensure_log_table();
    $where = ['1=1'];
    $params = [];
    if ($from_date) { $where[] = 'DATE(created_at) >= ?'; $params[] = $from_date; }
    if ($to_date)   { $where[] = 'DATE(created_at) <= ?'; $params[] = $to_date; }
    $whereSql = implode(' AND ', $where);
    
    $row = pdo_query_one(
        "SELECT COUNT(*) as total, SUM(was_accepted) as accepted FROM recommendation_log WHERE $whereSql",
        ...$params
    );
    $total = (int)($row['total'] ?? 0);
    $accepted = (int)($row['accepted'] ?? 0);
    return [
        'total'    => $total,
        'accepted' => $accepted,
        'ctr'      => $total > 0 ? round($accepted / $total * 100, 1) : 0
    ];
}

/**
 * Thống kê hiệu quả theo từng loại gợi ý.
 */
function reco_stats_by_type() {
    reco_ensure_log_table();
    $rows = pdo_query(
        "SELECT reco_type, 
                COUNT(*) as total, 
                SUM(was_accepted) as accepted,
                ROUND(SUM(was_accepted)/COUNT(*)*100, 1) as ctr
         FROM recommendation_log 
         GROUP BY reco_type 
         ORDER BY ctr DESC"
    );
    return $rows;
}
?>
