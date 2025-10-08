<?php
/**
 * Settings Page
 * 
 * Plugin settings and configuration
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Save settings
if (isset($_POST['kata_seo_save_settings']) && check_admin_referer('kata_seo_settings_nonce')) {
    // Sanitize and prepare settings
    $settings = array(
        'enable_auto_schema' => !empty($_POST['enable_auto_schema']),
        'default_schema_types' => isset($_POST['default_schema_types']) ? array_map('sanitize_text_field', $_POST['default_schema_types']) : array(),
        'enable_editor_button' => !empty($_POST['enable_editor_button']),
        'enable_statistics' => !empty($_POST['enable_statistics']),
        'enable_shortcodes' => !empty($_POST['enable_shortcodes']),
        'enable_rest_api' => !empty($_POST['enable_rest_api']),
        'schema_validation' => !empty($_POST['schema_validation']),
    );
    
    // Update the option
    $updated = update_option('kata_seo_manager_settings', $settings);
    
    // Show success message
    if ($updated || get_option('kata_seo_manager_settings') === $settings) {
        echo '<div class="notice notice-success is-dismissible"><p>' . 
             __('Settings saved successfully!', 'kata-seo-manager') . '</p></div>';
    } else {
        echo '<div class="notice notice-warning is-dismissible"><p>' . 
             __('Settings unchanged or failed to save.', 'kata-seo-manager') . '</p></div>';
    }
}

// Get current settings with proper defaults
$default_settings = array(
    'enable_auto_schema' => true,
    'default_schema_types' => array('Article', 'Breadcrumb', 'WebPage'),
    'enable_editor_button' => true,
    'enable_statistics' => true,
    'enable_shortcodes' => true,
    'enable_rest_api' => false,
    'schema_validation' => true,
);

$settings = wp_parse_args(get_option('kata_seo_manager_settings', array()), $default_settings);

// Get all schema types
$schema_types = array(
    'Article', 'Breadcrumb', 'Carousel', 'Course', 'Dataset', 'Forum',
    'EduQA', 'EmployerRating', 'Event', 'FAQ', 'HowTo', 'ImageMetadata',
    'JobPosting', 'LocalBusiness', 'MathSolver', 'Movie', 'Organization',
    'PracticeProblem', 'Product', 'ProfilePage', 'Recipe', 'Review',
    'Sitelinks', 'Speakable', 'Video', 'WebPage'
);
?>

<div class="wrap kata-seo-settings">
    <h1><?php _e('KATA SEO Settings', 'kata-seo-manager'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('kata_seo_settings_nonce'); ?>
        
        <div class="kata-settings-container">
            <!-- General Settings -->
            <div class="kata-settings-card">
                <h2><?php _e('General Settings', 'kata-seo-manager'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enable_auto_schema">
                                <?php _e('Enable Auto Schema', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_auto_schema" id="enable_auto_schema" value="1"
                                       <?php checked($settings['enable_auto_schema'], true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Automatically generate basic schema for posts and pages', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="enable_editor_button">
                                <?php _e('Enable Editor Button', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_editor_button" id="enable_editor_button" value="1"
                                       <?php checked($settings['enable_editor_button'], true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Show "Insert Schema" button in post editor', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="enable_statistics">
                                <?php _e('Enable Statistics', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_statistics" id="enable_statistics" value="1"
                                       <?php checked($settings['enable_statistics'], true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Track schema usage statistics', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="enable_shortcodes">
                                <?php _e('Enable Shortcodes', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_shortcodes" id="enable_shortcodes" value="1"
                                       <?php checked($settings['enable_shortcodes'], true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Enable schema shortcodes for easy insertion', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="schema_validation">
                                <?php _e('Schema Validation', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="schema_validation" id="schema_validation" value="1"
                                       <?php checked($settings['schema_validation'], true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Validate schema before output', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Default Schema Types -->
            <div class="kata-settings-card">
                <h2><?php _e('Default Schema Types', 'kata-seo-manager'); ?></h2>
                <p class="description">
                    <?php _e('Select which schema types to enable by default for new posts', 'kata-seo-manager'); ?>
                </p>
                
                <div class="kata-schema-types-grid">
                    <?php foreach ($schema_types as $type) : ?>
                        <label class="kata-schema-checkbox">
                            <input type="checkbox" name="default_schema_types[]" 
                                   value="<?php echo esc_attr($type); ?>"
                                   <?php checked(in_array($type, $settings['default_schema_types'])); ?>>
                            <span><?php echo esc_html($type); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Advanced Settings -->
            <div class="kata-settings-card">
                <h2><?php _e('Advanced Settings', 'kata-seo-manager'); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="enable_rest_api">
                                <?php _e('Enable REST API', 'kata-seo-manager'); ?>
                            </label>
                        </th>
                        <td>
                            <label class="kata-switch">
                                <input type="checkbox" name="enable_rest_api" id="enable_rest_api" value="1"
                                       <?php checked($settings['enable_rest_api'], true); ?>>
                                <span class="kata-slider"></span>
                            </label>
                            <p class="description">
                                <?php _e('Enable REST API endpoints for schema management', 'kata-seo-manager'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Demo Content Generator -->
            <div class="kata-settings-card kata-demo-card">
                <h2><?php _e('Tạo Dữ Liệu Mẫu Demo', 'kata-seo-manager'); ?></h2>
                
                <div class="kata-demo-description">
                    <p><?php _e('Tạo nội dung mẫu về <strong>Chăm Sóc Da</strong> để trải nghiệm đầy đủ tính năng của plugin:', 'kata-seo-manager'); ?></p>
                    <ul class="kata-feature-list">
                        <li><span class="dashicons dashicons-yes-alt"></span> 1 bài viết hướng dẫn với <strong>26 loại schema</strong></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> 3 <strong>Poll</strong> (Khảo sát) về chăm sóc da</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> 3 <strong>Quiz</strong> (Trắc nghiệm) kiến thức</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> 3 <strong>Vòng Quay</strong> may mắn nhận quà</li>
                        <li><span class="dashicons dashicons-yes-alt"></span> 3 <strong>User Interactions</strong> (Đánh giá, bình luận)</li>
                    </ul>
                    <div class="kata-demo-warning">
                        <span class="dashicons dashicons-info"></span>
                        <span><?php _e('Lưu ý: Dữ liệu mẫu sẽ được tạo mới. Bạn có thể xóa sau khi trải nghiệm.', 'kata-seo-manager'); ?></span>
                    </div>
                </div>
                
                <div class="kata-demo-actions">
                    <button type="button" id="kata-generate-demo-btn" class="button button-hero kata-demo-btn">
                        <span class="dashicons dashicons-admin-post"></span>
                        <span class="button-text"><?php _e('Tạo Dữ Liệu Mẫu Ngay', 'kata-seo-manager'); ?></span>
                    </button>
                    
                    <div id="kata-demo-progress" class="kata-demo-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill"></div>
                        </div>
                        <div class="progress-text">Đang tạo dữ liệu mẫu...</div>
                    </div>
                    
                    <div id="kata-demo-result" class="kata-demo-result" style="display: none;"></div>
                </div>
            </div>
            
            <!-- Plugin Information -->
            <div class="kata-settings-card kata-info-card">
                <h2><?php _e('Plugin Information', 'kata-seo-manager'); ?></h2>
                
                <table class="kata-info-table">
                    <tr>
                        <td><strong><?php _e('Version:', 'kata-seo-manager'); ?></strong></td>
                        <td><?php echo KATA_SEO_MANAGER_VERSION; ?></td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Schema Types:', 'kata-seo-manager'); ?></strong></td>
                        <td>26 types</td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Author:', 'kata-seo-manager'); ?></strong></td>
                        <td>KATA Channel</td>
                    </tr>
                    <tr>
                        <td><strong><?php _e('Website:', 'kata-seo-manager'); ?></strong></td>
                        <td><a href="https://katachannel.com" target="_blank">katachannel.com</a></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <p class="submit">
            <button type="submit" name="kata_seo_save_settings" class="button button-primary button-large">
                <span class="dashicons dashicons-yes"></span>
                <?php _e('Save Settings', 'kata-seo-manager'); ?>
            </button>
        </p>
    </form>
</div>

<style>
.kata-seo-settings {
    margin: 20px 20px 20px 0;
}

.kata-settings-container {
    max-width: 1200px;
}

.kata-settings-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.kata-settings-card h2 {
    margin: 0 0 20px 0;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
    font-size: 20px;
}

/* Toggle Switch */
.kata-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.kata-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.kata-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.kata-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .kata-slider {
    background-color: #667eea;
}

