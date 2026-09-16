<?php 
session_start();

// Check if user is logged in
if (!isset($_SESSION['user1'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>📱 Quét QR Vé - Mobile</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #000;
            color: #fff;
            overflow: hidden;
            height: 100vh;
        }

        #video-container {
            position: relative;
            width: 100%;
            height: 100vh;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        canvas {
            display: none;
        }

        /* QR Detection Overlay */
        .qr-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .scanner-frame {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 250px;
            height: 250px;
            transform: translate(-50%, -50%);
            border: 3px solid #00ff00;
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.5), inset 0 0 30px rgba(0, 255, 0, 0.1);
            z-index: 10;
        }

        .corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 3px solid #00ff00;
        }

        .corner.top-left {
            top: -6px;
            left: -6px;
            border-right: none;
            border-bottom: none;
        }

        .corner.top-right {
            top: -6px;
            right: -6px;
            border-left: none;
            border-bottom: none;
        }

        .corner.bottom-left {
            bottom: -6px;
            left: -6px;
            border-right: none;
            border-top: none;
        }

        .corner.bottom-right {
            bottom: -6px;
            right: -6px;
            border-left: none;
            border-top: none;
        }

        /* Top Bar */
        .top-bar {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.7), transparent);
            padding: 20px;
            z-index: 20;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .staff-info {
            font-size: 14px;
            line-height: 1.4;
        }

        .staff-info strong {
            display: block;
            font-size: 16px;
            color: #00ff00;
        }

        .close-btn {
            background: rgba(255, 0, 0, 0.7);
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .close-btn:active {
            background: rgba(255, 0, 0, 1);
            transform: scale(0.95);
        }

        /* Mode buttons */
        .btn-mode {
            padding: 10px 20px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 8px;
            margin: 0 5px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-mode.active {
            border-color: #007bff;
            background: #007bff;
            color: white;
        }

        .btn-mode:active {
            transform: scale(0.95);
        }

        /* Bottom status */
        .bottom-status {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            padding: 30px 20px 20px;
            z-index: 20;
            text-align: center;
        }

        .status-text {
            font-size: 14px;
            color: #aaa;
            margin-bottom: 10px;
        }

        .status-text.scanning {
            color: #00ff00;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Result Modal */
        .result-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.95);
            display: none;
            z-index: 100;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(100vh);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-card {
            background: white;
            color: #000;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            max-width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .result-card.success {
            border-left: 5px solid #28a745;
        }

        .result-card.error {
            border-left: 5px solid #dc3545;
        }

        .result-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .result-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .result-message {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .ticket-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            text-align: left;
            font-size: 13px;
        }

        .ticket-details div {
            margin: 8px 0;
            display: flex;
            justify-content: space-between;
        }

        .ticket-details strong {
            color: #333;
        }

        .ticket-details span {
            color: #666;
        }

        .result-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-continue {
            background: #007bff;
            color: white;
        }

        .btn-continue:active {
            background: #0056b3;
            transform: scale(0.98);
        }

        .btn-close {
            background: #f0f0f0;
            color: #333;
        }

        .btn-close:active {
            background: #ddd;
            transform: scale(0.98);
        }

        /* Loading */
        .loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 50;
            display: none;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading.show {
            display: block;
        }

        /* Error Overlay */
        .error-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            padding: 15px;
            text-align: center;
            z-index: 30;
            display: none;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
            }
            to {
                transform: translateY(0);
            }
        }

        .error-overlay.show {
            display: block;
        }
    </style>
