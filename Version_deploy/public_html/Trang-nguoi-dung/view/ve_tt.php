<?php
include "view/search.php";
    extract($load_ve_tt);
    
    // Lấy thông tin rạp, nếu không có thì dùng default
    $ten_rap_hienthi = !empty($ten_rap) ? __($ten_rap) : __('Galaxy Studio Gò Vấp');
    $dia_chi_hienthi = !empty($dia_chi_rap) ? $dia_chi_rap : __('Địa chỉ chưa cập nhật');
    
    // Load QR config if exists
    $qr_host = $_SERVER['HTTP_HOST'];
    if (file_exists(__DIR__ . '/../config/qr_config.php')) {
        include __DIR__ . '/../config/qr_config.php';
        if (!empty(QR_SERVER_IP)) {
            $qr_host = QR_SERVER_IP;
            if (QR_SERVER_PORT != 80) {
                $qr_host .= ':' . QR_SERVER_PORT;
            }
        } else {
            // Auto-detect IP
            $ip = gethostbyname(gethostname());
            if ($ip !== gethostname() && strpos($ip, '127.') !== 0) {
                $qr_host = $ip;
            }
        }
    } else {
        // Fallback function to get server IP address for LAN access
        if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
            $ip = gethostbyname(gethostname());
            if ($ip !== gethostname() && strpos($ip, '127.') !== 0) {
                $qr_host = $ip;
            }
        }
    }
    $base_dir = (strpos($_SERVER['REQUEST_URI'], '/webphim_hung/') !== false) ? '/webphim_hung/' : '/';
    ?>
    <section class="container">
        <div class="order-container">
            <div class="order">
                <img class="order__images" alt='' src="images/tickets.png">
                <p class="order__title"><?= __('Cảm ơn') ?> <br><span class="order__descript"><?= __('bạn đã mua vé thành công') ?></span></p>
            </div>
            
            <?php if (isset($_SESSION['diem_cong_moi']) && $_SESSION['diem_cong_moi'] > 0): ?>
            <!-- Thông báo tích điểm -->
            <div style="background: linear-gradient(135deg, #FFD700, #FFA500); color: #000; padding: 20px; border-radius: 15px; text-align: center; margin: 20px 0; box-shadow: 0 4px 15px rgba(255,215,0,0.3);">
                <h3 style="margin: 0 0 10px 0; font-size: 1.5rem;">
                    🎉 <?= __('Chúc mừng! Bạn nhận được') ?> <strong><?= number_format($_SESSION['diem_cong_moi']) ?> <?= __('điểm') ?></strong>
                </h3>
                <p style="margin: 0; font-size: 1rem; opacity: 0.9;">
                    <?php if (isset($_SESSION['hang_moi'])): ?>
                        🏆 <?= __('Bạn đã được nâng hạng lên') ?> <strong><?= __($_SESSION['hang_moi']) ?></strong>!<br>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['diem_da_doi']) && $_SESSION['diem_da_doi'] > 0): ?>
                        ⭐ <?= __('Đã sử dụng') ?> <?= number_format($_SESSION['diem_da_doi']) ?> <?= __('điểm') ?> <?= __('để giảm giá') ?><br>
                    <?php endif; ?>
                    <?= __('Tổng điểm hiện tại:') ?> <strong><?= number_format($_SESSION['user']['diem_tich_luy'] ?? 0) ?> <?= __('điểm') ?></strong>
                </p>
            </div>
            <?php 
                unset($_SESSION['diem_cong_moi']);
                unset($_SESSION['hang_moi']);
                unset($_SESSION['diem_da_doi']);
            endif; ?>

            <div class="ticket">
                <div class="ticket-position">
                    <div class="ticket__indecator indecator--pre"><div class="indecator-text pre--text">Galaxy Studio</div> </div>
                    <div class="ticket__inner">
                        <div class="ticket-secondary">
                            <span class="ticket__item"><?= __('Mã vé') ?> <strong class="ticket__number"><?= $id ?></strong></span>
                            <span class="ticket__item ticket__date"><i class="fa fa-calendar" style="color:#ffd564; margin-right:5px;"></i> <?= $ngay_chieu ?></span>
                            <span class="ticket__item ticket__time"><i class="fa fa-clock-o" style="color:#ffd564; margin-right:5px;"></i> <?= $thoi_gian_chieu ?></span>
                            <span class="ticket__item"><i class="fa fa-film" style="color:#ffd564; margin-right:5px;"></i> <?= __('Rạp chiếu:') ?> <span class="ticket__cinema"><?= $ten_rap_hienthi ?></span></span>
                            <span class="ticket__item"><i class="fa fa-map-marker" style="color:#ffd564; margin-right:5px;"></i> <?= __('Địa chỉ rạp:') ?> <span class="ticket__cinema"><?= $dia_chi_hienthi ?></span></span>
                            <span class="ticket__item"><i class="fa fa-desktop" style="color:#ffd564; margin-right:5px;"></i> <?= __('Phòng chiếu:') ?> <strong class="ticket__number"><?= $tenphong ?></strong></span>
                            <span class="ticket__item ticket__price" style="margin-top: 5px"><i class="fa fa-money" style="color:#ffd564; margin-right:5px;"></i> <?= __('Giá:') ?> <strong class="ticket__cost"><?= number_format($thanh_tien) ?> vnđ</strong></span>
                        </div>
                        <div class="ticket-primery" style="position: relative;">
                            <?php
                            $qr_url = "http://" . $qr_host . $base_dir . "Trang-nguoi-dung/quete.php?id=" . $id;
                            $qr_src = function_exists('get_qr_base64') ? get_qr_base64($qr_url) : 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($qr_url);
                            ?>
                            <div style="position: absolute; top: 86px; right: -1px; width: 107px; height: 107px; background: #fff; border: 2px solid #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <img src="<?= $qr_src ?>" alt="QR Code" style="width: 100px; height: 100px; object-fit: contain;" />
                            </div>
                            <span class="ticket__item ticket__item--primery ticket__film"><?= __('Phim:') ?> <br><strong class="ticket__movie"><?= __($tieu_de) ?></strong></span>
                            <span class="ticket__item ticket__time"><i class="fa fa-circle-o" style="color:#ffd564; margin-right:5px;"></i> <?= __('Ghế:') ?> <?= $ghe ?></span>
                            <span class="ticket__item ticket__time"><i class="fa fa-coffee" style="color:#ffd564; margin-right:5px;"></i> <?= __('Combo:') ?> <?= $combo ?></span>
                        </div>
                    </div>
                    <div class="ticket__indecator indecator--post"><div class="indecator-text post--text">Galaxy Studio</div></div>
                </div>
            </div>
            <div style="text-align:center;margin-top:16px">
                <a class="btn btn-md btn--primary" href="view/ve_invoice.php?id=<?= $id ?>" target="_blank" rel="noopener" style="color: #000;"><?= __('Tải / In hóa đơn (PDF)') ?></a>
            </div>
        </div>
    </section>
