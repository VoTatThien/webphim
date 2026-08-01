<?php include "view/search.php"?>


<!-- Main content -->
<section class="container">
    <h2 class="page-heading heading--outcontainer"><?=__("Liên hệ")?></h2>
    <div class="contact">
        <p class="contact__title"><?=__('Bạn có thắc mắc hoặc cần trợ giúp, <br><span class="contact__describe">đừng ngại ngùng và liên hệ với chúng tôi</span>')?></p>
        <span class="contact__mail">huyhung@gmail.com</span>
        <span class="contact__tel">0912345678</span>
    </div>
</section>

<div class="contact-form-wrapper">
    <div class="container">
        <div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
            <?php if (!empty($thongbao)): ?>
                <div class="alert <?= $thongbao_type === 'success' ? 'alert-success' : 'alert-danger' ?>" style="margin-bottom: 20px; padding: 15px; border-radius: 4px; font-size: 15px; text-align: center; color: #fff; background-color: <?= $thongbao_type === 'success' ? '#4caf50' : '#f44336' ?>;">
                    <?= $thongbao ?>
                </div>
            <?php endif; ?>
            <form id='contact-form' class="form row" method='post' action="index.php?act=lienhe">
                <p class="form__title"><?=__("Drop us a line")?></p>
                <div class="col-sm-6">
                    <input type='text' placeholder='<?=__("Your name")?>' name='user-name' class="form__name" required value="<?= htmlspecialchars($_POST['user-name'] ?? '') ?>">
                </div>
                <div class="col-sm-6">
                    <input type='email' placeholder='<?=__("Your email")?>' name='user-email' class="form__mail" required value="<?= htmlspecialchars($_POST['user-email'] ?? '') ?>">
                </div>
                <div class="col-sm-12">
                    <textarea placeholder="<?=__("Your message")?>" name="user-message" class="form__message" required><?= htmlspecialchars($_POST['user-message'] ?? '') ?></textarea>
                </div>
                <button type="submit" name="send_message" class='btn btn-md btn--danger'><?=__("send message")?></button>
            </form>
        </div>
    </div>
</div>

<section class="container">
    <div class="contact">
        <p class="contact__title"><?=__('Trying to find our location? <br> <span class="contact__describe">we are here</span>')?></p>
    </div>
</section>

<div id='location-map' class="map"></div>

<div class="clearfix"></div>
