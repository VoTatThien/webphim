<?php include __DIR__ . '/../home/sideheader.php'; ?>

<?php
$user_id_rap = (int)($_SESSION['user1']['id_rap'] ?? 0);
$id_rap = $user_id_rap;

$ds_rap = [];
if ($user_id_rap <= 0) {
    $ds_rap = pdo_query("SELECT id, ten_rap FROM rap_chieu ORDER BY id ASC");
    // Get cinema ID from request if provided, otherwise default to first cinema
    if (isset($_POST['selected_id_rap'])) {
        $id_rap = (int)$_POST['selected_id_rap'];
    } elseif (isset($_GET['selected_id_rap'])) {
        $id_rap = (int)$_GET['selected_id_rap'];
    } elseif (!empty($ds_rap)) {
        $id_rap = (int)$ds_rap[0]['id'];
    }
}

$success_report = [];
$error_report = "";

// Check and load rooms for the resolved theater
if ($id_rap <= 0) {
    $error_report = __("Lỗi: Không tìm thấy rạp chiếu nào cấu hình trên hệ thống.");
} else {
    // Load active movies
    $ds_phim = pdo_query("SELECT id, tieu_de, thoi_luong_phim FROM phim WHERE trang_thai_duyet = 'da_duyet' ORDER BY id DESC");
    $ds_phong = pdo_query("SELECT id, name FROM phongchieu WHERE id_rap = ?", $id_rap);

    if (isset($_POST['run_autopilot'])) {
        $ngay_chieu = $_POST['ngay_chieu'] ?? '';
        $buffer = (int)($_POST['buffer'] ?? 20);
        $open_time = $_POST['open_time'] ?? '08:30';
        $close_time = $_POST['close_time'] ?? '23:30';
        $golden_start = $_POST['golden_start'] ?? '17:00';
        $golden_end = $_POST['golden_end'] ?? '21:30';
        $auto_approve = isset($_POST['auto_approve']) ? 'Đã duyệt' : 'Chờ duyệt';
        
        if (empty($ngay_chieu)) {
            $error_report = __("Vui lòng chọn ngày xếp lịch!");
        } elseif (empty($ds_phim)) {
            $error_report = __("Không có phim nào đã duyệt trên hệ thống để xếp lịch!");
        } elseif (empty($ds_phong)) {
            $error_report = __("Rạp của bạn không có phòng chiếu nào được cấu hình!");
        } else {
            // Priority/Hot movies to place in golden hours
            $priority_keywords = ['lật mặt', 'địa đạo', 'thỏ ơi', 'nhà ba tôi', 'wonka', 'marvels'];
            $hot_movies = [];
            $regular_movies = [];
            foreach ($ds_phim as $p) {
                $is_hot = false;
                foreach ($priority_keywords as $kw) {
                    if (strpos(strtolower($p['tieu_de']), $kw) !== false) {
                        $is_hot = true;
                        break;
                    }
                }
                if ($is_hot) {
                    $hot_movies[] = $p;
                } else {
                    $regular_movies[] = $p;
                }
            }
            
            // If no hot movies detected, just mix all
            if (empty($hot_movies)) {
                $hot_movies = $ds_phim;
            }
            if (empty($regular_movies)) {
                $regular_movies = $ds_phim;
            }
            
            $total_created = 0;
            $rooms_scheduled = 0;
            
            // Process room by room
            foreach ($ds_phong as $phong) {
                $rooms_scheduled++;
                $current_minutes = strtotime("$ngay_chieu $open_time");
                $limit_minutes = strtotime("$ngay_chieu $close_time");
                
                $movie_cycle_idx = rand(0, 10);
                
                while ($current_minutes < $limit_minutes) {
                    $current_time_str = date('H:i', $current_minutes);
                    
                    // Determine if current time falls into Golden Hours
                    $is_golden = (date('H:i', $current_minutes) >= $golden_start && date('H:i', $current_minutes) <= $golden_end);
                    
                    // Select movie
                    if ($is_golden) {
                        $m = $hot_movies[$movie_cycle_idx % count($hot_movies)];
                    } else {
                        $m = $regular_movies[$movie_cycle_idx % count($regular_movies)];
                    }
                    $movie_cycle_idx++;
                    
                    $duration = (int)$m['thoi_luong_phim'];
                    if ($duration <= 0) $duration = 120; // default duration
                    
                    // Check if movie fits in closing limit
                    if ($current_minutes + ($duration * 60) > $limit_minutes) {
                        break; // movie would run past closing time
                    }
                    
                    // 1. Check or Insert parent showtime record (lichchieu)
                    $check_lc = pdo_query_one(
                        "SELECT id FROM lichchieu WHERE id_phim = ? AND ngay_chieu = ? AND id_rap = ?",
                        $m['id'], $ngay_chieu, $id_rap
                    );
                    
                    if ($check_lc) {
                        $id_lc = $check_lc['id'];
                    } else {
                        $ma_kh = 'AP-' . date('Ymd') . '-' . rand(1000, 9999);
                        pdo_execute(
                            "INSERT INTO lichchieu (id_phim, ngay_chieu, id_rap, trang_thai, ma_ke_hoach) VALUES (?, ?, ?, ?, ?)",
                            $m['id'], $ngay_chieu, $id_rap, $auto_approve, $ma_kh
                        );
                        $id_lc = pdo_query_one("SELECT LAST_INSERT_ID() as id")['id'];
                    }
                    
                    // 2. Check or Insert individual slot (khung_gio_chieu)
                    $time_sql_format = date('H:i:s', $current_minutes);
                    $check_kgc = pdo_query_one(
                        "SELECT id FROM khung_gio_chieu WHERE id_phong = ? AND thoi_gian_chieu = ? AND id_lich_chieu IN (SELECT id FROM lichchieu WHERE ngay_chieu = ?)",
                        $phong['id'], $time_sql_format, $ngay_chieu
                    );
                    
                    if (!$check_kgc) {
                        pdo_execute(
                            "INSERT INTO khung_gio_chieu (id_lich_chieu, id_phong, thoi_gian_chieu) VALUES (?, ?, ?)",
                            $id_lc, $phong['id'], $time_sql_format
                        );
                        
                        $success_report[] = [
                            'room' => $phong['name'],
                            'time' => $current_time_str,
                            'movie' => $m['tieu_de'],
                            'duration' => $duration,
                            'type' => $is_golden ? 'golden' : 'standard'
                        ];
                        $total_created++;
                    }
                    
                    // Advance pointer: duration + buffer minutes
                    $current_minutes += ($duration + $buffer) * 60;
                }
            }
            
            $success_msg = "⚡ AI Autopilot: Đã lập lịch chiếu thành công cho $rooms_scheduled phòng, sinh ra $total_created suất chiếu mới!";
        }
    }
}
?>

