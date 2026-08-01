<?php
// Lấy thông tin user từ session
if (!isset($_SESSION['user']) || $_SESSION['user']['vai_tro'] != 0) {
    header('Location: index.php?act=dangnhap');
    exit;
}

require_once 'model/pdo.php';
require_once 'model/diem.php';

$id_tk = $_SESSION['user']['id'];

// Load user info từ database để lấy dữ liệu mới nhất (điểm vừa được cộng)
$user_info = pdo_query_one("SELECT * FROM taikhoan WHERE id = ?", $id_tk);
if (!$user_info) {
    $user_info = $_SESSION['user']; // Fallback nếu load DB thất bại
}

// Lấy thông tin hạng hiện tại
$hang_hien_tai = get_thong_tin_hang($user_info['hang_thanh_vien']);
$all_hang = get_all_hang_thanh_vien();

// Tìm hạng tiếp theo
$hang_tiep_theo = null;
foreach ($all_hang as $hang) {
    if ($hang['diem_toi_thieu'] > $user_info['tong_diem_tich_luy']) {
        $hang_tiep_theo = $hang;
        break;
    }
}

// Tính phần trăm tiến độ
if ($hang_tiep_theo) {
    $diem_can_them = $hang_tiep_theo['diem_toi_thieu'] - $user_info['tong_diem_tich_luy'];
    $phan_tram_tien_do = ($user_info['tong_diem_tich_luy'] / $hang_tiep_theo['diem_toi_thieu']) * 100;
} else {
    // Đã đạt hạng cao nhất
    $diem_can_them = 0;
    $phan_tram_tien_do = 100;
}

// Lấy lịch sử điểm (mặc định 50 bản ghi gần nhất)
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$lich_su = get_lich_su_diem($id_tk, $limit);

include "view/search.php";
?>

