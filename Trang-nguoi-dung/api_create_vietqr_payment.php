<?php
/**
 * VietQR Payment API Endpoint
 * Generate banking QR code for customers to transfer
 * POST /api_create_vietqr_payment.php
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// ====================================================
// BANK ACCOUNT CONFIGURATION
// ====================================================

// CinePass Theater Bank Account
define('BANK_ACCOUNT_NAME', 'CINEPASS THEATER');
define('BANK_ACCOUNT_NUMBER', '1234567890'); // ← Thay bằng số tài khoản thực
define('BANK_CODE', 'VIETCOMBANK'); // Mã ngân hàng VietCom
define('BANK_NAME', 'Ngân hàng TMCP Ngoài Thương Việt Nam');

// VietQR API
define('VIETQR_API', 'https://api.vietqr.io/v2/generate');

// ====================================================
// VALIDATE REQUEST
// ====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 1, 'message' => 'Method not allowed']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    $data = $_POST;
}

// ====================================================
// LẤY DỮ LIỆU
// ====================================================

$amount = isset($data['amount']) ? (int)$data['amount'] : 
          (isset($_SESSION['tong']['gia_sau_giam']) && $_SESSION['tong']['gia_sau_giam'] > 0 ? 
           (int)$_SESSION['tong']['gia_sau_giam'] : 0);

$description = $data['description'] ?? 'Ve phim CinePass';

// ====================================================
// VALIDATE
// ====================================================

if ($amount < 10000) {
    http_response_code(400);
    echo json_encode([
        'error' => 1,
        'message' => 'Amount must be at least 10,000 VND'
    ]);
    exit;
}

// Tạo Order Code
$orderId = 'CINEMA_' . time() . '_' . rand(1000, 9999);

// ====================================================
// GENERATE VIETQR QR CODE
// ====================================================

// Ánh xạ mã BIN ngân hàng tự động dựa trên BANK_CODE
$bank_bin_map = [
    'VIETCOMBANK' => '970436',
    'MBBANK' => '970422',
    'VIETINBANK' => '970415',
    'BIDV' => '970418',
    'TECHCOMBANK' => '970407',
    'ACB' => '970416',
    'TPBANK' => '970423',
    'VPBANK' => '970432',
    'SACOMBANK' => '970403'
];
$acqId = $bank_bin_map[strtoupper(BANK_CODE)] ?? '970436'; // Mặc định Vietcombank nếu không khớp

// Prepare VietQR payload for POST request
$payload = [
    'accountNo' => BANK_ACCOUNT_NUMBER,
    'accountName' => BANK_ACCOUNT_NAME,
    'acqId' => $acqId,
    'amount' => $amount,
    'addInfo' => $description . ' - ' . $orderId,
    'format' => 'text',
    'template' => 'compact',
];

// Call VietQR API using POST request
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.vietqr.io/v2/generate',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json'
    ],
    CURLOPT_TIMEOUT => 10,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log
file_put_contents(__DIR__ . '/view/momo/vietqr_debug.log',
    date('Y-m-d H:i:s') . " Order: $orderId | Amount: $amount | HTTP: $httpCode | Error: $curlError\n",
    FILE_APPEND
);

// Parse response
$result = json_decode($response, true);

// ====================================================
// HANDLE RESPONSE
// ====================================================

if ($httpCode === 200 && isset($result['data']['qr'])) {
    // Success - VietQR returned QR code base64
    http_response_code(200);
    echo json_encode([
        'error' => 0,
        'message' => 'SUCCESS',
        'data' => [
            'orderId' => $orderId,
            'amount' => $amount,
            'bankName' => BANK_NAME,
            'bankCode' => BANK_CODE,
            'accountNumber' => BANK_ACCOUNT_NUMBER,
            'accountName' => BANK_ACCOUNT_NAME,
            'description' => $description . ' - ' . $orderId,
            'qrCode' => $result['data']['qr'], // Base64 QR code
            'qrUrl' => isset($result['data']['qrUrl']) ? $result['data']['qrUrl'] : null,
        ]
    ]);
} else {
    // Fallback: Nếu API chính thống lỗi, trả về link ảnh trực tiếp từ img.vietqr.io để luôn hiển thị mã QR thành công
    $quick_qr_url = "https://img.vietqr.io/image/" . $acqId . "-" . BANK_ACCOUNT_NUMBER . "-compact.png?amount=" . $amount . "&addInfo=" . urlencode($description . ' - ' . $orderId) . "&accountName=" . urlencode(BANK_ACCOUNT_NAME);
    
    file_put_contents(__DIR__ . '/view/momo/vietqr_debug.log',
        date('Y-m-d H:i:s') . " [FALLBACK] Generating direct image URL: $quick_qr_url\n",
        FILE_APPEND
    );
    
    http_response_code(200);
    echo json_encode([
        'error' => 0,
        'message' => 'SUCCESS',
        'data' => [
            'orderId' => $orderId,
            'amount' => $amount,
            'bankName' => BANK_NAME,
            'bankCode' => BANK_CODE,
            'accountNumber' => BANK_ACCOUNT_NUMBER,
            'accountName' => BANK_ACCOUNT_NAME,
            'description' => $description . ' - ' . $orderId,
            'qrCode' => $quick_qr_url, // Link image trực tiếp
            'manualTransfer' => true,
        ]
    ]);
}
?>
