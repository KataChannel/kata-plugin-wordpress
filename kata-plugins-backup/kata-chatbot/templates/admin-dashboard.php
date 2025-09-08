<?php
/**
 * Admin Dashboard Template
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get dashboard data
$db_handler = new KataChatbot_DB_Handler();
$stats = $db_handler->get_dashboard_stats();
$conversations_data = $db_handler->get_conversations_for_admin(1, 10);
$recent_conversations = $conversations_data['conversations'];
?>

<div class="wrap kata-chatbot-admin">
    <h1><?php _e('Tổng quan Kata Chatbot', 'kata-chatbot'); ?></h1>
    
    <!-- Statistics Cards -->
    <div class="kata-stats-grid">
        <div class="kata-stat-card">
            <div class="kata-stat-icon">💬</div>
            <div class="kata-stat-content">
                <h3><?php echo esc_html(number_format_i18n($stats['total_conversations'])); ?></h3>
                <p><?php _e('Tổng cuộc hội thoại', 'kata-chatbot'); ?></p>
            </div>
        </div>
        
        <div class="kata-stat-card">
            <div class="kata-stat-icon">📨</div>
            <div class="kata-stat-content">
                <h3><?php echo esc_html(number_format_i18n($stats['total_messages'])); ?></h3>
                <p><?php _e('Tổng tin nhắn', 'kata-chatbot'); ?></p>
            </div>
        </div>
        
        <div class="kata-stat-card">
            <div class="kata-stat-icon">👥</div>
            <div class="kata-stat-content">
                <h3><?php echo esc_html(number_format_i18n(isset($stats['active_users']) ? $stats['active_users'] : 0)); ?></h3>
                <p><?php _e('Người dùng hoạt động', 'kata-chatbot'); ?></p>
            </div>
        </div>
        
        <div class="kata-stat-card">
            <div class="kata-stat-icon">⭐</div>
            <div class="kata-stat-content">
                <h3><?php echo (isset($stats['avg_rating']) && $stats['avg_rating']) ? esc_html(number_format($stats['avg_rating'], 1)) : 'N/A'; ?></h3>
                <p><?php _e('Đánh giá trung bình', 'kata-chatbot'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Charts and Recent Activity -->
    <div class="kata-dashboard-content">
        <div class="kata-dashboard-main">
            <!-- Recent Conversations -->
            <div class="kata-admin-section">
                <div class="kata-section-header">
                    <h2><?php _e('Cuộc hội thoại gần đây', 'kata-chatbot'); ?></h2>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=kata-chatbot-conversations')); ?>" class="button">
                        <?php _e('Xem tất cả', 'kata-chatbot'); ?>
                    </a>
                </div>
                
                <div class="kata-conversations-list">
                    <?php if (!empty($recent_conversations)): ?>
                        <?php foreach ($recent_conversations as $conversation): ?>
                            <div class="kata-conversation-item">
                                <div class="kata-conversation-avatar">
                                    <?php if ($conversation->user_id): ?>
                                        <?php echo get_avatar($conversation->user_id, 40); ?>
                                    <?php else: ?>
                                        <div class="kata-guest-avatar">👤</div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="kata-conversation-details">
                                    <div class="kata-conversation-header">
                                        <strong>
                                            <?php if ($conversation->user_id): ?>
                                                <?php 
                                                $user = get_user_by('id', $conversation->user_id);
                                                echo $user ? $user->display_name : __('Người dùng đã xóa', 'kata-chatbot');
                                                ?>
                                            <?php else: ?>
                                                <?php _e('Khách', 'kata-chatbot'); ?>
                                            <?php endif; ?>
                                        </strong>
                                        <span class="kata-conversation-time">
                                            <?php echo human_time_diff(strtotime($conversation->started_at), current_time('timestamp')); ?> <?php _e('trước', 'kata-chatbot'); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="kata-conversation-preview">
                                        <?php echo isset($conversation->last_message) ? wp_trim_words($conversation->last_message, 15) : __('Không có tin nhắn', 'kata-chatbot'); ?>
                                    </div>
                                    
                                    <div class="kata-conversation-meta">
                                        <span class="kata-message-count">
                                            <?php echo sprintf(__('%d tin nhắn', 'kata-chatbot'), $conversation->message_count); ?>
                                        </span>
                                        
                                        <span class="kata-conversation-status <?php echo $conversation->status; ?>">
                                            <?php 
                                            $status_labels = array(
                                                'active' => __('Đang hoạt động', 'kata-chatbot'),
                                                'closed' => __('Đã đóng', 'kata-chatbot'),
                                                'archived' => __('Đã lưu trữ', 'kata-chatbot')
                                            );
                                            echo $status_labels[$conversation->status] ?? $conversation->status;
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="kata-conversation-actions">
                                    <a href="<?php echo admin_url('admin.php?page=kata-chatbot-conversations&conversation_id=' . $conversation->id); ?>" 
                                       class="button button-small"><?php _e('Xem', 'kata-chatbot'); ?></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="kata-empty-state">
                            <p><?php _e('Chưa có cuộc hội thoại nào.', 'kata-chatbot'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="kata-dashboard-sidebar">
            <!-- Quick Settings -->
            <div class="kata-admin-section">
                <h3><?php _e('Cài đặt nhanh', 'kata-chatbot'); ?></h3>
                
                <div class="kata-quick-settings">
                    <div class="kata-setting-item">
                        <label>
                            <input type="checkbox" id="kata-chatbot-enabled" 
                                   <?php checked(get_option('kata_chatbot_enabled', true)); ?>>
                            <?php _e('Bật Chatbot', 'kata-chatbot'); ?>
                        </label>
                    </div>
                    
                    <div class="kata-setting-item">
                        <label>
                            <input type="checkbox" id="kata-chatbot-sound" 
                                   <?php checked(get_option('kata_chatbot_sound_enabled', true)); ?>>
                            <?php _e('Âm thanh thông báo', 'kata-chatbot'); ?>
                        </label>
                    </div>
                    
                    <div class="kata-setting-item">
                        <label>
                            <input type="checkbox" id="kata-chatbot-offline-mode" 
                                   <?php checked(get_option('kata_chatbot_offline_mode', false)); ?>>
                            <?php _e('Chế độ offline', 'kata-chatbot'); ?>
                        </label>
                    </div>
                </div>
                
                <button type="button" class="button button-primary kata-save-quick-settings">
                    <?php _e('Lưu cài đặt', 'kata-chatbot'); ?>
                </button>
            </div>
            
            <!-- System Status -->
            <div class="kata-admin-section">
                <h3><?php _e('Trạng thái hệ thống', 'kata-chatbot'); ?></h3>
                
                <div class="kata-system-status">
                    <div class="kata-status-item">
                        <span class="kata-status-label"><?php _e('API Google AI:', 'kata-chatbot'); ?></span>
                        <span class="kata-status-value" id="kata-api-status">
                            <?php _e('Đang kiểm tra...', 'kata-chatbot'); ?>
                        </span>
                    </div>
                    
                    <div class="kata-status-item">
                        <span class="kata-status-label"><?php _e('Database:', 'kata-chatbot'); ?></span>
                        <span class="kata-status-value kata-status-ok">
                            <?php _e('Hoạt động bình thường', 'kata-chatbot'); ?>
                        </span>
                    </div>
                    
                    <div class="kata-status-item">
                        <span class="kata-status-label"><?php _e('Chatbot Widget:', 'kata-chatbot'); ?></span>
                        <span class="kata-status-value kata-status-ok">
                            <?php _e('Đã tích hợp', 'kata-chatbot'); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Performance Metrics -->
            <div class="kata-admin-section">
                <h3><?php _e('Hiệu suất', 'kata-chatbot'); ?></h3>
                
                <div class="kata-performance-metrics">
                    <div class="kata-metric-item">
                        <span class="kata-metric-label"><?php _e('Thời gian phản hồi TB:', 'kata-chatbot'); ?></span>
                        <span class="kata-metric-value">
                            <?php echo $stats['avg_response_time'] ? number_format($stats['avg_response_time'], 2) . 's' : 'N/A'; ?>
                        </span>
                    </div>
                    
                    <div class="kata-metric-item">
                        <span class="kata-metric-label"><?php _e('Độ hài lòng:', 'kata-chatbot'); ?></span>
                        <span class="kata-metric-value">
                            <?php echo isset($stats['satisfaction_rate']) && $stats['satisfaction_rate'] ? number_format($stats['satisfaction_rate'], 1) . '%' : 'N/A'; ?>
                        </span>
                    </div>
                    
                    <div class="kata-metric-item">
                        <span class="kata-metric-label"><?php _e('Hội thoại hôm nay:', 'kata-chatbot'); ?></span>
                        <span class="kata-metric-value">
                            <?php echo number_format_i18n($stats['today_conversations'] ?? 0); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kata-chatbot-admin {
    margin: 20px 0;
}

.kata-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kata-stat-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.kata-stat-icon {
    font-size: 2em;
    opacity: 0.8;
}

.kata-stat-content h3 {
    margin: 0;
    font-size: 1.8em;
    font-weight: bold;
    color: #2271b1;
}

.kata-stat-content p {
    margin: 5px 0 0;
    color: #666;
    font-size: 0.9em;
}

.kata-dashboard-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.kata-admin-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.kata-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.kata-section-header h2 {
    margin: 0;
    font-size: 1.3em;
}

.kata-conversations-list {
    max-height: 500px;
    overflow-y: auto;
}

.kata-conversation-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.kata-conversation-item:last-child {
    border-bottom: none;
}

.kata-conversation-avatar {
    flex-shrink: 0;
}

.kata-guest-avatar {
    width: 40px;
    height: 40px;
    background: #ddd;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2em;
}

.kata-conversation-details {
    flex: 1;
}

.kata-conversation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.kata-conversation-time {
    color: #666;
    font-size: 0.85em;
}

.kata-conversation-preview {
    color: #444;
    margin-bottom: 8px;
    line-height: 1.4;
}

.kata-conversation-meta {
    display: flex;
    gap: 15px;
    font-size: 0.85em;
}

.kata-message-count {
    color: #666;
}

.kata-conversation-status {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: 500;
}

.kata-conversation-status.active {
    background: #e7f5e7;
    color: #4a7c4a;
}

.kata-conversation-status.closed {
    background: #f0f0f0;
    color: #666;
}

.kata-conversation-status.archived {
    background: #fff3cd;
    color: #856404;
}

.kata-conversation-actions {
    flex-shrink: 0;
}

.kata-quick-settings {
    margin-bottom: 15px;
}

.kata-setting-item {
    margin-bottom: 10px;
}

.kata-setting-item label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.kata-system-status,
.kata-performance-metrics {
    space-y: 10px;
}

.kata-status-item,
.kata-metric-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.kata-status-item:last-child,
.kata-metric-item:last-child {
    border-bottom: none;
}

.kata-status-label,
.kata-metric-label {
    font-weight: 500;
    color: #555;
}

.kata-status-value.kata-status-ok {
    color: #4a7c4a;
}

.kata-status-value.kata-status-error {
    color: #d63638;
}

.kata-status-value.kata-status-warning {
    color: #dba617;
}

.kata-metric-value {
    font-weight: bold;
    color: #2271b1;
}

.kata-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

@media (max-width: 768px) {
    .kata-dashboard-content {
        grid-template-columns: 1fr;
    }
    
    .kata-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .kata-conversation-item {
        flex-direction: column;
        gap: 10px;
    }
    
    .kata-conversation-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Check API status
    checkApiStatus();
    
    // Save quick settings
    $('.kata-save-quick-settings').on('click', function() {
        var button = $(this);
        var originalText = button.text();
        
        button.text('<?php _e('Đang lưu...', 'kata-chatbot'); ?>').prop('disabled', true);
        
        var settings = {
            kata_chatbot_enabled: $('#kata-chatbot-enabled').is(':checked'),
            kata_chatbot_sound_enabled: $('#kata-chatbot-sound').is(':checked'),
            kata_chatbot_offline_mode: $('#kata-chatbot-offline-mode').is(':checked')
        };
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_chatbot_save_quick_settings',
                settings: settings,
                nonce: '<?php echo wp_create_nonce('kata_chatbot_admin'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $('<div class="notice notice-success is-dismissible"><p><?php _e('Cài đặt đã được lưu!', 'kata-chatbot'); ?></p></div>')
                        .insertAfter('.kata-chatbot-admin h1')
                        .delay(3000)
                        .fadeOut();
                } else {
                    alert('<?php _e('Có lỗi xảy ra. Vui lòng thử lại.', 'kata-chatbot'); ?>');
                }
            },
            error: function() {
                alert('<?php _e('Có lỗi xảy ra. Vui lòng thử lại.', 'kata-chatbot'); ?>');
            },
            complete: function() {
                button.text(originalText).prop('disabled', false);
            }
        });
    });
    
    function checkApiStatus() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_chatbot_check_api_status',
                nonce: '<?php echo wp_create_nonce('kata_chatbot_admin'); ?>'
            },
            success: function(response) {
                var statusElement = $('#kata-api-status');
                if (response.success && response.data.status === 'ok') {
                    statusElement.text('<?php _e('Hoạt động bình thường', 'kata-chatbot'); ?>')
                               .removeClass('kata-status-error kata-status-warning')
                               .addClass('kata-status-ok');
                } else {
                    statusElement.text('<?php _e('Có vấn đề', 'kata-chatbot'); ?>')
                               .removeClass('kata-status-ok kata-status-warning')
                               .addClass('kata-status-error');
                }
            },
            error: function() {
                $('#kata-api-status').text('<?php _e('Không thể kiểm tra', 'kata-chatbot'); ?>')
                                    .removeClass('kata-status-ok kata-status-warning')
                                    .addClass('kata-status-error');
            }
        });
    }
    
    // Auto-refresh stats every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script>
