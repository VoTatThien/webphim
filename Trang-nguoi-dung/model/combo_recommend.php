<?php
/**
 * ============================================================================
 * Model: Hybrid Recommendation Engine cho Combo Đồ Ăn
 * ============================================================================
 * 
 * Thuật toán: Kết hợp Content-Based Filtering + Item-Based Collaborative Filtering
 * 
 * Các phương pháp:
 *   1. Collaborative Filtering: Dựa trên lịch sử đặt vé (Phim→Combo, Profile→Combo)
 *   2. Popularity-Based: Combo được đặt nhiều nhất (toàn hệ thống / theo rạp)
 *   3. Context-Aware: Gợi ý theo ngữ cảnh (giờ chiếu, thể loại phim)
 * 
 * File: Trang-nguoi-dung/model/combo_recommend.php
 */

// =====================================================================
// 1. POPULARITY-BASED: Combo phổ biến nhất (toàn hệ thống)
// =====================================================================

/**
 * Lấy bản đồ popularity của từng combo dựa trên lịch sử bán hàng.
 * Trả về mảng ['Tên Combo' => số_lượt_đặt].
 * 
 * Thuật toán: Đếm tần suất xuất hiện của mỗi tên combo trong bảng `ve`,
 * chuẩn hóa tên (loại bỏ "x1", "x2"...) để gộp các biến thể.
 */
function get_combo_popularity_map() {
    $rows = pdo_query(
        "SELECT combo, COUNT(*) as cnt 
         FROM ve 
         WHERE combo != '' AND combo != '[]' AND combo IS NOT NULL
           AND trang_thai IN (1, 2, 4)
         GROUP BY combo 
         ORDER BY cnt DESC"
    );
    
    $popularity = [];
    foreach ($rows as $row) {
        // Tách chuỗi combo (VD: "Combo VIP, Combo Family" → ["Combo VIP", "Combo Family"])
        $names = array_map('trim', explode(',', $row['combo']));
        foreach ($names as $name) {
            // Chuẩn hóa: loại bỏ " x1", " x2"... ở cuối
            $clean = preg_replace('/\s*x\d+$/i', '', trim($name));
            if (empty($clean)) continue;
            $popularity[$clean] = ($popularity[$clean] ?? 0) + (int)$row['cnt'];
        }
    }
    
    return $popularity;
}


// =====================================================================
// 2. ITEM-BASED COLLABORATIVE FILTERING: Phim → Combo
// =====================================================================

/**
 * Combo phổ biến nhất cho một phim cụ thể.
 * Dựa trên nguyên lý: "Khách xem phim X thường chọn combo Y"
 * 
 * @param int $id_phim  ID phim đang đặt vé
 * @param int $limit    Số combo trả về tối đa
 * @return array        Mảng ['combo' => tên, 'frequency' => số lần]
 */
function get_popular_combo_by_movie($id_phim, $limit = 3) {
    $sql = "SELECT combo, COUNT(*) as frequency 
            FROM ve 
            WHERE id_phim = ? 
              AND combo != '' AND combo != '[]' AND combo IS NOT NULL
              AND trang_thai IN (1, 2, 4)
            GROUP BY combo 
            ORDER BY frequency DESC 
            LIMIT ?";
    return pdo_query($sql, $id_phim, $limit);
}


/**
 * Combo phổ biến nhất theo thể loại phim (Genre-Based CF).
 * Dựa trên nguyên lý: "Khách xem phim kinh dị thường chọn combo khác khách xem hài"
 * 
 * @param int $id_loai_phim  ID thể loại phim
 * @param int $limit         Số combo trả về tối đa
 * @return array
 */
function get_popular_combo_by_genre($id_loai_phim, $limit = 3) {
    $sql = "SELECT v.combo, COUNT(*) as frequency
            FROM ve v
            INNER JOIN phim p ON v.id_phim = p.id
            WHERE p.id_loai = ?
              AND v.combo != '' AND v.combo != '[]' AND v.combo IS NOT NULL
              AND v.trang_thai IN (1, 2, 4)
            GROUP BY v.combo
            ORDER BY frequency DESC
            LIMIT ?";
    return pdo_query($sql, $id_loai_phim, $limit);
}


// =====================================================================
// 3. CONTEXT-AWARE: Gợi ý theo ngữ cảnh (giờ chiếu)
// =====================================================================

/**
 * Combo phổ biến theo khung giờ chiếu.
 * Dựa trên nguyên lý: "Buổi sáng khách thích combo nhẹ, buổi tối thích combo đầy đủ"
 * 
 * @param int $hour_start  Giờ bắt đầu khung (VD: 18)
 * @param int $hour_end    Giờ kết thúc khung (VD: 23)
 * @param int $limit       Số combo trả về tối đa
 * @return array
 */
function get_popular_combo_by_timeslot($hour_start, $hour_end, $limit = 3) {
    $sql = "SELECT v.combo, COUNT(*) as frequency
            FROM ve v
            INNER JOIN khung_gio_chieu kg ON v.id_thoi_gian_chieu = kg.id
            WHERE HOUR(kg.thoi_gian_bat_dau) BETWEEN ? AND ?
              AND v.combo != '' AND v.combo != '[]' AND v.combo IS NOT NULL
              AND v.trang_thai IN (1, 2, 4)
            GROUP BY v.combo
            ORDER BY frequency DESC
            LIMIT ?";
    return pdo_query($sql, $hour_start, $hour_end, $limit);
}


