<?php
/**
 * AI Chatbot Endpoint (Gemini API & Smart Fallback)
 * File: Trang-nguoi-dung/api_chatbot.php
 */

header('Content-Type: application/json; charset=utf-8');

// Nhận dữ liệu POST dạng JSON
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($input['message']) ? trim($input['message']) : '';
$history = isset($input['history']) && is_array($input['history']) ? $input['history'] : [];

if (empty($userMessage)) {
    echo json_encode(['success' => false, 'message' => 'Nội dung tin nhắn không được để trống.']);
    exit;
}

/**
 * Hàm trả về câu trả lời tự động thông minh bằng từ khóa dựa trên dữ liệu DB thực tế
 */
function get_smart_fallback_reply($userMessage, $phims = [], $raps = [], $suats = [], $promos = [], $combos = [], $news = [], $tenWeb = 'Galaxy Studio', $sdtWeb = '1900 1234', $diaChiWeb = '', $emailWeb = 'support@galaxystudio.vn') {
    $reply = "";
    $msgLower = mb_strtolower($userMessage, 'UTF-8');
    
    // 1. Hỏi về Suất chiếu / Lịch chiếu (Đưa lên đầu và xử lý thông minh theo ngày + phim)
    if (strpos($msgLower, 'lịch') !== false || strpos($msgLower, 'lich') !== false || strpos($msgLower, 'suất') !== false || strpos($msgLower, 'suat') !== false || strpos($msgLower, 'xuất') !== false || strpos($msgLower, 'xuat') !== false || strpos($msgLower, 'mấy giờ') !== false || strpos($msgLower, 'may gio') !== false) {
        
        // Xác định bộ lọc ngày
        $dateFilter = null;
        $dateLabel = "hôm nay và sắp tới";
        if (strpos($msgLower, 'ngày mai') !== false || strpos($msgLower, 'ngay mai') !== false) {
            $dateFilter = date('Y-m-d', strtotime('+1 day'));
            $dateLabel = "ngày mai (" . date('d/m', strtotime('+1 day')) . ")";
        } elseif (strpos($msgLower, 'hôm nay') !== false || strpos($msgLower, 'hom nay') !== false) {
            $dateFilter = date('Y-m-d');
            $dateLabel = "hôm nay (" . date('d/m') . ")";
        }
        
        $filteredSuats = [];
        $hasSpecificPhimQuery = false;
        
        foreach ($suats as $s) {
            $phimNameLower = mb_strtolower($s['phim_name'], 'UTF-8');
            $matchesPhim = (strpos($msgLower, $phimNameLower) !== false);
            if ($matchesPhim) {
                $hasSpecificPhimQuery = true;
            }
            
            if ($dateFilter !== null) {
                // Lọc theo ngày
                if ($s['ngay_chieu'] === $dateFilter) {
                    if ($matchesPhim) {
                        $filteredSuats[] = $s;
                    }
                }
            } else {
                // Không lọc theo ngày, chỉ lọc theo phim nếu có
                if ($matchesPhim) {
                    $filteredSuats[] = $s;
                }
            }
        }
        
        // Nếu câu hỏi có lọc ngày nhưng không lọc phim cụ thể nào, lấy tất cả phim của ngày đó
        if ($dateFilter !== null && !$hasSpecificPhimQuery) {
            foreach ($suats as $s) {
                if ($s['ngay_chieu'] === $dateFilter) {
                    $filteredSuats[] = $s;
                }
            }
        }
        
        // Quyết định danh sách hiển thị
        $displaySuats = !empty($filteredSuats) ? $filteredSuats : $suats;
        
        $reply .= "📅 **Lịch chiếu phim dành cho {$dateLabel}:**\n";
        if (empty($displaySuats)) {
            $reply .= "- Hiện chưa có lịch chiếu mới cho bộ lọc này. Quý khách vui lòng liên hệ trực tiếp với chúng tôi qua số Hotline: **" . $sdtWeb . "** hoặc gửi Email về **" . $emailWeb . "** để được hỗ trợ kiểm tra trực tiếp nhé!\n";
        } else {
            $limitSuat = array_slice($displaySuats, 0, 10);
            foreach ($limitSuat as $s) {
                $ngayFormat = date('d/m', strtotime($s['ngay_chieu']));
                $reply .= "- Ngày **" . $ngayFormat . "** lúc **" . substr($s['thoi_gian_chieu'], 0, 5) . "**: Phim *" . $s['phim_name'] . "* tại *" . $s['rap_name'] . "*\n";
            }
            
            if ($hasSpecificPhimQuery) {
                $reply .= "\n👉 Để đặt vé phim này, bạn hãy bấm vào nút **Chi tiết** của phim nhé!";
            } else {
                $reply .= "\n👉 Để xem lịch chiếu đầy đủ and đặt vé, bạn hãy chọn phim và bấm nút **Chi tiết** nhé!";
            }
        }
    }
    // 2. Hỏi về Phim ảnh
    elseif (strpos($msgLower, 'phim') !== false || strpos($msgLower, 'movie') !== false || strpos($msgLower, 'chieu') !== false || strpos($msgLower, 'chiếu') !== false) {
        if (empty($phims)) {
            $reply = "🎬 Hiện tại hệ thống chưa cập nhật danh sách phim mới. Quý khách vui lòng liên hệ trực tiếp với chúng tôi qua số Hotline: **" . $sdtWeb . "** hoặc gửi Email về **" . $emailWeb . "** để biết thêm thông tin chi tiết.";
        } else {
            $reply .= "🎬 **Danh sách phim nổi bật trên hệ thống:**\n";
            $showing = [];
            $upcoming = [];
            $today = date('Y-m-d');
            foreach ($phims as $p) {
                if (strtotime($p['date_phat_hanh']) <= strtotime($today)) {
                    $showing[] = "- **" . $p['tieu_de'] . "** (" . $p['thoi_luong_phim'] . " phút)";
                } else {
                    $upcoming[] = "- **" . $p['tieu_de'] . "** (Sắp chiếu)";
                }
            }
            $reply .= "\n🔥 **Đang chiếu:**\n" . implode("\n", array_slice($showing, 0, 5));
            if (!empty($upcoming)) {
                $reply .= "\n\n📅 **Sắp chiếu:**\n" . implode("\n", array_slice($upcoming, 0, 3));
            }
            $reply .= "\n\n👉 Bạn hãy nhấn vào mục **Phim** trên thanh menu đầu trang để xem chi tiết và đặt vé nhé!";
        }
    }
    // 3. Hỏi về Rạp chiếu / Địa chỉ
    elseif (strpos($msgLower, 'rạp') !== false || strpos($msgLower, 'dia chi') !== false || strpos($msgLower, 'địa chỉ') !== false || strpos($msgLower, 'rap') !== false) {
        if (empty($raps)) {
            $reply = "🏢 Hiện tại chưa có thông tin rạp chiếu trên hệ thống. Quý khách vui lòng liên hệ trực tiếp qua số Hotline: **" . $sdtWeb . "** hoặc gửi Email về **" . $emailWeb . "** để nhận hỗ trợ.";
        } else {
            $reply .= "🏢 **Hệ thống rạp chiếu của " . $tenWeb . ":**\n";
            foreach ($raps as $r) {
                $reply .= "- **" . $r['ten_rap'] . "**: " . $r['dia_chi'] . " (Hotline: " . $r['so_dien_thoai'] . ")\n";
            }
            $reply .= "\n📍 Quý khách có thể xem danh sách rạp và lịch chiếu của từng rạp tại mục **Rạp chiếu**.";
        }
    }
    // 4. Hỏi về Khuyến mãi / Mã giảm giá / Voucher
    elseif (strpos($msgLower, 'khuyến mãi') !== false || strpos($msgLower, 'khuyen mai') !== false || strpos($msgLower, 'giảm giá') !== false || strpos($msgLower, 'giam gia') !== false || strpos($msgLower, 'voucher') !== false || strpos($msgLower, 'mã') !== false || strpos($msgLower, 'ma') !== false) {
        if (empty($promos)) {
            $reply = "🎁 Hiện tại chưa có chương trình khuyến mãi nào được cấu hình trên hệ thống. Quý khách vui lòng liên hệ Hotline: **" . $sdtWeb . "** hoặc gửi Email về **" . $emailWeb . "** để biết thêm chi tiết về các ưu đãi sắp tới.";
        } else {
            $reply .= "🎁 **Các chương trình khuyến mãi và Voucher hấp dẫn đang chạy:**\n";
            foreach ($promos as $pr) {
                $giam = $pr['loai_giam'] === 'phan_tram' ? $pr['phan_tram_giam'].'%' : number_format($pr['gia_tri_giam']).'đ';
                $reply .= "- Mã **" . $pr['ma_khuyen_mai'] . "**: " . $pr['ten_khuyen_mai'] . " (Giảm *" . $giam . "*)\n";
            }
            $reply .= "\n👉 Xem chi tiết điều kiện áp dụng tại mục **Khuyến mãi**.";
        }
    }
    // 5. Hỏi về Đồ ăn / Combo / Bắp nước
    elseif (strpos($msgLower, 'combo') !== false || strpos($msgLower, 'bắp') !== false || strpos($msgLower, 'nước') !== false || strpos($msgLower, 'bap') !== false || strpos($msgLower, 'nuoc') !== false || strpos($msgLower, 'ăn') !== false || strpos($msgLower, 'an') !== false) {
        if (empty($combos)) {
            $reply = "🍿 Hiện tại chưa có thông tin bắp nước/combo trực tuyến. Quý khách vui lòng liên hệ Hotline: **" . $sdtWeb . "** hoặc gửi Email về **" . $emailWeb . "** để được hỗ trợ mua bắp nước tại quầy.";
        } else {
            $reply .= "🍿 **Danh sách các combo bắp nước và đồ ăn tại rạp:**\n";
            foreach ($combos as $cb) {
                $giaFormat = number_format($cb['gia']) . 'đ';
                $moTa = empty($cb['mo_ta']) ? '' : " (" . $cb['mo_ta'] . ")";
                $reply .= "- **" . $cb['ten_combo'] . "**: " . $giaFormat . $moTa . "\n";
            }
            $reply .= "\n👉 Quý khách có thể chọn mua bắp nước trực tiếp trong quá trình đặt vé xem phim trực tuyến!";
        }
    }
    // 6. Hỏi về Tin tức / Sự kiện
    elseif (strpos($msgLower, 'tin tức') !== false || strpos($msgLower, 'tin tuc') !== false || strpos($msgLower, 'sự kiện') !== false || strpos($msgLower, 'su kien') !== false) {
        if (empty($news)) {
            $reply = "📰 Hiện tại chưa có tin tức mới được đăng tải. Quý khách vui lòng liên hệ Hotline: **" . $sdtWeb . "** hoặc gửi Email về **" . $emailWeb . "** để biết thêm thông tin sự kiện của rạp.";
        } else {
            $reply .= "📰 **Tin tức mới nhất từ hệ thống:**\n";
            foreach ($news as $n) {
                $ngayFormat = date('d/m/Y', strtotime($n['ngay_dang']));
                $reply .= "- **" . $n['tieu_de'] . "** (" . $ngayFormat . ")\n  _" . $n['tom_tat'] . "_\n";
            }
            $reply .= "\n👉 Xem chi tiết các bài viết tại mục **Tin tức** trên thanh menu.";
        }
    }
    // 7. Hỏi về Giá vé / Tiền vé
    elseif (strpos($msgLower, 'giá vé') !== false || strpos($msgLower, 'gia ve') !== false || strpos($msgLower, 'bao nhiêu tiền') !== false || strpos($msgLower, 'bao nhieu tien') !== false || strpos($msgLower, 'tiền vé') !== false || strpos($msgLower, 'tien ve') !== false) {
        $reply .= "💰 **Bảng giá vé tham khảo tại " . $tenWeb . ":**\n";
        $reply .= "- **Vé 2D Thường (Thứ 2 - Thứ 5):** 75.000 VNĐ / vé.\n";
        $reply .= "- **Vé VIP / Cuối tuần (Thứ 6 - Chủ Nhật):** 90.000 VNĐ - 120.000 VNĐ / vé.\n";
        $reply .= "- **Lưu ý:** Giá vé có thể chênh lệch nhẹ tùy theo loại phòng chiếu (3D, VIP, Ghế đôi Sweetbox) và đối tượng khách hàng (Học sinh - sinh viên được giảm giá khi xuất trình thẻ tại quầy).\n";
        $reply .= "✨ Thành viên có thể tích điểm để đổi mã giảm giá vé!";
    }
    // 8. Hỏi về Liên hệ / Hotline / Hỗ trợ
    elseif (strpos($msgLower, 'liên hệ') !== false || strpos($msgLower, 'lien he') !== false || strpos($msgLower, 'sđt') !== false || strpos($msgLower, 'hotline') !== false || strpos($msgLower, 'sdt') !== false || strpos($msgLower, 'email') !== false) {
        $reply .= "📞 **Thông tin liên hệ bộ phận hỗ trợ khách hàng của " . $tenWeb . ":**\n";
        $reply .= "- **Hotline:** " . $sdtWeb . "\n";
        $reply .= "- **Email:** " . $emailWeb . "\n";
        $reply .= "- **Địa chỉ văn phòng:** " . $diaChiWeb . "\n";
        $reply .= "\n✉️ Hoặc bạn có thể gửi phản hồi trực tiếp cho chúng tôi qua trang **Liên hệ**.";
    }
    // 9. Chào hỏi
    elseif (strpos($msgLower, 'chào') !== false || strpos($msgLower, 'hi') === 0 || strpos($msgLower, 'hello') === 0 || strpos($msgLower, 'xin chào') !== false) {
        $reply = "Xin chào! Tôi là Trợ lý ảo AI của **" . $tenWeb . "**. Rất vui được hỗ trợ bạn!\n\nTôi có thể giúp bạn tìm kiếm:\n- 🎬 Phim đang chiếu & sắp chiếu\n- 🏢 Địa chỉ các rạp và hotline liên hệ\n- 📅 Lịch chiếu suất chiếu\n- 🍿 Thông tin bắp nước, combo đồ ăn\n- 🎁 Các chương trình khuyến mãi hiện có\n- 💰 Thông tin giá vé và đổi điểm thành viên\n\nBạn muốn tìm hiểu thông tin nào?";
    }
    // 10. Các trường hợp khác (Không khớp - Hướng dẫn liên hệ trực tiếp hotline/email của web theo yêu cầu người dùng)
    else {
        $reply = "Xin chào! Hiện tại tôi chưa có thông tin chi tiết về chủ đề này trong hệ thống. Để được hỗ trợ tốt nhất, quý khách vui lòng liên hệ trực tiếp với chúng tôi qua số Hotline: **" . $sdtWeb . "** hoặc gửi Email về **" . $emailWeb . "** nhé! Nhân viên hỗ trợ sẽ giải đáp ngay lập tức cho quý khách.";
    }
    return $reply;
}

