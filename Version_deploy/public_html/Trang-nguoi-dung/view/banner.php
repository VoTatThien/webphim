<?php
// Lấy video banner từ cấu hình (siêu nhanh, không cURL)
$cfg = function_exists('get_website_config') ? get_website_config() : [];
$video_banner_url = !empty($cfg['video_banner']) ? $cfg['video_banner'] : 'video/OFFICIAL TRAILER.mp4';
?>
<!-- Slider -->
<div class="bannercontainer">
    <div class="banner">
        <ul>
            <li data-transition="fade" data-slotamount="7" class="slide">
                <div style="position: relative; width: 100%; height: 100%;">
                    <video class="media-element" autoplay="autoplay" preload="none" loop="loop" muted="" src="<?= htmlspecialchars($video_banner_url) ?>" style="width: 100%;  object-fit: cover;">
                    </video>
                </div>
            </li>
        </ul>
    </div>
</div>


