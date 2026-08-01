<?php include __DIR__ . '/../home/sideheader.php'; ?>

<style>
    #qr-scanner {
        width: 100%;
        max-width: 600px;
        height: 400px;
        margin: 0 auto;
        border: 3px solid #007bff;
        border-radius: 8px;
        overflow: hidden;
        background: #000;
        display: none;
    }

    .scanner-container {
        background: #f5f5f5;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .tab-btn {
        padding: 10px 20px;
        border: none;
        background: #f0f0f0;
        cursor: pointer;
        border-radius: 5px;
        font-weight: bold;
        transition: all 0.3s;
    }

    .tab-btn.active {
        background: #007bff;
        color: white;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .status-card {
        border-radius: 12px;
        padding: 25px;
        margin-top: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        animation: slideIn 0.3s ease-in-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .status-success {
        background: linear-gradient(135deg, #c9cac9ff 0%, #a6d6c8ff 100%);
        color: Black;
    }

    .status-error {
        background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
        color: white;
    }

    .status-title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .status-subtitle {
        font-size: 14px;
        opacity: 0.9;
        margin-bottom: 15px;
    }

    .ticket-info {
        background: rgba(255,255,255,0.15);
        padding: 15px;
        border-radius: 8px;
        font-size: 15px;
        line-height: 1.8;
    }

    .button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
        background: #007bff;
        color: white;
        transition: all 0.3s;
    }

    .button:hover {
        background: #0056b3;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th {
        background: #007bff;
        color: white;
        padding: 12px;
        text-align: left;
    }

    /* Ensure close button is always clickable */
    .side-header-close {
        pointer-events: auto !important;
        z-index: 1001 !important;
    }

    .history-table td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
    }

    .history-table tr:hover {
        background: #f9f9f9;
    }
</style>

<div class="content-body">
    <h2>📱 Kiểm Tra & Check-in Vé</h2>

    <!-- Staff Info -->
    <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <strong>👤 Nhân viên:</strong> <?= htmlspecialchars($_SESSION['user1']['name'] ?? 'N/A') ?> 
        | <strong>🏢 Rạp:</strong> <?= htmlspecialchars($_SESSION['user1']['id_rap'] ?? 'N/A') ?>
        | <strong>⏰ Thời gian:</strong> <span id="current-time"></span>
    </div>

    <!-- Scanner Mode Toggle -->
    <div class="scanner-mode-selector" style="margin-bottom: 20px; display: flex; gap: 15px; background: #fff; padding: 12px 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); align-items: center; border-left: 5px solid #007bff;">
        <span style="font-weight: bold; color: #4b5563; font-size: 15px;">🎯 Chế độ soát:</span>
        <label class="mode-label" style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 15px; margin: 0;">
            <input type="radio" name="scan_mode" value="ticket" checked onclick="setScanMode('ticket')"> 🎟️ Soát Vé Xem Phim
        </label>
        <label class="mode-label" style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 15px; margin: 0; margin-left: 10px;">
            <input type="radio" name="scan_mode" value="fb" onclick="setScanMode('fb')"> 🍿 Nhận Đồ Ăn F&B Fast Track
        </label>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <button class="tab-btn" onclick="switchTab('camera')">📷 Quét QR (Camera)</button>
        <button class="tab-btn active" onclick="switchTab('manual')">⌨️ Nhập Mã Vé</button>
        <button class="tab-btn" onclick="switchTab('history')">📋 Lịch Sử Check-in</button>
    </div>

    <!-- Camera Tab -->
    <div class="tab-content" id="tab-camera">
        <div class="scanner-container">
            <h5>📷 Quét Mã QR từ Camera</h5>
            <video id="qr-scanner"></video>
            <canvas id="debug-canvas" style="display:none;"></canvas>
            <div id="camera-status" style="text-align: center; padding: 20px;">
                <button class="button" onclick="startCamera()">▶️ Bắt đầu Quét</button>
            </div>
            <div id="camera-error" style="display:none; color:red; text-align:center; margin-top:10px;"></div>
            <div id="debug-info" style="display:none; background:#f0f0f0; padding:10px; margin-top:10px; border-radius:4px; font-size:12px; color:#666;">
                <strong>🔍 Debug Info:</strong>
                <div id="debug-text">Chưa bắt đầu quét</div>
            </div>
        </div>
    </div>

    <!-- Manual Input Tab -->
    <div class="tab-content active" id="tab-manual">
        <div class="scanner-container">
            <h5>⌨️ Nhập Mã Vé</h5>
            <p style="color: #666; margin-bottom: 15px; font-size: 14px;">
                💡 Nhập ID vé hoặc paste URL vé để kiểm tra. Ví dụ: <code>434</code> hoặc <code>https://localhost/webphim_hung/Trang-nguoi-dung/quete.php?id=434</code>
            </p>
            <form id="form-manual" style="display: flex; gap: 10px;">
                <input 
                    type="text" 
                    id="ma-ve-input" 
                    placeholder="Nhập mã vé hoặc quét barcode..." 
                    class="form-control"
                    style="flex: 1; padding: 12px; font-size: 16px;"
                    required
                />
                <button type="submit" class="button">🔍 Kiểm Tra</button>
            </form>
        </div>
    </div>

    <!-- History Tab -->
    <div class="tab-content" id="tab-history">
        <div class="scanner-container">
            <h5>📋 Lịch Sử Check-in Hôm Nay</h5>
            <div id="history-container" style="margin-top: 15px;">
                <p style="text-align: center; color: #999;">⏳ Đang tải...</p>
            </div>
        </div>
    </div>

    <!-- Status Display -->
    <div id="status-container"></div>
</div>

<script>
    let currentTicket = null;
    let scanMode = 'ticket'; // 'ticket' or 'fb'

    function setScanMode(mode) {
        scanMode = mode;
        const radio = document.querySelector(`input[name="scan_mode"][value="${mode}"]`);
        if (radio) radio.checked = true;
        document.getElementById('status-container').innerHTML = '';
        console.log('🎯 Đã đổi chế độ quét sang:', mode);
    }
    let cameraStream = null;
    let scanning = false;
    let scanInterval = null;
    let barcodeDetector = null;

    // Update current time
    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleString('vi-VN');
    }
    setInterval(updateTime, 1000);
    updateTime();

    // Switch tabs
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        
        document.getElementById('tab-' + tabName).classList.add('active');
        event.target.classList.add('active');

        // Clear result when switching tabs
        document.getElementById('status-container').innerHTML = '';

        if (tabName === 'history') {
            loadHistory();
        }
    }

    // Start camera with BarcodeDetector API
    async function startCamera() {
        if (scanning) return;
        
        // Khởi tạo BarcodeDetector
        try {
            barcodeDetector = new BarcodeDetector({ formats: ['qr_code'] });
        } catch (e) {
            console.warn('BarcodeDetector không hỗ trợ, dùng fallback');
        }
        
        const video = document.getElementById('qr-scanner');
        const errorDiv = document.getElementById('camera-error');
        const statusDiv = document.getElementById('camera-status');
        const debugDiv = document.getElementById('debug-info');
        const debugText = document.getElementById('debug-text');
        
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
            statusDiv.innerHTML = '<button class="button" onclick="stopCamera()">⏹️ Dừng Quét</button>';
            errorDiv.style.display = 'none';
            if (debugDiv) debugDiv.style.display = 'block';
            if (debugText) debugText.innerHTML = '⏳ Đang khởi động camera...';
            
            // Wait for video to load before scanning
            video.onloadedmetadata = () => {
                video.play();
                scanning = true;
                if (debugText) debugText.innerHTML = `✅ Camera bật thành công<br>Độ phân giải: ${video.videoWidth}x${video.videoHeight}px<br>⏳ Đang quét QR code...`;
                console.log('✅ Camera started:', video.videoWidth, 'x', video.videoHeight);
                scanQRCode();
            };
        } catch (err) {
            console.error('❌ Camera error:', err);
            errorDiv.style.display = 'block';
            errorDiv.textContent = '❌ Lỗi: ' + err.message + '. Vui lòng cho phép truy cập camera.';
            if (debugDiv) debugDiv.style.display = 'block';
            if (debugText) debugText.innerHTML = '❌ Lỗi camera: ' + err.message;
        }
    }

    // Stop camera
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
        document.getElementById('camera-status').innerHTML = '<button class="button" onclick="startCamera()">▶️ Bắt đầu Quét</button>';
    }

    // Scan QR code from video using BarcodeDetector API
    function scanQRCode() {
        if (!scanning) return;
        
        const video = document.getElementById('qr-scanner');
        
        // Quét mỗi 100ms
        scanInterval = setInterval(async () => {
            if (!scanning || !video) return;
            
            // Kiểm tra video đã ready chưa
            if (video.videoWidth === 0 || video.videoHeight === 0) {
                return; // Video chưa ready, bỏ qua frame này
            }
            
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
                    const debugText = document.getElementById('debug-text');
                    if (debugText) debugText.innerHTML += `<br>✅ QR Detected: ${qrData}`;
                    stopCamera();
                    checkTicket(qrData);
                    return;
                }
            } catch (err) {
                console.error('Scan error:', err);
            }
        }, 100);
    }

    // Check ticket (STEP 1)
    function checkTicket(maVe) {
        if (!maVe || maVe.trim() === '') {
            displayError('Vui lòng nhập hoặc quét mã vé');
            return;
        }

        // Nếu QR code chứa URL, trích xuất ID
        let ticketCode = maVe.trim();
        
        console.log('🔍 QR Data nhận được:', ticketCode);
        
        // Tự động chuyển chế độ nếu phát hiện &fb=1 hoặc fb=1 trong dữ liệu quét
        if (ticketCode.includes('&fb=1') || ticketCode.includes('?fb=1') || ticketCode.includes('fb=1')) {
            console.log('⚡ Phát hiện F&B QR code, tự động chuyển sang chế độ Nhận F&B');
            setScanMode('fb');
        }
        
        // Cố gắng parse URL nếu nó là URL
        if (ticketCode.startsWith('http://') || ticketCode.startsWith('https://')) {
            try {
                const url = new URL(ticketCode);
                const id = url.searchParams.get('id');
                
                if (id) {
                    console.log('✅ Trích xuất ID từ URL:', id);
                    ticketCode = id;
                } else {
                    // Nếu không có ?id=, thử lấy từ pathname
                    const pathMatch = url.pathname.match(/\/(\d+)$/);
                    if (pathMatch) {
                        console.log('✅ Trích xuất ID từ pathname:', pathMatch[1]);
                        ticketCode = pathMatch[1];
                    } else {
                        console.warn('⚠️ URL không chứa ID:', ticketCode);
                    }
                }
            } catch (e) {
                console.warn('⚠️ URL parse error, dùng toàn bộ data:', maVe, e);
            }
        } else if (/^\d+$/.test(ticketCode)) {
            // Nếu là số, dùng trực tiếp
            console.log('✅ Đã nhận ID số:', ticketCode);
        } else {
            console.log('⚠️ Format không rõ, dùng toàn bộ:', ticketCode);
        }

        const statusContainer = document.getElementById('status-container');
        statusContainer.innerHTML = '<div style="text-align:center;"><p>⏳ Đang kiểm tra...</p></div>';
        
        if (scanMode === 'fb') {
            console.log('📤 Gửi request check F&B với mã:', ticketCode);
            fetch('index.php?act=scanve_fb_check', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ma_ve: ticketCode })
            })
            .then(res => {
                if (!res.ok) throw new Error('Lỗi kết nối: ' + res.status);
                return res.json();
            })
            .then(data => {
                console.log('✅ Kết quả soát F&B:', data);
                if (data.success) {
                    displayFBSuccess(data.ticket);
                } else {
                    if (data.details) {
                        displayFBAlreadyClaimed(data.message, data.details);
                    } else {
                        displayError(data.message || 'Lỗi soát F&B');
                    }
                }
            })
            .catch(err => {
                console.error('❌ Lỗi F&B:', err);
                displayError('Lỗi kết nối F&B: ' + err.message);
            });
        } else {
            console.log('📤 Gửi request check-in với mã:', ticketCode);
            fetch('index.php?act=scanve_check', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ma_ve: ticketCode })
            })
            .then(res => {
                console.log('📥 Response status:', res.status, res.ok);
                if (!res.ok) throw new Error('Lỗi kết nối: ' + res.status);
                return res.text().then(text => {
                    console.log('📥 Raw response:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('❌ JSON parse error:', e);
                        throw new Error('Response không phải JSON: ' + text.substring(0, 100));
                    }
                });
            })
            .then(data => {
                console.log('✅ Kiểm tra vé:', data);
                if (data.success && data.ticket) {
                    currentTicket = data.ticket;
                    // Check if ticket is already checked in (trang_thai == 4)
                    if (data.ticket.trang_thai == 4) {
                        console.log('⏱️ Vé đã check-in');
                        displayAlreadyCheckedIn(data.ticket);
                    } else {
                        console.log('✅ Vé hợp lệ, hiển thị check-in button');
                        displayCheckResult(data.ticket);
                    }
                } else {
                    console.error('❌ Response không success:', data);
                    displayError(data.message || 'Vé không hợp lệ hoặc không tồn tại');
                }
            })
            .catch(err => {
                console.error('❌ Lỗi:', err);
                displayError('Lỗi kết nối: ' + err.message);
            });
        }
    }

    // Display check result with button
    function displayCheckResult(ticket) {
        const html = `
            <div class="status-card status-success">
                <div class="status-title">VÉ HỢP LỆ</div>
                <div class="status-subtitle">Nhấn CHECK-IN để xác nhận khách vào</div>
                <div class="ticket-info">
                    <p><strong>🎬 Phim:</strong> ${escapeHtml(ticket.movie_title || 'N/A')}</p>
                    <p><strong>📅 Ngày:</strong> ${escapeHtml(ticket.screening_date || 'N/A')}</p>
                    <p><strong>⏰ Giờ:</strong> ${escapeHtml(ticket.screening_time || 'N/A')}</p>
                    <p><strong>🚪 Phòng:</strong> ${escapeHtml(ticket.room_name || 'N/A')}</p>
                    <p><strong>💺 Ghế:</strong> ${escapeHtml(ticket.seat || 'N/A')}</p>
                </div>
                <button class="button" onclick="confirmCheckin()" style="width:100%; margin-top:15px; padding:15px; font-size:16px; background:linear-gradient(135deg, #4c4f5aff 0%, #534f56ff 100%);">
                    CHECK-IN NGAY
                </button>
            </div>
        `;
        document.getElementById('status-container').innerHTML = html;
    }

    function displayFBSuccess(ticket) {
        const html = `
            <div class="status-card status-success" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                <div class="status-title" style="color: white; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 8px;">🍿 NHẬN F&B THÀNH CÔNG</div>
                <div class="status-subtitle" style="color: rgba(255,255,255,0.9); margin-top: 8px;">Hãy bàn giao đồ ăn cho khách hàng</div>
                <div class="ticket-info" style="background: rgba(255,255,255,0.2); color: white; margin-top: 15px;">
                    <p><strong>👤 Khách hàng:</strong> ${escapeHtml(ticket.user_name || 'Khách vãng lai')}</p>
                    <p><strong>🍿 Combo:</strong> <span style="font-size: 1.15em; font-weight: bold; color: #fef08a;">${escapeHtml(ticket.combo || 'N/A')}</span></p>
                    <p><strong>🎬 Phim đặt kèm:</strong> ${escapeHtml(ticket.movie_title || 'N/A')}</p>
                    <p><strong>🎟️ Mã Vé:</strong> #${escapeHtml(ticket.id)}</p>
                </div>
            </div>
        `;
        document.getElementById('status-container').innerHTML = html;
        
        // Tự động clear sau 4 giây và quét tiếp
        setTimeout(() => {
            document.getElementById('status-container').innerHTML = '';
            if (!scanning && document.getElementById('tab-camera').classList.contains('active')) {
                startCamera();
            }
        }, 4000);
    }

    function displayFBAlreadyClaimed(msg, details) {
        const html = `
            <div class="status-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: 2px solid #b45309;">
                <div class="status-title" style="color: white; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 8px;">⚠️ COMBO ĐÃ NHẬN RỒI</div>
                <div class="status-subtitle" style="color: rgba(255,255,255,0.9); margin-top: 8px;">${escapeHtml(msg)}</div>
                <div class="ticket-info" style="background: rgba(0,0,0,0.15); color: white; margin-top: 15px;">
                    <p><strong>✓ Chi tiết:</strong> ${escapeHtml(details)}</p>
                </div>
            </div>
        `;
        document.getElementById('status-container').innerHTML = html;
    }

    // Display if already checked in
    function displayAlreadyCheckedIn(ticket) {
        const html = `
            <div class="status-card" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 2px solid #ffc107; color: #856404;">
                <div class="status-title" style="color: #856404;">ĐÃ CHECK-IN RỒI</div>
                <div class="status-subtitle" style="color: #856404;">Vé này đã được sử dụng</div>
                <div class="ticket-info" style="background: rgba(0,0,0,0.05); color: inherit;">
                    <p><strong>🎬 Phim:</strong> ${escapeHtml(ticket.movie_title || 'N/A')}</p>
                    <p><strong>📅 Ngày:</strong> ${escapeHtml(ticket.screening_date || 'N/A')}</p>
                    <p><strong>⏰ Giờ:</strong> ${escapeHtml(ticket.screening_time || 'N/A')}</p>
                    <p><strong>🚪 Phòng:</strong> ${escapeHtml(ticket.room_name || 'N/A')}</p>
                    <p><strong>💺 Ghế:</strong> ${escapeHtml(ticket.seat || 'N/A')}</p>
                    <p style="margin-top: 15px; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 10px;"><strong>✓ Check-in lúc:</strong> ${escapeHtml(ticket.check_in_luc || 'N/A')}</p>
                </div>
            </div>
        `;
        document.getElementById('status-container').innerHTML = html;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Confirm check-in (STEP 2)
    function confirmCheckin() {
        if (!currentTicket || !currentTicket.id) {
            displayError('Lỗi: Không tìm thấy vé');
            return;
        }
        
        const statusContainer = document.getElementById('status-container');
        statusContainer.innerHTML = '<div style="text-align:center;"><p>Đang xác nhận check-in...</p></div>';
        
        console.log('Gửi check-in với ID:', currentTicket.id);
        
        fetch('index.php?act=scanve_new', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_ve: currentTicket.id })
        })
        .then(res => {
            console.log('Check-in response status:', res.status);
            if (!res.ok) throw new Error('Lỗi kết nối: ' + res.status);
            return res.text().then(text => {
                console.log('Raw response:', text.substring(0, 200));
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    throw new Error('Response không phải JSON');
                }
            });
        })
        .then(data => {
            console.log('Check-in response:', data);
            if (data.success && data.ticket) {
                console.log('Check-in thành công, hiển thị success');
                displaySuccess(data.ticket);
                currentTicket = null;
            } else {
                console.error('Check-in không thành công:', data);
                displayError(data.message || 'Check-in thất bại');
            }
        })
        .catch(err => {
            console.error('Check-in error:', err);
            displayError('Lỗi: ' + err.message);
        });
    }

    // Display success
    function displaySuccess(ticket) {
        const html = `
            <div class="status-card status-success">
                <div class="status-title">🎉 CHECK-IN THÀNH CÔNG</div>
                <div class="status-subtitle">Khách được vào phòng chiếu</div>
                <div class="ticket-info">
                    <p><strong>🎬 Phim:</strong> ${escapeHtml(ticket.movie_title || 'N/A')}</p>
                    <p><strong>📅 Ngày:</strong> ${escapeHtml(ticket.screening_date || 'N/A')}</p>
                    <p><strong>⏰ Giờ:</strong> ${escapeHtml(ticket.screening_time || 'N/A')}</p>
                    <p><strong>🚪 Phòng:</strong> ${escapeHtml(ticket.room_name || 'N/A')}</p>
                    <p><strong>💺 Ghế:</strong> ${escapeHtml(ticket.seat || 'N/A')}</p>
                </div>
            </div>
        `;
        document.getElementById('status-container').innerHTML = html;
        
        // Clear result after 3 seconds and reset camera
        setTimeout(() => {
            document.getElementById('status-container').innerHTML = '';
            // Tự động quay lại tab camera để quét tiếp
            document.getElementById('tab-camera').classList.add('active');
            document.getElementById('tab-manual').classList.remove('active');
            document.getElementById('tab-history').classList.remove('active');
            document.querySelectorAll('.tab-btn')[0].classList.add('active');
            document.querySelectorAll('.tab-btn')[1].classList.remove('active');
            document.querySelectorAll('.tab-btn')[2].classList.remove('active');
            
            // Tự động bắt đầu quét lại
            if (!scanning) {
                startCamera();
            }
        }, 3000);
    }

    // Display error
    function displayError(msg) {
        const html = `
            <div class="status-card status-error">
                <div class="status-title">❌ LỖI</div>
                <p>${escapeHtml(msg)}</p>
            </div>
        `;
        document.getElementById('status-container').innerHTML = html;
    }

    // Load history
    function loadHistory() {
        const container = document.getElementById('history-container');
        fetch('index.php?act=scanve_history')
            .then(r => r.json())
            .then(data => {
                if (data.success && data.history && data.history.length) {
                    let html = '<table class="history-table"><thead><tr><th>Mã Vé</th><th>Phim</th><th>Giờ Check-in</th><th>Nhân viên</th></tr></thead><tbody>';
                    data.history.forEach(item => {
                        html += `<tr>
                            <td><strong>${item.ma_ve}</strong></td>
                            <td>${item.phim}</td>
                            <td>${item.check_in_time}</td>
                            <td>${item.staff}</td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p style="padding: 30px; text-align:center; color: #999;">📭 Chưa có check-in nào hôm nay</p>';
                }
            })
            .catch(err => {
                console.error('History error:', err);
                container.innerHTML = '<p style="color:red; text-align:center; padding:20px;">Lỗi tải dữ liệu</p>';
            });
    }

    // Manual Form Submit
    const formManual = document.getElementById('form-manual');
    if (formManual) {
        formManual.addEventListener('submit', (e) => {
            e.preventDefault();
            const maVe = document.getElementById('ma-ve-input').value;
            checkTicket(maVe);
            document.getElementById('ma-ve-input').value = '';
        });
    }

    // Vanilla JS - Ensure sidebar close button works
    function attachSidebarCloseHandler() {
        const closeBtn = document.querySelector('.side-header-close');
        const sideHeader = document.querySelector('.side-header');
        
        if (closeBtn && sideHeader) {
            // Remove any existing listeners first
            const newCloseBtn = closeBtn.cloneNode(true);
            closeBtn.parentNode.replaceChild(newCloseBtn, closeBtn);
            
            // Add new listener
            newCloseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                sideHeader.classList.remove('show');
                sideHeader.classList.add('hide');
                console.log('✓ Sidebar closed');
            }, true);
            
            console.log('✓ Sidebar close handler attached');
            return true;
        }
        return false;
    }

    // Attach sidebar toggle button handler
    function attachSidebarToggleHandler() {
        const toggleBtn = document.querySelector('.side-header-toggle');
        const sideHeader = document.querySelector('.side-header');
        
        if (toggleBtn && sideHeader) {
            const newToggleBtn = toggleBtn.cloneNode(true);
            toggleBtn.parentNode.replaceChild(newToggleBtn, toggleBtn);
            
            newToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (sideHeader.classList.contains('show')) {
                    sideHeader.classList.remove('show');
                    sideHeader.classList.add('hide');
                } else {
                    sideHeader.classList.remove('hide');
                    sideHeader.classList.add('show');
                }
                console.log('✓ Sidebar toggled');
            }, true);
            
            console.log('✓ Sidebar toggle handler attached');
            return true;
        }
        return false;
    }

    // Try immediately
    if (!attachSidebarCloseHandler()) {
        // If not found, retry after DOMContentLoaded
        document.addEventListener('DOMContentLoaded', attachSidebarCloseHandler);
        
        // Also retry after 500ms (in case async loading)
        setTimeout(attachSidebarCloseHandler, 500);
    }

    // Attach toggle handler
    if (!attachSidebarToggleHandler()) {
        document.addEventListener('DOMContentLoaded', attachSidebarToggleHandler);
        setTimeout(attachSidebarToggleHandler, 500);
    }

    // Retry every 1s for 5 seconds (for dynamic content)
    let retries = 0;
    const retryInterval = setInterval(() => {
        if (attachSidebarCloseHandler() && attachSidebarToggleHandler() || retries++ > 5) {
            clearInterval(retryInterval);
        }
    }, 1000);
</script>

<!-- jsQR Library from Local (không cần CDN) -->
<script src="../js/jsQR.min.js"></script>

