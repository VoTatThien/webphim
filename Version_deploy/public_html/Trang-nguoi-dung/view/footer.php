<footer class="footer-wrapper">
    <?php
    // Lấy cấu hình website trực tiếp từ Database (siêu nhanh, không cURL)
    if (!isset($web_config)) {
        $web_config = function_exists('get_website_config') ? get_website_config() : [
            'ten_website' => 'Galaxy Studio',
            'logo' => 'imgavt/Galaxy_Studio_2003_(Wordmark)_(Grey).webp',
            'dia_chi' => '',
            'so_dien_thoai' => '',
            'email' => '',
            'facebook' => '',
            'instagram' => '',
            'youtube' => '',
            'mo_ta' => 'Nền tảng mua vé xem phim hàng đầu'
        ];
    }
    ?>
    <section class="container">
        <div class="col-xs-4 col-md-2 footer-nav">
            <ul class="nav-link">
                <li><a href="index.php" class="nav-link__item">Trang chủ</a></li>
                <li><a href="index.php?act=dsphim1&sotrang=1" class="nav-link__item">Phim</a></li>
                <li><a href="index.php?act=rapchieu" class="nav-link__item">Rạp chiếu</a></li>
                <li><a href="index.php?act=khuyenmai" class="nav-link__item">Khuyến mãi</a></li>
            </ul>
        </div>
        <div class="col-xs-4 col-md-2 footer-nav">
            <ul class="nav-link">
                <li><a href="index.php?act=tintuc" class="nav-link__item">Tin tức</a></li>
                <li><a href="index.php?act=lienhe" class="nav-link__item">Liên hệ</a></li>
                <li><a href="#" class="nav-link__item">Điều khoản sử dụng</a></li>
                <li><a href="#" class="nav-link__item">Chính sách bảo mật</a></li>
            </ul>
        </div>
        <div class="col-xs-4 col-md-2 footer-nav">
            <ul class="nav-link">
                <?php if (!empty($web_config['so_dien_thoai'])): ?>
                    <li><a href="tel:<?= htmlspecialchars($web_config['so_dien_thoai']) ?>" class="nav-link__item">
                        <i class="fa fa-phone"></i> <?= htmlspecialchars($web_config['so_dien_thoai']) ?>
                    </a></li>
                <?php endif; ?>
                <?php if (!empty($web_config['email'])): ?>
                    <li><a href="mailto:<?= htmlspecialchars($web_config['email']) ?>" class="nav-link__item">
                        <i class="fa fa-envelope"></i> <?= htmlspecialchars($web_config['email']) ?>
                    </a></li>
                <?php endif; ?>
                <?php if (!empty($web_config['dia_chi'])): ?>
                    <li class="nav-link__item">
                        <i class="fa fa-map-marker"></i> <?= htmlspecialchars($web_config['dia_chi']) ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="footer-info">
                <p class="heading-special--small"><?= htmlspecialchars($web_config['ten_website']) ?><br><span class="title-edition">trên mạng xã hội</span></p>

                <div class="social">
                    <?php if (!empty($web_config['facebook'])): ?>
                        <a href='<?= htmlspecialchars($web_config['facebook']) ?>' target="_blank" class="social__variant fa fa-facebook" title="Facebook"></a>
                    <?php endif; ?>
                    <?php if (!empty($web_config['instagram'])): ?>
                        <a href='<?= htmlspecialchars($web_config['instagram']) ?>' target="_blank" class="social__variant fa fa-instagram" title="Instagram"></a>
                    <?php endif; ?>
                    <?php if (!empty($web_config['youtube'])): ?>
                        <a href='<?= htmlspecialchars($web_config['youtube']) ?>' target="_blank" class="social__variant fa fa-youtube" title="YouTube"></a>
                    <?php endif; ?>
                </div>

                <div class="clearfix"></div>
                <p class="copy">&copy; <?= htmlspecialchars($web_config['ten_website']) ?>, 2025. Đã được cấu hình từ Admin. <?php if (!empty($web_config['mo_ta'])): ?>| <?= htmlspecialchars($web_config['mo_ta']) ?><?php endif; ?></p>
            </div>
        </div>
    </section>
