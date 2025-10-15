<?php
/**
 * KATA Smart Chatbot - Chat Logs Page
 * 
 * @package KATA_SEO_Manager
 * @subpackage Admin
 */

if (!defined('ABSPATH')) exit;

global $wpdb;
$table_logs = $wpdb->prefix . 'kata_chatbot_logs';

// Pagination
$per_page = 50;
$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($paged - 1) * $per_page;

// Filters
$session_filter = isset($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : '';
$type_filter = isset($_GET['message_type']) ? sanitize_text_field($_GET['message_type']) : '';
$date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : '';

// Build query
$where = "1=1";
if ($session_filter) {
    $where .= $wpdb->prepare(" AND session_id = %s", $session_filter);
}
if ($type_filter) {
    $where .= $wpdb->prepare(" AND message_type = %s", $type_filter);
}
if ($date_from) {
    $where .= $wpdb->prepare(" AND DATE(created_at) >= %s", $date_from);
}
if ($date_to) {
    $where .= $wpdb->prepare(" AND DATE(created_at) <= %s", $date_to);
}

// Get total
$total = $wpdb->get_var("SELECT COUNT(*) FROM $table_logs WHERE $where");
$total_pages = ceil($total / $per_page);

// Get logs
$logs = $wpdb->get_results(
    "SELECT * FROM $table_logs 
     WHERE $where 
     ORDER BY created_at DESC 
     LIMIT $per_page OFFSET $offset"
);

// Get unique sessions for filter
$sessions = $wpdb->get_results(
    "SELECT DISTINCT session_id, COUNT(*) as message_count, MAX(created_at) as last_message 
     FROM $table_logs 
     GROUP BY session_id 
     ORDER BY last_message DESC 
     LIMIT 100"
);
?>

<div class="wrap kata-chatbot-logs">
    <h1>
        <span class="dashicons dashicons-format-chat"></span>
        Chat Logs
    </h1>
    
    <!-- Filters -->
    <div class="kata-logs-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="kata-chatbot-logs">
            
            <div class="kata-filter-row">
                <div class="kata-filter-item">
                    <label>Session ID:</label>
                    <select name="session_id">
                        <option value="">Tất cả sessions</option>
                        <?php foreach ($sessions as $session): ?>
                            <option value="<?php echo esc_attr($session->session_id); ?>" <?php selected($session_filter, $session->session_id); ?>>
                                <?php echo esc_html(substr($session->session_id, 0, 20)); ?>... 
                                (<?php echo $session->message_count; ?> messages)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="kata-filter-item">
                    <label>Loại tin nhắn:</label>
                    <select name="message_type">
                        <option value="">Tất cả</option>
                        <option value="user" <?php selected($type_filter, 'user'); ?>>User</option>
                        <option value="bot" <?php selected($type_filter, 'bot'); ?>>Bot</option>
                    </select>
                </div>
                
                <div class="kata-filter-item">
                    <label>Từ ngày:</label>
                    <input type="date" name="date_from" value="<?php echo esc_attr($date_from); ?>">
                </div>
                
                <div class="kata-filter-item">
                    <label>Đến ngày:</label>
                    <input type="date" name="date_to" value="<?php echo esc_attr($date_to); ?>">
                </div>
                
                <div class="kata-filter-item">
                    <button type="submit" class="button">Lọc</button>
                    <a href="?page=kata-chatbot-logs" class="button">Reset</a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Stats Summary -->
    <div class="kata-logs-summary">
        <strong>Tổng cộng:</strong> <?php echo number_format($total); ?> tin nhắn
    </div>
    
    <!-- Logs Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="15%">Session</th>
                <th width="8%">Type</th>
                <th width="40%">Message</th>
                <th width="12%">Page URL</th>
                <th width="10%">IP</th>
                <th width="10%">Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        Không có dữ liệu chat logs
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr class="kata-log-<?php echo esc_attr($log->message_type); ?>">
                        <td><?php echo $log->id; ?></td>
                        <td>
                            <code title="<?php echo esc_attr($log->session_id); ?>">
                                <?php echo esc_html(substr($log->session_id, 0, 15)); ?>...
                            </code>
                        </td>
                        <td>
                            <span class="kata-badge kata-badge-<?php echo esc_attr($log->message_type); ?>">
                                <?php echo $log->message_type === 'user' ? '👤 User' : '🤖 Bot'; ?>
                            </span>
                        </td>
                        <td>
                            <div class="kata-message-preview">
                                <?php echo esc_html(substr($log->message, 0, 100)); ?>
                                <?php if (strlen($log->message) > 100): ?>
                                    <button type="button" class="kata-show-full" data-message="<?php echo esc_attr($log->message); ?>">
                                        ...xem thêm
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if ($log->page_url): ?>
                                <a href="<?php echo esc_url($log->page_url); ?>" target="_blank" title="<?php echo esc_attr($log->page_url); ?>">
                                    <?php echo esc_html(substr($log->page_url, 0, 30)); ?>...
                                </a>
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo esc_html($log->ip_address); ?></code></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($log->created_at)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="tablenav">
            <div class="tablenav-pages">
                <?php
                echo paginate_links(array(
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => '« Trước',
                    'next_text' => 'Sau »',
                    'total' => $total_pages,
                    'current' => $paged
                ));
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Full Message Modal -->
<div id="kata-message-modal" class="kata-modal" style="display: none;">
    <div class="kata-modal-content">
        <span class="kata-modal-close">&times;</span>
        <h3>Nội dung đầy đủ</h3>
        <div id="kata-modal-message"></div>
    </div>
</div>

<style>
.kata-chatbot-logs {
    max-width: 1400px;
}

.kata-logs-filters {
    background: white;
    padding: 20px;
    margin: 20px 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.kata-filter-row {
    display: flex;
    gap: 16px;
    align-items: flex-end;
}

.kata-filter-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.kata-filter-item label {
    font-size: 12px;
    font-weight: 600;
    color: #666;
}

.kata-filter-item select,
.kata-filter-item input[type="date"] {
    padding: 6px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.kata-logs-summary {
    background: #f5f7fa;
    padding: 12px 16px;
    margin: 16px 0;
    border-radius: 4px;
    border-left: 4px solid #042277;
}

.kata-log-user {
    background: #e3f2fd;
}

.kata-log-bot {
    background: #f5f5f5;
}

.kata-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.kata-badge-user {
    background: #042277;
    color: white;
}

.kata-badge-bot {
    background: #4caf50;
    color: white;
}

.kata-message-preview {
    line-height: 1.5;
}

.kata-show-full {
    background: none;
    border: none;
    color: #042277;
    text-decoration: underline;
    cursor: pointer;
    padding: 0;
    font-size: 12px;
}

.kata-modal {
    display: none;
    position: fixed;
    z-index: 999999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.kata-modal-content {
    background: white;
    margin: 10% auto;
    padding: 30px;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
    position: relative;
}

.kata-modal-close {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 30px;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
}

.kata-modal-close:hover {
    color: #000;
}

#kata-modal-message {
    margin-top: 20px;
    padding: 16px;
    background: #f5f7fa;
    border-radius: 4px;
    line-height: 1.6;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Show full message
    $('.kata-show-full').on('click', function() {
        const message = $(this).data('message');
        $('#kata-modal-message').text(message);
        $('#kata-message-modal').fadeIn();
    });
    
    // Close modal
    $('.kata-modal-close, .kata-modal').on('click', function(e) {
        if (e.target === this) {
            $('#kata-message-modal').fadeOut();
        }
    });
});
</script>
