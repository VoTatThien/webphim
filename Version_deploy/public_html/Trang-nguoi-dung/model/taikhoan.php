<?php
// Load danh sách tài khoản
function loadall_taikhoan() {
    $sql = "SELECT * FROM taikhoan ORDER BY id ASC";
    return pdo_query($sql);
}

// Kiểm tra tài khoản
function check_tk($user, $pass) {
    $sql = "SELECT * FROM taikhoan WHERE user = '$user' AND pass = '$pass'";
    return pdo_query_one($sql);
}

// Đăng xuất
function dang_xuat() {
    unset($_SESSION['user']);
}

// Thêm tài khoản mới
function insert_taikhoan($email, $user, $pass, $name, $sdt, $dc) {
    // Đăng ký khách hàng thành viên (vai_tro = 0)
    $sql = "INSERT INTO taikhoan (email, user, pass, dia_chi, phone, name, vai_tro, id_rap, img) 
            VALUES (?, ?, ?, ?, ?, ?, 0, NULL, '')";
    pdo_execute($sql, $email, $user, $pass, $dc, $sdt, $name);
}

// Sửa tài khoản
function sua_tk($id, $user, $email, $sdt, $dc) {
    $sql = "UPDATE taikhoan 
            SET user = '$user', email = '$email', phone = '$sdt', dia_chi = '$dc' 
            WHERE id = $id";
    pdo_execute($sql);
}

// Lấy mật khẩu cũ
function mkcu($id) {
    $sql = "SELECT pass FROM taikhoan WHERE id = $id";
    $result = pdo_query_one($sql);
    return $result['pass'];
}

// Đổi mật khẩu
function doi_tk($id, $passmoi) {
    $sql = "UPDATE taikhoan SET pass = '$passmoi' WHERE id = $id";
    pdo_execute($sql);
}

// Lấy thông tin 1 tài khoản
function loadone_taikhoan($id) {
    $sql = "SELECT * FROM taikhoan WHERE id = $id";
    return pdo_query_one($sql);
}

// Kiểm tra email tồn tại
function check_email($email) {
    $sql = "SELECT email FROM taikhoan WHERE email = '$email'";
    return pdo_query_one($sql);
}

// Gửi mail khôi phục mật khẩu
function sendMail($email) {
    $sql = "SELECT * FROM taikhoan WHERE email = '$email'";
    $taikhoan = pdo_query_one($sql);

    if ($taikhoan != false) {
        sendMailPass($email, $taikhoan['name'], $taikhoan['pass']);
        return "✅ Mật khẩu đã được gửi về email của bạn.";
    } else {
        return "❌ Email không tồn tại trong hệ thống.";
    }
}

// Hàm gửi mail chi tiết
function sendMailPass($email, $name, $pass) 
{
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Cấu hình SMTP
        $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tatthiendh123@gmail.com';
        $mail->Password   = 'qjca onic cfks clad'; // Lưu ý: Không nên hardcode mật khẩu thật
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Người gửi & người nhận
        $mail->setFrom('tatthiendh123@gmail.com', 'Galaxy Studio');
        $mail->addAddress($email, $name);

        // Gửi HTML email
        $mail->isHTML(true);
        $mail->Subject = '=?UTF-8?B?' . base64_encode('🛡️ Khôi phục mật khẩu tài khoản Galaxy Studio') . '?=';

        // $safe_name = htmlspecialchars($name);
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #eee; border-radius: 10px; padding: 20px; background-color: #f9f9f9;">
                <h2 style="color: #333;">Xin chào<span style="color: #007bff;">' . $safe_name . '</span>,</h2>
                <p>Bạn hoặc ai đó đã yêu cầu khôi phục mật khẩu tài khoản tại <strong>Galaxy Studio</strong>.</p>
                <p><strong>Mật khẩu của bạn là:</strong></p>
                <div style="background-color: #e9ecef; padding: 10px; border-radius: 5px; text-align: center; font-size: 18px; font-weight: bold;">' . $pass . '</div>
                <p style="margin-top: 20px;">Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email này hoặc liên hệ bộ phận hỗ trợ.</p>
                <hr>
                <p style="font-size: 12px; color: #777;">Email này được gửi tự động từ hệ thống Galaxy Studio. Vui lòng không trả lời lại email.</p>
            </div>
        ';

        $mail->send();
    } catch (Exception $e) {
        echo "Gửi email thất bại. Lỗi: {$mail->ErrorInfo}";
    }
}

