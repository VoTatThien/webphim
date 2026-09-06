<?php
/**
 * Sepay Webhook Handler - Galaxy Studio
 * Tích hợp Sepay Payment Gateway với hệ thống quản lý vé
 * 
 * Xem hướng dẫn tại: https://docs.sepay.vn/tich-hop-webhooks.html
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include config
require('config.php');

// PDO Connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    http_response_code(500);
    file_put_contents(__DIR__ . '/webhook_logs.txt', date('Y-m-d H:i:s') . " Database error: " . $e->getMessage() . "\n", FILE_APPEND);
    exit;
}

// Log webhook for debugging
$log_file = __DIR__ . '/webhook_logs.txt';

// Lấy dữ liệu từ webhooks
// Xem các trường dữ liệu tại https://docs.sepay.vn/tich-hop-webhooks.html#du-lieu
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " No data received\n", FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'No data']);
    exit;
}

file_put_contents($log_file, date('Y-m-d H:i:s') . " Webhook received: " . json_encode($data) . "\n", FILE_APPEND);

try {
    // Khởi tạo các biến từ webhook
    $gateway = $data['gateway'] ?? '';
    $transaction_date = $data['transactionDate'] ?? '';
    $account_number = $data['accountNumber'] ?? '';
    $sub_account = $data['subAccount'] ?? null;
    $transfer_type = $data['transferType'] ?? 'in';
    $transfer_amount = (int)($data['transferAmount'] ?? 0);
    $accumulated = (int)($data['accumulated'] ?? 0);
    $code = $data['code'] ?? null;
    $transaction_content = $data['content'] ?? '';
    $reference_number = $data['referenceCode'] ?? '';
    $body = $data['description'] ?? '';

    // Chỉ xử lý giao dịch tiền vào
    if ($transfer_type !== "in") {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " Skipped: Not incoming transfer\n", FILE_APPEND);
        echo json_encode(['success' => true, 'message' => 'Not incoming transfer']);
        exit;
    }

    if ($transfer_amount <= 0) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " Skipped: Invalid amount\n", FILE_APPEND);
        echo json_encode(['success' => true, 'message' => 'Invalid amount']);
        exit;
    }

    $amount_in = $transfer_amount;
    $amount_out = 0;

    // ====================================================
    // BƯỚC 1: Tách mã vé từ nội dung thanh toán (TRƯỚC khi lưu transaction)
    // ====================================================
    // Biểu thức regex để khớp với mã vé (VE123)
    $regex = '/VE(\d+)/i';
    preg_match($regex, $transaction_content, $matches);

    if (!isset($matches[1])) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " No ticket ID found in: $transaction_content\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Ticket ID not found']);
        exit;
    }

    $ticket_id = (int)$matches[1];
    file_put_contents($log_file, date('Y-m-d H:i:s') . " Ticket ID extracted: $ticket_id\n", FILE_APPEND);

    // ====================================================
    // BƯỚC 2: Tìm vé trong database
    // ====================================================
    $ticket_sql = "SELECT v.*, tk.email, tk.ten_dang_nhap, tk.id_diem, p.tieu_de as ten_phim, 
                          lc.ngay_chieu, kgc.thoi_gian_chieu as gio_bat_dau, r.ten_rap
                   FROM ve v
                   LEFT JOIN taikhoan tk ON tk.id = v.id_tk
                   LEFT JOIN phim p ON p.id = v.id_phim
                   LEFT JOIN lichchieu lc ON lc.id = v.id_ngay_chieu
                   LEFT JOIN khung_gio_chieu kgc ON kgc.id = v.id_thoi_gian_chieu
                   LEFT JOIN rap r ON r.id = v.id_rap
                   WHERE v.id = :ticket_id";

    $stmt = $pdo->prepare($ticket_sql);
    $stmt->execute([':ticket_id' => $ticket_id]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " Ticket not found: ID=$ticket_id\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Ticket not found']);
        exit;
    }

    file_put_contents($log_file, date('Y-m-d H:i:s') . " Ticket found: ID=$ticket_id, Price=" . $ticket['price'] . ", UserID=" . $ticket['id_tk'] . "\n", FILE_APPEND);

    // ====================================================
    // BƯỚC 3: Kiểm tra số tiền khớp không
    // ====================================================
    $ticket_price = (int)$ticket['price'];
    if ($transfer_amount != $ticket_price) {
        file_put_contents($log_file, date('Y-m-d H:i:s') . " Amount mismatch: expected=$ticket_price, received=$transfer_amount\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Amount mismatch']);
        exit;
    }

    // ====================================================
    // BƯỚC 4: Kiểm tra vé đã thanh toán chưa
    // ====================================================
    if ($ticket['trang_thai'] == 1) { // 1 = Paid
        file_put_contents($log_file, date('Y-m-d H:i:s') . " Ticket already paid: ID=$ticket_id\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Ticket already paid']);
        exit;
    }

    // ====================================================
    // BƯỚC 5: Cập nhật trạng thái vé thành "Đã thanh toán"
    // ====================================================
    $update_ticket_sql = "UPDATE ve SET trang_thai = 1 WHERE id = :ticket_id";
    $stmt = $pdo->prepare($update_ticket_sql);
    $stmt->execute([':ticket_id' => $ticket_id]);

    file_put_contents($log_file, date('Y-m-d H:i:s') . " Ticket updated to paid: ID=$ticket_id\n", FILE_APPEND);

    // ====================================================
    // BƯỚC 6: Tích điểm cho user
    // ====================================================
    $points_earned = floor($transfer_amount * POINTS_PER_VND);
    if ($points_earned > 0 && $ticket['id_tk']) {
        $update_points_sql = "UPDATE taikhoan SET id_diem = COALESCE(id_diem, 0) + :points WHERE id = :user_id";
        $stmt = $pdo->prepare($update_points_sql);
        $stmt->execute([
            ':points' => $points_earned,
            ':user_id' => $ticket['id_tk']
        ]);

        file_put_contents($log_file, date('Y-m-d H:i:s') . " Points added: user_id={$ticket['id_tk']}, points=$points_earned (amount=$transfer_amount)\n", FILE_APPEND);
    }

    // ====================================================
    // BƯỚC 7: Lưu giao dịch vào bảng thanh_toan
    // ====================================================
    $sql_transaction = "INSERT INTO thanh_toan (id_hoa_don, phuong_thuc, ma_giao_dich, so_tien, trang_thai, thong_tin_thanh_toan, ngay_thanh_toan) 
                        VALUES (:invoice_id, 'qr_code', :reference, :amount, 'success', :info, NOW())";
    $stmt = $pdo->prepare($sql_transaction);
    $stmt->execute([
        ':invoice_id' => $ticket['id_hd'],
        ':reference' => $reference_number,
        ':amount' => $transfer_amount,
        ':info' => json_encode([
            'id_ve' => $ticket_id,
            'noi_dung' => $transaction_content,
            'sepay_reference' => $reference_number,
            'gateway' => 'Sepay'
        ])
    ]);

    file_put_contents($log_file, date('Y-m-d H:i:s') . " Transaction saved to thanh_toan table\n", FILE_APPEND);

    // ====================================================
    // BƯỚC 8: Gửi email xác nhận
    // ====================================================
    if ($ticket['email']) {
        send_confirmation_email(
            $ticket['email'],
            $ticket['ten_dang_nhap'],  // Sử dụng ten_dang_nhap thay vì name
            $ticket['ten_phim'],
            $ticket['ngay_chieu'],
            $ticket['gio_bat_dau'],
            $ticket['ten_rap'],
            $ticket['ghe'],
            $ticket['ma_ve'],
            $transfer_amount,
            $points_earned
        );
        file_put_contents($log_file, date('Y-m-d H:i:s') . " Confirmation email sent to: {$ticket['email']}\n", FILE_APPEND);
    }

    echo json_encode(['success' => true, 'message' => 'Ticket payment processed']);

} catch (Exception $e) {
    file_put_contents($log_file, date('Y-m-d H:i:s') . " Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Gửi email xác nhận thanh toán cho customer
 */
