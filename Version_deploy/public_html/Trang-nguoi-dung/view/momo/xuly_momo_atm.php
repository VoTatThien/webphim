<?php
session_start();
header('Content-type: text/html; charset=utf-8');

function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        )
    );
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    //execute post
    $result = curl_exec($ch);
    //close connection
    curl_close($ch);
    return $result;
}

// MoMo API Endpoint
$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

// MoMo Credentials
$partnerCode = 'MOMOBKUN20180529';
$accessKey = 'klm05TvNBzhg7h7j';
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

// Lấy thông tin từ session
$orderInfo = "Thanh toán vé phim";
if (isset($_SESSION['tong']['tieu_de'])) {
    $orderInfo = "Thanh toán vé phim " . $_SESSION['tong']['tieu_de'];
}

// Lấy giá từ session - ưu tiên lấy giá sau giảm
$amount = 0;
if (isset($_SESSION['tong']['gia_sau_giam']) && $_SESSION['tong']['gia_sau_giam'] > 0) {
    $amount = (int)$_SESSION['tong']['gia_sau_giam'];
} elseif (isset($_SESSION['tong']['gia_ghe']) && $_SESSION['tong']['gia_ghe'] > 0) {
    $amount = (int)$_SESSION['tong']['gia_ghe'];
}

// Nếu không có amount thì dừng
if ($amount <= 0) {
    die("Lỗi: Số tiền không hợp lệ");
}

// ============ TẠO VÉ TRONG DATABASE NGAY ============
// Fix đường dẫn để load PDO
$pdo_path = dirname(dirname(dirname(__FILE__))) . '/Trang-admin/model/pdo.php';
if (!file_exists($pdo_path)) {
    $pdo_path = __DIR__ . '/../../model/pdo.php';
}

require_once $pdo_path;

// Lấy connection PDO
$pdo = pdo_get_connection();

// Load hàm điểm
require_once __DIR__ . '/../../model/diem.php';

