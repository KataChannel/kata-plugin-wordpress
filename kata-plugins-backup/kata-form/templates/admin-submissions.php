<?php
if (!defined('ABSPATH')) {
    exit;
}

// Get submissions
global $wpdb;
$table_name = $wpdb->prefix . 'kata_form_submissions';

// Pagination
$page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filters
$form_filter = isset($_GET['form_id']) ? intval($_GET['form_id']) : 0;
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Build query
$where_clauses = array();
$params = array();

if ($form_filter > 0) {
    $where_clauses[] = "form_id = %d";
    $params[] = $form_filter;
}

if ($status_filter) {
    $where_clauses[] = "status = %s";
    $params[] = $status_filter;
}

if ($search) {
    $where_clauses[] = "(form_title LIKE %s OR submission_data LIKE %s)";
    $params[] = '%' . $wpdb->esc_like($search) . '%';
    $params[] = '%' . $wpdb->esc_like($search) . '%';
}

$where_sql = empty($where_clauses) ? '' : 'WHERE ' . implode(' AND ', $where_clauses);

// Get total count
$total_query = "SELECT COUNT(*) FROM {$table_name} {$where_sql}";
if (!empty($params)) {
    $total_query = $wpdb->prepare($total_query, $params);
}
$total_items = $wpdb->get_var($total_query);

// Get submissions
$query = "SELECT * FROM {$table_name} {$where_sql} ORDER BY created_at DESC LIMIT {$per_page} OFFSET {$offset}";
if (!empty($params)) {
    $query = $wpdb->prepare($query, $params);
}
$submissions = $wpdb->get_results($query);

// Get available forms
$forms = $wpdb->get_results("SELECT DISTINCT form_id, form_title FROM {$table_name} ORDER BY form_title");

