<?php
/**
 * Chatbot Widget Template
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get widget settings
$position = get_option('kata_chatbot_position', 'bottom-right');
$theme = get_option('kata_chatbot_theme', 'blue');
$welcome_message = get_option('kata_chatbot_welcome_message', __('Xin chào! Tôi có thể giúp gì cho bạn? 😊', 'kata-chatbot'));
$offline_message = get_option('kata_chatbot_offline_message', __('Chatbot hiện đang offline. Vui lòng thử lại sau.', 'kata-chatbot'));
$enabled = get_option('kata_chatbot_enabled', true);

// Get branches for contact options
$branch_handler = new KataChatbot_Branch_Handler();
$branches = $branch_handler->get_all_branches(true); // Get only active branches

if (!$enabled) {
    return;
}
?>

<!-- Kata Chatbot Widget -->
<div id="kata-chatbot-container" class="kata-chatbot-container kata-position-<?php echo esc_attr($position); ?> kata-theme-<?php echo esc_attr($theme); ?>">
    <!-- Chat Toggle Button -->
    <div id="kata-chat-toggle" class="kata-chat-toggle">
        <div class="kata-toggle-icon">
            <svg class="kata-icon-chat" viewBox="0 0 24 24" width="24" height="24">
                <path fill="currentColor" d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
            </svg>
            <svg class="kata-icon-close" viewBox="0 0 24 24" width="24" height="24">
                <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        </div>
        
        <!-- Notification Badge -->
        <div id="kata-notification-badge" class="kata-notification-badge" style="display: none;">
            <span id="kata-notification-count">1</span>
        </div>
        
        <!-- Status Indicator -->
        <div class="kata-status-indicator kata-status-online"></div>
    </div>

    <!-- Chat Window -->
    <div id="kata-chat-window" class="kata-chat-window">
        <!-- Chat Tabs -->
        <div class="kata-chat-tabs">
            <div class="kata-tab-nav">
                <button class="kata-tab-btn active" data-tab="chat">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path fill="currentColor" d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                    </svg>
                    <span><?php _e('Chat AI', 'kata-chatbot'); ?></span>
                </button>
                <button class="kata-tab-btn" data-tab="facebook">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span><?php _e('Facebook', 'kata-chatbot'); ?></span>
                </button>
                <button class="kata-tab-btn" data-tab="zalo">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path fill="currentColor" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.243 13.243a1 1 0 01-.707.293H8.464a1 1 0 01-.707-1.707L11.293 10.293a1 1 0 011.414 0l3.536 3.536a1 1 0 010 1.414z"/>
                    </svg>
                    <span><?php _e('Zalo', 'kata-chatbot'); ?></span>
                </button>
                <button class="kata-tab-btn" data-tab="hotline">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path fill="currentColor" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                    <span><?php _e('Hotline', 'kata-chatbot'); ?></span>
                </button>
            </div>
        </div>

        <!-- AI Chat Tab -->
        <div id="kata-tab-chat" class="kata-tab-content active">
            <!-- Chat Header -->
            <div class="kata-chat-header">
                <div class="kata-header-info">
                    <div class="kata-avatar">
                        <img src="<?php echo esc_url(get_option('kata_chatbot_avatar', plugins_url('assets/images/chatbot-avatar.png', dirname(__FILE__)))); ?>" 
                             alt="Kata AI" />
                    </div>
                    <div class="kata-header-text">
                        <h4><?php echo esc_html(get_option('kata_chatbot_name', __('Kata AI', 'kata-chatbot'))); ?></h4>
                        <span class="kata-status-text"><?php _e('Trực tuyến', 'kata-chatbot'); ?></span>
                    </div>
                </div>
                
                <div class="kata-header-actions">
                    <button id="kata-minimize-btn" class="kata-action-btn" title="<?php _e('Thu nhỏ', 'kata-chatbot'); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16">
                            <path fill="currentColor" d="M19 13H5v-2h14v2z"/>
                        </svg>
                    </button>
                    <button id="kata-close-btn" class="kata-action-btn" title="<?php _e('Đóng', 'kata-chatbot'); ?>">
                        <svg viewBox="0 0 24 24" width="16" height="16">
                            <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Chat Messages -->
            <div id="kata-chat-messages" class="kata-chat-messages">
                <!-- Welcome Message -->
                <div class="kata-message kata-bot-message">
                    <div class="kata-message-avatar">
                        <img src="<?php echo esc_url(get_option('kata_chatbot_avatar', plugins_url('assets/images/chatbot-avatar.png', dirname(__FILE__)))); ?>" 
                             alt="Kata AI" />
                    </div>
                    <div class="kata-message-content">
                        <div class="kata-message-bubble">
                            <?php echo wp_kses_post($welcome_message); ?>
                        </div>
                        <div class="kata-message-time">
                            <?php echo current_time('H:i'); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Suggested Actions -->
                <div class="kata-suggested-actions">
                    <button class="kata-suggestion-btn" data-message="<?php _e('Tôi muốn tìm hiểu về dịch vụ', 'kata-chatbot'); ?>">
                        <?php _e('Dịch vụ', 'kata-chatbot'); ?>
                    </button>
                    <button class="kata-suggestion-btn" data-message="<?php _e('Làm sao để liên hệ tư vấn?', 'kata-chatbot'); ?>">
                        <?php _e('Tư vấn', 'kata-chatbot'); ?>
                    </button>
                    <button class="kata-suggestion-btn" data-message="<?php _e('Báo giá dịch vụ như thế nào?', 'kata-chatbot'); ?>">
                        <?php _e('Báo giá', 'kata-chatbot'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Chat Input -->
            <div class="kata-chat-input">
                <div class="kata-input-group">
                    <textarea id="kata-message-input" 
                              placeholder="<?php _e('Nhập tin nhắn...', 'kata-chatbot'); ?>" 
                              rows="1"></textarea>
                    <button id="kata-send-btn" class="kata-send-btn" title="<?php _e('Gửi', 'kata-chatbot'); ?>">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Typing Indicator -->
                <div id="kata-typing-indicator" class="kata-typing-indicator" style="display: none;">
                    <div class="kata-typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <span class="kata-typing-text"><?php _e('Đang trả lời...', 'kata-chatbot'); ?></span>
                </div>
            </div>
        </div>

        <!-- Facebook Tab -->
        <div id="kata-tab-facebook" class="kata-tab-content">
            <div class="kata-contact-header">
                <div class="kata-contact-icon">
                    <svg viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </div>
                <div class="kata-contact-title">
                    <h3><?php _e('Liên hệ qua Facebook', 'kata-chatbot'); ?></h3>
                    <p><?php _e('Chọn chi nhánh để chat qua Messenger', 'kata-chatbot'); ?></p>
                </div>
            </div>
            
            <div class="kata-branch-list">
                <?php if (!empty($branches)) : ?>
                    <?php foreach ($branches as $branch) : ?>
                        <?php if (!empty($branch->facebook_url)) : ?>
                            <div class="kata-branch-item">
                                <div class="kata-branch-info">
                                    <h4><?php echo esc_html($branch->name); ?></h4>
                                    <p class="kata-branch-address"><?php echo esc_html($branch->address); ?></p>
                                    <?php if (!empty($branch->working_hours)) : ?>
                                        <p class="kata-working-hours">
                                            <span class="kata-icon">⏰</span>
                                            <?php echo esc_html($branch->working_hours); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="kata-branch-actions">
                                    <a href="<?php echo esc_url($branch->facebook_url); ?>" 
                                       target="_blank" 
                                       class="kata-contact-btn kata-facebook-btn">
                                        <svg viewBox="0 0 24 24" width="16" height="16">
                                            <path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        <?php _e('Chat Messenger', 'kata-chatbot'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="kata-no-contact">
                        <p><?php _e('Hiện tại chưa có thông tin Facebook được cấu hình.', 'kata-chatbot'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Zalo Tab -->
        <div id="kata-tab-zalo" class="kata-tab-content">
            <div class="kata-contact-header">
                <div class="kata-contact-icon">
                    <svg viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#0068FF" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.243 13.243a1 1 0 01-.707.293H8.464a1 1 0 01-.707-1.707L11.293 10.293a1 1 0 011.414 0l3.536 3.536a1 1 0 010 1.414z"/>
                    </svg>
                </div>
                <div class="kata-contact-title">
                    <h3><?php _e('Liên hệ qua Zalo', 'kata-chatbot'); ?></h3>
                    <p><?php _e('Chọn chi nhánh để chat qua Zalo', 'kata-chatbot'); ?></p>
                </div>
            </div>
            
            <div class="kata-branch-list">
                <?php if (!empty($branches)) : ?>
                    <?php foreach ($branches as $branch) : ?>
                        <?php if (!empty($branch->zalo_url)) : ?>
                            <div class="kata-branch-item">
                                <div class="kata-branch-info">
                                    <h4><?php echo esc_html($branch->name); ?></h4>
                                    <p class="kata-branch-address"><?php echo esc_html($branch->address); ?></p>
                                    <?php if (!empty($branch->working_hours)) : ?>
                                        <p class="kata-working-hours">
                                            <span class="kata-icon">⏰</span>
                                            <?php echo esc_html($branch->working_hours); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="kata-branch-actions">
                                    <a href="<?php echo esc_url($branch->zalo_url); ?>" 
                                       target="_blank" 
                                       class="kata-contact-btn kata-zalo-btn">
                                        <svg viewBox="0 0 24 24" width="16" height="16">
                                            <path fill="currentColor" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.243 13.243a1 1 0 01-.707.293H8.464a1 1 0 01-.707-1.707L11.293 10.293a1 1 0 011.414 0l3.536 3.536a1 1 0 010 1.414z"/>
                                        </svg>
                                        <?php _e('Chat Zalo', 'kata-chatbot'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="kata-no-contact">
                        <p><?php _e('Hiện tại chưa có thông tin Zalo được cấu hình.', 'kata-chatbot'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hotline Tab -->
        <div id="kata-tab-hotline" class="kata-tab-content">
            <div class="kata-contact-header">
                <div class="kata-contact-icon">
                    <svg viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#00C851" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                    </svg>
                </div>
                <div class="kata-contact-title">
                    <h3><?php _e('Gọi điện thoại', 'kata-chatbot'); ?></h3>
                    <p><?php _e('Chọn chi nhánh để gọi điện trực tiếp', 'kata-chatbot'); ?></p>
                </div>
            </div>
            
            <div class="kata-branch-list">
                <?php if (!empty($branches)) : ?>
                    <?php foreach ($branches as $branch) : ?>
                        <div class="kata-branch-item">
                            <div class="kata-branch-info">
                                <h4><?php echo esc_html($branch->name); ?></h4>
                                <p class="kata-branch-address"><?php echo esc_html($branch->address); ?></p>
                                <?php if (!empty($branch->working_hours)) : ?>
                                    <p class="kata-working-hours">
                                        <span class="kata-icon">⏰</span>
                                        <?php echo esc_html($branch->working_hours); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="kata-branch-actions">
                                <?php if (!empty($branch->hotline)) : ?>
                                    <a href="tel:<?php echo esc_attr($branch->hotline); ?>" 
                                       class="kata-contact-btn kata-hotline-btn">
                                        <svg viewBox="0 0 24 24" width="16" height="16">
                                            <path fill="currentColor" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                        </svg>
                                        <?php echo esc_html($branch->hotline); ?>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($branch->phone) && $branch->phone !== $branch->hotline) : ?>
                                    <a href="tel:<?php echo esc_attr($branch->phone); ?>" 
                                       class="kata-contact-btn kata-phone-btn">
                                        <svg viewBox="0 0 24 24" width="16" height="16">
                                            <path fill="currentColor" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                                        </svg>
                                        <?php echo esc_html($branch->phone); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <div class="kata-no-contact">
                        <p><?php _e('Hiện tại chưa có thông tin hotline được cấu hình.', 'kata-chatbot'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Tab Navigation */
