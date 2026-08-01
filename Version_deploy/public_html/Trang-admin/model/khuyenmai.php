<?php

// Lớp tương thích với schema khuyen_mai trong DB hiện tại
// Bảng cột tham chiếu: id, ten_khuyen_mai, mo_ta, phan_tram_giam, gia_tri_giam, loai_giam (phan_tram|tien_mat),
// ngay_bat_dau, ngay_ket_thuc, dieu_kien_ap_dung, trang_thai, ngay_tao

function km_all($unused = null){
    return pdo_query("SELECT * FROM khuyen_mai ORDER BY id DESC");
}

function km_one($id){ return pdo_query_one("SELECT * FROM khuyen_mai WHERE id=?", $id); }

function km_insert($ten, $ma_code, $loai_giam, $phan_tram_giam, $gia_tri_giam, $bat_dau, $ket_thuc, $trang_thai, $dieu_kien, $mo_ta, $id_rap = null){
    $ten = trim($ten);
    $code = strtoupper(trim($ma_code));

    if (empty($ten)) {
        throw new Exception("Tên khuyến mãi không được để trống.");
    }
    if (strlen($code) < 3 || strlen($code) > 20) {
        throw new Exception("Mã khuyến mãi phải từ 3 đến 20 ký tự.");
    }
    if (!preg_match('/^[A-Z0-9_-]+$/', $code)) {
        throw new Exception("Mã khuyến mãi chỉ được chứa chữ cái, chữ số, dấu gạch ngang và gạch dưới.");
    }
    if (empty($bat_dau) || empty($ket_thuc)) {
        throw new Exception("Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc.");
    }
    if ($ket_thuc < $bat_dau) {
        throw new Exception("Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.");
    }

    // Kiểm tra trùng mã
    $exists = pdo_query_one("SELECT id FROM khuyen_mai WHERE ma_khuyen_mai = ?", $code);
    if ($exists) {
        throw new Exception("Mã khuyến mãi '{$code}' đã tồn tại trong hệ thống.");
    }

    // Chuẩn hóa loại giảm và giá trị giảm
    if ($loai_giam === 'phan_tram') {
        $phan_tram_giam = (int)$phan_tram_giam;
        if ($phan_tram_giam < 1 || $phan_tram_giam > 100) {
            throw new Exception("Phần trăm giảm phải nằm trong khoảng từ 1% đến 100%.");
        }
        $gia_tri_giam = 0;
    } else {
        $gia_tri_giam = (float)$gia_tri_giam;
        if ($gia_tri_giam <= 0) {
            throw new Exception("Giá trị giảm bằng tiền mặt phải lớn hơn 0.");
        }
        $phan_tram_giam = 0;
    }

    pdo_execute("INSERT INTO khuyen_mai(ten_khuyen_mai, ma_khuyen_mai, loai_giam, phan_tram_giam, gia_tri_giam, ngay_bat_dau, ngay_ket_thuc, trang_thai, dieu_kien_ap_dung, mo_ta, id_rap)
                 VALUES(?,?,?,?,?,?,?,?,?,?,?)",
        $ten, $code, $loai_giam, $phan_tram_giam, $gia_tri_giam, $bat_dau, $ket_thuc, $trang_thai, $dieu_kien, $mo_ta, $id_rap);
}