try {
    // 1. Load các tệp kết nối DB và cấu hình
    require_once "model/pdo.php";
    require_once "config/chatbot_config.php";

    // 2. Truy vấn dữ liệu thực tế từ DB để làm Context
    // A. Lấy danh sách rạp
    $raps = pdo_query("SELECT id, ten_rap, dia_chi, so_dien_thoai FROM rap_chieu WHERE trang_thai = 1 ORDER BY ten_rap");
    
    // B. Lấy danh sách phim đang/sắp chiếu (Tăng giới hạn lên 100 để lấy toàn bộ danh sách phim bao gồm cả Búp bê)
    $phims = pdo_query("SELECT id, tieu_de, daodien, dienvien, thoi_luong_phim, gia_han_tuoi, date_phat_hanh 
                        FROM phim ORDER BY id DESC LIMIT 100");
    
    // C. Lấy lịch chiếu (ngày chiếu >= hôm nay và trong vòng 7 ngày tới)
    $suats = pdo_query("SELECT lc.ngay_chieu, p.tieu_de as phim_name, r.ten_rap as rap_name, ph.name AS phong_name, kgc.thoi_gian_chieu 
                        FROM khung_gio_chieu kgc 
                        JOIN lichchieu lc ON kgc.id_lich_chieu = lc.id 
                        JOIN phim p ON lc.id_phim = p.id 
                        JOIN rap_chieu r ON lc.id_rap = r.id 
                        JOIN phongchieu ph ON kgc.id_phong = ph.id 
                        WHERE DATE(lc.ngay_chieu) >= CURDATE() 
                          AND DATE(lc.ngay_chieu) <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                          AND lc.trang_thai_duyet = 'Đã duyệt'
                        ORDER BY p.tieu_de ASC, r.ten_rap ASC, lc.ngay_chieu ASC, kgc.thoi_gian_chieu ASC");

    // Group showtimes to represent them compactly in system instructions
    $groupedSuats = [];
    foreach ($suats as $s) {
        $phim = $s['phim_name'];
        $rap = $s['rap_name'];
        $ngay = date('d/m/Y', strtotime($s['ngay_chieu']));
        $gio = substr($s['thoi_gian_chieu'], 0, 5) . " (" . $s['phong_name'] . ")";
        $groupedSuats[$phim][$rap][$ngay][] = $gio;
    }

    // D. Lấy khuyến mãi hoạt động (Tăng giới hạn lên 50)
    $promos = pdo_query("SELECT km.ma_khuyen_mai, km.ten_khuyen_mai, km.mo_ta, km.loai_giam, km.phan_tram_giam, km.gia_tri_giam, km.ngay_ket_thuc, r.ten_rap 
                         FROM khuyen_mai km 
                         LEFT JOIN rap_chieu r ON km.id_rap = r.id 
                         WHERE km.ngay_ket_thuc >= CURDATE() 
                         LIMIT 50");

    // F. Lấy danh sách combo đồ ăn uống
    $combos = pdo_query("SELECT ten_combo, mo_ta, gia, id_rap FROM combo_do_an WHERE trang_thai = 1");

    // G. Lấy danh sách tin tức mới nhất
    $news = pdo_query("SELECT tieu_de, tom_tat, ngay_dang FROM tintuc ORDER BY id DESC LIMIT 5");

    // E. Lấy thông tin cấu hình website (tên rạp, hotline, v.v.)
    $webInfo = pdo_query_one("SELECT * FROM thong_tin_website WHERE id = 1");
    $tenWeb = $webInfo['ten_website'] ?? 'Galaxy Studio';
    $diaChiWeb = $webInfo['dia_chi'] ?? '';
    $sdtWeb = $webInfo['so_dien_thoai'] ?? '1900 1234';
    $emailWeb = $webInfo['email'] ?? 'support@galaxystudio.vn';

    // 3. Xây dựng System Instructions (Context)
    $systemInstruction = "Bạn là Trợ lý ảo AI chính thức của chuỗi rạp phim {$tenWeb} (tên mã dự án: Cinepass). Nhiệm vụ của bạn là tư vấn cho khách hàng về thông tin phim, rạp chiếu, suất chiếu, giá vé, bắp nước combo, tin tức và khuyến mãi.
Hãy trả lời cực kỳ ngắn gọn, lịch sự, chuyên nghiệp và có định dạng dễ đọc (sử dụng in đậm, danh sách gạch đầu dòng Markdown). Trả lời bằng tiếng Việt trừ khi khách hàng hỏi bằng tiếng Anh.

🔴 QUY TẮC BẮT BUỘC: Đối với bất kỳ chủ đề, câu hỏi nào mà bạn không có thông tin trong cơ sở dữ liệu dưới đây hoặc không trả lời được (bao gồm câu hỏi ngoài lề không liên quan đến rạp phim như thời tiết, nấu ăn, lập trình, tán gẫu... hoặc các phim/suất chiếu không có trong danh sách), bạn BẮT BUỘC phải từ chối trả lời và hướng dẫn khách hàng liên hệ trực tiếp thông tin liên hệ của website: Hotline: {$sdtWeb} hoặc Email: {$emailWeb} để được hỗ trợ tốt nhất. Tuyệt đối không trả lời chung chung hoặc bỏ sót thông tin Hotline/Email này.

Dưới đây là thông tin thực tế, cập nhật thời gian thực từ cơ sở dữ liệu của hệ thống. Bạn chỉ được phép trả lời dựa trên thông tin này, KHÔNG tự bịa ra thông tin không tồn tại:

---
THÔNG TIN LIÊN HỆ HỆ THỐNG:
- Hotline: {$sdtWeb}
- Email hỗ trợ: {$emailWeb}
- Văn phòng chính: {$diaChiWeb}

DANH SÁCH RẠP CHIẾU:
";
    foreach ($raps as $r) {
        $systemInstruction .= "- Rạp: {$r['ten_rap']} | Địa chỉ: {$r['dia_chi']} | SĐT: {$r['so_dien_thoai']}\n";
    }

    $systemInstruction .= "\nDANH SÁCH PHIM ĐANG CÓ TRÊN HỆ THỐNG:
";
    foreach ($phims as $p) {
        $today = date('Y-m-d');
        $trangthai = (strtotime($p['date_phat_hanh']) <= strtotime($today)) ? 'Đang chiếu' : 'Sắp chiếu';
        $systemInstruction .= "- Phim: {$p['tieu_de']} | Trạng thái: {$trangthai} | Thời lượng: {$p['thoi_luong_phim']} phút | Đạo diễn: {$p['daodien']} | Diễn viên: {$p['dienvien']} | Độ tuổi: {$p['gia_han_tuoi']}\n";
    }

    $systemInstruction .= "\nDANH SÁCH SUẤT CHIẾU HÔM NAY VÀ 7 NGÀY TỚI:
";
    if (empty($groupedSuats)) {
        $systemInstruction .= "- Hiện tại chưa có lịch chiếu mới được duyệt.\n";
    } else {
        foreach ($groupedSuats as $phim => $rapsList) {
            foreach ($rapsList as $rap => $ngays) {
                $scheduleStr = [];
                foreach ($ngays as $ngay => $gios) {
                    $scheduleStr[] = "Ngày {$ngay}: " . implode(', ', $gios);
                }
                $systemInstruction .= "- Phim: {$phim} | Rạp: {$rap} | " . implode(' ; ', $scheduleStr) . "\n";
            }
        }
    }

    $systemInstruction .= "\nDANH SÁCH CHƯƠNG TRÌNH KHUYẾN MÃI HOẠT ĐỘNG:
";
    if (empty($promos)) {
        $systemInstruction .= "- Hiện tại chưa có chương trình ưu đãi nào đang chạy.\n";
    } else {
        foreach ($promos as $pr) {
            $giam = $pr['loai_giam'] === 'phan_tram' ? number_format($pr['phan_tram_giam']).'%' : number_format($pr['gia_tri_giam']).'đ';
            $rapApDung = empty($pr['ten_rap']) ? 'Toàn hệ thống' : $pr['ten_rap'];
            $ngayEnd = date('d/m/Y', strtotime($pr['ngay_ket_thuc']));
            $systemInstruction .= "- Mã: {$pr['ma_khuyen_mai']} | Tên: {$pr['ten_khuyen_mai']} | Giảm: {$giam} | Rạp áp dụng: {$rapApDung} | Hạn dùng đến: {$ngayEnd} | Mô tả: {$pr['mo_ta']}\n";
        }
    }

    $systemInstruction .= "\nDANH SÁCH COMBO ĐỒ ĂN & NƯỚC UỐNG TẠI RẠP:
";
    if (empty($combos)) {
        $systemInstruction .= "- Hiện tại chưa cấu hình combo đồ ăn nước uống trực tuyến.\n";
    } else {
        foreach ($combos as $cb) {
            $giaFormat = number_format($cb['gia']) . 'đ';
            $moTa = empty($cb['mo_ta']) ? 'Không có mô tả' : $cb['mo_ta'];
            $systemInstruction .= "- Combo: {$cb['ten_combo']} | Giá: {$giaFormat} | Chi tiết: {$moTa}\n";
        }
    }

    $systemInstruction .= "\nDANH SÁCH BÀI VIẾT TIN TỨC MỚI NHẤT:
";
    if (empty($news)) {
        $systemInstruction .= "- Hiện tại chưa có tin tức mới được đăng.\n";
    } else {
        foreach ($news as $n) {
            $ngayFormat = date('d/m/Y', strtotime($n['ngay_dang']));
            $systemInstruction .= "- Tin tức: {$n['tieu_de']} ({$ngayFormat}) | Tóm tắt: {$n['tom_tat']}\n";
        }
    }

    $systemInstruction .= "\nQUY ĐỊNH GIÁ VÉ & ĐIỂM THÀNH VIÊN:
- Giá vé 2D Thường: 75.000 VNĐ. Giá vé VIP/3D/Cuối tuần: 90.000đ - 120.000 VNĐ.
- Quy đổi điểm tích lũy: Khách hàng có thể dùng điểm để giảm giá khi thanh toán vé. Tỷ lệ quy đổi: 100 điểm = 10.000 VNĐ. Quy đổi tối thiểu 1.000 điểm (= 100.000 VNĐ giảm giá).
- Quy định hủy vé: Khách hàng có thể hủy/đổi vé trước giờ chiếu ít nhất 4 tiếng tại mục 'Vé của tôi' và sẽ được hoàn lại điểm tích lũy tương đương.

HƯỚNG DẪN HÀNH ĐỘNG:
- Nếu khách muốn mua/đặt vé: Hướng dẫn họ chọn phim mong muốn, click nút 'Chi tiết' -> chọn suất chiếu hoặc click trực tiếp vào mục 'Phim' / 'Rạp chiếu' trên menu để chọn.
- Nếu không có thông tin suất chiếu hoặc phim khách yêu cầu trong danh sách trên, hoặc bạn không tìm thấy câu trả lời phù hợp trong cơ sở dữ liệu, hãy phản hồi lại và yêu cầu/hướng dẫn khách hàng liên hệ trực tiếp thông tin liên hệ của website: Hotline {$sdtWeb} hoặc Email {$emailWeb} để được hỗ trợ kiểm tra trực tiếp.
- Đối với tất cả các câu hỏi/chủ đề khác mà bạn KHÔNG trả lời được, KHÔNG có thông tin trong cơ sở dữ liệu đã cung cấp ở trên (ví dụ: các chủ đề ngoài lề không liên quan đến phim, rạp chiếu, khuyến mãi, bắp nước, tin tức của hệ thống; hoặc các câu hỏi nằm ngoài phạm vi xử lý tự động như chỉnh sửa vé đã mua, sự cố thanh toán, khiếu nại, tuyển dụng...), bạn BẮT BUỘC phải từ chối trả lời câu hỏi đó và phản hồi lại hướng dẫn khách hàng liên hệ trực tiếp với website qua Hotline: {$sdtWeb} hoặc Email: {$emailWeb} để được nhân viên hỗ trợ kịp thời. Tuyệt đối không tự bịa ra câu trả lời.";

    // 4. Kiểm tra cấu hình API Key để quyết định gọi AI hay dùng Fallback
    $apiKey = defined('GEMINI_API_KEY') ? trim(GEMINI_API_KEY) : '';
    $model = defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-1.5-flash';
    $chatbotEnabled = defined('CHATBOT_ENABLED') ? CHATBOT_ENABLED : true;

    if (!$chatbotEnabled) {
        echo json_encode([
            'success' => true,
            'reply' => 'Trợ lý ảo hiện đang được bảo trì tạm thời. Quý khách vui lòng thử lại sau!'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (empty($apiKey)) {
        $reply = get_smart_fallback_reply($userMessage, $phims, $raps, $suats, $promos, $combos, $news, $tenWeb, $sdtWeb, $diaChiWeb, $emailWeb);
        echo json_encode([
            'success' => true,
            'reply' => $reply
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ==========================================
    // CHẾ ĐỘ CHẠY CHÍNH THỨC QUA GEMINI API
    // ==========================================
    
    // Tạo cấu trúc contents gửi đi bao gồm cả lịch sử trò chuyện
    $contents = [];
    
    // Thêm lịch sử hội thoại
    foreach ($history as $h) {
        $role = $h['role'] === 'user' ? 'user' : 'model';
        $contents[] = [
            'role' => $role,
            'parts' => [
                ['text' => $h['text']]
            ]
        ];
    }
    
    // Thêm tin nhắn hiện tại của người dùng
    $contents[] = [
        'role' => 'user',
        'parts' => [
            ['text' => $userMessage]
        ]
    ];
    
    // Payload gửi tới Gemini API
    $payload = [
        'contents' => $contents,
        'systemInstruction' => [
            'parts' => [
                ['text' => $systemInstruction]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.4,
            'maxOutputTokens' => 800
        ]
    ];

    // Cổng cURL gọi API
    $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $result = json_decode($response, true);
        $replyText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
        
        if (!empty($replyText)) {
            echo json_encode([
                'success' => true,
                'reply' => trim($replyText)
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Ghi log lỗi nếu API gặp sự cố
    file_put_contents(__DIR__ . '/chatbot_debug.log', "[" . date('Y-m-d H:i:s') . "] API Error - HTTP Code: $httpCode | Response: $response\n", FILE_APPEND);

    // Nếu API lỗi, kích hoạt chế độ tự động Fallback làm cứu cánh
    throw new Exception("Gemini API failed or returned empty content. HTTP Code: $httpCode");

} catch (Exception $e) {
    file_put_contents(__DIR__ . '/chatbot_debug.log', "[" . date('Y-m-d H:i:s') . "] Catch Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Gọi fallback thông minh thay vì trả về chuỗi tĩnh generic
    $reply = get_smart_fallback_reply(
        $userMessage, 
        $phims ?? [], 
        $raps ?? [], 
        $suats ?? [], 
        $promos ?? [], 
        $combos ?? [],
        $news ?? [],
        $tenWeb ?? 'Galaxy Studio', 
        $sdtWeb ?? '1900 1234', 
        $diaChiWeb ?? '', 
        $emailWeb ?? 'support@galaxystudio.vn'
    );

    echo json_encode([
        'success' => true,
        'reply' => $reply
    ], JSON_UNESCAPED_UNICODE);
}
?>
