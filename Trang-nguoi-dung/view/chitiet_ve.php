<!-- Main content -->
<?php
// Load QR config if exists (same as ve.php)
$qr_host = $_SERVER['HTTP_HOST'];
if (file_exists(__DIR__ . '/../config/qr_config.php')) {
    include __DIR__ . '/../config/qr_config.php';
    if (!empty(QR_SERVER_IP)) {
        $qr_host = QR_SERVER_IP;
        if (QR_SERVER_PORT != 80) {
            $qr_host .= ':' . QR_SERVER_PORT;
        }
    } else {
        // Auto-detect IP
        $ip = gethostbyname(gethostname());
        if ($ip !== gethostname() && strpos($ip, '127.') !== 0) {
            $qr_host = $ip;
        }
    }
} else {
    // Fallback function to get server IP address for LAN access
    if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
        $ip = gethostbyname(gethostname());
        if ($ip !== gethostname() && strpos($ip, '127.') !== 0) {
            $qr_host = $ip;
        }
    }
}

$base_path = '';
if (preg_match('/^\/([^\/]+)\/(Trang-nguoi-dung|Trang-admin|Version_deploy)/', $_SERVER['REQUEST_URI'], $matches)) {
    $base_path = '/' . $matches[1];
}
?>

<?php include "view/search.php"; ?>
<form action="index.php?act=huy_ve" method="post">
    <section class="container">
        <div class="order-container">
            <?php if (isset($loadone_ve)) {
                echo "<h2>" . __('CHI TIẾT VÉ') . "</h2>";
                    extract($loadone_ve);
                    switch ($trang_thai) {
                        case 1:
                            $thong_bao = __('Đã thanh toán');
                            $huy_ve_style = '';
                            break;
                        case 2:
                            $thong_bao = __('Đã dùng');
                            $huy_ve_style = 'style="display:none;"';
                            break;
                        case 3:
                            $thong_bao = __('Đã hủy');
                            $huy_ve_style = 'style="display:none;"';
                            break;
                        case 4:
                            $thong_bao = __('Hết hạn');
                            $huy_ve_style = 'style="display:none;"';
                            break;
                        default:
                            $thong_bao = __('Trạng thái không xác định');
                            $huy_ve_style = '';
                    }
                    
                    // Lấy thông tin rạp nếu không có thì dùng default
                    $ten_rap_hienthi = !empty($ten_rap) ? __($ten_rap) : __('Galaxy Studio Gò Vấp');
                    $dia_chi_hienthi = !empty($dia_chi_rap) ? $dia_chi_rap : __('Địa chỉ chưa cập nhật');
                    
                    echo '
                       <div class="ticket">
                        <div class="ticket-position">
                            <div class="ticket__indecator indecator--pre"><div class="indecator-text pre--text">Galaxy Studio</div> </div>
                            <div class="ticket__inner">
                                <div class="ticket-secondary">
                                    <span class="ticket__item">' . __('Mã vé') . ' <strong class="ticket__number">' . $id . '</strong></span>
                                    <span class="ticket__item ticket__date"><i class="fa fa-calendar" style="color:#ffd564; margin-right:5px;"></i> ' . $ngay_chieu . '</span>
                                    <span class="ticket__item ticket__time"><i class="fa fa-clock-o" style="color:#ffd564; margin-right:5px;"></i> ' . $thoi_gian_chieu . '</span>
                                    <span class="ticket__item"><i class="fa fa-film" style="color:#ffd564; margin-right:5px;"></i> ' . __('Rạp chiếu:') . ' <span class="ticket__cinema">' . $ten_rap_hienthi . '</span></span>
                                    <span class="ticket__item"><i class="fa fa-map-marker" style="color:#ffd564; margin-right:5px;"></i> ' . __('Địa chỉ rạp:') . ' <span class="ticket__cinema">' . $dia_chi_hienthi . '</span></span>
                                    <span class="ticket__item"><i class="fa fa-desktop" style="color:#ffd564; margin-right:5px;"></i> ' . __('Phòng chiếu:') . ' <strong class="ticket__number">' . $tenphong . '</strong></span>
                                    <span class="ticket__item"><i class="fa fa-user" style="color:#ffd564; margin-right:5px;"></i> ' . __('Người đặt:') . ' <span class="ticket__cinema">' . $name . '</span></span>
                                    <span class="ticket__item"><i class="fa fa-clock-o" style="color:#ffd564; margin-right:5px;"></i> ' . __('Thời gian đặt:') . ' <span class="ticket__hall">' . $ngay_dat . '</span></span>
                                    <span class="ticket__item ticket__price" style="margin-top: 5px"><i class="fa fa-money" style="color:#ffd564; margin-right:5px;"></i> ' . __('Giá:') . ' <strong class="ticket__cost">' . number_format($price) . ' vnđ</strong></span>
                                </div>

                                <div class="ticket-primery" style="position: relative;">
                                    <div style="position: absolute; top: 86px; right: -1px; width: 107px; height: 107px; background: #fff; border: 2px solid #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode("http://" . $qr_host . $base_path . "/Trang-nguoi-dung/index.php?act=quetve&id=" . $id) . '" alt="QR Code" style="width: 115px; height: 115px; object-fit: contain;" />
                                    </div>
                                    <span class="ticket__item ticket__item--primery ticket__film" style="display:flex;"> <strong class="ticket__movie" >' . __('PHIM:') . ' ' . __($tieu_de) . '</strong></span>
                                    <span class="ticket__item ticket__item--primery"><i class="fa fa-circle-o" style="color:#ffd564; margin-right:5px;"></i> ' . __('Ghế:') . ' <span class="ticket__place">' . $ghe . '</span></span>
                                    <span class="ticket__item ticket__item--primery"><i class="fa fa-coffee" style="color:#ffd564; margin-right:5px;"></i> ' . __('Combo:') . ' <span class="ticket__place">' . $combo . '</span></span>
                                </div>
                            </div>
                            <div class="ticket__indecator indecator--post"><div class="indecator-text post--text">' . $ten_rap_hienthi . '</div></div>
                        </div>
                        <div>
                        <input type="hidden" name="id" value="'.$id.'">

                        <span>' . __('Trạng thái:') . ' '.$thong_bao.'</span>';
                        
                        // Kiểm tra xem có được phép hủy vé không
                        if (isset($ticket_check)) {
                            $can_cancel_button = $ticket_check['can_cancel'];
                            $cancel_message = $ticket_check['message'];
                            $disable_btn = !$can_cancel_button ? 'disabled' : '';
                            $btn_style = !$can_cancel_button ? 'style="margin-top:10px; opacity: 0.5; cursor: not-allowed;"' : 'style="margin-top:10px;"';
                            
                            echo '<br><span style="color: ' . ($can_cancel_button ? '#10b981' : '#ef4444') . '; font-size: 12px; margin-top: 5px; display: block;">💬 ' . __($cancel_message) . '</span>';
                            echo '<button type="submit" name="capnhat" class="btn btn-danger" ' . $btn_style . ' ' . $disable_btn . '>' . __('Hủy vé') . '</button>';
                        } else {
                            echo '<button type="submit" name="capnhat" class="btn btn-danger" '.$huy_ve_style.' style="margin-top:10px;">' . __('Hủy vé') . '</button>';
                        }
                        
                        echo '                        </div>
                    </div>';
                    
                    if (!empty($combo)) {
                        $fb_status = !empty($fb_check_in_luc) ? '<span style="color: #43e97b;"><i class="fa fa-check-circle"></i> ' . __("Đã nhận vào:") . ' ' . $fb_check_in_luc . '</span>' : '<span style="color: #ffd564; font-weight: bold;"><i class="fa fa-spinner"></i> ' . __("Chờ nhận đồ ăn (Fast Track)") . '</span>';
                        $fb_qr_url = "http://" . $qr_host . $base_path . "/Trang-nguoi-dung/index.php?act=quetve&id=" . $id . "&fb=1";
                        echo '
                        <div class="fb-ticket-card" style="margin-top: 25px; background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; color: #fff; text-align: left;">
                            <div style="flex: 1; min-width: 200px;">
                                <h4 style="margin: 0 0 10px 0; color: #ffd564; font-size: 16px; font-weight: bold; text-transform: uppercase;">
                                    🍿 ' . __('Mã Nhận Đồ Ăn F&B Fast Track') . '
                                </h4>
                                <p style="margin: 5px 0; font-size: 14px;"><strong>' . __('Combo:') . '</strong> ' . $combo . '</p>
                                <p style="margin: 5px 0; font-size: 13px;"><strong>' . __('Trạng thái:') . '</strong> ' . $fb_status . '</p>
                                <p style="margin: 10px 0 0 0; font-size: 12px; color: #ccc;"><i class="fa fa-info-circle"></i> ' . __('Trình mã này tại quầy ưu tiên để nhận đồ ăn nhanh chóng mà không cần xếp hàng.') . '</p>
                            </div>
                            ';
                        if (empty($fb_check_in_luc)) {
                            echo '
                            <div style="background: #fff; padding: 8px; border-radius: 8px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($fb_qr_url) . '" alt="F&B QR Code" style="width: 100px; height: 100px; display: block; object-fit: contain;" />
                            </div>
                            ';
                        }
                        echo '
                        </div>
                        ';
                    }
            ?>
        </div>
    </section>
    <?php
// Giả sử $loadone_ve contains ticket details
$qr_data = "http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $base_path . "/Trang-nguoi-dung/index.php?act=quetve&id=" . $loadone_ve['id'];
?>
<!-- Div ẩn chỉ dùng để xuất ảnh -->
<div id="qr-ticket-download" style="display:none; background:#fff; padding:20px; border-radius:8px; max-width:400px; margin:0 auto;">
    <h2 style="text-align:center;"><?= __('Vé xem phim') ?></h2>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($qr_data) ?>" alt="QR Code Vé Xem Phim" style="display:block; margin:0 auto; width:180px; height:180px;" />
    <hr>
    <div style="font-size:25px;">
        <p><b><?= __('Phim:') ?></b> <?= __($tieu_de) ?></p>
        <p><b><?= __('Ghế:') ?></b> <?= $ghe ?></p>
        <p><b><?= __('Ngày chiếu:') ?></b> <?= $ngay_chieu ?></p>
        <p><b><?= __('Giờ chiếu:') ?></b> <?= $thoi_gian_chieu ?></p>
        <p><b><?= __('Phòng:') ?></b> <?= $tenphong ?></p>
        <p><b><?= __('Giá vé:') ?></b> <?= number_format($price) ?> VNĐ</p>
        <p><b><?= __('Combo:') ?></b> <?= $combo ?></p>
        <p><b><?= __('Trạng thái:') ?></b>
            <?php
            switch ($trang_thai) {
                case 1: echo __('Đã thanh toán'); break;
                case 2: echo __('Đã dùng'); break;
                case 3: echo __('Đã hủy'); break;
                case 4: echo __('Hết hạn'); break;
                default: echo __('Không xác định');
            }
            ?>
        </p>
    </div>
    <div style="text-align:center; margin-top:10px; font-size:13px; color:#888;"><?= __('Vui lòng đưa mã này cho nhân viên tại cổng kiểm soát') ?><br><?= __('Galaxy Studio xin cảm ơn!') ?></div>
</div>
<div style="text-align:center; margin-top:20px; display:none;">
    <button id="save-qr-btn" type="button" style="padding:10px 20px; font-size:16px; background:#007bff; color:#fff; border:none; border-radius:4px;"><?= __('Lưu mã QR (ảnh)') ?></button>
    <div id="save-qr-error" style="color:red; margin-top:10px; display:none;"></div>
</div>
<div style="text-align:center; margin-top:12px;">
    <a class="btn btn--primary" href="view/ve_invoice.php?id=<?= $loadone_ve['id'] ?>" target="_blank" rel="noopener"><?= __('Tải / In vé (PDF)') ?></a>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
// Ẩn nút lưu QR
if (document.getElementById('save-qr-btn')) {
    document.getElementById('save-qr-btn').onclick = function(e) {
        e.preventDefault();
        const ticket = document.getElementById('qr-ticket-download');
        const errorDiv = document.getElementById('save-qr-error');
        errorDiv.style.display = 'none';
        ticket.style.display = 'block';
        html2canvas(ticket).then(canvas => {
            try {
                const link = document.createElement('a');
                link.download = 've_xem_phim.png';
                link.href = canvas.toDataURL('image/png');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } catch (e) {
                errorDiv.textContent = '<?= __('Không thể lưu ảnh. Vui lòng thử lại trên trình duyệt khác hoặc kiểm tra cài đặt tải file.') ?>';
                errorDiv.style.display = 'block';
            }
            ticket.style.display = 'none';
        }).catch(function(err) {
            errorDiv.textContent = '<?= __('Lỗi khi tạo ảnh:') ?> ' + err;
            errorDiv.style.display = 'block';
            ticket.style.display = 'none';
        });
    };
}
</script>
</form>

<?php
} else {
    // Nếu không tồn tại, in ra thông báo "Bạn chưa thanh toán"
    ?>
    <section class="container">
        <p><?= __('Bạn chưa thanh toán vé.') ?></p>
    </section>
    <?php
}
?>