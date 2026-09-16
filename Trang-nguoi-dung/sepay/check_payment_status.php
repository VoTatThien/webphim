<?php
/**
 * Check Payment Status & Confirm Payment (Sepay)
 * POST /sepay/check_payment_status.php
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!$data || !isset($data['ticket_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin mã vé']);
        exit;
    }
    
    $ticket_id = (int)$data['ticket_id'];
    $action = $data['action'] ?? 'check'; // 'check' hoặc 'confirm'
    
    $base_path = '';
    if (preg_match('/^\/([^\/]+)\/(Trang-nguoi-dung|Trang-admin|Version_deploy)/', $_SERVER['REQUEST_URI'], $matches)) {
        $base_path = '/' . $matches[1];
    }
    $redirect_url = $base_path . '/Trang-nguoi-dung/index.php?act=xacnhan&sepay=1';
    
    // Nếu người dùng bấm "Tôi đã chuyển khoản xong (Xác nhận)"
    if ($action === 'confirm') {
        // Đánh dấu đã xác nhận thanh toán Sepay
        $_SESSION['sepay_payment_confirmed'] = true;
        
        // Reset session vé cũ để controller xacnhan tạo vé mới chuẩn
        unset($_SESSION['id_hd']);
        unset($_SESSION['id_ve']);
        unset($_SESSION['da_tao_ve_' . session_id()]);
        
        echo json_encode([
            'success' => true,
            'status' => 'paid',
            'message' => '✅ Xác nhận thanh toán thành công!',
            'redirect_url' => $redirect_url
        ]);
        exit;
    }
    
    // Nếu là kiểm tra tự động (polling):
    // 1. Nếu đã xác nhận trước đó:
    if (!empty($_SESSION['sepay_payment_confirmed'])) {
        echo json_encode([
            'success' => true,
            'status' => 'paid',
            'message' => 'Đã xác nhận thanh toán',
            'redirect_url' => $redirect_url
        ]);
        exit;
    }
    
    // 2. Kiểm tra webhook thực tế trong database nếu có vé tương ứng
    require_once __DIR__ . '/config.php';
    require_once __DIR__ . '/../model/pdo.php';
    
    $ticket = pdo_query_one("SELECT trang_thai FROM ve WHERE id = ? OR ma_ve = ? LIMIT 1", $ticket_id, ORDER_PREFIX . $ticket_id);
    
    if ($ticket && (int)$ticket['trang_thai'] === 1) {
        echo json_encode([
            'success' => true,
            'status' => 'paid',
            'message' => 'Thanh toán thành công từ ngân hàng',
            'redirect_url' => $redirect_url
        ]);
        exit;
    }
    
    // Mặc định: Vẫn đang chờ chuyển khoản (không tự động nhảy ra ngoài)
    echo json_encode([
        'success' => true,
        'status' => 'unpaid',
        'message' => 'Đang chờ chuyển khoản...'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}