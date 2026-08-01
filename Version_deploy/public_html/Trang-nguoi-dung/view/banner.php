<?php
// Lấy video banner từ cấu hình admin
$video_banner_url = 'video/OFFICIAL TRAILER.mp4'; // Video mặc định

// Cố gắng lấy từ API config
if (!function_exists('website_get_banner_video')) {
    try {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $base_dir = (strpos($_SERVER['REQUEST_URI'], '/webphim_hung/') !== false) ? '/webphim_hung/' : '/';
        $api_url = $protocol . $host . $base_dir . 'Trang-nguoi-dung/api_config.php';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 3
        ]);
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($http_code === 200 && $response) {
            $data = json_decode($response, true);
            if ($data && $data['success'] && !empty($data['data']['video_banner'])) {
                $video_banner_url = $data['data']['video_banner'];
            }
        }
    } catch (Exception $e) {
        // Dùng video mặc định nếu lỗi
    }
}
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