input:checked + .kata-slider:before {
    transform: translateX(26px);
}

/* Schema Types Grid */
.kata-schema-types-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.kata-schema-checkbox {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    background: #f9f9f9;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.kata-schema-checkbox:hover {
    background: #f0f0f0;
    border-color: #667eea;
}

.kata-schema-checkbox input[type="checkbox"] {
    margin: 0 10px 0 0;
}

.kata-schema-checkbox input[type="checkbox"]:checked + span {
    font-weight: 600;
    color: #667eea;
}

/* Info Card */
.kata-info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.kata-info-card h2 {
    color: #fff;
    border-bottom-color: rgba(255,255,255,0.2);
}

.kata-info-table {
    width: 100%;
}

.kata-info-table tr td {
    padding: 10px 0;
    color: #fff;
}

.kata-info-table a {
    color: #fff;
    text-decoration: underline;
}

/* Submit Button */
.submit .button-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    font-size: 16px;
    height: auto;
}

.submit .button-primary .dashicons {
    font-size: 20px;
    width: 20px;
    height: 20px;
}

/* Demo Content Card */
.kata-demo-card {
    border-left: 4px solid #667eea !important;
    background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
}

.kata-demo-description {
    margin: 20px 0;
}

.kata-demo-description p {
    font-size: 15px;
    color: #555;
    margin-bottom: 15px;
}

