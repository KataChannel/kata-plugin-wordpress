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

// Get widget settings - try new settings first, fallback to old
$new_settings = get_option('kata_chatbot_settings', array());
$old_options = get_option('kata_chatbot_options', array());

// Use new settings if available, otherwise fallback to old
if (!empty($new_settings)) {
    $options = $new_settings;
    $position = isset($new_settings['position']) ? $new_settings['position'] : 'bottom-right';
    $enabled = isset($new_settings['enabled']) ? $new_settings['enabled'] : 1;
    $offline_mode = isset($new_settings['offline_mode']) ? $new_settings['offline_mode'] : 0;
    $offline_message = isset($new_settings['offline_message']) ? $new_settings['offline_message'] : __('Chatbot hiện đang offline. Vui lòng thử lại sau.', 'kata-chatbot');
    
    // Tab visibility from new settings
    $show_chat_tab = isset($new_settings['tab_visibility']['chat-ai']) ? $new_settings['tab_visibility']['chat-ai'] : 1;
    $show_facebook_tab = isset($new_settings['tab_visibility']['facebook']) ? $new_settings['tab_visibility']['facebook'] : 1;
    $show_zalo_tab = isset($new_settings['tab_visibility']['zalo']) ? $new_settings['tab_visibility']['zalo'] : 1;
    $show_hotline_tab = isset($new_settings['tab_visibility']['hotline']) ? $new_settings['tab_visibility']['hotline'] : 1;
} else {
    $options = $old_options;
    $position = get_option('kata_chatbot_position', 'bottom-right');
    $enabled = isset($old_options['enabled']) ? $old_options['enabled'] : 1;
    $offline_mode = get_option('kata_chatbot_offline_mode', 0);
    $offline_message = get_option('kata_chatbot_offline_message', __('Chatbot hiện đang offline. Vui lòng thử lại sau.', 'kata-chatbot'));
    
    // Tab visibility from old settings
    $show_chat_tab = isset($old_options['show_chat_tab']) ? $old_options['show_chat_tab'] : 1;
    $show_facebook_tab = isset($old_options['show_facebook_tab']) ? $old_options['show_facebook_tab'] : 1;
    $show_zalo_tab = isset($old_options['show_zalo_tab']) ? $old_options['show_zalo_tab'] : 1;
    $show_hotline_tab = isset($old_options['show_hotline_tab']) ? $old_options['show_hotline_tab'] : 1;
}

$theme = get_option('kata_chatbot_theme', 'blue');
$welcome_message = get_option('kata_chatbot_welcome_message', __('Xin chào! Tôi có thể giúp gì cho bạn? 😊', 'kata-chatbot'));

// Map new admin setting names to widget tab names
$admin_default_tab = isset($options['default_tab']) ? $options['default_tab'] : 'chat-ai';
$tab_mapping = array(
    'chat-ai' => 'chat',
    'default' => 'chat', // Default tab also maps to chat
    'facebook' => 'facebook',
    'zalo' => 'zalo',
    'hotline' => 'hotline'
);
$default_tab = isset($tab_mapping[$admin_default_tab]) ? $tab_mapping[$admin_default_tab] : 'chat';

// Get branches for contact options
$branch_handler = new KataChatbot_Branch_Handler();