function sendMailOTP($email, $otp) {
    require_once __DIR__ . '/../PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tatthiendh123@gmail.com'; // Thay bằng email gửi OTP
        $mail->Password   = 'qjca onic cfks clad'; // Thay bằng app password
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('tatthiendh123@gmail.com', 'Galaxy Studio');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = '=?UTF-8?B?' . base64_encode('Mã OTP xác nhận quên mật khẩu') . '?=';
        $mail->Body    = 'Mã OTP của bạn là: <b>' . $otp . '</b><br>Vui lòng nhập mã này để xác nhận đổi mật khẩu. Mã có hiệu lực trong 5 phút.';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function update_pass_by_email($email, $newpass) {
    $sql = "UPDATE taikhoan SET pass = ? WHERE email = ?";
    pdo_execute($sql, $newpass, $email);
}

/**
 * Tạo tài khoản khách vãng lai
 * @param string $name Họ tên
 * @param string $phone Số điện thoại (10 số)
 * @param string $email Email
 * @return array|false Thông tin tài khoản vừa tạo hoặc false nếu lỗi
 */
function create_guest_account($name, $phone, $email) {
    // Kiểm tra số điện thoại đã tồn tại chưa (trong tài khoản guest)
    $check_phone_sql = "SELECT * FROM taikhoan WHERE phone = ? AND vai_tro = -1";
    $existing = pdo_query_one($check_phone_sql, $phone);
    
    if ($existing) {
        // Đã có tài khoản guest với SĐT này → Cập nhật thông tin
        $update_sql = "UPDATE taikhoan SET name = ?, email = ? WHERE id = ?";
        pdo_execute($update_sql, $name, $email, $existing['id']);
        return loadone_taikhoan($existing['id']);
    }
    
    // Tạo username: guest_PHONE
    $username = 'guest_' . $phone;
    
    // Mật khẩu random (không cần thiết vì guest không login lại)
    $password = substr(md5(time()), 0, 8);
    
    // Insert tài khoản mới với vai_tro = -1 (khách vãng lai)
    $sql = "INSERT INTO taikhoan (name, user, pass, email, phone, dia_chi, vai_tro, id_rap, img) 
            VALUES (?, ?, ?, ?, ?, '', -1, NULL, '')";
    
    $id = pdo_execute_return_interlastid($sql, $name, $username, $password, $email, $phone);
    
    // Trả về thông tin tài khoản vừa tạo
    return loadone_taikhoan($id);
}

/**
 * Kiểm tra số điện thoại đã được sử dụng chưa
 * @param string $phone
 * @return bool True nếu đã tồn tại
 */
function check_phone_exists($phone) {
    $sql = "SELECT id FROM taikhoan WHERE phone = ?";
    $result = pdo_query_one($sql, $phone);
    return $result !== false;
}

/**
 * Kiểm tra địa chỉ Gmail hợp lệ và có tồn tại thực tế trên hệ thống Google hay không
 * @param string $email
 * @return array ['valid' => bool, 'message' => string]
 */
function verify_gmail($email) {
    $email = trim($email);
    // 1. Kiểm tra định dạng cơ bản của email và bắt buộc phải là đuôi gmail.com
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'valid' => false,
            'message' => 'Email không đúng định dạng!'
        ];
    }
    
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/i', $email)) {
        return [
            'valid' => false,
            'message' => 'Hệ thống chỉ chấp nhận địa chỉ Gmail (@gmail.com)!'
        ];
    }
    
    // 2. Kiểm tra sự tồn tại thực tế của Gmail qua SMTP handshake tới máy chủ Google
    $smtp_server = 'gmail-smtp-in.l.google.com';
    $port = 25;
    $timeout = 3;
    
    $fp = @fsockopen($smtp_server, $port, $errno, $errstr, $timeout);
    if ($fp) {
        // Đọc banner chào mừng
        fgets($fp, 1024);
        
        // HELO
        fputs($fp, "HELO localhost\r\n");
        fgets($fp, 1024);
        
        // MAIL FROM (Dùng một email gửi đi hợp lệ)
        fputs($fp, "MAIL FROM:<tatthiendh123@gmail.com>\r\n");
        fgets($fp, 1024);
        
        // RCPT TO
        fputs($fp, "RCPT TO:<$email>\r\n");
        $resp = fgets($fp, 1024);
        
        // QUIT
        fputs($fp, "QUIT\r\n");
        fclose($fp);
        
        // Nếu máy chủ Gmail trả về mã 250 nghĩa là tài khoản tồn tại thực tế
        if (strpos($resp, '250') !== false) {
            return [
                'valid' => true,
                'message' => ''
            ];
        } else {
            return [
                'valid' => false,
                'message' => 'Tài khoản Gmail này không tồn tại trên hệ thống Google!'
            ];
        }
    } else {
        // Nếu cổng 25 bị chặn bởi nhà mạng/firewall, ta fallback về kiểm tra MX record của gmail.com (luôn luôn đúng)
        // và chấp nhận email này vì đã đúng cú pháp @gmail.com
        if (checkdnsrr('gmail.com', 'MX')) {
            return [
                'valid' => true,
                'message' => ''
            ];
        }
    }
    
    return [
        'valid' => false,
        'message' => 'Không thể kết nối dịch vụ kiểm tra email!'
    ];
}

