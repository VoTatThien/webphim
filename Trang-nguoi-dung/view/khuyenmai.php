<?php include "view/search.php"?>

<style>
.promo-card {
    background: #1c181c;
    border: 1px solid #363033;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 25px;
    position: relative;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 5px solid #ffd564;
}

.promo-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.3);
}

.promo-card:nth-child(even) {
    border-left-color: #28a745;
}

.promo-card:nth-child(3n) {
    border-left-color: #fe505a;
}

.promo-card:nth-child(4n) {
    border-left-color: #ffd564;
}

.promo-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    position: relative;
    z-index: 1;
    gap: 15px;
}

.promo-title {
    color: #fff;
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 10px 0;
}

.promo-code {
    background: #ffd564;
    color: #4c4145;
    padding: 8px 20px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 16px;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    flex-shrink: 0;
    box-shadow: 0 3px 10px rgba(255, 213, 100, 0.2);
}

.promo-code:hover {
    transform: scale(1.05);
    background: #ffe08d;
}

.promo-body {
    position: relative;
    z-index: 1;
}

.promo-description {
    color: #b4b1b2;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 20px;
}

.promo-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin-top: 20px;
}

.promo-detail-item {
    background: #232023;
    padding: 12px 15px;
    border-radius: 10px;
    border: 1px solid #363033;
}

.promo-detail-label {
    color: #a59b9f;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
}

.promo-detail-value {
    color: #ffd564;
    font-size: 15px;
    font-weight: 600;
}

.discount-badge {
    position: absolute;
    top: 47px;
    right: 0px;
    background: #fe505a;
    color: #fff;
    padding: 8px 15px;
    border-radius: 25px;
    font-size: 18px;
    font-weight: 800;
    box-shadow: 0 3px 10px rgba(254, 80, 90, 0.3);
    z-index: 2;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: #a59b9f;
}

.empty-state i {
    font-size: 80px;
    margin-bottom: 20px;
    opacity: 0.3;
}

.cinema-tag {
    display: inline-block;
    background: #232023;
    color: #e5e0e3;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 13px;
    margin-right: 10px;
    border: 1px solid #363033;
}

.cinema-tag i {
    margin-right: 5px;
}

.copy-tooltip {
    position: relative;
}

.copy-tooltip::after {
    content: 'Click để copy!';
    position: absolute;
    bottom: -35px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

.copy-tooltip:hover::after {
    opacity: 1;
}

.promo-left {
    flex: 1;
    min-width: 0;
}

.promo-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
}

.btn-book-now {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffd564;
    color: #4c4145;
    padding: 12px 30px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 16px;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(255, 213, 100, 0.2);
    transition: all 0.3s ease;
    margin-top: 20px;
    border: none;
    cursor: pointer;
    float: right;
}

.btn-book-now:hover {
    transform: translateY(-2px);
    background: #ffe08d;
    color: #4c4145;
    text-decoration: none;
}

.btn-book-now i {
    font-size: 18px;
}

.promo-footer {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px dashed #363033;
    overflow: hidden;
}
</style>

