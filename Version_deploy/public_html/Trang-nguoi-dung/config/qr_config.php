<?php
/**
 * QR Code Configuration
 * Cấu hình IP address để quét QR code từ điện thoại trên mạng LAN
 */

// 🔴 ĐẶT IP ADDRESS CỦA XAMPP SERVER TẠI ĐÂY
// Hiện tại: 192.168.0.105 (IP Wi-Fi thực tế của máy chủ)
// Bạn tìm bằng lệnh: ipconfig -> tìm "IPv4 Address" của "Wi-Fi" hoặc "Ethernet"
define('QR_SERVER_IP', '192.168.0.105');

// Port (nếu XAMPP chạy trên port khác 80)
define('QR_SERVER_PORT', 80);

// Cách sử dụng trong code:
// $qr_url = "http://" . QR_SERVER_IP . ":".QR_SERVER_PORT."/webphim_hung/...";

// ⚠️ LƯU Ý: Đừng dùng:
// - 192.168.80.1 (đó là VMware Network, không phải server thực)
// - localhost (điện thoại không hiểu)
// - 127.0.0.1 (chỉ dành cho máy local)
?>

