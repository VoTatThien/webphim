<?php include "view/search.php"; ?>

<style>
    .container {
        width: 80%;
        margin: 0 auto;
    }

    h1 {
        text-align: center;
    }

    .prodoan {
        display: flex;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .prodo {
        width: 23%;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .prodo:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transform: translateY(-5px);
    }
    
    .prodo img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .combo-badge {
        display: inline-block;
        background: #4a3e43;
        border: 1px solid #ffd564;
        color: #ffd564;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 11px;
        margin-top: 5px;
    }

    .check_do_an {
        background-color: #dc3545;
        color: white;
        padding: 8px 15px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .check_do_an:hover {
        background-color: #c82333;
    }
    
    .btn--success {
        background-color: #28a745 !important;
    }
    
    .btn--success:hover {
        background-color: #218838 !important;
    }
    
    .no-combo-message {
        text-align: center;
        padding: 40px;
        color: #999;
        font-size: 18px;
    }
    
    
    /* Quantity control styles */
    .quantity-control {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 10px;
    }
    
    .quantity-btn {
        background: #ffd564;
        color: #4c4145;
        border: none;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .quantity-btn:hover {
        background: #ffe08d;
        transform: scale(1.1);
    }
    
    .quantity-btn:active {
        transform: scale(0.95);
    }
    
    .quantity-display {
        font-size: 20px;
        font-weight: bold;
        color: #fff;
        min-width: 30px;
        text-align: center;
    }
    
    .combo-selected-indicator {
        background: #28a745;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        margin-top: 10px;
        display: inline-block;
    }

    /* Premium Smart Recommendation Styles */
    .recommendation-section {
        background: linear-gradient(135deg, rgba(255, 213, 100, 0.08) 0%, rgba(254, 80, 90, 0.08) 100%);
        border: 1.5px dashed #ffd564;
        border-radius: 15px;
        padding: 20px 25px;
        margin: 25px auto 35px auto;
        max-width: 850px;
        display: flex;
        align-items: center;
        gap: 25px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        position: relative;
        overflow: hidden;
    }

    .recommendation-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #fe505a;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        animation: pulse_reco 2s infinite;
        border: 1px solid rgba(255,255,255,0.2);
    }

    @keyframes pulse_reco {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .recommendation-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 10px;
        border: 2px solid #ffd564;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .recommendation-info {
        flex-grow: 1;
        text-align: left;
    }

    .recommendation-info h2 {
        font-size: 20px;
        margin: 5px 0;
        color: #ffd564;
        font-weight: 800;
        text-transform: none;
        text-align: left;
    }

    .recommendation-info p {
        font-size: 13px;
        color: #ccc;
        margin: 0 0 10px 0;
        line-height: 1.4;
    }

    .recommendation-price {
        font-size: 20px;
        color: #fe505a;
        font-weight: bold;
        margin: 0;
    }

    .recommendation-control {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        min-width: 120px;
    }
</style>

<!-- Info Bar hiển thị thông tin đặt vé -->
<div class="booking-info-bar" style="background: #232023; border: 1px solid #4a3e43; padding: 20px; margin: 20px auto; max-width: 1200px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-around; align-items: center; color: white;">
        <div style="margin: 10px; text-align: center;">
            <i class="fa fa-film" style="font-size: 24px; color: #ffd564;"></i>
            <div style="margin-top: 5px;">
                <strong><?= __("Phim:") ?></strong><br>
                <?= isset($_SESSION['tong']['tieu_de']) ? htmlspecialchars(__($_SESSION['tong']['tieu_de'])) : 'N/A' ?>
            </div>
        </div>
        <div style="margin: 10px; text-align: center;">
            <i class="fa fa-building" style="font-size: 24px; color: #ffd564;"></i>
            <div style="margin-top: 5px;">
                <strong><?= __("Rạp:") ?></strong><br>
                <?= isset($_SESSION['tong']['ten_rap']) ? htmlspecialchars(__($_SESSION['tong']['ten_rap'])) : 'N/A' ?>
            </div>
        </div>
        <div style="margin: 10px; text-align: center;">
            <i class="fa fa-calendar" style="font-size: 24px; color: #ffd564;"></i>
            <div style="margin-top: 5px;">
                <strong><?= __("Ngày chiếu:") ?></strong><br>
                <?= isset($_SESSION['tong']['ngay_chieu']) ? htmlspecialchars($_SESSION['tong']['ngay_chieu']) : 'N/A' ?>
            </div>
        </div>
        <div style="margin: 10px; text-align: center;">
            <i class="fa fa-clock" style="font-size: 24px; color: #ffd564;"></i>
            <div style="margin-top: 5px;">
                <strong><?= __("Giờ chiếu:") ?></strong><br>
                <?= isset($_SESSION['tong']['thoi_gian_chieu']) ? htmlspecialchars($_SESSION['tong']['thoi_gian_chieu']) : 'N/A' ?>
            </div>
        </div>
    </div>
</div>

        <h1><?= __("Combo Đồ ăn") ?></h1>

        <?php
        // ====================================================================
        // HYBRID RECOMMENDATION ENGINE - GỢI Ý COMBO THÔNG MINH ĐA TIÊU CHÍ
        // ====================================================================
        // Thuật toán: Weighted Multi-Criteria Scoring
        // S(combo_i) = w1*F_keyword + w2*F_popularity + w3*F_genre + w4*F_time + w5*F_price + w6*F_cf
        // Phương pháp: Content-Based Filtering + Item-Based Collaborative Filtering
        // ====================================================================
        
        // Include model Collaborative Filtering & Tracking
        include_once __DIR__ . '/../model/combo_recommend.php';
        
        // --- BƯỚC 1: Thu thập dữ liệu đầu vào (Input Features) ---
        
        // 1a. Số ghế đã chọn
        $so_ghe = 1;
        if (isset($ten_ghe['ghe']) && is_array($ten_ghe['ghe'])) {
            $so_ghe = count($ten_ghe['ghe']);
        }
        
        // 1b. Truy cập thông tin Giới tính & Ngày sinh của Thành viên từ CSDL
        $user_id = $_SESSION['user']['id'] ?? 0;
        $ngay_sinh = null;
        $gioi_tinh = null;
        $tuoi = null;
        if ($user_id > 0) {
            $user_data = pdo_query_one("SELECT ngay_sinh, gioi_tinh FROM taikhoan WHERE id = ?", $user_id);
            if ($user_data) {
                $ngay_sinh = $user_data['ngay_sinh'];
                $gioi_tinh = $user_data['gioi_tinh'];
                if (!empty($ngay_sinh)) {
                    $birthDate = new DateTime($ngay_sinh);
                    $todayDate = new DateTime();
                    $tuoi = $todayDate->diff($birthDate)->y;
                }
            }
        }
        
        // 1c. Thông tin phim đang đặt (thể loại, ID)
        $id_phim = $_SESSION['tong']['id_phim'] ?? 0;
        $phim_info = pdo_query_one(
            "SELECT p.id_loai, lp.name as the_loai FROM phim p 
             LEFT JOIN loaiphim lp ON p.id_loai = lp.id 
             WHERE p.id = ?", $id_phim
        );
        $the_loai = mb_strtolower($phim_info['the_loai'] ?? '', 'UTF-8');
        $id_loai_phim = $phim_info['id_loai'] ?? 0;
        
        // 1d. Giờ chiếu → xác định khung thời gian
        $gio_chieu = $_SESSION['tong']['thoi_gian_chieu'] ?? '19:00';
        $hour = (int) explode(':', $gio_chieu)[0];
        $time_preference = 'normal';
        if ($hour < 12) $time_preference = 'light';       // Sáng → combo nhẹ
        elseif ($hour >= 18) $time_preference = 'full';    // Tối → combo đầy đủ
        
        // --- BƯỚC 2: Phân loại khách hàng (Rule-Based Classification) ---
        
        $reco_type = ""; 
        $reco_title = "";
        $reco_desc_label = "";
        
        if ($so_ghe >= 3) {
            $reco_type = 'family';
            $reco_title = __("Tiết kiệm cho nhóm (Family/Group)");
            $reco_desc_label = __("Đề xuất bắp nước cỡ lớn tối ưu nhất cho nhóm đi đông người.");
        } elseif ($so_ghe == 2) {
            $reco_type = 'couple';
            $reco_title = __("Ưu đãi cho cặp đôi (Couple)");
            $reco_desc_label = __("Combo 2 ly nước lớn kèm bắp ngọt ngào chia sẻ cùng người thương.");
        } else {
            if ($tuoi !== null && $tuoi < 18) {
                $reco_type = 'kid';
                $reco_title = __("Combo Trẻ em & Học sinh (Dưới 18 tuổi)");
                $reco_desc_label = __("Gợi ý combo bắp ngọt giòn tan kèm quà tặng/ly nước phim cực chất.");
            } elseif ($gioi_tinh === 'nu' && $tuoi !== null && $tuoi <= 30) {
                $reco_type = 'sweet_girl';
                $reco_title = __("Ưu đãi ngọt ngào cho Nữ giới (" . $tuoi . " tuổi)");
                $reco_desc_label = __("Gợi ý các combo bắp phô mai/caramel thơm béo, nước size vừa thích hợp.");
            } elseif ($gioi_tinh === 'nam' && $tuoi !== null && $tuoi <= 30) {
                $reco_type = 'solo_king';
                $reco_title = __("Combo Solo King cho Nam giới (" . $tuoi . " tuổi)");
                $reco_desc_label = __("Combo dung tích nước cực đại kèm bắp mặn nạp năng lượng suốt phim.");
            } elseif ($tuoi !== null && $tuoi > 45) {
                $reco_type = 'healthy';
                $reco_title = __("Combo Lành mạnh (Người lớn tuổi)");
                $reco_desc_label = __("Combo bắp ít ngọt, bổ sung nước tinh khiết/trà bảo vệ sức khỏe.");
            } else {
                $reco_type = 'solo';
                $reco_title = __("Gợi ý cho 1 người (Solo)");
                $reco_desc_label = __("Combo đơn tiện lợi vừa vặn thưởng thức trọn vẹn bộ phim.");
            }
        }
        
        // --- BƯỚC 3: Thu thập dữ liệu Collaborative Filtering ---
        
        // 3a. Popularity Map - tần suất mua combo toàn hệ thống
        $popularity_map = get_combo_popularity_map();
        $max_pop = max(1, !empty($popularity_map) ? max($popularity_map) : 1);
        
        // 3b. CF theo phim: combo phổ biến nhất cho phim đang đặt
        $cf_movie_combos = get_popular_combo_by_movie($id_phim);
        $cf_movie_names = [];
        foreach ($cf_movie_combos as $cfc) {
            $names = array_map('trim', explode(',', $cfc['combo']));
            foreach ($names as $n) {
                $cf_movie_names[] = preg_replace('/\s*x\d+$/i', '', $n);
            }
        }
        
        // 3c. CF theo thể loại phim
        $cf_genre_combos = get_popular_combo_by_genre($id_loai_phim);
        $cf_genre_names = [];
        foreach ($cf_genre_combos as $cfc) {
            $names = array_map('trim', explode(',', $cfc['combo']));
            foreach ($names as $n) {
                $cf_genre_names[] = preg_replace('/\s*x\d+$/i', '', $n);
            }
        }
        
        // 3d. Ma trận Genre → Keyword (Content-Based)
        $genre_combo_keywords = [
            'hài'       => ['family', 'nhóm', 'group', 'lớn', 'premium'],
            'ngôn tình' => ['couple', 'đôi', 'ngọt', 'caramel', 'premium'],
            'kinh dị'   => ['premium', 'vip', 'mặn', 'pepsi', 'coke'],
            'hoạt hình' => ['kid', 'trẻ em', 'ngọt', 'standard'],
            'ca nhạc'   => ['premium', 'vip', 'lớn'],
            'cổ trang'  => ['standard', 'đơn', 'premium'],
        ];
        $genre_keywords = $genre_combo_keywords[$the_loai] ?? [];
        
        // --- BƯỚC 4: WEIGHTED MULTI-CRITERIA SCORING ---
        
        $suggested_combo = null;
        $best_match_score = -1;
        $best_scoring_detail = [];
        
        if (isset($combos) && is_array($combos) && count($combos) > 0) {
            foreach ($combos as $combo) {
                $name_lower = mb_strtolower($combo['ten_combo'], 'UTF-8');
                $desc_lower = mb_strtolower($combo['mo_ta'] ?? '', 'UTF-8');
                $combo_price = (float)($combo['gia'] ?? 0);
                
                // ---- F1: Keyword Match (Content-Based) → max +10 ----
                $f_keyword = 0;
                if ($reco_type === 'family') {
                    if (strpos($name_lower, 'family') !== false || strpos($name_lower, 'gia đình') !== false || strpos($name_lower, 'nhóm') !== false || strpos($name_lower, 'group') !== false || strpos($name_lower, 'big') !== false) {
                        $f_keyword = 10;
                    }
                } elseif ($reco_type === 'couple') {
                    if (strpos($name_lower, 'couple') !== false || strpos($name_lower, 'đôi') !== false || strpos($name_lower, 'hai') !== false) {
                        $f_keyword = 10;
                    }
                } elseif ($reco_type === 'kid') {
                    if (strpos($name_lower, 'kid') !== false || strpos($name_lower, 'trẻ em') !== false || strpos($name_lower, 'đồ chơi') !== false || strpos($name_lower, 'toy') !== false) {
                        $f_keyword = 10;
                    }
                } elseif ($reco_type === 'sweet_girl') {
                    if (strpos($name_lower, 'ngọt') !== false || strpos($name_lower, 'caramel') !== false || strpos($name_lower, 'phô mai') !== false || strpos($name_lower, 'cheese') !== false) {
                        $f_keyword = 10;
                    }
                } elseif ($reco_type === 'solo_king') {
                    if (strpos($name_lower, 'lớn') !== false || strpos($name_lower, 'mặn') !== false || strpos($name_lower, 'king') !== false || strpos($name_lower, 'coke') !== false || strpos($name_lower, 'pepsi') !== false) {
                        $f_keyword = 10;
                    }
                } elseif ($reco_type === 'healthy') {
                    if (strpos($name_lower, 'nước lọc') !== false || strpos($name_lower, 'suối') !== false || strpos($name_lower, 'healthy') !== false || strpos($name_lower, 'mặn') !== false) {
                        $f_keyword = 10;
                    }
                } else {
                    if (strpos($name_lower, 'solo') !== false || strpos($name_lower, 'đơn') !== false || strpos($name_lower, 'cá nhân') !== false) {
                        $f_keyword = 10;
                    }
                }
                
                // ---- F2: Popularity Score → max +7 ----
                $pop_count = $popularity_map[$combo['ten_combo']] ?? 0;
                $f_popularity = round(($pop_count / $max_pop) * 7, 1);
                
                // ---- F3: Genre Match (Content-Based + CF) → max +5 ----
                $f_genre = 0;
                foreach ($genre_keywords as $gk) {
                    if (strpos($name_lower, $gk) !== false || strpos($desc_lower, $gk) !== false) {
                        $f_genre = 3;
                        break;
                    }
                }
                if (in_array($combo['ten_combo'], $cf_genre_names)) {
                    $f_genre = min(5, $f_genre + 3);
                }
                
                // ---- F4: Time Context → max +3 ----
                $f_time = 0;
                if ($time_preference === 'light' && $combo_price < 70000) {
                    $f_time = 3;
                } elseif ($time_preference === 'full' && $combo_price >= 80000) {
                    $f_time = 3;
                } elseif ($time_preference === 'normal') {
                    $f_time = 1;
                }
                
                // ---- F5: Price Fitness → max +4 ----
                $budget_per_person = 80000;
                $total_budget = $budget_per_person * $so_ghe;
                $price_ratio = $combo_price / max($total_budget, 1);
                $f_price = 0;
                if ($price_ratio >= 0.3 && $price_ratio <= 1.2) {
                    $f_price = 4;
                } elseif ($price_ratio < 0.3) {
                    $f_price = 1;
                }
                
                // ---- F6: Collaborative Filtering (Phim → Combo) → max +6 ----
                $f_cf = 0;
                if (in_array($combo['ten_combo'], $cf_movie_names)) {
                    $f_cf = 6;
                }
                
                // ---- TỔNG ĐIỂM (Weighted Sum) ----
                $total_score = $f_keyword + $f_popularity + $f_genre + $f_time + $f_price + $f_cf;
                
                if ($total_score > $best_match_score) {
                    $best_match_score = $total_score;
                    $suggested_combo = $combo;
                    $best_scoring_detail = [
                        'F1_keyword'    => $f_keyword,
                        'F2_popularity' => $f_popularity,
                        'F3_genre'      => $f_genre,
                        'F4_time'       => $f_time,
                        'F5_price'      => $f_price,
                        'F6_cf_movie'   => $f_cf,
                        'total'         => $total_score
                    ];
                }
            }
            
            // Fallback nếu tất cả combo đều score = 0
            if (($suggested_combo === null || $best_match_score == 0) && !empty($combos)) {
                $temp_combos = $combos;
                if ($so_ghe == 1) {
                    if ($reco_type === 'kid') {
                        usort($temp_combos, function($a, $b) {
                            return ($a['gia'] ?? 0) - ($b['gia'] ?? 0);
                        });
                        $suggested_combo = $temp_combos[0];
                    } elseif ($reco_type === 'healthy' || $reco_type === 'sweet_girl') {
                        $mid = (int)(count($temp_combos) / 2);
                        $suggested_combo = $temp_combos[$mid];
                    } else {
                        usort($temp_combos, function($a, $b) {
                            return ($b['gia'] ?? 0) - ($a['gia'] ?? 0);
                        });
                        $suggested_combo = $temp_combos[0];
                    }
                } elseif ($so_ghe == 2) {
                    $mid = (int)(count($temp_combos) / 2);
                    $suggested_combo = $temp_combos[$mid];
                } else {
                    usort($temp_combos, function($a, $b) {
                        return ($b['gia'] ?? 0) - ($a['gia'] ?? 0);
                    });
                    $suggested_combo = $temp_combos[0];
                }
                $best_scoring_detail = ['fallback' => true, 'reco_type' => $reco_type];
            }
        }
        
        // --- BƯỚC 5: Ghi log Recommendation (Tracking) ---
        $reco_log_id = 0;
        if ($suggested_combo) {
            try {
                $reco_log_id = reco_log_suggestion(
                    $user_id,
                    $id_phim,
                    $suggested_combo['id'] ?? 0,
                    $reco_type,
                    $best_match_score,
                    $best_scoring_detail
                );
                $_SESSION['reco_log_id'] = $reco_log_id;
                $_SESSION['reco_combo_name'] = $suggested_combo['ten_combo'] ?? '';
            } catch (Exception $e) {
                $reco_log_id = 0;
            }
        }

        
        if ($suggested_combo):
        ?>
        <div class="recommendation-section">
            <div class="recommendation-badge"><i class="fa fa-fire"></i> <?= __("Khuyên dùng") ?></div>
            <img class="recommendation-img" src="<?= !empty($suggested_combo['hinh_anh']) ? $suggested_combo['hinh_anh'] : 'imgavt/combo1.png' ?>" alt="<?= htmlspecialchars($suggested_combo['ten_combo']) ?>">
            <div class="recommendation-info">
                <span style="font-size: 11px; color: #ffd564; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; display: inline-block; margin-bottom: 3px;">
                    <i class="fa fa-thumbs-up"></i> 
                    <?= htmlspecialchars($reco_title) ?>
                </span>
                <h2><?= htmlspecialchars($suggested_combo['ten_combo']) ?></h2>
                <p><?= htmlspecialchars($reco_desc_label) ?><br/><span style="color:#aaa; font-style:italic; font-size:11px;"><?= htmlspecialchars($suggested_combo['mo_ta']) ?></span></p>
                <p class="recommendation-price"><?= number_format($suggested_combo['gia'] ?? 0, 0, ',', '.') ?>đ</p>
            </div>
            <div class="recommendation-control">
                <span style="font-size: 12px; color: #aaa;"><?= __("Số lượng:") ?></span>
                <div class="quantity-control suggested-control" 
                     data-combo-id="<?= $suggested_combo['id'] ?? $suggested_combo['id_combo'] ?>" 
                     data-combo-name="<?= htmlspecialchars($suggested_combo['ten_combo'] ?? $suggested_combo['ten'] ?? '') ?>"
                     data-combo-price="<?= $suggested_combo['gia'] ?? 0 ?>">
                    <button type="button" class="quantity-btn btn-suggested-decrease">−</button>
                    <span class="quantity-display suggested-qty">0</span>
                    <button type="button" class="quantity-btn btn-suggested-increase">+</button>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($combos) && count($combos) > 0): ?>
        <div class="prodoan">
            <?php foreach ($combos as $combo): ?>
            <div class="prodo">
                <img src="<?= !empty($combo['hinh_anh']) ? $combo['hinh_anh'] : 'imgavt/combo1.png' ?>" alt="<?= htmlspecialchars($combo['ten_combo']) ?>">
                <h3><?= htmlspecialchars($combo['ten_combo']) ?></h3>
                <p><?= htmlspecialchars($combo['mo_ta']) ?></p>
                
                <?php if ($combo['id_rap'] !== null): ?>
                    <span class="combo-badge"><i class="fa fa-building-o"></i> <?= __("Combo riêng của rạp") ?></span>
                <?php else: ?>
                    <span class="combo-badge"><i class="fa fa-star-o"></i> <?= __("Combo toàn hệ thống") ?></span>
                <?php endif; ?>
                
                <p style="font-size: 20px; color: #dc3545; font-weight: bold; margin-top: 10px;">
                    <?= __("Giá") ?>: <?= number_format($combo['gia'] ?? 0, 0, ',', '.') ?>đ
                </p>
                
                <!-- Quantity control -->
                <div class="quantity-control" data-combo-id="<?= $combo['id'] ?? $combo['id_combo'] ?>" 
                     data-combo-name="<?= htmlspecialchars($combo['ten_combo'] ?? $combo['ten'] ?? '') ?>"
                     data-combo-price="<?= $combo['gia'] ?? 0 ?>">
                    <button type="button" class="quantity-btn btn-decrease">−</button>
                    <span class="quantity-display">0</span>
                    <button type="button" class="quantity-btn btn-increase">+</button>
                </div>
                
                <div class="combo-selected-indicator" style="display: none;">
                    <?= __("Đã chọn:") ?> <span class="selected-count">0</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <div class="no-combo-message">
                <i class="fa fa-info-circle" style="font-size: 50px; color: #ddd;"></i>
                <p><?= __("Hiện tại không có combo đồ ăn nào khả dụng cho rạp này.") ?></p>
            </div>
        <?php endif; ?>

    </section>
</div>

<form action="index.php?act=dv4" method="post">
    <div class="col-lg-offset-1">
        <div class="tong">
            <h2 class="phim" style="color: #ffd564;"><?= __("Thông tin đặt vé") ?></h2>
            
            <div style="display: flex; margin-bottom: 10px; align-items: center;">
                <span style="color: #fff;"><i class="fa fa-circle-o" style="color: #ffd564; margin-right: 5px;"></i> <?= __("Ghế đã chọn:") ?></span>
                <div class="checked-place" style="margin-left: 10px;">
                    <?php
                    if (isset($ten_ghe['ghe'])) {
                        $ghes = $ten_ghe['ghe'];
                        echo '<span class="choosen-place" style="background:#ffd564; color:#4c4145; font-weight:bold;">' . implode(', ', $ghes) . '</span>';

                        // Tạo các hidden input cho mỗi ghế
                        foreach ($ghes as $ghe) {
                            echo '<input type="hidden" name="ten_ghe[]" value="' . htmlspecialchars($ghe) . '">';
                        }
                    }
                    ?>
                </div>
            </div>
            
            <div style="display: flex; margin-bottom: 10px; align-items: center;">
                <span style="color: #fff;"><i class="fa fa-coffee" style="color: #ffd564; margin-right: 5px;"></i> <?= __("Combo đã chọn:") ?></span>
                <div class="check-doan" id="selected-combos-display" style="margin-left: 10px;">
                    <!-- Combos will be dynamically added here -->
                </div>
            </div>

            <div class="tongtien">
                <div class="checked-result">
                    <span style="color: #fff;"><?= __("Tổng cộng:") ?></span>
                    <input name="giaghe" style="width: 120px; font-size: 20px; border: none; background: transparent; color: #fe505a; font-weight: bold;" type="text" id="gia_ghe"
                           value="<?php 
                           // Get seat price from session
                           $seat_price = $_SESSION['tong']['gia_ghe'] ?? 0;
                           echo $seat_price; 
                           ?>" readonly> VND
                </div>
            </div>
        </div>
    </div>

    <div class="booking-pagination">
        <a href="index.php?act=datve2&id=<?php echo $_SESSION['tong']['id_phim'] ?>">
            <span class="quaylai"><?= __("QUAY LẠI") ?></span>
        </a>
        <a href="#">
            <input type="submit" name="tiep_tuc" class="booking-pagination__button" value="<?= __("TIẾP TỤC") ?>" style="background:#ffd564; color:#4c4145;">
        </a>
    </div>
</form>

<div class="clearfix"></div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        // Initialize total price from seat selection
        var priceElement = document.getElementById('gia_ghe');
        var seatPrice = parseInt(priceElement?.value || 0);
        var comboTotal = 0;
        
        // Object to store combo quantities
        var comboQuantities = {};
        
        // Function to update total price display
        function updateTotalPrice() {
            var totalPrice = seatPrice + comboTotal;
            $('#gia_ghe').val(totalPrice);
            $('[name="giaghe"]').val(totalPrice);
        }
        
        // Function to update combo display
        function updateComboDisplay() {
            var displayHtml = '';
            var hasCombo = false;
            
            // Clear existing hidden inputs
            $('#selected-combos-display').empty();
            
            // Build display and hidden inputs
            $.each(comboQuantities, function(comboName, data) {
                if (data.quantity > 0) {
                    hasCombo = true;
                    displayHtml += '<span class="choosen-place" style="margin: 5px; padding: 5px 10px; background: #ffd564; color: #4c4145; font-weight: bold; border-radius: 15px; display: inline-block;">' + 
                                  comboName + ' x' + data.quantity + 
                                  '</span>';
                    
                    // Add multiple hidden inputs (one for each quantity)
                    for (var i = 0; i < data.quantity; i++) {
                        $('#selected-combos-display').append(
                            '<input type="hidden" name="ten_do_an[]" value="' + comboName + '">'
                        );
                    }
                }
            });
            
            if (!hasCombo) {
                displayHtml = '<span style="color: #999; font-style: italic;">' + '<?= __("Chưa chọn combo nào") ?>' + '</span>';
            }
            
            $('#selected-combos-display').html($('#selected-combos-display').html() + displayHtml);
        }
        
        // Handle increase button
        $('.btn-increase').on('click', function(e) {
            e.preventDefault();
            
            var container = $(this).closest('.quantity-control');
            var comboName = container.data('combo-name');
            var comboPrice = parseInt(container.data('combo-price')) || 0;
            var quantityDisplay = container.find('.quantity-display');
            var currentQty = parseInt(quantityDisplay.text()) || 0;
            var indicator = container.siblings('.combo-selected-indicator');
            
            // Increase quantity
            currentQty++;
            quantityDisplay.text(currentQty);
            
            // Update indicator
            indicator.find('.selected-count').text(currentQty);
            indicator.show();
            
            // Update combo quantities object
            if (!comboQuantities[comboName]) {
                comboQuantities[comboName] = {
                    price: comboPrice,
                    quantity: 0
                };
            }
            comboQuantities[comboName].quantity = currentQty;
            
            // Update combo total
            comboTotal += comboPrice;
            
            // Update displays
            updateTotalPrice();
            updateComboDisplay();
            
            // Sync with Suggested box if it exists
            var sugContainer = $('.suggested-control[data-combo-id="' + container.data('combo-id') + '"]');
            if (sugContainer.length > 0) {
                sugContainer.find('.quantity-display').text(currentQty);
            }
            
            // Visual feedback
            $(this).css('transform', 'scale(1.2)');
            setTimeout(() => {
                $(this).css('transform', 'scale(1)');
            }, 200);
        });
        
        // Handle decrease button
        $('.btn-decrease').on('click', function(e) {
            e.preventDefault();
            
            var container = $(this).closest('.quantity-control');
            var comboName = container.data('combo-name');
            var comboPrice = parseInt(container.data('combo-price')) || 0;
            var quantityDisplay = container.find('.quantity-display');
            var currentQty = parseInt(quantityDisplay.text()) || 0;
            var indicator = container.siblings('.combo-selected-indicator');
            
            // Decrease quantity (minimum 0)
            if (currentQty > 0) {
                currentQty--;
                quantityDisplay.text(currentQty);
                
                // Update indicator
                if (currentQty > 0) {
                    indicator.find('.selected-count').text(currentQty);
                } else {
                    indicator.hide();
                }
                
                // Update combo quantities object
                if (comboQuantities[comboName]) {
                    comboQuantities[comboName].quantity = currentQty;
                }
                
                // Update combo total
                comboTotal -= comboPrice;
                
                // Update displays
                updateTotalPrice();
                updateComboDisplay();
                
                // Sync with Suggested box if it exists
                var sugContainer = $('.suggested-control[data-combo-id="' + container.data('combo-id') + '"]');
                if (sugContainer.length > 0) {
                    sugContainer.find('.quantity-display').text(currentQty);
                }
                
                // Visual feedback
                $(this).css('transform', 'scale(1.2)');
                setTimeout(() => {
                    $(this).css('transform', 'scale(1)');
                }, 200);
            }
        });

        // Handle Suggested Increase Button
        $('.btn-suggested-increase').on('click', function(e) {
            e.preventDefault();
            var container = $(this).closest('.quantity-control');
            var comboId = container.data('combo-id');
            
            // Trigger click on the main catalog equivalent increase button
            var mainContainer = $('.quantity-control[data-combo-id="' + comboId + '"]').not('.suggested-control');
            if (mainContainer.length > 0) {
                mainContainer.find('.btn-increase').trigger('click');
                var newQty = mainContainer.find('.quantity-display').text();
                container.find('.quantity-display').text(newQty);
            }
        });

        // Handle Suggested Decrease Button
        $('.btn-suggested-decrease').on('click', function(e) {
            e.preventDefault();
            var container = $(this).closest('.quantity-control');
            var comboId = container.data('combo-id');
            
            // Trigger click on the main catalog equivalent decrease button
            var mainContainer = $('.quantity-control[data-combo-id="' + comboId + '"]').not('.suggested-control');
            if (mainContainer.length > 0) {
                mainContainer.find('.btn-decrease').trigger('click');
                var newQty = mainContainer.find('.quantity-display').text();
                container.find('.quantity-display').text(newQty);
            }
        });
        
        // Prevent form submission if no seats selected
        $('form').on('submit', function(e) {
            var seatInputs = $('input[name="ten_ghe[]"]');
            if (seatInputs.length === 0) {
                e.preventDefault();
                alert('<?= __("Vui lòng chọn ghế trước khi tiếp tục!") ?>');
                return false;
            }
        });
    });
</script>
