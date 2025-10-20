<?php
/**
 * KATA Smart Chatbot - Admin Settings Page
 * 
 * @package KATA_SEO_Manager
 * @subpackage Admin
 */

if (!defined('ABSPATH')) exit;

// Get current settings
$enabled = get_option('kata_chatbot_enabled', true);
$auto_open = get_option('kata_chatbot_auto_open', false);
$open_delay = get_option('kata_chatbot_open_delay', 5);
$scroll_trigger = get_option('kata_chatbot_scroll_trigger', 50);
$exit_intent = get_option('kata_chatbot_exit_intent', true);
$position = get_option('kata_chatbot_position', 'bottom-right');
$primary_color = get_option('kata_chatbot_primary_color', '#042277');
$secondary_color = get_option('kata_chatbot_secondary_color', '#040B1E');
$welcome_message = get_option('kata_chatbot_welcome_message', 'Xin chào! Tôi có thể giúp gì cho bạn?');
$bot_name = get_option('kata_chatbot_bot_name', 'KATA Assistant');
$contact_phone = get_option('kata_chatbot_contact_phone', '');
$contact_email = get_option('kata_chatbot_contact_email', '');
$contact_address = get_option('kata_chatbot_contact_address', '');

// AI Settings
$ai_enabled = get_option('kata_chatbot_ai_enabled', false);
$ai_provider = get_option('kata_chatbot_ai_provider', 'openai');
$ai_api_key = get_option('kata_chatbot_ai_api_key', '');
$ai_model = get_option('kata_chatbot_ai_model', 'gpt-3.5-turbo');

// Get statistics
$chatbot = KATA_Smart_Chatbot::get_instance();
$stats = $chatbot->get_statistics(30);

// Handle form submission
if (isset($_POST['kata_chatbot_save_settings'])) {
    check_admin_referer('kata_chatbot_settings');
    
    update_option('kata_chatbot_enabled', isset($_POST['kata_chatbot_enabled']));
    update_option('kata_chatbot_auto_open', isset($_POST['kata_chatbot_auto_open']));
    update_option('kata_chatbot_open_delay', intval($_POST['kata_chatbot_open_delay']));
    update_option('kata_chatbot_scroll_trigger', intval($_POST['kata_chatbot_scroll_trigger']));
    update_option('kata_chatbot_exit_intent', isset($_POST['kata_chatbot_exit_intent']));
    update_option('kata_chatbot_position', sanitize_text_field($_POST['kata_chatbot_position']));
    update_option('kata_chatbot_primary_color', sanitize_hex_color($_POST['kata_chatbot_primary_color']));
    update_option('kata_chatbot_secondary_color', sanitize_hex_color($_POST['kata_chatbot_secondary_color']));
    update_option('kata_chatbot_welcome_message', sanitize_textarea_field($_POST['kata_chatbot_welcome_message']));
    update_option('kata_chatbot_bot_name', sanitize_text_field($_POST['kata_chatbot_bot_name']));
    update_option('kata_chatbot_contact_phone', sanitize_text_field($_POST['kata_chatbot_contact_phone']));
    update_option('kata_chatbot_contact_email', sanitize_email($_POST['kata_chatbot_contact_email']));
    update_option('kata_chatbot_contact_address', sanitize_textarea_field($_POST['kata_chatbot_contact_address']));
    
    // AI Settings
    update_option('kata_chatbot_ai_enabled', isset($_POST['kata_chatbot_ai_enabled']));
    update_option('kata_chatbot_ai_provider', sanitize_text_field($_POST['kata_chatbot_ai_provider']));
    update_option('kata_chatbot_ai_api_key', sanitize_text_field($_POST['kata_chatbot_ai_api_key']));
    update_option('kata_chatbot_ai_model', sanitize_text_field($_POST['kata_chatbot_ai_model']));
    
    echo '<div class="notice notice-success"><p>✅ Cài đặt đã được lưu thành công!</p></div>';
}
?>

