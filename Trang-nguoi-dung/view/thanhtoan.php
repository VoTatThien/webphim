<?php

include 'view/search.php';

// ============ RELOAD THÔNG TIN USER TỪ DATABASE ============
// Để đảm bảo điểm tích lũy luôn là dữ liệu mới nhất
if (isset($_SESSION['user']) && $_SESSION['user']['vai_tro'] == 0) {
    require_once 'model/pdo.php';
    $user_updated = pdo_query_one("SELECT * FROM taikhoan WHERE id = ?", $_SESSION['user']['id']);
    if ($user_updated) {
        $_SESSION['user'] = $user_updated;
    }
}

// Get total price from session (seat price + combo price)
$gia_total = isset($_SESSION['tong']['gia_ghe']) ? $_SESSION['tong']['gia_ghe'] : 0;
$gia_goc = $gia_total; // Lưu giá gốc

// Xử lý mã khuyến mãi nếu có
$ma_giam_gia = '';
$giam_gia = 0;
$ten_km = '';
$error_km = '';

// Xử lý hủy mã khuyến mãi
if (isset($_POST['huy_ma'])) {
    unset($_SESSION['tong']['ma_khuyen_mai']);
    unset($_SESSION['tong']['giam_gia']);
    unset($_SESSION['tong']['gia_sau_giam']);
}

// ============ XỬ LÝ ĐỔI ĐIỂM ============
$diem_doi = 0;
$giam_gia_diem = 0;
$error_diem = '';

// Tỷ lệ quy đổi: 100,000 điểm = 10,000,000 VND (tức 100 VND = 1 điểm - nhất quán với cộng điểm)
define('TI_LE_DOI_DIEM', 100); // 1 điểm = 100 VND

// Xử lý hủy đổi điểm
if (isset($_POST['huy_diem'])) {
    unset($_SESSION['tong']['diem_doi']);
    unset($_SESSION['tong']['giam_gia_diem']);
}

// Xử lý áp dụng điểm
if (isset($_POST['ap_dung_diem']) && !empty($_POST['so_diem_doi'])) {
    // Kiểm tra user có đăng nhập và là thành viên không
    if (isset($_SESSION['user']) && $_SESSION['user']['vai_tro'] == 0) {
        require_once 'model/diem.php';
        
        $id_tk = (int)$_SESSION['user']['id'];
        $diem_hien_tai = (int)($_SESSION['user']['diem_tich_luy'] ?? 0);
        $diem_muon_doi = (int)$_POST['so_diem_doi'];
        
        // Validate
        if ($diem_muon_doi <= 0) {
            $error_diem = __('Số điểm phải lớn hơn 0!');
        } elseif ($diem_muon_doi > $diem_hien_tai) {
            $error_diem = __('Bạn không đủ điểm! Điểm hiện tại:') . ' ' . number_format($diem_hien_tai);
        } elseif ($diem_muon_doi < 1000) {
            $error_diem = __('Tối thiểu phải đổi 1,000 điểm (= 10,000 VND)');
        } else {
            // Tính số tiền giảm
            $giam_gia_diem = (int)($diem_muon_doi * TI_LE_DOI_DIEM);
            
            // Không được giảm quá tổng tiền
            if ($giam_gia_diem > $gia_total) {
                $giam_gia_diem = $gia_total;
                $diem_muon_doi = (int)ceil($giam_gia_diem / TI_LE_DOI_DIEM);
            }
            
            $diem_doi = $diem_muon_doi;
            
            // Lưu vào session
            $_SESSION['tong']['diem_doi'] = $diem_doi;
            $_SESSION['tong']['giam_gia_diem'] = $giam_gia_diem;
            
            // ⚠️ Chưa trừ điểm ngay - sẽ trừ sau khi thanh toán thành công
        }
    } else {
        $error_diem = __('Chỉ thành viên mới được đổi điểm!');
    }
} elseif (isset($_SESSION['tong']['diem_doi'])) {
    // Lấy thông tin đổi điểm từ session
    $diem_doi = $_SESSION['tong']['diem_doi'];
    $giam_gia_diem = $_SESSION['tong']['giam_gia_diem'];
}

// Áp dụng cả mã khuyến mãi và điểm
$tong_giam_gia = $giam_gia + $giam_gia_diem;

