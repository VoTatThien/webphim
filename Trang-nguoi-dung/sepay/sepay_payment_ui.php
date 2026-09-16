<?php
/**
 * Sepay Payment UI
 * Hiển thị QR code khi user chọn thanh toán Sepay
 * Được gọi từ: Trang-nguoi-dung/view/thanhtoan.php (payment method selection)
 */

session_start();

// Giả sử $ticket_id và $amount được truyền từ thanhtoan.php
if (!isset($_GET['ticket_id']) || !isset($_GET['amount'])) {
    die('Missing parameters');
}

$ticket_id = (int)$_GET['ticket_id'];
$amount = (int)$_GET['amount'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán bằng Sepay - Galaxy Studio</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #f8f8f8ff 0%, #ffffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container { 
            max-width: 900px; 
            width: 100%;
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .payment-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .payment-header {
            background: linear-gradient(135deg, #eb4949ff 0%, #e92b2bff 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .payment-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }
        
        .payment-header h1 { 
            font-size: 28px; 
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        
        .payment-header p { 
            font-size: 14px; 
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }
        
        .payment-content { 
            padding: 30px; 
            display: flex;
            gap: 30px;
        }
        
        /* Cột trái - QR */
        .payment-left {
            flex: 0 0 45%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        /* Cột phải - Thông tin */
        .payment-right {
            flex: 1;
        }
        
        .qr-container {
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
            width: 100%;
        }
        
        .qr-container > p {
            margin-bottom: 15px; 
            color: #666;
            font-weight: 500;
            font-size: 13px;
        }
        
        .qr-image {
            max-width: 352px;
            width: 100%;
            margin: 0 auto;
            background: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            animation: scaleIn 0.6s ease-out;
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .qr-image img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        
        .payment-details {
            background: #f9fafb;
            padding: 18px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #e0e0e0;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 10px 0;
            font-size: 13px;
            padding: 6px 0;
        }
        
        .detail-row:not(:last-child) {
            border-bottom: 1px solid #eee;
        }
        
        .detail-row strong { 
            color: #333;
            font-weight: 600;
        }
        
        .detail-row span { 
            color: #667eea; 
            font-weight: bold;
            font-size: 14px;
        }
        
        .info-box {
            background: #eff6ff;
            border-left: 4px solid #667eea;
            padding: 14px;
            border-radius: 6px;
            margin: 12px 0;
            font-size: 12px;
            line-height: 1.7;
        }
        
        .info-box strong { 
            color: #667eea;
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
        }
        
        .info-box div {
            margin: 4px 0;
            color: #333;
            font-size: 20px;
        }
        
        .status-message {
            padding: 14px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
            display: none;
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .status-message.loading {
            background: #e3f2fd;
            color: #1976d2;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 14px;
        }
        
        .loading-spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(25, 118, 210, 0.3);
            border-top-color: #1976d2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .status-message.success {
            background: #e8f5e9;
            color: #388e3c;
            display: block;
            border-left: 5px solid #4caf50;
            font-size: 13px;
        }
        
        .status-message.error {
            background: #ffebee;
            color: #c62828;
            display: block;
            border-left: 5px solid #f44336;
            font-size: 13px;
        }
        
        .action-buttons {
            margin-top: 20px;
            text-align: center;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 11px 28px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover { 
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        
        .btn-secondary:hover { 
            background: #e0e0e0;
            transform: translateY(-2px);
        }
        
        .instructions {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            border-left: 5px solid #f57c00;
            padding: 16px;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 15px;
            line-height: 1.7;
            width: 400px;
        }
        
        .instructions strong { 
            color: #f57c00;
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .instructions ol {
            margin-left: 20px;
            color: #666;
        }
        
        .instructions li {
            margin: 6px 0;
        }
        
        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        @media (max-width: 900px) {
            .payment-card {
                flex-direction: column;
            }
            
            .payment-header {
                flex: 1;
                padding: 30px 20px;
            }
            
            .payment-header h1 { font-size: 26px; }
            .payment-content { padding: 25px; }
            .qr-image { max-width: 300px; }
        }
        
        @media (max-width: 600px) {
            .container { max-width: 100%; }
            .payment-content { padding: 20px; }
            .payment-header { padding: 25px 15px; }
            .payment-header h1 { font-size: 22px; }
            .qr-image { max-width: 200px; }
            .action-buttons { flex-direction: column; }
            .btn { width: 100%; padding: 12px 20px; }
            .info-box { padding: 12px; font-size: 12px; }
            .instructions { padding: 12px; font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <div class="payment-header">
                <h1>Thanh toán bằng Chuyển khoản</h1>
                <p>Galaxy Studio Cinema - Trải nghiệm phim ảnh tuyệt vời</p>
            </div>

            <div class="payment-content">
                <!-- CỘT TRÁI: QR Code -->
                <div class="payment-left">
                    <div class="qr-container">
                        <p>Quét mã QR bằng app ngân hàng</p>
                        <div class="qr-image" id="qr-code-container">
                            <img id="sepay-qr-img" src="https://qr.sepay.vn/img?bank=MBBANK&acc=0384104942&template=compact&amount=<?= $amount ?>&des=VE<?= $ticket_id ?>" onerror="this.onerror=null; this.src='https://img.vietqr.io/image/MB-0384104942-compact.png?amount=<?= $amount ?>&addInfo=VE<?= $ticket_id ?>&accountName=GALAXY%20STUDIO';" alt="QR Code Thanh Toán" />
                        </div>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="instructions">
                        <strong>Hướng dẫn:</strong>
                        <ol>
                            <li>Mở app ngân hàng bất kỳ</li>
                            <li>Chọn "Quét mã QR"</li>
                            <li>Quét mã QR ở trên</li>
                            <li>Kiểm tra số tiền và nội dung</li>
                            <li>Xác nhận chuyển tiền trên app</li>
                            <li>Nhấn nút "✓ Tôi đã chuyển khoản xong" bên dưới</li>
                        </ol>
                    </div>
                </div>

                <!-- CỘT PHẢI: Thông tin -->
                <div class="payment-right">
                    <!-- Payment Details -->
                    <div class="payment-details">
                        <div class="detail-row">
                            <strong>🎟️ Mã vé:</strong>
                            <span>VE<?= $ticket_id ?></span>
                        </div>
                        <div class="detail-row">
                            <strong>💰 Số tiền cần chuyển:</strong>
                            <span style="color: #e11d48; font-size: 17px;"><?= number_format($amount, 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="detail-row">
                            <strong>✓ Trạng thái:</strong>
                            <span id="status-badge" style="color: #f59e0b;">⏳ Chờ quý khách chuyển khoản</span>
                        </div>
                    </div>

                    <!-- Bank Info -->
                    <div class="info-box">
                        <strong>🏧 Thông tin tài khoản nhận tiền:</strong>
                        <div style="font-size: 14px; line-height: 1.8;">
                            <div><strong>Ngân hàng:</strong> MB Bank (Quân Đội)</div>
                            <div><strong>Chủ tài khoản:</strong> GALAXY STUDIO</div>
                            <div><strong>Số tài khoản:</strong> <span style="font-family: monospace; font-size: 16px; font-weight: bold; color: #1e40af;">0384104942</span> 
                                <button type="button" onclick="copyText('0384104942', this)" style="padding: 2px 8px; font-size: 11px; cursor: pointer; border-radius: 4px; border: 1px solid #93c5fd; background: #fff; margin-left: 5px;">📋 Copy</button>
                            </div>
                            <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(102, 126, 234, 0.2);">
                                <strong>Nội dung chuyển khoản:</strong> <span style="font-family: monospace; font-size: 16px; font-weight: bold; color: #dc2626;">VE<?= $ticket_id ?></span>
                                <button type="button" onclick="copyText('VE<?= $ticket_id ?>', this)" style="padding: 2px 8px; font-size: 11px; cursor: pointer; border-radius: 4px; border: 1px solid #fca5a5; background: #fff; margin-left: 5px;">📋 Copy</button>
                            </div>
                        </div>
                    </div>

                    <!-- Status Messages -->
                    <div class="status-message loading" id="status-loading">
                        <div class="loading-spinner"></div>
                        <span id="loading-text">Đang kiểm tra giao dịch...</span>
                    </div>
                    <div class="status-message success" id="status-success">
                        ✅ <strong>Thanh toán thành công!</strong><br>
                        <small>Hệ thống đang xuất vé và gửi xác nhận qua email...</small>
                    </div>
                    <div class="status-message error" id="status-error"></div>

                    <!-- Action Buttons -->
                    <div class="action-buttons" style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                        <button class="btn" id="btn-confirm-pay" onclick="confirmPaymentManual()" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 14px; font-size: 16px; font-weight: bold; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 14px rgba(16,185,129,0.3);">
                            ✓ Tôi đã chuyển khoản xong
                        </button>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn" onclick="checkPaymentStatus('check')" style="flex: 1; background: #3b82f6; color: white; padding: 10px; font-size: 13px; border-radius: 8px; border: none; cursor: pointer;">
                                🔄 Kiểm tra lại
                            </button>
                            <button class="btn" onclick="cancelPayment()" style="flex: 1; background: #f3f4f6; color: #374151; padding: 10px; font-size: 13px; border-radius: 8px; border: 1px solid #d1d5db; cursor: pointer;">
                                ← Quay lại
                            </button>
                        </div>
                    </div>

                    <div class="footer">
                        <p>Powered by Sepay | Galaxy Studio © 2026</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const TICKET_ID = <?= $ticket_id ?>;
        const CHECK_INTERVAL = 5000; // Kiểm tra mỗi 5 giây
        let checkCount = 0;
        const MAX_CHECKS = 360;

        // Determine base path dynamically
        const matches = window.location.pathname.match(/^(.+)\/(Trang-nguoi-dung|Trang-admin)/);
        const basePath = matches ? matches[1] : '';

        function copyText(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const old = btn.innerText;
                btn.innerText = '✓ Đã chép';
                setTimeout(() => { btn.innerText = old; }, 2000);
            });
        }

        /**
         * Người dùng chủ động xác nhận đã chuyển khoản
         */
        async function confirmPaymentManual() {
            const btn = document.getElementById('btn-confirm-pay');
            btn.disabled = true;
            btn.innerHTML = '<span class="loading-spinner" style="display:inline-block; vertical-align:middle; width:16px; height:16px; margin-right:8px;"></span> Đang xác nhận thanh toán...';
            
            await checkPaymentStatus('confirm');
        }

        /**
         * Hủy / Quay lại trang chọn thanh toán
         */
        function cancelPayment() {
            if (confirm('Bạn có chắc muốn quay lại trang chọn phương thức thanh toán?')) {
                window.location.href = basePath + '/Trang-nguoi-dung/index.php?act=thanhtoan';
            }
        }

        /**
         * Kiểm tra trạng thái thanh toán
         * @param {string} action 'check' hoặc 'confirm'
         */
        async function checkPaymentStatus(action = 'check') {
            try {
                if (action === 'confirm') {
                    showLoading('Đang xác thực giao dịch chuyển khoản...');
                }

                const response = await fetch(basePath + '/Trang-nguoi-dung/sepay/check_payment_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ticket_id: TICKET_ID, action: action })
                });

                const result = await response.json();

                if (result.success && result.status === 'paid') {
                    showSuccess();
                    clearInterval(autoCheckInterval);
                    
                    const redirectUrl = result.redirect_url || (basePath + '/Trang-nguoi-dung/index.php?act=xacnhan&sepay=1');
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 1500);
                    
                    return true;
                } else {
                    if (action === 'confirm') {
                        hideLoading();
                        showError('Chưa nhận được giao dịch. Nếu bạn vừa chuyển, vui lòng đợi 5-10 giây rồi thử lại!');
                        const btn = document.getElementById('btn-confirm-pay');
                        btn.disabled = false;
                        btn.innerHTML = '✓ Tôi đã chuyển khoản xong';
                    }
                    return false;
                }
            } catch (error) {
                if (action === 'confirm') {
                    hideLoading();
                    showError('❌ Lỗi kết nối: ' + error.message);
                    const btn = document.getElementById('btn-confirm-pay');
                    btn.disabled = false;
                    btn.innerHTML = '✓ Tôi đã chuyển khoản xong';
                }
                return false;
            }
        }

        function showSuccess() {
            hideLoading();
            document.getElementById('status-error').style.display = 'none';
            document.getElementById('status-success').style.display = 'block';
            document.getElementById('status-badge').textContent = '✓ Đã thanh toán';
            document.getElementById('status-badge').style.color = '#10b981';
        }

        function showLoading(text = 'Kiểm tra trạng thái...') {
            const loadingEl = document.getElementById('status-loading');
            document.getElementById('loading-text').textContent = text;
            loadingEl.style.display = 'flex';
            document.getElementById('status-error').style.display = 'none';
            document.getElementById('status-success').style.display = 'none';
        }

        function hideLoading() {
            document.getElementById('status-loading').style.display = 'none';
        }

        function showError(message) {
            hideLoading();
            const errEl = document.getElementById('status-error');
            errEl.style.display = 'block';
            errEl.textContent = message;
        }

        // Auto check trong nền (chỉ check âm thầm, không tự ý chuyển hướng trừ khi có xác nhận thanh toán thật)
        let autoCheckInterval = setInterval(async () => {
            if (checkCount >= MAX_CHECKS) {
                clearInterval(autoCheckInterval);
                return;
            }
            checkCount++;
            await checkPaymentStatus('check');
        }, CHECK_INTERVAL);
    </script>
</body>
</html>
