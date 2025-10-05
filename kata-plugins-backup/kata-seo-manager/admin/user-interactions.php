<?php
/**
 * User Interactions Admin Page
 * 
 * @package KATA_SEO_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'kata_user_interactions';

// Handle actions
if (isset($_POST['action']) && $_POST['action'] === 'bulk_action' && !empty($_POST['interactions'])) {
    $action = sanitize_text_field($_POST['bulk_action']);
    $interaction_ids = array_map('intval', $_POST['interactions']);
    
    if ($action === 'approve') {
        $wpdb->query("UPDATE $table_name SET status = 'approved' WHERE id IN (" . implode(',', $interaction_ids) . ")");
        echo '<div class="notice notice-success"><p>Đã duyệt ' . count($interaction_ids) . ' tương tác.</p></div>';
    } elseif ($action === 'reject') {
        $wpdb->query("UPDATE $table_name SET status = 'rejected' WHERE id IN (" . implode(',', $interaction_ids) . ")");
        echo '<div class="notice notice-success"><p>Đã từ chối ' . count($interaction_ids) . ' tương tác.</p></div>';
    } elseif ($action === 'delete') {
        $wpdb->query("DELETE FROM $table_name WHERE id IN (" . implode(',', $interaction_ids) . ")");
        echo '<div class="notice notice-success"><p>Đã xóa ' . count($interaction_ids) . ' tương tác.</p></div>';
    }
}

// Handle single actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = sanitize_text_field($_GET['action']);
    $id = intval($_GET['id']);
    
    if ($action === 'approve') {
        $wpdb->update($table_name, array('status' => 'approved'), array('id' => $id));
        echo '<div class="notice notice-success"><p>Đã duyệt tương tác.</p></div>';
    } elseif ($action === 'reject') {
        $wpdb->update($table_name, array('status' => 'rejected'), array('id' => $id));
        echo '<div class="notice notice-success"><p>Đã từ chối tương tác.</p></div>';
    } elseif ($action === 'delete') {
        $wpdb->delete($table_name, array('id' => $id));
        echo '<div class="notice notice-success"><p>Đã xóa tương tác.</p></div>';
    } elseif ($action === 'feature') {
        $wpdb->update($table_name, array('is_featured' => 1), array('id' => $id));
        echo '<div class="notice notice-success"><p>Đã đánh dấu tương tác nổi bật.</p></div>';
    } elseif ($action === 'unfeature') {
        $wpdb->update($table_name, array('is_featured' => 0), array('id' => $id));
        echo '<div class="notice notice-success"><p>Đã bỏ đánh dấu tương tác nổi bật.</p></div>';
    }
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'all';
$post_filter = isset($_GET['post']) ? intval($_GET['post']) : 0;
$per_page = 20;
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($page - 1) * $per_page;

// Build query
$where_conditions = array();
$where_values = array();

if ($status_filter !== 'all') {
    $where_conditions[] = "status = %s";
    $where_values[] = $status_filter;
}

if ($post_filter > 0) {
    $where_conditions[] = "post_id = %d";
    $where_values[] = $post_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get interactions
$query = "SELECT * FROM $table_name $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d";
$where_values[] = $per_page;
$where_values[] = $offset;

$interactions = $wpdb->get_results($wpdb->prepare($query, $where_values));

// Get total count
$count_query = "SELECT COUNT(*) FROM $table_name $where_clause";
$total = $wpdb->get_var(!empty($where_values) ? $wpdb->prepare($count_query, array_slice($where_values, 0, -2)) : $count_query);

// Get statistics
$stats = $wpdb->get_row("
    SELECT 
        COUNT(*) as total,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
        COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected,
        AVG(overall_rating) as avg_rating
    FROM $table_name
    WHERE overall_rating > 0
");

// Get popular posts
$popular_posts = $wpdb->get_results("
    SELECT 
        post_id,
        COUNT(*) as interaction_count,
        AVG(overall_rating) as avg_rating
    FROM $table_name 
    WHERE status = 'approved'
    GROUP BY post_id 
    ORDER BY interaction_count DESC 
    LIMIT 10
");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        🤝 Quản lý User Interactions
        <a href="#" class="page-title-action" onclick="kataToggleNewForm()">Thêm mới</a>
    </h1>

    <!-- Statistics Dashboard -->
    <div class="kata-admin-stats" style="display: flex; gap: 20px; margin: 20px 0;">
        <div class="kata-stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1;">
            <h3 style="margin: 0 0 10px 0; color: #2271b1;">📊 Tổng quan</h3>
            <div style="font-size: 24px; font-weight: bold; color: #3c434a;"><?php echo number_format($stats->total); ?></div>
            <div style="color: #646970;">Tổng tương tác</div>
        </div>
        
        <div class="kata-stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1;">
            <h3 style="margin: 0 0 10px 0; color: #d63638;">⏳ Chờ duyệt</h3>
            <div style="font-size: 24px; font-weight: bold; color: #d63638;"><?php echo number_format($stats->pending); ?></div>
            <div style="color: #646970;">Cần xử lý</div>
        </div>
        
        <div class="kata-stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1;">
            <h3 style="margin: 0 0 10px 0; color: #00a32a;">✅ Đã duyệt</h3>
            <div style="font-size: 24px; font-weight: bold; color: #00a32a;"><?php echo number_format($stats->approved); ?></div>
            <div style="color: #646970;">Hiển thị công khai</div>
        </div>
        
        <div class="kata-stat-card" style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1;">
            <h3 style="margin: 0 0 10px 0; color: #dba617;">⭐ Đánh giá TB</h3>
            <div style="font-size: 24px; font-weight: bold; color: #dba617;"><?php echo $stats->avg_rating ? number_format($stats->avg_rating, 1) : '0'; ?></div>
            <div style="color: #646970;">Trên 5 sao</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="tablenav top">
        <div class="alignleft actions">
            <form method="get">
                <input type="hidden" name="page" value="kata-seo-user-interactions">
                
                <select name="status">
                    <option value="all" <?php selected($status_filter, 'all'); ?>>Tất cả trạng thái</option>
                    <option value="pending" <?php selected($status_filter, 'pending'); ?>>Chờ duyệt</option>
                    <option value="approved" <?php selected($status_filter, 'approved'); ?>>Đã duyệt</option>
                    <option value="rejected" <?php selected($status_filter, 'rejected'); ?>>Đã từ chối</option>
                </select>
                
                <input type="submit" class="button" value="Lọc">
            </form>
        </div>
        
        <div class="alignright">
            <span class="displaying-num"><?php echo number_format($total); ?> mục</span>
        </div>
    </div>

    <!-- Interactions Table -->
    <form method="post">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all-1">
                    </td>
                    <th class="manage-column column-user">Người dùng</th>
                    <th class="manage-column column-content">Nội dung</th>
                    <th class="manage-column column-rating">Đánh giá</th>
                    <th class="manage-column column-post">Bài viết</th>
                    <th class="manage-column column-status">Trạng thái</th>
                    <th class="manage-column column-date">Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($interactions): ?>
                    <?php foreach ($interactions as $interaction): ?>
                    <tr>
                        <th scope="row" class="check-column">
                            <input type="checkbox" name="interactions[]" value="<?php echo $interaction->id; ?>">
                        </th>
                        
                        <td class="column-user">
                            <strong><?php echo esc_html($interaction->user_name); ?></strong><br>
                            <small><?php echo esc_html($interaction->user_email); ?></small>
                            <?php if ($interaction->is_verified): ?>
                                <span class="dashicons dashicons-yes-alt" title="Đã xác thực" style="color: #00a32a;"></span>
                            <?php endif; ?>
                            <?php if ($interaction->is_featured): ?>
                                <span class="dashicons dashicons-star-filled" title="Nổi bật" style="color: #dba617;"></span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="column-content">
                            <?php if ($interaction->review_title): ?>
                                <strong><?php echo esc_html(wp_trim_words($interaction->review_title, 8)); ?></strong><br>
                            <?php endif; ?>
                            
                            <?php if ($interaction->review_content): ?>
                                <div><?php echo esc_html(wp_trim_words($interaction->review_content, 15)); ?></div>
                            <?php endif; ?>
                            
                            <?php if ($interaction->comment_content): ?>
                                <div><em><?php echo esc_html(wp_trim_words($interaction->comment_content, 15)); ?></em></div>
                            <?php endif; ?>
                            
                            <div class="row-actions">
                                <?php if ($interaction->status === 'pending'): ?>
                                    <span class="approve">
                                        <a href="?page=kata-seo-user-interactions&action=approve&id=<?php echo $interaction->id; ?>">Duyệt</a> |
                                    </span>
                                    <span class="trash">
                                        <a href="?page=kata-seo-user-interactions&action=reject&id=<?php echo $interaction->id; ?>">Từ chối</a> |
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (!$interaction->is_featured): ?>
                                    <span class="feature">
                                        <a href="?page=kata-seo-user-interactions&action=feature&id=<?php echo $interaction->id; ?>">Nổi bật</a> |
                                    </span>
                                <?php else: ?>
                                    <span class="unfeature">
                                        <a href="?page=kata-seo-user-interactions&action=unfeature&id=<?php echo $interaction->id; ?>">Bỏ nổi bật</a> |
                                    </span>
                                <?php endif; ?>
                                
                                <span class="delete">
                                    <a href="?page=kata-seo-user-interactions&action=delete&id=<?php echo $interaction->id; ?>" 
                                       onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                                </span>
                            </div>
                        </td>
                        
                        <td class="column-rating">
                            <?php if ($interaction->overall_rating > 0): ?>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <span style="font-weight: bold;"><?php echo number_format($interaction->overall_rating, 1); ?></span>
                                    <div>
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span style="color: <?php echo $i <= $interaction->overall_rating ? '#dba617' : '#ddd'; ?>;">⭐</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <?php if ($interaction->quality_rating > 0 || $interaction->value_rating > 0): ?>
                                <small style="color: #646970;">
                                    <?php if ($interaction->quality_rating): ?>CL: <?php echo $interaction->quality_rating; ?><?php endif; ?>
                                    <?php if ($interaction->value_rating): ?> | GT: <?php echo $interaction->value_rating; ?><?php endif; ?>
                                </small>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: #646970;">—</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="column-post">
                            <?php
                            $post = get_post($interaction->post_id);
                            if ($post):
                            ?>
                                <a href="<?php echo get_edit_post_link($interaction->post_id); ?>" target="_blank">
                                    <?php echo esc_html(wp_trim_words($post->post_title, 6)); ?>
                                </a>
                                <br><small>ID: <?php echo $interaction->post_id; ?></small>
                            <?php else: ?>
                                <span style="color: #d63638;">Bài viết đã xóa</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="column-status">
                            <?php
                            $status_labels = array(
                                'pending' => '<span style="color: #d63638;">⏳ Chờ duyệt</span>',
                                'approved' => '<span style="color: #00a32a;">✅ Đã duyệt</span>',
                                'rejected' => '<span style="color: #646970;">❌ Đã từ chối</span>',
                                'spam' => '<span style="color: #d63638;">🚫 Spam</span>'
                            );
                            echo $status_labels[$interaction->status] ?? $interaction->status;
                            ?>
                        </td>
                        
                        <td class="column-date">
                            <?php echo date('j/n/Y H:i', strtotime($interaction->created_at)); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <p>Không có tương tác nào.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Bulk Actions -->
        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <select name="bulk_action">
                    <option value="">Chọn hành động</option>
                    <option value="approve">Duyệt</option>
                    <option value="reject">Từ chối</option>
                    <option value="delete">Xóa</option>
                </select>
                <input type="submit" class="button" value="Áp dụng">
                <input type="hidden" name="action" value="bulk_action">
            </div>
        </div>
    </form>

    <!-- Pagination -->
    <?php
    $total_pages = ceil($total / $per_page);
    if ($total_pages > 1):
    ?>
    <div class="tablenav-pages">
        <?php
        $page_links = paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => '&laquo;',
            'next_text' => '&raquo;',
            'total' => $total_pages,
            'current' => $page
        ));
        echo $page_links;
        ?>
    </div>
    <?php endif; ?>

    <!-- Popular Posts -->
    <?php if ($popular_posts): ?>
    <div style="margin-top: 40px;">
        <h2>📈 Bài viết được tương tác nhiều nhất</h2>
        <table class="wp-list-table widefat">
            <thead>
                <tr>
                    <th>Bài viết</th>
                    <th>Số tương tác</th>
                    <th>Đánh giá TB</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($popular_posts as $popular): ?>
                <tr>
                    <td>
                        <?php
                        $post = get_post($popular->post_id);
                        if ($post):
                        ?>
                            <a href="<?php echo get_edit_post_link($popular->post_id); ?>" target="_blank">
                                <?php echo esc_html($post->post_title); ?>
                            </a>
                        <?php else: ?>
                            <span style="color: #d63638;">Bài viết đã xóa (ID: <?php echo $popular->post_id; ?>)</span>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo number_format($popular->interaction_count); ?></strong> tương tác</td>
                    <td>
                        <?php if ($popular->avg_rating): ?>
                            <strong><?php echo number_format($popular->avg_rating, 1); ?></strong>/5 ⭐
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?page=kata-seo-user-interactions&post=<?php echo $popular->post_id; ?>" class="button button-small">
                            Xem chi tiết
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<style>
.kata-admin-stats {
    display: flex;
    gap: 20px;
    margin: 20px 0;
}

.kata-stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    flex: 1;
}

.kata-stat-card h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
}

.wp-list-table .column-user {
    width: 15%;
}

.wp-list-table .column-content {
    width: 35%;
}

.wp-list-table .column-rating {
    width: 12%;
}

.wp-list-table .column-post {
    width: 15%;
}

.wp-list-table .column-status {
    width: 10%;
}

.wp-list-table .column-date {
    width: 13%;
}

@media (max-width: 768px) {
    .kata-admin-stats {
        flex-direction: column;
    }
}
</style>

<script>
// Select all checkbox functionality
document.getElementById('cb-select-all-1').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('input[name="interactions[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>