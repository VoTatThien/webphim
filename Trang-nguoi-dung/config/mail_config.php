<?php
/**
 * SMTP Mail Configuration
 * Cấu hình SMTP Gmail gửi vé cho khách hàng
 */

// 🔴 Địa chỉ email gửi đi (Thay thế bằng Gmail mới của bạn)
define('SMTP_USERNAME', 'thanhbang0162@gmail.com');

// 🔴 Mật khẩu ứng dụng 16 ký tự (Thay thế bằng Mật khẩu ứng dụng của Gmail mới)
// Cách tạo: Tài khoản Google -> Bảo mật -> Mật khẩu ứng dụng (App Passwords)
define('SMTP_PASSWORD', 'qjca onic cfks clad');

// Các cấu hình SMTP mặc định (thường không cần đổi nếu dùng Gmail)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_FROM_NAME', 'Galaxy Studio');
?>
