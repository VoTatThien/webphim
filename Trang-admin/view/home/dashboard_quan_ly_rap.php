<?php
// Dashboard cho Quản lý rạp
if (!isset($_SESSION['user1']) || $_SESSION['user1']['vai_tro'] != ROLE_QUAN_LY_RAP) {
    echo '<div class="alert alert-danger">Bạn không có quyền truy cập!</div>';
    return;
}

$id_rap = $_SESSION['user1']['id_rap'] ?? null;
if (!$id_rap) {
    echo '<div class="alert alert-warning">Không thể xác định rạp của bạn!</div>';
    return;
}

// Lấy thông tin rạp
$thong_tin_rap = pdo_query_one("SELECT * FROM rap_chieu WHERE id = ?", $id_rap);
$ten_rap = $thong_tin_rap['ten_rap'] ?? 'Rạp của tôi';

// Lấy ngày filter từ GET
$from_date = isset($_GET['from']) ? trim($_GET['from']) : date('Y-m-d', strtotime('-30 days'));
$to_date = isset($_GET['to']) ? trim($_GET['to']) : date('Y-m-d');
$id_phim_filter = isset($_GET['id_phim']) ? (int)$_GET['id_phim'] : 0;

// Lấy danh sách phim đang chiếu ở rạp này
$danh_sach_phim = pdo_query("
    SELECT DISTINCT p.id, p.tieu_de
    FROM phim p
    JOIN lichchieu lc ON p.id = lc.id_phim
    WHERE lc.id_rap = ?
    ORDER BY p.tieu_de
", $id_rap);

// Tổng doanh thu (không filter phim - tổng toàn rạp)
$tong = pdo_query_one("
    SELECT 
        COALESCE(SUM(v.price), 0) as tong_doanh_thu,
        COUNT(v.id) as tong_so_luong_ve_dat
    FROM ve v
    WHERE v.id_rap = ? AND v.trang_thai = 1 AND DATE(v.ngay_dat) >= ? AND DATE(v.ngay_dat) <= ?
", $id_rap, $from_date, $to_date);

// Phim đang chiếu
$phim_dang_chieu = pdo_query_one("
    SELECT COUNT(DISTINCT lc.id_phim) as total_phim
    FROM lichchieu lc 
    WHERE lc.id_rap = ? AND lc.ngay_chieu >= CURDATE()
", $id_rap);

// Phim sắp chiếu
$phim_sap_chieu = pdo_query_one("
    SELECT COUNT(DISTINCT lc.id_phim) as total_phim
    FROM lichchieu lc 
    WHERE lc.id_rap = ? AND lc.ngay_chieu > CURDATE() AND lc.ngay_chieu < DATE_ADD(CURDATE(), INTERVAL 30 DAY)
", $id_rap);

// Doanh thu hôm nay
$tong_day = pdo_query_one("
    SELECT COALESCE(SUM(v.price), 0) as tong_doanh_thu
    FROM ve v
    WHERE v.id_rap = ? AND DATE(v.ngay_dat) = CURDATE() AND v.trang_thai = 1
", $id_rap);

// Doanh thu tuần này
$tong_tuan = pdo_query_one("
    SELECT COALESCE(SUM(v.price), 0) as tong_doanh_thu
    FROM ve v
    WHERE v.id_rap = ? AND v.ngay_dat >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND v.trang_thai = 1
", $id_rap);

// Doanh thu tháng này
$tong_thang = pdo_query_one("
    SELECT COALESCE(SUM(v.price), 0) as tong_doanh_thu
    FROM ve v
    WHERE v.id_rap = ? AND MONTH(v.ngay_dat) = MONTH(CURDATE()) AND YEAR(v.ngay_dat) = YEAR(CURDATE()) AND v.trang_thai = 1
", $id_rap);

// Top combo bán chạy
$best_combo = pdo_query_one("
    SELECT v.combo, COUNT(*) as so_luong_dat
    FROM ve v
    WHERE v.id_rap = ? AND v.combo IS NOT NULL AND v.combo != '' AND v.trang_thai = 1 AND DATE(v.ngay_dat) >= ? AND DATE(v.ngay_dat) <= ?
    GROUP BY v.combo
    ORDER BY so_luong_dat DESC
    LIMIT 1
", $id_rap, $from_date, $to_date);

// Doanh thu 30 ngày gần nhất cho chart (sử dụng filter phim nếu có)
if ($id_phim_filter > 0) {
    $doanh_thu_30_ngay = pdo_query("
        SELECT 
            DATE(v.ngay_dat) as ngay,
            COALESCE(SUM(v.price), 0) as doanh_thu
        FROM ve v
        WHERE v.id_rap = ? 
        AND v.ngay_dat >= ? AND v.ngay_dat <= ?
        AND v.trang_thai = 1
        AND v.id_phim = ?
        GROUP BY DATE(v.ngay_dat)
        ORDER BY ngay
    ", $id_rap, $from_date . ' 00:00:00', $to_date . ' 23:59:59', $id_phim_filter);
} else {
    $doanh_thu_30_ngay = pdo_query("
        SELECT 
            DATE(v.ngay_dat) as ngay,
            COALESCE(SUM(v.price), 0) as doanh_thu
        FROM ve v
        WHERE v.id_rap = ? 
        AND v.ngay_dat >= ? AND v.ngay_dat <= ?
        AND v.trang_thai = 1
        GROUP BY DATE(v.ngay_dat)
        ORDER BY ngay
    ", $id_rap, $from_date . ' 00:00:00', $to_date . ' 23:59:59');
}

// Phân bổ phim theo trạng thái
$phim_trang_thai = pdo_query("
    SELECT 
        CASE 
            WHEN lc.ngay_chieu >= CURDATE() THEN 'Đang Chiếu'
            WHEN lc.ngay_chieu > CURDATE() AND lc.ngay_chieu < DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'Sắp Chiếu'
            ELSE 'Kết Thúc'
        END as trang_thai,
        COUNT(DISTINCT lc.id_phim) as so_phim
    FROM lichchieu lc
    WHERE lc.id_rap = ?
    GROUP BY trang_thai
", $id_rap);

// Doanh thu theo combo (sử dụng filter)
if ($id_phim_filter > 0) {
    $doanh_thu_combo = pdo_query("
        SELECT 
            CASE WHEN v.combo IS NULL OR v.combo = '' THEN 'Không Combo' ELSE v.combo END as combo,
            COALESCE(SUM(v.price), 0) as doanh_thu
        FROM ve v
        WHERE v.id_rap = ? AND v.trang_thai = 1 AND DATE(v.ngay_dat) >= ? AND DATE(v.ngay_dat) <= ? AND v.id_phim = ?
        GROUP BY v.combo
        ORDER BY doanh_thu DESC
        LIMIT 5
    ", $id_rap, $from_date, $to_date, $id_phim_filter);
} else {
    $doanh_thu_combo = pdo_query("
        SELECT 
            CASE WHEN v.combo IS NULL OR v.combo = '' THEN 'Không Combo' ELSE v.combo END as combo,
            COALESCE(SUM(v.price), 0) as doanh_thu
        FROM ve v
        WHERE v.id_rap = ? AND v.trang_thai = 1 AND DATE(v.ngay_dat) >= ? AND DATE(v.ngay_dat) <= ?
        GROUP BY v.combo
        ORDER BY doanh_thu DESC
        LIMIT 5
    ", $id_rap, $from_date, $to_date);
}

?>

<style>
    /* Gradient Header */
    .rap-dashboard-header {
        background: linear-gradient(135deg, #a2afebff 0%, #764ba2 100%);
        color: white;
        padding: 19px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .rap-dashboard-header h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    .rap-dashboard-header p {
        margin: 10px 0 0 0;
        opacity: 0.95;
        font-size: 16px;
    }

    .rap-dashboard-header .clock {
        font-size: 18px;
        margin-top: 15px;
        opacity: 0.9;
    }

    /* Statistics Grid */
    .rap-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .rap-stat-card {
        background: white;
        border-radius: 8px;
        padding: 5px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .rap-stat-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
        border-color: #667eea;
    }

    .rap-stat-icon {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .rap-stat-value {
        font-size: 32px;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 10px 0;
    }

    .rap-stat-label {
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
    }

    /* Report Row */
    .rap-report-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .rap-report-box {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 25px;
        transition: all 0.3s ease;
    }

    .rap-report-box:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .rap-report-box-header {
        color: #667eea;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .rap-report-box-value {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 8px;
    }

    .rap-report-box-unit {
        color: #9ca3af;
        font-size: 13px;
    }

    /* Content Body */
    .content-body {
        padding: 20px;
    }

    .box {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .box .head {
        margin-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 15px;
    }

    .box .head h4 {
        margin: 0;
        color: #1f2937;
        font-weight: 600;
    }
</style>

<div class="content-body">
    <!-- Header with Greeting -->
    <div class="rap-dashboard-header">
        <h2><?php echo __('Dashboard Quản Lý Rạp'); ?></h2>
        <p><?php echo __('Chào mừng'); ?> <?= htmlspecialchars($_SESSION['user1']['name'] ?? __('Quản lý rạp')) ?> • <?= htmlspecialchars($ten_rap) ?></p>
        <div class="clock">
            <strong><?php echo __('Thời gian:'); ?></strong> <span id="real-time-clock">--:--:--</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <form method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <input type="hidden" name="act" value="home">
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <label style="font-weight: 600; color: #1f2937; font-size: 14px;"><?php echo __('Từ ngày'); ?></label>
                <input type="date" name="from" value="<?= htmlspecialchars($from_date) ?>" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <label style="font-weight: 600; color: #1f2937; font-size: 14px;"><?php echo __('Đến ngày'); ?></label>
                <input type="date" name="to" value="<?= htmlspecialchars($to_date) ?>" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            </div>
            <div style="display: flex; flex-direction: column; gap: 5px;">
                <label style="font-weight: 600; color: #1f2937; font-size: 14px;"><?php echo __('Phim'); ?></label>
                <select name="id_phim" style="padding: 11px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                    <option value="0">-- <?php echo __('Tất cả phim'); ?> --</option>
                    <?php foreach ($danh_sach_phim as $phim): ?>
                        <option value="<?= $phim['id'] ?>" <?= $id_phim_filter == $phim['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($phim['tieu_de']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px;">🔍 <?php echo __('Lọc'); ?></button>
            <a href="index.php?act=home" style="background: #f3f4f6; color: #1f2937; padding: 10px 24px; border: 1px solid #d1d5db; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 14px; text-decoration: none;">↺ <?php echo __('Đặt lại'); ?></a>
        </form>
    </div>

    <!-- Statistics Grid -->
    <div class="rap-stats-grid">
        <div class="rap-stat-card">
            <div class="rap-stat-icon">💰</div>
            <div class="rap-stat-label"><?php echo __('Tổng Doanh Thu'); ?></div>
            <div class="rap-stat-value">
                <?= number_format($tong['tong_doanh_thu'] ?? 0) ?>
            </div>
            <div class="rap-stat-label"><?php echo __('VNĐ'); ?></div>
        </div>

        <div class="rap-stat-card">
            <div class="rap-stat-icon">🎟️</div>
            <div class="rap-stat-label"><?php echo __('Tổng Vé Bán'); ?></div>
            <div class="rap-stat-value">
                <?= $tong['tong_so_luong_ve_dat'] ?? '0' ?>
            </div>
            <div class="rap-stat-label"><?php echo __('Vé'); ?></div>
        </div>

        <div class="rap-stat-card">
            <div class="rap-stat-icon">🎬</div>
            <div class="rap-stat-label"><?php echo __('Phim Đang Chiếu'); ?></div>
            <div class="rap-stat-value">
                <?= $phim_dang_chieu['total_phim'] ?? '0' ?>
            </div>
            <div class="rap-stat-label"><?php echo __('Bộ'); ?></div>
        </div>

        <div class="rap-stat-card">
            <div class="rap-stat-icon">📅</div>
            <div class="rap-stat-label"><?php echo __('Phim Sắp Chiếu'); ?></div>
            <div class="rap-stat-value">
                <?= $phim_sap_chieu['total_phim'] ?? '0' ?>
            </div>
            <div class="rap-stat-label"><?php echo __('Bộ'); ?></div>
        </div>
    </div>

    <!-- Revenue Report -->
    <div class="rap-report-row">
        <div class="rap-report-box">
            <div class="rap-report-box-header"><?php echo __('Doanh Thu Hôm Nay'); ?></div>
            <div class="rap-report-box-value">
                <?= number_format($tong_day['tong_doanh_thu'] ?? 0) ?>
            </div>
            <div class="rap-report-box-unit"><?php echo __('VNĐ'); ?></div>
        </div>

        <div class="rap-report-box">
            <div class="rap-report-box-header"><?php echo __('Doanh Thu Tuần Này'); ?></div>
            <div class="rap-report-box-value">
                <?= number_format($tong_tuan['tong_doanh_thu'] ?? 0) ?>
            </div>
            <div class="rap-report-box-unit"><?php echo __('VNĐ'); ?></div>
        </div>

        <div class="rap-report-box">
            <div class="rap-report-box-header"><?php echo __('Doanh Thu Tháng Này'); ?></div>
            <div class="rap-report-box-value">
                <?= number_format($tong_thang['tong_doanh_thu'] ?? 0) ?>
            </div>
            <div class="rap-report-box-unit"><?php echo __('VNĐ'); ?></div>
        </div>

        <div class="rap-report-box">
            <div class="rap-report-box-header"><?php echo __('Combo Top'); ?></div>
            <div class="rap-report-box-value">
                <?= htmlspecialchars($best_combo['combo'] ?? 'N/A') ?>
            </div>
            <div class="rap-report-box-unit">
                <?= $best_combo['so_luong_dat'] ?? '0' ?> <?php echo __('đơn'); ?>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mt-30">
        <div class="col-12 mb-30">
            <div class="box">
                <div class="head">
                    <h4><?php echo __('Biểu Đồ Doanh Thu 30 Ngày Qua'); ?></h4>
                </div>
                <div class="content" style="padding: 20px;">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="box">
                <div class="head">
                    <h4><?php echo __('Phân Bổ Phim Theo Trạng Thái'); ?></h4>
                </div>
                <div class="content" style="padding: 20px;">
                    <canvas id="movieStatusChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="box">
                <div class="head">
                    <h4><?php echo __('Doanh Thu Theo Loại Combo'); ?></h4>
                </div>
                <div class="content" style="padding: 20px;">
                    <canvas id="comboChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    function updateClock() {
        var currentTime = new Date();
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();

        hours = (hours < 10 ? "0" : "") + hours;
        minutes = (minutes < 10 ? "0" : "") + minutes;
        seconds = (seconds < 10 ? "0" : "") + seconds;

        var clockElement = document.getElementById('real-time-clock');
        if (clockElement) {
            clockElement.innerText = hours + ":" + minutes + ":" + seconds;
        }

        setTimeout(updateClock, 1000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateClock();
        
        // Initialize Charts
        initRevenueChart();
        initMovieStatusChart();
        initComboChart();
    });

    // Revenue Chart - 30 days
    function initRevenueChart() {
        var ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        
        var data = <?= json_encode($doanh_thu_30_ngay) ?>;
        var labels = [];
        var values = [];
        
        // Ensure 30 days of data
        var today = new Date();
        for (var i = 29; i >= 0; i--) {
            var date = new Date(today);
            date.setDate(date.getDate() - i);
            var dateStr = date.getFullYear() + '-' + 
                          (date.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                          date.getDate().toString().padStart(2, '0');
            
            labels.push(date.getDate() + '/' + (date.getMonth() + 1));
            
            var found = data.find(d => d.ngay === dateStr);
            values.push(found ? found.doanh_thu : 0);
        }
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '<?php echo __('Doanh Thu (VNĐ)'); ?>',
                    data: values,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                            }
                        }
                    }
                }
            }
        });
    }

    // Movie Status Chart
    function initMovieStatusChart() {
        var ctx = document.getElementById('movieStatusChart');
        if (!ctx) return;
        
        var data = <?= json_encode($phim_trang_thai) ?>;
        var labels = [];
        var values = [];
        var colors = ['#10b981', '#f59e0b', '#ef4444'];
        
        // Dictionary for JS translation
        var langMap = {
            'Đang Chiếu': '<?php echo __('Đang Chiếu'); ?>',
            'Sắp Chiếu': '<?php echo __('Sắp Chiếu'); ?>',
            'Kết Thúc': '<?php echo __('Kết Thúc'); ?>'
        };
        
        // Chuẩn bị dữ liệu với đầy đủ 3 trạng thái
        var states = ['Đang Chiếu', 'Sắp Chiếu', 'Kết Thúc'];
        states.forEach((state, idx) => {
            var found = data.find(d => d.trang_thai === state);
            labels.push(langMap[state] || state);
            values.push(found ? found.so_phim : 0);
        });
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Combo Chart
    function initComboChart() {
        var ctx = document.getElementById('comboChart');
        if (!ctx) return;
        
        var data = <?= json_encode($doanh_thu_combo) ?>;
        var labels = [];
        var values = [];
        var colors = ['#667eea', '#764ba2', '#f093fb', '#4facfe', '#43e97b'];
        
        // Dictionary for JS translation
        var langMap = {
            'Không Combo': '<?php echo __('Không Combo'); ?>'
        };
        
        data.forEach((item, idx) => {
            labels.push(langMap[item.combo] || item.combo);
            values.push(item.doanh_thu);
        });
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '<?php echo __('Doanh Thu (VNĐ)'); ?>',
                    data: values,
                    backgroundColor: colors.slice(0, data.length),
                    borderRadius: 4,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + 'đ';
                            }
                        }
                    }
                }
            }
        });
    }
</script>