// Ensure branch table exists and has data
$branch_handler->check_and_repair_table();

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
        <div class="kata-status-indicator <?php echo $offline_mode ? 'kata-status-offline' : 'kata-status-online'; ?>"></div>
    </div>

    <!-- Chat Window -->
    <div id="kata-chat-window" class="kata-chat-window">
        <!-- Close Button -->
        <button id="kata-window-close" class="kata-window-close" title="<?php _e('Đóng', 'kata-chatbot'); ?>">
            <svg viewBox="0 0 24 24" width="16" height="16">
                <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        </button>
        
        <!-- Main Wrapper: Content Left, Tabs Right -->
        <div class="kata-chat-main-wrapper">
            <!-- Content Area (Left) -->
            <div class="kata-chat-content-area">
                <!-- Tab Contents -->
                <?php if ($show_chat_tab) : ?>
                <!-- AI Chat Tab -->
                <div id="kata-tab-chat" class="kata-tab-content <?php echo ($default_tab === 'chat') ? 'active' : ''; ?>">
            <!-- Contact Header (giống style các tab khác) -->
            <div class="kata-contact-header">
                <div class="kata-contact-icon">
                    <svg viewBox="0 0 24 24" width="32" height="32">
                        <path fill="#4A90E2" d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                    </svg>
                </div>
                <div class="kata-contact-title">
                    <h3><?php echo esc_html(get_option('kata_chatbot_name', __('Chat với AI Assistant', 'kata-chatbot'))); ?></h3>
                    <p><?php echo $offline_mode ? __('Hiện đang offline - không thể chat', 'kata-chatbot') : __('Trợ lý AI 24/7 - Sẵn sàng hỗ trợ bạn', 'kata-chatbot'); ?></p>
                </div>
            </div>
            
            <?php if ($offline_mode) : ?>
            <!-- Offline Mode Display -->
            <div class="kata-branch-list">
                <div class="kata-no-contact">
                    <div style="font-size: 48px; margin-bottom: 16px;">⏰</div>
                    <h4 style="margin: 0 0 8px 0; color: #666;"><?php _e('AI Assistant đang offline', 'kata-chatbot'); ?></h4>
                    <p style="margin: 0; color: #888;"><?php echo esc_html($offline_message); ?></p>
                    <div style="margin-top: 20px;">
                        <p style="font-size: 12px; color: #999; margin: 0;">
                            <?php _e('Vui lòng sử dụng các kênh liên hệ khác hoặc thử lại sau.', 'kata-chatbot'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php else : ?>
            <!-- Chat Interface -->
            <div class="kata-chat-interface">
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
                    
                    <!-- Quick Actions (giống suggested actions nhưng style như contact buttons) -->
                    <div class="kata-quick-actions">
                        <div class="kata-quick-action-item">
                            <button class="kata-contact-btn kata-suggestion-btn" 
                                    data-message="<?php _e('Tôi muốn tìm hiểu về các khóa học thẩm mỹ', 'kata-chatbot'); ?>"
                                    style="background: #4A90E2; color: white;">
                                <svg viewBox="0 0 24 24" width="16" height="16">
                                    <path fill="currentColor" d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19ZM17 12H7V10H17V12ZM17 9H7V7H17V9ZM17 15H7V13H17V15Z"/>
                                </svg>
                                <?php _e('Khóa học', 'kata-chatbot'); ?>
                            </button>
                        </div>
                        <div class="kata-quick-action-item">
                            <button class="kata-contact-btn kata-suggestion-btn" 
                                    data-message="<?php _e('Tôi cần tư vấn về lộ trình học phù hợp', 'kata-chatbot'); ?>"
                                    style="background: #28a745; color: white;">
                                <svg viewBox="0 0 24 24" width="16" height="16">
                                    <path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12S6.48 22 12 22 22 17.52 22 12 17.52 2 12 2ZM13 17H11V15H13V17ZM13 13H11V7H13V13Z"/>
                                </svg>
                                <?php _e('Tư vấn', 'kata-chatbot'); ?>
                            </button>
                        </div>
                        <div class="kata-quick-action-item">
                            <button class="kata-contact-btn kata-suggestion-btn" 
                                    data-message="<?php _e('Học phí và khuyến mãi hiện tại như thế nào?', 'kata-chatbot'); ?>"
                                    style="background: #ffc107; color: #333;">
                                <svg viewBox="0 0 24 24" width="16" height="16">
                                    <path fill="currentColor" d="M12 2L13.09 8.26L22 9L17 14L18.18 23L12 19.5L5.82 23L7 14L2 9L10.91 8.26L12 2Z"/>
                                </svg>
                                <?php _e('Học phí', 'kata-chatbot'); ?>
                            </button>
                        </div>
                        <div class="kata-quick-action-item">
                            <button class="kata-contact-btn kata-suggestion-btn" 
                                    data-message="<?php _e('Tôi muốn đăng ký tham quan cơ sở', 'kata-chatbot'); ?>"
                                    style="background: #e83e8c; color: white;">
                                <svg viewBox="0 0 24 24" width="16" height="16">
                                    <path fill="currentColor" d="M12 2L13.09 8.26L22 9L17 14L18.18 23L12 19.5L5.82 23L7 14L2 9L10.91 8.26L12 2Z"/>
                                </svg>
                                <?php _e('Tham quan', 'kata-chatbot'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Chat Input -->
                <div class="kata-chat-input">
                    <div class="kata-input-group">
                        <textarea id="kata-message-input" 
                                  placeholder="<?php _e('Nhập câu hỏi của bạn...', 'kata-chatbot'); ?>" 
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
                        <span class="kata-typing-text"><?php _e('AI đang suy nghĩ...', 'kata-chatbot'); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($show_facebook_tab) : ?>
        <!-- Facebook Tab -->
        <div id="kata-tab-facebook" class="kata-tab-content <?php echo ($default_tab === 'facebook') ? 'active' : ''; ?>">
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
        <?php endif; ?>

        <?php if ($show_zalo_tab) : ?>
        <!-- Zalo Tab -->
        <div id="kata-tab-zalo" class="kata-tab-content <?php echo ($default_tab === 'zalo') ? 'active' : ''; ?>">
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
        <?php endif; ?>

        <?php if ($show_hotline_tab) : ?>
        <!-- Hotline Tab -->
        <div id="kata-tab-hotline" class="kata-tab-content <?php echo ($default_tab === 'hotline') ? 'active' : ''; ?>">
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
        <?php endif; ?>
            </div> <!-- End .kata-chat-content-area -->
            
            <!-- Tab Navigation - Vertical on Right Side -->
            <div class="kata-chat-tabs">
                <div class="kata-tab-nav">
                    <?php if ($show_chat_tab) : ?>
                    <button class="kata-tab-btn <?php echo ($default_tab === 'chat') ? 'active' : ''; ?>" data-tab="chat">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/>
                        </svg>
                        <span><?php _e('Chat', 'kata-chatbot'); ?></span>
                    </button>
                    <?php endif; ?>
                    
                    <?php if ($show_facebook_tab) : ?>
                    <button class="kata-tab-btn <?php echo ($default_tab === 'facebook') ? 'active' : ''; ?>" data-tab="facebook">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span><?php _e('FB', 'kata-chatbot'); ?></span>
                    </button>
                    <?php endif; ?>
                    
                    <?php if ($show_zalo_tab) : ?>
                    <button class="kata-tab-btn <?php echo ($default_tab === 'zalo') ? 'active' : ''; ?>" data-tab="zalo">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.243 13.243a1 1 0 01-.707.293H8.464a1 1 0 01-.707-1.707L11.293 10.293a1 1 0 011.414 0l3.536 3.536a1 1 0 010 1.414z"/>
                        </svg>
                        <span><?php _e('Zalo', 'kata-chatbot'); ?></span>
                    </button>
                    <?php endif; ?>
                    
                    <?php if ($show_hotline_tab) : ?>
                    <button class="kata-tab-btn <?php echo ($default_tab === 'hotline') ? 'active' : ''; ?>" data-tab="hotline">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path fill="currentColor" d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                        </svg>
                        <span><?php _e('Call', 'kata-chatbot'); ?></span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div> <!-- End .kata-chat-main-wrapper -->
    </div> <!-- End .kata-chat-window -->
</div> <!-- End .kata-chatbot-container -->

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
    display: none !important;
    height: 400px;
    overflow-y: auto;
    animation: fadeIn 0.3s ease-in-out;
}

.kata-tab-content.active {
    display: block !important;
}

/* Chat tab specific - use flex when active */
#kata-tab-chat.active {
    display: flex !important;
    flex-direction: column;
}

