<?php
/**
 * KATA Smart Chatbot - Leads Management Page
 * 
 * @package KataChatbot
 * @subpackage Admin
 */

if (!defined('ABSPATH')) exit;

global $wpdb;
$table_leads = $wpdb->prefix . 'kata_chatbot_leads';

// Handle status update
if (isset($_POST['update_lead_status']) && isset($_POST['lead_id'])) {
    check_admin_referer('kata_lead_status');
    $lead_id = intval($_POST['lead_id']);
    $new_status = sanitize_text_field($_POST['status']);
    
    $wpdb->update(
        $table_leads,
        array('status' => $new_status),
        array('id' => $lead_id),
        array('%s'),
        array('%d')
    );
    
    echo '<div class="notice notice-success"><p>✅ Đã cập nhật trạng thái lead!</p></div>';
}

// Pagination
$per_page = 20;
$paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($paged - 1) * $per_page;

// Filters
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$interest_filter = isset($_GET['interest_type']) ? sanitize_text_field($_GET['interest_type']) : '';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Build query
$where = "1=1";
if ($status_filter) {
    $where .= $wpdb->prepare(" AND status = %s", $status_filter);
}
if ($interest_filter) {
    $where .= $wpdb->prepare(" AND interest_type = %s", $interest_filter);
}
if ($search) {
    $where .= $wpdb->prepare(" AND (name LIKE %s OR email LIKE %s OR phone LIKE %s)", 
        '%' . $wpdb->esc_like($search) . '%',
        '%' . $wpdb->esc_like($search) . '%',
        '%' . $wpdb->esc_like($search) . '%'
    );
}

// Get total
$total = $wpdb->get_var("SELECT COUNT(*) FROM $table_leads WHERE $where");
$total_pages = ceil($total / $per_page);

// Get leads
$leads = $wpdb->get_results(
    "SELECT * FROM $table_leads 
     WHERE $where 
     ORDER BY created_at DESC 
     LIMIT $per_page OFFSET $offset"
);

// Get statistics by status
$status_stats = $wpdb->get_results(
    "SELECT status, COUNT(*) as count FROM $table_leads GROUP BY status"
);
?>

<div class="wrap kata-chatbot-leads">
    <h1>
        <span class="dashicons dashicons-groups"></span>
        Chatbot Leads Management
    </h1>
    
    <!-- Status Statistics -->
    <div class="kata-leads-stats">
        <?php foreach ($status_stats as $stat): ?>
            <div class="kata-status-stat">
                <div class="kata-status-count"><?php echo number_format($stat->count); ?></div>
                <div class="kata-status-label">
                    <?php 
                    $labels = array(
                        'new' => '🆕 Mới',
                        'contacted' => '📞 Đã liên hệ',
                        'qualified' => '✅ Đủ điều kiện',
                        'converted' => '💰 Đã chuyển đổi',
                        'lost' => '❌ Mất'
                    );
                    echo $labels[$stat->status] ?? ucfirst($stat->status);
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Filters and Search -->
    <div class="kata-leads-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="kata-chatbot-leads">
            
            <div class="kata-filter-row">
                <div class="kata-filter-item">
                    <input type="text" name="s" placeholder="Tìm kiếm tên, email, phone..." 
                           value="<?php echo esc_attr($search); ?>" class="kata-search-input">
                </div>
                
                <div class="kata-filter-item">
                    <select name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="new" <?php selected($status_filter, 'new'); ?>>🆕 Mới</option>
                        <option value="contacted" <?php selected($status_filter, 'contacted'); ?>>📞 Đã liên hệ</option>
                        <option value="qualified" <?php selected($status_filter, 'qualified'); ?>>✅ Đủ điều kiện</option>
                        <option value="converted" <?php selected($status_filter, 'converted'); ?>>💰 Đã chuyển đổi</option>
                        <option value="lost" <?php selected($status_filter, 'lost'); ?>>❌ Mất</option>
                    </select>
                </div>
                
                <div class="kata-filter-item">
                    <select name="interest_type">
                        <option value="">Tất cả quan tâm</option>
                        <option value="course" <?php selected($interest_filter, 'course'); ?>>Khóa học</option>
                        <option value="consultation" <?php selected($interest_filter, 'consultation'); ?>>Tư vấn</option>
                        <option value="price" <?php selected($interest_filter, 'price'); ?>>Học phí</option>
                        <option value="other" <?php selected($interest_filter, 'other'); ?>>Khác</option>
                    </select>
                </div>
                
                <div class="kata-filter-item">
                    <button type="submit" class="button button-primary">Tìm kiếm</button>
                    <a href="?page=kata-chatbot-leads" class="button">Reset</a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Leads Table -->
    <table class="wp-list-table widefat fixed striped kata-leads-table">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="15%">Tên</th>
                <th width="15%">Email</th>
                <th width="10%">Phone</th>
                <th width="20%">Tin nhắn</th>
                <th width="10%">Quan tâm</th>
                <th width="10%">Trạng thái</th>
                <th width="10%">Ngày tạo</th>
                <th width="5%">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px;">
                        Chưa có leads nào
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><?php echo $lead->id; ?></td>
                        <td><strong><?php echo esc_html($lead->name); ?></strong></td>
                        <td>
                            <a href="mailto:<?php echo esc_attr($lead->email); ?>">
                                <?php echo esc_html($lead->email); ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($lead->phone): ?>
                                <a href="tel:<?php echo esc_attr($lead->phone); ?>">
                                    <?php echo esc_html($lead->phone); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($lead->message): ?>
                                <div class="kata-lead-message">
                                    <?php echo esc_html(substr($lead->message, 0, 80)); ?>
                                    <?php if (strlen($lead->message) > 80): ?>
                                        <button type="button" class="kata-show-full-lead" 
                                                data-lead-id="<?php echo $lead->id; ?>"
                                                data-name="<?php echo esc_attr($lead->name); ?>"
                                                data-email="<?php echo esc_attr($lead->email); ?>"
                                                data-phone="<?php echo esc_attr($lead->phone); ?>"
                                                data-message="<?php echo esc_attr($lead->message); ?>"
                                                data-interest="<?php echo esc_attr($lead->interest_type); ?>"
                                                data-source="<?php echo esc_attr($lead->source_page); ?>">
                                            ...xem chi tiết
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php 
                            $interests = array(
                                'course' => '📚 Khóa học',
                                'consultation' => '💡 Tư vấn',
                                'price' => '💰 Học phí',
                                'other' => '📌 Khác'
                            );
                            echo $interests[$lead->interest_type] ?? '-';
                            ?>
                        </td>
                        <td>
                            <form method="post" class="kata-status-form">
                                <?php wp_nonce_field('kata_lead_status'); ?>
                                <input type="hidden" name="lead_id" value="<?php echo $lead->id; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="new" <?php selected($lead->status, 'new'); ?>>🆕 Mới</option>
                                    <option value="contacted" <?php selected($lead->status, 'contacted'); ?>>📞 Đã liên hệ</option>
                                    <option value="qualified" <?php selected($lead->status, 'qualified'); ?>>✅ Đủ điều kiện</option>
                                    <option value="converted" <?php selected($lead->status, 'converted'); ?>>💰 Đã chuyển đổi</option>
                                    <option value="lost" <?php selected($lead->status, 'lost'); ?>>❌ Mất</option>
                                </select>
                                <input type="hidden" name="update_lead_status" value="1">
                            </form>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($lead->created_at)); ?></td>
                        <td>
                            <button type="button" class="button button-small kata-view-lead"
                                    data-lead-id="<?php echo $lead->id; ?>">
                                👁️
                            </button>
                        </td>
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