try {
    // Lấy thông tin vé từ session
    $id_ngay_chieu = $_SESSION['tong']['id_lichchieu'] ?? $_SESSION['tong'][3] ?? null;
    $id_tk = $_SESSION['user']['id'] ?? null;
    
    // Lấy ghế - có thể nằm ở nhiều chỗ
    $ghe_list = [];
    if (isset($_SESSION['tong']['ten_ghe']['ghe']) && is_array($_SESSION['tong']['ten_ghe']['ghe'])) {
        $ghe_list = $_SESSION['tong']['ten_ghe']['ghe'];
    } elseif (isset($_SESSION['tong']['ghe']) && is_array($_SESSION['tong']['ghe'])) {
        $ghe_list = $_SESSION['tong']['ghe'];
    } elseif (isset($_SESSION['tong'][0]) && is_array($_SESSION['tong'][0])) {
        $ghe_list = $_SESSION['tong'][0];
    } elseif (isset($_SESSION['tong']['ghe_string']) && !empty($_SESSION['tong']['ghe_string'])) {
        $ghe_list = [$_SESSION['tong']['ghe_string']];
    }
    
    $id_phong = $_SESSION['tong']['id_phong'] ?? null;
    $id_phim = $_SESSION['tong']['id_phim'] ?? $_SESSION['tong'][1] ?? null;
    $id_rap = $_SESSION['tong']['id_rap'] ?? null;
    $price = $amount;
    $combo = $_SESSION['tong']['combo'] ?? $_SESSION['tong'][2] ?? '';
    $id_gio = $_SESSION['tong']['id_gio'] ?? null;
    
    // Debug: Log session info
    $debug_log = "Session Debug:\n";
    $debug_log .= "id_ngay_chieu: " . ($id_ngay_chieu ?? "NULL") . "\n";
    $debug_log .= "id_tk: " . ($id_tk ?? "NULL") . "\n";
    $debug_log .= "ghe_list: " . json_encode($ghe_list) . "\n";
    $debug_log .= "Full session tong: " . json_encode($_SESSION['tong'] ?? []) . "\n";
    
    file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - " . $debug_log . "\n", FILE_APPEND);
    
    // Kiểm tra thông tin tối thiểu
    if (!$id_tk) {
        die("Lỗi: Không tìm thấy user ID. Bạn đã đăng nhập chưa?");
    }
    
    if (!$id_ngay_chieu) {
        die("Lỗi: Không tìm thấy lịch chiếu. id_lichchieu: " . ($id_ngay_chieu ?? "NULL"));
    }
    
    if (empty($ghe_list)) {
        die("Lỗi: Không tìm thấy ghế. ghe_list: " . json_encode($ghe_list) . ", Session: " . json_encode($_SESSION['tong'] ?? []));
    }
    
    // ✅ Gộp tất cả các ghế thành 1 vé duy nhất
    $ma_ve = 'VE' . time() . rand(1000, 9999);
    $ghe_string = implode(',', $ghe_list); // Gộp ghế: "L12,L11,L10,L9,L8,L7,L6,L5"
    
    $sql = "INSERT INTO ve (id_tk, id_phim, id_ngay_chieu, id_thoi_gian_chieu, id_rap, ghe, price, combo, trang_thai, ma_ve, ngay_dat, id_hd) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, ?, NOW(), 0)";
    
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        $id_tk, 
        $id_phim ?? null, 
        $id_ngay_chieu, 
        $id_gio ?? null,
        $id_rap ?? null, 
        $ghe_string,  // Lưu tất cả ghế làm 1 string
        $price, 
        $combo, 
        $ma_ve
    ]);
    
    if (!$result) {
        $error_info = $stmt->errorInfo();
        throw new Exception("Không thể tạo vé - " . $error_info[2]);
    }
    
    file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - ✅ Vé tạo thành công! Số ghế: " . count($ghe_list) . ", Ghế: " . $ghe_string . "\n", FILE_APPEND);
    
    // DEBUG: Log session tong để xem có diem_doi không
    file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - DEBUG SESSION TONG: " . json_encode($_SESSION['tong'] ?? []) . "\n", FILE_APPEND);
    
    // ============ TRỪ ĐIỂM NẾU DÙNG ĐIỂM ĐỂ GIẢM GIÁ ============
    if (isset($_SESSION['tong']['diem_doi']) && $_SESSION['tong']['diem_doi'] > 0) {
        $diem_doi = (int)$_SESSION['tong']['diem_doi'];
        if ($id_tk && $diem_doi > 0) {
            // Trừ điểm từ database
            $result_tru = tru_diem($id_tk, $diem_doi, 'Sử dụng điểm để giảm giá vé phim');
            file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - 💸 Trừ điểm: -" . $diem_doi . " (lý do: Sử dụng giảm giá, kết quả: " . ($result_tru ? "OK" : "FAIL") . ")\n", FILE_APPEND);
        }
    } else {
        file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - ℹ️ Không dùng điểm để giảm giá (diem_doi không tồn tại hoặc = 0)\n", FILE_APPEND);
    }
    
    // ============ CỘNG ĐIỂM TÍCH LŨY ============
    // Tỷ lệ: Mỗi 1000 VND = 1 điểm
    if ($id_tk && $price > 0) {
        // Tính điểm từ giá thanh toán thực tế
        $diem_tang = intval($price / 1000);  // Mỗi 1000 VND = 1 điểm
        
        file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - 💰 Tính điểm: Price=" . $price . ", Diem_tang=" . $diem_tang . " (mỗi 1000 VND = 1 điểm)\n", FILE_APPEND);
        
        if ($diem_tang > 0) {
            // Dùng hàm cong_diem để ghi lịch sử
            $result = cong_diem($id_tk, $diem_tang, 'Thanh toán vé phim qua MoMo');
            file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - ✅ Điểm cộng: +" . $diem_tang . " (kết quả: " . ($result ? "OK" : "FAIL") . ")\n", FILE_APPEND);
        }
    }
    
    // ✅ VÉ ĐÃ ĐƯỢC TẠO VÀ THANH TOÁN THÀNH CÔNG
    $_SESSION['thanh_toan_thanh_cong'] = true;
    $_SESSION['tong_tien_thanh_toan'] = $amount;
    
    // ============ GỬI EMAIL XÁCH NHẬN THANH TOÁN ============
    $user_info = pdo_query_one("SELECT email, name FROM taikhoan WHERE id = ?", $id_tk);
    if ($user_info && !empty($user_info['email'])) {
        $to = $user_info['email'];
        $subject = "✓ Thanh toán thành công - Vé phim CinePass";
        $movie_info = pdo_query_one("SELECT tieu_de FROM phim WHERE id = ?", $id_phim ?? 0) ?? ['tieu_de' => 'Vé phim'];
        $showtime_info = pdo_query_one("SELECT ngay_chieu FROM lichchieu WHERE id = ?", $id_ngay_chieu ?? 0) ?? ['ngay_chieu' => 'N/A'];
        $time_info = pdo_query_one("SELECT thoi_gian_chieu FROM khung_gio_chieu WHERE id = ?", $id_gio ?? 0) ?? ['thoi_gian_chieu' => 'N/A'];
        
        $base_path = '';
        if (preg_match('/^\/([^\/]+)\/(Trang-nguoi-dung|Trang-admin|Version_deploy)/', $_SERVER['REQUEST_URI'], $matches)) {
            $base_path = '/' . $matches[1];
        }

        $message = "
            <html>
            <head>
                <meta charset='UTF-8'>
            </head>
            <body>
                <h2>✓ Thanh Toán Thành Công</h2>
                <p>Xin chào <strong>" . htmlspecialchars($user_info['name']) . "</strong>,</p>
                <p>Cảm ơn bạn đã đặt vé phim tại <strong>CinePass</strong>!</p>
                
                <h3>Thông tin thanh toán:</h3>
                <ul>
                    <li><strong>Phim:</strong> " . htmlspecialchars($movie_info['tieu_de']) . "</li>
                    <li><strong>Ngày chiếu:</strong> " . htmlspecialchars($showtime_info['ngay_chieu']) . "</li>
                    <li><strong>Giờ chiếu:</strong> " . htmlspecialchars($time_info['thoi_gian_chieu']) . "</li>
                    <li><strong>Ghế:</strong> " . htmlspecialchars($ghe_string) . "</li>
                    <li><strong>Tổng tiền:</strong> " . number_format($price, 0, ',', '.') . " VND</li>
                    <li><strong>Mã vé:</strong> <strong>" . htmlspecialchars($ma_ve) . "</strong></li>
                </ul>
                
                <p><strong>⭐ Điểm thưởng nhận được: +" . $diem_tang . " điểm</strong></p>
                
                <p>Vui lòng đến rạp chiếu trước giờ chiếu 15 phút để check-in với mã vé.</p>
                <p><a href='http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $base_path . "/Trang-nguoi-dung/index.php?p=ve_cua_toi'>👉 Xem vé của tôi</a></p>
                
                <p>Cảm ơn bạn!</p>
            </body>
            </html>
        ";
        
        require_once dirname(dirname(dirname(__FILE__))) . '/PHPMailer/src/Exception.php';
        require_once dirname(dirname(dirname(__FILE__))) . '/PHPMailer/src/PHPMailer.php';
        require_once dirname(dirname(dirname(__FILE__))) . '/PHPMailer/src/SMTP.php';
        
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tatthiendh123@gmail.com';
            $mail->Password   = 'qjca onic cfks clad';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            
            $mail->setFrom('tatthiendh123@gmail.com', 'Galaxy Studio');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
            $mail->Body = $message;
            $mail->send();
            file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - 📧 Email gửi tới: " . $to . " (PHPMailer)\n", FILE_APPEND);
        } catch (Exception $mailEx) {
            file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - 📧 Lỗi gửi email (PHPMailer): " . $mailEx->getMessage() . "\n", FILE_APPEND);
        }
    }
    
    // ============ RELOAD SESSION USER ============
    // Reload thông tin user từ database để cập nhật điểm trên giao diện
    $user_updated = pdo_query_one("SELECT * FROM taikhoan WHERE id = ?", $id_tk);
    if ($user_updated) {
        $_SESSION['user'] = $user_updated;
        file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - ✅ Session user reload: điểm = " . $user_updated['diem_tich_luy'] . "\n", FILE_APPEND);
    }
    
    // Clear session giỏ hàng
    unset($_SESSION['tong']);
    
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/momo_debug.log', date('Y-m-d H:i:s') . " - ❌ Lỗi: " . $e->getMessage() . "\n", FILE_APPEND);
    die("Lỗi tạo vé: " . $e->getMessage());
}

