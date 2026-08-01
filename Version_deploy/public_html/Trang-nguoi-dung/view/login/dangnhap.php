
<!-- Form without bootstrap -->
<div class="auth-wrapper">
    <div class="auth-container" style="margin-top: 100px">
        <div class="auth-action-left">
            <div class="auth-form-outer">
                <?php if(isset($_SESSION['user'])){
                    extract($_SESSION['user']);
                    $link_ve = "index.php?act=ve&id=".$id;
                    echo ' <h3>' . __('Xin chào : ') . $name . ' </h3>';
                    echo ' <p style="color: #ffd564; font-weight: bold; margin: 10px 0;"><i class="fa fa-star"></i> ' . __('Điểm tích lũy: ') . number_format($diem_tich_luy) . ' ' . __('điểm') . ' (' . __('Hạng ') . __(ucfirst($hang_thanh_vien)) . ')</p>';
                    echo '<br><button class="btn btn-md btn--warning"><a href="index.php?act=suatk&idsua='.$id.'">' . __('Cập nhật tài khoản') . '</a></button>';
                    echo '<br><button class="btn btn-md btn--warning"><a href="'.$link_ve.'">' . __('Vé của tôi') . '</a></button>';
                    echo '<br><button class="btn btn-md btn--warning"><a href="index.php?act=doimk">' . __('Đổi mật khẩu') . '</a></button>';
                    echo '<br><button class="btn btn-md btn--warning"><a href="index.php?act=dangxuat">' . __('Đăng xuất') . '</a></button>';
                    
                    // ==========================================
                    // MEMBER MISSIONS SECTION (GAMIFICATION)
                    // ==========================================
                    $id_tk = (int)$id;
                    $current_month = (int)date('m');
                    $current_year = (int)date('Y');
                    
                    // 1. Phim đã xem trong tháng
                    $sql_phim = "SELECT COUNT(DISTINCT id_phim) as total FROM ve WHERE id_tk = ? AND (trang_thai = 1 OR trang_thai = 4) AND MONTH(ngay_dat) = ? AND YEAR(ngay_dat) = ?";
                    $res_phim = pdo_query_one($sql_phim, $id_tk, $current_month, $current_year);
                    $phim_count = $res_phim ? (int)$res_phim['total'] : 0;
                    $phim_target = 3;
                    $phim_pct = min(100, round(($phim_count / $phim_target) * 100));
                    
                    // 2. Combo F&B đã mua trong tháng
                    $sql_combo = "SELECT COUNT(*) as total FROM ve WHERE id_tk = ? AND (trang_thai = 1 OR trang_thai = 4) AND combo IS NOT NULL AND combo != '' AND MONTH(ngay_dat) = ? AND YEAR(ngay_dat) = ?";
                    $res_combo = pdo_query_one($sql_combo, $id_tk, $current_month, $current_year);
                    $combo_count = $res_combo ? (int)$res_combo['total'] : 0;
                    $combo_target = 2;
                    $combo_pct = min(100, round(($combo_count / $combo_target) * 100));
                    
                    // 3. CinePass Subactive
                    $is_sub = (isset($cinepass_sub_status) && $cinepass_sub_status == 1 && strtotime($cinepass_expire_date) >= time()) ? 1 : 0;
                    $sub_pct = $is_sub ? 100 : 0;
                    
                    // Check if missions are already claimed this month
                    $claimed_keys = [];
                    try {
                        $sql_claimed = "SELECT mission_key FROM claimed_missions WHERE id_tk = ? AND thang = ? AND nam = ?";
                        $claimed_rows = pdo_query($sql_claimed, $id_tk, $current_month, $current_year);
                        $claimed_keys = $claimed_rows ? array_column($claimed_rows, 'mission_key') : [];
                    } catch (Exception $e) {
                        // Suppress if DB table not upgraded yet
                    }
                    ?>
                    
                    <div class="member-missions-card" style="margin-top: 25px; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 12px; padding: 20px; color: #fff; text-align: left;">
                        <h4 style="margin: 0 0 15px 0; color: #ffd564; font-size: 18px; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
                            <i class="fa fa-trophy" style="margin-right: 8px;"></i> <?= __("Nhiệm Vụ Tích Điểm Hội Viên") ?> (<?= __("Tháng ") . $current_month ?>)
                        </h4>
                        
                        <!-- Mission 1: Watch 3 Movies -->
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 5px;">
                                <span>🎬 <strong><?= __("Xem 3 bộ phim khác nhau") ?></strong> (+100 <?= __("điểm") ?>)</span>
                                <span style="color: #ffd564; font-weight: bold;"><?= $phim_count ?>/3</span>
                            </div>
                            <div style="background: #333; height: 10px; border-radius: 5px; overflow: hidden; position: relative;">
                                <div style="background: linear-gradient(90deg, #ffd564, #ffa600); width: <?= $phim_pct ?>%; height: 100%; border-radius: 5px; transition: width 0.5s;"></div>
                            </div>
                            <div style="margin-top: 8px; text-align: right;">
                                <?php if (in_array('mission_phim', $claimed_keys)): ?>
                                    <span style="color: #888; font-size: 12px;"><i class="fa fa-check-circle"></i> <?= __("Đã nhận thưởng") ?></span>
                                <?php elseif ($phim_count >= $phim_target): ?>
                                    <button class="btn btn-xs btn--success claim-mission-btn" data-mission="mission_phim" style="background: #43e97b; color: #000; border: none; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;"><?= __("Nhận 100 Điểm") ?></button>
                                <?php else: ?>
                                    <span style="color: #aaa; font-size: 12px;"><?= __("Chưa hoàn thành") ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Mission 2: Buy 2 Combos -->
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 5px;">
                                <span>🍿 <strong><?= __("Mua 2 Combo bỏng nước") ?></strong> (+50 <?= __("điểm") ?>)</span>
                                <span style="color: #ffd564; font-weight: bold;"><?= $combo_count ?>/2</span>
                            </div>
                            <div style="background: #333; height: 10px; border-radius: 5px; overflow: hidden; position: relative;">
                                <div style="background: linear-gradient(90deg, #4facfe, #00f2fe); width: <?= $combo_pct ?>%; height: 100%; border-radius: 5px; transition: width 0.5s;"></div>
                            </div>
                            <div style="margin-top: 8px; text-align: right;">
                                <?php if (in_array('mission_combo', $claimed_keys)): ?>
                                    <span style="color: #888; font-size: 12px;"><i class="fa fa-check-circle"></i> <?= __("Đã nhận thưởng") ?></span>
                                <?php elseif ($combo_count >= $combo_target): ?>
                                    <button class="btn btn-xs btn--success claim-mission-btn" data-mission="mission_combo" style="background: #43e97b; color: #000; border: none; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;"><?= __("Nhận 50 Điểm") ?></button>
                                <?php else: ?>
                                    <span style="color: #aaa; font-size: 12px;"><?= __("Chưa hoàn thành") ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Mission 3: CinePass Subscription -->
                        <div>
                            <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 5px;">
                                <span>🎫 <strong><?= __("Gia hạn/Đăng ký Gói CinePass VIP") ?></strong> (+150 <?= __("điểm") ?>)</span>
                                <span style="color: #ffd564; font-weight: bold;"><?= $is_sub ? '1/1' : '0/1' ?></span>
                            </div>
                            <div style="background: #333; height: 10px; border-radius: 5px; overflow: hidden; position: relative;">
                                <div style="background: linear-gradient(90deg, #f12711, #f5af19); width: <?= $sub_pct ?>%; height: 100%; border-radius: 5px; transition: width 0.5s;"></div>
                            </div>
                            <div style="margin-top: 8px; text-align: right;">
                                <?php if (in_array('mission_sub', $claimed_keys)): ?>
                                    <span style="color: #888; font-size: 12px;"><i class="fa fa-check-circle"></i> <?= __("Đã nhận thưởng") ?></span>
                                <?php elseif ($is_sub): ?>
                                    <button class="btn btn-xs btn--success claim-mission-btn" data-mission="mission_sub" style="background: #43e97b; color: #000; border: none; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer;"><?= __("Nhận 150 Điểm") ?></button>
                                <?php else: ?>
                                    <a href="index.php?act=cinepass_sub" style="color: #ffd564; font-size: 12px; text-decoration: underline;"><?= __("Đăng ký ngay") ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const claimButtons = document.querySelectorAll(".claim-mission-btn");
                        claimButtons.forEach(btn => {
                            btn.addEventListener("click", function() {
                                const missionKey = this.getAttribute("data-mission");
                                const buttonEl = this;
                                
                                fetch(`index.php?act=claim_mission_ajax&mission=${missionKey}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert(`Chúc mừng! Bạn đã nhận thành công ${data.points} điểm thưởng.`);
                                            const parent = buttonEl.parentNode;
                                            parent.innerHTML = '<span style="color: #888; font-size: 12px;"><i class="fa fa-check-circle"></i> Đã nhận thưởng</span>';
                                            // Optional: reload page to update total points display
                                            location.reload();
                                        } else {
                                            alert(data.message || "Có lỗi xảy ra khi nhận điểm!");
                                        }
                                    })
                                    .catch(err => {
                                        console.error("Error claiming mission:", err);
                                        alert("Có lỗi kết nối mạng!");
                                    });
                            });
                        });
                    });
                    </script>
                <?php }else{ ?>

                <h2 class="auth-form-title" style="margin-bottom:10px;">
                  <div class="dn"><?= __('Đăng nhập') ?></div>

                <div class="auth-external-container" style="margin-top: 20px;">
                </div>


                <form class="login-form" method="post" action="index.php?act=dangnhap">
                    <?php 
                    $msg = "";
                    if (isset($thongbao) && is_string($thongbao) && $thongbao !== "") {
                        $msg = $thongbao;
                    } elseif (isset($error) && is_string($error) && $error !== "") {
                        $msg = $error;
                    } elseif (isset($thongbao1) && is_string($thongbao1) && $thongbao1 !== "") {
                        $msg = $thongbao1;
                    } elseif (isset($thongbao['dangnhap']) && is_string($thongbao['dangnhap'])) {
                        $msg = $thongbao['dangnhap'];
                    }
                    if ($msg !== ""): 
                    ?>
                        <div class="alert alert-danger" style="color: #ff4d4d; background-color: rgba(255, 77, 77, 0.1); border: 1px solid rgba(255, 77, 77, 0.2); padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; text-align: left;">
                            <i class="fa fa-exclamation-circle" style="margin-right: 8px;"></i>
                            <?= __($msg) ?>
                        </div>
                    <?php endif; ?>
                    <input type="text" class="auth-form-input" name="user"  placeholder="<?= __('Tên đăng nhập') ?>" >
                    <div class="input-icon">
                        <input type="password" class="auth-form-input" name="pass" placeholder="<?= __('Mật khẩu') ?>" >
                        <i class="fa fa-eye show-password"></i>
                    </div>
                    <div class="footer-action">
                        <input type="submit" value="<?= __('Đăng nhập') ?>" class="auth-submit" name="login">
                        <a href="index.php?act=dangky" class="auth-btn-direct"><?= __('Đăng ký') ?></a>
                        <a href="../Trang-admin/login.php" class="auth-btn-direct" style="color: red; font-weight: bold;"><?= __('Đăng nhập với tư cách Quản trị') ?></a>

                    </div>
                </form>
                <div class="auth-forgot-password">
                    <a href="index.php?act=quenmk"><?= __('Quên mật khẩu') ?></a>
                </div>
                <?php } ?>
            </div>
        </div>

        <div class="auth-action-right">
            <div class="auth-image">
                <img src="login-ui2/login-ui2/assets/diadao.jpg" alt="login">
            </div>
        </div>
    </div>
</div>

