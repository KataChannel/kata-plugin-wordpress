<?php
/**
 * Admin Conversations Template
 * 
 * @package KataChatbot
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="kata-conversations-container">
        <div class="kata-conversations-filters">
            <form method="get" class="kata-filter-form">
                <input type="hidden" name="page" value="kata-chatbot-conversations">
                
                <select name="status">
                    <option value=""><?php _e('Tất cả trạng thái', 'kata-chatbot'); ?></option>
                    <option value="active" <?php selected(isset($_GET['status']) ? $_GET['status'] : '', 'active'); ?>><?php _e('Đang hoạt động', 'kata-chatbot'); ?></option>
                    <option value="closed" <?php selected(isset($_GET['status']) ? $_GET['status'] : '', 'closed'); ?>><?php _e('Đã đóng', 'kata-chatbot'); ?></option>
                </select>
                
                <input type="date" name="date_from" value="<?php echo esc_attr(isset($_GET['date_from']) ? $_GET['date_from'] : ''); ?>" placeholder="<?php _e('Từ ngày', 'kata-chatbot'); ?>">
                <input type="date" name="date_to" value="<?php echo esc_attr(isset($_GET['date_to']) ? $_GET['date_to'] : ''); ?>" placeholder="<?php _e('Đến ngày', 'kata-chatbot'); ?>">
                
                <input type="submit" class="button" value="<?php _e('Lọc', 'kata-chatbot'); ?>">
            </form>
        </div>
        
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col"><?php _e('ID', 'kata-chatbot'); ?></th>
                    <th scope="col"><?php _e('Người dùng', 'kata-chatbot'); ?></th>
                    <th scope="col"><?php _e('Tin nhắn', 'kata-chatbot'); ?></th>
                    <th scope="col"><?php _e('Bắt đầu', 'kata-chatbot'); ?></th>
                    <th scope="col"><?php _e('Hoạt động cuối', 'kata-chatbot'); ?></th>
                    <th scope="col"><?php _e('Trạng thái', 'kata-chatbot'); ?></th>
                    <th scope="col"><?php _e('Hành động', 'kata-chatbot'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($conversations)) : ?>
                    <?php foreach ($conversations as $conversation) : ?>
                        <tr>
                            <td><?php echo esc_html($conversation->id); ?></td>
                            <td>
                                <?php if ($conversation->user_id) : ?>
                                    <?php 
                                    $user = get_user_by('id', $conversation->user_id);
                                    echo esc_html($user ? $user->display_name : 'Unknown User');
                                    ?>
                                <?php else : ?>
                                    <?php _e('Guest', 'kata-chatbot'); ?>
                                    <br><small><?php echo esc_html($conversation->user_ip); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($conversation->total_messages); ?></td>
                            <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($conversation->started_at))); ?></td>
                            <td><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($conversation->last_activity))); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo esc_attr($conversation->status); ?>">
                                    <?php echo esc_html(ucfirst($conversation->status)); ?>
                                </span>
                            </td>
                            <td>
                                <a href="#" class="button button-small view-conversation" data-id="<?php echo esc_attr($conversation->id); ?>">
                                    <?php _e('View', 'kata-chatbot'); ?>
                                </a>
                                <a href="#" class="button button-small delete-conversation" data-id="<?php echo esc_attr($conversation->id); ?>">
                                    <?php _e('Delete', 'kata-chatbot'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="no-items">
                            <?php _e('No conversations found.', 'kata-chatbot'); ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <?php if (isset($total_pages) && $total_pages > 1) : ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php
                    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
                    
                    echo paginate_links(array(
                        'base' => add_query_arg('paged', '%#%'),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => $total_pages,
                        'current' => $current_page
                    ));
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.status-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-active {
    background: #46b450;
    color: #fff;
}

.status-closed {
    background: #dc3232;
    color: #fff;
}

.kata-filter-form {
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
}

.kata-filter-form select,
.kata-filter-form input[type="date"] {
    margin-right: 10px;
}
</style>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $('.view-conversation').on('click', function(e) {
        e.preventDefault();
        var conversationId = $(this).data('id');
        // Implement view conversation modal
        alert('View conversation #' + conversationId + ' - To be implemented');
    });
    
    $('.delete-conversation').on('click', function(e) {
        e.preventDefault();
        var conversationId = $(this).data('id');
        
        if (confirm('<?php _e('Are you sure you want to delete this conversation?', 'kata-chatbot'); ?>')) {
            $.post(ajaxurl, {
                action: 'kata_chatbot_delete_conversation',
                nonce: kata_chatbot_admin.nonce,
                id: conversationId
            }, function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('<?php _e('Error deleting conversation', 'kata-chatbot'); ?>');
                }
            });
        }
    });
});
</script>