if (isset($_POST['ap_dung_ma']) && !empty($_POST['ma_khuyen_mai'])) {
    require_once __DIR__ . '/../../Trang-admin/model/khuyenmai.php';
    
    $ma_code = trim($_POST['ma_khuyen_mai']);
    $id_rap = $_SESSION['tong']['id_rap'] ?? null;
    
    // Tìm mã khuyến mãi
    $km = km_find_by_code($ma_code);
    
    if ($km) {
        // Kiểm tra mã có thuộc rạp này không
        if ($km['id_rap'] === null || $km['id_rap'] == $id_rap) {
            $ma_giam_gia = $ma_code;
            $ten_km = $km['ten_khuyen_mai'];
            $giam_gia = km_calculate_discount($km, $gia_total);
            
            // Cập nhật giá sau giảm
            $gia_total = max(0, $gia_total - $giam_gia);
            
            // Lưu vào session
            $_SESSION['tong']['ma_khuyen_mai'] = $ma_code;
            $_SESSION['tong']['giam_gia'] = $giam_gia;
            $_SESSION['tong']['gia_sau_giam'] = $gia_total;
        } else {
            $error_km = __('Mã khuyến mãi không áp dụng cho rạp này!');
        }
    } else {
        $error_km = __('Mã khuyến mãi không hợp lệ hoặc đã hết hạn!');
    }
} elseif (isset($_SESSION['tong']['giam_gia'])) {
    // Lấy thông tin giảm giá từ session nếu đã áp dụng trước đó
    $giam_gia = $_SESSION['tong']['giam_gia'];
    $ma_giam_gia = $_SESSION['tong']['ma_khuyen_mai'] ?? '';
    $gia_total = $_SESSION['tong']['gia_sau_giam'] ?? $gia_total;
}

// Cập nhật giá cuối cùng sau khi trừ cả mã KM và điểm
$gia_total = max(0, $gia_total - $tong_giam_gia);
$_SESSION['tong']['gia_sau_giam'] = $gia_total;

$gia = number_format($gia_total, 0, ',', '.');

?>

<style>
    /* Cải thiện font và giao diện */
    .checkout-wrapper {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #1c181c;
        border: 1px solid #363033;
        padding: 30px;
        border-radius: 12px;
    }
    
    .page-heading {
        font-size: 24px;
        font-weight: 600;
        color: #ffd564;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid #ffd564;
    }
    
    .book-result {
        list-style: none;
        padding: 0;
    }
    
    .book-result__item {
        padding: 12px 0;
        font-size: 16px;
        color: #e5e0e3;
        border-bottom: 1px solid #363033;
    }
    
    .book-result__count {
        float: right;
        font-weight: 600;
        color: #ffd564;
    }
    
    /* Style cho ô nhập mã khuyến mãi */
    .promo-section {
        background: #232023;
        border: 1px solid #4a3e43;
        border-left: 4px solid #ffd564;
        padding: 25px;
        border-radius: 12px;
        margin: 20px 0;
    }
    
    .promo-title {
        color: #ffd564;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .promo-input-group {
        display: flex;
        gap: 10px;
    }
    
    .promo-input {
        flex: 1;
        padding: 12px 15px;
        background: #1c181c;
        border: 1px solid #4a3e43;
        color: white;
        border-radius: 8px;
        font-size: 16px;
        text-transform: uppercase;
        font-weight: 600;
        outline: none;
        transition: all 0.3s;
    }
    
    .promo-input::placeholder {
        color: #7a7075;
    }
    
    .promo-input:focus {
        border-color: #ffd564;
        box-shadow: 0 0 0 3px rgba(255, 213, 100, 0.2);
    }
    
    .promo-btn {
        padding: 12px 30px;
        background: #ffd564;
        color: #4c4145;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 16px;
    }
    
    .promo-btn:hover {
        background: #ffe08d;
        transform: translateY(-2px);
    }
    
    .promo-error {
        color: #ff6b6b;
        background: #2d1818;
        border: 1px solid #f56565;
        padding: 10px 15px;
        border-radius: 6px;
        margin-top: 10px;
        font-size: 14px;
    }
    
    .promo-success {
        color: #51cf66;
        background: #1c2e24;
        border: 1px solid #2f855a;
        padding: 10px 15px;
        border-radius: 6px;
        margin-top: 10px;
        font-size: 14px;
        font-weight: 600;
    }
    
    .price-breakdown {
        background: #232023;
        border: 1px solid #363033;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 16px;
        color: #e5e0e3;
    }
    
    .price-row.total {
        border-top: 1px solid #363033;
        margin-top: 10px;
        padding-top: 15px;
        font-size: 20px;
        font-weight: bold;
        color: #fe505a;
    }
    
    .discount-row {
        color: #51cf66;
        font-weight: 600;
    }