<!-- Main content -->
<section class="container">
    <div class="overflow-wrapper">
        <div class="col-sm-12">

            <h2 class="page-heading"><i class="fa fa-gift" style="color:#ffd564; margin-right:8px;"></i> Khuyến Mãi Đặc Biệt</h2>
            <p style="color: #a59b9f; margin-bottom: 40px; font-size: 16px;">
                Khám phá các ưu đãi hấp dẫn từ Galaxy Studio - Rạp chiếu phim số 1 Việt Nam
            </p>

            <?php if (!empty($ds_khuyenmai)): ?>
                <?php foreach ($ds_khuyenmai as $km): 
                    // Tính toán giảm giá
                    $discount_text = '';
                    if ($km['loai_giam'] === 'phan_tram') {
                        $discount_text = number_format($km['phan_tram_giam']) . '%';
                    } else {
                        $discount_text = number_format($km['gia_tri_giam']) . 'đ';
                    }
                    
                    // Format dates
                    $bat_dau = date('d/m/Y', strtotime($km['ngay_bat_dau']));
                    $ket_thuc = date('d/m/Y', strtotime($km['ngay_ket_thuc']));
                    
                    // Tính số ngày còn lại
                    $today = new DateTime();
                    $end_date = new DateTime($km['ngay_ket_thuc']);
                    $days_left = $today->diff($end_date)->days;
                ?>
                
                <div class="promo-card">
                    <div class="promo-header">
                        <div class="promo-left">
                            <h3 class="promo-title"><?= htmlspecialchars($km['ten_khuyen_mai']) ?></h3>
                            <?php if (!empty($km['ten_rap'])): ?>
                                <span class="cinema-tag" style="background: #152535; color: #64b5f6; border-color: #1e88e5;">
                                    <i class="fa fa-map-marker"></i> <?= htmlspecialchars($km['ten_rap']) ?>
                                </span>
                            <?php else: ?>
                                <span class="cinema-tag" style="background: #2d1f0f; color: #ffb74d; border-color: #fb8c00;">
                                    <i class="fa fa-globe"></i> Áp dụng toàn hệ thống
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="promo-right">
                            <!-- Discount badge nhỏ hơn -->
                            <div class="discount-badge">
                                -<?= $discount_text ?>
                            </div>
                            
                            <!-- Mã khuyến mãi -->
                            <div class="promo-code copy-tooltip" 
                                 onclick="copyPromoCode('<?= htmlspecialchars($km['ma_khuyen_mai']) ?>')"
                                 title="Click để copy mã">
                                <?= htmlspecialchars($km['ma_khuyen_mai']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="promo-body">
                        <?php if (!empty($km['mo_ta'])): ?>
                            <p class="promo-description">
                                <?= nl2br(htmlspecialchars($km['mo_ta'])) ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="promo-details">
                            <div class="promo-detail-item">
                                <div class="promo-detail-label">
                                    <i class="fa fa-calendar"></i> Ngày bắt đầu
                                </div>
                                <div class="promo-detail-value"><?= $bat_dau ?></div>
                            </div>
                            
                            <div class="promo-detail-item">
                                <div class="promo-detail-label">
                                    <i class="fa fa-calendar-times-o"></i> Ngày kết thúc
                                </div>
                                <div class="promo-detail-value"><?= $ket_thuc ?></div>
                            </div>
                            
                            <div class="promo-detail-item">
                                <div class="promo-detail-label">
                                    <i class="fa fa-clock-o"></i> Thời gian còn lại
                                </div>
                                <div class="promo-detail-value">
                                    <?php if ($days_left > 0): ?>
                                        <?= $days_left ?> ngày
                                    <?php else: ?>
                                        Hôm nay là ngày cuối!
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($km['dieu_kien_ap_dung'])): ?>
                                <div class="promo-detail-item">
                                    <div class="promo-detail-label">
                                        <i class="fa fa-info-circle"></i> Điều kiện
                                    </div>
                                    <div class="promo-detail-value" style="font-size: 14px;">
                                        <?= htmlspecialchars($km['dieu_kien_ap_dung']) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Nút đặt vé ngay -->
                        <div class="promo-footer">
                            <?php if (!empty($km['id_rap'])): ?>
                                <!-- Khuyến mãi cho rạp cụ thể -> Đặt vé tại rạp đó -->
                                <a href="index.php?act=phim_theo_rap&id_rap=<?= (int)$km['id_rap'] ?>" class="btn-book-now">
                                    <i class="fa fa-ticket"></i>
                                    Đặt vé tại <?= htmlspecialchars($km['ten_rap']) ?>
                                </a>
                            <?php else: ?>
                                <!-- Khuyến mãi toàn hệ thống -> Luồng đặt vé cũ -->
                                <a href="index.php?act=dsphim1&sotrang=1" class="btn-book-now">
                                    <i class="fa fa-ticket"></i>
                                    Đặt vé ngay
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php endforeach; ?>
                
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa fa-gift" style="font-size:50px; color:#4a3e43; display:block;"></i>
                    <h3>Hiện chưa có khuyến mãi nào</h3>
                    <p>Vui lòng quay lại sau để không bỏ lỡ các ưu đãi hấp dẫn!</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<script>
function copyPromoCode(code) {
    // Copy to clipboard
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(function() {
            showCopySuccess(code);
        }).catch(function(err) {
            fallbackCopyTextToClipboard(code);
        });
    } else {
        fallbackCopyTextToClipboard(code);
    }
}

function fallbackCopyTextToClipboard(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.top = 0;
    textArea.style.left = 0;
    textArea.style.opacity = 0;
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        var successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(text);
        }
    } catch (err) {
        alert('Không thể copy mã. Vui lòng copy thủ công: ' + text);
    }
    
    document.body.removeChild(textArea);
}

function showCopySuccess(code) {
    // Tạo thông báo tạm thời
    var notification = document.createElement('div');
    notification.innerHTML = '<i class="fa fa-check-circle"></i> Đã copy mã: <strong>' + code + '</strong>';
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #28a745; color: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.3); z-index: 99999; font-size: 16px; animation: slideInRight 0.3s ease;';
    
    document.body.appendChild(notification);
    
    setTimeout(function() {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(function() {
            document.body.removeChild(notification);
        }, 300);
    }, 2000);
}

// Add animation styles
var style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>

<?php include "view/footer.php"?>