.kata-feature-list {
    list-style: none;
    padding: 0;
    margin: 20px 0;
}

.kata-feature-list li {
    padding: 10px 15px;
    margin-bottom: 8px;
    background: #fff;
    border-radius: 6px;
    border-left: 3px solid #667eea;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: #333;
}

.kata-feature-list li .dashicons {
    color: #10b981;
    font-size: 20px;
    width: 20px;
    height: 20px;
}

.kata-demo-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 6px;
    padding: 12px 15px;
    margin-top: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #856404;
}

.kata-demo-warning .dashicons {
    color: #ffc107;
    font-size: 20px;
    width: 20px;
    height: 20px;
}

.kata-demo-actions {
    margin-top: 20px;
}

.kata-demo-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    color: #fff !important;
    padding: 15px 35px !important;
    font-size: 16px !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
}

.kata-demo-btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5) !important;
}

.kata-demo-btn:active {
    transform: translateY(0) !important;
}

.kata-demo-btn .dashicons {
    font-size: 22px;
    width: 22px;
    height: 22px;
}

.kata-demo-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed !important;
    transform: none !important;
}

/* Progress Bar */
.kata-demo-progress {
    margin-top: 20px;
}

.progress-bar {
    width: 100%;
    height: 30px;
    background: #e0e0e0;
    border-radius: 15px;
    overflow: hidden;
    position: relative;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    width: 0%;
    transition: width 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
}

.progress-text {
    margin-top: 10px;
    text-align: center;
    color: #667eea;
    font-weight: 600;
}

