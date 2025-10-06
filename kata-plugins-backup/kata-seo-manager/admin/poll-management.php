<?php
/**
 * Poll Management Admin Page - Modern Modal & Toggle UI
 * Senior Developer Level Design
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get polls with stats
$polls = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kata_polls ORDER BY created_at DESC");

// Calculate statistics
$total_polls = count($polls);
$active_polls = count(array_filter($polls, function($p) { return $p->active == 1; }));
$total_votes = array_sum(array_column($polls, 'total_votes'));
$closed_polls = count(array_filter($polls, function($p) { return $p->active == 0; }));

// Include styles and scripts
include_once(__DIR__ . '/poll-management-styles.php');
include_once(__DIR__ . '/poll-management-scripts.php');
?>

<div class="wrap kata-poll-management-wrap">
    
    <!-- Modern Header with Actions -->
    <div class="kata-poll-header">
        <div class="header-left">
            <h1 class="kata-page-title">
                <span class="dashicons dashicons-chart-bar"></span>
                Quản lý Poll
            </h1>
            <p class="kata-page-subtitle">Tạo và quản lý các poll tương tác với người dùng</p>
        </div>
        <div class="header-actions">
            <button type="button" class="kata-button kata-button-primary" id="create-poll-btn">
                <span class="dashicons dashicons-plus-alt"></span>
                Tạo Poll Mới
            </button>
            <button type="button" class="kata-button kata-button-secondary" id="toggle-list-btn">
                <span class="dashicons dashicons-list-view"></span>
                <span class="toggle-text">Ẩn Danh Sách</span>
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="kata-stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-chart-bar"></span>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $total_polls; ?></div>
                <div class="stat-label">Tổng Polls</div>
            </div>
        </div>
        
        <div class="stat-card stat-card-success">
            <div class="stat-icon">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $active_polls; ?></div>
                <div class="stat-label">Đang hoạt động</div>
            </div>
        </div>
        
        <div class="stat-card stat-card-info">
            <div class="stat-icon">
                <span class="dashicons dashicons-groups"></span>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo number_format($total_votes); ?></div>
                <div class="stat-label">Tổng Votes</div>
            </div>
        </div>
        
        <div class="stat-card stat-card-warning">
            <div class="stat-icon">
                <span class="dashicons dashicons-lock"></span>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?php echo $closed_polls; ?></div>
                <div class="stat-label">Đã đóng</div>
            </div>
        </div>
    </div>

    <!-- Polls List Container (Collapsible) -->
    <div class="kata-polls-list-container" id="polls-list-container">
        <div class="list-header">
            <h2>
                <span class="dashicons dashicons-list-view"></span>
                Danh sách Poll
            </h2>
            <div class="list-controls">
                <input type="text" id="search-polls" class="search-input" placeholder="🔍 Tìm kiếm poll...">
                <select id="filter-status" class="filter-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Đang hoạt động</option>
                    <option value="closed">Đã đóng</option>
                    <option value="draft">Nháp</option>
                </select>
            </div>
        </div>

        <?php if (empty($polls)): ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <span class="dashicons dashicons-chart-bar"></span>
                </div>
                <h3>Chưa có poll nào</h3>
                <p>Bắt đầu tạo poll đầu tiên để thu thập ý kiến người dùng!</p>
                <button type="button" class="kata-button kata-button-primary" id="create-first-poll">
                    <span class="dashicons dashicons-plus-alt"></span>
                    Tạo Poll Đầu Tiên
                </button>
            </div>
        <?php else: ?>
            <div class="polls-grid" id="polls-grid">
                <?php foreach ($polls as $poll): 
                    $options = json_decode($poll->options, true);
                    $option_count = is_array($options) ? count($options) : 0;
                    
                    // Map active column to status strings
                    $status = $poll->active ? 'active' : 'closed';
                    
                    // Calculate status class
                    $status_class = 'status-' . $status;
                    $status_text = '';
                    $status_icon = '';
                    
                    switch ($status) {
                        case 'active':
                            $status_text = 'Hoạt động';
                            $status_icon = '<span class="status-dot"></span>';
                            break;
                        case 'closed':
                            $status_text = 'Đã đóng';
                            $status_icon = '<span class="dashicons dashicons-lock"></span>';
                            break;
                        case 'draft':
                            $status_text = 'Nháp';
                            $status_icon = '<span class="dashicons dashicons-edit"></span>';
                            break;
                    }
                ?>
                <div class="poll-card" data-poll-id="<?php echo $poll->id; ?>" data-status="<?php echo $status; ?>">
                    <div class="poll-card-header">
                        <div class="poll-status <?php echo $status_class; ?>">
                            <?php echo $status_icon . ' ' . $status_text; ?>
                        </div>
                        <div class="poll-id">#<?php echo $poll->id; ?></div>
                    </div>
                    
                    <div class="poll-card-body">
                        <h3 class="poll-title"><?php echo esc_html($poll->poll_title); ?></h3>
                        <p class="poll-question"><?php echo esc_html(wp_trim_words($poll->poll_question, 15)); ?></p>
                        
                        <div class="poll-meta">
                            <div class="meta-item">
                                <span class="dashicons dashicons-forms"></span>
                                <span><?php echo $option_count; ?> tùy chọn</span>
                            </div>
                            <div class="meta-item">
                                <span class="dashicons dashicons-groups"></span>
                                <span><?php echo $poll->total_votes; ?> votes</span>
                            </div>
                            <div class="meta-item">
                                <span class="dashicons dashicons-calendar"></span>
                                <span><?php echo date('d/m/Y', strtotime($poll->created_at)); ?></span>
                            </div>
                        </div>
                        
                        <div class="poll-shortcode">
                            <code>[kata_poll id="<?php echo $poll->id; ?>"]</code>
                            <button type="button" class="copy-shortcode-btn" data-shortcode='[kata_poll id="<?php echo $poll->id; ?>"]' title="Copy shortcode">
                                <span class="dashicons dashicons-clipboard"></span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="poll-card-footer">
                        <button type="button" class="action-btn edit-poll-btn" data-poll-id="<?php echo $poll->id; ?>" title="Chỉnh sửa">
                            <span class="dashicons dashicons-edit"></span>
                            Sửa
                        </button>
                        
                        <?php if ($poll->total_votes > 0): ?>
                        <a href="?page=kata-seo-poll-analytics&poll_id=<?php echo $poll->id; ?>" class="action-btn analytics-btn" title="Xem phân tích">
                            <span class="dashicons dashicons-chart-line"></span>
                            Phân tích
                        </a>
                        <?php endif; ?>
                        
                        <button type="button" class="action-btn toggle-status-btn" data-poll-id="<?php echo $poll->id; ?>" data-status="<?php echo $status; ?>" title="<?php echo $status === 'active' ? 'Tạm dừng' : 'Kích hoạt'; ?>">
                            <span class="dashicons dashicons-controls-<?php echo $status === 'active' ? 'pause' : 'play'; ?>"></span>
                        </button>
                        
                        <button type="button" class="action-btn delete-poll-btn" data-poll-id="<?php echo $poll->id; ?>" title="Xóa">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal for Create/Edit Poll -->
    <div class="kata-modal" id="poll-modal">
        <div class="kata-modal-overlay"></div>
        <div class="kata-modal-container">
            <div class="kata-modal-header">
                <h2 id="modal-title">
                    <span class="dashicons dashicons-plus-alt"></span>
                    <span id="modal-title-text">Tạo Poll Mới</span>
                </h2>
                <button type="button" class="kata-modal-close" id="close-modal">
                    <span class="dashicons dashicons-no"></span>
                </button>
            </div>
            
            <div class="kata-modal-body">
                <form method="post" action="" class="kata-poll-form" id="poll-form">
                    <?php wp_nonce_field('kata_poll_action'); ?>
                    <input type="hidden" name="kata_poll_action" id="form-action" value="create">
                    <input type="hidden" name="poll_id" id="poll-id" value="">
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="poll_title" class="form-label">
                                <span class="dashicons dashicons-admin-post"></span>
                                Tiêu đề Poll <span class="required">*</span>
                            </label>
                            <input type="text" id="poll_title" name="poll_title" 
                                   class="form-control" placeholder="VD: Bạn thích màu gì nhất?" required>
                            <p class="field-hint">Tiêu đề ngắn gọn, thu hút</p>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="poll_description" class="form-label">
                                <span class="dashicons dashicons-text"></span>
                                Mô tả
                            </label>
                            <textarea id="poll_description" name="poll_description" 
                                      rows="2" class="form-control" placeholder="Mô tả ngắn về poll này (optional)"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="poll_question" class="form-label">
                                <span class="dashicons dashicons-editor-help"></span>
                                Câu hỏi Poll <span class="required">*</span>
                            </label>
                            <textarea id="poll_question" name="poll_question" 
                                      rows="2" class="form-control" placeholder="Câu hỏi chính của poll..." required></textarea>
                            <p class="field-hint">Câu hỏi rõ ràng, dễ hiểu</p>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label class="form-label">
                                <span class="dashicons dashicons-forms"></span>
                                Tùy chọn trả lời <span class="required">*</span>
                            </label>
                            <div id="poll-options-container" class="options-container">
                                <!-- Options will be added dynamically -->
                            </div>
                            <button type="button" id="add-option-btn" class="kata-button kata-button-secondary kata-button-small">
                                <span class="dashicons dashicons-plus-alt"></span>
                                Thêm tùy chọn
                            </button>
                            <p class="field-hint">Cần ít nhất 2 tùy chọn. Để trống để bỏ qua.</p>
                        </div>
                    </div>
                    
                    <div class="form-row" id="status-row" style="display: none;">
                        <div class="form-group full-width">
                            <label for="poll_status" class="form-label">
                                <span class="dashicons dashicons-admin-settings"></span>
                                Trạng thái
                            </label>
                            <select id="poll_status" name="poll_status" class="form-control">
                                <option value="active">🟢 Hoạt động</option>
                                <option value="closed">🔴 Đã đóng</option>
                                <option value="draft">🟡 Nháp</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="kata-modal-footer">
                        <button type="button" class="kata-button kata-button-secondary" id="cancel-btn">
                            <span class="dashicons dashicons-no"></span>
                            Hủy
                        </button>
                        <button type="submit" class="kata-button kata-button-primary" id="submit-btn">
                            <span class="dashicons dashicons-saved"></span>
                            <span id="submit-btn-text">Tạo Poll</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden forms for toggle/delete actions -->
    <form method="post" id="toggle-status-form" style="display: none;">
        <?php wp_nonce_field('kata_poll_action'); ?>
        <input type="hidden" name="kata_poll_action" value="toggle_status">
        <input type="hidden" name="poll_id" id="toggle-poll-id" value="">
        <input type="hidden" name="new_status" id="toggle-new-status" value="">
    </form>
    
    <form method="post" id="delete-poll-form" style="display: none;">
        <?php wp_nonce_field('kata_poll_action'); ?>
        <input type="hidden" name="kata_poll_action" value="delete">
        <input type="hidden" name="poll_id" id="delete-poll-id" value="">
    </form>

</div>
