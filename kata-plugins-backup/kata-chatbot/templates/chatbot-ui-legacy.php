<?php
/**
 * KATA Smart Chatbot UI Template
 * 
 * @package KATA_SEO_Manager
 * @subpackage Templates
 */

if (!defined('ABSPATH')) exit;

$settings = $GLOBALS['kataChatbot']['settings'] ?? array();
$position = $settings['position'] ?? 'bottom-right';
$primary_color = $settings['primaryColor'] ?? '#042277';
$secondary_color = $settings['secondaryColor'] ?? '#040B1E';
$bot_name = $settings['botName'] ?? 'KATA Assistant';
?>

<!-- KATA Smart Chatbot Container -->
<div id="kata-chatbot-container" class="kata-chatbot-<?php echo esc_attr($position); ?>" style="display: none;">
    
    <!-- Chatbot Toggle Button -->
    <div id="kata-chatbot-toggle" class="kata-chatbot-toggle" style="background: <?php echo esc_attr($primary_color); ?>;">
        <svg class="kata-chatbot-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill="white"/>
            <path d="M7 9H9V11H7V9ZM11 9H13V11H11V9ZM15 9H17V11H15V9Z" fill="white"/>
        </svg>
        <svg class="kata-chatbot-close-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="white"/>
        </svg>
        <span class="kata-chatbot-badge" style="display: none;">1</span>
    </div>
    
    <!-- Chatbot Window -->
    <div id="kata-chatbot-window" class="kata-chatbot-window" style="display: none;">
        
        <!-- Header -->
        <div class="kata-chatbot-header" style="background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, <?php echo esc_attr($secondary_color); ?> 100%);">
            <div class="kata-chatbot-header-info">
                <div class="kata-chatbot-avatar">
                    <img src="<?php echo esc_url($settings['avatar'] ?? KATA_SEO_MANAGER_PLUGIN_URL . 'assets/images/chatbot-avatar.png'); ?>" alt="<?php echo esc_attr($bot_name); ?>">
                    <span class="kata-chatbot-status"></span>
                </div>
                <div class="kata-chatbot-header-text">
                    <h3><?php echo esc_html($bot_name); ?></h3>
                    <span class="kata-chatbot-subtitle">Tư vấn trực tuyến</span>
                </div>
            </div>
            <div class="kata-chatbot-header-actions">
                <button class="kata-chatbot-minimize" title="Thu gọn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M19 13H5V11H19V13Z" fill="white"/>
                    </svg>
                </button>
                <button class="kata-chatbot-close" title="Đóng">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="white"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Messages Container -->
        <div class="kata-chatbot-messages" id="kata-chatbot-messages">
            <!-- Welcome message will be inserted here -->
        </div>
        
        <!-- Quick Actions -->
        <div class="kata-chatbot-quick-actions" id="kata-chatbot-quick-actions" style="display: none;">
            <!-- Dynamic buttons will be inserted here -->
        </div>
        
        <!-- Input Area -->
        <div class="kata-chatbot-input-area">
            <form id="kata-chatbot-form" class="kata-chatbot-form">
                <input 
                    type="text" 
                    id="kata-chatbot-input" 
                    class="kata-chatbot-input" 
                    placeholder="Nhập tin nhắn của bạn..."
                    autocomplete="off"
                >
                <button type="submit" class="kata-chatbot-send" style="background: <?php echo esc_attr($primary_color); ?>;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M2.01 21L23 12L2.01 3L2 10L17 12L2 14L2.01 21Z" fill="white"/>
                    </svg>
                </button>
            </form>
            <div class="kata-chatbot-powered">
                Powered by <strong>KATA AI</strong>
            </div>
        </div>
        
        <!-- Lead Form (Hidden by default) -->
        <div class="kata-chatbot-lead-form" id="kata-chatbot-lead-form" style="display: none;">
            <div class="kata-chatbot-form-header">
                <h4>📝 Để lại thông tin</h4>
                <p>Chúng tôi sẽ liên hệ tư vấn miễn phí cho bạn!</p>
            </div>
            <form id="kata-chatbot-lead-form-submit">
                <div class="kata-chatbot-form-group">
                    <input type="text" name="name" placeholder="Họ và tên *" required>
                </div>
                <div class="kata-chatbot-form-group">
                    <input type="email" name="email" placeholder="Email *" required>
                </div>
                <div class="kata-chatbot-form-group">
                    <input type="tel" name="phone" placeholder="Số điện thoại">
                </div>
                <div class="kata-chatbot-form-group">
                    <select name="interest_type">
                        <option value="">Bạn quan tâm đến...</option>
                        <option value="course">Khóa học</option>
                        <option value="consultation">Tư vấn</option>
                        <option value="price">Học phí</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="kata-chatbot-form-group">
                    <textarea name="message" placeholder="Tin nhắn của bạn..." rows="3"></textarea>
                </div>
                <div class="kata-chatbot-form-actions">
                    <button type="button" class="kata-chatbot-btn-secondary" id="kata-chatbot-cancel-lead">
                        Hủy
                    </button>
                    <button type="submit" class="kata-chatbot-btn-primary" style="background: <?php echo esc_attr($primary_color); ?>;">
                        Gửi thông tin
                    </button>
                </div>
            </form>
        </div>
        
    </div>
    
