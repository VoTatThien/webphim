<?php include __DIR__ . '/../home/sideheader.php'; ?>

<!-- Content Body Start -->
<div class="content-body" style="padding: 25px; color: #fff; font-family: 'Outfit', sans-serif; background: #151218; min-height: 100vh;">
    <!-- Page Headings Start -->
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-12 col-lg-auto mb-20">
            <div class="page-heading">
                <h3 style="font-size: 28px; font-weight: 800; background: linear-gradient(135deg, #ff9900 0%, #ff5e62 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    ⚠️ <?= __("QUẢN LÝ SỰ CỐ TRANG THIẾT BỊ / GHẾ NGỒI") ?>
                </h3>
                <span style="color: #8a8a8a; font-size: 14px;"><?= __("Ghi nhận, giám sát và xử lý các sự cố cơ sở vật chất phòng chiếu") ?></span>
            </div>
        </div>
    </div>
    
    <?php if (isset($success_suco)): ?>
        <div class="alert alert-success" style="background: rgba(67, 233, 123, 0.1); border: 1px solid #43e97b; color: #43e97b; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <i class="fa fa-check-circle"></i> <?= $success_suco ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_suco)): ?>
        <div class="alert alert-danger" style="background: rgba(255, 94, 98, 0.1); border: 1px solid #ff5e62; color: #ff5e62; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
            <i class="fa fa-exclamation-circle"></i> <?= $error_suco ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <style>
            /* Custom radio buttons for Severity */
            .severity-selector {
                display: flex;
                gap: 10px;
                width: 100%;
                margin-top: 8px;
            }
            .severity-option {
                flex: 1;
                text-align: center;
                border: 1px solid rgba(255,255,255,0.08) !important;
                border-radius: 8px;
                padding: 10px 5px;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                flex-direction: column;
                align-items: center;
                background: rgba(255,255,255,0.02) !important;
                opacity: 0.6;
            }
            .severity-option input[type="radio"] {
                margin-bottom: 8px;
            }
            .severity-option:hover {
                opacity: 0.9;
                border-color: rgba(255,255,255,0.2) !important;
            }
            .severity-option.option-nhe:has(input[type="radio"]:checked) {
                border-color: #43e97b !important;
                background: rgba(67, 233, 123, 0.12) !important;
                opacity: 1;
                box-shadow: 0 0 10px rgba(67, 233, 123, 0.2);
            }
            .severity-option.option-trung_binh:has(input[type="radio"]:checked) {
                border-color: #ff9900 !important;
                background: rgba(255, 153, 0, 0.12) !important;
                opacity: 1;
                box-shadow: 0 0 10px rgba(255, 153, 0, 0.2);
            }
            .severity-option.option-nghiem_trong:has(input[type="radio"]:checked) {
                border-color: #ff5e62 !important;
                background: rgba(255, 94, 98, 0.12) !important;
                opacity: 1;
                box-shadow: 0 0 10px rgba(255, 94, 98, 0.2);
            }
            /* Table Styling */
            .table th {
                font-weight: 700;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 2px solid rgba(255,255,255,0.08) !important;
                padding: 15px 10px !important;
            }
            .table td {
                padding: 14px 10px !important;
                vertical-align: middle !important;
                border-bottom: 1px solid rgba(255,255,255,0.05) !important;
            }
            .btn-update-suco {
                background: linear-gradient(135deg, #007bff 0%, #00c6ff 100%) !important;
                color: #fff !important;
                border: none !important;
                padding: 6px 14px !important;
                border-radius: 6px !important;
                font-weight: 700 !important;
                box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2) !important;
                transition: all 0.3s ease !important;
            }
            .btn-update-suco:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 12px rgba(0, 123, 255, 0.35) !important;
            }
        </style>

        <!-- Cột trái: Khai báo sự cố mới -->
        <div class="col-lg-4 col-12 mb-30">
            <div class="box" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 25px;">
                <div class="box-head" style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px; margin-bottom: 20px;">
                    <h4 style="font-size: 18px; font-weight: 700; color: #ff9900; margin: 0;"><i class="fa fa-plus-circle"></i> <?= __("Báo Cáo Sự Cố Mới") ?></h4>
                </div>
                <div class="box-body">
                    <?php $user_id_rap = (int)($_SESSION['user1']['id_rap'] ?? 0); ?>
                    <form action="index.php?act=QLsuco" method="post">
                        <?php if ($user_id_rap <= 0): ?>
                            <!-- Chọn rạp chiếu -->
                            <div class="form-group mb-4">
                                <label style="color: #ccc; font-weight: 600; margin-bottom: 8px; display: block;"><?= __("Rạp chiếu:") ?></label>
                                <select name="id_rap" id="suco_id_rap" class="form-control" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px; width: 100%;">
                                    <option value=""><?= __("-- Chọn rạp chiếu --") ?></option>
                                    <?php 
                                    $ds_all_raps = pdo_query("SELECT id, ten_rap FROM rap_chieu ORDER BY id ASC");
                                    foreach ($ds_all_raps as $r): ?>
                                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['ten_rap']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Chọn phòng chiếu -->
                        <div class="form-group mb-4">
                            <label style="color: #ccc; font-weight: 600; margin-bottom: 8px; display: block;"><?= __("Phòng chiếu:") ?></label>
                            <select name="id_phong" id="suco_id_phong" class="form-control" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px; width: 100%;">
                                <option value=""><?= __("-- Chọn phòng (Để trống nếu sự cố chung) --") ?></option>
                                <?php foreach ($ds_phong as $phong): ?>
                                    <option value="<?= $phong['id'] ?>" data-rap-id="<?= $phong['id_rap'] ?>"><?= htmlspecialchars($phong['name']) ?> (<?= htmlspecialchars($phong['ten_rap']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Loại sự cố -->
                        <div class="form-group mb-4">
                            <label style="color: #ccc; font-weight: 600; margin-bottom: 8px; display: block;"><?= __("Loại sự cố:") ?></label>
                            <select name="loai_su_co" class="form-control" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px; width: 100%;" required>
                                <option value="ghe_hong">🪑 <?= __("Ghế ngồi bị hỏng / gãy") ?></option>
                                <option value="may_chieu">📹 <?= __("Hỏng máy chiếu / Ống kính") ?></option>
                                <option value="dieu_hoa">❄️ <?= __("Lỗi điều hòa / Hệ thống gió") ?></option>
                                <option value="am_thanh">🔊 <?= __("Hỏng loa / Âm thanh rè") ?></option>
                                <option value="khac">❓ <?= __("Sự cố khác") ?></option>
                            </select>
                        </div>
                        
                        <!-- Vị trí cụ thể -->
                        <div class="form-group mb-4">
                            <label style="color: #ccc; font-weight: 600; margin-bottom: 8px; display: block;"><?= __("Vị trí cụ thể (VD: Ghế C5, Máy chiếu số #1):") ?></label>
                            <input type="text" name="vi_tri" class="form-control" placeholder="E.g. C5" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px; width: 100%;">
                            <span style="font-size: 11px; color: #888; margin-top: 5px; display: block;">* Quan trọng: Nhập chính xác mã ghế (VD: C5) để hệ thống tự khóa ghế khi đặt vé.</span>
                        </div>
                        
                        <!-- Mức độ nghiêm trọng -->
                        <div class="form-group mb-4">
                            <label style="color: #ccc; font-weight: 600; margin-bottom: 8px; display: block;"><?= __("Mức độ nghiêm trọng:") ?></label>
                            <div class="severity-selector">
                                <label class="severity-option option-nhe">
                                    <input type="radio" name="muc_do" value="nhe">
                                    <span style="color: #43e97b; font-weight: bold;">🟢 <?= __("Nhẹ") ?></span>
                                </label>
                                <label class="severity-option option-trung_binh">
                                    <input type="radio" name="muc_do" value="trung_binh" checked>
                                    <span style="color: #ff9900; font-weight: bold;">🟡 <?= __("T.Bình") ?></span>
                                </label>
                                <label class="severity-option option-nghiem_trong">
                                    <input type="radio" name="muc_do" value="nghiem_trong">
                                    <span style="color: #ff5e62; font-weight: bold;">🔴 <?= __("N.Trọng") ?></span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Mô tả chi tiết -->
                        <div class="form-group mb-4">
                            <label style="color: #ccc; font-weight: 600; margin-bottom: 8px; display: block;"><?= __("Mô tả chi tiết sự cố:") ?></label>
                            <textarea name="mo_ta" class="form-control" rows="4" placeholder="<?= __("VD: Ghế bị lung lay dữ dội, gãy tay vịn trái...") ?>" style="background: #222; border: 1px solid #444; color: #fff; border-radius: 6px; padding: 10px; width: 100%;" required></textarea>
                        </div>
                        
                        <button type="submit" name="bao_su_co" class="btn btn-warning w-100" style="background: linear-gradient(135deg, #ff9900 0%, #ff5e62 100%); color: #fff; font-weight: bold; border: none; padding: 12px; border-radius: 6px; cursor: pointer; transition: transform 0.2s;">
                            <i class="fa fa-paper-plane"></i> <?= __("Gửi báo cáo sự cố") ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Cột phải: Danh sách sự cố -->
        <div class="col-lg-8 col-12 mb-30">
            <div class="box" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 25px;">
                <div class="box-head" style="border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 15px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
                    <h4 style="font-size: 18px; font-weight: 700; color: #ff5e62; margin: 0;"><i class="fa fa-list-alt"></i> <?= __("Danh Sách Sự Cố Đã Ghi Nhận") ?></h4>
                </div>
                
                <div class="box-body" style="overflow-x: auto;">
                    <table class="table" style="width: 100%; color: #fff; border-collapse: collapse; min-width: 700px;">
                        <thead>
                            <tr style="border-bottom: 2px solid rgba(255,255,255,0.1); color: #ccc;">
                                <th style="padding: 12px; text-align: left;"><?= __("ID") ?></th>
                                <th style="padding: 12px; text-align: left;"><?= __("Phòng/Vị trí") ?></th>
                                <th style="padding: 12px; text-align: left;"><?= __("Loại & Mô tả") ?></th>
                                <th style="padding: 12px; text-align: left;"><?= __("Mức độ") ?></th>
                                <th style="padding: 12px; text-align: left;"><?= __("Trạng thái") ?></th>
                                <th style="padding: 12px; text-align: left;"><?= __("Chi tiết báo cáo") ?></th>
                                <th style="padding: 12px; text-align: center;"><?= __("Thao tác") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ds_su_co)): ?>
                                <tr>
                                    <td colspan="7" style="padding: 20px; text-align: center; color: #888;"><?= __("Không có sự cố nào được ghi nhận.") ?></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ds_su_co as $sc): ?>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                        <td style="padding: 12px;">#<?= $sc['id'] ?></td>
                                        <td style="padding: 12px;">
                                            <strong><?= htmlspecialchars($sc['ten_phong'] ?? __('Sự cố chung')) ?></strong><br>
                                            <span style="font-size: 12px; color: #ffd564;"><?= $sc['vi_tri'] ? __('Vị trí: ') . htmlspecialchars($sc['vi_tri']) : '' ?></span>
                                        </td>
                                        <td style="padding: 12px; max-width: 200px;">
                                            <span style="font-size: 11px; background: rgba(255,255,255,0.1); padding: 2px 6px; border-radius: 4px; display: inline-block; margin-bottom: 4px;">
                                                <?php
                                                switch ($sc['loai_su_co']) {
                                                    case 'ghe_hong': echo '🪑 ' . __('Ghế hỏng'); break;
                                                    case 'may_chieu': echo '📹 ' . __('Máy chiếu'); break;
                                                    case 'dieu_hoa': echo '❄️ ' . __('Điều hòa'); break;
                                                    case 'am_thanh': echo '🔊 ' . __('Âm thanh'); break;
                                                    default: echo '❓ ' . __('Khác');
                                                }
                                                ?>
                                            </span><br>
                                            <span style="font-size: 13px; color: #ccc;"><?= htmlspecialchars($sc['mo_ta']) ?></span>
                                        </td>
                                        <td style="padding: 12px;">
                                            <?php
                                            switch ($sc['muc_do']) {
                                                case 'nhe':
                                                    echo '<span style="color: #43e97b; background: rgba(67, 233, 123, 0.1); padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">' . __('Nhẹ') . '</span>';
                                                    break;
                                                case 'trung_binh':
                                                    echo '<span style="color: #ff9900; background: rgba(255, 153, 0, 0.1); padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">' . __('Trung bình') . '</span>';
                                                    break;
                                                case 'nghiem_trong':
                                                    echo '<span style="color: #ff5e62; background: rgba(255, 94, 98, 0.1); padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold;">' . __('Nghiêm trọng') . '</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td style="padding: 12px;">
                                            <?php
                                            switch ($sc['trang_thai']) {
                                                case 'chua_xu_ly':
                                                    echo '<span style="color: #ff5e62;"><i class="fa fa-circle"></i> ' . __('Chưa xử lý') . '</span>';
                                                    break;
                                                case 'dang_xu_ly':
                                                    echo '<span style="color: #4facfe;"><i class="fa fa-spinner fa-spin"></i> ' . __('Đang xử lý') . '</span>';
                                                    break;
                                                case 'da_khac_phuc':
                                                    echo '<span style="color: #43e97b;"><i class="fa fa-check-circle"></i> ' . __('Đã khắc phục') . '</span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td style="padding: 12px; font-size: 12px; color: #aaa;">
                                            <?= __('Bởi: ') ?> <strong><?= htmlspecialchars($sc['nguoi_bao_ten'] ?? 'Staff') ?></strong><br>
                                            <span style="font-size: 11px;"><?= $sc['ngay_bao'] ?></span>
                                            <?php if ($sc['trang_thai'] === 'da_khac_phuc'): ?>
                                                <div style="margin-top: 5px; color: #43e97b;">
                                                    <?= __('Xử lý bởi: ') ?> <strong><?= htmlspecialchars($sc['nguoi_xu_ly_ten'] ?? '') ?></strong><br>
                                                    <span style="font-size: 10px;"><?= $sc['ngay_xu_ly'] ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="padding: 12px; text-align: center;">
                                            <?php if ($sc['trang_thai'] !== 'da_khac_phuc'): ?>
                                                <button type="button" class="btn btn-update-suco" onclick="openResolveModal(<?= $sc['id'] ?>, '<?= $sc['trang_thai'] ?>', '<?= htmlspecialchars(addslashes($sc['ghi_chu_xu_ly'] ?? '')) ?>')">
                                                    <i class="fa fa-edit"></i> <?= __("Cập nhật") ?>
                                                </button>
                                            <?php else: ?>
                                                <span style="color: #888; font-size: 12px;"><i class="fa fa-lock"></i> <?= __('Đóng') ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal cập nhật tiến trình xử lý sự cố (Popup Modal) -->
<div id="resolveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); align-items: center; justify-content: center; z-index: 9999; animation: fadeIn 0.3s;">
    <div style="background: #222; border: 1px solid #444; width: 90%; max-width: 500px; padding: 25px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
        <h3 style="margin: 0 0 20px 0; color: #ff9900; font-size: 20px; font-weight: bold;"><i class="fa fa-wrench"></i> <?= __("Cập Nhật Tiến Trình Xử Lý") ?></h3>
        <form action="index.php?act=QLsuco" method="post">
            <input type="hidden" name="id_sc" id="modal_id_sc">
            
            <div class="form-group mb-4">
                <label style="color: #ccc; display: block; margin-bottom: 8px; font-weight: bold;"><?= __("Trạng thái:") ?></label>
                <select name="trang_thai" id="modal_trang_thai" class="form-control" style="background: #333; border: 1px solid #555; color: #fff; border-radius: 6px; padding: 10px; width: 100%;">
                    <option value="chua_xu_ly">🔴 <?= __("Chưa xử lý") ?></option>
                    <option value="dang_xu_ly">🔵 <?= __("Đang xử lý / Đang sửa") ?></option>
                    <option value="da_khac_phuc">🟢 <?= __("Đã khắc phục xong (Mở lại ghế/phòng)") ?></option>
                </select>
            </div>
            
            <div class="form-group mb-4">
                <label style="color: #ccc; display: block; margin-bottom: 8px; font-weight: bold;"><?= __("Ghi chú xử lý:") ?></label>
                <textarea name="ghi_chu_xu_ly" id="modal_ghi_chu" class="form-control" rows="3" placeholder="<?= __("Nhập ghi chú chi tiết về tiến trình khắc phục...") ?>" style="background: #333; border: 1px solid #555; color: #fff; border-radius: 6px; padding: 10px; width: 100%;"></textarea>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeResolveModal()" class="btn btn-secondary" style="background: #555; border: none; color: #fff; padding: 8px 16px; border-radius: 4px; cursor: pointer;"><?= __("Hủy bỏ") ?></button>
                <button type="submit" name="cap_nhat_su_co" class="btn btn-warning" style="background: linear-gradient(135deg, #ff9900 0%, #ff5e62 100%); border: none; color: #fff; font-weight: bold; padding: 8px 20px; border-radius: 4px; cursor: pointer;"><?= __("Lưu thay đổi") ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openResolveModal(id, status, note) {
    document.getElementById('modal_id_sc').value = id;
    document.getElementById('modal_trang_thai').value = status;
    document.getElementById('modal_ghi_chu').value = note;
    document.getElementById('resolveModal').style.display = 'flex';
}

function closeResolveModal() {
    document.getElementById('resolveModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const rapSelect = document.getElementById('suco_id_rap');
    const phongSelect = document.getElementById('suco_id_phong');
    if (rapSelect && phongSelect) {
        const originalOptions = Array.from(phongSelect.options);
        
        rapSelect.addEventListener('change', function() {
            const rapId = this.value;
            phongSelect.innerHTML = '';
            
            // Add default empty option
            phongSelect.appendChild(originalOptions[0]);
            
            originalOptions.forEach(opt => {
                if (opt.value === '') return;
                const optRapId = opt.getAttribute('data-rap-id');
                if (!rapId || optRapId === rapId) {
                    phongSelect.appendChild(opt);
                }
            });
        });
    }
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>
<?php include __DIR__ . '/../home/footer.php'; ?>