// =====================================================================
// 4. USER-BASED CF: Gợi ý theo hồ sơ khách tương tự
// =====================================================================

/**
 * Combo phổ biến trong nhóm khách có hồ sơ tương tự (cùng giới tính, độ tuổi ±5).
 * Dựa trên nguyên lý User-Based Collaborative Filtering.
 * 
 * @param int    $tuoi       Tuổi khách hiện tại
 * @param string $gioi_tinh  'nam' hoặc 'nu'
 * @param int    $limit      Số combo trả về tối đa
 * @return array
 */
function get_popular_combo_by_profile($tuoi, $gioi_tinh, $limit = 3) {
    if ($tuoi === null || $gioi_tinh === null) return [];
    
    $tuoi_min = max(0, $tuoi - 5);
    $tuoi_max = $tuoi + 5;
    $sql = "SELECT v.combo, COUNT(*) as frequency
            FROM ve v
            INNER JOIN taikhoan tk ON v.id_tk = tk.id
            WHERE tk.gioi_tinh = ?
              AND TIMESTAMPDIFF(YEAR, tk.ngay_sinh, CURDATE()) BETWEEN ? AND ?
              AND v.combo != '' AND v.combo != '[]' AND v.combo IS NOT NULL
              AND v.trang_thai IN (1, 2, 4)
            GROUP BY v.combo
            ORDER BY frequency DESC
            LIMIT ?";
    return pdo_query($sql, $gioi_tinh, $tuoi_min, $tuoi_max, $limit);
}


// =====================================================================
// 5. TRACKING: Ghi log gợi ý để đo lường hiệu quả
// =====================================================================

/**
 * Đảm bảo bảng recommendation_log tồn tại (safety net cho dev).
 */
function reco_ensure_log_table() {
    $sql = "CREATE TABLE IF NOT EXISTS recommendation_log (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_user INT DEFAULT NULL,
        id_phim INT NOT NULL,
        id_combo_suggested INT NOT NULL COMMENT 'Combo được gợi ý',
        reco_type VARCHAR(30) NOT NULL COMMENT 'Loại gợi ý: family/couple/kid/sweet_girl/solo_king/healthy/solo',
        reco_score DECIMAL(6,1) COMMENT 'Tổng điểm scoring',
        scoring_factors TEXT COMMENT 'JSON chi tiết từng yếu tố scoring',
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
 * @param int    $id_user           ID tài khoản (0 nếu khách vãng lai)
 * @param int    $id_phim           ID phim đang đặt vé
 * @param int    $id_combo          ID combo được gợi ý
 * @param string $reco_type         Loại gợi ý (family/couple/kid...)
 * @param float  $reco_score        Tổng điểm scoring
 * @param array  $scoring_factors   Mảng chi tiết điểm từng yếu tố
 * @return int   ID bản ghi log (dùng để cập nhật sau)
 */
function reco_log_suggestion($id_user, $id_phim, $id_combo, $reco_type, $reco_score, $scoring_factors = []) {
    reco_ensure_log_table();
    $factors_json = json_encode($scoring_factors, JSON_UNESCAPED_UNICODE);
    pdo_execute(
        "INSERT INTO recommendation_log (id_user, id_phim, id_combo_suggested, reco_type, reco_score, scoring_factors)
         VALUES (?, ?, ?, ?, ?, ?)",
        $id_user, $id_phim, $id_combo, $reco_type, $reco_score, $factors_json
    );
    // Trả về ID vừa insert
    return pdo_query_value("SELECT LAST_INSERT_ID()");
}

/**
 * Cập nhật log khi khách hoàn tất đặt vé (biết được khách chọn combo nào).
 * 
 * @param int    $log_id                ID bản ghi log
 * @param bool   $was_accepted          Khách có chọn combo gợi ý không
 * @param string $combo_actually_chosen Combo khách thực sự chọn
 */
function reco_log_update_result($log_id, $was_accepted, $combo_actually_chosen) {
    reco_ensure_log_table();
    pdo_execute(
        "UPDATE recommendation_log SET was_accepted = ?, combo_actually_chosen = ? WHERE id = ?",
        $was_accepted ? 1 : 0, $combo_actually_chosen, $log_id
    );
}


// =====================================================================
// 6. THỐNG KÊ HIỆU QUẢ (cho Dashboard Admin)
// =====================================================================

/**
 * Tính CTR (Click-Through Rate) của hệ thống gợi ý.
 * CTR = Số lần khách chọn combo gợi ý / Tổng lần hiện gợi ý
 * 
 * @param string|null $from_date  Ngày bắt đầu (YYYY-MM-DD)
 * @param string|null $to_date    Ngày kết thúc (YYYY-MM-DD)
 * @return array ['total' => int, 'accepted' => int, 'ctr' => float]
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
 * Thống kê hiệu quả theo từng loại gợi ý (reco_type).
 * Giúp biết loại nào hiệu quả nhất.
 * 
 * @return array Mảng ['reco_type' => ..., 'total' => ..., 'accepted' => ..., 'ctr' => ...]
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