</footer>

<!-- ==============================================
     CINEPASS AI CHATBOT WIDGET
     ============================================== -->
<style>
.cp-chatbot-toggle {
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 60px;
    height: 60px;
    background: #ffd564;
    color: #1c181c;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(255, 213, 100, 0.4);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    outline: none;
}
.cp-chatbot-toggle:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: 0 6px 25px rgba(255, 213, 100, 0.5);
    background: #ffe08d;
}
.cp-chatbot-toggle:active {
    transform: scale(0.95);
}
.cp-toggle-badge {
    position: absolute;
    top: -3px;
    right: -3px;
    background: #fe505a;
    color: white;
    font-size: 11px;
    font-weight: 700;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #1c181c;
    box-shadow: 0 2px 10px rgba(254, 80, 90, 0.5);
    animation: cp-pulse 2s infinite;
}
@keyframes cp-pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.15); }
    100% { transform: scale(1); }
}

.cp-chatbot-container {
    position: fixed;
    bottom: 95px;
    right: 25px;
    width: 370px;
    height: 520px;
    background: rgba(20, 17, 20, 0.96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 213, 100, 0.2);
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
    z-index: 999999;
    display: none;
    flex-direction: column;
    overflow: hidden;
    transition: all 0.3s ease;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
.cp-chatbot-header {
    background: #1c181c;
    border-bottom: 1px solid rgba(255, 213, 100, 0.1);
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.cp-avatar-status {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: rgba(255, 213, 100, 0.1);
    border-radius: 50%;
}
.cp-status-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 10px;
    height: 10px;
    background: #28a745;
    border-radius: 50%;
    border: 2px solid #1c181c;
}
.cp-chatbot-close {
    background: none;
    border: none;
    color: #a59b9f;
    font-size: 24px;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    transition: color 0.2s;
}
.cp-chatbot-close:hover {
    color: #fe505a;
}
.cp-chatbot-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 15px;
    background: rgba(10, 8, 10, 0.3);
}
.cp-message {
    max-width: 80%;
    padding: 12px 16px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
}
.cp-message-user {
    background: #ffd564;
    color: #1c181c;
    border-radius: 16px 16px 0 16px;
    align-self: flex-end;
    box-shadow: 0 2px 10px rgba(255, 213, 100, 0.1);
}
.cp-message-ai {
    background: #1c181c;
    color: #e5e0e3;
    border: 1px solid #363033;
    border-radius: 16px 16px 16px 0;
    align-self: flex-start;
}
.cp-message-ai ul, .cp-message-ai ol {
    margin: 5px 0;
    padding-left: 20px;
}
.cp-message-ai li {
    margin-bottom: 4px;
}
.cp-message-ai strong {
    color: #ffd564;
}

.cp-chatbot-chips {
    padding: 10px 15px;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    white-space: nowrap;
    border-top: 1px solid rgba(255, 213, 100, 0.05);
    background: rgba(10, 8, 10, 0.4);
}
.cp-chatbot-chips::-webkit-scrollbar {
    height: 4px;
}
.cp-chatbot-chips::-webkit-scrollbar-thumb {
    background: rgba(255, 213, 100, 0.2);
    border-radius: 2px;
}
.cp-chip {
    background: rgba(54, 48, 51, 0.5);
    color: #dcd7da;
    border: 1px solid #363033;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.cp-chip:hover {
    background: rgba(255, 213, 100, 0.1);
    color: #ffd564;
    border-color: #ffd564;
}