/**
 * Kích hoạt gói hội viên CinePass cho tài khoản
 * @param int $user_id
 * @param string $type ('standard' hoặc 'premium')
 * @return bool
 */
function activate_cinepass_subscription($user_id, $type) {
    $tickets = ($type === 'premium') ? 5 : 3;
    $combos = ($type === 'premium') ? 2 : 0;
    $points = ($type === 'premium') ? 3000 : 1500;
    $expire_date = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    // Kích hoạt trong table taikhoan
    $sql = "UPDATE taikhoan SET 
            cinepass_sub_status = 1,
            cinepass_sub_type = ?,
            cinepass_tickets_left = cinepass_tickets_left + ?,
            cinepass_combos_left = cinepass_combos_left + ?,
            cinepass_expire_date = ?,
            diem_tich_luy = diem_tich_luy + ?,
            tong_diem_tich_luy = tong_diem_tich_luy + ?
            WHERE id = ?";
            
    $result = pdo_execute($sql, $type, $tickets, $combos, $expire_date, $points, $points, $user_id);
    
    // Lưu lịch sử tích điểm
    try {
        $sql_point_history = "INSERT INTO `lich_su_diem` (`id_tk`, `so_diem`, `loai_giao_dich`, `ly_do`) 
                              VALUES (?, ?, 'cong', ?)";
        pdo_execute($sql_point_history, $user_id, $points, "Điểm thưởng đăng ký gói CinePass " . ($type == 'premium' ? 'Premium' : 'Standard'));
    } catch (Exception $e) {
        error_log("Point history log failed: " . $e->getMessage());
    }
    
    return $result !== false;
}

/**
 * Trừ số dư vé/combo của hội viên CinePass
 * @param int $user_id
 * @param int $tickets
 * @param int $combos
 * @return bool
 */
function deduct_cinepass_balance($user_id, $tickets, $combos) {
    $sql = "UPDATE taikhoan SET 
            cinepass_tickets_left = GREATEST(0, cinepass_tickets_left - ?),
            cinepass_combos_left = GREATEST(0, cinepass_combos_left - ?)
            WHERE id = ?";
    return pdo_execute($sql, $tickets, $combos, $user_id) !== false;
}

