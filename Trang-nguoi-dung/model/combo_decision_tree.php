<?php
/**
 * Decision Tree Classifier cho Gợi ý Combo Đồ Ăn
 * Thuật toán: ID3 (Iterative Dichotomiser 3) với Information Gain
 * File: Trang-nguoi-dung/model/combo_decision_tree.php
 */

// ============================================================
// 1. TOÁN HỌC: Entropy & Information Gain
// ============================================================

/**
 * Tính Entropy của tập dữ liệu
 * H(S) = -Σ p(x) * log2(p(x))
 */
function dt_entropy($data, $label_key = 'combo_chon') {
    if (empty($data)) return 0;
    
    $counts = [];
    foreach ($data as $row) {
        $label = isset($row[$label_key]) ? $row[$label_key] : 'unknown';
        if (!isset($counts[$label])) $counts[$label] = 0;
        $counts[$label]++;
    }
    
    $entropy = 0;
    $total = count($data);
    foreach ($counts as $count) {
        $p = $count / $total;
        if ($p > 0) {
            $entropy -= $p * log($p, 2);
        }
    }
    
    return $entropy;
}

/**
 * Tính Information Gain khi chia dữ liệu theo feature
 * IG(S, A) = H(S) - Σ (|Sv|/|S|) * H(Sv)
 */
function dt_information_gain($data, $feature, $label_key = 'combo_chon') {
    $base_entropy = dt_entropy($data, $label_key);
    
    $subsets = [];
    foreach ($data as $row) {
        $val = isset($row[$feature]) ? $row[$feature] : 'unknown';
        if (!isset($subsets[$val])) $subsets[$val] = [];
        $subsets[$val][] = $row;
    }
    
    $total = count($data);
    $subset_entropy = 0;
    
    foreach ($subsets as $subset) {
        $p = count($subset) / $total;
        $subset_entropy += $p * dt_entropy($subset, $label_key);
    }
    
    return $base_entropy - $subset_entropy;
}

// ============================================================
// 2. XÂY DỰNG CÂY QUYẾT ĐỊNH (BUILD TREE)
// ============================================================

/**
 * Tìm label xuất hiện nhiều nhất (majority vote)
 */
function dt_majority_label($data, $label_key = 'combo_chon') {
    $counts = [];
    foreach ($data as $row) {
        $label = isset($row[$label_key]) ? $row[$label_key] : 'unknown';
        if (!isset($counts[$label])) $counts[$label] = 0;
        $counts[$label]++;
    }
    
    if (empty($counts)) {
        return ['label' => 'unknown', 'confidence' => 0, 'samples' => 0, 'counts' => []];
    }
    
    arsort($counts);
    $majority = array_key_first($counts);
    $total = count($data);
    
    return [
        'label' => $majority,
        'confidence' => $total > 0 ? $counts[$majority] / $total : 0,
        'samples' => $total,
        'counts' => $counts
    ];
}

/**
 * Xây dựng cây quyết định đệ quy (ID3)
 * @param array $data - Dữ liệu huấn luyện
 * @param array $features - Danh sách features còn lại
 * @param int $max_depth - Độ sâu tối đa (tránh overfitting)
 * @param int $min_samples - Số mẫu tối thiểu tại node (tránh overfitting)
 * @return array - Node cây quyết định
 */
function dt_build_tree($data, $features, $label_key = 'combo_chon', $max_depth = 10, $min_samples = 5, $depth = 0) {
    $majority_info = dt_majority_label($data, $label_key);
    
    // Điều kiện dừng:
    // 1. Dữ liệu rỗng
    // 2. Độ sâu đạt tối đa
    // 3. Số lượng mẫu nhỏ hơn tối thiểu
    // 4. Hết feature để chia
    // 5. Tất cả mẫu cùng 1 label (confidence = 1)
    if (empty($data) || $depth >= $max_depth || $majority_info['samples'] < $min_samples || empty($features) || $majority_info['confidence'] == 1) {
        return [
            'type' => 'leaf',
            'label' => $majority_info['label'],
            'confidence' => $majority_info['confidence'],
            'samples' => $majority_info['samples'],
            'counts' => $majority_info['counts']
        ];
    }
    
    // Tìm feature tốt nhất (Gain cao nhất)
    $best_gain = -1;
    $best_feature = null;
    
    foreach ($features as $feature) {
        $gain = dt_information_gain($data, $feature, $label_key);
        if ($gain > $best_gain) {
            $best_gain = $gain;
            $best_feature = $feature;
        }
    }
    
    // Nếu gain <= 0, không có sự cải thiện nào -> trở thành node lá
    if ($best_gain <= 0 || $best_feature === null) {
        return [
            'type' => 'leaf',
            'label' => $majority_info['label'],
            'confidence' => $majority_info['confidence'],
            'samples' => $majority_info['samples'],
            'counts' => $majority_info['counts']
        ];
    }
    
    // Chia nhánh
    $node = [
        'type' => 'decision',
        'feature' => $best_feature,
        'children' => [],
        'default' => $majority_info['label'],
        'default_confidence' => $majority_info['confidence'],
        'default_counts' => $majority_info['counts']
    ];
    
    // Loại feature đã chọn khỏi danh sách
    $remaining_features = array_diff($features, [$best_feature]);
    
    // Phân nhóm dữ liệu theo giá trị của feature tốt nhất
    $subsets = [];
    foreach ($data as $row) {
        $val = isset($row[$best_feature]) ? $row[$best_feature] : 'unknown';
        if (!isset($subsets[$val])) $subsets[$val] = [];
        $subsets[$val][] = $row;
    }
    
    // Đệ quy xây dựng các node con
    foreach ($subsets as $val => $subset) {
        $node['children'][$val] = dt_build_tree($subset, $remaining_features, $label_key, $max_depth, $min_samples, $depth + 1);
    }
    
    return $node;
}

