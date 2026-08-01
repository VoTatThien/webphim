
<body>
<!-- Form without bootstrap -->
<div class="auth-wrapper" >
    <div class="auth-container" style="margin-top: 100px">
        <div class="auth-action-left">
            <div class="auth-form-outer">
                <h2 class="auth-form-title">
                    <?= __('Đăng ký') ?>
                </h2>
                <?php if (isset($thongbao) && $thongbao != ""): ?>
                    <?php 
                    $is_success = (strpos($thongbao, 'thành công') !== false || strpos($thongbao, 'success') !== false);
                    $bg_color = $is_success ? 'rgba(76, 175, 80, 0.1)' : 'rgba(255, 77, 77, 0.1)';
                    $border_color = $is_success ? 'rgba(76, 175, 80, 0.2)' : 'rgba(255, 77, 77, 0.2)';
                    $text_color = $is_success ? '#4caf50' : '#ff4d4d';
                    $icon = $is_success ? 'fa-check-circle' : 'fa-exclamation-circle';
                    ?>
                    <div class="alert" style="color: <?= $text_color ?>; background-color: <?= $bg_color ?>; border: 1px solid <?= $border_color ?>; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; text-align: left;">
                        <i class="fa <?= $icon ?>" style="margin-right: 8px;"></i>
                        <?= __($thongbao) ?>
                    </div>
                <?php endif; ?>
                <div class="auth-external-container">

                </div>
                <form class="login-form" method="post" action="index.php?act=dangky">
                    <input type="text" class="auth-form-input" placeholder="<?= __('Họ và tên') ?>" name="name" >
                    <input type="text" class="auth-form-input" placeholder="<?= __('Tên đăng nhập') ?>" name="user" >
                    <div class="input-icon">
                        <input type="password" class="auth-form-input" placeholder="<?= __('Mật khẩu') ?>" name="pass" >
                        <i class="fa fa-eye show-password"></i>
                    </div>
                    <input type="text" class="auth-form-input" placeholder="<?= __('Số điện thoại') ?>" name="phone" >
                    <input type="email" class="auth-form-input" placeholder="<?= __('Email') ?>" name="email" >
                    <input type="text" class="auth-form-input" placeholder="<?= __('Địa chỉ') ?>" name="dia_chi" >
                    
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label style="color: #666; font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;"><?= __('Ngày sinh') ?></label>
                        <input type="date" class="auth-form-input" name="ngay_sinh" style="margin-bottom: 0; padding: 10px 15px; border-radius: 4px; border: 1px solid #ddd; width: 100%; display: block; background: #fff;" required>
                    </div>
                    
                    <div style="margin-bottom: 20px; text-align: left;">
                        <label style="color: #666; font-size: 13px; font-weight: 600; display: block; margin-bottom: 8px;"><?= __('Giới tính') ?></label>
                        <div style="display: flex; gap: 15px; margin-top: 5px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 16px; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; background: rgba(255,255,255,0.05); color: #fff; font-weight: 500; transition: all 0.2s; min-width: 95px; justify-content: center; user-select: none;">
                                <input type="radio" name="gioi_tinh" value="nam" checked style="accent-color: #ffd564; width: 16px; height: 16px; margin: 0; cursor: pointer;">
                                <span><?= __('Nam') ?></span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 16px; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; background: rgba(255,255,255,0.05); color: #fff; font-weight: 500; transition: all 0.2s; min-width: 95px; justify-content: center; user-select: none;">
                                <input type="radio" name="gioi_tinh" value="nu" style="accent-color: #ffd564; width: 16px; height: 16px; margin: 0; cursor: pointer;">
                                <span><?= __('Nữ') ?></span>
                            </label>
                        </div>
                    </div>

                    <label class="btn active">
                        <input type="checkbox" name='email1' checked>
                        <i class="fa fa-square-o"></i><i class="fa fa-check-square-o"></i>
                        <span> <?= __('I agree to the') ?> <a href="#"><?= __('Terms') ?></a> <?= __('and') ?> <a href="#"><?= __('Privacy Policy') ?></a>.</span>
                    </label>
                    <div class="footer-action">
                        <input type="submit" value="<?= __('Đăng ký') ?>" class="auth-submit" name="dangky">
                        <a href="index.php?act=dangnhap" class="auth-btn-direct"><?= __('Đăng nhập') ?></a>
                    </div>
                </form>
            </div>
        </div>
        <div class="auth-action-right">
            <div class="auth-image">
                <img src="login-ui2/login-ui2/assets/diadao.jpg" alt="login">
            </div>
        </div>
    </div>
</div>
</body>

