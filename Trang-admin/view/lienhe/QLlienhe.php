<?php 
include "./view/home/sideheader.php";

// Count stats
$total_count = 0;
$pending_count = 0;
$processed_count = 0;

$all_lh = loadall_lienhe(null);
if (!empty($all_lh)) {
    $total_count = count($all_lh);
    foreach ($all_lh as $item) {
        if ($item['trang_thai'] == 1) {
            $processed_count++;
        } else {
            $pending_count++;
        }
    }
}
?>

<style>
    .contact-header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        padding: 25px 30px;
        border-radius: 8px;
        margin-bottom: 25px;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }

    .contact-header h3 {
        margin: 0;
        font-size: 26px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
    }

    .contact-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .stat-box {
        background: rgba(255, 255, 255, 0.15);
        padding: 12px 15px;
        border-radius: 6px;
        font-size: 13px;
        backdrop-filter: blur(10px);
    }

    .stat-box strong {
        display: block;
        font-size: 18px;
        margin-top: 5px;
    }

    .filter-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        background: #f3f4f6;
        padding: 10px;
        border-radius: 8px;
    }

    .filter-btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        color: #4b5563;
        background: white;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .filter-btn:hover {
        background: #f9fafb;
        color: #111827;
    }

    .filter-btn.active {
        background: #4f46e5;
        color: white;
        border-color: #4f46e5;
    }

    .contact-grid {
        display: grid;
        gap: 20px;
    }

    .contact-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .contact-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: #4f46e5;
    }

    .contact-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .contact-user {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .contact-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 16px;
    }

    .contact-user-info h4 {
        margin: 0;
        font-size: 15px;
        color: #111827;
        font-weight: 600;
    }

    .contact-email {
        font-size: 13px;
        color: #4f46e5;
        text-decoration: none;
    }

    .contact-date {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 2px;
    }

    .badge-status {
        padding: 4px 8px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-pending {
        background-color: #fef3c7;
        color: #d97706;
    }

    .badge-processed {
        background-color: #d1fae5;
        color: #059669;
    }

    .contact-content {
        background: #f9fafb;
        padding: 12px 15px;
        border-radius: 6px;
        margin: 12px 0;
        border-left: 3px solid #4f46e5;
        font-size: 14px;
        color: #374151;
        line-height: 1.6;
    }

    .contact-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        align-items: center;
    }

    .btn-reply {
        background: #e0e7ff;
        color: #3730a3;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-reply:hover {
        background: #c7d2fe;
    }

    .btn-delete {
        background: #fee2e2;
        color: #991b1b;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-delete:hover {
        background: #fca5a5;
    }

    /* Reply Section */
    .reply-section {
        margin-top: 15px;
        padding: 15px;
        background: #f5f3ff;
        border-radius: 6px;
        border-left: 3px solid #7c3aed;
    }

    .reply-form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .reply-textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 13px;
        resize: vertical;
        min-height: 80px;
        background: white;
    }

    .reply-textarea:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
    }

    .reply-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-submit-reply {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-submit-reply:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .saved-reply {
        background: white;
        padding: 12px 15px;
        border-radius: 4px;
        border: 1px solid #e5e7eb;
        font-size: 13px;
        color: #4b5563;
        line-height: 1.5;
        margin-top: 8px;
    }

    .saved-reply-header {
        font-weight: 600;
        color: #7c3aed;
        margin-bottom: 4px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #9ca3af;
        background: white;
        border-radius: 8px;
        border: 1px dashed #d1d5db;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 15px;
    }
</style>

<!-- Content Body Start -->
<div class="content-body">
    <!-- Header -->
    <div class="contact-header">
        <h3>✉️ <?= __("Quản lý liên hệ") ?></h3>
        <div class="contact-stats">
            <div class="stat-box">
                <span><?= __("Tổng số liên hệ") ?></span>
                <strong><?= $total_count ?></strong>
            </div>
            <div class="stat-box">
                <span><?= __("Chưa xử lý") ?></span>
                <strong><?= $pending_count ?></strong>
            </div>
            <div class="stat-box">
                <span><?= __("Đã xử lý") ?></span>
                <strong><?= $processed_count ?></strong>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <a href="index.php?act=QLlienhe" class="filter-btn <?= $trang_thai_filter === null ? 'active' : '' ?>"><?= __("Tất cả") ?></a>
        <a href="index.php?act=QLlienhe&status=0" class="filter-btn <?= $trang_thai_filter === 0 ? 'active' : '' ?>"><?= __("Chưa xử lý") ?></a>
        <a href="index.php?act=QLlienhe&status=1" class="filter-btn <?= $trang_thai_filter === 1 ? 'active' : '' ?>"><?= __("Đã xử lý") ?></a>
    </div>

    <!-- List -->
    <div class="contact-grid">
        <?php if (!empty($list_lh)): ?>
            <?php foreach ($list_lh as $lh): 
                $avatar_char = strtoupper(substr($lh['ten_khach'] ?? 'K', 0, 1));
                $is_pending = $lh['trang_thai'] == 0;
            ?>
                <div class="contact-card">
                    <div class="contact-header-row">
                        <div class="contact-user">
                            <div class="contact-avatar"><?= $avatar_char ?></div>
                            <div class="contact-user-info">
                                <h4><?= htmlspecialchars($lh['ten_khach']) ?></h4>
                                <a href="mailto:<?= htmlspecialchars($lh['email']) ?>" class="contact-email">✉️ <?= htmlspecialchars($lh['email']) ?></a>
                                <div class="contact-date">📅 <?= date('d/m/Y H:i', strtotime($lh['ngay_tao'])) ?></div>
                            </div>
                        </div>
                        <div>
                            <span class="badge-status <?= $is_pending ? 'badge-pending' : 'badge-processed' ?>">
                                <?= $is_pending ? __("Chưa xử lý") : __("Đã xử lý") ?>
                            </span>
                        </div>
                    </div>

                    <div class="contact-content">
                        <?= nl2br(htmlspecialchars($lh['tin_nhan'])) ?>
                    </div>

                    <!-- Actions -->
                    <div class="contact-actions">
                        <button class="btn-reply" onclick="toggleReplySection(<?= $lh['id'] ?>)">
                            <?= $is_pending ? "💬 " . __("Xử lý / Trả lời") : "👁️ " . __("Xem phản hồi") ?>
                        </button>
                        <a href="index.php?act=xoalienhe&id=<?= $lh['id'] ?>" onclick="return confirm('<?= __('Bạn có chắc chắn muốn xóa phản hồi này không?') ?>')">
                            <button class="btn-delete">🗑️ <?= __("Xóa") ?></button>
                        </a>
                    </div>

                    <!-- Reply & Status update form -->
                    <div class="reply-section" id="reply-section-<?= $lh['id'] ?>" style="display: none;">
                        <form class="reply-form" method="post" action="index.php?act=traloi_lienhe">
                            <input type="hidden" name="id" value="<?= $lh['id'] ?>">
                            
                            <div class="form-group">
                                <label style="font-size: 13px; font-weight: 600; color: #4b5563; margin-bottom: 5px;"><?= __("Nội dung trả lời") ?></label>
                                <textarea name="tra_loi" class="reply-textarea" placeholder="<?= __('Nhập câu trả lời hoặc ghi chú xử lý phản hồi...') ?>" required><?= htmlspecialchars($lh['tra_loi'] ?? '') ?></textarea>
                            </div>

                            <div class="reply-controls">
                                <div class="form-group">
                                    <label style="font-size: 13px; font-weight: 600; color: #4b5563; margin-right: 10px;"><?= __("Trạng thái") ?>:</label>
                                    <select name="trang_thai" style="padding: 6px 12px; border-radius: 4px; border: 1px solid #d1d5db; font-size: 13px;">
                                        <option value="0" <?= $lh['trang_thai'] == 0 ? 'selected' : '' ?>><?= __("Chưa xử lý") ?></option>
                                        <option value="1" <?= $lh['trang_thai'] == 1 ? 'selected' : '' ?>><?= __("Đã xử lý") ?></option>
                                    </select>
                                </div>
                                <button type="submit" class="btn-submit-reply">💾 <?= __("Cập nhật") ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <p><?= __("Chưa có phản hồi liên hệ nào") ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- Content Body End -->

<script>
function toggleReplySection(id) {
    const el = document.getElementById('reply-section-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}
</script>