// ============================================================
// 3. DỰ ĐOÁN (PREDICT)
// ============================================================

/**
 * Dự đoán combo cho 1 mẫu dữ liệu
 * @param array $tree - Cây quyết định
 * @param array $sample - Mẫu cần dự đoán ['the_loai' => 'Hài', 'gio_chieu' => 'toi', ...]
 * @return array ['combo' => 'Combo Family', 'confidence' => 0.85, 'path' => 'so_ghe=3+ -> the_loai=Hài']
 */
function dt_predict($tree, $sample, $path = "") {
    if (!isset($tree['type'])) {
        return ['combo' => 'Combo Standard', 'confidence' => 0.5, 'path' => 'error', 'counts' => []];
    }
    
    if ($tree['type'] === 'leaf') {
        return [
            'combo' => isset($tree['label']) ? $tree['label'] : 'Combo Standard',
            'confidence' => isset($tree['confidence']) ? $tree['confidence'] : 0.5,
            'path' => $path,
            'counts' => isset($tree['counts']) ? $tree['counts'] : []
        ];
    }
    
    $feature = isset($tree['feature']) ? $tree['feature'] : '';
    $val = isset($sample[$feature]) ? $sample[$feature] : 'unknown';
    
    if (isset($tree['children'][$val])) {
        $new_path = empty($path) ? "$feature=$val" : "$path -> $feature=$val";
        return dt_predict($tree['children'][$val], $sample, $new_path);
    } else {
        // Fallback nhánh không tồn tại trong tập train
        $new_path = empty($path) ? "fallback($feature=$val)" : "$path -> fallback($feature=$val)";
        return [
            'combo' => isset($tree['default']) ? $tree['default'] : 'Combo Standard',
            'confidence' => isset($tree['default_confidence']) ? $tree['default_confidence'] : 0.5,
            'path' => $new_path,
            'counts' => isset($tree['default_counts']) ? $tree['default_counts'] : []
        ];
    }
}

/**
 * Dự đoán Top N combo (duyệt nhiều nhánh của cây)
 */
function dt_predict_top_n($tree, $sample, $n = 3) {
    // Dự đoán nhánh chính
    $primary_pred = dt_predict($tree, $sample);
    
    $combo_scores = [];
    if (!empty($primary_pred['counts'])) {
        $total = array_sum($primary_pred['counts']);
        if ($total > 0) {
            foreach ($primary_pred['counts'] as $combo => $count) {
                $combo_scores[$combo] = $count / $total;
            }
        }
    }
    
    // Explore sibling branches if primary confidence is low (< 0.7) and we are at a decision node
    if (isset($primary_pred['confidence']) && $primary_pred['confidence'] < 0.7 && isset($tree['type']) && $tree['type'] === 'decision') {
        $feature = $tree['feature'];
        $val = isset($sample[$feature]) ? $sample[$feature] : 'unknown';
        
        foreach ($tree['children'] as $child_val => $child_node) {
            if ($child_val !== $val) {
                // Khám phá nhánh anh em, trọng số penalty = 0.3
                $sibling_pred = dt_predict($child_node, $sample);
                if (!empty($sibling_pred['counts'])) {
                    $total = array_sum($sibling_pred['counts']);
                    if ($total > 0) {
                        foreach ($sibling_pred['counts'] as $combo => $count) {
                            if (!isset($combo_scores[$combo])) {
                                $combo_scores[$combo] = 0;
                            }
                            $combo_scores[$combo] += ($count / $total) * 0.3;
                        }
                    }
                }
            }
        }
    }
    
    if (empty($combo_scores) && isset($primary_pred['combo'])) {
        $combo_scores = [$primary_pred['combo'] => $primary_pred['confidence']];
    }
    
    arsort($combo_scores);
    
    $results = [];
    $count = 0;
    foreach ($combo_scores as $combo => $score) {
        if ($count >= $n) break;
        $conf = min(0.99, $score); // Cap confidence
        $results[] = [
            'combo' => $combo,
            'confidence' => round($conf, 2),
            'path' => isset($primary_pred['path']) ? $primary_pred['path'] : ''
        ];
        $count++;
    }
    
    return $results;
}

