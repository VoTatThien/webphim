<?php
/**
 * MOBILE CHECK-IN PAGE
 * 
 * Trang check-in vé bằng camera điện thoại
 * Sử dụng QR code hoặc barcode scanner
 * 
 * Truy cập: http://localhost/webphim_hung/Trang-admin/mobile_checkin.php
 * 
 * Features:
 * - Quét QR code từ vé
 * - Check-in tức thì
 * - Lịch sử check-in
 * - Giao diện mobile-friendly
 * - Không yêu cầu đăng nhập (hoặc đăng nhập đơn giản)
 */

session_start();
require_once 'model/pdo.php';

$pdo = pdo_get_connection();

// Cho phép truy cập không cần đăng nhập hoặc kiểm tra session nhân viên
// $require_staff = true; // Bỏ comment nếu muốn yêu cầu đăng nhập nhân viên

// Xử lý AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    
    if ($action === 'check_ticket') {
        $ma_ve = trim($_POST['ma_ve'] ?? '');
        
        if (empty($ma_ve)) {
            echo json_encode(['success' => false, 'message' => 'Mã vé không được để trống']);
            exit;
        }
        
        try {
            // Tìm vé
            $stmt = $pdo->prepare("
                SELECT v.*, 
                       p.tieu_de, p.ma_phim,
                       l.ngay_chieu, l.thoi_gian_chieu,
                       ph.ten as tenphong,
                       tk.name as khach_hang
                FROM ve v
                LEFT JOIN lichchieu l ON v.id_lichchieu = l.id
                LEFT JOIN phim p ON l.id_phim = p.id
                LEFT JOIN phong ph ON l.id_phong = ph.id
                LEFT JOIN taikhoan tk ON v.id_tk = tk.id
                WHERE v.ma_ve = :ma_ve
                LIMIT 1
            ");
            $stmt->execute(['ma_ve' => $ma_ve]);
            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$ticket) {
                echo json_encode(['success' => false, 'message' => 'Vé không tồn tại!']);
                exit;
            }
            
            // Kiểm tra trạng thái vé
            if ($ticket['trang_thai'] == 1) {
                echo json_encode([
                    'success' => false, 
                    'message' => '⚠️ Vé đã được check-in rồi!',
                    'ticket' => [
                        'movie' => htmlspecialchars($ticket['tieu_de'] ?? 'N/A'),
                        'date' => htmlspecialchars($ticket['ngay_chieu'] ?? 'N/A'),
                        'time' => htmlspecialchars($ticket['thoi_gian_chieu'] ?? 'N/A'),
                        'room' => htmlspecialchars($ticket['tenphong'] ?? 'N/A'),
                        'seat' => htmlspecialchars($ticket['ghe'] ?? 'N/A'),
                        'customer' => htmlspecialchars($ticket['khach_hang'] ?? 'N/A')
                    ]
                ]);
                exit;
            }
            
            // Kiểm tra hạn vé (nếu ngày chiếu đã qua)
            $ngay_chieu = strtotime($ticket['ngay_chieu']);
            if ($ngay_chieu && $ngay_chieu < strtotime('today')) {
                echo json_encode([
                    'success' => false, 
                    'message' => '❌ Vé đã hết hạn (ngày chiếu đã qua)!',
                    'ticket' => [
                        'movie' => htmlspecialchars($ticket['tieu_de'] ?? 'N/A'),
                        'date' => htmlspecialchars($ticket['ngay_chieu'] ?? 'N/A'),
                        'time' => htmlspecialchars($ticket['thoi_gian_chieu'] ?? 'N/A'),
                        'room' => htmlspecialchars($ticket['tenphong'] ?? 'N/A'),
                        'seat' => htmlspecialchars($ticket['ghe'] ?? 'N/A'),
                        'customer' => htmlspecialchars($ticket['khach_hang'] ?? 'N/A')
                    ]
                ]);
                exit;
            }
            
            // Update trạng thái vé thành check-in
            $staff_id = $_SESSION['user1']['id'] ?? 1; // Mặc định staff ID = 1 nếu không đăng nhập
            $update_stmt = $pdo->prepare("
                UPDATE ve 
                SET trang_thai = 1, 
                    id_nhanvien_checkin = :staff_id,
                    thoi_gian_checkin = NOW()
                WHERE id = :id
            ");
            $update_stmt->execute([
                'staff_id' => $staff_id,
                'id' => $ticket['id']
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => '✅ Check-in thành công!',
                'ticket' => [
                    'movie' => htmlspecialchars($ticket['tieu_de'] ?? 'N/A'),
                    'date' => htmlspecialchars($ticket['ngay_chieu'] ?? 'N/A'),
                    'time' => htmlspecialchars($ticket['thoi_gian_chieu'] ?? 'N/A'),
                    'room' => htmlspecialchars($ticket['tenphong'] ?? 'N/A'),
                    'seat' => htmlspecialchars($ticket['ghe'] ?? 'N/A'),
                    'customer' => htmlspecialchars($ticket['khach_hang'] ?? 'N/A')
                ]
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📱 Check-in Vé - CinePass</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 0;
        }

        .container {
            max-width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: white;
            overflow-y: auto;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .scanner-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            gap: 15px;
        }

        #qr-scanner {
            width: 100%;
            max-width: 500px;
            height: 500px;
            margin: 0 auto;
            border: 3px solid #667eea;
            border-radius: 12px;
            background: black;
            display: none;
            object-fit: cover;
        }

        .scanner-placeholder {
            width: 100%;
            max-width: 500px;
            height: 500px;
            margin: 0 auto;
            background: #f5f5f5;
            border: 3px dashed #ddd;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #666;
            font-size: 14px;
            text-align: center;
            padding: 20px;
        }

        .scanner-placeholder.hidden {
            display: none;
        }

        .controls {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            flex: 1;
            min-width: 120px;
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
        }

        .btn-secondary:hover:not(:disabled) {
            background: #e0e0e0;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .input-group {
            display: flex;
            gap: 10px;
            max-width: 500px;
            margin: 0 auto;
            width: 100%;
        }

        .input-group input {
            flex: 1;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }

        .input-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .input-group button {
            flex-shrink: 0;
        }

        .status-card {
            border-radius: 12px;
            padding: 20px;
            margin: 10px auto;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            animation: slideIn 0.3s ease-in-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .status-success {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            color: #155e75;
        }

        .status-error {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: #7c2d12;
        }

        .status-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .ticket-details {
            background: rgba(255,255,255,0.4);
            padding: 15px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.8;
            margin-top: 10px;
        }

        .ticket-details div {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .ticket-details div:last-child {
            border-bottom: none;
        }

        .ticket-details strong {
            flex-shrink: 0;
            margin-right: 10px;
        }

        .tabs {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding: 0 20px;
            flex-wrap: wrap;
            max-width: 500px;
            margin: 0 auto;
        }

        .tab-btn {
            padding: 10px 20px;
            border: none;
            background: #f0f0f0;
            color: #333;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            flex: 1;
            min-width: 100px;
        }

        .tab-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .loading {
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .history-empty {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .history-item {
            background: white;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            margin: 10px auto;
            max-width: 500px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .history-item-info {
            flex: 1;
            font-size: 14px;
            line-height: 1.6;
        }

        .history-item-status {
            font-weight: bold;
            color: #28a745;
            font-size: 12px;
            padding: 5px 10px;
            background: #d4edda;
            border-radius: 4px;
            white-space: nowrap;
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 20px;
            }

            .btn {
                padding: 12px 16px;
                font-size: 14px;
            }

            #qr-scanner,
            .scanner-placeholder {
                height: 400px;
            }

            .status-card {
                margin: 10px 10px;
                padding: 15px;
            }

            .tabs {
                padding: 0 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📱 Check-in Vé</h1>
            <p>CinePass Cinema Management</p>
        </div>

        <!-- Main Content -->
        <div class="scanner-section">
            <!-- Tabs -->
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('scanner')">📷 Quét QR</button>
                <button class="tab-btn" onclick="switchTab('manual')">⌨️ Nhập Mã</button>
                <button class="tab-btn" onclick="switchTab('history')">📋 Lịch Sử</button>
            </div>

            <!-- Scanner Tab -->
            <div id="tab-scanner" class="tab-content active">
                <video id="qr-scanner" playsinline></video>
                <div class="scanner-placeholder" id="scanner-placeholder">
                    <div>🎯</div>
                    <div>Bấm nút bên dưới để bắt đầu quét</div>
                </div>
                <div class="controls">
                    <button class="btn btn-primary" onclick="startCamera()" id="start-btn">▶️ Bắt đầu Quét</button>
                    <button class="btn btn-secondary" onclick="stopCamera()" id="stop-btn" style="display: none;">⏹️ Dừng Quét</button>
                </div>
            </div>

            <!-- Manual Tab -->
            <div id="tab-manual" class="tab-content">
                <div class="input-group">
                    <input type="text" id="ma-ve-input" placeholder="Nhập mã vé (VD: VE123456)" autocomplete="off">
                    <button class="btn btn-primary" onclick="checkTicketManual()">🔍 Kiểm Tra</button>
                </div>
            </div>

            <!-- History Tab -->
            <div id="tab-history" class="tab-content">
                <div id="history-container" class="loading">
                    <div class="spinner"></div>
                    <p>Đang tải lịch sử...</p>
                </div>
            </div>

            <!-- Status Display -->
            <div id="status-container"></div>
        </div>
    </div>

    <script src="../js/jsQR.min.js"></script>
    <script>
        let currentTicket = null;
        let cameraStream = null;
        let scanning = false;
        let scanInterval = null;
        let barcodeDetector = null;

        // Khởi tạo BarcodeDetector
        async function initBarcodeDetector() {
            try {
                if (window.BarcodeDetector) {
                    barcodeDetector = new BarcodeDetector({ formats: ['qr_code'] });
                    console.log('✅ BarcodeDetector API hỗ trợ');
                }
            } catch (e) {
                console.log('⚠️ BarcodeDetector không hỗ trợ, sẽ dùng fallback jsQR');
            }
        }
        initBarcodeDetector();

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');

            document.getElementById('status-container').innerHTML = '';

            if (tabName === 'scanner') {
                stopCamera();
            } else if (tabName === 'history') {
                loadHistory();
            }
        }

        async function startCamera() {
            if (scanning) return;

            const video = document.getElementById('qr-scanner');
            const placeholder = document.getElementById('scanner-placeholder');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                });

                cameraStream = stream;
                video.srcObject = stream;
                video.style.display = 'block';
                placeholder.classList.add('hidden');
                
                document.getElementById('start-btn').style.display = 'none';
                document.getElementById('stop-btn').style.display = 'block';

                video.onloadedmetadata = () => {
                    video.play();
                    scanning = true;
                    scanQRCode();
                };
            } catch (err) {
                alert('❌ Lỗi truy cập camera: ' + err.message + '\n\nVui lòng kiểm tra:\n1. Bạn đã cho phép truy cập camera?\n2. Sử dụng HTTPS hoặc localhost?');
            }
        }

        function stopCamera() {
            scanning = false;
            if (scanInterval) {
                clearInterval(scanInterval);
                scanInterval = null;
            }
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }
            document.getElementById('qr-scanner').style.display = 'none';
            document.getElementById('scanner-placeholder').classList.remove('hidden');
            document.getElementById('start-btn').style.display = 'block';
            document.getElementById('stop-btn').style.display = 'none';
        }

        function scanQRCode() {
            if (!scanning) return;

            const video = document.getElementById('qr-scanner');

            scanInterval = setInterval(async () => {
                if (!scanning || !video) return;

                if (video.videoWidth === 0 || video.videoHeight === 0) return;

                try {
                    let qrData = null;

                    // 1. Thử dùng BarcodeDetector API nếu được hỗ trợ
                    if (barcodeDetector) {
                        try {
                            const barcodes = await barcodeDetector.detect(video);
                            if (barcodes && barcodes.length > 0) {
                                qrData = barcodes[0].rawValue;
                                console.log('✅ QR detected (BarcodeDetector):', qrData);
                            }
                        } catch (e) {
                            console.warn('BarcodeDetector.detect error, falling back to jsQR:', e);
                        }
                    }

                    // 2. Nếu BarcodeDetector không phát hiện được hoặc bị lỗi, dùng fallback jsQR
                    if (!qrData) {
                        const canvas = document.createElement('canvas');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;

                        if (canvas.width > 0 && canvas.height > 0) {
                            const ctx = canvas.getContext('2d', { willReadFrequently: true });
                            if (ctx) {
                                ctx.drawImage(video, 0, 0);
                                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

                                let code = null;
                                if (typeof jsQR !== 'undefined') {
                                    code = jsQR(imageData.data, imageData.width, imageData.height, {
                                        inversionAttempts: 'attemptBoth'
                                    });
                                }

                                if (!code && typeof jsQR !== 'undefined') {
                                    const cropSize = Math.min(canvas.width, canvas.height) * 0.7;
                                    const startX = (canvas.width - cropSize) / 2;
                                    const startY = (canvas.height - cropSize) / 2;

                                    const croppedData = ctx.getImageData(startX, startY, cropSize, cropSize);
                                    code = jsQR(croppedData.data, cropSize, cropSize, {
                                        inversionAttempts: 'attemptBoth'
                                    });
                                }

                                if (code && code.data) {
                                    qrData = code.data;
                                    console.log('✅ QR detected (Canvas/jsQR):', qrData);
                                }
                            }
                        }
                    }

                    // 3. Nếu tìm thấy dữ liệu QR
                    if (qrData) {
                        stopCamera();
                        checkTicket(qrData);
                        return;
                    }
                } catch (err) {
                    console.error('Scan error:', err);
                }
            }, 200);
        }

        function checkTicketManual() {
            const maVe = document.getElementById('ma-ve-input').value.trim();
            if (!maVe) {
                alert('Vui lòng nhập mã vé');
                return;
            }
            checkTicket(maVe);
        }

        function checkTicket(maVe) {
            if (!maVe) return;

            const statusDiv = document.getElementById('status-container');
            statusDiv.innerHTML = '<div class="status-card"><div class="loading"><div class="spinner"></div>Đang kiểm tra...</div></div>';

            fetch('mobile_checkin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=check_ticket&ma_ve=' + encodeURIComponent(maVe)
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById('ma-ve-input').value = '';
                
                let cardClass = data.success ? 'status-success' : 'status-error';
                let html = `<div class="status-card ${cardClass}">
                    <div class="status-title">${data.message}</div>`;
                
                if (data.ticket) {
                    html += `<div class="ticket-details">
                        <div><strong>Phim:</strong> <span>${data.ticket.movie}</span></div>
                        <div><strong>Ngày:</strong> <span>${data.ticket.date}</span></div>
                        <div><strong>Giờ:</strong> <span>${data.ticket.time}</span></div>
                        <div><strong>Phòng:</strong> <span>${data.ticket.room}</span></div>
                        <div><strong>Ghế:</strong> <span>${data.ticket.seat}</span></div>
                        <div><strong>Khách:</strong> <span>${data.ticket.customer}</span></div>
                    </div>`;
                }
                
                html += '</div>';
                statusDiv.innerHTML = html;

                if (data.success) {
                    loadHistory();
                }
            })
            .catch(err => {
                statusDiv.innerHTML = `<div class="status-card status-error">
                    <div class="status-title">❌ Lỗi kết nối</div>
                    <div>${err.message}</div>
                </div>`;
            });
        }

        function loadHistory() {
            // TODO: Tải lịch sử check-in từ server
            // Hiện tại để placeholder
            document.getElementById('history-container').innerHTML = '<div class="history-empty">📋 Lịch sử check-in sẽ hiển thị ở đây</div>';
        }

        // Auto-focus vào input khi chuyển sang tab manual
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('ma-ve-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') checkTicketManual();
            });
        });
    </script>
</body>
</html>