function send_confirmation_email($email, $name, $movie, $date, $time, $cinema, $seats, $ticket_code, $amount, $points) {
    require_once dirname(__DIR__) . '/PHPMailer/src/Exception.php';
    require_once dirname(__DIR__) . '/PHPMailer/src/PHPMailer.php';
    require_once dirname(__DIR__) . '/PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'thanhbang0162@gmail.com';
        $mail->Password   = 'qjca onic cfks clad';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('thanhbang0162@gmail.com', 'Galaxy Studio');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "=?UTF-8?B?" . base64_encode("✓ Xác nhận thanh toán vé xem phim - Galaxy Studio") . "?=";

        $base_path = '';
        if (preg_match('/^\/([^\/]+)\/(Trang-nguoi-dung|Trang-admin|Version_deploy)/', $_SERVER['REQUEST_URI'], $matches)) {
            $base_path = '/' . $matches[1];
        }

        // Generate QR code URL
        // Match ticket ID by query or extracting numbers from ticket_code
        $ticket_id_numeric = preg_replace('/[^0-9]/', '', $ticket_code);
        $qr_data = urlencode("http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $base_path . "/Trang-nguoi-dung/index.php?act=quetve&id=" . $ticket_id_numeric);
        $qr_code_url = ($_SERVER['REQUEST_SCHEME'] ?? 'http') . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $base_path . "/Trang-nguoi-dung/view/qr.php?data=" . $qr_data . "&t=" . time();

        $message = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #667eea; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { border: 1px solid #ddd; padding: 20px; border-radius: 0 0 5px 5px; }
                .ticket-info { background: #f5f5f5; padding: 15px; margin: 15px 0; border-left: 4px solid #667eea; }
                .ticket-info p { margin: 8px 0; }
                .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
                .highlight { color: #28a745; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🎬 XÁC NHẬN THANH TOÁN VÉ</h2>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>$name</strong>,</p>
                    <p>Cảm ơn bạn đã thanh toán vé xem phim tại <strong>Galaxy Studio</strong>. Thanh toán của bạn đã được xác nhận thành công.</p>
                    
                    <div class='ticket-info'>
                        <h3 style='margin-top: 0; color: #667eea;'>📋 Thông tin vé</h3>
                        <p><strong>Phim:</strong> $movie</p>
                        <p><strong>Rạp:</strong> $cinema</p>
                        <p><strong>Ngày chiếu:</strong> " . ($date ? date('d/m/Y', strtotime($date)) : 'N/A') . "</p>
                        <p><strong>Giờ chiếu:</strong> $time</p>
                        <p><strong>Ghế:</strong> $seats</p>
                        <p><strong>Mã vé:</strong> <span class='highlight'>$ticket_code</span></p>
                    </div>
                    
                    <div class='ticket-info'>
                        <h3 style='margin-top: 0; color: #28a745;'>💰 Chi tiết thanh toán</h3>
                        <p><strong>Số tiền:</strong> " . number_format($amount, 0, ',', '.') . " VND</p>
                        <p><strong>Điểm thưởng:</strong> <span class='highlight'>+ " . number_format($points, 0, ',', '.') . " điểm</span></p>
                    </div>
                    
                    <p>✓ Vé của bạn đã sẵn sàng! Vui lòng mang theo mã vé hoặc quét mã QR bên dưới để check-in.</p>
                    <div style='text-align: center; margin: 20px 0;'>
                        <img src='" . $qr_code_url . "' alt='QR Code' style='width: 200px; height: 200px; border: 1px solid #ddd; padding: 5px;'>
                    </div>
                    <p>Nếu có bất kỳ câu hỏi nào, vui lòng liên hệ chúng tôi.</p>
                    
                    <div class='footer'>
                        <p>Galaxy Studio - Rạp chiếu phim hàng đầu</p>
                        <p>Đây là email tự động, vui lòng không trả lời email này.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->Body = $message;
        $mail->send();

    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
    }
}

?>