.kata-chat-tabs {
    border-bottom: 1px solid #e0e0e0;
}

.kata-tab-nav {
    display: flex;
    background: #f8f9fa;
}

.kata-tab-btn {
    flex: 1;
    padding: 12px 8px;
    border: none;
    background: transparent;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
    font-size: 11px;
    color: #666;
}

.kata-tab-btn:hover {
    background: #e9ecef;
    color: #333;
}

.kata-tab-btn.active {
    color: #0073aa;
    border-bottom-color: #0073aa;
    background: #fff;
}

.kata-tab-btn svg {
    margin-bottom: 2px;
}

/* Tab Content */
.kata-tab-content {
    display: none;
    height: 400px;
    overflow-y: auto;
}

.kata-tab-content.active {
    display: block;
}

/* Contact Headers */
.kata-contact-header {
    display: flex;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #e0e0e0;
    background: #f8f9fa;
}

.kata-contact-icon {
    margin-right: 12px;
}

.kata-contact-title h3 {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 600;
    color: #333;
}

.kata-contact-title p {
    margin: 0;
    font-size: 12px;
    color: #666;
}

/* Branch List */
.kata-branch-list {
    padding: 15px;
}

.kata-branch-item {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.kata-branch-info h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.kata-branch-address {
    margin: 0 0 8px 0;
    font-size: 12px;
    color: #666;
    line-height: 1.4;
}

.kata-working-hours {
    margin: 0 0 12px 0;
    font-size: 11px;
    color: #888;
    display: flex;
    align-items: center;
    gap: 4px;
}

.kata-icon {
    font-size: 10px;
}

/* Contact Buttons */
.kata-branch-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.kata-contact-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    cursor: pointer;
}

