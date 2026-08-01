<!-- Chat Modal Component -->
<style>
    /* Chat Modal Styles */
    #chat-modal {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 400px;
        max-width: 90vw;
        height: 600px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        display: none;
        flex-direction: column;
        z-index: 10000;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    #chat-modal.show {
        display: flex;
    }

    #chat-modal-header {
        background: linear-gradient(135deg, #8d94b0ff 0%, #764ba2 100%);
        color: white;
        padding: 16px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    #chat-modal-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    #chat-close-btn {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s;
    }

    #chat-close-btn:hover {
        transform: scale(1.1);
    }

    #chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f8f9fa;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chat-message {
        display: flex;
        flex-direction: column;
        margin-bottom: 4px;
        animation: slideIn 0.3s ease-out;
    }

    .chat-message.own {
        align-items: flex-end;
    }

    .chat-message.other {
        align-items: flex-start;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .chat-message-sender {
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 2px;
        padding: 0 8px;
    }

    .chat-message.own .chat-message-sender {
        color: #667eea;
        text-align: right;
    }

    .chat-message.other .chat-message-sender {
        color: #555;
        text-align: left;
    }

    .chat-message-content {
        max-width: 75%;
        padding: 10px 12px;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.5;
        word-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        overflow-wrap: break-word;
    }

    .chat-message.own .chat-message-content {
        background: #667eea;
        color: white;
        border-radius: 8px 2px 8px 8px;
    }

    .chat-message.other .chat-message-content {
        background: white;
        color: #333;
        border-radius: 2px 8px 8px 8px;
        border: 1px solid #ddd;
    }

    .chat-message-time {
        font-size: 10px;
        color: #999;
        margin-top: 2px;
        padding: 0 8px;
    }

    .chat-message.own .chat-message-time {
        text-align: right;
    }

    .chat-message.other .chat-message-time {
        text-align: left;
    }

    #chat-input-area {
        padding: 12px;
        border-top: 1px solid #e0e0e0;
        background: white;
        border-radius: 0 0 12px 12px;
        display: flex;
        gap: 8px;
    }

    #chat-input {
        flex: 1;
        border: 1px solid #e0e0e0;
        border-radius: 20px;
        padding: 10px 14px;
        font-size: 13px;
        font-family: inherit;
        outline: none;
        transition: border-color 0.2s;
        resize: none;
        max-height: 60px;
    }

    #chat-input:focus {
        border-color: #667eea;
    }

    #chat-send-btn {
        background: linear-gradient(135deg, #0022b9ff 0%, #0f3995ff 100%);
        color: white;
        border: none;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }

    #chat-send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    #chat-send-btn:active {
        transform: scale(0.95);
    }

    #chat-floating-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.2s, box-shadow 0.2s;
        z-index: 9999;
    }

    #chat-floating-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    #chat-floating-btn:active {
        transform: scale(0.95);
    }

    #chat-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
        border: 2px solid white;
    }

    #chat-badge.hidden {
        display: none;
    }

    #chat-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
        text-align: center;
    }

    #chat-empty-state-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    #chat-loading {
        display: none;
        text-align: center;
        padding: 20px;
        color: #667eea;
    }

    #chat-loading.show {
        display: block;
    }
</style>

<!-- Floating Button -->
<button id="chat-floating-btn" title="Tin nhắn">
    💬
    <span id="chat-badge" class="hidden">0</span>
</button>

<!-- Chat Modal -->
<div id="chat-modal">
    <div id="chat-modal-header">
        <h3>💬 Tin nhắn rạp</h3>
        <button id="chat-close-btn">✕</button>
    </div>
    
    <div id="chat-messages">
        <div id="chat-empty-state">
            <div id="chat-empty-state-icon">📭</div>
            <div>Chưa có tin nhắn</div>
        </div>
        <div id="chat-loading">Đang tải...</div>
    </div>
    
    <div id="chat-input-area">
        <input 
            id="chat-input" 
            type="text" 
            placeholder="Nhập tin nhắn..." 
            maxlength="500"
        />
        <button id="chat-send-btn" title="Gửi">📤</button>
    </div>
</div>

<script>
// ============================================
// CHAT SYSTEM
// ============================================