</head>
<body>
    <div id="scanner-container">
        <!-- Mode selector -->
        <div style="text-align: center; padding: 20px; background: #f8f9fa; border-bottom: 1px solid #ddd;">
            <button id="btn-camera-mode" class="btn-mode active" onclick="switchMode('camera')">📷 Camera</button>
            <button id="btn-manual-mode" class="btn-mode" onclick="switchMode('manual')">✏️ Nhập mã</button>
        </div>

        <!-- Camera mode -->
        <div id="camera-mode" style="display: block;">
            <div id="qr-reader"></div>
        </div>

        <!-- Manual input mode -->
        <div id="manual-mode" style="display: none; padding: 20px; text-align: center;">
            <p style="margin-bottom: 20px; color: #666;">Nhập mã QR hoặc ID vé</p>
            <input type="text" id="manual-qr-input" placeholder="Quét mã hoặc nhập ID vé..." 
                   style="width: 100%; padding: 12px; font-size: 16px; border: 2px solid #ddd; border-radius: 8px; margin-bottom: 20px;">
            <button class="btn btn-primary" onclick="processManualQR()" style="width: 100%; padding: 12px;">🔍 Kiểm tra vé</button>
        </div>

        <div class="top-bar">
            <div class="staff-info">
                <strong>👤 <?= htmlspecialchars($_SESSION['user1']['name'] ?? 'Nhân viên') ?></strong>
                <div style="font-size: 12px;">🏢 Rạp: <?= htmlspecialchars($_SESSION['user1']['id_rap'] ?? 'N/A') ?></div>
                <div id="current-time" style="font-size: 12px;">⏰ --:--:--</div>
            </div>
            <button class="close-btn" onclick="location.href='index.php'">✕</button>
        </div>

        <div class="bottom-status">
            <div class="status-text scanning">📱 Đưa QR code vé lên camera để quét...</div>
        </div>
    </div>

    <div class="result-modal" id="result-modal">
        <div class="result-card" id="result-card">
            <!-- Result will be displayed here -->
        </div>
    </div>

    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>

    <div class="error-overlay" id="error-overlay"></div>

    <!-- Html5Qrcode library - Multiple CDN sources for reliability -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.4/html5-qrcode.min.js"></script>
    <script>
        // Fallback if CDN fails
        if (typeof Html5Qrcode === 'undefined') {
            document.write('<script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"><\/script>');
        }
    </script>

    <script>
        let html5QrcodeScanner = null;
        let cameraStarted = false;
        let isProcessing = false;
        let lastScannedCode = null;
        let lastScanTime = 0;
        let libraryReady = false;

        // Update time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = 
                '⏰ ' + now.toLocaleTimeString('vi-VN');
        }
        setInterval(updateTime, 1000);
        updateTime();

        // Debug log - display on screen
        function debugLog(message) {
            console.log(message);
            
            // Also show in a debug panel on screen
            let debugPanel = document.getElementById('debug-panel');
            if (!debugPanel) {
                debugPanel = document.createElement('div');
                debugPanel.id = 'debug-panel';
                debugPanel.style.cssText = `
                    position: fixed;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    background: rgba(0,0,0,0.95);
                    color: #0f0;
                    font-family: monospace;
                    font-size: 11px;
                    max-height: 150px;
                    overflow-y: auto;
                    z-index: 999;
                    padding: 10px;
                    border-top: 2px solid #00ff00;
                    line-height: 1.4;
                `;
                document.body.appendChild(debugPanel);
            }
            
            const timestamp = new Date().toLocaleTimeString('vi-VN');
            debugPanel.innerHTML += `[${timestamp}] ${message}<br>`;
            debugPanel.scrollTop = debugPanel.scrollHeight;
        }

        // Check if Html5Qrcode library is loaded
        function checkLibraryReady() {
            if (typeof Html5Qrcode !== 'undefined') {
                debugLog('✅ Html5Qrcode library loaded');
                libraryReady = true;
                return true;
            }
            debugLog('⚠️ Html5Qrcode library not loaded yet');
            return false;
        }

        // Initialize scanner with Html5Qrcode
        async function initScanner() {
            try {
                debugLog('📱 Initializing Html5Qrcode scanner...');
                
                // Check if library is loaded
                if (!checkLibraryReady()) {
                    debugLog('❌ Html5Qrcode library not loaded');
                    showError('❌ Thư viện không load được. Kiểm tra kết nối internet hoặc thử lại.');
                    return;
                }

                html5QrcodeScanner = new Html5Qrcode("qr-reader");
                debugLog('✅ Html5Qrcode instance created');

                // Get available cameras
                let cameras = [];
                try {
                    cameras = await Html5Qrcode.getCameras();
                    debugLog('📷 getCameras() returned: ' + (cameras ? cameras.length + ' cameras' : 'null'));
                } catch (getCamerasErr) {
                    debugLog('⚠️ getCameras() failed: ' + (getCamerasErr.message || 'unknown error'));
                    debugLog('📱 Falling back to direct camera access...');
                    
                    // Fallback: Try to get camera directly using getUserMedia
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'environment' }
                        });
                        
                        // Stop stream to get camera list another way
                        stream.getTracks().forEach(track => track.stop());
                        
                        debugLog('✅ Direct camera access worked');
                        // Use default camera
                        cameras = [{ id: 'default', label: 'Camera' }];
                    } catch (fallbackErr) {
                        debugLog('❌ Fallback also failed: ' + (fallbackErr.message || 'unknown error'));
                        debugLog('Error name: ' + fallbackErr.name);
                        
                        if (fallbackErr.name === 'NotAllowedError') {
                            showError('❌ Bạn từ chối cấp phép camera. Kiểm tra cài đặt quyền ứng dụng.');
                        } else if (fallbackErr.name === 'NotFoundError') {
                            showError('❌ Không tìm thấy camera trên thiết bị.');
                        } else if (fallbackErr.name === 'SecurityError') {
                            showError('❌ Lỗi bảo mật. iPhone cần HTTPS để truy cập camera. Liên hệ admin.');
                        } else {
                            showError('❌ Không thể truy cập camera: ' + (fallbackErr.message || 'Lỗi không xác định'));
                        }
                        return;
                    }
                }
                
                if (!cameras || cameras.length === 0) {
                    showError('❌ Không tìm thấy camera trên thiết bị. Kiểm tra cài đặt quyền truy cập.');
                    return;
                }

                debugLog('✅ Cameras found: ' + cameras.length);
                cameras.forEach((cam, i) => {
                    debugLog(`Camera ${i}: ${cam.label || 'Default'} (${cam.id})`);
                });
                
                // Prefer back camera for mobile
                let selectedCameraId = cameras[0].id;
                for (let camera of cameras) {
                    if (camera.label && camera.label.toLowerCase().includes('back')) {
                        selectedCameraId = camera.id;
                        debugLog('📍 Selected back camera: ' + camera.label);
                        break;
                    }
                }

                startScannerWithCamera(selectedCameraId);

            } catch (err) {
                debugLog('❌ Init error: ' + (err.name || 'unknown') + ' - ' + (err.message || 'unknown error'));
                
                let errorMsg = 'Lỗi không xác định';
                
                if (err && err.name === 'NotAllowedError') {
                    errorMsg = 'Bạn từ chối cấp phép camera. Kiểm tra cài đặt quyền ứng dụng.';
                } else if (err && err.name === 'NotFoundError') {
                    errorMsg = 'Không tìm thấy camera trên thiết bị.';
                } else if (err && err.name === 'NotReadableError') {
                    errorMsg = 'Camera đang được sử dụng. Đóng ứng dụng khác rồi thử lại.';
                } else if (err && err.name === 'SecurityError') {
                    errorMsg = 'Lỗi bảo mật. iPhone cần HTTPS để truy cập camera.';
                } else if (err && err.name === 'OverconstrainedError') {
                    errorMsg = 'Thiết bị không hỗ trợ cài đặt camera yêu cầu.';
                } else if (err && err.message) {
                    errorMsg = err.message;
                }
                
                showError('❌ Lỗi camera: ' + errorMsg);
            }
        }

        function startScannerWithCamera(cameraId) {
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                rememberLastUsedCamera: true,
                showTorchButtonIfSupported: true,
                showZoomSliderIfSupported: true,
                defaultZoomValueIfSupported: 1,
                aspectRatio: 1.0,
                disableFlip: false
            };

            debugLog('🎬 Starting scanner with camera: ' + cameraId);
            debugLog('⚙️ Config: fps=10, qrbox=250x250');

            html5QrcodeScanner.start(
                cameraId,
                config,
                onScanSuccess,
                onScanError
            ).then(() => {
                cameraStarted = true;
                debugLog('✅ Scanner started successfully');
            }).catch(err => {
                debugLog('❌ Error starting scanner: ' + err.name + ' - ' + err.message);
                showError('❌ Không thể khởi động camera: ' + (err.message || 'Lỗi không xác định'));
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            if (isProcessing) return;

            const now = Date.now();
            // Prevent duplicate scans within 2 seconds
            if (decodedText === lastScannedCode && now - lastScanTime < 2000) {
                return;
            }

            lastScannedCode = decodedText;
            lastScanTime = now;

            console.log('✅ QR detected:', decodedText);
            isProcessing = true;

            // Pause scanner
            if (cameraStarted) {
                html5QrcodeScanner.pause(true);
            }

            // Extract ticket ID
            let ticketId = null;

            // Try to extract ID from URL
            if (decodedText.includes('id=')) {
                const match = decodedText.match(/id=(\d+)/);
                if (match) ticketId = match[1];
            }
            // Or if it's just a number
            else if (/^\d+$/.test(decodedText)) {
                ticketId = decodedText;
            }

            if (!ticketId) {
                showError('❌ Mã QR không hợp lệ. Vui lòng quét lại.');
                resumeScanner();
                return;
            }

            // Check ticket on server
            checkTicket(ticketId);
        }

        function onScanError(errorMessage) {
            // Html5Qrcode will continuously try to scan
            // We don't log every error as it's normal during scanning
            // console.debug('Scan error:', errorMessage);
        }

        async function checkTicket(ticketId) {
            document.getElementById('loading').classList.add('show');

            try {
                const response = await fetch('model/scanve_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=check&ma_ve=' + encodeURIComponent(ticketId)
                });

                const result = await response.json();
                document.getElementById('loading').classList.remove('show');

                if (result.success) {
                    showResult('success', result);
                } else {
                    showResult('error', result);
                }
            } catch (err) {
                document.getElementById('loading').classList.remove('show');
                console.error('Server error:', err);
                showError('❌ Lỗi kết nối server: ' + err.message);
                resumeScanner();
            }
        }

        function resumeScanner() {
            setTimeout(() => {
                isProcessing = false;
                lastScannedCode = null;
                if (cameraStarted) {
                    html5QrcodeScanner.resume();
                }
            }, 1000);
        }

        // Show result modal
        function showResult(type, data) {
            const modal = document.getElementById('result-modal');
            const card = document.getElementById('result-card');

            const icon = type === 'success' ? '✅' : '❌';
            const statusClass = type === 'success' ? 'success' : 'error';

            let detailsHTML = '';
            if (data.ticket) {
                const ticket = data.ticket;
                detailsHTML = `
                    <div class="ticket-details">
                        <div><strong>Mã vé:</strong> <span>${ticket.id || 'N/A'}</span></div>
                        <div><strong>Phim:</strong> <span>${ticket.ten_phim || 'N/A'}</span></div>
                        <div><strong>Rạp:</strong> <span>${ticket.ten_rap || 'N/A'}</span></div>
                        <div><strong>Phòng:</strong> <span>${ticket.ten_phong || 'N/A'}</span></div>
                        <div><strong>Ghế:</strong> <span>${ticket.so_ghe || 'N/A'}</span></div>
                        <div><strong>Giờ chiếu:</strong> <span>${ticket.gio_chieu || 'N/A'}</span></div>
                        <div><strong>Giá:</strong> <span>${ticket.gia_ve ? ticket.gia_ve.toLocaleString('vi-VN') + '₫' : 'N/A'}</span></div>
                    </div>
                `;
            }

            card.className = 'result-card ' + statusClass;
            card.innerHTML = `
                <div class="result-icon">${icon}</div>
                <div class="result-title">${data.message}</div>
                ${data.details ? '<div class="result-message">' + data.details + '</div>' : ''}
                ${detailsHTML}
                <div class="result-buttons">
                    <button class="btn btn-continue" onclick="closeResultAndResume()">Tiếp tục quét</button>
                </div>
            `;

            modal.style.display = 'flex';

            // Auto-close success after 3 seconds
            if (type === 'success') {
                setTimeout(() => {
                    closeResultAndResume();
                }, 3000);
            }
        }

        function closeResultAndResume() {
            document.getElementById('result-modal').style.display = 'none';
            resumeScanner();
        }

        // Show error
        function showError(message) {
            const overlay = document.getElementById('error-overlay');
            overlay.textContent = message;
            overlay.classList.add('show');

            setTimeout(() => {
                overlay.classList.remove('show');
            }, 3000);
        }

        // Close result modal on outside click
        document.getElementById('result-modal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('result-modal')) {
                closeResultAndResume();
            }
        });

        // Initialize on page load
        window.addEventListener('load', () => {
            debugLog('📱 Page loaded');
            debugLog('Html5Qrcode available? ' + (typeof Html5Qrcode !== 'undefined'));
            debugLog('Browser: ' + navigator.userAgent.substring(0, 50));
            debugLog('Protocol: ' + location.protocol);
            
            // Check if library is available, retry if not
            let retries = 0;
            const maxRetries = 5;
            
            function tryInit() {
                if (typeof Html5Qrcode !== 'undefined') {
                    debugLog('✅ Html5Qrcode ready, initializing scanner...');
                    setTimeout(initScanner, 500);
                } else {
                    retries++;
                    if (retries < maxRetries) {
                        debugLog(`⏳ Retry ${retries}/${maxRetries} - waiting for Html5Qrcode...`);
                        setTimeout(tryInit, 1000);
                    } else {
                        debugLog('❌ Html5Qrcode library failed to load after retries');
                        showError('❌ Thư viện quét mã QR không load được. Vui lòng kiểm tra kết nối internet và tải lại trang.');
                    }
                }
            }
            
            tryInit();
        });

        // Handle visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                if (cameraStarted) {
                    html5QrcodeScanner.pause();
                }
            } else {
                if (cameraStarted && !isProcessing) {
                    html5QrcodeScanner.resume();
                }
            }
        });

        // Mode switching
        function switchMode(mode) {
            const cameraMode = document.getElementById('camera-mode');
            const manualMode = document.getElementById('manual-mode');
            const btnCamera = document.getElementById('btn-camera-mode');
            const btnManual = document.getElementById('btn-manual-mode');

            if (mode === 'camera') {
                cameraMode.style.display = 'block';
                manualMode.style.display = 'none';
                btnCamera.classList.add('active');
                btnManual.classList.remove('active');
                
                if (!cameraStarted && typeof Html5Qrcode !== 'undefined') {
                    debugLog('🎬 Switching to camera mode...');
                    initScanner();
                }
            } else {
                cameraMode.style.display = 'none';
                manualMode.style.display = 'block';
                btnCamera.classList.remove('active');
                btnManual.classList.add('active');
                
                if (cameraStarted && html5QrcodeScanner) {
                    debugLog('⌨️ Switching to manual mode, pausing camera...');
                    html5QrcodeScanner.pause();
                }
                
                // Focus on input field
                setTimeout(() => {
                    document.getElementById('manual-qr-input').focus();
                }, 100);
            }
        }

        // Process manual QR input
        function processManualQR() {
            const qrInput = document.getElementById('manual-qr-input');
            const qrData = qrInput.value.trim();
            
            if (!qrData) {
                showError('❌ Vui lòng nhập mã QR hoặc ID vé');
                return;
            }

            debugLog('⌨️ Manual QR input: ' + qrData);
            isProcessing = true;
            handleQRCodeDetected(qrData);
        }

        // Handle Enter key in manual input
        document.addEventListener('DOMContentLoaded', function() {
            const manualInput = document.getElementById('manual-qr-input');
            if (manualInput) {
                manualInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        processManualQR();
                    }
                });
            }
        });

        // Gallery image selection (kept for reference, not used)
        function scanImageFromGallery() {
            // Not implemented in this version
            showError('❌ Vui lòng dùng chế độ Camera hoặc Nhập mã');
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (html5QrcodeScanner && cameraStarted) {
                html5QrcodeScanner.stop().catch(err => {
                    console.error('Error stopping scanner:', err);
                });
            }
        });
    </script>
</body>
</html>