.kata-facebook-btn {
    background: #1877F2;
    color: white;
}

.kata-facebook-btn:hover {
    background: #166fe5;
    color: white;
    text-decoration: none;
}

.kata-zalo-btn {
    background: #0068FF;
    color: white;
}

.kata-zalo-btn:hover {
    background: #0056d6;
    color: white;
    text-decoration: none;
}

.kata-hotline-btn {
    background: #00C851;
    color: white;
}

.kata-hotline-btn:hover {
    background: #00a843;
    color: white;
    text-decoration: none;
}

.kata-phone-btn {
    background: #17a2b8;
    color: white;
}

.kata-phone-btn:hover {
    background: #138496;
    color: white;
    text-decoration: none;
}

/* No Contact Message */
.kata-no-contact {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.kata-no-contact p {
    margin: 0;
    font-size: 14px;
}

/* Chat specific styles for tab content */
#kata-tab-chat {
    display: flex;
    flex-direction: column;
    height: 400px;
}

#kata-tab-chat .kata-chat-header {
    flex-shrink: 0;
}

#kata-tab-chat .kata-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
}

#kata-tab-chat .kata-chat-input {
    flex-shrink: 0;
    padding: 15px;
    border-top: 1px solid #e0e0e0;
}

/* Responsive Design */
@media (max-width: 480px) {
    .kata-tab-btn {
        font-size: 10px;
        padding: 10px 6px;
    }
    
    .kata-tab-btn span {
        display: none;
    }
    
    .kata-branch-actions {
        flex-direction: column;
    }
    
    .kata-contact-btn {
        text-align: center;
        justify-content: center;
    }
}

/* Animation for tabs */
.kata-tab-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Badge styles for tab notifications */
.kata-tab-btn {
    position: relative;
}