<style>
    .points-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
    }
    
    .points-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .points-card {
        background: #1c181c;
        border: 1px solid #363033;
        border-left: 4px solid #ffd564;
        padding: 25px;
        border-radius: 15px;
        color: white;
    }
    
    .points-card h3 {
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 10px;
        color: #a59b9f;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .points-card .value {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 5px;
        color: #ffd564;
    }
    
    .points-card .subtitle {
        font-size: 11px;
        color: #a59b9f;
    }
    
    .tier-card {
        background: #1c181c;
        border: 1px solid #363033;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        color: white;
    }
    
    .tier-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 20px;
    }
    
    .tier-progress {
        margin-top: 20px;
    }
    
    .tier-progress-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 13px;
        color: #a59b9f;
    }
    
    .progress-bar-container {
        height: 30px;
        background: #151215;
        border: 1px solid #363033;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }
    
    .progress-bar-fill {
        height: 100%;
        background: #ffd564;
        color: #4c4145;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.85rem;
        transition: width 1s ease-in-out;
    }
    
    .tier-benefits {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .benefit-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px;
        background: #232023;
        border: 1px solid #363033;
        border-radius: 10px;
        font-size: 13px;
    }
    
    .benefit-icon {
        font-size: 1.5rem;
    }
    
    .history-container {
        background: #1c181c;
        border: 1px solid #363033;
        padding: 30px;
        border-radius: 15px;
        color: white;
    }
    
    .history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #363033;
    }
    
    .history-header h2 {
        font-size: 16px;
        margin: 0;
        color: #ffd564;
    }
    
    .filter-dropdown {
        padding: 8px 15px;
        background: #1c181c;
        border: 1px solid #363033;
        color: white;
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
    }
    
    .history-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
    }
    
    .history-table thead th {
        padding: 12px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #a59b9f;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .history-table tbody tr {
        background: #232023;
        transition: all 0.3s ease;
    }
    
    .history-table tbody tr:hover {
        background: #2e292e;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    
    .history-table tbody td {
        padding: 15px 12px;
        font-size: 12px;
        color: #e5e0e3;
    }
    
    .history-table tbody tr td:first-child {
        border-radius: 10px 0 0 10px;
    }
    
    .history-table tbody tr td:last-child {
        border-radius: 0 10px 10px 0;
    }
    
    .transaction-type {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .type-cong {
        background: #1c2e24;
        color: #28a745;
        border: 1px solid #28a74530;
    }
    
    .type-tru {
        background: #2d1818;
        color: #dc3545;
        border: 1px solid #dc354530;
    }
    
    .points-change {
        font-weight: bold;
        font-size: 12px;
    }
    
    .points-add {
        color: #28a745;
    }
    
    .points-subtract {
        color: #dc3545;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #a59b9f;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    
    @media (max-width: 768px) {
        .points-summary {
            grid-template-columns: 1fr;
        }
        
        .history-table {
            font-size: 0.8rem;
        }
        
        .history-table thead {
            display: none;
        }
        
        .history-table tbody tr {
            display: block;
            margin-bottom: 15px;
            border-radius: 10px;
        }
        
        .history-table tbody td {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border: none;
        }
        
        .history-table tbody td::before {
            content: attr(data-label);
            font-weight: bold;
            color: #a59b9f;
        }
    }
</style>

<section class="points-container">
    <!-- Tổng quan điểm -->
    <div class="points-summary">
        <div class="points-card">
            <h3>💎 <?= __('Điểm hiện có') ?></h3>
            <div class="value"><?= number_format($user_info['diem_tich_luy']) ?></div>
            <div class="subtitle"><?= __('Có thể sử dụng') ?></div>
        </div>
        
        <div class="points-card" style="border-left-color: #fe505a;">
            <h3>🏆 <?= __('Tổng điểm tích luỹ') ?></h3>
            <div class="value" style="color: #fe505a;"><?= number_format($user_info['tong_diem_tich_luy']) ?></div>
            <div class="subtitle"><?= __('Từ trước đến nay') ?></div>
        </div>
        
        <div class="points-card" style="border-left-color: #10b981;">
            <h3>⭐ <?= __('Hệ số nhân điểm') ?></h3>
            <div class="value" style="color: #10b981;"><?= $hang_hien_tai['ti_le_tich_diem'] ?>x</div>
            <div class="subtitle"><?= __('Hạng') ?> <?= __($hang_hien_tai['ten_hang']) ?></div>
        </div>
    </div>
    
    <!-- Thông tin hạng thành viên -->
    <div class="tier-card">
        <div class="tier-badge" style="background-color: <?= $hang_hien_tai['mau_sac'] ?>; color: #fff;">
            <i class="fa fa-trophy" style="color: #ffd564; font-size: 1.5rem;"></i>
            <span><?= __('Hạng') ?> <?= __($hang_hien_tai['ten_hang']) ?></span>
        </div>
        
        <div class="tier-benefits">
            <div class="benefit-item">
                <span class="benefit-icon" style="color: #ffd564;"><i class="fa fa-star"></i></span>
                <span><?= __('Tích điểm') ?> x<?= $hang_hien_tai['ti_le_tich_diem'] ?></span>
            </div>
            <div class="benefit-item">
                <span class="benefit-icon" style="color: #ffd564;"><i class="fa fa-gift"></i></span>
                <span><?= __('Giảm giá') ?> <?= $hang_hien_tai['ti_le_giam_gia'] ?>%</span>
            </div>
            <?php if ($hang_hien_tai['uu_dai_khac']): ?>
            <div class="benefit-item">
                <span class="benefit-icon" style="color: #ffd564;"><i class="fa fa-star"></i></span>
                <span><?= __($hang_hien_tai['uu_dai_khac']) ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($hang_tiep_theo): ?>
        <div class="tier-progress">
            <div class="tier-progress-label">
                <span><strong><?= __('Tiến độ lên hạng') ?> <?= __($hang_tiep_theo['ten_hang']) ?></strong></span>
                <span><?= __('Còn') ?> <strong><?= number_format($diem_can_them) ?></strong> <?= __('điểm') ?></span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: <?= min($phan_tram_tien_do, 100) ?>%">
                    <?= number_format($phan_tram_tien_do, 1) ?>%
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="tier-progress">
            <div style="text-align: center; padding: 20px; color: #ffd564; font-weight: bold; font-size: 1.1rem;">
                <i class="fa fa-check-circle"></i> <?= __('Chúc mừng! Bạn đã đạt hạng cao nhất!') ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Lịch sử giao dịch -->
    <div class="history-container">
        <div class="history-header">
            <h2><i class="fa fa-history" style="color:#ffd564; margin-right:8px;"></i> <?= __('Lịch sử giao dịch') ?></h2>
            <select class="filter-dropdown" onchange="window.location.href='index.php?act=lich_su_diem&limit=' + this.value">
                <option value="50" <?= $limit == 50 ? 'selected' : '' ?>><?= __('50 giao dịch gần nhất') ?></option>
                <option value="100" <?= $limit == 100 ? 'selected' : '' ?>><?= __('100 giao dịch gần nhất') ?></option>
                <option value="200" <?= $limit == 200 ? 'selected' : '' ?>><?= __('200 giao dịch gần nhất') ?></option>
                <option value="999999" <?= $limit == 999999 ? 'selected' : '' ?>><?= __('Tất cả') ?></option>
            </select>
        </div>
        
        <?php if (empty($lich_su)): ?>
        <div class="empty-state">
            <i class="fa fa-history" style="font-size:50px; color:#4a3e43; display:block;"></i>
            <h3><?= __('Chưa có lịch sử giao dịch') ?></h3>
            <p><?= __('Hãy đặt vé xem phim để bắt đầu tích điểm nhé!') ?></p>
        </div>
        <?php else: ?>
        <table class="history-table">
            <thead>
                <tr>
                    <th><?= __('Ngày giờ') ?></th>
                    <th><?= __('Loại giao dịch') ?></th>
                    <th><?= __('Nội dung') ?></th>
                    <th><?= __('Điểm thay đổi') ?></th>
                    <th><?= __('Mã vé') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lich_su as $gd): ?>
                <tr>
                    <td data-label="<?= __('Ngày giờ') ?>">
                        <div style="line-height: 1.4;">
                            <div><?= date('d/m/Y', strtotime($gd['ngay_tao'])) ?></div>
                            <div style="font-size: 0.8rem; color: #999;"><?= date('H:i:s', strtotime($gd['ngay_tao'])) ?></div>
                        </div>
                    </td>
                    <td data-label="<?= __('Loại') ?>">
                        <?php if ($gd['loai_giao_dich'] == 'cong'): ?>
                            <span class="transaction-type type-cong"><i class="fa fa-plus-circle"></i> <?= __('Cộng điểm') ?></span>
                        <?php else: ?>
                            <span class="transaction-type type-tru"><i class="fa fa-minus-circle"></i> <?= __('Trừ điểm') ?></span>
                        <?php endif; ?>
                    </td>
                    <td data-label="<?= __('Nội dung') ?>">
                        <?= htmlspecialchars(__($gd['ly_do'])) ?>
                    </td>
                    <td data-label="<?= __('Điểm') ?>">
                        <?php if ($gd['loai_giao_dich'] == 'cong'): ?>
                            <span class="points-change points-add">+<?= number_format($gd['so_diem']) ?></span>
                        <?php else: ?>
                            <span class="points-change points-subtract">-<?= number_format($gd['so_diem']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td data-label="<?= __('Mã vé') ?>">
                        <?php if ($gd['id_ve']): ?>
                            <a href="index.php?act=ctve&id=<?= $gd['id_ve'] ?>" style="color: #ffd564; text-decoration: none;">
                                #<?= $gd['id_ve'] ?>
                            </a>
                        <?php else: ?>
                            <span style="color: #999;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</section>

<?php include "view/footer.php"; ?>
