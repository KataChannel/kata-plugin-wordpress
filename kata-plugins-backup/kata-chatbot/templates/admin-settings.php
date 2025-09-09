<?php
/**
 * Admin Settings Template - Single Page Version
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

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

    <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=' . $_GET['page'])); ?>">
        <?php wp_nonce_field('kata_chatbot_save_settings', 'kata_chatbot_nonce'); ?>
        
        <div class="kata-settings-container">
            <!-- General Settings Section -->
            <div class="kata-settings-section">
                <h2><?php _e('🔧 Cài đặt chung', 'kata-chatbot'); ?></h2>
                
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

            <!-- AI Configuration Section -->
            <div class="kata-settings-section">
                <h2><?php _e('🤖 Cấu hình AI', 'kata-chatbot'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ai_assistant_enabled"><?php _e('Bật AI Assistant', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="ai_assistant_enabled" name="ai_assistant_enabled" value="1" 
                                <?php checked(isset($options['ai_assistant_enabled']) ? $options['ai_assistant_enabled'] : 1, 1); ?>>
                            <p class="description"><?php _e('Bật trợ lý AI để trả lời tin nhắn tự động', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ai_model"><?php _e('Mô hình AI', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <select id="ai_model" name="ai_model" class="regular-text">
                                <option value="gpt-3.5-turbo" <?php selected(isset($options['ai_model']) ? $options['ai_model'] : 'gpt-3.5-turbo', 'gpt-3.5-turbo'); ?>>
                                    <?php _e('GPT-3.5 Turbo', 'kata-chatbot'); ?>
                                </option>
                                <option value="gpt-4" <?php selected(isset($options['ai_model']) ? $options['ai_model'] : '', 'gpt-4'); ?>>
                                    <?php _e('GPT-4', 'kata-chatbot'); ?>
                                </option>
                                <option value="gemini-pro" <?php selected(isset($options['ai_model']) ? $options['ai_model'] : '', 'gemini-pro'); ?>>
                                    <?php _e('Gemini Pro', 'kata-chatbot'); ?>
                                </option>
                            </select>
                            <p class="description"><?php _e('Chọn mô hình AI để sử dụng cho trả lời', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ai_api_key"><?php _e('API Key', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="password" id="ai_api_key" name="ai_api_key" 
                                value="<?php echo esc_attr(isset($options['ai_api_key']) ? $options['ai_api_key'] : ''); ?>" 
                                class="regular-text">
                            <p class="description"><?php _e('Nhập API key của nhà cung cấp AI (OpenAI, Google AI Studio)', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ai_temperature"><?php _e('AI Temperature', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="ai_temperature" name="ai_temperature" 
                                value="<?php echo esc_attr(isset($options['ai_temperature']) ? $options['ai_temperature'] : 0.7); ?>" 
                                min="0" max="2" step="0.1" class="small-text">
                            <p class="description"><?php _e('Điều chỉnh độ sáng tạo của AI (0.0 - 2.0). Thấp = chính xác, cao = sáng tạo', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="ai_max_tokens"><?php _e('Max Tokens', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="ai_max_tokens" name="ai_max_tokens" 
                                value="<?php echo esc_attr(isset($options['ai_max_tokens']) ? $options['ai_max_tokens'] : 150); ?>" 
                                min="50" max="4000" class="small-text">
                            <p class="description"><?php _e('Số token tối đa cho mỗi phản hồi AI', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Appearance Settings Section -->
            <div class="kata-settings-section">
                <h2><?php _e('🎨 Cài đặt giao diện', 'kata-chatbot'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="chatbot_position"><?php _e('Vị trí Chatbot', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <select id="chatbot_position" name="position">
                                <option value="bottom-right" <?php selected(isset($options['position']) ? $options['position'] : 'bottom-right', 'bottom-right'); ?>>
                                    <?php _e('Dưới bên phải', 'kata-chatbot'); ?>
                                </option>
                                <option value="bottom-left" <?php selected(isset($options['position']) ? $options['position'] : '', 'bottom-left'); ?>>
                                    <?php _e('Dưới bên trái', 'kata-chatbot'); ?>
                                </option>
                            </select>
                            <p class="description"><?php _e('Chọn vị trí hiển thị chatbot trên website', 'kata-chatbot'); ?></p>
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
                                <label>
                                    <input type="checkbox" name="tab_visibility[chat-ai]" value="1" 
                                        <?php checked(isset($options['tab_visibility']['chat-ai']) ? $options['tab_visibility']['chat-ai'] : 1, 1); ?>>
                                    <?php _e('Chat AI', 'kata-chatbot'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="tab_visibility[default]" value="1" 
                                        <?php checked(isset($options['tab_visibility']['default']) ? $options['tab_visibility']['default'] : 1, 1); ?>>
                                    <?php _e('Default', 'kata-chatbot'); ?>
                                    <small style="color: #666;"><?php _e('(Tự động hiển thị khi Chat AI được bật)', 'kata-chatbot'); ?></small>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="tab_visibility[facebook]" value="1" 
                                        <?php checked(isset($options['tab_visibility']['facebook']) ? $options['tab_visibility']['facebook'] : 1, 1); ?>>
                                    <?php _e('Facebook', 'kata-chatbot'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="tab_visibility[zalo]" value="1" 
                                        <?php checked(isset($options['tab_visibility']['zalo']) ? $options['tab_visibility']['zalo'] : 1, 1); ?>>
                                    <?php _e('Zalo', 'kata-chatbot'); ?>
                                </label><br>
                                
                                <label>
                                    <input type="checkbox" name="tab_visibility[hotline]" value="1" 
                                        <?php checked(isset($options['tab_visibility']['hotline']) ? $options['tab_visibility']['hotline'] : 1, 1); ?>>
                                    <?php _e('Hotline', 'kata-chatbot'); ?>
                                </label>
                            </fieldset>
                            <p class="description"><?php _e('Chọn tab nào sẽ hiển thị trong chatbot', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="default_tab"><?php _e('Tab hiển thị mặc định', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <select id="default_tab" name="default_tab">
                                <option value="chat-ai" <?php selected(isset($options['default_tab']) ? $options['default_tab'] : 'chat-ai', 'chat-ai'); ?>>
                                    <?php _e('💬 Chat AI', 'kata-chatbot'); ?>
                                </option>
                                <option value="default" <?php selected(isset($options['default_tab']) ? $options['default_tab'] : '', 'default'); ?>>
                                    <?php _e('🏠 Default', 'kata-chatbot'); ?>
                                </option>
                                <option value="facebook" <?php selected(isset($options['default_tab']) ? $options['default_tab'] : '', 'facebook'); ?>>
                                    <?php _e('📘 Facebook', 'kata-chatbot'); ?>
                                </option>
                                <option value="zalo" <?php selected(isset($options['default_tab']) ? $options['default_tab'] : '', 'zalo'); ?>>
                                    <?php _e('💙 Zalo', 'kata-chatbot'); ?>
                                </option>
                                <option value="hotline" <?php selected(isset($options['default_tab']) ? $options['default_tab'] : '', 'hotline'); ?>>
                                    <?php _e('📞 Hotline', 'kata-chatbot'); ?>
                                </option>
                            </select>
                            <p class="description"><?php _e('Tab sẽ được hiển thị đầu tiên khi mở chatbot. Chỉ các tab được bật ở trên mới có thể chọn làm mặc định.', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Contact Information Section -->
            <div class="kata-settings-section">
                <h2><?php _e('📞 Thông tin liên hệ', 'kata-chatbot'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="zalo_number"><?php _e('Số Zalo', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="zalo_number" name="zalo_number" 
                                value="<?php echo esc_attr(isset($options['zalo_number']) ? $options['zalo_number'] : ''); ?>" 
                                class="regular-text">
                            <p class="description"><?php _e('Số điện thoại Zalo để khách hàng liên hệ', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="hotline_number"><?php _e('Hotline', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="hotline_number" name="hotline_number" 
                                value="<?php echo esc_attr(isset($options['hotline_number']) ? $options['hotline_number'] : ''); ?>" 
                                class="regular-text">
                            <p class="description"><?php _e('Số hotline để khách hàng gọi trực tiếp', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="facebook_page_id"><?php _e('Facebook Page ID', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="facebook_page_id" name="facebook_page_id" 
                                value="<?php echo esc_attr(isset($options['facebook_page_id']) ? $options['facebook_page_id'] : ''); ?>" 
                                class="regular-text">
                            <p class="description"><?php _e('ID của Facebook Page để tích hợp Facebook Messenger', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Advanced Settings Section -->
            <div class="kata-settings-section">
                <h2><?php _e('⚙️ Cài đặt nâng cao', 'kata-chatbot'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="auto_open_delay"><?php _e('Thời gian tự động mở (giây)', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="auto_open_delay" name="auto_open_delay" 
                                value="<?php echo esc_attr(isset($options['auto_open_delay']) ? $options['auto_open_delay'] : 0); ?>" 
                                min="0" max="60" class="small-text">
                            <p class="description"><?php _e('Số giây trước khi chatbot tự động mở (0 = không tự động mở)', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="show_on_mobile"><?php _e('Hiển thị trên mobile', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="show_on_mobile" name="show_on_mobile" value="1" 
                                <?php checked(isset($options['show_on_mobile']) ? $options['show_on_mobile'] : 1, 1); ?>>
                            <p class="description"><?php _e('Cho phép hiển thị chatbot trên thiết bị di động', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="enable_sound"><?php _e('Âm thanh thông báo', 'kata-chatbot'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="enable_sound" name="enable_sound" value="1" 
                                <?php checked(isset($options['enable_sound']) ? $options['enable_sound'] : 0, 1); ?>>
                            <p class="description"><?php _e('Phát âm thanh khi có tin nhắn mới', 'kata-chatbot'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        
            <!-- Submit and Reset Buttons -->
            <div class="kata-settings-section">
                <table class="form-table">
                    <tr>
                        <td>
                            <?php submit_button(__('Lưu cài đặt', 'kata-chatbot'), 'primary', 'submit', false); ?>
                            <a href="?page=kata-chatbot-settings&repair_branches=1" 
                               class="button button-secondary" 
                               onclick="return confirm('<?php echo esc_js(__('Bạn có chắc chắn muốn sửa chữa chi nhánh? Điều này có thể mất vài phút.', 'kata-chatbot')); ?>')">
                                <?php _e('Sửa chữa chi nhánh', 'kata-chatbot'); ?>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
    
    <!-- Add custom CSS for better styling -->
    <style>
        .kata-settings-container {
            max-width: 100%;
        }
        
        .kata-settings-section {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .kata-settings-section h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #0073aa;
            color: #0073aa;
        }
        
        .kata-settings-section h3 {
            color: #666;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .form-table th {
            width: 200px;
            font-weight: 600;
        }
        
        /* Default tab selector disabled state styling */
        .default-tab-disabled {
            opacity: 0.5;
            background-color: #f5f5f5 !important;
        }
        
        /* Tab preview styling */
        .kata-tab-preview {
            margin-top: 15px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .kata-tab-preview-tabs {
            display: flex;
            gap: 10px;
            margin: 10px 0;
            flex-wrap: wrap;
        }
        
        .kata-tab-preview-tab {
            padding: 8px 15px;
            background: #e5e5e5;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .kata-tab-preview-tab.active {
            background: #0073aa;
            color: white;
            border-color: #0073aa;
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all tab visibility checkboxes
        const tabCheckboxes = {
            'chat-ai': document.querySelector('input[name="tab_visibility[chat-ai]"]'),
            'default': document.querySelector('input[name="tab_visibility[default]"]'),
            'facebook': document.querySelector('input[name="tab_visibility[facebook]"]'),
            'zalo': document.querySelector('input[name="tab_visibility[zalo]"]'),
            'hotline': document.querySelector('input[name="tab_visibility[hotline]"]')
        };
        
        const defaultTabSelect = document.getElementById('default_tab');
        
        // Tab labels with icons
        const tabLabels = {
            'chat-ai': '💬 Chat AI',
            'default': '🏠 Default',
            'facebook': '📘 Facebook',
            'zalo': '💙 Zalo',
            'hotline': '📞 Hotline'
        };
        
        // Function to update default tab options
        function updateDefaultTabOptions() {
            const currentValue = defaultTabSelect.value;
            const enabledTabs = [];
            
            // Find all enabled tabs
            Object.keys(tabCheckboxes).forEach(function(tab) {
                if (tabCheckboxes[tab] && tabCheckboxes[tab].checked) {
                    enabledTabs.push(tab);
                }
            });
            
            // Clear and rebuild options
            defaultTabSelect.innerHTML = '';
            
            if (enabledTabs.length > 0) {
                enabledTabs.forEach(function(tab) {
                    const option = document.createElement('option');
                    option.value = tab;
                    option.textContent = tabLabels[tab];
                    option.selected = (currentValue === tab);
                    defaultTabSelect.appendChild(option);
                });
                
                // If current value is not in enabled tabs, select first enabled tab
                if (!enabledTabs.includes(currentValue)) {
                    defaultTabSelect.value = enabledTabs[0];
                }
                
                defaultTabSelect.disabled = false;
                defaultTabSelect.classList.remove('default-tab-disabled');
            } else {
                // No tabs enabled - disable selector
                const option = document.createElement('option');
                option.value = '';
                option.textContent = '<?php _e('Vui lòng chọn ít nhất một tab ở trên', 'kata-chatbot'); ?>';
                defaultTabSelect.appendChild(option);
                defaultTabSelect.disabled = true;
                defaultTabSelect.classList.add('default-tab-disabled');
            }
            
            updateTabPreview();
        }
        
        // Function to update tab preview
        function updateTabPreview() {
            let preview = document.getElementById('kata-tab-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.id = 'kata-tab-preview';
                preview.className = 'kata-tab-preview';
                preview.innerHTML = `
                    <h4><?php _e('🔍 Xem trước các tab:', 'kata-chatbot'); ?></h4>
                    <div class="kata-tab-preview-tabs"></div>
                    <p><em><?php _e('Đây là cách các tab sẽ hiển thị trong chatbot widget. Tab được khoanh tròn là tab mặc định.', 'kata-chatbot'); ?></em></p>
                `;
                defaultTabSelect.parentNode.appendChild(preview);
            }
            
            const previewTabs = preview.querySelector('.kata-tab-preview-tabs');
            previewTabs.innerHTML = '';
            
            const defaultTab = defaultTabSelect.value;
            
            Object.keys(tabCheckboxes).forEach(function(tab) {
                if (tabCheckboxes[tab] && tabCheckboxes[tab].checked) {
                    const tabDiv = document.createElement('div');
                    tabDiv.className = 'kata-tab-preview-tab' + (tab === defaultTab ? ' active' : '');
                    tabDiv.innerHTML = tabLabels[tab];
                    previewTabs.appendChild(tabDiv);
                }
            });
            
            if (previewTabs.children.length === 0) {
                previewTabs.innerHTML = '<div style="color: #999; font-style: italic;"><?php _e('Không có tab nào được chọn', 'kata-chatbot'); ?></div>';
            }
        }
        
        // Bind events to all tab checkboxes
        Object.values(tabCheckboxes).forEach(function(checkbox) {
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    // Prevent unchecking all tabs
                    const checkedTabs = Object.values(tabCheckboxes).filter(cb => cb && cb.checked);
                    if (checkedTabs.length === 0) {
                        this.checked = true;
                        alert('<?php _e('Bạn phải giữ ít nhất một tab được chọn để chatbot có thể hoạt động.', 'kata-chatbot'); ?>');
                        return;
                    }
                    updateDefaultTabOptions();
                });
            }
        });
        
        // Bind event to default tab selector
        if (defaultTabSelect) {
            defaultTabSelect.addEventListener('change', updateTabPreview);
        }
        
        // Initialize on page load
        updateDefaultTabOptions();
        
        // Form validation before submit
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const checkedTabs = Object.values(tabCheckboxes).filter(cb => cb && cb.checked);
                if (checkedTabs.length === 0) {
                    e.preventDefault();
                    alert('<?php _e('Vui lòng chọn ít nhất một tab để hiển thị trước khi lưu cài đặt.', 'kata-chatbot'); ?>');
                    return false;
                }
            });
        }
    });
    </script>
    <style>    
        .form-table td {
            padding: 15px 10px;
        }
        
        .form-table input[type="text"],
        .form-table input[type="password"],
        .form-table input[type="number"],
        .form-table select,
        .form-table textarea {
            border-radius: 3px;
        }
        
        .button-primary {
            background: #0073aa;
            border-color: #005a87;
            text-shadow: 0 -1px 1px #005177;
            box-shadow: 0 1px 0 #005177;
        }
        
        .button-secondary {
            margin-left: 10px;
        }
    </style>
</div>