.cp-chatbot-input-area {
    padding: 15px;
    background: #1c181c;
    border-top: 1px solid rgba(255, 213, 100, 0.1);
    display: flex;
    gap: 10px;
    align-items: center;
}
.cp-chatbot-input-area input {
    flex: 1;
    background: #232023;
    border: 1px solid #363033;
    color: white;
    padding: 10px 15px;
    border-radius: 20px;
    outline: none;
    font-size: 14px;
    transition: border-color 0.2s;
}
.cp-chatbot-input-area input:focus {
    border-color: #ffd564;
}
.cp-chatbot-send {
    background: #ffd564;
    color: #1c181c;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.cp-chatbot-send:hover {
    background: #ffe08d;
    transform: scale(1.05);
}
.cp-chatbot-send:active {
    transform: scale(0.95);
}

/* Scrollbar for chat messages */
.cp-chatbot-messages::-webkit-scrollbar {
    width: 6px;
}
.cp-chatbot-messages::-webkit-scrollbar-track {
    background: transparent;
}
.cp-chatbot-messages::-webkit-scrollbar-thumb {
    background: rgba(255, 213, 100, 0.2);
    border-radius: 3px;
}
.cp-chatbot-messages::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 213, 100, 0.4);
}

/* Typing Indicator Animation */
.cp-typing-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 12px;
}
.cp-dot {
    width: 6px;
    height: 6px;
    background: #a59b9f;
    border-radius: 50%;
    animation: cp-bounce 1.4s infinite ease-in-out both;
}
.cp-dot:nth-child(1) { animation-delay: -0.32s; }
.cp-dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes cp-bounce {
    0%, 80%, 100% { transform: scale(0); }
    40% { transform: scale(1); }
}
</style>

<div class="cp-chatbot-container" id="chatbot-window">
    <div class="cp-chatbot-header">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div class="cp-avatar-status">
                <i class="fa fa-android" style="font-size: 20px; color: #ffd564;"></i>
                <span class="cp-status-dot"></span>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 15px; color: #fff; line-height: 1.2;">Cinepass AI</div>
                <div style="font-size: 11px; color: #28a745; display: flex; align-items: center; gap: 3px;">
                    Online
                </div>
            </div>
        </div>
        <button class="cp-chatbot-close" onclick="toggleChatbot()">&times;</button>
    </div>
    
    <div class="cp-chatbot-messages" id="chatbot-msg-box">
        <!-- Messages will load dynamically -->
    </div>
    
    <div class="cp-chatbot-chips">
        <span class="cp-chip" onclick="sendQuickQuery('🎬 Phim đang chiếu')">🎬 Phim đang chiếu</span>
        <span class="cp-chip" onclick="sendQuickQuery('🎁 Khuyến mãi mới')">🎁 Khuyến mãi mới</span>
        <span class="cp-chip" onclick="sendQuickQuery('💰 Giá vé & Rạp')">💰 Giá vé & Rạp</span>
    </div>
    
    <div class="cp-chatbot-input-area">
        <input type="text" id="chatbot-txt-input" placeholder="Hỏi tôi về phim, suất chiếu, ưu đãi..." onkeydown="if(event.key==='Enter') sendChat()" />
        <button class="cp-chatbot-send" onclick="sendChat()"><i class="fa fa-paper-plane"></i></button>
    </div>
</div>

<button class="cp-chatbot-toggle" id="chatbot-bubble-btn" onclick="toggleChatbot()">
    <i class="fa fa-comments" style="font-size: 24px;"></i>
    <span class="cp-toggle-badge" id="chatbot-bubble-badge">1</span>
</button>

<script>
let cpChatHistory = [];

document.addEventListener("DOMContentLoaded", function() {
    // Tải lịch sử chat từ sessionStorage nếu có
    const cachedHistory = sessionStorage.getItem("cp_chat_history");
    const msgBox = document.getElementById("chatbot-msg-box");
    
    if (cachedHistory) {
        cpChatHistory = JSON.parse(cachedHistory);
        renderHistory();
        document.getElementById("chatbot-bubble-badge").style.display = "none";
    } else {
        // Tin nhắn chào mừng mặc định
        appendMessage("ai", "Xin chào! Tôi là Trợ lý ảo AI của **Cinepass**. Tôi có thể hỗ trợ quý khách tìm thông tin phim đang chiếu, khuyến mãi, địa chỉ rạp và lịch chiếu suất chiếu hôm nay. Quý khách cần hỏi gì ạ?");
    }
});

