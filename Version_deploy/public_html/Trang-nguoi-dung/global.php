<?php

// Set timezone to Vietnam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Ensure the seat-map helpers are available (they live under Trang-admin/model)
// path: ../Trang-admin/model/phong_ghe.php relative to this file
@include_once __DIR__ . '/../Trang-admin/model/phong_ghe.php';

$id_kgc = $_SESSION['tong']['id_gio'] ?? 0;
$id_lc = $_SESSION['tong']['id_lichchieu'] ?? 0;
$id_phim = $_SESSION['tong']['id_phim'] ?? 0;

// Debug session data for seat locking
if (isset($_GET['debug'])) {
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
    echo "<h4>Debug Session Data:</h4>";
    echo "<p>Current Time Slot ID: $id_kgc</p>";
    echo "<p>Schedule ID: $id_lc</p>";
    echo "<p>Movie ID: $id_phim</p>";
    echo "<p>Session mv: " . print_r($_SESSION['mv'] ?? 'Not set', true) . "</p>";
    echo "</div>";
}

// Get room pricing info
$room_pricing = [];
if ($id_kgc) {
    $kg = pdo_query_one("SELECT id_phong FROM khung_gio_chieu WHERE id = ?", $id_kgc);
    if ($kg) {
        $room_info = pdo_query_one("SELECT gia_thuong, gia_trung, gia_vip FROM phongchieu WHERE id = ?", $kg['id_phong']);
        if ($room_info) {
            $room_pricing = [
                'cheap' => (int)$room_info['gia_thuong'],
                'middle' => (int)$room_info['gia_trung'], 
                'expensive' => (int)$room_info['gia_vip']
            ];
        }
    }
}

// Fallback prices if no room pricing found
if (empty($room_pricing)) {
    $room_pricing = ['cheap' => 60000, 'middle' => 80000, 'expensive' => 100000];
}

// Try to load an explicit seat-map for the room tied to the selected time.
// If none exists, fall back to the old static $ghes array.
$seat_map = [];
if ($id_kgc) {
    $seat_map = pg_list_for_time($id_kgc);
}

// Build $ghes-like structure from $seat_map when available
if (!empty($seat_map)) {
    $ghes = [];
    foreach ($seat_map as $s) {
        $r = $s['row_label'];
        $n = (int)$s['seat_number'];
        // Use actual price from room pricing
        $price = $room_pricing[$s['tier']] ?? $room_pricing['cheap'];
        $ghes[$r][] = [
            $n, 
            $price, 
            'code' => $s['code'], 
            'active' => (int)$s['active'],
            'tier' => $s['tier'] // <-- PRESERVE ORIGINAL SEAT TIER FROM DATABASE
        ];
    }
    // Ensure rows are ordered by label
    ksort($ghes);
} else {
    // legacy static layout (keeps previous behaviour)
    $ghes = [
        'A' => [[1, $room_pricing['cheap']], [2, $room_pricing['cheap']], [3, $room_pricing['cheap']], [4, $room_pricing['cheap']], [5, $room_pricing['cheap']], [6, $room_pricing['cheap']], [7, $room_pricing['cheap']],[8,$room_pricing['cheap']],[9,$room_pricing['cheap']]],
        'B' => [[1, $room_pricing['cheap']], [2, $room_pricing['cheap']], [3, $room_pricing['cheap']], [4, $room_pricing['cheap']], [5, $room_pricing['cheap']], [6, $room_pricing['cheap']], [7, $room_pricing['cheap']],[8,$room_pricing['cheap']],[9,$room_pricing['cheap']]],
        'C' => [[1, $room_pricing['cheap']], [2, $room_pricing['cheap']], [3, $room_pricing['cheap']], [4, $room_pricing['cheap']], [5, $room_pricing['cheap']], [6, $room_pricing['cheap']], [7, $room_pricing['cheap']],[8,$room_pricing['cheap']],[9,$room_pricing['cheap']]],
        'D' => [[1, $room_pricing['cheap']], [2, $room_pricing['middle']], [3, $room_pricing['middle']], [4, $room_pricing['middle']], [5, $room_pricing['middle']], [6, $room_pricing['middle']], [7, $room_pricing['middle']],[8,$room_pricing['middle']],[9,$room_pricing['cheap']]],
        'E' => [[1, $room_pricing['cheap']], [2, $room_pricing['middle']], [3, $room_pricing['middle']], [4, $room_pricing['middle']], [5, $room_pricing['middle']], [6, $room_pricing['middle']], [7, $room_pricing['middle']],[8,$room_pricing['middle']],[9,$room_pricing['cheap']]],
        'F' => [[1, $room_pricing['cheap']], [2, $room_pricing['middle']], [3, $room_pricing['middle']], [4, $room_pricing['middle']], [5, $room_pricing['middle']], [6, $room_pricing['middle']], [7, $room_pricing['middle']],[8,$room_pricing['middle']],[9,$room_pricing['cheap']]],
        'G' => [[1, $room_pricing['cheap']], [2, $room_pricing['expensive']], [3, $room_pricing['expensive']], [4, $room_pricing['expensive']], [5, $room_pricing['expensive']], [6, $room_pricing['expensive']], [7, $room_pricing['expensive']],[8,$room_pricing['expensive']],[9,$room_pricing['cheap']]],
        'H' => [[1, $room_pricing['cheap']], [2, $room_pricing['expensive']], [3, $room_pricing['expensive']], [4, $room_pricing['expensive']], [5, $room_pricing['expensive']], [6, $room_pricing['expensive']], [7, $room_pricing['expensive']],[8,$room_pricing['expensive']],[9,$room_pricing['cheap']]],
    ];
}

