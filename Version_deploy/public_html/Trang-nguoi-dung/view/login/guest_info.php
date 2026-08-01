<!-- Form nhập thông tin khách vãng lai -->
<style>
* {
    box-sizing: border-box;
}

.guest-booking-container {
    max-width: 650px;
    margin: 60px auto;
    padding: 0 15px;
}

.guest-info-card {
    background: white;
    border-radius: 25px;
    box-shadow: 0 10px 50px rgba(0,0,0,0.12);
    overflow: hidden;
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.guest-card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 25px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.guest-card-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: -50px;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.guest-card-header::after {
    content: '';
    position: absolute;
    bottom: -30px;
    left: -50px;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}

.guest-card-header h2 {
    margin: 0 0 10px 0;
    font-size: 2.2rem;
    font-weight: 700;
    position: relative;
    z-index: 1;
    color: white !important;
}

.guest-card-header h2 i {
    margin-right: 12px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.guest-card-header p {
    margin: 0;
    font-size: 1.05rem;
    opacity: 0.95;
    position: relative;
    z-index: 1;
    color: white !important;
}

.guest-card-body {
    padding: 45px 35px;
}

.guest-notice {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe5a1 100%);
    border: 1px solid #ffc107;
    border-radius: 15px;
    padding: 18px;
    margin-bottom: 30px;
    display: flex;
    align-items: flex-start;
    gap: 15px;
    animation: fadeIn 0.8s ease-out 0.2s both;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.guest-notice-icon {
    color: #ff9800;
    font-size: 1.8rem;
    flex-shrink: 0;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.guest-notice-text {
    flex: 1;
}

.guest-notice-text strong {
    display: block;
    color: #856404;
    margin-bottom: 5px;
    font-size: 1.05rem;
}

.guest-notice-text p {
    margin: 0;
    color: #856404;
    font-size: 0.9rem;
    line-height: 1.6;
}

.form-group-guest {
    margin-bottom: 25px;
    animation: fadeIn 0.8s ease-out both;
}

.form-group-guest:nth-child(1) { animation-delay: 0.3s; }
.form-group-guest:nth-child(2) { animation-delay: 0.4s; }
.form-group-guest:nth-child(3) { animation-delay: 0.5s; }

.form-group-guest label {
    display: block;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-group-guest label .required {
    color: #e74c3c;
}

.guest-input-wrapper {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
}

.guest-input-icon {
    position: absolute !important;
    left: 15px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #667eea !important;
    font-size: 1.1rem !important;
    pointer-events: none !important;
    z-index: 10 !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1 !important;
}

.form-control-guest {
    width: 100% !important;
    padding: 13px 15px 13px 45px !important;
    border: 2px solid #e9ecef !important;
    border-radius: 12px !important;
    font-size: 1rem !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-sizing: border-box !important;
    background: #f8f9fa !important;
    color: #333 !important;
    height: auto !important;
}

.form-control-guest:hover {
    border-color: #ddd !important;
    background: white !important;
}

.form-control-guest:focus {
    outline: none !important;
    border-color: #667eea !important;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15) !important;
    background: white !important;
}

.error-message {
    color: #e74c3c;
    font-size: 0.85rem;
    margin-top: 6px;
    display: none;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.guest-actions {
    display: flex;
    gap: 15px;
    margin-top: 35px;
    animation: fadeIn 0.8s ease-out 0.6s both;
}

.btn-guest {
    flex: 1;
    padding: 15px;
    border: none;
    border-radius: 12px;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-align: center;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
}

.btn-guest::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn-guest:active::before {
    width: 300px;
    height: 300px;
}

.btn-continue {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
    z-index: 1;
}

.btn-continue:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.45);
    color: white !important;
}

.btn-continue:active {
    transform: translateY(-1px);
}

.btn-continue.loading {
    opacity: 0.8;
    pointer-events: none;
}

.btn-continue.loading span {
    display: inline-block;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.btn-back {
    background: white;
    color: #6c757d;
    border: 2px solid #dee2e6;
}

.btn-back:hover {
    background: #f8f9fa;
    border-color: #adb5bd;
    text-decoration: none;
    transform: translateY(-2px);
    color: #6c757d !important;
}

.btn-back:active {
    transform: translateY(0);
}

.login-link-section {
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px solid #f0f0f0;
    text-align: center;
    animation: fadeIn 0.8s ease-out 0.7s both;
}

.login-link-section p {
    color: #6c757d;
    margin: 0 0 12px 0;
    font-size: 0.98rem;
}

.login-link-section a {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.login-link-section a:hover {
    color: #5568d3;
    text-decoration: underline;
}

@media (max-width: 576px) {
    .guest-booking-container {
        margin: 40px auto;
    }
    
    .guest-card-header {
        padding: 30px 20px;
    }
    
    .guest-card-header h2 {
        font-size: 1.8rem;
    }
    
    .guest-card-body {
        padding: 30px 20px;
    }
    
    .guest-actions {
        flex-direction: column-reverse;
        gap: 12px;
    }
    
    .btn-guest {
        padding: 14px;
    }
}
</style>

<div class="guest-booking-container">
    <div class="guest-info-card">
        <div class="guest-card-header">
            <h2><i class="fa fa-user-circle"></i> <?= __('Thông tin khách hàng vãng lai') ?></h2>
            <p><?= __('Nhập thông tin để tiếp tục đặt vé mà không cần đăng nhập') ?></p>
        </div>
        
        <div class="guest-card-body">
            <?php if (isset($thongbao['guest_error']) && $thongbao['guest_error'] != ''): ?>
            <p id="server_error" style="color: red; margin-bottom: 20px; font-weight: 500; text-align: center;">
                <?php echo __($thongbao['guest_error']); ?>
            </p>
            <?php endif; ?>
            
            <div class="guest-notice">
                <div class="guest-notice-icon">
                    <i class="fa fa-lightbulb-o"></i>
                </div>
                <div class="guest-notice-text">
                    <strong>✨ <?= __('Đặt vé không cần đăng ký tài khoản') ?></strong>
                    <p><?= __('Thông tin của bạn sẽ được sử dụng để gửi xác nhận vé qua Email. Bạn có thể đăng ký tài khoản sau để quản lý vé dễ dàng hơn.') ?></p>
                </div>
            </div>
            
            <form action="index.php?act=datve2" method="POST" id="guestForm">
                <div class="form-group-guest">
                    <label for="guest_name">
                        <i class="fa fa-user" style="color: #667eea;"></i>
                        <?= __('Họ và tên') ?><span class="required">*</span>
                    </label>
                    <div class="guest-input-wrapper">
                        <i class="guest-input-icon fa fa-user"></i>
                        <input type="text" 
                               class="form-control-guest" 
                               id="guest_name" 
                               name="guest_name" 
                               placeholder="<?= __('Nhập họ và tên của bạn') ?>"
                               required>
                    </div>
                    <span class="error-message" id="error_name">❌ <?= __('Vui lòng nhập họ và tên (ít nhất 2 ký tự)') ?></span>
                </div>
                
                <div class="form-group-guest">
                    <label for="guest_phone">
                        <i class="fa fa-phone" style="color: #667eea;"></i>
                        <?= __('Số điện thoại') ?><span class="required">*</span>
                    </label>
                    <div class="guest-input-wrapper">
                        <i class="guest-input-icon fa fa-phone"></i>
                        <input type="tel" 
                               class="form-control-guest" 
                               id="guest_phone" 
                               name="guest_phone" 
                               placeholder="VD: 0912345678"
                               required>
                    </div>
                    <span class="error-message" id="error_phone">❌ <?= __('Số điện thoại không hợp lệ (10 số bắt đầu từ 0)') ?></span>
                </div>
                
                <div class="form-group-guest">
                    <label for="guest_email">
                        <i class="fa fa-envelope" style="color: #667eea;"></i>
                        <?= __('Email') ?><span class="required">*</span>
                    </label>
                    <div class="guest-input-wrapper">
                        <i class="guest-input-icon fa fa-envelope"></i>
                        <input type="email" 
                               class="form-control-guest" 
                               id="guest_email" 
                               name="guest_email" 
                               placeholder="<?= __('Nhập email của bạn') ?>"
                               required>
                    </div>
                    <span class="error-message" id="error_email">❌ <?= __('Email không tồn tại, vui lòng nhập lại') ?></span>
                </div>
                
                <div class="guest-actions">
                    <a href="javascript:history.back()" class="btn-guest btn-back">
                        <i class="fa fa-arrow-left"></i>
                        <?= __('Quay lại') ?>
                    </a>
                    <button type="submit" class="btn-guest btn-continue" id="submitBtn">
                        <i class="fa fa-arrow-right"></i>
                        <span><?= __('Tiếp tục đặt vé') ?></span>
                    </button>
                </div>
            </form>
            
            <div class="login-link-section">
                <p>👤 <?= __('Đã có tài khoản?') ?></p>
                <a href="index.php?act=dangnhap">
                    <i class="fa fa-sign-in"></i> <?= __('Đăng nhập ngay') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let emailValid = false;
let checkingEmail = false;
let emailTimeout;

const emailInput = document.getElementById('guest_email');
const errorEmail = document.getElementById('error_email');
const errorName = document.getElementById('error_name');
const errorPhone = document.getElementById('error_phone');

function checkEmailExistGuest(emailVal, callback) {
    if (!emailVal) {
        emailValid = false;
        if (callback) callback(false);
        return;
    }
    
    // Validate định dạng cơ bản có đuôi @gmail.com
    const emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/i;
    if (!emailPattern.test(emailVal)) {
        emailValid = false;
        errorEmail.textContent = '❌ <?= __('Email không tồn tại, vui lòng nhập lại') ?>';
        errorEmail.style.color = 'red';
        errorEmail.style.display = 'block';
        if (callback) callback(false);
        return;
    }

    checkingEmail = true;
    errorEmail.textContent = 'ℹ️ <?= __('Đang kiểm tra email...') ?>';
    errorEmail.style.color = '#666';
    errorEmail.style.display = 'block';

    $.getJSON('index.php?act=kiemtra_email_ajax&email=' + encodeURIComponent(emailVal), function(res) {
        checkingEmail = false;
        if (res && res.valid) {
            emailValid = true;
            errorEmail.style.display = 'none';
            if (callback) callback(true);
        } else {
            emailValid = false;
            errorEmail.textContent = '❌ <?= __('Email không tồn tại, vui lòng nhập lại') ?>';
            errorEmail.style.color = 'red';
            errorEmail.style.display = 'block';
            if (callback) callback(false);
        }
    }).fail(function() {
        checkingEmail = false;
        emailValid = false;
        errorEmail.textContent = '❌ <?= __('Email không tồn tại, vui lòng nhập lại') ?>';
        errorEmail.style.color = 'red';
        errorEmail.style.display = 'block';
        if (callback) callback(false);
    });
}

emailInput.addEventListener('blur', function() {
    const emailVal = this.value.trim();
    if (emailVal !== '') {
        checkEmailExistGuest(emailVal);
    }
});

emailInput.addEventListener('input', function() {
    emailValid = false;
    errorEmail.style.display = 'none';
    
    clearTimeout(emailTimeout);
    const emailVal = this.value.trim();
    if (emailVal !== '') {
        emailTimeout = setTimeout(function() {
            checkEmailExistGuest(emailVal);
        }, 1000);
    }
});

document.getElementById('guestForm').addEventListener('submit', function(e) {
    const nameInput = document.getElementById('guest_name');
    const phoneInput = document.getElementById('guest_phone');
    const emailInput = document.getElementById('guest_email');
    
    let isValid = true;
    
    // Validate tên
    if (nameInput.value.trim().length < 2) {
        isValid = false;
        errorName.style.display = 'block';
    } else {
        errorName.style.display = 'none';
    }
    
    // Validate số điện thoại (10 chữ số bắt đầu bằng 0)
    const phonePattern = /^0[0-9]{9}$/;
    if (!phonePattern.test(phoneInput.value.trim())) {
        isValid = false;
        errorPhone.style.display = 'block';
    } else {
        errorPhone.style.display = 'none';
    }
    
    if (!isValid) {
        e.preventDefault();
        return false;
    }
    
    // Nếu kiểm tra email chưa có kết quả thành công
    if (!emailValid) {
        e.preventDefault();
        
        // Nếu đang trong quá trình check, chỉ cần chờ
        if (checkingEmail) {
            return false;
        }
        
        // Gọi check email
        checkEmailExistGuest(emailInput.value.trim(), function(isValidEmail) {
            if (isValidEmail) {
                emailValid = true;
                // Submit form lại
                showLoadingState();
                document.getElementById('guestForm').submit();
            }
        });
    } else {
        showLoadingState();
    }
    
    function showLoadingState() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.classList.add('loading');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner"></i> <span><?= __('Đang xử lý...') ?></span>';
    }
});

// Clear error on input for name and phone
document.querySelectorAll('#guest_name, #guest_phone').forEach(input => {
    input.addEventListener('input', function() {
        const errorId = 'error_' + this.id.replace('guest_', '');
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    });
});
</script>

<div class="clearfix"></div>