<!-- Lead Detail Modal -->
<div id="kata-lead-modal" class="kata-modal" style="display: none;">
    <div class="kata-modal-content">
        <span class="kata-modal-close">&times;</span>
        <h2>Chi tiết Lead</h2>
        <div id="kata-lead-details"></div>
    </div>
</div>

<style>
.kata-chatbot-leads {
    max-width: 1600px;
}

.kata-leads-stats {
    display: flex;
    gap: 16px;
    margin: 20px 0;
}

.kata-status-stat {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    flex: 1;
    text-align: center;
}

.kata-status-count {
    font-size: 32px;
    font-weight: bold;
    color: #042277;
    margin-bottom: 8px;
}

.kata-status-label {
    font-size: 14px;
    color: #666;
}

.kata-leads-filters {
    background: white;
    padding: 20px;
    margin: 20px 0;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.kata-search-input {
    width: 300px;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.kata-status-form {
    margin: 0;
}

.kata-status-form select {
    padding: 4px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 13px;
}

.kata-lead-message {
    line-height: 1.5;
}

.kata-show-full-lead {
    background: none;
    border: none;
    color: #042277;
    text-decoration: underline;
    cursor: pointer;
    padding: 0;
    font-size: 12px;
}

#kata-lead-details {
    margin-top: 20px;
}

.kata-lead-info-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
}

.kata-lead-info-label {
    width: 150px;
    font-weight: 600;
    color: #666;
}

.kata-lead-info-value {
    flex: 1;
}
</style>

<script>
jQuery(document).ready(function($) {
    // View lead details
    $('.kata-show-full-lead, .kata-view-lead').on('click', function() {
        const name = $(this).data('name') || $(this).closest('tr').find('td:eq(1)').text().trim();
        const email = $(this).data('email') || $(this).closest('tr').find('td:eq(2)').text().trim();
        const phone = $(this).data('phone') || $(this).closest('tr').find('td:eq(3)').text().trim();
        const message = $(this).data('message') || '';
        const interest = $(this).data('interest') || '';
        const source = $(this).data('source') || '';
        
        let html = '<div class="kata-lead-details-content">';
        html += '<div class="kata-lead-info-row"><div class="kata-lead-info-label">👤 Họ tên:</div><div class="kata-lead-info-value"><strong>' + name + '</strong></div></div>';
        html += '<div class="kata-lead-info-row"><div class="kata-lead-info-label">📧 Email:</div><div class="kata-lead-info-value"><a href="mailto:' + email + '">' + email + '</a></div></div>';
        if (phone) {
            html += '<div class="kata-lead-info-row"><div class="kata-lead-info-label">📱 Phone:</div><div class="kata-lead-info-value"><a href="tel:' + phone + '">' + phone + '</a></div></div>';
        }
        if (interest) {
            html += '<div class="kata-lead-info-row"><div class="kata-lead-info-label">📚 Quan tâm:</div><div class="kata-lead-info-value">' + interest + '</div></div>';
        }
        if (message) {
            html += '<div class="kata-lead-info-row"><div class="kata-lead-info-label">💬 Tin nhắn:</div><div class="kata-lead-info-value">' + message + '</div></div>';
        }
        if (source) {
            html += '<div class="kata-lead-info-row"><div class="kata-lead-info-label">🔗 Nguồn:</div><div class="kata-lead-info-value"><a href="' + source + '" target="_blank">' + source + '</a></div></div>';
        }
        html += '</div>';
        
        $('#kata-lead-details').html(html);
        $('#kata-lead-modal').fadeIn();
    });
    
    // Close modal
    $('.kata-modal-close, .kata-modal').on('click', function(e) {
        if (e.target === this) {
            $('#kata-lead-modal').fadeOut();
        }
    });
});
</script>