.kata-tab-btn .kata-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    font-size: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Tab switching functionality
    $('.kata-tab-btn').on('click', function() {
        var tab = $(this).data('tab');
        
        // Remove active class from all tabs and contents
        $('.kata-tab-btn').removeClass('active');
        $('.kata-tab-content').removeClass('active');
        
        // Add active class to clicked tab and its content
        $(this).addClass('active');
        $('#kata-tab-' + tab).addClass('active');
        
        // Track tab switching for analytics
        if (typeof kataTracking !== 'undefined') {
            kataTracking.trackEvent('tab_switch', tab);
        }
    });
    
    // Contact button tracking
    $('.kata-contact-btn').on('click', function() {
        var type = 'unknown';
        var branchName = $(this).closest('.kata-branch-item').find('h4').text();
        
        if ($(this).hasClass('kata-facebook-btn')) {
            type = 'facebook';
        } else if ($(this).hasClass('kata-zalo-btn')) {
            type = 'zalo';
        } else if ($(this).hasClass('kata-hotline-btn')) {
            type = 'hotline';
        } else if ($(this).hasClass('kata-phone-btn')) {
            type = 'phone';
        }
        
        // Track contact interaction
        if (typeof kataTracking !== 'undefined') {
            kataTracking.trackEvent('contact_click', type, branchName);
        }
        
        // Add visual feedback
        $(this).addClass('clicked');
        setTimeout(function() {
            $('.kata-contact-btn').removeClass('clicked');
        }, 300);
    });
    
    // Auto-switch to appropriate tab based on device
    function autoSwitchTab() {
        var isMobile = window.innerWidth <= 768;
        var userAgent = navigator.userAgent.toLowerCase();
        
        // Auto-suggest Zalo on mobile in Vietnam
        if (isMobile && (userAgent.includes('mobile') || userAgent.includes('android'))) {
            // Don't auto-switch, let user choose
            return;
        }
    }
    
    // Initialize
    autoSwitchTab();
    $(window).on('resize', autoSwitchTab);
    
    // Add notification badges if there are active contacts
    function updateTabBadges() {
        var facebookCount = $('#kata-tab-facebook .kata-branch-item').length;
        var zaloCount = $('#kata-tab-zalo .kata-branch-item').length;
        var hotlineCount = $('#kata-tab-hotline .kata-branch-item').length;
        
        // Add badges to tabs with available contacts
        if (facebookCount > 0) {
            $('.kata-tab-btn[data-tab="facebook"]').append('<span class="kata-badge">' + facebookCount + '</span>');
        }
        if (zaloCount > 0) {
            $('.kata-tab-btn[data-tab="zalo"]').append('<span class="kata-badge">' + zaloCount + '</span>');
        }
        if (hotlineCount > 0) {
            $('.kata-tab-btn[data-tab="hotline"]').append('<span class="kata-badge">' + hotlineCount + '</span>');
        }
    }
    
    updateTabBadges();
});
</script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chat Input -->
        <div class="kata-chat-input">
            <div class="kata-input-wrapper">
                <textarea id="kata-message-input" 
                         class="kata-message-textarea"
                         placeholder="<?php _e('Nhập tin nhắn của bạn...', 'kata-chatbot'); ?>"
                         rows="1"
                         maxlength="<?php echo esc_attr(get_option('kata_chatbot_max_message_length', 1000)); ?>"></textarea>
                
                <div class="kata-input-actions">
                    <button id="kata-emoji-btn" class="kata-input-btn" title="<?php _e('Biểu tượng cảm xúc', 'kata-chatbot'); ?>">
                        😊
                    </button>
                    
                    <button id="kata-send-btn" class="kata-send-btn" title="<?php _e('Gửi tin nhắn', 'kata-chatbot'); ?>">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Character Counter -->
            <div class="kata-char-counter">
                <span id="kata-char-count">0</span>/<span id="kata-char-limit"><?php echo esc_html(get_option('kata_chatbot_max_message_length', 1000)); ?></span>
            </div>
        </div>
        
        <!-- Emoji Picker -->
        <div id="kata-emoji-picker" class="kata-emoji-picker" style="display: none;">
            <div class="kata-emoji-grid">
                <span class="kata-emoji-item">😊</span>
                <span class="kata-emoji-item">😂</span>
                <span class="kata-emoji-item">🤔</span>
                <span class="kata-emoji-item">👍</span>
                <span class="kata-emoji-item">👎</span>
                <span class="kata-emoji-item">❤️</span>
                <span class="kata-emoji-item">🎉</span>
                <span class="kata-emoji-item">🔥</span>
                <span class="kata-emoji-item">💡</span>
                <span class="kata-emoji-item">💼</span>
                <span class="kata-emoji-item">🚀</span>
                <span class="kata-emoji-item">⭐</span>
            </div>
        </div>
        
        <!-- Chat Footer -->
        <div class="kata-chat-footer">
            <div class="kata-footer-text">
                <?php printf(__('Hỗ trợ bởi %s', 'kata-chatbot'), '<strong>Timona AI</strong>'); ?>
            </div>
            
            <div class="kata-footer-actions">
                <button id="kata-feedback-btn" class="kata-footer-btn" title="<?php _e('Đánh giá', 'kata-chatbot'); ?>">
                    ⭐
                </button>
                <button id="kata-sound-toggle" class="kata-footer-btn" title="<?php _e('Bật/tắt âm thanh', 'kata-chatbot'); ?>">
                    🔊
                </button>
            </div>
        </div>
    </div>
    
    <!-- Feedback Modal -->
    <div id="kata-feedback-modal" class="kata-modal" style="display: none;">
        <div class="kata-modal-content">
            <div class="kata-modal-header">
                <h3><?php _e('Đánh giá trải nghiệm', 'kata-chatbot'); ?></h3>
                <button class="kata-modal-close">&times;</button>
            </div>
            
            <div class="kata-modal-body">
                <div class="kata-rating-section">
                    <p><?php _e('Bạn có hài lòng với cuộc hội thoại?', 'kata-chatbot'); ?></p>
                    <div class="kata-rating-stars">
                        <span class="kata-star" data-rating="1">⭐</span>
                        <span class="kata-star" data-rating="2">⭐</span>
                        <span class="kata-star" data-rating="3">⭐</span>
                        <span class="kata-star" data-rating="4">⭐</span>
                        <span class="kata-star" data-rating="5">⭐</span>
                    </div>
                </div>
                
                <div class="kata-feedback-section">
                    <label for="kata-feedback-text"><?php _e('Góp ý (tùy chọn):', 'kata-chatbot'); ?></label>
                    <textarea id="kata-feedback-text" rows="3" placeholder="<?php _e('Chia sẻ trải nghiệm của bạn...', 'kata-chatbot'); ?>"></textarea>
                </div>
                
                <div class="kata-modal-actions">
                    <button type="button" class="kata-btn kata-btn-secondary" id="kata-feedback-skip">
                        <?php _e('Bỏ qua', 'kata-chatbot'); ?>
                    </button>
                    <button type="button" class="kata-btn kata-btn-primary" id="kata-feedback-submit">
                        <?php _e('Gửi đánh giá', 'kata-chatbot'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
.kata-chatbot-container {
    position: fixed;
    z-index: 999999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Position variants */
.kata-position-bottom-right {
    bottom: 20px;
    right: 20px;
}

.kata-position-bottom-left {
    bottom: 20px;
    left: 20px;
}

.kata-position-bottom-center {
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
}

/* Theme variants */
.kata-theme-blue {
    --kata-primary: #2271b1;
    --kata-primary-dark: #135e96;
    --kata-secondary: #f0f6fc;
}

.kata-theme-green {
    --kata-primary: #00a32a;
    --kata-primary-dark: #007c20;
    --kata-secondary: #f0f8f0;
}

.kata-theme-purple {
    --kata-primary: #8b5cf6;
    --kata-primary-dark: #7c3aed;
    --kata-secondary: #f3f0ff;
}

/* Chat Toggle Button */
.kata-chat-toggle {
    position: relative;
    width: 60px;
    height: 60px;
    background: var(--kata-primary);
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    border: none;
}

.kata-chat-toggle:hover {
    background: var(--kata-primary-dark);
    transform: scale(1.05);
}

.kata-toggle-icon {
    position: relative;
    color: white;
}

.kata-icon-close {
    display: none;
}

.kata-chatbot-container.kata-chat-open .kata-icon-chat {
    display: none;
}

.kata-chatbot-container.kata-chat-open .kata-icon-close {
    display: block;
}

.kata-notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.kata-status-indicator {
    position: absolute;
    bottom: 5px;
    right: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.kata-status-online {
    background: #00a32a;
}

.kata-status-offline {
    background: #666;
}

/* Chat Window */
.kata-chat-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
}

.kata-chatbot-container.kata-chat-open .kata-chat-window {
    display: flex;
}

/* Chat Header */
.kata-chat-header {
    background: var(--kata-primary);
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.kata-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.kata-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.kata-header-text h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.kata-status-text {
    font-size: 12px;
    opacity: 0.9;
}

.kata-header-actions {
    display: flex;
    gap: 8px;
}

.kata-action-btn {
    background: none;
    border: none;
    color: white;
    padding: 4px;
    border-radius: 4px;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.kata-action-btn:hover {
    opacity: 1;
    background: rgba(255,255,255,0.1);
}

/* Chat Messages */
.kata-chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    scroll-behavior: smooth;
}

.kata-message {
    display: flex;
    margin-bottom: 16px;
    animation: slideIn 0.3s ease;
}

.kata-bot-message {
    justify-content: flex-start;
}

.kata-user-message {
    justify-content: flex-end;
}

.kata-message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 8px;
}

.kata-message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.kata-message-content {
    max-width: 80%;
}

.kata-message-bubble {
    background: #f1f1f1;
    padding: 12px 16px;
    border-radius: 18px;
    margin-bottom: 4px;
    word-wrap: break-word;
    line-height: 1.4;
}

.kata-user-message .kata-message-bubble {
    background: var(--kata-primary);
    color: white;
    border-bottom-right-radius: 6px;
}

.kata-bot-message .kata-message-bubble {
    border-bottom-left-radius: 6px;
}

.kata-message-time {
    font-size: 11px;
    color: #666;
    text-align: right;
    padding: 0 4px;
}

.kata-user-message .kata-message-time {
    text-align: right;
}

.kata-bot-message .kata-message-time {
    text-align: left;
}

/* Suggested Actions */
.kata-suggested-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 16px 0;
}

.kata-suggestion-btn {
    background: var(--kata-secondary);
    border: 1px solid var(--kata-primary);
    color: var(--kata-primary);
    padding: 8px 12px;
    border-radius: 16px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.kata-suggestion-btn:hover {
    background: var(--kata-primary);
    color: white;
}

/* Typing Indicator */
.kata-typing-bubble {
    background: #f1f1f1;
    padding: 16px;
    border-radius: 18px;
    border-bottom-left-radius: 6px;
}

.kata-typing-dots {
    display: flex;
    gap: 4px;
}

.kata-typing-dots span {
    width: 6px;
    height: 6px;
    background: #999;
    border-radius: 50%;
    animation: typingDots 1.4s infinite ease-in-out;
}

.kata-typing-dots span:nth-child(1) {
    animation-delay: -0.32s;
}

.kata-typing-dots span:nth-child(2) {
    animation-delay: -0.16s;
}

@keyframes typingDots {
    0%, 80%, 100% {
        opacity: 0.3;
        transform: scale(0.8);
    }
    40% {
        opacity: 1;
        transform: scale(1);
    }
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

/* Chat Input */
.kata-chat-input {
    border-top: 1px solid #eee;
    padding: 15px;
}

.kata-input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    background: #f8f9fa;
    border-radius: 20px;
    padding: 8px 12px;
}

.kata-message-textarea {
    flex: 1;
    border: none;
    background: none;
    resize: none;
    outline: none;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.4;
    min-height: 20px;
    max-height: 80px;
}

.kata-input-actions {
    display: flex;
    align-items: center;
    gap: 4px;
}

.kata-input-btn {
    background: none;
    border: none;
    font-size: 16px;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: background 0.2s;
}

.kata-input-btn:hover {
    background: rgba(0,0,0,0.05);
}

.kata-send-btn {
    background: var(--kata-primary);
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}

.kata-send-btn:hover {
    background: var(--kata-primary-dark);
}

.kata-send-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.kata-char-counter {
    text-align: right;
    font-size: 11px;
    color: #666;
    margin-top: 4px;
}

/* Emoji Picker */
.kata-emoji-picker {
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.kata-emoji-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
}

.kata-emoji-item {
    font-size: 18px;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    text-align: center;
    transition: background 0.2s;
}

.kata-emoji-item:hover {
    background: #f0f0f0;
}

/* Chat Footer */
.kata-chat-footer {
    border-top: 1px solid #eee;
    padding: 8px 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fafafa;
}

.kata-footer-text {
    font-size: 11px;
    color: #666;
}

.kata-footer-actions {
    display: flex;
    gap: 8px;
}

.kata-footer-btn {
    background: none;
    border: none;
    font-size: 14px;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.kata-footer-btn:hover {
    opacity: 1;
}

/* Modal */
.kata-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kata-modal-content {
    background: white;
    border-radius: 12px;
    max-width: 400px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.kata-modal-header {
    padding: 20px 20px 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.kata-modal-header h3 {
    margin: 0;
    font-size: 18px;
}

.kata-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.kata-modal-body {
    padding: 20px;
}

.kata-rating-section {
    text-align: center;
    margin-bottom: 20px;
}

.kata-rating-stars {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 12px;
}

.kata-star {
    font-size: 24px;
    cursor: pointer;
    opacity: 0.3;
    transition: opacity 0.2s;
}

.kata-star:hover,
.kata-star.active {
    opacity: 1;
}

.kata-feedback-section {
    margin-bottom: 20px;
}

.kata-feedback-section label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.kata-feedback-section textarea {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 6px;
    padding: 8px 12px;
    font-family: inherit;
    resize: vertical;
}

.kata-modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.kata-btn {
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
}

.kata-btn-secondary {
    background: #f1f1f1;
    color: #666;
}

.kata-btn-secondary:hover {
    background: #e1e1e1;
}

.kata-btn-primary {
    background: var(--kata-primary);
    color: white;
}

.kata-btn-primary:hover {
    background: var(--kata-primary-dark);
}

/* Responsive */
@media (max-width: 768px) {
    .kata-chat-window {
        width: calc(100vw - 40px);
        height: calc(100vh - 140px);
        bottom: 80px;
        right: 20px;
        left: 20px;
    }
    
    .kata-position-bottom-center {
        left: 20px;
        transform: none;
    }
}

@media (max-width: 480px) {
    .kata-chat-window {
        width: calc(100vw - 20px);
        height: calc(100vh - 100px);
        bottom: 80px;
        right: 10px;
        left: 10px;
    }
}
</style>

<!-- JavaScript -->
<script>
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    window.KataChatbot = new function() {
        var self = this;
        var isOpen = false;
        var sessionId = null;
        var messageHistory = [];
        var soundEnabled = <?php echo get_option('kata_chatbot_sound_enabled', true) ? 'true' : 'false'; ?>;
        
        // Initialize
        this.init = function() {
            bindEvents();
            initializeSession();
            checkOnlineStatus();
        };
        
        // Bind events
        function bindEvents() {
            // Toggle chat
            document.getElementById('kata-chat-toggle').addEventListener('click', toggleChat);
            document.getElementById('kata-close-btn').addEventListener('click', closeChat);
            document.getElementById('kata-minimize-btn').addEventListener('click', minimizeChat);
            
            // Send message
            document.getElementById('kata-send-btn').addEventListener('click', sendMessage);
            document.getElementById('kata-message-input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
            
            // Auto-resize textarea
            document.getElementById('kata-message-input').addEventListener('input', autoResizeTextarea);
            
            // Character counter
            document.getElementById('kata-message-input').addEventListener('input', updateCharCounter);
            
            // Emoji picker
            document.getElementById('kata-emoji-btn').addEventListener('click', toggleEmojiPicker);
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#kata-emoji-picker') && !e.target.closest('#kata-emoji-btn')) {
                    hideEmojiPicker();
                }
            });
            
            // Emoji selection
            document.querySelectorAll('.kata-emoji-item').forEach(function(emoji) {
                emoji.addEventListener('click', function() {
                    insertEmoji(this.textContent);
                });
            });
            
            // Suggestion buttons
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('kata-suggestion-btn')) {
                    var message = e.target.getAttribute('data-message');
                    sendUserMessage(message);
                }
            });
            
            // Feedback
            document.getElementById('kata-feedback-btn').addEventListener('click', showFeedbackModal);
            document.querySelectorAll('.kata-modal-close, #kata-feedback-skip').forEach(function(btn) {
                btn.addEventListener('click', hideFeedbackModal);
            });
            document.getElementById('kata-feedback-submit').addEventListener('click', submitFeedback);
            
            // Star rating
            document.querySelectorAll('.kata-star').forEach(function(star) {
                star.addEventListener('click', function() {
                    var rating = parseInt(this.getAttribute('data-rating'));
                    updateStarRating(rating);
                });
            });
            
            // Sound toggle
            document.getElementById('kata-sound-toggle').addEventListener('click', toggleSound);
        }
        
        // Initialize session
        function initializeSession() {
            sessionId = 'chat_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }
        
        // Toggle chat window
        function toggleChat() {
            if (isOpen) {
                closeChat();
            } else {
                openChat();
            }
        }
        
        // Open chat
        function openChat() {
            isOpen = true;
            document.getElementById('kata-chatbot-container').classList.add('kata-chat-open');
            document.getElementById('kata-message-input').focus();
            hideNotificationBadge();
        }
        
        // Close chat
        function closeChat() {
            isOpen = false;
            document.getElementById('kata-chatbot-container').classList.remove('kata-chat-open');
            hideEmojiPicker();
        }
        
        // Minimize chat
        function minimizeChat() {
            closeChat();
        }
        
        // Send message
        function sendMessage() {
            var input = document.getElementById('kata-message-input');
            var message = input.value.trim();
            
            if (!message) return;
            
            // Clear input
            input.value = '';
            autoResizeTextarea();
            updateCharCounter();
            hideEmojiPicker();
            
            // Send user message
            sendUserMessage(message);
        }
        
        // Send user message
        function sendUserMessage(message) {
            // Add user message to chat
            addMessage('user', message);
            
            // Show typing indicator
            showTypingIndicator();
            
            // Hide suggestions
            hideSuggestedActions();
            
            // Send to backend
            sendToBackend(message);
        }
        
        // Send to backend
        function sendToBackend(message) {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'kata_chatbot_send_message',
                    message: message,
                    session_id: sessionId,
                    nonce: '<?php echo wp_create_nonce('kata_chatbot_nonce'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                hideTypingIndicator();
                
                if (data.success) {
                    // Add bot response
                    addMessage('bot', data.data.message);
                    
                    // Play notification sound
                    if (soundEnabled) {
                        playNotificationSound();
                    }
                    
                    // Show notification if chat is closed
                    if (!isOpen) {
                        showNotificationBadge();
                    }
                    
                    // Show new suggestions
                    showSuggestedActions(data.data.suggestions || []);
                } else {
                    addMessage('bot', '<?php _e('Xin lỗi, có lỗi xảy ra. Vui lòng thử lại.', 'kata-chatbot'); ?>');
                }
            })
            .catch(error => {
                console.error('Chat error:', error);
                hideTypingIndicator();
                addMessage('bot', '<?php _e('Không thể kết nối. Vui lòng kiểm tra internet và thử lại.', 'kata-chatbot'); ?>');
            });
        }
        
        // Add message to chat
        function addMessage(type, text) {
            var messagesContainer = document.getElementById('kata-chat-messages');
            var messageEl = document.createElement('div');
            messageEl.className = 'kata-message kata-' + type + '-message';
            
            var now = new Date();
            var timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                         now.getMinutes().toString().padStart(2, '0');
            
            if (type === 'bot') {
                messageEl.innerHTML = `
                    <div class="kata-message-avatar">
                        <img src="<?php echo esc_url(get_option('kata_chatbot_avatar', plugins_url('assets/images/chatbot-avatar.png', dirname(__FILE__)))); ?>" alt="Kata AI" />
                    </div>
                    <div class="kata-message-content">
                        <div class="kata-message-bubble">${text}</div>
                        <div class="kata-message-time">${timeStr}</div>
                    </div>
                `;
            } else {
                messageEl.innerHTML = `
                    <div class="kata-message-content">
                        <div class="kata-message-bubble">${text}</div>
                        <div class="kata-message-time">${timeStr}</div>
                    </div>
                `;
            }
            
            messagesContainer.appendChild(messageEl);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            // Store in history
            messageHistory.push({type: type, text: text, time: now});
        }
        
        // Show/hide typing indicator
        function showTypingIndicator() {
            document.getElementById('kata-typing-indicator').style.display = 'block';
            scrollToBottom();
        }
        
        function hideTypingIndicator() {
            document.getElementById('kata-typing-indicator').style.display = 'none';
        }
        
        // Scroll to bottom
        function scrollToBottom() {
            var messagesContainer = document.getElementById('kata-chat-messages');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Auto resize textarea
        function autoResizeTextarea() {
            var textarea = document.getElementById('kata-message-input');
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 80) + 'px';
        }
        
        // Update character counter
        function updateCharCounter() {
            var input = document.getElementById('kata-message-input');
            var count = input.value.length;
            var limit = parseInt(document.getElementById('kata-char-limit').textContent);
            
            document.getElementById('kata-char-count').textContent = count;
            
            if (count > limit * 0.8) {
                document.getElementById('kata-char-count').style.color = count >= limit ? '#d63638' : '#dba617';
            } else {
                document.getElementById('kata-char-count').style.color = '#666';
            }
            
            // Disable send button if over limit
            document.getElementById('kata-send-btn').disabled = count >= limit;
        }
        
        // Emoji picker
        function toggleEmojiPicker() {
            var picker = document.getElementById('kata-emoji-picker');
            picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
        }
        
        function hideEmojiPicker() {
            document.getElementById('kata-emoji-picker').style.display = 'none';
        }
        
        function insertEmoji(emoji) {
            var input = document.getElementById('kata-message-input');
            var start = input.selectionStart;
            var end = input.selectionEnd;
            var text = input.value;
            
            input.value = text.substring(0, start) + emoji + text.substring(end);
            input.selectionStart = input.selectionEnd = start + emoji.length;
            input.focus();
            
            updateCharCounter();
            hideEmojiPicker();
        }
        
        // Suggested actions
        function showSuggestedActions(suggestions) {
            if (!suggestions || suggestions.length === 0) return;
            
            var messagesContainer = document.getElementById('kata-chat-messages');
            var existingSuggestions = messagesContainer.querySelector('.kata-suggested-actions');
            if (existingSuggestions) {
                existingSuggestions.remove();
            }
            
            var suggestionsEl = document.createElement('div');
            suggestionsEl.className = 'kata-suggested-actions';
            
            suggestions.forEach(function(suggestion) {
                var btn = document.createElement('button');
                btn.className = 'kata-suggestion-btn';
                btn.setAttribute('data-message', suggestion);
                btn.textContent = suggestion;
                suggestionsEl.appendChild(btn);
            });
            
            messagesContainer.appendChild(suggestionsEl);
            scrollToBottom();
        }
        
        function hideSuggestedActions() {
            var suggestions = document.querySelector('.kata-suggested-actions');
            if (suggestions) {
                suggestions.remove();
            }
        }
        
        // Notification badge
        function showNotificationBadge() {
            var badge = document.getElementById('kata-notification-badge');
            var count = parseInt(badge.querySelector('span').textContent) || 0;
            badge.querySelector('span').textContent = count + 1;
            badge.style.display = 'flex';
        }
        
        function hideNotificationBadge() {
            var badge = document.getElementById('kata-notification-badge');
            badge.style.display = 'none';
            badge.querySelector('span').textContent = '1';
        }
        
        // Sound
        function playNotificationSound() {
            // Create audio context for notification sound
            try {
                var audioContext = new (window.AudioContext || window.webkitAudioContext)();
                var oscillator = audioContext.createOscillator();
                var gainNode = audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
                gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
                
                oscillator.start();
                oscillator.stop(audioContext.currentTime + 0.3);
            } catch (e) {
                // Fallback - no sound
                console.log('Audio not supported');
            }
        }
        
        function toggleSound() {
            soundEnabled = !soundEnabled;
            var btn = document.getElementById('kata-sound-toggle');
            btn.textContent = soundEnabled ? '🔊' : '🔇';
            
            // Save preference
            localStorage.setItem('kata_chatbot_sound', soundEnabled);
        }
        
        // Feedback modal
        function showFeedbackModal() {
            document.getElementById('kata-feedback-modal').style.display = 'flex';
        }
        
        function hideFeedbackModal() {
            document.getElementById('kata-feedback-modal').style.display = 'none';
            resetFeedbackForm();
        }
        
        function resetFeedbackForm() {
            document.querySelectorAll('.kata-star').forEach(function(star) {
                star.classList.remove('active');
            });
            document.getElementById('kata-feedback-text').value = '';
        }
        
        function updateStarRating(rating) {
            document.querySelectorAll('.kata-star').forEach(function(star, index) {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }
        
        function submitFeedback() {
            var rating = document.querySelectorAll('.kata-star.active').length;
            var feedback = document.getElementById('kata-feedback-text').value;
            
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'kata_chatbot_submit_feedback',
                    session_id: sessionId,
                    rating: rating,
                    feedback: feedback,
                    nonce: '<?php echo wp_create_nonce('kata_chatbot_nonce'); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                hideFeedbackModal();
                if (data.success) {
                    addMessage('bot', '<?php _e('Cảm ơn bạn đã đánh giá! Phản hồi của bạn rất quan trọng với chúng tôi. 😊', 'kata-chatbot'); ?>');
                }
            })
            .catch(error => {
                console.error('Feedback error:', error);
            });
        }
        
        // Check online status
        function checkOnlineStatus() {
            var statusIndicator = document.querySelector('.kata-status-indicator');
            var statusText = document.querySelector('.kata-status-text');
            
            if (navigator.onLine) {
                statusIndicator.className = 'kata-status-indicator kata-status-online';
                statusText.textContent = '<?php _e('Trực tuyến', 'kata-chatbot'); ?>';
            } else {
                statusIndicator.className = 'kata-status-indicator kata-status-offline';
                statusText.textContent = '<?php _e('Ngoại tuyến', 'kata-chatbot'); ?>';
            }
        }
        
        // Listen for online/offline events
        window.addEventListener('online', checkOnlineStatus);
        window.addEventListener('offline', checkOnlineStatus);
        
        // Load sound preference
        var savedSound = localStorage.getItem('kata_chatbot_sound');
        if (savedSound !== null) {
            soundEnabled = savedSound === 'true';
            document.getElementById('kata-sound-toggle').textContent = soundEnabled ? '🔊' : '🔇';
        }
    };
    
    // Initialize chatbot
    KataChatbot.init();
});
</script>