/* Force hide when not active */
#kata-tab-chat:not(.active) {
    display: none !important;
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

/* Chat Interface */
.kata-chat-interface {
    display: flex;
    flex-direction: column;
    height: 100%;
    flex: 1;
}

#kata-tab-chat .kata-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
}

#kata-tab-chat .kata-chat-input {
    flex-shrink: 0;
    padding: 15px;
    background: white;
}

/* Quick Actions Styling */
.kata-quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin: 16px 0;
    padding: 0 8px;
}

.kata-quick-action-item .kata-contact-btn {
    width: 100%;
    justify-content: center;
    padding: 12px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.kata-quick-action-item .kata-contact-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.kata-quick-action-item .kata-contact-btn svg {
    margin-right: 6px;
}

/* Chat Message Styling */
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
    flex-shrink: 0;
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
    background: white;
    padding: 12px 16px;
    border-radius: 18px;
    margin-bottom: 4px;
    word-wrap: break-word;
    line-height: 1.4;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.kata-user-message .kata-message-bubble {
    background: #4A90E2;
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

/* Input Group Styling */
.kata-input-group {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    background: #f8f9fa;
    border-radius: 20px;
    padding: 8px 12px;
    border: 1px solid #e0e0e0;
}

#kata-message-input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    resize: none;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.4;
    max-height: 80px;
    min-height: 20px;
}

.kata-send-btn {
    background: #4A90E2;
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: white;
    flex-shrink: 0;
}

.kata-send-btn:hover {
    background: #357ABD;
    transform: scale(1.05);
}

.kata-send-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
}

