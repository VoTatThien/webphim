<?php
/**
 * Generate Ticket PDF
 * Downloads ticket as PDF file with mobile-friendly design
 */

// Get ticket ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die('Invalid ticket ID');
}

// Include database functions
include __DIR__ . '/model/pdo.php';

// Query ticket info
$sql = "SELECT v.id, v.trang_thai, v.ghe, v.check_in_luc,
               p.tieu_de, p.img, 
               lc.ngay_chieu, 
               kgc.thoi_gian_chieu, 
               pc.name as tenphong,
               rc.ten_rap, rc.dia_chi
        FROM ve v
        JOIN phim p ON p.id = v.id_phim
        JOIN lichchieu lc ON lc.id = v.id_ngay_chieu
        JOIN khung_gio_chieu kgc ON kgc.id = v.id_thoi_gian_chieu
        JOIN phongchieu pc ON pc.id = kgc.id_phong
        JOIN rap_chieu rc ON rc.id = lc.id_rap
        WHERE v.id = ?
        LIMIT 1";

$ticket = pdo_query_one($sql, $id);

if (!$ticket) {
    die('Ticket not found');
}

// Determine ticket status
$status_info = ['color' => '#999', 'text' => 'Không xác định', 'icon' => '❓'];
switch ($ticket['trang_thai'] ?? 0) {
    case 1:
        $status_info = ['color' => '#4caf50', 'text' => 'Đã thanh toán', 'icon' => '✅'];
        break;
    case 2:
        $status_info = ['color' => '#2196f3', 'text' => 'Đã sử dụng', 'icon' => '✔️'];
        break;
    case 3:
        $status_info = ['color' => '#f44336', 'text' => 'Đã hủy', 'icon' => '❌'];
        break;
    case 4:
        $status_info = ['color' => '#2196f3', 'text' => 'Đã check-in', 'icon' => '🎬'];
        break;
}

// Fix image path
$base_dir = (strpos($_SERVER['REQUEST_URI'], '/webphim_hung/') !== false) ? '/webphim_hung/' : '/';
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$img_url = $ticket['img'] ?? '';
if (!empty($img_url)) {
    if (strpos($img_url, 'http') !== 0 && strpos($img_url, '/') !== 0) {
        $img_url = $protocol . $_SERVER['HTTP_HOST'] . $base_dir . 'Trang-nguoi-dung/imgavt/' . $img_url;
    }
}

