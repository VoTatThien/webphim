<?php
function loadall_vephim(){
    $sql="SELECT v.id,phim.tieu_de,v.price, v.ngay_dat,v.ghe,v.combo, taikhoan.name, khung_gio_chieu.thoi_gian_chieu ,v.id_hd,v.trang_thai FROM ve v 
    LEFT JOIN taikhoan ON taikhoan.id = v.id_tk 
    LEFT JOIN khung_gio_chieu ON khung_gio_chieu.id = v.id_thoi_gian_chieu 
     LEFT JOIN phim ON phim.id = v.id_phim       
    LEFT JOIN lichchieu ON lichchieu.id = khung_gio_chieu.id_lich_chieu
    WHERE 1 ORDER BY id DESC;";
    $re=pdo_query($sql);
    return $re;
}
function loadone_vephim($id){
    $sql="SELECT v.id, 
                 phim.tieu_de,
                 lichchieu.ngay_chieu, 
                 v.price, 
                 v.ngay_dat, 
                 v.ghe, 
                 v.combo, 
                 taikhoan.name, 
                 khung_gio_chieu.thoi_gian_chieu, 
                 v.id_hd, 
                 v.trang_thai, 
                 phongchieu.name as tenphong,
                 rap_chieu.ten_rap as tenrap,
                 v.ma_ve,
                 v.check_in_luc,
                 v.check_in_boi
          FROM ve v
          LEFT JOIN taikhoan ON taikhoan.id = v.id_tk
          LEFT JOIN khung_gio_chieu ON khung_gio_chieu.id = v.id_thoi_gian_chieu
          LEFT JOIN phim ON phim.id = v.id_phim
          LEFT JOIN lichchieu ON lichchieu.id = v.id_ngay_chieu
          LEFT JOIN phongchieu ON phongchieu.id = khung_gio_chieu.id_phong
          LEFT JOIN rap_chieu ON rap_chieu.id = v.id_rap
          WHERE v.id = ?";

    $re = pdo_query_one($sql, $id);
    return $re;
}
function update_vephim($id,$trang_thai){
    // Update trạng thái vé
    $sql = "update ve set `trang_thai`='{$trang_thai}' where `ve`.`id`=" . $id;
    pdo_execute($sql);
}

function loadall_vephim1($searchName, $searchTieuDe,$searchid){
    $sql = "SELECT v.id, phim.tieu_de,lichchieu.ngay_chieu , v.price, v.ngay_dat, v.ghe, v.combo, taikhoan.name, khung_gio_chieu.thoi_gian_chieu, v.id_hd, v.trang_thai ,phongchieu.name as tenphong
            FROM ve v 
            LEFT JOIN taikhoan ON taikhoan.id = v.id_tk 
            LEFT JOIN khung_gio_chieu ON khung_gio_chieu.id = v.id_thoi_gian_chieu 
            LEFT JOIN phim ON phim.id = v.id_phim
            LEFT JOIN lichchieu ON lichchieu.id = khung_gio_chieu.id_lich_chieu
            LEFT JOIN phongchieu ON phongchieu.id = khung_gio_chieu.id_phong
            WHERE taikhoan.name LIKE '%" . $searchName . "%' AND phim.tieu_de LIKE '%" . $searchTieuDe . "%' and v.id like '%" . $searchid . "%'
            ORDER BY v.id DESC";

    $re = pdo_query($sql);
    return $re;
}

function loadall_vephim1_by_rap($searchName, $searchTieuDe, $searchid, $id_rap){
    $sql = "SELECT v.id, phim.tieu_de, lichchieu.ngay_chieu, v.price, v.ngay_dat, v.ghe, v.combo, taikhoan.name, khung_gio_chieu.thoi_gian_chieu, v.id_hd, v.trang_thai, phongchieu.name as tenphong
            FROM ve v 
            LEFT JOIN taikhoan ON taikhoan.id = v.id_tk 
            LEFT JOIN khung_gio_chieu ON khung_gio_chieu.id = v.id_thoi_gian_chieu 
            LEFT JOIN phim ON phim.id = v.id_phim
            LEFT JOIN lichchieu ON lichchieu.id = khung_gio_chieu.id_lich_chieu
            LEFT JOIN phongchieu ON phongchieu.id = khung_gio_chieu.id_phong
            WHERE taikhoan.name LIKE ? AND phim.tieu_de LIKE ? AND v.id LIKE ? AND lichchieu.id_rap = ?
            ORDER BY v.id DESC";
    return pdo_query($sql, "%$searchName%", "%$searchTieuDe%", "%$searchid%", $id_rap);
}


function capnhat_tt_ve(){
    $sql = "UPDATE `ve`
INNER JOIN `lichchieu` ON `ve`.`id_ngay_chieu` = `lichchieu`.`id`
SET `ve`.`trang_thai` = 4
WHERE `lichchieu`.`ngay_chieu` < NOW() AND `ve`.`trang_thai` = 1;
";
    pdo_execute($sql);
}