</style>
<!-- Main content -->

<section class="container">
    <div class="order-container">
        <div class="order">
            <img class="order__images" alt='' src="images/tickets.png">
            <p class="order__title"><?= __("Đặt vé xem phim") ?> <br><span class="order__descript"><?= __("Tận Hưởng Thời Gian Xem Phim Vui Vẻ") ?></span></p>
        </div>
    </div>
    <div class="order-step-area">
        <div class="order-step first--step order-step--disable "><?= __("1. Lịch Chiếu &amp; Thời gian") ?></div>
        <div class="order-step second--step order-step--disable"><?= __("2. Chọn ghế") ?></div>
        <div class="order-step third--step"><?= __("3. Thanh Toán") ?> </div>
    </div>
    <form action="" method="post">
    <div class="col-sm-12">
        <div class="checkout-wrapper">
            <h2 class="page-heading"><?= __("Thông tin đặt vé") ?></h2>
            <ul class="book-result">
                <li class="book-result__item"><i class="fa fa-video-camera" style="color: #ffd564; margin-right: 8px;"></i> <?= __("Phim:") ?> <span class="book-result__count booking-cost"><?php echo htmlspecialchars(__($_SESSION['tong']['tieu_de'])) ?></span></li>
                
                <li class="book-result__item"><i class="fa fa-film" style="color: #ffd564; margin-right: 8px;"></i> <?= __("Rạp chiếu:") ?> <span class="book-result__count booking-cost"><?php echo isset($_SESSION['tong']['ten_rap']) ? htmlspecialchars(__($_SESSION['tong']['ten_rap'])) : 'N/A' ?></span></li>
                
                <li class="book-result__item"><i class="fa fa-map-marker" style="color: #ffd564; margin-right: 8px;"></i> <?= __("Địa chỉ rạp:") ?> <span class="book-result__count booking-cost"><?php echo isset($_SESSION['tong']['dia_chi_rap']) ? htmlspecialchars($_SESSION['tong']['dia_chi_rap']) : 'N/A' ?></span></li>
                
                <li class="book-result__item"><i class="fa fa-desktop" style="color: #ffd564; margin-right: 8px;"></i> <?= __("Phòng chiếu:") ?> <span class="book-result__count booking-cost"><?php echo isset($_SESSION['tong']['ten_phong']) ? htmlspecialchars($_SESSION['tong']['ten_phong']) : 'N/A' ?></span></li>

                <li class="book-result__item"><i class="fa fa-calendar" style="color: #ffd564; margin-right: 8px;"></i> <?= __("Ngày chiếu:") ?> <span class="book-result__count booking-cost"><?php echo $_SESSION['tong']['ngay_chieu'] ?></span></li>
                
                <li class="book-result__item"><i class="fa fa-clock-o" style="color: #ffd564; margin-right: 8px;"></i> <?= __("Khung giờ chiếu:") ?> <span class="book-result__count booking-cost"><?php echo $_SESSION['tong']['thoi_gian_chieu'] ?></span></li>
                <br>
                <hr style="border-color: #363033;">
                <li class="book-result__item"><i class="fa fa-circle-o" style="color: #ffd564; margin-right: 8px;"></i> <?= __("Số ghế:") ?> <span class="book-result__count booking-cost"><?php
                        if (isset($ten_ghe['ghe'])) {
                            $ghes = $ten_ghe['ghe'];
                            echo '<span class="choosen-plac">' . implode(', ', $ghes) . '</span>';

                            foreach ($ghes as $ghe) {
                                echo '<input type="hidden" name="ten_ghe[]" value="' . $ghe . '">';
                            }
                        }
                        ?>
</span></li>
                <li class="book-result__item"><i class="fa fa-coffee" style="color: #ffd564; margin-right: 8px;"></i> <?= __("Combo:") ?> <span class="book-result__count booking-cost"><span class="check-doan"> <?php
                            if (isset($ten_doan['doan'])) {
                                foreach ($ten_doan['doan'] as $doan) {
                                    echo  '<span class="check-doan">' . $doan . '</span>';

                                }
                            } else {
                            } ?></span>
