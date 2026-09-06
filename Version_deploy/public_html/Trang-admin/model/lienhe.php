<?php
function loadall_lienhe($trang_thai = null) {
    $sql = "SELECT * FROM `lien_he` WHERE 1";
    if ($trang_thai !== null) {
        $sql .= " AND `trang_thai` = " . (int)$trang_thai;
    }
    $sql .= " ORDER BY `ngay_tao` DESC";
    return pdo_query($sql);
}

function delete_lienhe($id) {
    $sql = "DELETE FROM `lien_he` WHERE `id` = ?";
    pdo_execute($sql, $id);
}

function update_lienhe($id, $tra_loi, $trang_thai = 1) {
    $sql = "UPDATE `lien_he` SET `tra_loi` = ?, `trang_thai` = ? WHERE `id` = ?";
    pdo_execute($sql, $tra_loi, $trang_thai, $id);
}

function loadone_lienhe($id) {
    $sql = "SELECT * FROM `lien_he` WHERE `id` = ?";
    return pdo_query_one($sql, $id);
}

function sendMailLienHeResponse($email, $ten_khach, $tin_nhan, $tra_loi) {
    require_once __DIR__ . '/../../Trang-nguoi-dung/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../../Trang-nguoi-dung/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../../Trang-nguoi-dung/PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'thanhbang0162@gmail.com';
        $mail->Password   = 'qjca onic cfks clad';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('thanhbang0162@gmail.com', 'Galaxy Studio');
        $mail->addAddress($email, $ten_khach);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        $mail->Subject = '=?UTF-8?B?' . base64_encode('Phản hồi yêu cầu liên hệ từ Galaxy Studio') . '?=';
        
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 25px; background-color: #ffffff; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
                <div style="text-align: center; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; margin-bottom: 20px;">
                    <h2 style="color: #4f46e5; margin: 0; font-size: 24px;">Galaxy Studio</h2>
                </div>
                <p style="font-size: 16px; color: #1f2937;">Xin chào <strong>' . htmlspecialchars($ten_khach) . '</strong>,</p>
                <p style="font-size: 15px; color: #4b5563; line-height: 1.6;">
                    Chúng tôi đã nhận được yêu cầu liên hệ của bạn với nội dung:
                </p>
                <div style="background-color: #f3f4f6; border-left: 4px solid #9ca3af; padding: 12px 15px; margin: 15px 0; font-style: italic; color: #4b5563; font-size: 14px; border-radius: 0 4px 4px 0;">
                    ' . nl2br(htmlspecialchars($tin_nhan)) . '
                </div>
                
                <p style="font-size: 15px; color: #1f2937; font-weight: bold; margin-top: 25px;">
                    Phản hồi từ Ban quản trị Galaxy Studio:
                </p>
                <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; margin: 15px 0; color: #1e3a8a; font-size: 15px; line-height: 1.6; border-radius: 0 4px 4px 0;">
                    ' . nl2br(htmlspecialchars($tra_loi)) . '
                </div>
                
                <p style="font-size: 14px; color: #4b5563; margin-top: 30px; line-height: 1.6;">
                    Nếu bạn có thêm bất kỳ câu hỏi nào, vui lòng liên hệ lại với chúng tôi qua website hoặc phản hồi trực tiếp email này.
                </p>
                
                <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 25px 0;">
                <p style="font-size: 12px; color: #9ca3af; text-align: center;">
                    Email này được gửi tự động từ hệ thống Galaxy Studio. Vui lòng không trả lời trực tiếp trừ khi có yêu cầu thêm.
                </p>
            </div>
        ';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