// ============================================================
// 4. TRAIN & SAVE MODEL
// ============================================================

/**
 * Train model từ dữ liệu trong bảng combo_training_data
 * @return array ['success' => bool, 'accuracy' => float, 'message' => string]
 */
function dt_train_model() {
    // Kiểm tra hàm pdo_query có tồn tại không
    if (!function_exists('pdo_query')) {
        return ['success' => false, 'accuracy' => 0, 'message' => 'Lỗi: Hệ thống chưa nạp thư viện PDO'];
    }

    try {
        // 1. Load dữ liệu từ DB
        $data = pdo_query("SELECT the_loai, gio_chieu, khoang_tuoi, gioi_tinh, thu_trong_tuan, so_ghe, combo_chon FROM combo_training_data");
        
        if (empty($data) || count($data) < 10) {
            return ['success' => false, 'accuracy' => 0, 'message' => 'Không đủ dữ liệu huấn luyện (cần ít nhất 10 mẫu)'];
        }
        
        // 2. Chia train/test (80/20)
        shuffle($data);
        $split = (int)(count($data) * 0.8);
        $train_data = array_slice($data, 0, $split);
        $test_data = array_slice($data, $split);
        
        // 3. Build tree
        $features = ['the_loai', 'gio_chieu', 'khoang_tuoi', 'gioi_tinh', 'thu_trong_tuan', 'so_ghe'];
        $tree = dt_build_tree($train_data, $features);
        
        // 4. Tính accuracy trên test set
        $correct = 0;
        foreach ($test_data as $sample) {
            $prediction = dt_predict($tree, $sample);
            if (isset($prediction['combo']) && isset($sample['combo_chon']) && $prediction['combo'] === $sample['combo_chon']) {
                $correct++;
            }
        }
        $accuracy = count($test_data) > 0 ? round($correct / count($test_data) * 100, 2) : 0;
        
        // 5. Re-train on full data for production
        $tree = dt_build_tree($data, $features);
        
        // 6. Save to DB
        $model_json = json_encode($tree, JSON_UNESCAPED_UNICODE);
        $features_json = json_encode($features);
        
        // Upsert model vào database
        if (function_exists('pdo_execute')) {
            pdo_execute(
                "INSERT INTO decision_tree_model (model_name, model_data, accuracy, training_samples, features_used) 
                 VALUES ('combo_recommender', ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE model_data = VALUES(model_data), accuracy = VALUES(accuracy), 
                 training_samples = VALUES(training_samples), features_used = VALUES(features_used), trained_at = CURRENT_TIMESTAMP",
                $model_json, $accuracy, count($data), $features_json
            );
            return ['success' => true, 'accuracy' => $accuracy, 'message' => "Train thành công! Accuracy: {$accuracy}% trên " . count($test_data) . " mẫu test"];
        } else {
             return ['success' => false, 'accuracy' => 0, 'message' => 'Lỗi: Hàm pdo_execute không tồn tại'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'accuracy' => 0, 'message' => 'Lỗi ngoại lệ khi huấn luyện mô hình: ' . $e->getMessage()];
    }
}

/**
 * Load model đã train từ DB
 * @return array|null Cây quyết định hoặc null nếu chưa train
 */
function dt_load_model() {
    if (!function_exists('pdo_query_one')) return null;
    
    try {
        $row = pdo_query_one("SELECT model_data FROM decision_tree_model WHERE model_name = 'combo_recommender'");
        if ($row && !empty($row['model_data'])) {
            return json_decode($row['model_data'], true);
        }
    } catch (Exception $e) {
        // Lỗi database
        return null;
    }
    return null;
}

// ============================================================
// 5. HÀM CHÍNH: GỢI Ý COMBO (Main API)
// ============================================================

/**
 * Gợi ý combo cho khách hàng dựa trên Decision Tree
 * 
 * @param array $features - Đặc trưng khách hàng:
 *   'the_loai'       => string (tên thể loại phim)
 *   'gio_chieu'      => string ('sang'/'chieu'/'toi')
 *   'khoang_tuoi'    => string ('duoi_18'/'18_25'/'26_35'/'36_45'/'tren_45')
 *   'gioi_tinh'      => string ('nam'/'nu'/'khac')
 *   'thu_trong_tuan'  => string ('weekday'/'weekend')
 *   'so_ghe'         => string ('1'/'2'/'3+')
 * @param int $top_n - Số lượng combo gợi ý
 * @return array
 */
function get_combo_recommendations($features, $top_n = 3) {
    $tree = dt_load_model();
    
    if ($tree === null) {
        // Fallback: nếu chưa train hoặc lỗi db, trả về combo phổ biến nhất
        return [['combo' => 'Combo Standard', 'confidence' => 0.5, 'path' => 'fallback_no_model']];
    }
    
    return dt_predict_top_n($tree, $features, $top_n);
}
