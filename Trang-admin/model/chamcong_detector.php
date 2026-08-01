<?php
/**
 * Face Detection API for Employee Attendance
 * Receives base64 image data, detects faces, saves attendance record
 */

session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

if (!function_exists('pdo_get_connection')) {
    include 'pdo.php';
}

if (!defined('ROLES')) {
    include '../helpers/quyen.php';
}

// Include attendance functions (for face verification with Haar Cascade)
if (!function_exists('cc_verify_face_strict')) {
    include 'chamcong.php';
}

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Lỗi chưa xác định',
    'faces_detected' => 0,
    'face_data' => [],
    'debug' => [] // For debugging
];

try {
    // Get POST data
    $action = $_POST['action'] ?? null;
    $photo_data = $_POST['photo'] ?? null;
    $fingerprint_data = $_POST['fingerprint_' . $action] ?? null; // fingerprint_checkin hoặc fingerprint_checkout
    
    // Get GPS data (optional)
    $latitude = isset($_POST['latitude']) ? (float)$_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) ? (float)$_POST['longitude'] : null;
    $location_accuracy = isset($_POST['location_accuracy']) ? (float)$_POST['location_accuracy'] : null;
    
    if (!$action || !in_array($action, ['checkin', 'checkout'])) {
        throw new Exception('Action không hợp lệ (checkin/checkout)');
    }
    
    // Photo and fingerprint are now OPTIONAL - support quick check-in without camera
    $has_photo = !empty($photo_data);
    $has_fingerprint = !empty($fingerprint_data);
    
    // If no photo and no fingerprint, this is a pure quick check-in (no biometrics)
    // In this case, skip face verification
    
    // Get current user ID from session (check multiple possible keys)
    $user_id = null;
    
    // Try from POST first (for test page)
    if (isset($_POST['user_id'])) {
        $user_id = (int)$_POST['user_id'];
    }
    
    // Try the admin system's primary session key first: $_SESSION['user1']['id']
    if (!$user_id && isset($_SESSION['user1']['id'])) {
        $user_id = (int)$_SESSION['user1']['id'];
    }
    
    // Fallback: try other common session keys
    if (!$user_id && isset($_SESSION['user_id'])) {
        $user_id = (int)$_SESSION['user_id'];
    } elseif (!$user_id && isset($_SESSION['id_nv'])) {
        $user_id = (int)$_SESSION['id_nv'];
    } elseif (!$user_id && isset($_SESSION['id'])) {
        $user_id = (int)$_SESSION['id'];
    } elseif (!$user_id && isset($_SESSION['nhan_vien_id'])) {
        $user_id = (int)$_SESSION['nhan_vien_id'];
    }
    
    if (!$user_id) {
        $response['debug']['session_keys'] = array_keys($_SESSION);
        $response['debug']['user1_keys'] = isset($_SESSION['user1']) ? array_keys($_SESSION['user1']) : 'not set';
        throw new Exception('Không tìm thấy user_id. Vui lòng đăng nhập lại.');
    }
    
    $response['debug']['user_id'] = $user_id;
    
    // Create temp directory for photos if not exists
    $temp_dir = dirname(__DIR__) . '/assets/temp_photos/';
    
    // Create directory if not exists
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0755, true);
    }
    
    // ===== BƯỚC 1: Xác minh khuôn mặt (CHỈ KHI có ảnh và fingerprint) =====
    $photo_relative_path = null;
    
    if ($has_photo && $has_fingerprint) {
        // Decode base64 image
        if (strpos($photo_data, 'data:image') === 0) {
            $photo_data = substr($photo_data, strpos($photo_data, ',') + 1);
        }
        
        $image_binary = base64_decode($photo_data, true);
        if ($image_binary === false) {
            throw new Exception('Dữ liệu ảnh không hợp lệ');
        }
        
        // Try Haar Cascade detection (optional, don't fail if it errors)
        try {
            $haar_result = cc_detect_face_from_base64($photo_data);
            if (!$haar_result['detected']) {
                $response['debug']['haar_status'] = 'NOT_DETECTED: ' . $haar_result['error'];
            } else {
                $response['debug']['haar_status'] = 'DETECTED: ' . $haar_result['face_count'] . ' face(s)';
                if ($haar_result['face_count'] > 1) {
                    throw new Exception('❌ Phát hiện nhiều hơn 1 khuôn mặt. Vui lòng chấm công một mình.');
                }
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Phát hiện nhiều') !== false) {
                throw $e; // Re-throw multiple-face error
            }
            $response['debug']['haar_error'] = $e->getMessage();
            $response['debug']['haar_status'] = 'ERROR_SKIPPED';
        }
        
        // MAIN: Verify fingerprint with registered face template
        try {
            $verification_result = cc_verify_face_strict($user_id, $fingerprint_data, $photo_data);
            $response['face_verification'] = $verification_result;
        } catch (Exception $e) {
            throw new Exception('❌ Xác minh khuôn mặt thất bại: ' . $e->getMessage());
        }
        
        // Save image AFTER successful verification
        $timestamp = date('Y-m-d_H-i-s');
        $photo_filename = "employee_{$user_id}_{$action}_{$timestamp}.jpg";
        $photo_file = $temp_dir . $photo_filename;
        $bytes_written = file_put_contents($photo_file, $image_binary);
        
        if ($bytes_written !== false) {
            $base_path = '';
            if (preg_match('/^(.*)\/Trang-admin/', $_SERVER['SCRIPT_NAME'], $matches)) {
                $base_path = $matches[1];
            }
            $photo_relative_path = $base_path . '/Trang-admin/assets/temp_photos/' . $photo_filename;
        }
        
        $response['debug']['face_verified'] = true;
        $response['debug']['auth_method'] = 'face';
    } else {
        // No biometrics - manual attendance (quick check-in)
        $response['debug']['face_verified'] = false;
        $response['debug']['auth_method'] = 'manual';
    }
    
    // Save attendance record to database
    $conn = pdo_get_connection();
    $today = date('Y-m-d');
    $now = date('H:i:s');
    
    // Get id_rap from taikhoan table (get employee's assigned cinema)
    $employee_sql = "SELECT id_rap FROM taikhoan WHERE id = :user_id LIMIT 1";
    $stmt_emp = $conn->prepare($employee_sql);
    $stmt_emp->execute([':user_id' => $user_id]);
    $employee = $stmt_emp->fetch(PDO::FETCH_ASSOC);
    
    if (!$employee || !$employee['id_rap']) {
        throw new Exception('Không tìm thấy thông tin rạp của nhân viên');
    }
    
    $rap_id = $employee['id_rap'];
    $response['debug']['rap_id_from_db'] = $rap_id;
    
    // Geofencing verification
    $rap_data = pdo_query_one("SELECT ten_rap, latitude, longitude FROM rap_chieu WHERE id = ?", $rap_id);
    if ($rap_data && $rap_data['latitude'] !== null && $rap_data['longitude'] !== null) {
        $rap_lat = (float)$rap_data['latitude'];
        $rap_lng = (float)$rap_data['longitude'];
        $rap_name = $rap_data['ten_rap'];
        
        if ($latitude === null || $longitude === null) {
            throw new Exception("❌ Không thể xác định vị trí. Vui lòng bật định vị GPS trên trình duyệt.");
        }
        
        $distance = cc_calculate_distance($latitude, $longitude, $rap_lat, $rap_lng);
        $allowed_radius = 50.0; // 50 mét
        
        if ($distance > $allowed_radius) {
            $distance_round = round($distance, 1);
            throw new Exception("❌ Bạn đang ở ngoài phạm vi rạp $rap_name (khoảng cách: {$distance_round}m). Vui lòng di chuyển đến gần rạp hơn (phạm vi cho phép: {$allowed_radius}m).");
        }
    }
    
    // Get current attendance record for today
    // Note: Query should only check id_nv, not id_rap, to be consistent with how cc_check_today_status works
    $check_sql = "SELECT id, gio_vao, gio_ra FROM cham_cong WHERE id_nv = :user_id AND ngay = :today ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($check_sql);
    $stmt->execute([':user_id' => $user_id, ':today' => $today]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($action === 'checkin') {
        if ($record && $record['gio_vao'] && $record['gio_ra'] === null) {
            // Already checked in (gio_ra IS NULL means not checked out yet)
            $response['success'] = false;
            $response['message'] = 'Bạn đã check-in hôm nay lúc ' . $record['gio_vao'] . '. Chưa thể check-in lại.';
        } else if ($record && $record['gio_vao'] && $record['gio_ra'] !== null) {
            // Already checked in AND checked out
            $response['success'] = false;
            $response['message'] = 'Bạn đã hoàn thành ca làm hôm nay (check-in: ' . $record['gio_vao'] . ', check-out: ' . $record['gio_ra'] . ').';
        } else {
            // Create new check-in record (gio_ra = NULL)
            $insert_sql = "INSERT INTO cham_cong (id_nv, id_rap, ngay, gio_vao, gio_ra, anh_vao, fingerprint_vao, latitude, longitude, location_accuracy, auth_method_in) 
                          VALUES (:user_id, :id_rap, :ngay, :gio_vao, NULL, :photo_path, :fingerprint, :latitude, :longitude, :location_accuracy, :auth_method)";
            $stmt = $conn->prepare($insert_sql);
            
            $stmt->execute([
                ':user_id'          => $user_id,
                ':id_rap'           => $rap_id,
                ':ngay'             => $today,
                ':gio_vao'          => $now,
                ':photo_path'       => $photo_relative_path,
                ':fingerprint'      => $fingerprint_data,
                ':latitude'         => $latitude,
                ':longitude'        => $longitude,
                ':location_accuracy'=> $location_accuracy,
                ':auth_method'      => ($has_photo && $has_fingerprint) ? 'face' : 'manual',
            ]);
            $response['success'] = true;
            $response['message'] = 'Check-in thành công lúc ' . date('H:i');
        }
    } else if ($action === 'checkout') {
        if (!$record) {
            throw new Exception('Bạn chưa check-in hôm nay');
        }
        
        if ($record['gio_ra'] !== null) {
            throw new Exception('Bạn đã check-out rồi lúc ' . $record['gio_ra']);
        }
        
        // Validate: must be at least 30 minutes after check-in
        $diff_minutes = (strtotime($now) - strtotime($record['gio_vao'])) / 60;
        if ($diff_minutes < 30) {
            throw new Exception('Phải chờ ít nhất 30 phút sau check-in mới có thể check-out');
        }
        
        // Update check-out time
        $update_sql = "UPDATE cham_cong SET gio_ra = :gio_ra, anh_ra = :photo_path, fingerprint_ra = :fingerprint, auth_method_out = :auth_method WHERE id = :id";
        $stmt = $conn->prepare($update_sql);
        $stmt->execute([
            ':gio_ra'       => $now,
            ':photo_path'   => $photo_relative_path,
            ':fingerprint'  => $fingerprint_data,
            ':auth_method'  => ($has_photo && $has_fingerprint) ? 'face' : 'manual',
            ':id'           => $record['id']
        ]);
        $total_hours = round($diff_minutes / 60, 1);
        $response['success'] = true;
        $response['message'] = 'Check-out thành công lúc ' . date('H:i') . '. Tổng: ' . $total_hours . ' giờ';
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    http_response_code(400);
}

echo json_encode($response);
exit;
?>
