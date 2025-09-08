<?php
/**
 * Admin Settings Template
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get current tab
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

// Get current settings
$options = get_option('kata_chatbot_options', array());
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <!-- Tab Navigation -->
    <h2 class="nav-tab-wrapper">
        <a href="?page=kata-chatbot-settings&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Chung', 'kata-chatbot'); ?>
        </a>
        <a href="?page=kata-chatbot-settings&tab=ai" class="nav-tab <?php echo $active_tab === 'ai' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Cấu hình AI', 'kata-chatbot'); ?>
        </a>
        <a href="?page=kata-chatbot-settings&tab=appearance" class="nav-tab <?php echo $active_tab === 'appearance' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Giao diện', 'kata-chatbot'); ?>
        </a>
        <a href="?page=kata-chatbot-settings&tab=advanced" class="nav-tab <?php echo $active_tab === 'advanced' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Nâng cao', 'kata-chatbot'); ?>
        </a>
    </h2>
    
    <form method="post" action="options.php">
        <?php
        settings_fields('kata_chatbot_settings');
        do_settings_sections('kata_chatbot_settings');
        ?>
        
        <div class="kata-settings-container">
            <?php if ($active_tab === 'general') : ?>
                <!-- General Settings Tab -->
                <div class="kata-settings-section">
                    <h2><?php _e('Cài đặt chung', 'kata-chatbot'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="chatbot_enabled"><?php _e('Bật Chatbot', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="chatbot_enabled" name="kata_chatbot_options[enabled]" value="1" 
                                    <?php checked(isset($options['enabled']) ? $options['enabled'] : 1, 1); ?>>
                                <p class="description"><?php _e('Bật hoặc tắt chatbot trên website của bạn', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="chatbot_title"><?php _e('Tiêu đề Chatbot', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="chatbot_title" name="kata_chatbot_options[title]" 
                                    value="<?php echo esc_attr(isset($options['title']) ? $options['title'] : 'Kata Chatbot'); ?>" 
                                    class="regular-text">
                                <p class="description"><?php _e('Tiêu đề hiển thị trong header chatbot', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="welcome_message"><?php _e('Tin nhắn chào mừng', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <textarea id="welcome_message" name="kata_chatbot_options[welcome_message]" 
                                    rows="3" class="large-text"><?php echo esc_textarea(isset($options['welcome_message']) ? $options['welcome_message'] : 'Xin chào! Tôi có thể giúp gì cho bạn?'); ?></textarea>
                                <p class="description"><?php _e('Tin nhắn chào mừng hiển thị khi mở chatbot', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
            <?php elseif ($active_tab === 'ai') : ?>
                <!-- AI Configuration Tab -->
                <div class="kata-settings-section">
                    <h2><?php _e('Cấu hình AI', 'kata-chatbot'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="ai_provider"><?php _e('Nhà cung cấp AI', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <select id="ai_provider" name="kata_chatbot_options[ai_provider]">
                                    <option value="google_ai_studio" <?php selected(isset($options['ai_provider']) ? $options['ai_provider'] : 'google_ai_studio', 'google_ai_studio'); ?>>
                                        <?php _e('Google AI Studio', 'kata-chatbot'); ?>
                                    </option>
                                    <option value="openai" <?php selected(isset($options['ai_provider']) ? $options['ai_provider'] : '', 'openai'); ?>>
                                        <?php _e('OpenAI', 'kata-chatbot'); ?>
                                    </option>
                                    <option value="anthropic" <?php selected(isset($options['ai_provider']) ? $options['ai_provider'] : '', 'anthropic'); ?>>
                                        <?php _e('Anthropic Claude', 'kata-chatbot'); ?>
                                    </option>
                                </select>
                                <p class="description"><?php _e('Chọn nhà cung cấp AI ưa thích', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="api_key"><?php _e('API Key', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="password" id="api_key" name="kata_chatbot_options[api_key]" 
                                    value="<?php echo esc_attr(isset($options['api_key']) ? $options['api_key'] : ''); ?>" 
                                    class="regular-text">
                                <p class="description"><?php _e('Nhập API key của nhà cung cấp AI', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="ai_model"><?php _e('Mô hình AI', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="ai_model" name="kata_chatbot_options[ai_model]" 
                                    value="<?php echo esc_attr(isset($options['ai_model']) ? $options['ai_model'] : 'gemini-1.5-flash'); ?>" 
                                    class="regular-text">
                                <p class="description"><?php _e('Mô hình AI sử dụng để trả lời', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
            <?php elseif ($active_tab === 'appearance') : ?>
                <!-- Appearance Tab -->
                <div class="kata-settings-section">
                    <h2><?php _e('Cài đặt giao diện', 'kata-chatbot'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="chatbot_position"><?php _e('Vị trí Chatbot', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <select id="chatbot_position" name="kata_chatbot_options[position]">
                                    <option value="bottom-right" <?php selected(isset($options['position']) ? $options['position'] : 'bottom-right', 'bottom-right'); ?>>
                                        <?php _e('Dưới bên phải', 'kata-chatbot'); ?>
                                    </option>
                                    <option value="bottom-left" <?php selected(isset($options['position']) ? $options['position'] : '', 'bottom-left'); ?>>
                                        <?php _e('Dưới bên trái', 'kata-chatbot'); ?>
                                    </option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="primary_color"><?php _e('Màu chủ đạo', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="color" id="primary_color" name="kata_chatbot_options[primary_color]" 
                                    value="<?php echo esc_attr(isset($options['primary_color']) ? $options['primary_color'] : '#007cba'); ?>">
                                <p class="description"><?php _e('Màu chủ đạo cho giao diện chatbot', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="chatbot_avatar"><?php _e('Avatar Chatbot', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="url" id="chatbot_avatar" name="kata_chatbot_options[avatar]" 
                                    value="<?php echo esc_attr(isset($options['avatar']) ? $options['avatar'] : ''); ?>" 
                                    class="regular-text">
                                <p class="description"><?php _e('URL đến hình ảnh avatar chatbot', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
            <?php elseif ($active_tab === 'advanced') : ?>
                <!-- Advanced Settings Tab -->
                <div class="kata-settings-section">
                    <h2><?php _e('Cài đặt nâng cao', 'kata-chatbot'); ?></h2>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="max_messages"><?php _e('Số tin nhắn tối đa mỗi cuộc trò chuyện', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="max_messages" name="kata_chatbot_options[max_messages]" 
                                    value="<?php echo esc_attr(isset($options['max_messages']) ? $options['max_messages'] : 50); ?>" 
                                    min="10" max="200" class="small-text">
                                <p class="description"><?php _e('Số lượng tin nhắn tối đa lưu trữ mỗi cuộc trò chuyện', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="session_timeout"><?php _e('Thời gian hết hạn phiên (phút)', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="number" id="session_timeout" name="kata_chatbot_options[session_timeout]" 
                                    value="<?php echo esc_attr(isset($options['session_timeout']) ? $options['session_timeout'] : 30); ?>" 
                                    min="5" max="1440" class="small-text">
                                <p class="description"><?php _e('Thời gian giữ cuộc trò chuyện hoạt động khi không có hoạt động', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="enable_analytics"><?php _e('Bật thống kê', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="enable_analytics" name="kata_chatbot_options[enable_analytics]" value="1" 
                                    <?php checked(isset($options['enable_analytics']) ? $options['enable_analytics'] : 1, 1); ?>>
                                <p class="description"><?php _e('Theo dõi việc sử dụng và chỉ số hiệu suất chatbot', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="debug_mode"><?php _e('Chế độ debug', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="debug_mode" name="kata_chatbot_options[debug_mode]" value="1" 
                                    <?php checked(isset($options['debug_mode']) ? $options['debug_mode'] : 0, 1); ?>>
                                <p class="description"><?php _e('Bật ghi log debug (chỉ dành cho phát triển)', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
                
            <?php endif; ?>
        </div>
        
        <?php submit_button(__('Lưu thay đổi', 'kata-chatbot')); ?>
    </form>
</div>

<style>
.kata-settings-container {
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-top: 20px;
}

.kata-settings-section {
    margin-bottom: 30px;
}

.kata-settings-section h2 {
    margin-top: 0;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #ddd;
}

.form-table th {
    width: 200px;
    padding: 15px 10px 15px 0;
}

.form-table td {
    padding: 15px 10px;
}

.nav-tab-wrapper {
    margin-bottom: 0;
}

input[type="color"] {
    width: 50px;
    height: 30px;
    padding: 0;
    border: 1px solid #ddd;
    border-radius: 3px;
}

@media (max-width: 768px) {
    .form-table th,
    .form-table td {
        display: block;
        width: 100%;
        padding: 10px 0;
    }
    
    .form-table th {
        border-bottom: none;
    }
}
</style>