// Create HTML content for PDF (Mobile-friendly design)
$html = '
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé #' . $id . '</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 10px;
        }
        
        .container {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .ticket-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        
        /* Movie Poster */
        .poster-section {
            position: relative;
            height: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            overflow: hidden;
        }
        
        .poster-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .no-poster {
            font-size: 64px;
        }
        
        /* Cinema Info Header */
        .cinema-header {
            background: linear-gradient(135deg, #4e5056ff 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .cinema-header h3 {
            font-size: 18px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .cinema-header p {
            font-size: 13px;
            opacity: 0.9;
            margin: 0;
        }
        
        /* Ticket Info */
        .info-section {
            padding: 20px;
        }
        
        .info-section h1 {
            font-size: 22px;
            color: #333;
            margin-bottom: 5px;
            word-wrap: break-word;
            font-weight: 600;
        }
        
        .ticket-code {
            color: #666;
            font-size: 13px;
            margin-bottom: 15px;
            font-family: "Courier New", monospace;
        }
        
        .status-line {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            padding: 12px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        
        .status-icon {
            font-size: 20px;
        }
        
        .status-text {
            font-weight: bold;
            color: #333;
        }
        
        .info-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .info-row:last-of-type {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-icon {
            font-size: 20px;
            min-width: 24px;
            text-align: center;
        }
        
        .info-content {
            flex: 1;
        }
        
        .info-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 4px;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }
        
        .seat-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        
        /* QR Code Section */
        .qr-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed #eee;
            text-align: center;
        }
        
        .qr-label {
            font-size: 11px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .qr-container {
            display: flex;
            justify-content: center;
            margin: 15px 0;
        }
        
        .qr-code {
            width: 180px;
            height: 180px;
            border: 3px solid #667eea;
            border-radius: 8px;
            padding: 8px;
            background: white;
        }
        
        .qr-note {
            font-size: 12px;
            color: #999;
            margin-top: 10px;
            font-style: italic;
        }
        
        /* Footer */
        .footer {
            padding: 15px 20px;
            background: #f9f9f9;
            border-top: 1px solid #eee;
            text-align: center;
            color: #999;
            font-size: 12px;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .container {
                max-width: 100%;
            }
            
            .ticket-card {
                box-shadow: none;
                margin-bottom: 0;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ticket-card">
            <!-- Poster -->
            <div class="poster-section">
                ' . (!empty($img_url) ? '<img src="' . htmlspecialchars($img_url) . '" alt="' . htmlspecialchars($ticket['tieu_de']) . '">' : '<div class="no-poster">🎬</div>') . '
            </div>
            
            <!-- Cinema Info -->
            <div class="cinema-header">
                <h3>🏢 ' . htmlspecialchars($ticket['ten_rap'] ?? "N/A") . '</h3>
                <p>📍 ' . htmlspecialchars($ticket['dia_chi'] ?? "N/A") . '</p>
            </div>
            
            <!-- Ticket Info -->
            <div class="info-section">
                <h1>' . htmlspecialchars($ticket['tieu_de'] ?? "N/A") . '</h1>
                <div class="ticket-code">Mã vé: ' . htmlspecialchars($id) . '</div>
                
                <!-- Status -->
                <div class="status-line">
                    <span class="status-icon">' . $status_info['icon'] . '</span>
                    <span class="status-text" style="color: ' . $status_info['color'] . ';">
                        ' . htmlspecialchars($status_info['text']) . '
                    </span>
                </div>
                
                <!-- Date & Time -->
                <div class="info-row">
                    <div class="info-icon">📅</div>
                    <div class="info-content">
                        <div class="info-label">Ngày Chiếu</div>
                        <div class="info-value">
                            ' . date('d/m/Y', strtotime($ticket['ngay_chieu'] ?? "")) . '
                        </div>
                    </div>
                </div>
                
                <!-- Time -->
                <div class="info-row">
                    <div class="info-icon">⏰</div>
                    <div class="info-content">
                        <div class="info-label">Giờ Chiếu</div>
                        <div class="info-value">
                            ' . date('H:i', strtotime($ticket['thoi_gian_chieu'] ?? "")) . '
                        </div>
                    </div>
                </div>
                
                <!-- Room -->
                <div class="info-row">
                    <div class="info-icon">🚪</div>
                    <div class="info-content">
                        <div class="info-label">Phòng Chiếu</div>
                        <div class="info-value">
                            ' . htmlspecialchars($ticket['tenphong'] ?? "N/A") . '
                        </div>
                    </div>
                </div>
                
                <!-- Seats -->
                <div class="info-row">
                    <div class="info-icon">🪑</div>
                    <div class="info-content">
                        <div class="info-label">Ghế</div>
                        <div class="info-value">
                            <div class="seat-badge">
                                ' . htmlspecialchars($ticket['ghe'] ?? "N/A") . '
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- QR Code -->
                <div class="qr-section">
                    <div class="qr-label">Mã QR Vé</div>
                    <div class="qr-container">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($protocol . $_SERVER['HTTP_HOST'] . $base_dir . 'Trang-nguoi-dung/quete.php?id=' . $id) . '" alt="QR Code" class="qr-code">
                    </div>
                    <p class="qr-note">Quét mã QR này để xem thông tin vé</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                🎫 Galaxy Studio Cinema | In vé để giữ lưu niệm
            </div>
        </div>
    </div>
    
    <script>
        // Auto print khi tải xong
        window.print();
    </script>
</body>
</html>
';

// Output as HTML
header('Content-Type: text/html; charset=utf-8');
echo $html;
?>