// Calculate pagination
$total_pages = ceil($total_items / $per_page);
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Dữ Liệu Form Gửi', 'kata-form'); ?>
        <span class="title-count">(<?php echo number_format($total_items); ?>)</span>
    </h1>
    
    <hr class="wp-header-end">
    
    <!-- Filters -->
    <div class="tablenav top">
        <div class="alignleft actions">
            <form method="get" action="">
                <input type="hidden" name="page" value="kata-form">
                
                <select name="form_id">
                    <option value=""><?php _e('Tất cả Form', 'kata-form'); ?></option>
                    <?php foreach ($forms as $form): ?>
                        <option value="<?php echo esc_attr($form->form_id); ?>" <?php selected($form_filter, $form->form_id); ?>>
                            <?php echo esc_html($form->form_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="status">
                    <option value=""><?php _e('Tất cả Trạng thái', 'kata-form'); ?></option>
                    <option value="new" <?php selected($status_filter, 'new'); ?>><?php _e('Mới', 'kata-form'); ?></option>
                    <option value="read" <?php selected($status_filter, 'read'); ?>><?php _e('Đã đọc', 'kata-form'); ?></option>
                    <option value="archived" <?php selected($status_filter, 'archived'); ?>><?php _e('Lưu trữ', 'kata-form'); ?></option>
                </select>
                
                <?php submit_button(__('Lọc', 'kata-form'), '', 'submit', false); ?>
                
                <a href="<?php echo admin_url('admin.php?page=kata-form'); ?>" class="button">
                    <?php _e('Đặt lại', 'kata-form'); ?>
                </a>
            </form>
        </div>
        
        <div class="alignright actions">
            <button type="button" class="button" id="kata-export-btn">
                <?php _e('Xuất Dữ Liệu', 'kata-form'); ?>
            </button>
        </div>
        
        <div class="tablenav-pages">
            <?php
            $pagination_args = array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $total_pages,
                'current' => $page
            );
            echo paginate_links($pagination_args);
            ?>
        </div>
    </div>
    
    <!-- Search Box -->
    <p class="search-box">
        <label class="screen-reader-text" for="submission-search-input"><?php _e('Tìm kiếm dữ liệu:', 'kata-form'); ?></label>
        <input type="search" id="submission-search-input" name="s" value="<?php echo esc_attr($search); ?>">
        <input type="submit" id="search-submit" class="button" value="<?php _e('Tìm Kiếm', 'kata-form'); ?>">
    </p>
    
    <!-- Submissions Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-cb check-column">
                    <input type="checkbox" id="cb-select-all-1">
                </th>
                <th scope="col" class="manage-column column-primary"><?php _e('Form & Dữ Liệu', 'kata-form'); ?></th>
                <th scope="col" class="manage-column"><?php _e('Trạng Thái', 'kata-form'); ?></th>
                <th scope="col" class="manage-column"><?php _e('Địa Chỉ IP', 'kata-form'); ?></th>
                <th scope="col" class="manage-column"><?php _e('Ngày', 'kata-form'); ?></th>
                <th scope="col" class="manage-column"><?php _e('Hành Động', 'kata-form'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($submissions)): ?>
                <tr>
                    <td colspan="6" class="no-items">
                        <?php _e('Không tìm thấy dữ liệu.', 'kata-form'); ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($submissions as $submission): ?>
                    <?php
                    $submission_data = json_decode($submission->submission_data, true);
                    $status_class = $submission->status === 'new' ? 'status-new' : 'status-' . $submission->status;
                    ?>
                    <tr class="<?php echo esc_attr($status_class); ?>">
                        <th scope="row" class="check-column">
                            <input type="checkbox" name="submission[]" value="<?php echo esc_attr($submission->id); ?>">
                        </th>
                        <td class="column-primary">
                            <strong><?php echo esc_html($submission->form_title); ?></strong>
                            <div class="submission-data">
                                <?php if ($submission_data): ?>
                                    <?php foreach ($submission_data as $key => $value): ?>
                                        <?php if (is_string($value) && !empty($value)): ?>
                                            <div class="field-data">
                                                <span class="field-label"><?php echo esc_html(ucfirst(str_replace('-', ' ', $key))); ?>:</span>
                                                <span class="field-value"><?php echo esc_html(wp_trim_words($value, 10)); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="toggle-row"><span class="screen-reader-text"><?php _e('Xem thêm chi tiết', 'kata-form'); ?></span></button>
                        </td>
                        <td>
                            <select class="status-select" data-submission-id="<?php echo esc_attr($submission->id); ?>">
                                <option value="new" <?php selected($submission->status, 'new'); ?>><?php _e('Mới', 'kata-form'); ?></option>
                                <option value="read" <?php selected($submission->status, 'read'); ?>><?php _e('Đã đọc', 'kata-form'); ?></option>
                                <option value="archived" <?php selected($submission->status, 'archived'); ?>><?php _e('Lưu trữ', 'kata-form'); ?></option>
                            </select>
                        </td>
                        <td>
                            <?php if ($submission->ip_address): ?>
                                <code><?php echo esc_html($submission->ip_address); ?></code>
                            <?php else: ?>
                                <span class="dashicons dashicons-minus"></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <abbr title="<?php echo esc_attr($submission->created_at); ?>">
                                <?php echo esc_html(human_time_diff(strtotime($submission->created_at), current_time('timestamp')) . ' trước'); ?>
                            </abbr>
                        </td>
                        <td>
                            <button type="button" class="button button-small view-submission" data-submission-id="<?php echo esc_attr($submission->id); ?>">
                                <?php _e('Xem', 'kata-form'); ?>
                            </button>
                            <button type="button" class="button button-small button-link-delete delete-submission" data-submission-id="<?php echo esc_attr($submission->id); ?>">
                                <?php _e('Xóa', 'kata-form'); ?>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <!-- Bottom Pagination -->
    <div class="tablenav bottom">
        <div class="tablenav-pages">
            <?php echo paginate_links($pagination_args); ?>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="kata-export-modal" class="kata-modal" style="display: none;">
    <div class="kata-modal-content">
        <div class="kata-modal-header">
            <h3><?php _e('Export Submissions', 'kata-form'); ?></h3>
            <span class="kata-modal-close">&times;</span>
        </div>
        <div class="kata-modal-body">
            <form id="kata-export-form">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Format', 'kata-form'); ?></th>
                        <td>
                            <select name="format">
                                <option value="csv"><?php _e('CSV', 'kata-form'); ?></option>
                                <option value="json"><?php _e('JSON', 'kata-form'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Form', 'kata-form'); ?></th>
                        <td>
                            <select name="form_id">
                                <option value=""><?php _e('All Forms', 'kata-form'); ?></option>
                                <?php foreach ($forms as $form): ?>
                                    <option value="<?php echo esc_attr($form->form_id); ?>">
                                        <?php echo esc_html($form->form_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Khoảng Thời Gian', 'kata-form'); ?></th>
                        <td>
                            <input type="date" name="date_from" placeholder="<?php _e('Từ', 'kata-form'); ?>">
                            <input type="date" name="date_to" placeholder="<?php _e('Đến', 'kata-form'); ?>">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="kata-modal-footer">
            <button type="button" class="button button-primary" id="kata-export-submit">
                <?php _e('Xuất Dữ Liệu', 'kata-form'); ?>
            </button>
            <button type="button" class="button kata-modal-close">
                <?php _e('Hủy', 'kata-form'); ?>
            </button>
        </div>
    </div>
</div>

<!-- Submission Detail Modal -->
<div id="kata-submission-modal" class="kata-modal" style="display: none;">
    <div class="kata-modal-content">
        <div class="kata-modal-header">
            <h3><?php _e('Chi Tiết Dữ Liệu', 'kata-form'); ?></h3>
            <span class="kata-modal-close">&times;</span>
        </div>
        <div class="kata-modal-body" id="kata-submission-details">
            <!-- Content loaded via AJAX -->
        </div>
        <div class="kata-modal-footer">
            <button type="button" class="button kata-modal-close">
                <?php _e('Đóng', 'kata-form'); ?>
            </button>
        </div>
    </div>
</div>