// ==========================================
// DYNAMIC PRICING IMPLEMENTATION
// ==========================================
$show_date = date('Y-m-d');
if ($id_lc) {
    $lc_info = pdo_query_one("SELECT ngay_chieu FROM lichchieu WHERE id = ?", $id_lc);
    if ($lc_info) {
        $show_date = $lc_info['ngay_chieu'];
    }
}
$day_num = date('N', strtotime($show_date));
$is_weekend = ($day_num >= 5); // Fri, Sat, Sun
$days_diff = (strtotime($show_date) - time()) / 86400;
$is_early_bird = ($days_diff >= 3);

if (!function_exists('calculate_dynamic_price')) {
    function calculate_dynamic_price($base_price, $tier, $is_weekend, $is_early_bird) {
        $price = $base_price;
        // VIP seat (+15%)
        if ($tier === 'expensive') {
            $price = $price * 1.15;
        } 
        // Double seat (+30%)
        elseif ($tier === 'double') {
            $price = $price * 1.30;
        }
        
        // Weekend (+10%)
        if ($is_weekend) {
            $price = $price * 1.10;
        }
        
        // Early bird (-15%)
        if ($is_early_bird) {
            $price = $price * 0.85;
        }
        
        // Round to nearest 1,000 VND
        return round($price, -3);
    }
}

// Adjust all seat prices in $ghes according to dynamic pricing
foreach ($ghes as $r => $seats) {
    foreach ($seats as $idx => $seat) {
        $price = $seat[1];
        
        // If tier is already stored (from database), use it. Otherwise infer it.
        if (is_array($seat) && isset($seat['tier'])) {
            $tier = $seat['tier'];
        } else {
            $tier = 'cheap';
            if ($price == $room_pricing['middle']) {
                $tier = 'middle';
            } elseif ($price == $room_pricing['expensive']) {
                $tier = 'expensive';
            }
            // Row H is double seats in our fallback
            if ($r === 'H') {
                $tier = 'double';
            }
        }
        
        $ghes[$r][$idx][1] = calculate_dynamic_price($price, $tier, $is_weekend, $is_early_bird);
        $ghes[$r][$idx]['tier'] = $tier;
    }
}


$khoa_ghe = khoa_ghe($id_kgc, $id_lc, $id_phim);
if (isset($khoa_ghe) && $khoa_ghe != array()) {
    $khoa_ghe_ = [];
    foreach ($khoa_ghe as $sub_array) {
        $khoa_ghe_ = array_merge($khoa_ghe_, $sub_array);
    }
    $khoa_ghe__ = implode(',', $khoa_ghe_);
    $khoa_ghe_all = explode(',', $khoa_ghe__);
} else {
    $khoa_ghe_all = array();
}