const ChatSystem = {
    isOpen: false,
    messages: [],
    unreadCount: 0,
    autoRefreshInterval: null,
    lastLoadTime: 0,
    isLoadingMessages: false,
    lastMessageCount: 0,

    init() {
        this.attachEventListeners();
        this.checkUnreadMessages();
        this.startAutoRefresh();
    },

    attachEventListeners() {
        document.getElementById('chat-floating-btn').addEventListener('click', () => this.toggleModal());
        document.getElementById('chat-close-btn').addEventListener('click', () => this.toggleModal());
        
        const input = document.getElementById('chat-input');
        document.getElementById('chat-send-btn').addEventListener('click', () => this.sendMessage());
        
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Auto-resize textarea
        input.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 60) + 'px';
        });
    },

    toggleModal() {
        const modal = document.getElementById('chat-modal');
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            modal.classList.add('show');
            // Load tin nhắn ngay khi mở modal
            this.loadMessages();
            this.markAsRead();
        } else {
            modal.classList.remove('show');
        }
    },

    async loadMessages() {
        // Tránh load đồng thời
        if (this.isLoadingMessages) return;
        this.isLoadingMessages = true;
        
        try {
            const response = await fetch('model/tin_nhan.php?action=get_messages&limit=50&offset=0&t=' + Date.now());
            
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            
            const text = await response.text();
            const result = JSON.parse(text);
            
            if (!result.success) {
                throw new Error(result.message || 'Lỗi tải tin nhắn');
            }
            
            const newMessages = result.data || [];
            
            // Chỉ update nếu có tin nhắn mới (so sánh số lượng)
            // Hoặc nếu lần đầu load (lastMessageCount === 0)
            if (newMessages.length !== this.lastMessageCount || this.lastMessageCount === 0) {
                this.messages = newMessages;
                this.displayMessages();
                this.lastMessageCount = newMessages.length;
            }
            
            this.lastLoadTime = Date.now();
        } catch (err) {
            console.error('Lỗi tải tin nhắn:', err);
            if (this.isOpen && this.lastMessageCount === 0) {
                const msgContainer = document.getElementById('chat-messages');
                if (msgContainer) {
                    msgContainer.innerHTML = '<div style="padding: 20px; color: #d32f2f; text-align: center;">❌ Lỗi: ' + err.message + '</div>';
                }
            }
        } finally {
            this.isLoadingMessages = false;
        }
    },

    displayMessages() {
        const container = document.getElementById('chat-messages');
        if (!container) return;
        
        const emptyState = document.getElementById('chat-empty-state');
        
        if (this.messages.length === 0) {
            container.innerHTML = '<div id="chat-empty-state"><div id="chat-empty-state-icon">📭</div><div>Chưa có tin nhắn</div></div>';
            return;
        }
        
        if (emptyState) emptyState.style.display = 'none';
        
        container.innerHTML = this.messages.map(msg => `
            <div class="chat-message ${msg.is_current_user ? 'own' : 'other'}">
                <span class="chat-message-sender">${this.escapeHtml(msg.ten_nguoi_gui || 'Ẩn danh')}</span>
                <div class="chat-message-content">${this.escapeHtml(msg.noi_dung)}</div>
                <div class="chat-message-time">${msg.thoi_gian_formatted}</div>
            </div>
        `).join('');
        
        // Auto scroll to bottom
        container.scrollTop = container.scrollHeight;
    },

    async sendMessage() {
        const input = document.getElementById('chat-input');
        const noi_dung = input.value.trim();
        
        if (!noi_dung) return;
        
        // Clear input ngay
        input.value = '';
        input.style.height = 'auto';
        
        try {
            const formData = new FormData();
            formData.append('action', 'send_message');
            formData.append('noi_dung', noi_dung);
            formData.append('id_nguoi_nhan', 0); // 0 = broadcast
            
            const response = await fetch('model/tin_nhan.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            
            const text = await response.text();
            const result = JSON.parse(text);
            
            if (!result.success) {
                throw new Error(result.message || 'Lỗi gửi tin nhắn');
            }
            
            // Load tin nhắn ngay sau khi gửi
            await this.loadMessages();
        } catch (err) {
            alert('❌ Lỗi gửi tin nhắn: ' + err.message);
            console.error(err);
            // Restore input value on error
            input.value = noi_dung;
        }
    },

    async checkUnreadMessages() {
        try {
            const response = await fetch('model/tin_nhan.php?action=get_unread_count&t=' + Date.now());
            
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            
            const text = await response.text();
            const result = JSON.parse(text);
            
            if (result.success) {
                this.unreadCount = result.data.unread_count || 0;
                this.updateBadge();
            }
        } catch (err) {
            console.error('Lỗi kiểm tra tin chưa đọc:', err);
        }
    },

    updateBadge() {
        const badge = document.getElementById('chat-badge');
        if (!badge) return;
        
        if (this.unreadCount > 0) {
            badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    },

    async markAsRead() {
        try {
            const response = await fetch('model/tin_nhan.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=mark_as_read'
            });
            
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            
            const text = await response.text();
            const result = JSON.parse(text);
            
            if (result.success) {
                this.unreadCount = 0;
                this.updateBadge();
            }
        } catch (err) {
            console.error('Lỗi đánh dấu đã đọc:', err);
        }
    },

    startAutoRefresh() {
        // Refresh nhanh hơn: 1 giây khi modal mở, 3 giây khi đóng
        this.autoRefreshInterval = setInterval(() => {
            if (this.isOpen) {
                // Khi modal đang mở: refresh nhanh (1 giây)
                this.loadMessages();
            } else {
                // Khi modal đóng: chỉ check unread (1.5 giây)
                this.checkUnreadMessages();
            }
        }, 1000);
    },

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Initialize chat system khi trang load
document.addEventListener('DOMContentLoaded', () => ChatSystem.init());
</script>