</span></li>
            </ul>
            
            <!-- Mã khuyến mãi -->
            <form method="post" style="margin: 0;">
                <div class="promo-section">
                    <div class="promo-title">
                        <i class="fa fa-gift" style="color: #ffd564;"></i> <?= __("Bạn có mã khuyến mãi?") ?>
                    </div>
                    <div class="promo-input-group">
                        <input type="text" 
                               name="ma_khuyen_mai" 
                               class="promo-input" 
                               placeholder="<?= __("Nhập mã khuyến mãi") ?>" 
                               value="<?php echo htmlspecialchars($ma_giam_gia); ?>"
                               <?php echo $ma_giam_gia ? 'readonly' : ''; ?>>
                        <?php if (!$ma_giam_gia): ?>
                            <button type="submit" name="ap_dung_ma" class="promo-btn"><?= __("Áp dụng") ?></button>
                        <?php else: ?>
                            <button type="submit" name="huy_ma" class="promo-btn" style="background: #ff6b6b; color: white;"><?= __("Hủy mã") ?></button>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($error_km): ?>
                        <div class="promo-error"><i class="fa fa-exclamation-circle"></i> <?php echo $error_km; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($ma_giam_gia && $giam_gia > 0): ?>
                        <div class="promo-success">
                            <i class="fa fa-check-circle"></i> <?= __("Đã áp dụng mã:") ?> <?php echo strtoupper($ma_giam_gia); ?>
                            <?php if ($ten_km): ?>
                                (<?php echo $ten_km; ?>)
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
            
            <!-- Đổi điểm tích lũy -->
            <?php if (isset($_SESSION['user']) && $_SESSION['user']['vai_tro'] == 0): ?>
            <form method="post" style="margin: 0;">
                <div class="promo-section">
                    <div class="promo-title">
                        <i class="fa fa-star" style="color: #ffd564;"></i> <?= __("Đổi điểm tích lũy") ?>
                        <span style="font-size: 14px; font-weight: normal; margin-left: auto; color: #a59b9f;">
                            <?= __("Điểm hiện tại:") ?> <strong style="color: #ffd564;"><?php echo number_format($_SESSION['user']['diem_tich_luy'] ?? 0); ?></strong> <?= __("điểm") ?>
                        </span>
                    </div>
                    <div style="color: #a59b9f; font-size: 13px; margin-bottom: 10px;">
                        <i class="fa fa-info-circle" style="color: #ffd564;"></i> <?= __("Tỷ lệ đổi: 100,000 điểm = 10,000,000 VND (100 VND = 1 điểm) | Tối thiểu: 1,000 điểm") ?>
                    </div>
                    <div class="promo-input-group">
                        <input type="number" 
                               name="so_diem_doi" 
                               class="promo-input" 
                               placeholder="<?= __("Nhập số điểm muốn đổi") ?>" 
                               min="1000"
                               step="100"
                               value="<?php echo $diem_doi; ?>"
                               <?php echo $diem_doi ? 'readonly' : ''; ?>>
                        <?php if (!$diem_doi): ?>
                            <button type="submit" name="ap_dung_diem" class="promo-btn"><?= __("Đổi điểm") ?></button>
                        <?php else: ?>
                            <button type="submit" name="huy_diem" class="promo-btn" style="background: #ff6b6b; color: white;"><?= __("Hủy đổi") ?></button>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($error_diem): ?>
                        <div class="promo-error"><i class="fa fa-exclamation-circle"></i> <?php echo $error_diem; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($diem_doi > 0 && $giam_gia_diem > 0): ?>
                        <div class="promo-success">
                            <i class="fa fa-check-circle"></i> <?= __("Đã đổi") ?> <?php echo number_format($diem_doi); ?> <?= __("điểm") ?> 
                            → <?= __("Giảm") ?> <?php echo number_format($giam_gia_diem); ?> VND
                        </div>
                    <?php endif; ?>
                </div>
            </form>
            <?php endif; ?>
            
            <!-- Chi tiết giá -->
            <div class="price-breakdown">
                <div class="price-row">
                    <span><?= __("Tổng tiền vé:") ?></span>
                    <span><?php echo number_format($gia_goc, 0, ',', '.'); ?> VND</span>
                </div>
                
                <?php if ($giam_gia > 0): ?>
                <div class="price-row discount-row">
                    <span><i class="fa fa-tag" style="color: #51cf66; margin-right: 5px;"></i> <?= __("Mã khuyến mãi:") ?></span>
                    <span>- <?php echo number_format($giam_gia, 0, ',', '.'); ?> VND</span>
                </div>
                <?php endif; ?>
                
                <?php if ($giam_gia_diem > 0): ?>
                <div class="price-row discount-row" style="color: #fe505a;">
                    <span><i class="fa fa-star" style="color: #fe505a; margin-right: 5px;"></i> <?= __("Đã đổi") ?> <?php echo number_format($diem_doi); ?> <?= __("điểm") ?>:</span>
                    <span>- <?php echo number_format($giam_gia_diem, 0, ',', '.'); ?> VND</span>
                </div>
                <?php endif; ?>
                
                <?php if ($tong_giam_gia > 0): ?>
                <div class="price-row" style="color: #51cf66; font-weight: 600;">
                    <span><i class="fa fa-money" style="color: #51cf66; margin-right: 5px;"></i> <?= __("Tổng tiết kiệm:") ?></span>
                    <span>- <?php echo number_format($tong_giam_gia, 0, ',', '.'); ?> VND</span>
                </div>
                <?php endif; ?>
                
                <div class="price-row total">
                    <span><i class="fa fa-credit-card" style="color: #fe505a; margin-right: 5px;"></i> <?= __("Số tiền thanh toán:") ?></span>
                    <span><?php echo $gia; ?> VND</span>
                </div>
            </div>
    </form>

            <h2 class="page-heading"><?= __("Chọn hình thức thanh toán") ?></h2>
            <form action="" method="post">
                <!-- Hidden fields to pass data -->
                <input type="hidden" name="gia_thanh_toan" value="<?php echo $gia_total; ?>">
                <?php if ($ma_giam_gia): ?>
                    <input type="hidden" name="ma_khuyen_mai_applied" value="<?php echo $ma_giam_gia; ?>">
                    <input type="hidden" name="giam_gia_applied" value="<?php echo $giam_gia; ?>">
                <?php endif; ?>
                
                <style>
                    .payment-methods-container {
                        background: #1c181c;
                        border: 1px solid #363033;
                        padding: 30px;
                        border-radius: 12px;
                        margin: 20px 0;
                    }

                    .payment-methods-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                        gap: 20px;
                        margin-top: 20px;
                    }

                    .payment-method-card {
                        background: #232023;
                        border: 2px solid #363033;
                        border-radius: 12px;
                        padding: 20px;
                        text-align: center;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        text-decoration: none;
                        color: white;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                    }

                    .payment-method-card:hover {
                        border-color: #ffd564;
                        transform: translateY(-8px);
                        box-shadow: 0 12px 30px rgba(255, 213, 100, 0.15);
                    }

                    .payment-method-icon {
                        width: 80px;
                        height: 80px;
                        border-radius: 12px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 40px;
                        font-weight: bold;
                        margin-bottom: 12px;
                    }

                    .payment-method-name {
                        font-weight: 700;
                        font-size: 16px;
                        margin-bottom: 6px;
                    }

                    .payment-method-desc {
                        font-size: 12px;
                        color: #9ca3af;
                    }

                    /* Zalopay */
                    .payment-zalopay .payment-method-icon {
                        background: linear-gradient(135deg, #0068FF 0%, #00A7FF 100%);
                        color: white;
                    }

                    .payment-zalopay .payment-method-name {
                        color: #0068FF;
                    }

                    .payment-zalopay:hover {
                        background: linear-gradient(135deg, #0068FF10 0%, #00A7FF10 100%);
                    }

                    /* MoMo */
                    .payment-momo .payment-method-icon {
                        background: linear-gradient(135deg, #C41E3A 0%, #A50064 100%);
                        color: white;
                    }

                    .payment-momo .payment-method-name {
                        color: #A50064;
                    }

                    .payment-momo:hover {
                        background: linear-gradient(135deg, #A5006415 0%, #C41E3A15 100%);
                    }

                    /* VietQR */
                    .payment-vietqr .payment-method-icon {
                        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
                        color: white;
                    }

                    .payment-vietqr .payment-method-name {
                        color: #1e40af;
                    }

                    .payment-vietqr:hover {
                        background: linear-gradient(135deg, #1e40af15 0%, #3b82f615 100%);
                    }

                    /* Sepay */
                    .payment-sepay .payment-method-icon {
                        background: linear-gradient(135deg, #059669 0%, #047857 100%);
                        color: white;
                    }

                    .payment-sepay .payment-method-name {
                        color: #059669;
                    }

                    .payment-sepay:hover {
                        background: linear-gradient(135deg, #05966915 0%, #04785715 100%);
                    }

                    /* PayOS */
                    .payment-payos .payment-method-icon {
                        background: linear-gradient(135deg, #00D4FF 0%, #0099CC 100%);
                        color: white;
                    }

                    .payment-payos .payment-method-name {
                        color: #00D4FF;
                    }

                    /* MoMo QR */
                    .payment-momo .payment-method-icon {
                        background: linear-gradient(135deg, #e91e63 0%, #c2185b 100%);
                    }

                    .payment-momo .payment-method-name {
                        color: #e91e63;
                    }

                    .payment-momo:hover {
                        background: linear-gradient(135deg, #e91e6315 0%, #c2185b15 100%);
                    }

                    .payment-info {
                        background: #f0f4ff;
                        border-left: 4px solid #667eea;
                        padding: 15px;
                        border-radius: 6px;
                        margin-bottom: 20px;
                        font-size: 14px;
                        color: #333;
                    }

                    .payment-info strong {
                        color: #667eea;
                    }
                </style>

                <div class="payment-info">
                    ℹ️ <strong><?= __("Lưu ý:") ?></strong> <?= __("Chọn phương thức thanh toán bên dưới để tiếp tục") ?>
                </div>

                <div class="payment-methods-container">
                    <?php
                    $user = $_SESSION['user'] ?? null;
                    $has_sub = ($user && isset($user['cinepass_sub_status']) && $user['cinepass_sub_status'] == 1);
                    $tickets_left = $has_sub ? (int)$user['cinepass_tickets_left'] : 0;
                    $seats_count = isset($ten_ghe) ? count($ten_ghe) : 0;
                    $can_pay_with_cinepass = ($has_sub && $tickets_left >= $seats_count && $seats_count > 0);
                    ?>
                    <div class="payment-methods-grid">
                        <?php if ($can_pay_with_cinepass): ?>
                        <!-- Thẻ hội viên CinePass -->
                        <button type="button" onclick="initiateCinePassPayment()" class="payment-method-card payment-cinepass" style="cursor: pointer; border: 2px solid #ffd564; background: linear-gradient(135deg, #2a2213 0%, #151216 100%); padding: 20px; text-align: center; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%;">
                            <div class="payment-method-icon" style="background: linear-gradient(135deg, #ffd564 0%, #ff9f43 100%); color: #151216; width: 80px; height: 80px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 40px; margin-bottom: 12px;">💳</div>
                            <div class="payment-method-name" style="color: #ffd564; font-weight: 700; font-size: 16px; margin-bottom: 6px;">CinePass Card</div>
                            <div class="payment-method-desc" style="font-size: 12px; color: #fff;"><?= __("Còn") ?> <?= $tickets_left ?> <?= __("vé free") ?></div>
                        </button>
                        <?php endif; ?>

                        <!-- Sepay (Chuyển khoản ngân hàng) -->
                        <button type="button" onclick="initiateSepayPayment()" class="payment-method-card payment-sepay" style="cursor: pointer; border: none; background: none; padding: 0; text-align: center;">
                            <div class="payment-method-icon">🏦</div>
                            <div class="payment-method-name">Sepay</div>
                            <div class="payment-method-desc"><?= __("QR Chuyển khoản") ?></div>
                        </button>

                        <!-- MoMo QR -->
                        <button type="button" onclick="initiateMoMoPayment()" class="payment-method-card payment-momo" style="cursor: pointer; border: none; background: none; padding: 0; text-align: center;">
                            <div class="payment-method-icon">📱</div>
                            <div class="payment-method-name">MoMo QR</div>
                            <div class="payment-method-desc"><?= __("Quét mã QR") ?></div>
                        </button>

                        <!-- VietQR -->
                        <button type="button" onclick="initiateVietQRPayment()" class="payment-method-card payment-vietqr" style="cursor: pointer; border: none; background: none; padding: 0; text-align: center;">
                            <div class="payment-method-icon">🏦</div>
                            <div class="payment-method-name">VietQR</div>
                            <div class="payment-method-desc"><?= __("Chuyển tiền") ?></div>
                        </button>
                    </div>
                </div>
            </form>

        </div>

    </div>

</section>

</form>
<div class="clearfix"></div>


<div class="clearfix"></div>

<script>
// Determine base path dynamically
const matches = window.location.pathname.match(/^(.+)\/(Trang-nguoi-dung|Trang-admin)/);
const basePath = matches ? matches[1] : '';

/**
 * Xử lý thanh toán qua Thẻ hội viên CinePass
 */
function initiateCinePassPayment() {
    if (confirm('<?= __("Bạn có chắc chắn muốn sử dụng vé hội viên CinePass để đặt các ghế này (0 VND)?") ?>')) {
        window.location.href = basePath + '/Trang-nguoi-dung/index.php?act=xacnhan&pay_method=cinepass';
    }
}

/**
 * Xử lý thanh toán Sepay (Chuyển khoản)
 */
function initiateSepayPayment() {
    const amount = <?php echo (int)$gia_total; ?>;
    
    console.log('🔍 Sepay Amount:', amount);
    
    if (amount <= 0) {
        alert('<?= __("Số tiền không hợp lệ! Vui lòng kiểm tra đơn đặt hàng của bạn.") ?>');
        console.error('❌ Invalid amount:', amount);
        return;
    }
    
    // Lấy ticket_id từ session (giả sử đã được lưu)
    // Nếu không có, sử dụng timestamp làm ID tạm thời
    const ticket_id = <?php echo isset($_SESSION['ticket_id']) ? $_SESSION['ticket_id'] : 'Math.floor(Date.now() / 1000)'; ?>;
    
    console.log('✅ Redirecting to Sepay payment...');
    // Redirect tới Sepay payment UI - Dùng absolute path từ root
    window.location.href = `${basePath}/Trang-nguoi-dung/sepay/sepay_payment_ui.php?ticket_id=${ticket_id}&amount=${amount}`;
}

/**
 * Xử lý thanh toán MoMo ATM (Redirect trực tiếp)
 */
function initiateMoMoPayment() {
    const amount = <?php echo (int)$gia_total; ?>;
    
    console.log('🔍 MoMo Amount:', amount);
    
    if (amount <= 0) {
        alert('<?= __("Số tiền không hợp lệ! Vui lòng kiểm tra đơn đặt hàng của bạn.") ?>');
        console.error('❌ Invalid amount:', amount);
        return;
    }
    
    console.log('✅ Redirecting to MoMo payment...');
    // Redirect trực tiếp tới xử lý MoMo ATM
    window.location.href = basePath + '/Trang-nguoi-dung/view/momo/xuly_momo_atm.php';
}

/**
 * Xử lý thanh toán VietQR bằng AJAX
 * Redirect tới trang checkout
 */
function initiateVietQRPayment() {
    const amount = <?php echo $gia_total; ?>;
    
    if (amount < 10000) {
        alert('<?= __("Số tiền thanh toán phải tối thiểu 10,000 VND") ?>');
        return;
    }
    
    const btn = event.target.closest('button');
    const originalText = btn.innerText;
    btn.disabled = true;
    btn.innerText = '<?= __("Đang tạo QR...") ?>';
    
    fetch(basePath + '/Trang-nguoi-dung/api_create_vietqr_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            amount: amount,
            description: 'Ve phim CinePass'
        })
    })
    .then(response => response.json())
    .then(data => {
        btn.disabled = false;
        btn.innerText = originalText;
        
        if (data.error === 0 && data.data) {
            console.log('✅ VietQR created:', data.data);
            
            // Build checkout URL with all parameters
            const params = new URLSearchParams({
                orderId: data.data.orderId,
                amount: data.data.amount,
                bankName: data.data.bankName,
                bankCode: data.data.bankCode,
                accountNumber: data.data.accountNumber,
                accountName: data.data.accountName,
                description: data.data.description,
                qrCode: data.data.qrCode || ''
            });
            
            // Redirect tới trang checkout
            window.location.href = basePath + '/Trang-nguoi-dung/vietqr_checkout.php?' + params.toString();
        } else {
            const errorMsg = data.message || 'Không thể tạo QR VietQR';
            console.error('❌ Error:', data);
            alert('Lỗi: ' + errorMsg);
        }
    })
    .catch(error => {
        btn.disabled = false;
        btn.innerText = originalText;
        console.error('❌ Fetch error:', error);
        alert('Lỗi kết nối: ' + error.message);
    });
}
</script>