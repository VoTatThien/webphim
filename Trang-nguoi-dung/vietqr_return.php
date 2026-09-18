<?php
/**
 * VietQR Payment Return/Confirmation Page
 * Auto-create vé and hóa đơn after user confirms payment
 */

session_start();

// Get parameters
$orderId = $_GET['orderId'] ?? '';
$amount = (int)($_GET['amount'] ?? 0);
$status = $_GET['status'] ?? 'pending';

// ====================================================
// CREATE TỰ ĐỘNG: VÉ + HÓA ĐƠN
// ====================================================

if ($status === 'confirmed' && $amount > 0 && !empty($orderId)) {
    header('Location: index.php?act=xacnhan&vietqr=1');
    exit;
} else {
    $success = false;
    $message = "Thanh toán bị hủy hoặc không hợp lệ.";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? "Thanh Toán Thành Công" : "Thanh Toán Thất Bại"; ?> - CinePass</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .content {
            padding: 40px 30px;
            text-align: center;
        }

        .icon {
            font-size: 64px;
            margin-bottom: 20px;
            animation: bounce 0.6s ease-out;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }

        .message {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            color: #333;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }

        .buttons {
            display: flex;
            gap: 12px;
            flex-direction: column;
        }

        button {
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: #e9ecef;
            color: #495057;
            border: 2px solid #dee2e6;
        }

        .btn-secondary:hover {
            background: #dee2e6;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #f5c6cb;
        }

        .error h1 {
            color: #721c24;
        }

        .success {
            background: transparent;
        }

        .success h1 {
            color: #28a745;
        }

        @media (max-width: 480px) {
            .content {
                padding: 30px 20px;
            }

            h1 {
                font-size: 24px;
            }

            .icon {
                font-size: 48px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="content <?php echo $success ? 'success' : 'error'; ?>">
            <!-- Icon -->
            <div class="icon">
                <?php echo $success ? '✓' : '✕'; ?>
            </div>

            <!-- Title -->
            <h1>
                <?php echo $success ? 'Thanh Toán Thành Công!' : 'Thanh Toán Thất Bại'; ?>
            </h1>

            <!-- Message -->
            <div class="message">
                <?php echo $message; ?>
            </div>

            <!-- Details (if success) -->
            <?php if ($success): ?>
            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Mã Đơn Hàng</span>
                    <span class="detail-value"><?php echo htmlspecialchars($orderId); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Số Tiền</span>
                    <span class="detail-value"><?php echo number_format($amount, 0, ',', '.'); ?> ₫</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phương Thức</span>
                    <span class="detail-value">VietQR</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Hóa Đơn</span>
                    <span class="detail-value">#<?php echo $hoadon_id ?? '---'; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Điểm Thưởng</span>
                    <span class="detail-value">+<?php echo (int)($amount / 10000); ?> đ</span>
                </div>
            </div>
            <?php endif; ?>

            <!-- Buttons -->
            <div class="buttons">
                <a href="index.php?p=ve_cua_toi">
                    <button class="btn-primary">📽️ Xem Vé Của Tôi</button>
                </a>
                <a href="index.php">
                    <button class="btn-secondary">← Quay Lại Trang Chủ</button>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