function km_update($id, $ten, $ma_code, $loai_giam, $phan_tram_giam, $gia_tri_giam, $bat_dau, $ket_thuc, $trang_thai, $dieu_kien, $mo_ta, $id_rap = null){
    $ten = trim($ten);
    $code = strtoupper(trim($ma_code));

    if (empty($ten)) {
        throw new Exception("Tên khuyến mãi không được để trống.");
    }
    if (strlen($code) < 3 || strlen($code) > 20) {
        throw new Exception("Mã khuyến mãi phải từ 3 đến 20 ký tự.");
    }
    if (!preg_match('/^[A-Z0-9_-]+$/', $code)) {
        throw new Exception("Mã khuyến mãi chỉ được chứa chữ cái, chữ số, dấu gạch ngang và gạch dưới.");
    }
    if (empty($bat_dau) || empty($ket_thuc)) {
        throw new Exception("Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc.");
    }
    if ($ket_thuc < $bat_dau) {
        throw new Exception("Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.");
    }

    // Kiểm tra trùng mã với dòng khác
    $exists = pdo_query_one("SELECT id FROM khuyen_mai WHERE ma_khuyen_mai = ? AND id != ?", $code, $id);
    if ($exists) {
        throw new Exception("Mã khuyến mãi '{$code}' đã được sử dụng ở một khuyến mãi khác.");
    }

    // Chuẩn hóa loại giảm và giá trị giảm
    if ($loai_giam === 'phan_tram') {
        $phan_tram_giam = (int)$phan_tram_giam;
        if ($phan_tram_giam < 1 || $phan_tram_giam > 100) {
            throw new Exception("Phần trăm giảm phải nằm trong khoảng từ 1% đến 100%.");
        }
        $gia_tri_giam = 0;
    } else {
        $gia_tri_giam = (float)$gia_tri_giam;
        if ($gia_tri_giam <= 0) {
            throw new Exception("Giá trị giảm bằng tiền mặt phải lớn hơn 0.");
        }
        $phan_tram_giam = 0;
    }

    pdo_execute("UPDATE khuyen_mai SET ten_khuyen_mai=?, ma_khuyen_mai=?, loai_giam=?, phan_tram_giam=?, gia_tri_giam=?, ngay_bat_dau=?, ngay_ket_thuc=?, trang_thai=?, dieu_kien_ap_dung=?, mo_ta=?, id_rap=? WHERE id=?",
        $ten, $code, $loai_giam, $phan_tram_giam, $gia_tri_giam, $bat_dau, $ket_thuc, $trang_thai, $dieu_kien, $mo_ta, $id_rap, $id);
}

function km_delete($id){ pdo_execute("DELETE FROM khuyen_mai WHERE id=?", $id); }

function km_toggle($id){ $r = km_one($id); if(!$r) return; $new = (int)($r['trang_thai']??1)?0:1; pdo_execute("UPDATE khuyen_mai SET trang_thai=? WHERE id=?", $new, $id); }

// Tìm mã khuyến mãi theo mã code (ma_khuyen_mai)
function km_find_by_code($code){
    $sql = "SELECT * FROM khuyen_mai 
            WHERE ma_khuyen_mai = ? 
            AND trang_thai = 1 
            AND ngay_bat_dau <= NOW() 
            AND ngay_ket_thuc >= NOW() 
            LIMIT 1";
    return pdo_query_one($sql, trim(strtoupper($code)));
}

// Tính toán giảm giá từ mã khuyến mãi
function km_calculate_discount($km_row, $original_price){
    if (!$km_row) return 0;
    
    $loai_giam = $km_row['loai_giam'] ?? 'phan_tram';
    
    if ($loai_giam === 'phan_tram') {
        $phan_tram = (int)($km_row['phan_tram_giam'] ?? 0);
        return (int)($original_price * $phan_tram / 100);
    } else {
        // Giảm theo tiền mặt
        return (int)($km_row['gia_tri_giam'] ?? 0);
    }
}

// Lấy danh sách mã khuyến mãi đang hoạt động (cho nhân viên tham khảo)
function km_active_list(){
    $sql = "SELECT id, ten_khuyen_mai, mo_ta, loai_giam, phan_tram_giam, gia_tri_giam, ngay_ket_thuc
            FROM khuyen_mai 
            WHERE trang_thai = 1 
            AND ngay_bat_dau <= NOW() 
            AND ngay_ket_thuc >= NOW() 
            ORDER BY ngay_ket_thuc ASC";
    return pdo_query($sql);
}

// Lấy danh sách mã khuyến mãi đang hoạt động theo rạp (cho dropdown)
function km_active_list_by_rap($id_rap){
    $sql = "SELECT id, ten_khuyen_mai, ma_khuyen_mai, mo_ta, loai_giam, phan_tram_giam, gia_tri_giam, ngay_ket_thuc
            FROM khuyen_mai 
            WHERE (id_rap = ? OR id_rap IS NULL)
            AND trang_thai = 1 
            AND ngay_bat_dau <= NOW() 
            AND ngay_ket_thuc >= NOW() 
            ORDER BY ngay_ket_thuc ASC";
    return pdo_query($sql, $id_rap);
}
