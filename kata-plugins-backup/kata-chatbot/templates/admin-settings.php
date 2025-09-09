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

// Get current settings - try new format first, fallback to old
$new_settings = get_option('kata_chatbot_settings', array());
$old_options = get_option('kata_chatbot_options', array());

// Use new settings if available, otherwise fallback to old
if (!empty($new_settings)) {
    $options = $new_settings;
} else {
    $options = $old_options;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php 
    // Show update message if redirected after save
    if (isset($_GET['updated']) && $_GET['updated'] == '1') {
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Cài đặt đã được lưu thành công!', 'kata-chatbot') . '</p></div>';
    }
    
    // Show repair branches result
    if (isset($_GET['repair_result'])) {
        if ($_GET['repair_result'] == 'success') {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('Chi nhánh đã được sửa chữa thành công!', 'kata-chatbot') . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . __('Không thể sửa chữa chi nhánh. Vui lòng kiểm tra log lỗi.', 'kata-chatbot') . '</p></div>';
        }
    }
    
    settings_errors(); 
    ?>
    
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
    
    <form method="post" action="">
        <?php wp_nonce_field('kata_chatbot_save_settings', 'kata_chatbot_nonce'); ?>
        
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
                                <input type="checkbox" id="chatbot_enabled" name="enabled" value="1" 
                                    <?php checked(isset($options['enabled']) ? $options['enabled'] : 1, 1); ?>>
                                <p class="description"><?php _e('Bật hoặc tắt chatbot trên website của bạn', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="offline_mode"><?php _e('Chế độ Offline', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" id="offline_mode" name="offline_mode" value="1" 
                                    <?php checked(isset($options['offline_mode']) ? $options['offline_mode'] : 0, 1); ?>>
                                <p class="description"><?php _e('Bật chế độ offline khi chatbot không thể hoạt động', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="offline_message"><?php _e('Tin nhắn Offline', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <textarea id="offline_message" name="offline_message" 
                                    rows="3" class="large-text"><?php echo esc_textarea(isset($options['offline_message']) ? $options['offline_message'] : __('Chatbot hiện đang offline. Vui lòng thử lại sau.', 'kata-chatbot')); ?></textarea>
                                <p class="description"><?php _e('Tin nhắn hiển thị khi chatbot ở chế độ offline', 'kata-chatbot'); ?></p>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="chatbot_title"><?php _e('Tiêu đề Chatbot', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="chatbot_title" name="title" 
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
                                <textarea id="welcome_message" name="welcome_message" 
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
                    
                    <h3><?php _e('Cài đặt hiển thị Tab', 'kata-chatbot'); ?></h3>
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label><?php _e('Tab hiển thị', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e('Chọn tab hiển thị', 'kata-chatbot'); ?></span>
                                    </legend>
                                    
                                    <label for="show_chat_tab">
                                        <input type="checkbox" id="show_chat_tab" name="kata_chatbot_options[show_chat_tab]" value="1" 
                                            <?php checked(isset($options['show_chat_tab']) ? $options['show_chat_tab'] : 1, 1); ?>>
                                        <span class="kata-tab-icon">💬</span>
                                        <?php _e('Tab Chat AI', 'kata-chatbot'); ?>
                                    </label><br>
                                    
                                    <label for="show_facebook_tab">
                                        <input type="checkbox" id="show_facebook_tab" name="kata_chatbot_options[show_facebook_tab]" value="1" 
                                            <?php checked(isset($options['show_facebook_tab']) ? $options['show_facebook_tab'] : 1, 1); ?>>
                                        <span class="kata-tab-icon" style="color: #1877F2;">📘</span>
                                        <?php _e('Tab Facebook', 'kata-chatbot'); ?>
                                    </label><br>
                                    
                                    <label for="show_zalo_tab">
                                        <input type="checkbox" id="show_zalo_tab" name="kata_chatbot_options[show_zalo_tab]" value="1" 
                                            <?php checked(isset($options['show_zalo_tab']) ? $options['show_zalo_tab'] : 1, 1); ?>>
                                        <span class="kata-tab-icon" style="color: #0068FF;">💙</span>
                                        <?php _e('Tab Zalo', 'kata-chatbot'); ?>
                                    </label><br>
                                    
                                    <label for="show_hotline_tab">
                                        <input type="checkbox" id="show_hotline_tab" name="kata_chatbot_options[show_hotline_tab]" value="1" 
                                            <?php checked(isset($options['show_hotline_tab']) ? $options['show_hotline_tab'] : 1, 1); ?>>
                                        <span class="kata-tab-icon" style="color: #00C851;">📞</span>
                                        <?php _e('Tab Hotline', 'kata-chatbot'); ?>
                                    </label><br>
                                    
                                    <p class="description">
                                        <?php _e('Chọn các tab bạn muốn hiển thị trong chatbot widget. Ít nhất một tab phải được chọn.', 'kata-chatbot'); ?>
                                    </p>
                                </fieldset>
                            </td>
                        </tr>
                        
                        <tr>
                            <th scope="row">
                                <label for="default_tab"><?php _e('Tab mặc định', 'kata-chatbot'); ?></label>
                            </th>
                            <td>
                                <select id="default_tab" name="kata_chatbot_options[default_tab]">
                                    <option value="chat" <?php selected(isset($options['default_tab']) ? $options['default_tab'] : 'chat', 'chat'); ?>>
                                        <?php _e('Chat AI', 'kata-chatbot'); ?>
                                    </option>
                                    <option value="facebook" <?php selected(isset($options['default_tab']) ? $options['default_tab'] : '', 'facebook'); ?>>
                                        <?php _e('Facebook', 'kata-chatbot'); ?>
                                    </option>
                                    <option value="zalo" <?php selected(isset($options['default_tab']) ? $options['default_tab'] : '', 'zalo'); ?>>
                                        <?php _e('Zalo', 'kata-chatbot'); ?>
                                    </option>
                                    <option value="hotline" <?php selected(isset($options['default_tab']) ? $options['default_tab'] : '', 'hotline'); ?>>
                                        <?php _e('Hotline', 'kata-chatbot'); ?>
                                    </option>
                                </select>
                                <p class="description"><?php _e('Tab sẽ được hiển thị đầu tiên khi mở chatbot', 'kata-chatbot'); ?></p>
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
    
    <!-- Debug Tools -->
    <div class="kata-debug-tools" style="margin-top: 20px; padding: 20px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
        <h3><?php _e('Công cụ Debug & Sửa chữa', 'kata-chatbot'); ?></h3>
        <p><?php _e('Sử dụng các công cụ này khi gặp vấn đề với plugin.', 'kata-chatbot'); ?></p>
        
        <a href="<?php echo wp_nonce_url(add_query_arg('kata_repair_branches', '1'), 'kata_repair_branches'); ?>" 
           class="button button-secondary" 
           onclick="return confirm('<?php _e('Bạn có chắc muốn sửa chữa bảng chi nhánh?', 'kata-chatbot'); ?>')">
            <?php _e('🔧 Sửa chữa Chi nhánh', 'kata-chatbot'); ?>
        </a>
        
        <p class="description" style="margin-top: 10px;">
            <?php _e('Kiểm tra và tạo lại bảng chi nhánh nếu bị thiếu hoặc lỗi. Sử dụng khi triển khai lên server và không tạo được chi nhánh.', 'kata-chatbot'); ?>
        </p>
    </div>
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

/* Tab Settings Styling */
.kata-tab-icon {
    display: inline-block;
    width: 20px;
    font-size: 16px;
    margin-right: 8px;
}

fieldset label {
    display: flex;
    align-items: center;
    padding: 8px 0;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

fieldset label:hover {
    background-color: #f0f0f1;
    padding-left: 10px;
    border-radius: 4px;
}

fieldset input[type="checkbox"] {
    margin-right: 12px;
}

fieldset label:has(input:checked) {
    color: #0073aa;
    font-weight: 600;
}

.kata-tab-preview {
    margin-top: 15px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 5px;
    border-left: 4px solid #0073aa;
}

.kata-tab-preview h4 {
    margin: 0 0 10px 0;
    color: #23282d;
}

.kata-tab-preview-tabs {
    display: flex;
    gap: 5px;
    margin-bottom: 10px;
}

.kata-tab-preview-tab {
    padding: 8px 12px;
    background: #e0e0e0;
    color: #666;
    font-size: 12px;
    border-radius: 3px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.kata-tab-preview-tab.active {
    background: #0073aa;
    color: white;
}

.kata-tab-preview-tab.hidden {
    display: none;
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
    
    fieldset label {
        font-size: 14px;
    }
    
    .kata-tab-preview-tabs {
        flex-wrap: wrap;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab visibility controls
    const tabCheckboxes = {
        chat: document.getElementById('show_chat_tab'),
        facebook: document.getElementById('show_facebook_tab'),
        zalo: document.getElementById('show_zalo_tab'),
        hotline: document.getElementById('show_hotline_tab')
    };
    
    const defaultTabSelect = document.getElementById('default_tab');
    
    // Update default tab options based on visible tabs
    function updateDefaultTabOptions() {
        const currentValue = defaultTabSelect.value;
        defaultTabSelect.innerHTML = '';
        
        const tabLabels = {
            chat: '<?php _e('Chat AI', 'kata-chatbot'); ?>',
            facebook: '<?php _e('Facebook', 'kata-chatbot'); ?>',
            zalo: '<?php _e('Zalo', 'kata-chatbot'); ?>',
            hotline: '<?php _e('Hotline', 'kata-chatbot'); ?>'
        };
        
        let hasVisibleTabs = false;
        let firstVisibleTab = null;
        
        Object.keys(tabCheckboxes).forEach(function(tabKey) {
            if (tabCheckboxes[tabKey].checked) {
                hasVisibleTabs = true;
                if (!firstVisibleTab) firstVisibleTab = tabKey;
                
                const option = document.createElement('option');
                option.value = tabKey;
                option.textContent = tabLabels[tabKey];
                option.selected = (currentValue === tabKey);
                defaultTabSelect.appendChild(option);
            }
        });
        
        // If current selected tab is not visible, select first visible tab
        if (hasVisibleTabs && !defaultTabSelect.querySelector('option[value="' + currentValue + '"]')) {
            defaultTabSelect.value = firstVisibleTab;
        }
        
        // Show warning if no tabs are selected
        showTabWarning(!hasVisibleTabs);
        updateTabPreview();
    }
    
    // Show/hide warning message
    function showTabWarning(show) {
        let warning = document.getElementById('kata-tab-warning');
        if (show && !warning) {
            warning = document.createElement('div');
            warning.id = 'kata-tab-warning';
            warning.className = 'notice notice-warning';
            warning.innerHTML = '<p><strong><?php _e('Cảnh báo:', 'kata-chatbot'); ?></strong> <?php _e('Bạn phải chọn ít nhất một tab để hiển thị.', 'kata-chatbot'); ?></p>';
            defaultTabSelect.parentNode.insertBefore(warning, defaultTabSelect.nextSibling);
        } else if (!show && warning) {
            warning.remove();
        }
    }
    
    // Update tab preview
    function updateTabPreview() {
        let preview = document.getElementById('kata-tab-preview');
        if (!preview) {
            preview = document.createElement('div');
            preview.id = 'kata-tab-preview';
            preview.className = 'kata-tab-preview';
            preview.innerHTML = `
                <h4><?php _e('Xem trước tab:', 'kata-chatbot'); ?></h4>
                <div class="kata-tab-preview-tabs"></div>
                <p><em><?php _e('Đây là cách các tab sẽ hiển thị trong chatbot widget', 'kata-chatbot'); ?></em></p>
            `;
            defaultTabSelect.parentNode.appendChild(preview);
        }
        
        const previewTabs = preview.querySelector('.kata-tab-preview-tabs');
        previewTabs.innerHTML = '';
        
        const tabIcons = {
            chat: '💬',
            facebook: '📘',
            zalo: '💙', 
            hotline: '📞'
        };
        
        const tabLabels = {
            chat: '<?php _e('Chat AI', 'kata-chatbot'); ?>',
            facebook: '<?php _e('Facebook', 'kata-chatbot'); ?>',
            zalo: '<?php _e('Zalo', 'kata-chatbot'); ?>',
            hotline: '<?php _e('Hotline', 'kata-chatbot'); ?>'
        };
        
        const defaultTab = defaultTabSelect.value;
        
        Object.keys(tabCheckboxes).forEach(function(tabKey) {
            if (tabCheckboxes[tabKey].checked) {
                const tabDiv = document.createElement('div');
                tabDiv.className = 'kata-tab-preview-tab' + (tabKey === defaultTab ? ' active' : '');
                tabDiv.innerHTML = `<span>${tabIcons[tabKey]}</span> ${tabLabels[tabKey]}`;
                previewTabs.appendChild(tabDiv);
            }
        });
    }
    
    // Bind events to checkboxes
    Object.values(tabCheckboxes).forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // Prevent unchecking all tabs
            const checkedTabs = Object.values(tabCheckboxes).filter(cb => cb.checked);
            if (checkedTabs.length === 0) {
                this.checked = true;
                alert('<?php _e('Bạn phải giữ ít nhất một tab được chọn.', 'kata-chatbot'); ?>');
                return;
            }
            updateDefaultTabOptions();
        });
    });
    
    // Bind event to default tab select
    defaultTabSelect.addEventListener('change', updateTabPreview);
    
    // Initialize
    updateDefaultTabOptions();
    
    // Form validation before submit
    document.querySelector('form').addEventListener('submit', function(e) {
        const checkedTabs = Object.values(tabCheckboxes).filter(cb => cb.checked);
        if (checkedTabs.length === 0) {
            e.preventDefault();
            alert('<?php _e('Vui lòng chọn ít nhất một tab để hiển thị.', 'kata-chatbot'); ?>');
            return false;
        }
    });
});
</script>
