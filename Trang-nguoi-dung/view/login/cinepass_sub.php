<?php
include 'view/search.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_tro'] != 0) {
    echo '<script>alert("' . __("Bạn cần đăng nhập bằng tài khoản thành viên để mua gói hội viên!") . '"); window.location.href="index.php?act=dangnhap";</script>';
    exit;
}

$user = $_SESSION['user'];
$active_sub = (int)($user['cinepass_sub_status'] ?? 0);
$sub_type = $user['cinepass_sub_type'] ?? '';
$tickets_left = (int)($user['cinepass_tickets_left'] ?? 0);
$combos_left = (int)($user['cinepass_combos_left'] ?? 0);
$expire_date = $user['cinepass_expire_date'] ?? '';

?>

<style>
    .cinepass-sub-wrapper {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #151216;
        color: #fff;
        padding: 50px 20px;
        min-height: 80vh;
    }

    .sub-container {
        max-width: 1100px;
        margin: 0 auto;
    }

    .sub-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .sub-header h1 {
        font-size: 36px;
        color: #ffd564;
        font-weight: 700;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
    }

    .sub-header p {
        color: #9ca3af;
        font-size: 16px;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Thẻ thông tin gói hiện tại */
    .current-sub-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .current-sub-info h3 {
        margin: 0 0 10px 0;
        font-size: 20px;
        color: #ffd564;
    }

    .current-sub-info p {
        margin: 5px 0;
        color: #d1d5db;
        font-size: 14px;
    }

    .current-sub-badge {
        background: linear-gradient(135deg, #ffd564 0%, #ff9f43 100%);
        color: #151216;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 14px;
        text-transform: uppercase;
        box-shadow: 0 4px 15px rgba(255, 213, 100, 0.3);
    }

    /* Các thẻ gói sản phẩm */
    .sub-plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-top: 20px;
    }

    .plan-card {
        background: linear-gradient(145deg, #1f1a21 0%, #171318 100%);
        border: 2px solid #322b35;
        border-radius: 20px;
        padding: 40px 30px;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .plan-card:hover {
        transform: translateY(-12px);
        border-color: #ffd564;
        box-shadow: 0 20px 45px rgba(255, 213, 100, 0.15);
    }

    /* Gói nổi bật */
    .plan-card.featured {
        border-color: #ffd564;
        background: linear-gradient(145deg, #28202b 0%, #171318 100%);
    }

    .plan-card.featured::after {
        content: 'POPULAR';
        position: absolute;
        top: 15px;
        right: 15px;
        background: #fe505a;
        color: white;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 50px;
        letter-spacing: 0.5px;
    }

    .plan-name {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .plan-card.standard .plan-name {
        color: #e2e8f0;
    }

    .plan-card.premium .plan-name {
        color: #ffd564;
    }

    .plan-price {
        font-size: 36px;
        font-weight: 800;
        margin: 20px 0;
        color: #fff;
    }

    .plan-price span {
        font-size: 14px;
        color: #9ca3af;
        font-weight: 400;
    }

    .plan-features {
        list-style: none;
        padding: 0;
        margin: 30px 0;
        text-align: left;
    }

    .plan-features li {
        padding: 10px 0;
        color: #d1d5db;
        font-size: 15px;
        display: flex;
        align-items: center;
    }

    .plan-features li i {
        color: #ffd564;
        margin-right: 12px;
        font-size: 18px;
    }

    .plan-btn {
        display: block;
        width: 100%;
        background: transparent;
        color: #ffd564;
        border: 2px solid #ffd564;
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        text-decoration: none;
        margin-top: auto;
    }

    .plan-btn:hover {
        background: #ffd564;
        color: #151216;
        box-shadow: 0 6px 20px rgba(255, 213, 100, 0.3);
    }

    .plan-card.featured .plan-btn {
        background: #ffd564;
        color: #151216;
    }

    .plan-card.featured .plan-btn:hover {
        background: #ffa801;
        border-color: #ffa801;
    }
</style>

<div class="cinepass-sub-wrapper">
    <div class="sub-container">
        
        <!-- Header giới thiệu -->
        <div class="sub-header">
            <h1>CinePass Subscription</h1>
            <p><?= __("Đăng ký gói thuê bao tháng độc quyền của Galaxy Studio. Xem phim thả ga, nhận combo bắp nước miễn phí và tích lũy ngập tràn quà tặng!") ?></p>
        </div>

        <!-- Trạng thái gói hiện tại của người dùng -->
        <div class="current-sub-card">
            <div class="current-sub-info">
                <?php if ($active_sub == 1): ?>
                    <h3><i class="fa fa-ticket-alt" style="color: #ffd564; margin-right: 10px;"></i> <?= __("Gói hội viên đang hoạt động:") ?> <?= strtoupper($sub_type) ?></h3>
                    <p><i class="fa fa-film"></i> <?= __("Vé xem phim miễn phí còn lại:") ?> <strong><?= $tickets_left ?></strong> <?= __("vé") ?></p>
                    <p><i class="fa fa-glass-martini-alt"></i> <?= __("Combo bắp nước miễn phí còn lại:") ?> <strong><?= $combos_left ?></strong> <?= __("combo") ?></p>
                    <p><i class="fa fa-calendar-alt"></i> <?= __("Ngày hết hạn:") ?> <strong style="color: #fe505a;"><?= date('d/m/Y H:i', strtotime($expire_date)) ?></strong></p>
                <?php else: ?>
                    <h3><i class="fa fa-info-circle" style="color: #ffd564; margin-right: 10px;"></i> <?= __("Bạn chưa đăng ký gói hội viên") ?></h3>
                    <p><?= __("Hãy chọn một trong các gói hội viên hấp dẫn bên dưới để bắt đầu nhận ưu đãi độc quyền!") ?></p>
                <?php endif; ?>
            </div>
            <?php if ($active_sub == 1): ?>
                <div class="current-sub-badge"><?= __("Hội viên Active") ?></div>
            <?php endif; ?>
        </div>

        <!-- Grid các gói sản phẩm -->
        <div class="sub-plans-grid">
            
            <!-- Gói Standard -->
            <div class="plan-card standard">
                <div>
                    <div class="plan-name">CinePass Standard</div>
                    <div class="plan-price">150.000đ <span>/ <?= __("tháng") ?></span></div>
                    <ul class="plan-features">
                        <li><i class="fa fa-check-circle"></i> <strong>3 <?= __("vé xem phim") ?></strong> <?= __("miễn phí bất kỳ (2D)") ?></li>
                        <li><i class="fa fa-check-circle"></i> <?= __("Gửi vé điện tử & mã QR check-in nhanh") ?></li>
                        <li><i class="fa fa-check-circle"></i> <strong><?= __("Tặng ngay 1,500 điểm thưởng") ?></strong> <?= __("vào tài khoản") ?></li>
                        <li><i class="fa fa-check-circle"></i> <?= __("Xem phim mọi ngày, kể cả cuối tuần & lễ") ?></li>
                    </ul>
                </div>
                <form action="index.php?act=buy_cinepass_sub" method="post">
                    <input type="hidden" name="sub_type" value="standard">
                    <input type="hidden" name="amount" value="150000">
                    <button type="submit" class="plan-btn"><?= __("Đăng ký Standard") ?></button>
                </form>
            </div>

            <!-- Gói Premium -->
            <div class="plan-card premium featured">
                <div>
                    <div class="plan-name">CinePass Premium</div>
                    <div class="plan-price">250.000đ <span>/ <?= __("tháng") ?></span></div>
                    <ul class="plan-features">
                        <li><i class="fa fa-check-circle"></i> <strong>5 <?= __("vé xem phim") ?></strong> <?= __("miễn phí bất kỳ (2D/3D)") ?></li>
                        <li><i class="fa fa-check-circle"></i> <strong>2 <?= __("combo bắp nước") ?></strong> <?= __("miễn phí tự chọn") ?></li>
                        <li><i class="fa fa-check-circle"></i> <strong><?= __("Tặng ngay 3,000 điểm thưởng") ?></strong> <?= __("vào tài khoản") ?></li>
                        <li><i class="fa fa-check-circle"></i> <?= __("Đặc quyền check-in lối đi VIP soát vé") ?></li>
                        <li><i class="fa fa-check-circle"></i> <?= __("Xem phim mọi ngày, kể cả cuối tuần & lễ") ?></li>
                    </ul>
                </div>
                <form action="index.php?act=buy_cinepass_sub" method="post">
                    <input type="hidden" name="sub_type" value="premium">
                    <input type="hidden" name="amount" value="250000">
                    <button type="submit" class="plan-btn"><?= __("Đăng ký Premium") ?></button>
                </form>
            </div>

        </div>

    </div>
</div>
<div class="clearfix"></div>