function toggleChatbot() {
    const windowEl = document.getElementById("chatbot-window");
    const isVisible = windowEl.style.display === "flex";
    windowEl.style.display = isVisible ? "none" : "flex";
    
    if (!isVisible) {
        document.getElementById("chatbot-bubble-badge").style.display = "none";
        document.getElementById("chatbot-txt-input").focus();
        // Cuộn xuống cuối
        const msgBox = document.getElementById("chatbot-msg-box");
        msgBox.scrollTop = msgBox.scrollHeight;
    }
}

function appendMessage(role, text) {
    const msgBox = document.getElementById("chatbot-msg-box");
    const msgDiv = document.createElement("div");
    msgDiv.className = `cp-message cp-message-${role}`;
    msgDiv.innerHTML = parseMarkdown(text);
    msgBox.appendChild(msgDiv);
    msgBox.scrollTop = msgBox.scrollHeight;
    
    // Lưu vào lịch sử chat
    cpChatHistory.push({ role: role, text: text });
    sessionStorage.setItem("cp_chat_history", JSON.stringify(cpChatHistory));
}

function renderHistory() {
    const msgBox = document.getElementById("chatbot-msg-box");
    msgBox.innerHTML = "";
    cpChatHistory.forEach(msg => {
        const msgDiv = document.createElement("div");
        msgDiv.className = `cp-message cp-message-${msg.role}`;
        msgDiv.innerHTML = parseMarkdown(msg.text);
        msgBox.appendChild(msgDiv);
    });
    msgBox.scrollTop = msgBox.scrollHeight;
}

function parseMarkdown(text) {
    // Thay thế ký tự đặc biệt phòng tránh XSS nhưng giữ cấu trúc cơ bản
    let safeText = text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");

    // Bold (**text** -> <strong>text</strong>)
    safeText = safeText.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");
    
    // Lists (- text -> <li>text</li>)
    safeText = safeText.replace(/^\s*[-*]\s+(.*?)$/gm, "<li>$1</li>");
    // Bọc các thẻ li liên tiếp vào ul
    safeText = safeText.replace(/(<li>.*?<\/li>)+/g, "<ul>$&</ul>");
    
    // Line breaks (\n -> <br>)
    safeText = safeText.replace(/\n/g, "<br>");
    
    return safeText;
}

function showTypingIndicator() {
    const msgBox = document.getElementById("chatbot-msg-box");
    const indicatorDiv = document.createElement("div");
    indicatorDiv.className = "cp-message cp-message-ai";
    indicatorDiv.id = "cp-typing-indicator";
    indicatorDiv.innerHTML = `
        <div class="cp-typing-indicator">
            <span class="cp-dot"></span>
            <span class="cp-dot"></span>
            <span class="cp-dot"></span>
        </div>
    `;
    msgBox.appendChild(indicatorDiv);
    msgBox.scrollTop = msgBox.scrollHeight;
}

function removeTypingIndicator() {
    const indicator = document.getElementById("cp-typing-indicator");
    if (indicator) {
        indicator.parentNode.removeChild(indicator);
    }
}

