<?php
/**
 * ============================================================================
 * Script: Train Decision Tree Model cho Gợi ý Combo
 * ============================================================================
 * 
 * Chạy script này để huấn luyện (train) model Decision Tree từ dữ liệu
 * trong bảng combo_training_data. Model sau khi train sẽ được lưu vào
 * bảng decision_tree_model.
 * 
 * Cách sử dụng:
 *   1. Import DB/migration_decision_tree.sql (tạo bảng)
 *   2. Import DB/training_data_1000.sql (nạp dữ liệu huấn luyện)
 *   3. Chạy: php train_combo_model.php
 * 
 * File: Trang-nguoi-dung/train_combo_model.php
 */

// Kết nối CSDL
include_once __DIR__ . '/model/pdo.php';
include_once __DIR__ . '/model/combo_decision_tree.php';

echo "============================================\n";
echo "  TRAIN DECISION TREE - GỢI Ý COMBO ĐỒ ĂN  \n";
echo "============================================\n\n";

// Kiểm tra bảng training data có tồn tại không
try {
    $count = pdo_query_one("SELECT COUNT(*) as cnt FROM combo_training_data");
    $total = (int)($count['cnt'] ?? 0);
    echo "📊 Số lượng dữ liệu huấn luyện: {$total} dòng\n";
    
    if ($total < 10) {
        echo "❌ Không đủ dữ liệu! Cần ít nhất 10 mẫu.\n";
        echo "   Hãy import file DB/training_data_1000.sql trước.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Bảng combo_training_data chưa tồn tại!\n";
    echo "   Hãy import file DB/migration_decision_tree.sql trước.\n";
    echo "   Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}

// Hiển thị phân bố dữ liệu
echo "\n📈 Phân bố combo trong dữ liệu huấn luyện:\n";
$distribution = pdo_query(
    "SELECT combo_chon, COUNT(*) as cnt, 
     ROUND(COUNT(*)*100.0/(SELECT COUNT(*) FROM combo_training_data), 1) as pct 
     FROM combo_training_data GROUP BY combo_chon ORDER BY cnt DESC"
);
foreach ($distribution as $row) {
    $bar = str_repeat('█', (int)($row['pct'] / 2));
    echo "   {$row['combo_chon']}: {$row['cnt']} ({$row['pct']}%) {$bar}\n";
}

// Train model
echo "\n🔄 Đang huấn luyện Decision Tree...\n";
$start_time = microtime(true);

$result = dt_train_model();

$elapsed = round(microtime(true) - $start_time, 2);

if ($result['success']) {
    echo "✅ {$result['message']}\n";
    echo "⏱  Thời gian train: {$elapsed}s\n";
    
    // Test dự đoán với một số trường hợp mẫu
    echo "\n🧪 TEST DỰ ĐOÁN:\n";
    echo str_repeat('-', 70) . "\n";
    
    $test_cases = [
        [
            'desc' => 'Gia đình (3+ ghế) xem Hài buổi tối',
            'features' => ['the_loai' => 'Hài', 'gio_chieu' => 'toi', 'khoang_tuoi' => '26_35', 'gioi_tinh' => 'nam', 'thu_trong_tuan' => 'weekend', 'so_ghe' => '3+']
        ],
        [
            'desc' => 'Cặp đôi (2 ghế) xem Ngôn Tình buổi tối',
            'features' => ['the_loai' => 'Ngôn Tình', 'gio_chieu' => 'toi', 'khoang_tuoi' => '18_25', 'gioi_tinh' => 'nu', 'thu_trong_tuan' => 'weekend', 'so_ghe' => '2']
        ],
        [
            'desc' => 'Học sinh (<18) xem Hoạt Hình buổi sáng',
            'features' => ['the_loai' => 'Hoạt hình', 'gio_chieu' => 'sang', 'khoang_tuoi' => 'duoi_18', 'gioi_tinh' => 'nam', 'thu_trong_tuan' => 'weekday', 'so_ghe' => '1']
        ],
        [
            'desc' => 'Nam 20 tuổi xem Kinh Dị buổi tối',
            'features' => ['the_loai' => 'Kinh Dị', 'gio_chieu' => 'toi', 'khoang_tuoi' => '18_25', 'gioi_tinh' => 'nam', 'thu_trong_tuan' => 'weekend', 'so_ghe' => '1']
        ],
        [
            'desc' => 'Người lớn tuổi (>45) xem Cổ Trang buổi chiều',
            'features' => ['the_loai' => 'Cổ Trang', 'gio_chieu' => 'chieu', 'khoang_tuoi' => 'tren_45', 'gioi_tinh' => 'nam', 'thu_trong_tuan' => 'weekday', 'so_ghe' => '1']
        ],
        [
            'desc' => 'Nữ 22 tuổi xem Tình cảm cuối tuần',
            'features' => ['the_loai' => 'Tình cảm', 'gio_chieu' => 'toi', 'khoang_tuoi' => '18_25', 'gioi_tinh' => 'nu', 'thu_trong_tuan' => 'weekend', 'so_ghe' => '2']
        ],
    ];
    
    foreach ($test_cases as $tc) {
        $predictions = get_combo_recommendations($tc['features'], 3);
        echo "\n📌 {$tc['desc']}\n";
        foreach ($predictions as $i => $pred) {
            $rank = $i + 1;
            $conf = round($pred['confidence'] * 100, 1);
            echo "   Top {$rank}: {$pred['combo']} (Tin cậy: {$conf}%)\n";
        }
    }
    
    echo "\n" . str_repeat('=', 70) . "\n";
    echo "✅ Model đã sẵn sàng sử dụng!\n";
    echo "   Hệ thống sẽ tự động gợi ý combo khi khách đặt vé.\n";
    
} else {
    echo "❌ {$result['message']}\n";
    exit(1);
}
?>
