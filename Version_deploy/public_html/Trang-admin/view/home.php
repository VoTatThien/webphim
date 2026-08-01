<?php 
include "./view/home/sideheader.php";

// Kiểm tra role và hiển thị dashboard phù hợp
if (isset($_SESSION['user1']['vai_tro']) && $_SESSION['user1']['vai_tro'] == ROLE_QUAN_LY_RAP) {
    // Dashboard cho Quản lý rạp
    include "./view/home/dashboard_quan_ly_rap.php";
    include "./view/home/footer.php";
    exit;
}

if (isset($_SESSION['user1']['vai_tro']) && $_SESSION['user1']['vai_tro'] == ROLE_QUAN_LY_CUM) {
    // Dashboard cho Quản lý cụm
    include "./view/home/dashboard_quan_ly_cum.php";
    include "./view/home/footer.php";
    exit;
}

if (isset($_SESSION['user1']['vai_tro']) && $_SESSION['user1']['vai_tro'] == ROLE_NHAN_VIEN) {
    // Dashboard cho Nhân viên
    include "./view/home/dashboard_nhan_vien.php";
    include "./view/home/footer.php";
    exit;
}
?>

<!-- Content Body Start -->
<div class="content-body">

    <!-- Page Headings Start -->
    <div class="row justify-content-between align-items-center mb-10">
        <?php if (isset($_SESSION['user1']['vai_tro']) && $_SESSION['user1']['vai_tro'] == 2): ?>
        <!-- Page Heading Start -->
        <div class="col-12 col-lg-auto mb-20">
            <div class="page-heading">
                <h3><?php echo __('Trang Chủ'); ?></h3>

            </div>
        </div><!-- Page Heading End -->

        <!-- Page Button Group Start -->
        <div class="col-12 col-lg-auto mb-20">
            <h1 id="real-time-clock"></h1>
        </div><!-- Page Button Group End -->
        <?php endif; ?>
    </div><!-- Page Headings End -->

    <?php if (isset($_SESSION['user1']['vai_tro']) && $_SESSION['user1']['vai_tro'] == 2): ?>
    <!-- Top Report Wrap Start -->
    <div class="row">
        <!-- Top Report Start -->
        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="top-report">

                <!-- Head -->
                <div class="head">
                    <h4><?php echo __('Tổng doanh thu'); ?></h4>
                    <a href="#" class="view"><i class="zmdi zmdi-eye"></i></a>
                </div>

                <!-- Content -->
                <div class="content">
                    <?php foreach ($tong as $t){
                         extract($t);
                         echo ' <h2>'.number_format($tong_doanh_thu).' ' . __('VNĐ') . '</h2>';
                    } ?>

                </div>

                <!-- Footer -->

            </div>
        </div><!-- Top Report End -->

        <!-- Top Report Start -->
        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="top-report">

                <!-- Head -->
                <div class="head">
                    <h4><?php echo __('Tổng vé đã bán'); ?></h4>
                    <a href="#" class="view"><i class="zmdi zmdi-eye"></i></a>
                </div>

                <!-- Content -->
                <div class="content">
                    <?php foreach ($tong as $t){
                        extract($t);
                        echo ' <h2>'.$tong_so_luong_ve_dat.' ' . __('VÉ') . '</h2>';
                    } ?>
                </div>

                <!-- Footer -->

            </div>
        </div><!-- Top Report End -->

        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="top-report">

                <!-- Head -->
                <div class="head">
                    <h4><?php echo __('Tổng doanh thu hôm nay'); ?></h4>
                    <a href="#" class="view"><i class="zmdi zmdi-eye"></i></a>
                </div>

                <!-- Content -->
                <div class="content">
                    <?php foreach ($tong_day as $t){
                        extract($t);
                        echo ' <h2>'.number_format($tong_doanh_thu).' ' . __('VNĐ') . '</h2>';
                    } ?>

                </div>

                <!-- Footer -->

            </div>
        </div><!-- Top Report End -->
        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="top-report">

                <!-- Head -->
                <div class="head">
                    <h4><?php echo __('Tổng doanh thu tuần này'); ?></h4>
                    <a href="#" class="view"><i class="zmdi zmdi-eye"></i></a>
                </div>

                <!-- Content -->
                <div class="content">
                    <?php foreach ($tong_tuan as $t){
                        extract($t);
                        echo ' <h2>'.number_format($tong_doanh_thu).' ' . __('VNĐ') . '</h2>';
                    } ?>

                </div>

                <!-- Footer -->

            </div>
        </div><!-- Top Report End -->
        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="top-report">

                <!-- Head -->
                <div class="head">
                    <h4><?php echo __('Tổng doanh thu tháng này'); ?></h4>
                    <a href="#" class="view"><i class="zmdi zmdi-eye"></i></a>
                </div>

                <!-- Content -->
                <div class="content">
                    <?php foreach ($tong_thang as $t){
                        extract($t);
                        echo ' <h2>'.number_format($tong_thang['tong_doanh_thu'] ?? $t['tong_doanh_thu']).' ' . __('VNĐ') . '</h2>';
                    } ?>

                </div>

                <!-- Footer -->

            </div>
        </div><!-- Top Report End -->
        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="top-report">

                <!-- Head -->
                <div class="head">
                    <h4><?php echo __('Tổng Phim Đang Chiếu'); ?></h4>
                    <a href="#" class="view"><i class="zmdi zmdi-eye"></i></a>
                </div>

                <!-- Content -->
                <div class="content">
                    <?php foreach ($tpdc as $t){
                        extract($t);
                        echo ' <h2>'.$total_phim.' ' . __('Phim') . '</h2>';
                    } ?>
                </div>

                <!-- Footer -->

            </div>
        </div><!-- Top Report End -->
        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="top-report">

                <!-- Head -->
                <div class="head">
                    <h4><?php echo __('Tổng Phim Sắp Chiếu'); ?></h4>
                    <a href="#" class="view"><i class="zmdi zmdi-eye"></i></a>
                </div>

                <!-- Content -->
                <div class="content">
                    <?php foreach ($tpsc as $t){
                        extract($t);
                        echo ' <h2>'.$total_phim.' ' . __('Phim') . '</h2>';
                    } ?>
                </div>

                <!-- Footer -->

            </div>
        </div><!-- Top Report End -->
        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="top-report">

                <!-- Head -->
                <div class="head">
                    <h4><?php echo __('Combo được đặt nhiều nhất'); ?></h4>
                    <a href="#" class="view"><i class="zmdi zmdi-eye"></i></a>
                </div>

                <!-- Content -->
                <div class="content">
                    <?php foreach ($best_combo as $t){
                        extract($t);
                        echo ' <h2>' . __('Đã có') . ' '.$so_luong_dat.' '.htmlspecialchars($combo).' ' . __('được đặt') . '</h2>';
                    } ?>

                </div>

                <!-- Footer -->

            </div>
        </div><!-- Top Report End -->

    </div><!-- Top Report Wrap End -->

    <!-- Revenue Chart Section -->
    <?php if (isset($_SESSION['user1']['vai_tro']) && $_SESSION['user1']['vai_tro'] == 2): ?>
    <div class="row mt-30">
        <div class="col-12 mb-30">
            <div class="box">
                <div class="head">
                    <h4><?php echo __('📊 Biểu Đồ Doanh Thu 30 Ngày Qua'); ?></h4>
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
                    <h4><?php echo __('🎬 Phân Bổ Phim Theo Trạng Thái'); ?></h4>
                </div>
                <div class="content" style="padding: 20px;">
                    <canvas id="movieStatusChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xlg-6 col-md-6 col-12 mb-30">
            <div class="box">
                <div class="head">
                    <h4><?php echo __('💰 Doanh Thu Theo Loại Combo'); ?></h4>
                </div>
                <div class="content" style="padding: 20px;">
                    <canvas id="comboChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php else: ?>
        <div class="row">
            <div class="col-12">
                <h1><?php echo __('Chào mừng'); ?> <?= htmlspecialchars($_SESSION['user1']['name'] ?? __('Người dùng')) ?> <?php echo __('đến với trang làm việc của Galaxy Studio'); ?></h1>
            </div>
        </div>
    <?php endif; ?>