function ve_find_by_code($ma_ve){
    $sql = "SELECT v.*, phim.tieu_de, khung_gio_chieu.thoi_gian_chieu, lichchieu.ngay_chieu, phongchieu.name as tenphong
            FROM ve v
            JOIN phim ON phim.id = v.id_phim
            JOIN khung_gio_chieu ON khung_gio_chieu.id = v.id_thoi_gian_chieu
            JOIN lichchieu ON lichchieu.id = v.id_ngay_chieu
            JOIN phongchieu ON phongchieu.id = khung_gio_chieu.id_phong
            WHERE v.ma_ve = ?";
    $row = pdo_query_one($sql, $ma_ve);
    if (!$row && ctype_digit($ma_ve)) {
        // fallback: cho phép nhập ID vé trực tiếp
        $sql2 = "SELECT v.*, phim.tieu_de, khung_gio_chieu.thoi_gian_chieu, lichchieu.ngay_chieu, phongchieu.name as tenphong
                 FROM ve v
                 JOIN phim ON phim.id = v.id_phim
                 JOIN khung_gio_chieu ON khung_gio_chieu.id = v.id_thoi_gian_chieu
                 JOIN lichchieu ON lichchieu.id = v.id_ngay_chieu
                 JOIN phongchieu ON phongchieu.id = khung_gio_chieu.id_phong
                 WHERE v.id = ?";
        $row = pdo_query_one($sql2, (int)$ma_ve);
    }
    return $row;
}

function ve_checkin($id_ve, $id_nv){
    $sql = "UPDATE ve SET check_in_luc = NOW(), check_in_boi = ? WHERE id = ?";
    pdo_execute($sql, $id_nv, $id_ve);
}

function ve_create_admin($id_phim, $id_rap, $id_tg, $id_lc, $id_kh, $ghe_csv, $price, $id_nv, $combo_text = '', $payment_method = 'cash'){
    $ma = substr(md5(uniqid((string)$id_kh, true)), 0, 12);
    
    // Nếu payment_method là sepay, trang_thai = 0 (chờ thanh toán)
    // Nếu payment_method là cash, trang_thai = 1 (đã thanh toán)
    $trang_thai = ($payment_method === 'sepay') ? 0 : 1;
    
    $sql = "INSERT INTO ve(id_phim,id_rap,id_thoi_gian_chieu,id_ngay_chieu,id_tk,ghe,combo,price,id_hd,trang_thai,ngay_dat,ma_ve,tao_boi)
            VALUES(?,?,?,?,?,?,?,?,0,?,NOW(),?,?)";
    
    // Sử dụng kết nối trực tiếp để lấy lastInsertId
    try {
        $conn = pdo_get_connection();
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id_phim, $id_rap, $id_tg, $id_lc, $id_kh, $ghe_csv, $combo_text, $price, $trang_thai, $ma, $id_nv]);
        $ve_id = $conn->lastInsertId();
        return (int)$ve_id;
    } catch(PDOException $e) {
        error_log("ve_create_admin error: " . $e->getMessage());
        throw $e;
    } finally {
        unset($conn);
    }
}

function ve_reserved_seats($id_tg, $id_lc){
    $sql = "SELECT ghe FROM ve WHERE id_thoi_gian_chieu = ? AND id_ngay_chieu = ?";
    $rows = pdo_query($sql, $id_tg, $id_lc);
    $seats = [];
    foreach ($rows as $r) {
        $g = trim($r['ghe'] ?? '');
        if ($g !== '') {
            foreach (explode(',', $g) as $s) { $seats[] = trim($s); }
        }
    }
    return array_values(array_unique($seats));
}

// Thống kê doanh thu theo NGÀY cho 1 nhân viên (theo trường tao_boi)
function ve_stats_by_staff_date_range($id_nv, $id_rap, $from_date, $to_date){
    $sql = "SELECT DATE(ngay_dat) AS ngay, COUNT(*) AS so_ve, COALESCE(SUM(price),0) AS doanh_thu
            FROM ve
            WHERE tao_boi = ? AND id_rap = ? AND trang_thai IN (1,2,4)
              AND DATE(ngay_dat) BETWEEN ? AND ?
            GROUP BY DATE(ngay_dat)
            ORDER BY ngay";
    return pdo_query($sql, $id_nv, $id_rap, $from_date, $to_date);
}

// Tổng hợp doanh thu cho 1 nhân viên trong khoảng ngày
function ve_sum_by_staff($id_nv, $id_rap, $from_date, $to_date){
    $row = pdo_query_one(
        "SELECT COUNT(*) AS so_ve, COALESCE(SUM(price),0) AS doanh_thu
         FROM ve WHERE tao_boi = ? AND id_rap = ? AND trang_thai IN (1,2,4)
           AND DATE(ngay_dat) BETWEEN ? AND ?",
        $id_nv, $id_rap, $from_date, $to_date
    );
    return $row ?: ['so_ve'=>0,'doanh_thu'=>0];
}

