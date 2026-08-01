<?php 
    extract($loadtk);
 ?>
<body>
<!-- Form without bootstrap -->
<div class="auth-wrapper" >
    <div class="auth-container" style="margin-top: 100px">
        <div class="auth-action-left">
            <div class="auth-form-outer">
                <h2 class="auth-form-title">
                    <?= __('Cập nhật tài khoản') ?>
                </h2>
                <div class="auth-external-container">

                </div>
                <form class="login-form" method="post" action="index.php?act=updatetk">
                    <input type="text" class="auth-form-input" placeholder="<?= __('Tên đăng nhập') ?>" name="user" value="<?=$user?>">
                    <input type="text" class="auth-form-input" placeholder="<?= __('Số điện thoại') ?>" name="phone" value="<?=$phone?>">
                    <input type="email" class="auth-form-input" placeholder="<?= __('Email') ?>" name="email" value="<?=$email?>">
                    <input type="text" class="auth-form-input" placeholder="<?= __('Địa chỉ') ?>" name="dia_chi" value="<?=$dia_chi?>">
                    
                    <div style="margin-bottom: 15px; text-align: left;">
                        <label style="color: #666; font-size: 13px; font-weight: 600; display: block; margin-bottom: 5px;"><?= __('Ngày sinh') ?></label>
                        <input type="date" class="auth-form-input" name="ngay_sinh" value="<?= $ngay_sinh ?? '' ?>" style="margin-bottom: 0; padding: 10px 15px; border-radius: 4px; border: 1px solid #ddd; width: 100%; display: block; background: #fff;">
                    </div>
                    
                    <div style="margin-bottom: 20px; text-align: left;">
                        <label style="color: #666; font-size: 13px; font-weight: 600; display: block; margin-bottom: 8px;"><?= __('Giới tính') ?></label>
                        <div style="display: flex; gap: 15px; margin-top: 5px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 16px; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; background: rgba(255,255,255,0.05); color: #fff; font-weight: 500; transition: all 0.2s; min-width: 95px; justify-content: center; user-select: none;">
                                <input type="radio" name="gioi_tinh" value="nam" <?= (isset($gioi_tinh) && $gioi_tinh === 'nam') ? 'checked' : '' ?> style="accent-color: #ffd564; width: 16px; height: 16px; margin: 0; cursor: pointer;">
                                <span><?= __('Nam') ?></span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px 16px; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; background: rgba(255,255,255,0.05); color: #fff; font-weight: 500; transition: all 0.2s; min-width: 95px; justify-content: center; user-select: none;">
                                <input type="radio" name="gioi_tinh" value="nu" <?= (isset($gioi_tinh) && $gioi_tinh === 'nu') ? 'checked' : '' ?> style="accent-color: #ffd564; width: 16px; height: 16px; margin: 0; cursor: pointer;">
                                <span><?= __('Nữ') ?></span>
                            </label>
                        </div>
                    </div>
                    <div class="footer-action">
                        <input type="hidden" class="auth-form-input" placeholder="Name" name="id" value="<?=$id?>">
                        <input type="submit" value="<?= __('Cập nhật') ?>" class="auth-submit" name="capnhat">
                    </div>
                </form>
                <?php if(isset($thongbao)&&$thongbao !=""){
                echo '<p  style="color: red; "
                > '.$thongbao.' </p>';
            } ?>
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