<div class="wrap kata-chatbot-admin">
    <h1>
        <span class="dashicons dashicons-admin-comments"></span>
        KATA Smart Chatbot Settings
    </h1>
    
    <!-- Statistics Dashboard -->
    <div class="kata-chatbot-stats">
        <h2>📊 Thống kê (30 ngày gần nhất)</h2>
        <div class="kata-stats-grid">
            <div class="kata-stat-card">
                <div class="kata-stat-icon">💬</div>
                <div class="kata-stat-info">
                    <div class="kata-stat-value"><?php echo number_format($stats['total_chats']); ?></div>
                    <div class="kata-stat-label">Tổng cuộc trò chuyện</div>
                </div>
            </div>
            
            <div class="kata-stat-card">
                <div class="kata-stat-icon">✉️</div>
                <div class="kata-stat-info">
                    <div class="kata-stat-value"><?php echo number_format($stats['total_messages']); ?></div>
                    <div class="kata-stat-label">Tổng tin nhắn</div>
                </div>
            </div>
            
            <div class="kata-stat-card">
                <div class="kata-stat-icon">👥</div>
                <div class="kata-stat-info">
                    <div class="kata-stat-value"><?php echo number_format($stats['total_leads']); ?></div>
                    <div class="kata-stat-label">Tổng Leads</div>
                </div>
            </div>
            
            <div class="kata-stat-card">
                <div class="kata-stat-icon">🆕</div>
                <div class="kata-stat-info">
                    <div class="kata-stat-value"><?php echo number_format($stats['new_leads_today']); ?></div>
                    <div class="kata-stat-label">Leads hôm nay</div>
                </div>
            </div>
            
            <div class="kata-stat-card">
                <div class="kata-stat-icon">📈</div>
                <div class="kata-stat-info">
                    <div class="kata-stat-value"><?php echo $stats['conversion_rate']; ?>%</div>
                    <div class="kata-stat-label">Tỷ lệ chuyển đổi</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Settings Form -->
    <form method="post" action="" class="kata-chatbot-settings-form">
        <?php wp_nonce_field('kata_chatbot_settings'); ?>
        
        <div class="kata-settings-tabs">
            <button type="button" class="kata-tab-btn active" data-tab="general">
                <span class="dashicons dashicons-admin-generic"></span> Cài đặt chung
            </button>
            <button type="button" class="kata-tab-btn" data-tab="triggers">
                <span class="dashicons dashicons-controls-play"></span> Kích hoạt
            </button>
            <button type="button" class="kata-tab-btn" data-tab="appearance">
                <span class="dashicons dashicons-admin-appearance"></span> Giao diện
            </button>
            <button type="button" class="kata-tab-btn" data-tab="messages">
                <span class="dashicons dashicons-format-chat"></span> Tin nhắn
            </button>
            <button type="button" class="kata-tab-btn" data-tab="contact">
                <span class="dashicons dashicons-phone"></span> Liên hệ
            </button>
            <button type="button" class="kata-tab-btn" data-tab="ai">
                <span class="dashicons dashicons-admin-network"></span> AI Integration
            </button>
        </div>
        
        <!-- General Tab -->
        <div class="kata-tab-content active" data-tab="general">
            <h2>⚙️ Cài đặt chung</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Kích hoạt Chatbot</th>
                    <td>
                        <label>
                            <input type="checkbox" name="kata_chatbot_enabled" value="1" <?php checked($enabled); ?>>
                            Bật KATA Smart Chatbot
                        </label>
                        <p class="description">Bật/tắt chatbot trên toàn bộ website</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Tên Bot</th>
                    <td>
                        <input type="text" name="kata_chatbot_bot_name" value="<?php echo esc_attr($bot_name); ?>" class="regular-text">
                        <p class="description">Tên hiển thị của chatbot</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Vị trí hiển thị</th>
                    <td>
                        <select name="kata_chatbot_position">
                            <option value="bottom-right" <?php selected($position, 'bottom-right'); ?>>Góc dưới phải</option>
                            <option value="bottom-left" <?php selected($position, 'bottom-left'); ?>>Góc dưới trái</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Triggers Tab -->
        <div class="kata-tab-content" data-tab="triggers">
            <h2>🎯 Cài đặt kích hoạt thông minh</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Tự động mở</th>
                    <td>
                        <label>
                            <input type="checkbox" name="kata_chatbot_auto_open" value="1" <?php checked($auto_open); ?>>
                            Tự động mở chatbot
                        </label>
                        <p class="description">Chatbot sẽ tự động mở sau một khoảng thời gian</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Thời gian delay</th>
                    <td>
                        <input type="number" name="kata_chatbot_open_delay" value="<?php echo esc_attr($open_delay); ?>" min="0" max="60" class="small-text"> giây
                        <p class="description">Thời gian chờ trước khi tự động mở chatbot</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Kích hoạt khi scroll</th>
                    <td>
                        <input type="number" name="kata_chatbot_scroll_trigger" value="<?php echo esc_attr($scroll_trigger); ?>" min="0" max="100" class="small-text"> %
                        <p class="description">Mở chatbot khi người dùng scroll đến % trang</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Exit Intent</th>
                    <td>
                        <label>
                            <input type="checkbox" name="kata_chatbot_exit_intent" value="1" <?php checked($exit_intent); ?>>
                            Kích hoạt khi người dùng chuẩn bị rời khỏi trang
                        </label>
                        <p class="description">Hiện chatbot khi người dùng di chuyển chuột ra ngoài trang</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Appearance Tab -->
        <div class="kata-tab-content" data-tab="appearance">
            <h2>🎨 Giao diện</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Màu chính</th>
                    <td>
                        <input type="color" name="kata_chatbot_primary_color" value="<?php echo esc_attr($primary_color); ?>">
                        <p class="description">Màu sắc chính của chatbot (mặc định: #042277)</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Màu phụ</th>
                    <td>
                        <input type="color" name="kata_chatbot_secondary_color" value="<?php echo esc_attr($secondary_color); ?>">
                        <p class="description">Màu sắc phụ cho gradient (mặc định: #040B1E)</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Messages Tab -->
        <div class="kata-tab-content" data-tab="messages">
            <h2>💬 Tin nhắn</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Tin nhắn chào mừng</th>
                    <td>
                        <textarea name="kata_chatbot_welcome_message" rows="3" class="large-text"><?php echo esc_textarea($welcome_message); ?></textarea>
                        <p class="description">Tin nhắn đầu tiên khi người dùng mở chatbot</p>
                    </td>
                </tr>
            </table>
            
            <div class="kata-info-box">
                <h3>📝 Contextual Messages</h3>
                <p>Chatbot tự động phát hiện nội dung và gửi tin nhắn phù hợp:</p>
                <ul>
                    <li>✅ <strong>Course Schema</strong> → Gợi ý tư vấn khóa học</li>
                    <li>✅ <strong>FAQ Schema</strong> → Hỗ trợ thêm câu hỏi</li>
                    <li>✅ <strong>Article</strong> → Giới thiệu khóa học liên quan</li>
                    <li>✅ <strong>Exit Intent</strong> → Thu thập lead trước khi rời</li>
                </ul>
            </div>
        </div>
        
        <!-- Contact Tab -->
        <div class="kata-tab-content" data-tab="contact">
            <h2>📞 Thông tin liên hệ</h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Số điện thoại</th>
                    <td>
                        <input type="text" name="kata_chatbot_contact_phone" value="<?php echo esc_attr($contact_phone); ?>" class="regular-text">
                        <p class="description">Hotline để chatbot hiển thị khi người dùng hỏi</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Email</th>
                    <td>
                        <input type="email" name="kata_chatbot_contact_email" value="<?php echo esc_attr($contact_email); ?>" class="regular-text">
                        <p class="description">Email liên hệ</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Địa chỉ</th>
                    <td>
                        <textarea name="kata_chatbot_contact_address" rows="3" class="large-text"><?php echo esc_textarea($contact_address); ?></textarea>
                        <p class="description">Địa chỉ văn phòng/cơ sở</p>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- AI Tab -->
        <div class="kata-tab-content" data-tab="ai">
            <h2>🤖 AI Integration</h2>
            
            <div class="kata-warning-box">
                <strong>⚠️ Tính năng Beta</strong>
                <p>Tích hợp AI để chatbot trả lời thông minh hơn. Yêu cầu API key từ OpenAI hoặc Claude.</p>
            </div>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Kích hoạt AI</th>
                    <td>
                        <label>
                            <input type="checkbox" name="kata_chatbot_ai_enabled" value="1" <?php checked($ai_enabled); ?>>
                            Bật AI-powered responses
                        </label>
                        <p class="description">Sử dụng AI để tạo câu trả lời thông minh</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">AI Provider</th>
                    <td>
                        <select name="kata_chatbot_ai_provider">
                            <option value="openai" <?php selected($ai_provider, 'openai'); ?>>OpenAI (ChatGPT)</option>
                            <option value="claude" <?php selected($ai_provider, 'claude'); ?>>Anthropic (Claude)</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">API Key</th>
                    <td>
                        <input type="password" name="kata_chatbot_ai_api_key" value="<?php echo esc_attr($ai_api_key); ?>" class="large-text">
                        <p class="description">API key từ OpenAI hoặc Anthropic</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">Model</th>
                    <td>
                        <input type="text" name="kata_chatbot_ai_model" value="<?php echo esc_attr($ai_model); ?>" class="regular-text">
                        <p class="description">Ví dụ: gpt-3.5-turbo, gpt-4, claude-3-opus</p>
                    </td>
                </tr>
            </table>
            
            <div class="kata-info-box">
                <h3>💡 Hướng dẫn tích hợp AI</h3>
                <ol>
                    <li>Tạo API key tại <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI</a> hoặc <a href="https://console.anthropic.com/" target="_blank">Anthropic</a></li>
                    <li>Copy API key và dán vào ô trên</li>
                    <li>Chọn model phù hợp (gpt-3.5-turbo tiết kiệm chi phí, gpt-4 thông minh hơn)</li>
                    <li>Bật tính năng AI</li>
                    <li>Chatbot sẽ tự động sử dụng AI khi không có câu trả lời sẵn</li>
                </ol>
            </div>
        </div>
        
        <p class="submit">
            <button type="submit" name="kata_chatbot_save_settings" class="button button-primary button-hero">
                <span class="dashicons dashicons-yes"></span> Lưu cài đặt
            </button>
        </p>
    </form>
</div>

<style>
.kata-chatbot-admin {
    max-width: 1200px;
}

.kata-chatbot-admin h1 {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 28px;
    margin-bottom: 30px;
}

.kata-chatbot-stats {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.kata-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.kata-stat-card {
    background: linear-gradient(135deg, #042277 0%, #0630a8 100%);
    color: white;
    padding: 24px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 16px;
}

.kata-stat-icon {
    font-size: 36px;
}

.kata-stat-value {
    font-size: 32px;
    font-weight: bold;
    line-height: 1;
}

.kata-stat-label {
    font-size: 13px;
    opacity: 0.9;
    margin-top: 4px;
}

.kata-chatbot-settings-form {
    background: white;
    padding: 24px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.kata-settings-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    border-bottom: 2px solid #e0e0e0;
}

.kata-tab-btn {
    background: transparent;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #666;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 3px solid transparent;
    transition: all 0.2s;
}

.kata-tab-btn:hover {
    color: #042277;
}

.kata-tab-btn.active {
    color: #042277;
    border-bottom-color: #042277;
}

.kata-tab-content {
    display: none;
}

.kata-tab-content.active {
    display: block;
}

.kata-info-box,
.kata-warning-box {
    padding: 16px 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.kata-info-box {
    background: #e3f2fd;
    border-left: 4px solid #042277;
}

.kata-warning-box {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
}

.kata-info-box h3,
.kata-warning-box strong {
    margin-top: 0;
    color: #042277;
}

.kata-info-box ul {
    margin: 12px 0;
    padding-left: 20px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.kata-tab-btn').on('click', function() {
        const tab = $(this).data('tab');
        
        $('.kata-tab-btn').removeClass('active');
        $(this).addClass('active');
        
        $('.kata-tab-content').removeClass('active');
        $('.kata-tab-content[data-tab="' + tab + '"]').addClass('active');
    });
});
</script>
