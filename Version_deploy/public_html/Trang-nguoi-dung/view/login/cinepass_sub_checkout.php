<?php
// Ensure search is loaded if needed
include 'view/search.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo '<script>alert("' . __("Bạn cần đăng nhập để thực hiện giao dịch!") . '"); window.location.href="index.php?act=dangnhap";</script>';
    exit;
}

$user = $_SESSION['user'];
$sub_type = isset($_GET['sub_type']) ? $_GET['sub_type'] : (isset($_POST['sub_type']) ? $_POST['sub_type'] : 'standard');
$amount = (int)(isset($_GET['amount']) ? $_GET['amount'] : (isset($_POST['amount']) ? $_POST['amount'] : 150000));
$plan_name = ($sub_type === 'premium') ? 'CinePass Premium' : 'CinePass Standard';

// Generate MB Bank QR details
$bank_code = 'MB';
$account_number = '123456789';
$account_name = 'CINEPASS STUDIO';
$description = 'CINEPASS SUB ' . strtoupper($user['user']) . ' ' . strtoupper($sub_type);

// URL encode details for VietQR API
$qr_url = "https://img.vietqr.io/image/{$bank_code}-{$account_number}-compact2.png?amount={$amount}&addInfo=" . urlencode($description) . "&accountName=" . urlencode($account_name);
?>

<style>
    .sub-checkout-wrapper {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #151216;
        color: #fff;
        padding: 50px 20px;
        min-height: 85vh;
    }

    .checkout-container {
        max-width: 900px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(10px);
    }

    .checkout-title {
        text-align: center;
        margin-bottom: 40px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        padding-bottom: 20px;
    }

    .checkout-title h2 {
        color: #ffd564;
        font-size: 28px;
        font-weight: 700;
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 1px;
    }

    .checkout-title p {
        color: #9ca3af;
        margin: 10px 0 0 0;
        font-size: 15px;
    }

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Chi tiết đơn hàng */
    .order-summary-box {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 25px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 15px;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.08);
        padding-bottom: 10px;
    }

    .summary-item:last-of-type {
        border-bottom: none;
    }

    .summary-item span:first-child {
        color: #9ca3af;
    }

    .summary-item span:last-child {
        font-weight: 600;
    }

    .total-price-box {
        background: rgba(254, 80, 90, 0.1);
        border: 1px solid rgba(254, 80, 90, 0.2);
        border-radius: 8px;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }

    .total-price-box span:first-child {
        font-size: 16px;
        font-weight: 600;
        color: #fe505a;
    }

    .total-price-box span:last-child {
        font-size: 24px;
        font-weight: 700;
        color: #fe505a;
    }

    /* Thanh toán chuyển khoản */
    .qr-payment-box {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }

    .qr-image-container {
        background: #fff;
        padding: 15px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        display: inline-block;
        border: 3px solid #ffd564;
    }

    .qr-image-container img {
        display: block;
        max-width: 240px;
        height: auto;
    }

    .bank-details-card {
        width: 100%;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 15px 20px;
        text-align: left;
        font-size: 14px;
    }

    .bank-details-card table {
        width: 100%;
        border-collapse: collapse;
    }

    .bank-details-card td {
        padding: 6px 0;
        vertical-align: top;
    }

    .bank-details-card td:first-child {
        color: #9ca3af;
        width: 130px;
    }

    .bank-details-card td:last-child {
        color: #fff;
        font-weight: 600;
    }

    /* Loader kiểm tra giao dịch */
    .transaction-loader {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        color: #ffd564;
        font-size: 14px;
        font-weight: 600;
        margin-top: 15px;
    }

    .spinner {
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 213, 100, 0.2);
        border-top-color: #ffd564;
        border-radius: 50%;
        animation: spin 1s infinite linear;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .activate-btn {
        width: 100%;
        background: linear-gradient(135deg, #ffd564 0%, #ff9f43 100%);
        color: #151216;
        border: none;
        padding: 14px;
        font-size: 16px;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        text-transform: uppercase;
        box-shadow: 0 4px 15px rgba(255, 213, 100, 0.3);
        transition: transform 0.2s, box-shadow 0.2s;
        margin-top: 10px;
    }

    .activate-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 213, 100, 0.4);
    }