function sendChat() {
    const inputEl = document.getElementById("chatbot-txt-input");
    const message = inputEl.value.trim();
    if (!message) return;
    
    // Thêm tin nhắn của User
    appendMessage("user", message);
    inputEl.value = "";
    
    // Hiển thị gõ loading
    showTypingIndicator();
    
    // Call API Backend
    const baseDir = window.location.pathname.includes('/webphim_hung/') ? '/webphim_hung/' : '/';
    const endpoint = baseDir + 'Trang-nguoi-dung/api_chatbot.php';
    
    // Chỉ lấy 6 tin nhắn gần nhất để làm ngữ cảnh tránh quá tải token
    const contextHistory = cpChatHistory.slice(-6);
    
    fetch(endpoint, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            message: message,
            history: contextHistory.filter(m => m.text !== message) // Loại bỏ tin nhắn hiện tại vừa append
        })
    })
    .then(res => res.json())
    .then(data => {
        removeTypingIndicator();
        if (data && data.reply) {
            appendMessage("ai", data.reply);
        } else {
            appendMessage("ai", "Xin lỗi quý khách, tôi gặp sự cố kết nối máy chủ một chút. Quý khách có thể hỏi lại được không ạ?");
        }
    })
    .catch(err => {
        removeTypingIndicator();
        appendMessage("ai", "Xin lỗi quý khách, hệ thống mạng đang gặp trục trặc. Quý khách có thể vui lòng thử lại sau.");
    });
}

// Chức năng gửi tin từ gợi ý
function sendQuickQuery(query) {
    const inputEl = document.getElementById("chatbot-txt-input");
    inputEl.value = query;
    sendChat();
}
</script>
</div>

<!-- open/close -->
<div class="overlay overlay-hugeinc">

    <section class="container">

        <div class="col-sm-4 col-sm-offset-4">
            <button type="button" class="overlay-close">Close</button>
            <form id="login-form" class="login" method='get' novalidate=''>
                <p class="login__title">sign in <br><span class="login-edition">welcome to A.Movie</span></p>

                <div class="social social--colored">
                    <a href='#' class="social__variant fa fa-facebook"></a>
                    <a href='#' class="social__variant fa fa-twitter"></a>
                    <a href='#' class="social__variant fa fa-tumblr"></a>
                </div>

                <p class="login__tracker">or</p>

                <div class="field-wrap">
                    <input type='email' placeholder='Email' name='user-email' class="login__input">
                    <input type='password' placeholder='Password' name='user-password' class="login__input">

                    <input type='checkbox' id='#informed' class='login__check styled'>
                    <label for='#informed' class='login__check-info'>remember me</label>
                </div>

                <div class="login__control">
                    <button type='submit' class="btn btn-md btn--warning btn--wider">sign in</button>
                    <a href="#" class="login__tracker form__tracker">Forgot password?</a>
                </div>
            </form>
        </div>

    </section>
</div>

<!-- JavaScript-->
<!-- jQuery 1.9.1-->
<script src="ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>  
<script>window.jQuery || document.write('<script src="js/external/jquery-1.10.1.min.js"><\/script>')</script>
<!-- Migrate -->
<script src="js/external/jquery-migrate-1.2.1.min.js"></script>
<!-- jQuery UI -->
<script src="code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<!-- Bootstrap 3-->
<script src="netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>

<!-- jQuery REVOLUTION Slider -->
<script type="text/javascript" src="rs-plugin/js/jquery.themepunch.plugins.min.js"></script>
<script type="text/javascript" src="rs-plugin/js/jquery.themepunch.revolution.min.js"></script>

<!--*** Google map  ***-->
<script src="https://maps.google.com/maps/api/js?sensor=true"></script>
<!--*** Google map infobox  ***-->
<script src="js/external/infobox.js"></script>

<!-- Mobile menu -->
<script src="js/jquery.mobile.menu.js"></script>
<!-- Select -->
<script src="js/external/jquery.selectbox-0.2.min.js"></script>
<!-- Stars rate -->
<script src="js/external/jquery.raty.js"></script>

<!-- Form element -->
<script src="js/external/form-element.js"></script>
<!-- Form validation -->
<script src="js/form.js"></script>

<!-- Twitter feed -->
<script src="js/external/twitterfeed.js"></script> -->

<!-- Custom -->
<script src="js/custom.js?v=<?php echo time(); ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        init_BookingTwo();
    });
</script>
<script src="login-ui2/login-ui2/js/common.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        init_Home();
    });
</script>

</script>
</body>

</html>

