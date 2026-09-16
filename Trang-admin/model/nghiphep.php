<?php
function np_list_by_rap($id_rap){
    $sql = "SELECT dnp.*, tk.name as ten_nv
            FROM don_nghi_phep dnp
            JOIN taikhoan tk ON tk.id = dnp.id_nhan_vien
            WHERE dnp.id_rap = ?
            ORDER BY dnp.ngay_tao DESC";
    return pdo_query($sql, $id_rap);
}

function np_list_by_user($id_user){
    $sql = "SELECT dnp.*, rc.ten_rap
            FROM don_nghi_phep dnp
            JOIN rap_chieu rc ON rc.id = dnp.id_rap
            WHERE dnp.id_nhan_vien = ?
            ORDER BY dnp.ngay_tao DESC";
    return pdo_query($sql, $id_user);
}

/**
 * Gửi đơn xin nghỉ phép với đầy đủ kiểm tra ràng buộc:
 * - tu_ngay không được là ngày đã qua (phải >= hôm nay)
 * - den_ngay phải >= tu_ngay
 * - Số ngày nghỉ không quá 30 ngày/lần
 * - Lý do phải từ 10 ký tự trở lên
 * - Không trùng với đơn đang chờ duyệt trong khoảng thời gian đó
 */
function np_insert($id_nv, $id_rap, $tu_ngay, $den_ngay, $ly_do){
    $today = date('Y-m-d');

    // 1. Ngày bắt đầu không được là ngày đã qua
    if ($tu_ngay < $today) {
        throw new Exception("Ngày bắt đầu nghỉ phép không được là ngày đã qua (phải từ hôm nay trở đi).");
    }

    // 2. Ngày kết thúc phải >= ngày bắt đầu
    if ($den_ngay < $tu_ngay) {
        throw new Exception("Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.");
    }

    // 3. Số ngày nghỉ không quá 30 ngày/lần
    $so_ngay = (strtotime($den_ngay) - strtotime($tu_ngay)) / 86400 + 1;
    if ($so_ngay > 30) {
        throw new Exception("Số ngày nghỉ không được vượt quá 30 ngày mỗi lần xin (hiện tại: {$so_ngay} ngày).");
    }

    // 4. Lý do phải đủ ít nhất 10 ký tự
    if (mb_strlen(trim($ly_do), 'UTF-8') < 10) {
        throw new Exception("Lý do nghỉ phép phải có ít nhất 10 ký tự.");
    }

    // 5. Kiểm tra trùng đơn đang chờ duyệt trong khoảng thời gian đó
    $trung = pdo_query_one(
        "SELECT id FROM don_nghi_phep 
         WHERE id_nhan_vien = ? 
         AND trang_thai = 'Chờ duyệt'
         AND tu_ngay <= ? AND den_ngay >= ?
         LIMIT 1",
        $id_nv, $den_ngay, $tu_ngay
    );
    if ($trung) {
        throw new Exception("Bạn đã có đơn nghỉ phép đang chờ duyệt trong khoảng thời gian này. Vui lòng chờ xử lý.");
    }

    $sql = "INSERT INTO don_nghi_phep(id_nhan_vien, id_rap, tu_ngay, den_ngay, ly_do) VALUES(?,?,?,?,?)";
    pdo_execute($sql, $id_nv, $id_rap, $tu_ngay, $den_ngay, $ly_do);
}


function np_update_trang_thai($id, $trang_thai){
    $sql = "UPDATE don_nghi_phep SET trang_thai = ? WHERE id = ?";
    pdo_execute($sql, $trang_thai, $id);
}