</style>

<div class="sub-checkout-wrapper">
    <div class="checkout-container">
        
        <div class="checkout-title">
            <h2><?= __("Thanh toán gói hội viên") ?></h2>
            <p><?= __("Quét mã VietQR hoặc chuyển khoản thủ công để hoàn tất kích hoạt") ?></p>
        </div>

        <div class="checkout-grid">
            
            <!-- Cột trái: Thông tin gói & hướng dẫn -->
            <div class="order-summary-box">
                <div>
                    <h3 style="color: #ffd564; margin-top: 0; font-size: 18px; margin-bottom: 20px;"><i class="fa fa-shopping-basket"></i> <?= __("Thông tin đăng ký") ?></h3>
                    
                    <div class="summary-item">
                        <span><?= __("Tài khoản thành viên:") ?></span>
                        <span><?= htmlspecialchars($user['user']) ?></span>
                    </div>
                    
                    <div class="summary-item">
                        <span><?= __("Email liên lạc:") ?></span>
                        <span><?= htmlspecialchars($user['email']) ?></span>
                    </div>

                    <div class="summary-item">
                        <span><?= __("Gói hội viên:") ?></span>
                        <span style="color: #ffd564;"><?= $plan_name ?></span>
                    </div>

                    <div class="summary-item">
                        <span><?= __("Thời hạn sử dụng:") ?></span>
                        <span>30 <?= __("ngày") ?></span>
                    </div>

                    <div class="summary-item">
                        <span><?= __("Quyền lợi:") ?></span>
                        <span style="text-align: right; font-size: 13px; max-width: 200px;">
                            <?= ($sub_type === 'premium') ? '5 vé free, 2 combo free, +3,000 điểm' : '3 vé free, +1,500 điểm' ?>
                        </span>
                    </div>
                </div>

                <div>
                    <div class="total-price-box">
                        <span><?= __("Tổng tiền cần trả:") ?></span>
                        <span><?= number_format($amount) ?>đ</span>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Chuyển khoản QR code -->
            <div class="qr-payment-box">
                <div class="qr-image-container">
                    <img src="<?= $qr_url ?>" alt="VietQR Code">
                </div>

                <div class="bank-details-card">
                    <table>
                        <tr>
                            <td><?= __("Ngân hàng:") ?></td>
                            <td>MB Bank (Ngân hàng Quân Đội)</td>
                        </tr>
                        <tr>
                            <td><?= __("Số tài khoản:") ?></td>
                            <td style="color: #ffd564; font-size: 16px; letter-spacing: 0.5px;">123456789</td>
                        </tr>
                        <tr>
                            <td><?= __("Chủ tài khoản:") ?></td>
                            <td>CINEPASS STUDIO</td>
                        </tr>
                        <tr>
                            <td><?= __("Nội dung CK:") ?></td>
                            <td style="color: #fe505a; font-family: monospace; font-size: 15px;"><?= htmlspecialchars($description) ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Loader giả lập chuyển khoản -->
                <div class="transaction-loader">
                    <div class="spinner"></div>
                    <span><?= __("Đang chờ xác nhận giao dịch chuyển khoản...") ?></span>
                </div>

                <!-- Form xác nhận thanh toán -->
                <form action="index.php?act=activate_cinepass_sub" method="post" style="width: 100%;">
                    <input type="hidden" name="sub_type" value="<?= htmlspecialchars($sub_type) ?>">
                    <input type="hidden" name="amount" value="<?= $amount ?>">
                    <button type="submit" class="activate-btn">
                        <i class="fa fa-check-circle"></i> <?= __("Xác nhận đã chuyển khoản (Simulate)") ?>
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
<div class="clearfix"></div>
