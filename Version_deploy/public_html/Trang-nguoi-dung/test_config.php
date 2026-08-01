<?php
/**
 * Test page: Kiểm tra cấu hình website
 * Truy cập: http://localhost/webphim_hung/Trang-nguoi-dung/test_config.php
 */

// Fetch dữ liệu cấu hình từ API
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => 'http://localhost/webphim_hung/Trang-nguoi-dung/api_config.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 5,
    CURLOPT_HTTPHEADER => ['Accept: application/json']
]);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$data = json_decode($response, true);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test Cấu Hình Website</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .status-box { padding: 15px; margin: 15px 0; border-left: 4px solid #10b981; background: #ecfdf5; border-radius: 5px; }
        .status-box.error { border-left-color: #ef4444; background: #fef2f2; }
        .config-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 20px; }
        .config-item { background: #f9fafb; padding: 15px; border-radius: 5px; border-left: 3px solid #3b82f6; }
        .config-item strong { color: #1f2937; display: block; margin-bottom: 5px; }
        .config-item span { color: #6b7280; word-break: break-all; }
        .logo-preview { margin: 15px 0; padding: 15px; background: #f9fafb; border-radius: 5px; text-align: center; }
        .logo-preview img { max-width: 200px; height: auto; border-radius: 5px; }
        .social-links { margin: 15px 0; }
        .social-links a { display: inline-block; margin-right: 10px; padding: 8px 15px; background: #3b82f6; color: white; text-decoration: none; border-radius: 5px; font-size: 12px; }
        .social-links a:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Test Cấu Hình Website</h1>
            <p>Kiểm tra dữ liệu cấu hình từ API endpoint</p>
        </div>

        <h3>📊 Trạng thái API</h3>
        <?php if ($http_code === 200 && $data && $data['success']): ?>
            <div class="status-box">
                ✅ <strong>API đang hoạt động tốt!</strong>
                <p>HTTP Code: <?= $http_code ?> | Response: Success</p>
            </div>
        <?php else: ?>
            <div class="status-box error">
                ❌ <strong>Lỗi kết nối API!</strong>
                <p>HTTP Code: <?= $http_code ?> | Response: <?= htmlspecialchars($response) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($data && $data['success'] && isset($data['data'])): 
            $config = $data['data'];
        ?>

        <h3>📋 Thông tin Website</h3>
        <div class="config-grid">
            <div class="config-item">
                <strong>📛 Tên Website</strong>
                <span><?= htmlspecialchars($config['ten_website']) ?></span>
            </div>
            <div class="config-item">
                <strong>📧 Email</strong>
                <span><?= htmlspecialchars($config['email'] ?? 'Chưa cấu hình') ?></span>
            </div>
            <div class="config-item">
                <strong>📞 Số Điện Thoại</strong>
                <span><?= htmlspecialchars($config['so_dien_thoai'] ?? 'Chưa cấu hình') ?></span>
            </div>
            <div class="config-item">
                <strong>📍 Địa Chỉ</strong>
                <span><?= htmlspecialchars($config['dia_chi'] ?? 'Chưa cấu hình') ?></span>
            </div>
        </div>

        <h3>🖼️ Logo Preview</h3>
        <div class="logo-preview">
            <img src="<?= htmlspecialchars($config['logo']) ?>" alt="<?= htmlspecialchars($config['ten_website']) ?>">
            <p><small>Path: <?= htmlspecialchars($config['logo']) ?></small></p>
        </div>

        <h3>📱 Mạng Xã Hội</h3>
        <div class="social-links">
            <?php if (!empty($config['facebook'])): ?>
                <a href="<?= htmlspecialchars($config['facebook']) ?>" target="_blank">👍 Facebook</a>
            <?php else: ?>
                <span style="opacity: 0.5;">👍 Facebook (chưa cấu hình)</span>
            <?php endif; ?>

            <?php if (!empty($config['instagram'])): ?>
                <a href="<?= htmlspecialchars($config['instagram']) ?>" target="_blank">📷 Instagram</a>
            <?php else: ?>
                <span style="opacity: 0.5;">📷 Instagram (chưa cấu hình)</span>
            <?php endif; ?>

            <?php if (!empty($config['youtube'])): ?>
                <a href="<?= htmlspecialchars($config['youtube']) ?>" target="_blank">▶️ YouTube</a>
            <?php else: ?>
                <span style="opacity: 0.5;">▶️ YouTube (chưa cấu hình)</span>
            <?php endif; ?>
        </div>

        <h3>📝 Mô Tả</h3>
        <div style="background: #f9fafb; padding: 15px; border-radius: 5px; border-left: 3px solid #3b82f6;">
            <p><?= htmlspecialchars($config['mo_ta'] ?? 'Chưa cấu hình') ?></p>
        </div>

        <h3>🔄 JSON Response</h3>
        <pre style="background: #1f2937; color: #10b981; padding: 15px; border-radius: 5px; overflow-x: auto;">
<?= json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?>
        </pre>

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">
        <p style="text-align: center; color: #6b7280;">
            Để thay đổi thông tin, vui lòng truy cập: 
            <strong><a href="http://localhost/webphim_hung/Trang-admin/index.php?act=cauhinh">Admin Panel - Cấu Hình Website</a></strong>
        </p>

        <?php else: ?>
        <div class="status-box error">
            <strong>⚠️ Không thể lấy dữ liệu cấu hình</strong>
            <p>Response: <?= htmlspecialchars($response) ?></p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