/* Result Display */
.kata-demo-result {
    margin-top: 20px;
    padding: 20px;
    border-radius: 8px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.kata-demo-result.success {
    background: #d1fae5;
    border: 2px solid #10b981;
    color: #065f46;
}

.kata-demo-result.error {
    background: #fee2e2;
    border: 2px solid #ef4444;
    color: #991b1b;
}

.kata-demo-result h3 {
    margin: 0 0 15px 0;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.kata-demo-result ul {
    list-style: none;
    padding: 0;
    margin: 15px 0;
}

.kata-demo-result ul li {
    padding: 8px 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
}

.kata-demo-result ul li:last-child {
    border-bottom: none;
}

.kata-demo-result .demo-link {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    margin-top: 15px;
    padding: 10px 20px;
    background: #fff;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.kata-demo-result .demo-link:hover {
    background: #667eea;
    color: #fff;
    transform: translateX(5px);
}
</style>

<script>
jQuery(document).ready(function($) {
    // Debug: Check if kata_ajax is available
    console.log('KATA SEO Settings loaded');
    console.log('kata_ajax available:', typeof kata_ajax !== 'undefined');
    if (typeof kata_ajax !== 'undefined') {
        console.log('kata_ajax object:', kata_ajax);
    }
    
    // Handle Generate Demo Content button
    $('#kata-generate-demo-btn').on('click', function() {
        const $btn = $(this);
        const $progress = $('#kata-demo-progress');
        const $progressFill = $('.progress-fill');
        const $progressText = $('.progress-text');
        const $result = $('#kata-demo-result');
        
        // Check if kata_ajax is available
        if (typeof kata_ajax === 'undefined') {
            alert('AJAX không khả dụng. Vui lòng refresh trang và thử lại.');
            return;
        }
        
        // Confirm action
        if (!confirm('Bạn có chắc muốn tạo dữ liệu mẫu? Hành động này sẽ tạo mới 1 bài viết, 3 polls, 3 quizzes, 3 wheels và 3 user interactions.')) {
            return;
        }
        
        // Disable button
        $btn.prop('disabled', true);
        $btn.find('.button-text').text('Đang xử lý...');
        
        // Show progress
        $result.hide();
        $progress.show();
        $progressFill.css('width', '0%');
        
        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(function() {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            $progressFill.css('width', progress + '%');
        }, 200);
        
        // Send AJAX request
        $.ajax({
            url: kata_ajax.url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'kata_generate_demo_content',
                nonce: kata_ajax.nonce
            },
            success: function(response) {
                clearInterval(progressInterval);
                $progressFill.css('width', '100%');
                
                setTimeout(function() {
                    $progress.hide();
                    
                    if (response && response.success) {
                        const data = response.data;
                        
                        let html = '<div class="kata-demo-result success">';
                        html += '<h3><span class="dashicons dashicons-yes-alt"></span> Tạo Dữ Liệu Mẫu Thành Công!</h3>';
                        html += '<p><strong>Dữ liệu đã được tạo:</strong></p>';
                        html += '<ul>';
                        
                        // Post info
                        if (data.post_id) {
                            html += '<li>✅ <strong>Bài viết:</strong> Hướng dẫn chăm sóc da với 26 schema types';
                            html += '<br><a href="' + data.post_url + '" target="_blank" class="demo-link">';
                            html += '<span class="dashicons dashicons-external"></span> Xem bài viết';
                            html += '</a></li>';
                        }
                        
                        // Polls
                        if (data.polls && data.polls.length > 0) {
                            html += '<li>✅ <strong>Polls:</strong> ' + data.polls.length + ' khảo sát';
                            html += '<ul style="margin-left: 20px; margin-top: 5px;">';
                            data.polls.forEach(function(poll) {
                                html += '<li>' + poll.title + ' (ID: ' + poll.id + ')</li>';
                            });
                            html += '</ul></li>';
                        }
                        
                        // Quizzes
                        if (data.quizzes && data.quizzes.length > 0) {
                            html += '<li>✅ <strong>Quizzes:</strong> ' + data.quizzes.length + ' trắc nghiệm';
                            html += '<ul style="margin-left: 20px; margin-top: 5px;">';
                            data.quizzes.forEach(function(quiz) {
                                html += '<li>' + quiz.title + ' (ID: ' + quiz.id + ')</li>';
                            });
                            html += '</ul></li>';
                        }
                        
                        // Wheels
                        if (data.wheels && data.wheels.length > 0) {
                            html += '<li>✅ <strong>Vòng Quay:</strong> ' + data.wheels.length + ' vòng quay may mắn';
                            html += '<ul style="margin-left: 20px; margin-top: 5px;">';
                            data.wheels.forEach(function(wheel) {
                                html += '<li>' + wheel.title + ' (ID: ' + wheel.id + ')</li>';
                            });
                            html += '</ul></li>';
                        }
                        
                        // Interactions
                        if (data.interactions && data.interactions.length > 0) {
                            html += '<li>✅ <strong>User Interactions:</strong> ' + data.interactions.length + ' tương tác';
                            html += '<ul style="margin-left: 20px; margin-top: 5px;">';
                            data.interactions.forEach(function(interaction) {
                                html += '<li>' + interaction.type + ' - ' + interaction.user + ' (ID: ' + interaction.id + ')</li>';
                            });
                            html += '</ul></li>';
                        }
                        
                        html += '</ul>';
                        html += '<p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.1);">';
                        html += '<strong>💡 Gợi ý:</strong> Truy cập bài viết demo để xem tất cả tính năng hoạt động!';
                        html += '</p>';
                        html += '</div>';
                        
                        $result.html(html).show();
                        
                        // Re-enable button
                        $btn.prop('disabled', false);
                        $btn.find('.button-text').text('Tạo Lại Dữ Liệu Mẫu');
                        
                    } else {
                        let html = '<div class="kata-demo-result error">';
                        html += '<h3><span class="dashicons dashicons-warning"></span> Lỗi Khi Tạo Dữ Liệu</h3>';
                        html += '<p>' + (response.data.message || 'Có lỗi xảy ra. Vui lòng thử lại.') + '</p>';
                        html += '</div>';
                        
                        $result.html(html).show();
                        
                        // Re-enable button
                        $btn.prop('disabled', false);
                        $btn.find('.button-text').text('Thử Lại');
                    }
                }, 500);
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                $progress.hide();
                
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                
                let html = '<div class="kata-demo-result error">';
                html += '<h3><span class="dashicons dashicons-warning"></span> Lỗi Kết Nối</h3>';
                
                // Try to parse the error
                let errorMsg = 'Không thể kết nối đến server. Vui lòng thử lại.';
                if (xhr.responseText) {
                    if (xhr.responseText.indexOf('SyntaxError') !== -1) {
                        errorMsg = 'Server trả về dữ liệu không hợp lệ (có thể do PHP warning/error).';
                    } else if (xhr.responseText.indexOf('Fatal error') !== -1) {
                        errorMsg = 'Lỗi PHP nghiêm trọng. Vui lòng kiểm tra log.';
                    } else if (xhr.status === 500) {
                        errorMsg = 'Lỗi server nội bộ (HTTP 500).';
                    } else if (xhr.status === 403) {
                        errorMsg = 'Không có quyền truy cập (HTTP 403).';
                    }
                }
                
                html += '<p>' + errorMsg + '</p>';
                html += '<p><small>Status: ' + status + ' | Error: ' + error + '</small></p>';
                
                // Show response text if available (for debugging)
                if (xhr.responseText && xhr.responseText.length < 500) {
                    html += '<details style="margin-top: 10px;">';
                    html += '<summary>Chi tiết lỗi (dành cho developer)</summary>';
                    html += '<pre style="background: #f5f5f5; padding: 10px; font-size: 11px; overflow-x: auto;">';
                    html += xhr.responseText.substring(0, 500);
                    html += '</pre></details>';
                }
                
                html += '</div>';
                
                $result.html(html).show();
                
                // Re-enable button
                $btn.prop('disabled', false);
                $btn.find('.button-text').text('Thử Lại');
            }
        });
    });
});
</script>