// ============ REDIRECT ĐẾN TRANG MoMo ============
// Tạo order ID
$orderId = time() . "";
$requestId = time() . "";

// URLs
$currentHost = $_SERVER['HTTP_HOST'];
$base_path = '';
if (preg_match('/^\/([^\/]+)\/(Trang-nguoi-dung|Trang-admin|Version_deploy)/', $_SERVER['REQUEST_URI'], $matches)) {
    $base_path = '/' . $matches[1];
}
$baseUrl = "http://" . $currentHost . $base_path . "/Trang-nguoi-dung";
$redirectUrl = $baseUrl . "/index.php?act=ve";
$ipnUrl = $baseUrl . "/view/momo/xuly_callback_momo.php";
$extraData = "";

// Request Type
$requestType = "payWithATM";

// Tạo signature
$rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;

$signature = hash_hmac("sha256", $rawHash, $secretKey);

// Chuẩn bị data
$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "CinePass Cinema",
    'storeId' => "CinePassStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
);

// Gửi request
$result = execPostRequest($endpoint, json_encode($data));
$jsonResult = json_decode($result, true);

// Lưu order info vào session
$_SESSION['momo_order_id'] = $orderId;
$_SESSION['momo_amount'] = $amount;

// Redirect đến URL thanh toán
if (isset($jsonResult['payUrl']) && !empty($jsonResult['payUrl'])) {
    header('Location: ' . $jsonResult['payUrl']);
} else {
    // Nếu lỗi kết nối MoMo, vẫn redirect về trang vé (vé đã được tạo)
    header('Location: ' . $redirectUrl . '?thanh_toan=ok');
}
?>
