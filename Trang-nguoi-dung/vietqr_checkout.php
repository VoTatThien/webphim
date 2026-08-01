<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VietQR Thanh Toán - CinePass</title>
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

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        /* Content */
        .content {
            padding: 30px;
        }

        /* Order Info */
        .order-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #667eea;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        .order-id {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }

        .amount {
            color: #667eea;
            font-size: 18px;
        }

        /* QR Code */
        .qr-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .qr-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .qr-code-container {
            background: #f0f0f0;
            border-radius: 12px;
            padding: 20px;
            display: inline-block;
            min-width: 270px;
        }

        .qr-code {
            width: 250px;
            height: 250px;
            border-radius: 8px;
            background: white;
            display: block;
            margin: 0 auto;
        }

        /* Bank Info */
        .bank-info {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border: 2px solid #667eea30;
        }

        .bank-header {
            font-size: 12px;
            color: #667eea;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bank-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            font-size: 14px;
        }

        .bank-detail:last-child {
            margin-bottom: 0;
        }

        .bank-label {
            color: #666;
            font-weight: 500;
        }

        .bank-value {
            color: #333;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            text-align: right;
        }

        /* Instructions */
        .instructions {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 13px;
            color: #856404;
            line-height: 1.6;
        }

        .instructions strong {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 12px;
        }

        button {
            flex: 1;
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

        .btn-confirm {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .btn-confirm:active {
            transform: translateY(0);
        }

        .btn-cancel {
            background: #e9ecef;
            color: #495057;
            border: 2px solid #dee2e6;
        }

        .btn-cancel:hover {
            background: #dee2e6;
            border-color: #adb5bd;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #999;
            background: #fafbfc;
        }

        /* Timer */
        .timer {
            display: inline-block;
            background: #f8d7da;
            color: #721c24;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Status */
        .status {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .status.loading {
            background: #e7f3ff;
            color: #004085;
            border-left: 4px solid #0056b3;
        }

        .status.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #f5c6cb;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .content {
                padding: 20px;
            }

            .qr-code-container {
                min-width: 240px;
                padding: 15px;
            }

            .qr-code {
                width: 220px;
                height: 220px;
            }

            .button-group {
                flex-direction: column;
            }

            button {
                padding: 12px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🏦 VietQR Thanh Toán</h1>
            <p>Quét mã QR để chuyển tiền</p>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- Status Messages -->
            <div id="statusMessage"></div>

            <!-- Order Info -->
            <div class="order-info">
                <div class="info-row">
                    <span class="info-label">Mã đơn hàng</span>
                    <span class="info-value order-id" id="orderId"></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Số tiền thanh toán</span>
                    <span class="info-value amount" id="amount"></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nội dung chuyển</span>
                    <span class="info-value" id="description" style="font-size: 12px;"></span>
                </div>
            </div>

            <!-- QR Code -->
            <div class="qr-section">
                <div class="qr-label">Mã QR Chuyển Tiền</div>
                <div class="qr-code-container">
                    <img class="qr-code" id="qrCode" src="" alt="VietQR">
                </div>
            </div>

            <!-- Bank Info -->
            <div class="bank-info">
                <div class="bank-header">
                    🏦 Thông Tin Tài Khoản Nhận
                </div>
                <div class="bank-detail">
                    <span class="bank-label">Ngân hàng</span>
                    <span class="bank-value" id="bankName"></span>
                </div>
                <div class="bank-detail">
                    <span class="bank-label">Tên chủ tài khoản</span>
                    <span class="bank-value" id="accountName"></span>
                </div>
                <div class="bank-detail">
                    <span class="bank-label">Số tài khoản</span>
                    <span class="bank-value" id="accountNumber"></span>
                </div>
                <div class="bank-detail">
                    <span class="bank-label">Số tiền</span>
                    <span class="bank-value" id="amountBank"></span>
                </div>
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <strong>📱 Hướng dẫn chuyển tiền:</strong>
                <ol style="margin-left: 20px; margin-top: 8px;">
                    <li>Mở app ngân hàng của bạn</li>
                    <li>Chọn "Chuyển tiền" hoặc "Thanh toán QR"</li>
                    <li>Quét mã QR phía trên</li>
                    <li>Xác nhận số tiền và chuyển</li>
                    <li>Bấm nút "✓ Đã Chuyển Tiền" dưới đây</li>
                </ol>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button class="btn-confirm" onclick="confirmPayment()">
                    ✓ Đã Chuyển Tiền
                </button>
                <button class="btn-cancel" onclick="cancelPayment()">
                    ✕ Hủy
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>⏱️ Thanh toán sẽ được xác nhận trong vòng 24 giờ</p>
            <p style="margin-top: 8px; opacity: 0.7;">CinePass © 2024</p>
        </div>
    </div>

    <script>
        // Get parameters from URL
        const params = new URLSearchParams(window.location.search);
        const orderId = params.get('orderId');
        const amount = parseInt(params.get('amount')) || 0;
        const qrCode = params.get('qrCode');
        const bankName = params.get('bankName') || 'Ngân hàng Quân Đội Việt Nam (MBBank)';
        const accountNumber = params.get('accountNumber') || '79799999889';
        const accountName = params.get('accountName') || 'CINEPASS THEATER';
        const description = params.get('description') || 'Ve phim CinePass';

        // Display data
        document.getElementById('orderId').textContent = orderId;
        document.getElementById('amount').textContent = amount.toLocaleString('vi-VN') + ' ₫';
        document.getElementById('amountBank').textContent = amount.toLocaleString('vi-VN') + ' ₫';
        document.getElementById('description').textContent = description;
        document.getElementById('bankName').textContent = bankName;
        document.getElementById('accountNumber').textContent = accountNumber;
        document.getElementById('accountName').textContent = accountName;

        // Display QR Code
        if (qrCode) {
            const qrImg = document.getElementById('qrCode');
            if (qrCode.startsWith('data:') || qrCode.startsWith('http')) {
                qrImg.src = qrCode;
            } else {
                // Assume it's base64
                qrImg.src = 'data:image/png;base64,' + qrCode;
            }
        }

        // Confirm Payment
        function confirmPayment() {
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = '⏳ Đang xử lý...';

            // Redirect to return page
            setTimeout(() => {
                window.location.href = '/webphim_hung/Trang-nguoi-dung/vietqr_return.php?orderId=' + encodeURIComponent(orderId) + '&amount=' + amount + '&status=confirmed';
            }, 1000);
        }

        // Cancel Payment
        function cancelPayment() {
            if (confirm('Bạn có chắc muốn hủy thanh toán?')) {
                window.history.back();
            }
        }
    </script>
</body>
</html>