</div>

<!-- Notification Toast -->
<div id="kata-chatbot-toast" class="kata-chatbot-toast" style="display: none;"></div>

<style>
/* Inline critical styles to prevent FOUC */
#kata-chatbot-container {
    position: fixed;
    z-index: 999999;
    font-family: 'SVN-Aguda', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
}

#kata-chatbot-container.kata-chatbot-bottom-right {
    bottom: 20px;
    right: 20px;
}

#kata-chatbot-container.kata-chatbot-bottom-left {
    bottom: 20px;
    left: 20px;
}

.kata-chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(4, 34, 119, 0.3);
    transition: all 0.3s ease;
    position: relative;
}

.kata-chatbot-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(4, 34, 119, 0.4);
}

.kata-chatbot-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4444;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.kata-chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    max-width: calc(100vw - 40px);
    height: 600px;
    max-height: calc(100vh - 120px);
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.kata-chatbot-header {
    padding: 20px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.kata-chatbot-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.kata-chatbot-avatar {
    position: relative;
    width: 48px;
    height: 48px;
}

.kata-chatbot-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.kata-chatbot-status {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: #4caf50;
    border: 2px solid white;
    border-radius: 50%;
}

.kata-chatbot-header-text h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.kata-chatbot-subtitle {
    font-size: 12px;
    opacity: 0.9;
}

.kata-chatbot-header-actions {
    display: flex;
    gap: 8px;
}

.kata-chatbot-header-actions button {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.kata-chatbot-header-actions button:hover {
    background: rgba(255, 255, 255, 0.3);
}

.kata-chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f5f7fa;
}

.kata-chatbot-input-area {
    padding: 16px;
    background: white;
    border-top: 1px solid #e0e0e0;
}

.kata-chatbot-form {
    display: flex;
    gap: 8px;
}

.kata-chatbot-input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 24px;
    font-size: 14px;
    outline: none;
    font-family: inherit;
}

.kata-chatbot-input:focus {
    border-color: <?php echo esc_attr($primary_color); ?>;
}

.kata-chatbot-send {
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.2s;
}

.kata-chatbot-send:hover {
    opacity: 0.9;
}

.kata-chatbot-powered {
    text-align: center;
    font-size: 11px;
    color: #999;
    margin-top: 8px;
}

@media (max-width: 480px) {
    .kata-chatbot-window {
        width: calc(100vw - 40px);
        height: calc(100vh - 120px);
    }
}
</style>