</div><!-- Content Body End -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    function updateClock() {
        var currentTime = new Date();
        var hours = currentTime.getHours();
        var minutes = currentTime.getMinutes();
        var seconds = currentTime.getSeconds();

        // Thêm số 0 đằng trước nếu giờ, phút hoặc giây chỉ có một chữ số
        hours = (hours < 10 ? "0" : "") + hours;
        minutes = (minutes < 10 ? "0" : "") + minutes;
        seconds = (seconds < 10 ? "0" : "") + seconds;

        // Kiểm tra element tồn tại trước khi cập nhật
        var clockElement = document.getElementById('real-time-clock');
        if (clockElement) {
            clockElement.innerText = hours + ":" + minutes + ":" + seconds;
        }

        // Cập nhật thời gian mỗi giây
        setTimeout(updateClock, 1000);
    }

    // Bắt đầu cập nhật thời gian khi DOM đã sẵn sàng
    document.addEventListener('DOMContentLoaded', function() {
        updateClock();
        
        // Initialize Revenue Chart
        initRevenueChart();
        initMovieStatusChart();
        initComboChart();
    });

    // Revenue Chart - 30 days
    function initRevenueChart() {
        var ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        
        // Generate last 30 days data
        var labels = [];
        var data = [];
        var today = new Date();
        
        for (var i = 29; i >= 0; i--) {
            var date = new Date(today);
            date.setDate(date.getDate() - i);
            labels.push((date.getDate()) + '/' + (date.getMonth() + 1));
            
            // Generate random revenue data (replace with real data from server)
            data.push(Math.floor(Math.random() * 50) + 20);
        }
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '<?php echo __('Doanh Thu (Triệu VNĐ)'); ?>',
                    data: data,
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
                                return value + 'M';
                            }
                        }
                    }
                }
            }
        });
    }

    // Movie Status Chart - Pie
    function initMovieStatusChart() {
        var ctx = document.getElementById('movieStatusChart');
        if (!ctx) return;
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['<?php echo __('Đang Chiếu'); ?>', '<?php echo __('Sắp Chiếu'); ?>', '<?php echo __('Kết Thúc'); ?>'],
                datasets: [{
                    data: [12, 8, 5],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
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

    // Combo Revenue Chart - Bar
    function initComboChart() {
        var ctx = document.getElementById('comboChart');
        if (!ctx) return;
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Combo A', 'Combo B', 'Combo C', 'Combo D'],
                datasets: [{
                    label: '<?php echo __('Doanh Thu (Triệu VNĐ)'); ?>',
                    data: [35, 28, 42, 31],
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#4facfe'
                    ],
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
                                return value + 'M';
                            }
                        }
                    }
                }
            }
        });
    }
</script>