/* Typing Indicator */
.kata-typing-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
    font-size: 12px;
    color: #666;
}

.kata-typing-dots {
    display: flex;
    gap: 4px;
}

.kata-typing-dots span {
    width: 6px;
    height: 6px;
    background: #4A90E2;
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
    
    /* Quick Actions Mobile Layout */
    .kata-quick-actions {
        grid-template-columns: 1fr;
        gap: 8px;
        margin: 12px 0;
        padding: 0 4px;
    }
    
    .kata-quick-action-item .kata-contact-btn {
        padding: 10px 8px;
        font-size: 12px;
    }
    
    .kata-chat-window {
        width: calc(100vw - 20px);
        height: calc(100vh - 100px);
        bottom: 80px;
        right: 10px;
        left: 10px;
    }
    
    #kata-tab-chat .kata-chat-messages {
        padding: 10px;
    }
    
    #kata-tab-chat .kata-chat-input {
        padding: 10px;
    }
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
        
        // Force hide all tab contents first
        $('.kata-tab-content').css('display', 'none');
        
        // Add active class to clicked tab and its content
        $(this).addClass('active');
        $('#kata-tab-' + tab).addClass('active');
        
        // Force show the selected tab with proper display type
        if (tab === 'chat') {
            $('#kata-tab-chat').css('display', 'flex');
        } else {
            $('#kata-tab-' + tab).css('display', 'block');
        }
        
        // Track tab switching for analytics
        if (typeof kataTracking !== 'undefined') {
            kataTracking.trackEvent('tab_switch', tab);
        }
    });
    
    // Initialize default tab
    var defaultTab = '<?php echo esc_js($default_tab); ?>';
    
    // Force initial state
    $('.kata-tab-content').removeClass('active').css('display', 'none');
    $('.kata-tab-btn').removeClass('active');
    
    // Set default tab
    $('#kata-tab-' + defaultTab).addClass('active');
    $('.kata-tab-btn[data-tab="' + defaultTab + '"]').addClass('active');
    
    // Show default tab with proper display
    if (defaultTab === 'chat') {
        $('#kata-tab-chat').css('display', 'flex');
    } else {
        $('#kata-tab-' + defaultTab).css('display', 'block');
    }
    
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
        
        <!-- Chat Footer -->
        <div class="kata-chat-footer">
            <div class="kata-footer-text">
                <?php printf(__('Hỗ trợ bởi %s', 'kata-chatbot'), '<strong>Timona AI</strong>'); ?>
            </div>
            
            <div class="kata-footer-actions">
                <button id="kata-sound-toggle" class="kata-footer-btn" title="<?php _e('Bật/tắt âm thanh', 'kata-chatbot'); ?>">
                    🔊
                </button>
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