// Fetch unresolved broken seats for this room from su_co table
$broken_seats = [];
if ($id_kgc) {
    $kg = pdo_query_one("SELECT id_phong FROM khung_gio_chieu WHERE id = ?", $id_kgc);
    if ($kg && isset($kg['id_phong'])) {
        $id_phong = (int)$kg['id_phong'];
        try {
            $incidents = pdo_query("SELECT vi_tri FROM su_co WHERE id_phong = ? AND loai_su_co = 'ghe_hong' AND trang_thai != 'da_khac_phuc'", $id_phong);
            if ($incidents) {
                foreach ($incidents as $inc) {
                    if (!empty($inc['vi_tri'])) {
                        $seats = array_map('strtoupper', array_map('trim', explode(',', $inc['vi_tri'])));
                        foreach ($seats as $seat) {
                            $broken_seats[] = $seat;
                        }
                    }
                }
            }
        } catch (Exception $e) {
            // Table might not exist or connection failed, ignore
        }
    }
}
?>

<!-- Main content -->
<div class="place-form-area">
    <section class="container">
        <div class="order-container">
        </div>
        <div class="order-step-area">
            <div class="order-step first--step order-step--disable "><?= __("1.   Lịch Chiếu &amp; Thời Gian") ?></div>
            <div class="order-step second--step"><?= __("2. Chọn ghế ") ?></div>
        </div>

        <div class="choose-sits">
            <div class="choose-sits__info choose-sits__info--first">
                <ul>
                    <li class="sits-price marker--none"><strong><?= __("Giá ") ?></strong></li>
                    <li class="sits-price sits-price--cheap"><?= number_format(calculate_dynamic_price($room_pricing['cheap'], 'cheap', $is_weekend, $is_early_bird)) ?> VNĐ</li>
                    <li class="sits-price sits-price--middle"><?= number_format(calculate_dynamic_price($room_pricing['middle'], 'middle', $is_weekend, $is_early_bird)) ?> VNĐ</li>
                    <li class="sits-price sits-price--expensive"><?= number_format(calculate_dynamic_price($room_pricing['expensive'], 'expensive', $is_weekend, $is_early_bird)) ?> VNĐ</li>

                </ul>
            </div>

            <div class="choose-sits__info">
                <ul>
                    <li class="sits-state sits-state--not"><?= __(" Đã được chọn") ?></li>
                    <li class="sits-state sits-state--your"><?= __("Lựa chọn của bạn ") ?></li>
                </ul>
            </div>
            
            <!-- Dynamic Pricing Active Policies -->
            <div style="margin-bottom: 20px; font-size: 14px; color: #fff; background: rgba(255, 213, 100, 0.08); border-left: 4px solid #ffd564; padding: 12px 20px; border-radius: 6px; width: 100%; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div>
                    <i class="fa fa-info-circle" style="color: #ffd564; margin-right: 5px;"></i>
                    <strong><?= __("Chính sách giá vé linh hoạt:") ?></strong>
                    <?= $is_weekend ? '<span style="color: #ff758c; font-weight: bold;">' . __("Suất chiếu Cuối tuần (+10% áp dụng)") . '</span>' : '<span style="color: #43e97b;">' . __("Suất chiếu Ngày thường") . '</span>' ?>
                </div>
                <div>
                    <?= $is_early_bird ? '<span style="background: rgba(67, 233, 123, 0.2); color: #43e97b; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;"><i class="fa fa-percentage"></i> ' . __("Đặt sớm >3 ngày: Giảm 15% tổng tiền") . '</span>' : '<span style="background: rgba(255,255,255,0.1); color: #aaa; padding: 3px 8px; border-radius: 4px; font-size: 12px;">' . __("Đặt cận ngày: Giá tiêu chuẩn") . '</span>' ?>
                </div>
            </div>

            <!-- Khung tính năng mở rộng: Đặt vé nhóm & Gợi ý ghế -->
            <div class="booking-enhancements-panel" style="display: flex; flex-wrap: wrap; align-items: center; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; gap: 15px; width: 100%;">
                <!-- Đặt vé nhóm -->
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-weight: 600; color: #fff; font-size: 15px;"><i class="fa fa-users" style="color: #ffd564; margin-right: 5px;"></i> <?= __("Chế độ đặt vé nhóm:") ?></span>
                    <label class="switch-toggle" style="position: relative; display: inline-block; width: 50px; height: 26px; margin: 0; cursor: pointer;">
                        <input type="checkbox" id="group-booking-checkbox" style="opacity: 0; width: 0; height: 0;">
                        <span class="slider-toggle" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: #4b4b4b; transition: .4s; border-radius: 34px;"></span>
                    </label>
                    <button type="button" id="create-group-link-btn" class="btn btn-sm btn--warning" style="display: none; padding: 5px 12px; border-radius: 6px; font-size: 13px; font-weight: 600;"><i class="fa fa-link"></i> <?= __("Tạo link nhóm") ?></button>
                </div>
                
                <!-- Gợi ý ghế thông minh -->
                <div style="display: flex; align-items: center; gap: 10px; margin-left: auto; flex-wrap: wrap;">
                    <span style="font-weight: 600; color: #fff; font-size: 15px;"><i class="fa fa-magic" style="color: #ffd564; margin-right: 5px;"></i> <?= __("Gợi ý ghế thông minh:") ?></span>
                    <div class="suggest-buttons" style="display: flex; gap: 8px;">
                        <button type="button" class="btn btn-sm btn-suggest-seat" data-type="couple" style="background: linear-gradient(135deg, #ff758c 0%, #ff7eb3 100%); color: white; padding: 6px 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: transform 0.2s;"><i class="fa fa-heart"></i> <?= __("Cặp đôi") ?></button>
                        <button type="button" class="btn btn-sm btn-suggest-seat" data-type="best" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 6px 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: transform 0.2s;"><i class="fa fa-eye"></i> <?= __("Xem rõ nhất") ?></button>
                        <button type="button" class="btn btn-sm btn-suggest-seat" data-type="saving" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 6px 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: transform 0.2s;"><i class="fa fa-tags"></i> <?= __("Tiết kiệm") ?></button>
                        <button type="button" class="btn btn-sm btn-suggest-seat" data-type="exit" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: white; padding: 6px 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: transform 0.2s;"><i class="fa fa-sign-out"></i> <?= __("Gần lối ra") ?></button>
                    </div>
                </div>
            </div>

            <style>
                .slider-toggle:before {
                    position: absolute;
                    content: "";
                    height: 18px;
                    width: 18px;
                    left: 4px;
                    bottom: 4px;
                    background-color: white;
                    transition: .4s;
                    border-radius: 50%;
                }
                #group-booking-checkbox:checked + .slider-toggle {
                    background-color: #ffd564;
                }
                #group-booking-checkbox:checked + .slider-toggle:before {
                    transform: translateX(24px);
                    background-color: #151216;
                }
                /* Show seat labels clearly inside the seats */
                .sits .sits__row .sits__place {
                    text-indent: 0 !important;
                    text-align: center;
                    font-size: 9px !important;
                    font-weight: bold;
                    color: #fff !important;
                    line-height: 30px !important;
                }
                .sits-state--broken {
                    color: #888 !important;
                }
                /* Premium seat and legend colors */
                .sits-price--cheap:before,
                .sits .sits__row .sits-price--cheap:before {
                    background-color: #ffd564 !important; /* Gold/yellow standard */
                }
                .sits-price--middle:before,
                .sits .sits__row .sits-price--middle:before {
                    background-color: #ff5e62 !important; /* Red/pink VIP */
                }
                .sits-price--expensive:before,
                .sits .sits__row .sits-price--expensive:before {
                    background-color: #c084fc !important; /* Royal purple expensive/double */
                }
                .sits-state--not:before,
                .sits .sits__row .sits-state--not:before {
                    background-color: #555555 !important; /* Muted grey for taken/disabled */
                }
                .sits-state--broken:before,
                .sits .sits__row .sits-state--broken:before {
                    background-color: #222222 !important; /* Dark grey for broken */
                    border: 1px dashed #ff5e62;
                }
                /* Hide checkmark icon to keep seat label fully visible & readable when selected */
                .sits .sits__row .sits-state--your:after {
                    display: none !important;
                }
            </style>

            <div class="ghe12">
                <div class=" col-lg-10 col-lg-offset-1">

                    <div class="sits-anchor"><?= __("Màn hình") ?></div>

                    <div class="sits">
                        <?php
                        // Get all row labels from $ghes
                        $row_labels = array_keys($ghes);
                        ?>
                        <aside class="sits__line">
                            <?php foreach ($row_labels as $row_label): ?>
                            <span class="sits__indecator"><?= htmlspecialchars($row_label) ?></span>
                            <?php endforeach; ?>
                        </aside> 
                        <aside class="sits__right">
                            <?php foreach ($row_labels as $row_label): ?>
                            <span class="sits__indecator"><?= htmlspecialchars($row_label) ?></span>
                            <?php endforeach; ?>
                        </aside>

                        <?php foreach ($ghes as $key => $value) : ?>
                            <div class="sits__row">
                                <?php foreach ($value as $o) : ?>
                                    <?php
                                    // support two shapes for $o:
                                    // legacy: [col, price]
                                    // new: [col, price, 'code'=>..., 'active'=>...]
                                    $col = is_array($o) ? ($o[0] ?? null) : null;
                                    $price = is_array($o) ? ($o[1] ?? $room_pricing['cheap']) : $room_pricing['cheap'];
                                    $code = is_array($o) && isset($o['code']) ? $o['code'] : ($key . $col);
                                    $active = is_array($o) && isset($o['active']) ? (int)$o['active'] : 1;
                                    $place = $code;
                                    $class = '';

                                    // First determine base class by price tier
                                    $tier = is_array($o) && isset($o['tier']) ? $o['tier'] : 'cheap';
                                    if ($tier === 'cheap') {
                                        $class = 'sits__place sits-price--cheap';
                                    } elseif ($tier === 'middle') {
                                        $class = 'sits__place sits-price--middle';
                                    } elseif ($tier === 'expensive' || $tier === 'double') {
                                        $class = 'sits__place sits-price--expensive';
                                    } else {
                                        $class = 'sits__place sits-price--cheap'; // fallback
                                    }
                                    
                                    // Then apply state classes
                                    $is_broken = in_array($place, $broken_seats);
                                    if (!$active) {
                                        $class .= ' sits-state--not'; // Ghế không hoạt động (admin tắt)
                                    } elseif (in_array($place, $khoa_ghe_all)) {
                                        $class .= ' sits-state--not'; // Ghế đã được đặt trong khung giờ này
                                    } elseif ($is_broken) {
                                        $class .= ' sits-state--not sits-state--broken'; // Ghế bị hỏng (khóa tự động)
                                    }
                                    ?>

                                    <span class="<?= $class ?>" data-place='<?= htmlspecialchars($place) ?>' data-price='<?= $price ?>' <?= $is_broken ? 'title="Ghế đang bảo trì/hỏng" style="background-color: #555 !important; color: #888; border: 1px dashed red; cursor: not-allowed;"' : '' ?>><?= htmlspecialchars($place) ?></span>

                                <?php endforeach ?>
                            </div>
                        <?php endforeach ?><div class="sits">



                            <?php
                            // Get max column number from all rows
                            $max_col = 0;
                            foreach ($ghes as $row) {
                                foreach ($row as $seat) {
                                    $col = is_array($seat) ? ($seat[0] ?? 0) : 0;
                                    if ($col > $max_col) $max_col = $col;
                                }
                            }
                            ?>
                            <footer class="sits__number">
                                <?php for ($i = 1; $i <= $max_col; $i++): ?>
                                <span class="sits__indecator"><?= $i ?></span>
                                <?php endfor; ?>
                            </footer>

                        </div>
                    </div></div>
            </div>

        </div>


</div>