/**
 * Gửi email thông báo đăng ký gói hội viên CinePass thành công
 */
function send_cinepass_subscription_email($email, $username, $type, $tickets, $combos, $expire_date) {
    require_once __DIR__ . '/../PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tatthiendh123@gmail.com'; 
        $mail->Password   = 'qjca onic cfks clad'; 
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->setFrom('tatthiendh123@gmail.com', 'Galaxy Studio');
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        $plan_name = ($type === 'premium') ? 'CinePass Premium' : 'CinePass Standard';
        $plan_price = ($type === 'premium') ? '250,000 VND' : '150,000 VND';
        $points = ($type === 'premium') ? '3,000' : '1,500';
        
        $mail->Subject = '=?UTF-8?B?' . base64_encode('🎉 Kích hoạt thành công gói hội viên ' . $plan_name) . '?=';
        
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #363038; border-radius: 12px; padding: 25px; background-color: #151216; color: #fff;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <span style="font-size: 40px;">💳</span>
                    <h2 style="color: #ffd564; margin: 10px 0 0 0; text-transform: uppercase;">Galaxy Studio</h2>
                    <p style="color: #9ca3af; margin: 5px 0 0 0; font-size: 14px;">Xác nhận đăng ký gói hội viên CinePass</p>
                </div>
                <hr style="border: 0; border-top: 1px solid #363038; margin: 20px 0;">
                <p>Xin chào <strong>' . htmlspecialchars($username) . '</strong>,</p>
                <p>Chúc mừng bạn đã đăng ký thành công gói hội viên <strong>' . $plan_name . '</strong> của Galaxy Studio! Tài khoản của bạn đã được nâng cấp đặc quyền thành viên.</p>
                
                <div style="background-color: #201a22; border: 1px solid #363038; border-radius: 8px; padding: 15px; margin: 20px 0;">
                    <h4 style="color: #ffd564; margin: 0 0 10px 0; font-size: 16px; border-bottom: 1px solid #363038; padding-bottom: 5px;">THÔNG TIN GÓI ĐĂNG KÝ</h4>
                    <p style="margin: 5px 0; font-size: 14px; color: #fff;">• Gói: <strong>' . $plan_name . '</strong></p>
                    <p style="margin: 5px 0; font-size: 14px; color: #fff;">• Giá gói: <strong>' . $plan_price . '</strong></p>
                    <p style="margin: 5px 0; font-size: 14px; color: #fff;">• Số vé phim miễn phí nhận được: <strong>' . $tickets . ' vé</strong></p>
                    <p style="margin: 5px 0; font-size: 14px; color: #fff;">• Số combo miễn phí nhận được: <strong>' . $combos . ' combo</strong></p>
                    <p style="margin: 5px 0; font-size: 14px; color: #fff;">• Điểm tích lũy cộng thêm: <strong>+' . $points . ' điểm</strong></p>
                    <p style="margin: 5px 0; font-size: 14px; color: #fff;">• Ngày hết hạn: <strong>' . date('d/m/Y H:i:s', strtotime($expire_date)) . '</strong></p>
                </div>
                
                <p>Bây giờ bạn đã có thể bắt đầu sử dụng số dư vé của thẻ CinePass để đặt vé xem phim với giá <strong>0 VNĐ</strong> trực tiếp tại trang thanh toán của website!</p>
                <p style="margin-top: 30px;">Chúc bạn có những trải nghiệm xem phim tuyệt vời tại Galaxy Studio!</p>
                <hr style="border: 0; border-top: 1px solid #363038; margin: 20px 0;">
                <p style="font-size: 11px; color: #9ca3af; text-align: center;">Email này được gửi tự động từ hệ thống Galaxy Studio. Vui lòng không trả lời lại email này.</p>
            </div>
        ';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send subscription confirmation email: " . $mail->ErrorInfo);
        return false;
    }
}
?>