if (!function_exists('gui_mail_ve')) {
    function gui_mail_ve($load_ve_tt, $recipient_email = null, $recipient_name = null) {
        require_once dirname(dirname(__DIR__)) . '/Trang-nguoi-dung/PHPMailer/src/Exception.php';
        require_once dirname(dirname(__DIR__)) . '/Trang-nguoi-dung/PHPMailer/src/PHPMailer.php';
        require_once dirname(dirname(__DIR__)) . '/Trang-nguoi-dung/PHPMailer/src/SMTP.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Determine recipient details
            $email = $recipient_email;
            if (empty($email)) {
                $email = $_SESSION['user']['email'] ?? '';
            }
            
            $name = $recipient_name;
            if (empty($name)) {
                $name = $_SESSION['user']['name'] ?? 'Khách hàng';
            }

            // Verify email exists
            if (empty($email)) {
                error_log("❌ ERROR: Email khách hàng không tồn tại");
                return false;
            }

            // Server settings
            $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'thanhbang0162@gmail.com';
            $mail->Password   = 'cooh jnvf szck cwux';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Email sender/recipient
            $mail->setFrom('thanhbang0162@gmail.com', 'Galaxy Studio');
            $mail->addAddress($email);

            // Determine base path dynamically
            $base_path = '';
            if (preg_match('/^\/([^\/]+)\/(Trang-nguoi-dung|Trang-admin|Version_deploy)/', $_SERVER['REQUEST_URI'], $matches)) {
                $base_path = '/' . $matches[1];
            }

            // Generate QR code URL
            $qr_data = urlencode("http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $base_path . "/Trang-nguoi-dung/index.php?act=quetve&id=" . $load_ve_tt['id']);
            $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . $qr_data;

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Thank you for booking movie tickets';
            
            $price_val = isset($load_ve_tt['thanh_tien']) ? $load_ve_tt['thanh_tien'] : ($load_ve_tt['price'] ?? 0);
            $ngay_tt_val = isset($load_ve_tt['ngay_tt']) ? $load_ve_tt['ngay_tt'] : ($load_ve_tt['ngay_dat'] ?? date('Y-m-d H:i:s'));
            $ten_rap_val = !empty($load_ve_tt['ten_rap']) ? $load_ve_tt['ten_rap'] : (!empty($load_ve_tt['tenrap']) ? $load_ve_tt['tenrap'] : 'Galaxy Studio');

            $mail->Body    = 'Xác nhận Đặt Vé Xem Phim Thành Công <br><hr>
                                 Chào '.$name.',<br><br>
                                 Chúng tôi xin chân thành cảm ơn bạn đã chọn Galaxy Studio để trải nghiệm bộ phim tuyệt vời. Chúc mừng! Đơn đặt vé của bạn đã được xác nhận thành công. 
                                 Dưới đây là thông tin chi tiết về đơn đặt vé của bạn:<br>
                                 - Mã đặt vé: ' . $load_ve_tt['id'] . ' <br>
                                 - Tên phim: ' . $load_ve_tt['tieu_de'] . '<br>
                                 - Rạp : ' . $ten_rap_val . ' <br>
                                 - Phòng: ' . $load_ve_tt['tenphong'] . '<br>
                                 - Xuất chiếu: ' . $load_ve_tt['thoi_gian_chieu'] . ' --- ' . $load_ve_tt['ngay_chieu'] . '<br>
                                 - Ghế ngồi: ' . $load_ve_tt['ghe'] . '<br>
                                 - Combo: ' . $load_ve_tt['combo'] . '<br>
                                 - Ngày thanh toán: ' . $ngay_tt_val . '<br>
                                 - Thành tiền: ' . number_format($price_val) . ' VND<br>
                                 <hr>
                                 <strong>Mã QR của vé:</strong><br>
                                 <img src="' . $qr_code_url . '" alt="QR Code" style="width: 200px; height: 200px; border: 1px solid #ddd; padding: 5px;"><br>
                                 <em>Vui lòng mang theo mã vé hoặc quét mã QR này tại quầy vé để checkin khi vào phòng chiếu!</em><br>
                                 <hr>
                                 Lưu ý quan trọng:<br>
                                 Hãy đảm bảo bạn đến sớm trước thời gian chiếu để có đủ thời gian kiểm tra vé và chọn ghế.<br>
                                 Mã đặt vé trên có thể được sử dụng để kiểm tra thông tin đặt vé tại quầy vé hoặc máy tự động tại rạp.<br>
                                 Nếu bạn có bất kỳ câu hỏi hoặc cần hỗ trợ gì thêm, vui lòng liên hệ với chúng tôi qua số điện thoại 0384104942 hoặc email huyhung@gmail.com.<br>
                                 Chúng tôi rất mong đợi sự xuất hiện của bạn và hy vọng bạn sẽ có một trải nghiệm thú vị tại rạp phim của chúng tôi.<br><br>
                                 Trân trọng,<br>
                                 Galaxy Studio';

            $mail->send();
            
            // Log success
            error_log("✅ Email sent successfully to {$email}");
            
            return true;
        } catch (Exception $e) {
            error_log("❌ Mail Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