<!-- Content Body Start -->
<div class="content-body" style="padding: 25px; color: #fff; font-family: 'Outfit', sans-serif; background: #151218; min-height: 100vh;">
    <!-- Page Headings Start -->
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-12 col-lg-auto mb-20">
            <div class="page-heading">
                <h3 style="font-size: 28px; font-weight: 800; background: linear-gradient(135deg, #13f1fc 0%, #0470dc 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    ⚡ AI AUTOPILOT - TỰ ĐỘNG SẮP XẾP LỊCH CHIẾU
                </h3>
                <span style="color: #8a8a8a; font-size: 14px;"><?= __("Xếp lịch chiếu thông minh dựa trên độ hot phim, giờ vàng và khoảng đệm dọn phòng") ?></span>
            </div>
        </div>
    </div>
    
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success" style="background: rgba(67, 233, 123, 0.1); border: 1px solid #43e97b; color: #43e97b; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <i class="fa fa-check-circle"></i> <?= $success_msg ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_report)): ?>
        <div class="alert alert-danger" style="background: rgba(255, 94, 98, 0.1); border: 1px solid #ff5e62; color: #ff5e62; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <i class="fa fa-exclamation-circle"></i> <?= $error_report ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Cấu hình Autopilot -->
        <div class="col-lg-4 col-12 mb-30">
            <div class="box" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 25px;">
                <div class="box-head" style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px; margin-bottom: 20px;">
                    <h4 style="font-size: 18px; font-weight: 700; color: #13f1fc; margin: 0;"><i class="fa fa-cogs"></i> <?= __("Tham Số Xếp Lịch") ?></h4>
                </div>
                <div class="box-body">
                    <form action="index.php?act=autopilot_showtimes" method="post">
                        <?php if ($user_id_rap <= 0): ?>
                            <!-- Chọn rạp chiếu -->
                            <div class="form-group mb-4">
                                <label style="color: #ccc; font-weight: 600; margin-bottom: 8px; display: block;"><?= __("Chọn rạp chiếu để xếp lịch:") ?></label>
                                <select name="selected_id_rap" class="form-control" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px;" onchange="window.location.href='index.php?act=autopilot_showtimes&selected_id_rap=' + this.value">
                                    <?php foreach ($ds_rap as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= $r['id'] == $id_rap ? 'selected' : '' ?>><?= htmlspecialchars($r['ten_rap']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- Chọn ngày xếp lịch -->
                        <div class="form-group mb-4">
                            <label style="color: #ccc; font-weight: 600; margin-bottom: 8px; display: block;"><?= __("Ngày xếp lịch chiếu:") ?></label>
                            <input type="date" name="ngay_chieu" class="form-control" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px;" required>
                        </div>
                        
                        <!-- Khoảng đệm dọn phòng -->
                        <div class="form-group mb-4">
                            <label style="color: #ccc; font-weight: 600; margin-bottom: 8px; display: block;"><?= __("Khoảng đệm dọn dẹp phòng (phút):") ?></label>
                            <input type="number" name="buffer" class="form-control" value="20" min="10" max="60" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px;" required>
                        </div>
                        
                        <!-- Giờ mở cửa & Đóng cửa -->
                        <div class="form-group mb-4" style="display: flex; gap: 15px;">
                            <div style="flex: 1;">
                                <label style="color: #ccc; font-size: 13px; margin-bottom: 6px; display: block;"><?= __("Giờ bắt đầu suất:") ?></label>
                                <input type="time" name="open_time" class="form-control" value="08:30" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px;">
                            </div>
                            <div style="flex: 1;">
                                <label style="color: #ccc; font-size: 13px; margin-bottom: 6px; display: block;"><?= __("Giờ đóng cửa rạp:") ?></label>
                                <input type="time" name="close_time" class="form-control" value="23:30" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px;">
                            </div>
                        </div>
                        
                        <!-- Khung giờ vàng -->
                        <div class="form-group mb-4" style="display: flex; gap: 15px;">
                            <div style="flex: 1;">
                                <label style="color: #ccc; font-size: 13px; margin-bottom: 6px; display: block;"><?= __("Bắt đầu giờ vàng:") ?></label>
                                <input type="time" name="golden_start" class="form-control" value="17:00" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px;">
                            </div>
                            <div style="flex: 1;">
                                <label style="color: #ccc; font-size: 13px; margin-bottom: 6px; display: block;"><?= __("Kết thúc giờ vàng:") ?></label>
                                <input type="time" name="golden_end" class="form-control" value="21:30" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px;">
                            </div>
                        </div>
                        
                        <!-- Tự động duyệt kế hoạch -->
                        <div class="form-group mb-4" style="display: flex; align-items: center; gap: 10px;">
                            <input type="checkbox" name="auto_approve" id="auto_approve" checked style="width: 18px; height: 18px; cursor: pointer;">
                            <label for="auto_approve" style="color: #ccc; font-weight: 600; margin: 0; cursor: pointer;"><?= __("Tự động duyệt kế hoạch (Đã duyệt)") ?></label>
                        </div>
                        
                        <button type="submit" name="run_autopilot" class="btn btn-warning w-100" style="background: linear-gradient(135deg, #13f1fc 0%, #0470dc 100%); color: #fff; font-weight: bold; border: none; padding: 12px; border-radius: 6px; cursor: pointer;">
                            <i class="fa fa-flash"></i> <?= __("KÍCH HOẠT AUTOPILOT 🚀") ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Kết quả xếp lịch -->
        <div class="col-lg-8 col-12 mb-30">
            <div class="box" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 25px;">
                <div class="box-head" style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px; margin-bottom: 20px;">
                    <h4 style="font-size: 18px; font-weight: 700; color: #43e97b; margin: 0;"><i class="fa fa-check-square-o"></i> <?= __("Chi Tiết Suất Chiếu Vừa Tạo") ?></h4>
                </div>
                
                <div class="box-body" style="max-height: 450px; overflow-y: auto;">
                    <?php if (empty($success_report)): ?>
                        <div style="text-align: center; color: #888; padding: 40px 0;">
                            <i class="fa fa-magic" style="font-size: 48px; color: #444; margin-bottom: 15px; display: block;"></i>
                            <?= __("Nhấn nút Kích Hoạt Autopilot để sinh lịch chiếu tự động.") ?>
                        </div>
                    <?php else: ?>
                        <div class="timeline" style="border-left: 2px solid rgba(255,255,255,0.1); padding-left: 20px; margin-left: 10px;">
                            <?php foreach ($success_report as $item): ?>
                                <div style="position: relative; margin-bottom: 20px;">
                                    <div style="position: absolute; left: -26px; top: 3px; width: 10px; height: 10px; border-radius: 50%; background: <?= $item['type'] === 'golden' ? '#ff9900' : '#13f1fc' ?>; box-shadow: 0 0 8px <?= $item['type'] === 'golden' ? '#ff9900' : '#13f1fc' ?>;"></div>
                                    <div style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px; padding: 12px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <strong style="color: #ffd564; font-size: 15px;"><?= $item['time'] ?></strong>
                                            <span style="font-size: 12px; background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($item['room']) ?></span>
                                        </div>
                                        <div style="font-size: 14px; color: #fff;">
                                            🎬 <?= htmlspecialchars($item['movie']) ?>
                                        </div>
                                        <div style="font-size: 12px; color: #aaa; margin-top: 5px; display: flex; justify-content: space-between;">
                                            <span>⏱️ <?= $item['duration'] ?> <?= __("phút") ?></span>
                                            <?= $item['type'] === 'golden' ? '<span style="color:#ff9900; font-weight:bold;">🔥 ' . __("Khung Giờ Vàng") . '</span>' : '<span>Standard Slot</span>' ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../home/footer.php'; ?>