/* Quick Actions and Suggestion Buttons */
.kata-quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin: 16px 0;
    padding: 0 8px;
}

.kata-quick-action-item .kata-contact-btn {
    width: 100%;
    justify-content: center;
    padding: 12px 8px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.kata-quick-action-item .kata-contact-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    text-decoration: none;
}

.kata-quick-action-item .kata-contact-btn svg {
    margin-right: 6px;
    flex-shrink: 0;
}

/* Legacy suggestion buttons for compatibility */
.kata-suggestion-btn {
    background: #f8f9fa;
    border: 1px solid #4A90E2;
    color: #4A90E2;
    padding: 8px 12px;
    border-radius: 16px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
}

.kata-suggestion-btn:hover {
    background: #4A90E2;
    color: white;
    transform: translateY(-1px);
}

/* Suggested Actions (for dynamic suggestions) */
.kata-suggested-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 16px 0;
    padding: 0 8px;
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
</style>

<!-- JavaScript -->
<script>
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    window.KataChatbot = new function() {
        var self = this;
        var isOpen = false;
        var currentTab = '<?php echo esc_js($default_tab); ?>';
        var sessionId = null;
        var messageHistory = [];
        var soundEnabled = <?php echo get_option('kata_chatbot_sound_enabled', true) ? 'true' : 'false'; ?>;
        var offlineMode = <?php echo $offline_mode ? 'true' : 'false'; ?>;
        var offlineMessage = '<?php echo esc_js($offline_message); ?>';
        
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
            
            // Suggestion buttons
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('kata-suggestion-btn')) {
                    var message = e.target.getAttribute('data-message');
                    sendUserMessage(message);
                }
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
            var container = document.getElementById('kata-chatbot-container');
            if (container) {
                container.classList.add('kata-chat-open');
            }
            
            // Focus input with delay to ensure it's visible
            setTimeout(function() {
                var messageInput = document.getElementById('kata-message-input');
                if (messageInput && currentTab === 'chat') {
                    messageInput.focus();
                }
            }, 100);
            
            hideNotificationBadge();
        }
        
        // Close chat
        function closeChat() {
            isOpen = false;
            var container = document.getElementById('kata-chatbot-container');
            if (container) {
                container.classList.remove('kata-chat-open');
            }
            hideEmojiPicker();
        }
        
        // Minimize chat
        function minimizeChat() {
            closeChat();
        }
        
        // Send message
        function sendMessage() {
            // Check if offline mode is enabled
            if (offlineMode) {
                addMessage('bot', offlineMessage);
                return;
            }
            
            var input = document.getElementById('kata-message-input');
            var message = input.value.trim();
            
            if (!message) return;
            
            // Clear input
            input.value = '';
            autoResizeTextarea();
            
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
            if (badge) {
                badge.style.display = 'none';
                var span = badge.querySelector('span');
                if (span) {
                    span.textContent = '1';
                }
            }
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
        
        // Check online status
        function checkOnlineStatus() {
            var statusIndicator = document.querySelector('.kata-status-indicator');
            var statusText = document.querySelector('.kata-status-text');
            
            if (statusIndicator) {
                if (navigator.onLine) {
                    statusIndicator.className = 'kata-status-indicator kata-status-online';
                } else {
                    statusIndicator.className = 'kata-status-indicator kata-status-offline';
                }
            }
            
            if (statusText) {
                if (navigator.onLine) {
                    statusText.textContent = '<?php _e('Trực tuyến', 'kata-chatbot'); ?>';
                } else {
                    statusText.textContent = '<?php _e('Ngoại tuyến', 'kata-chatbot'); ?>';
                }
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
