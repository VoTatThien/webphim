<?php
/**
 * Gemini AI Chatbot Configuration
 * File: Trang-nguoi-dung/config/chatbot_config.php
 */

// 🔴 Cấu hình API Key của Gemini (Nhận tại: https://aistudio.google.com/)
// Để trống để sử dụng chế độ trả lời tự động thông minh (Fallback) của hệ thống
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: '');

// Cấu hình Model sử dụng (Mặc định là gemini-1.5-flash hoặc gemini-2.5-flash)
define('GEMINI_MODEL', 'gemini-2.5-flash-lite');

// Trạng thái hoạt động của chatbot (true = bật, false = tắt)
define('CHATBOT_ENABLED', true);
?>
