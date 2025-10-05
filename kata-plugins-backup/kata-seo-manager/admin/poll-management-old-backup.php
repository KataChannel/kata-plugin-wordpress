<?php
/**
 * Poll Management Admin Page - Professional Senior UI
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get polls from database
$polls = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kata_polls ORDER BY created_at DESC");
$current_poll = null;

// Handle edit mode
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $current_poll = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}kata_polls WHERE id = %d",
        $edit_id
    ));
}

?>
<div class="wrap kata-poll-management-wrap">
    <!-- Professional Header -->
    <div class="kata-admin-header">
        <div class="kata-header-content">
            <h1 class="kata-page-title">
                <span class="dashicons dashicons-chart-bar"></span>
                Quản lý Poll
            </h1>
            <p class="kata-page-subtitle">Tạo và quản lý các poll tương tác với người dùng</p>
        </div>
        <div class="kata-header-stats">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count($polls); ?></div>
                    <div class="stat-label">Tổng Polls</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count(array_filter($polls, function($p) { return $p->status === 'active'; })); ?></div>
                    <div class="stat-label">Đang hoạt động</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo array_sum(array_column($polls, 'total_votes')); ?></div>
                    <div class="stat-label">Tổng Votes</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="kata-poll-admin-container">
        <!-- Form Section -->
        <div class="kata-poll-form-card">
            <div class="card-header">
                <h2 class="card-title">
                    <span class="dashicons dashicons-edit"></span>
                    <?php echo $current_poll ? '✏️ Chỉnh Sửa Poll' : '➕ Tạo Poll Mới'; ?>
                </h2>
            </div>
            <div class="card-body">
                <form method="post" action="" class="kata-poll-form">
                    <?php wp_nonce_field('kata_poll_action'); ?>
                    <input type="hidden" name="kata_poll_action" value="<?php echo $current_poll ? 'update' : 'create'; ?>">
                    <?php if ($current_poll): ?>
                        <input type="hidden" name="poll_id" value="<?php echo $current_poll->id; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="poll_title" class="form-label">
                            <span class="dashicons dashicons-admin-post"></span>
                            Tiêu đề Poll
                        </label>
                        <input type="text" id="poll_title" name="poll_title" 
                               value="<?php echo $current_poll ? esc_attr($current_poll->poll_title) : ''; ?>" 
                               class="form-control" placeholder="Nhập tiêu đề cho poll..." required>
                        <p class="field-description">Tiêu đề hiển thị cho poll</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="poll_description" class="form-label">
                            <span class="dashicons dashicons-text"></span>
                            Mô tả
                        </label>
                        <textarea id="poll_description" name="poll_description" 
                                  rows="3" class="form-control" placeholder="Mô tả ngắn về poll..."><?php echo $current_poll ? esc_textarea($current_poll->poll_description) : ''; ?></textarea>
                        <p class="field-description">Mô tả ngắn về poll này</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="poll_question" class="form-label">
                            <span class="dashicons dashicons-editor-help"></span>
                            Câu hỏi Poll
                        </label>
                        <textarea id="poll_question" name="poll_question" 
                                  rows="2" class="form-control" placeholder="Câu hỏi của bạn..." required><?php echo $current_poll ? esc_textarea($current_poll->poll_question) : ''; ?></textarea>
                        <p class="field-description">Câu hỏi chính của poll</p>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <span class="dashicons dashicons-forms"></span>
                            Tùy chọn trả lời
                        </label>
                        <div id="poll-options-container" class="options-container">
                            <?php
                            $options = array();
                            if ($current_poll && !empty($current_poll->poll_options)) {
                                $options = json_decode($current_poll->poll_options, true);
                            }
                            
                            // Show at least 2 options for new poll, or existing options
                            $min_options = max(2, count($options));
                            for ($i = 0; $i < max($min_options, 4); $i++):
                                $option_value = isset($options[$i]) ? $options[$i] : '';
                            ?>
                            <div class="poll-option-row">
                                <span class="option-number"><?php echo $i + 1; ?></span>
                                <input type="text" name="poll_options[]" 
                                       value="<?php echo esc_attr($option_value); ?>" 
                                       placeholder="Nhập tùy chọn <?php echo $i + 1; ?>" 
                                       class="form-control option-input">
                                <button type="button" class="kata-button kata-button-icon remove-option" 
                                        <?php echo $i < 2 ? 'style="display:none;"' : ''; ?>>
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <button type="button" id="add-option" class="kata-button kata-button-secondary kata-button-small">
                            <span class="dashicons dashicons-plus-alt"></span>
                            Thêm tùy chọn
                        </button>
                        <p class="field-description">Cần ít nhất 2 tùy chọn. Để trống để bỏ qua.</p>
                    </div>
                    
                    <?php if ($current_poll): ?>
                    <div class="form-group">
                        <label for="poll_status" class="form-label">
                            <span class="dashicons dashicons-admin-settings"></span>
                            Trạng thái
                        </label>
                        <select id="poll_status" name="poll_status" class="form-control">
                            <option value="active" <?php selected($current_poll->status, 'active'); ?>>🟢 Hoạt động</option>
                            <option value="closed" <?php selected($current_poll->status, 'closed'); ?>>🔴 Đã đóng</option>
                            <option value="draft" <?php selected($current_poll->status, 'draft'); ?>>🟡 Nháp</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-actions">
                        <button type="submit" class="kata-button kata-button-primary">
                            <span class="dashicons dashicons-saved"></span>
                            <?php echo $current_poll ? 'Cập nhật Poll' : 'Tạo Poll'; ?>
                        </button>
                        <?php if ($current_poll): ?>
                            <a href="?page=kata-seo-poll-management" class="kata-button kata-button-secondary">
                                <span class="dashicons dashicons-no"></span>
                                Hủy chỉnh sửa
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- List Section -->
        <div class="kata-poll-list-card">
            <div class="card-header">
                <h2 class="card-title">
                    <span class="dashicons dashicons-list-view"></span>
                    📋 Danh sách Poll
                </h2>
            </div>
            <div class="card-body">
            
            <?php if (empty($polls)): ?>
                <div class="empty-state">
                    <span class="dashicons dashicons-info-outline"></span>
                    <p>Chưa có poll nào được tạo. Hãy tạo poll đầu tiên!</p>
                </div>
            <?php else: ?>
                <div class="polls-table-wrapper">
                    <table class="kata-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Thông tin</th>
                                <th>Tùy chọn</th>
                                <th>Votes</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($polls as $poll): 
                                $options = json_decode($poll->poll_options, true);
                                $option_count = is_array($options) ? count($options) : 0;
                            ?>
                            <tr>
                                <td><div class="poll-id">#<?php echo $poll->id; ?></div></td>
                                <td>
                                    <div class="poll-info">
                                        <div class="poll-title"><?php echo esc_html($poll->poll_title); ?></div>
                                        <div class="poll-question"><?php echo esc_html(wp_trim_words($poll->poll_question, 12)); ?></div>
                                        <div class="shortcode-display">
                                            <code>[kata_poll id="<?php echo $poll->id; ?>"]</code>
                                            <button type="button" class="copy-shortcode" data-shortcode='[kata_poll id="<?php echo $poll->id; ?>"]' title="Copy shortcode">
                                                <span class="dashicons dashicons-clipboard"></span>
                                            </button>
                                        </div>
                                        <div class="poll-meta">
                                            <span class="dashicons dashicons-calendar"></span>
                                            <?php echo date('d/m/Y H:i', strtotime($poll->created_at)); ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="option-badge"><?php echo $option_count; ?> tùy chọn</div>
                                </td>
                                <td>
                                    <div class="votes-info">
                                        <div class="votes-count"><?php echo $poll->total_votes; ?></div>
                                        <?php if ($poll->total_votes > 0): ?>
                                            <a href="?page=kata-seo-poll-analytics&poll_id=<?php echo $poll->id; ?>" class="view-analytics">
                                                <span class="dashicons dashicons-chart-line"></span>
                                                Chi tiết
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="poll-status-badge poll-status-<?php echo $poll->status; ?>">
                                        <?php
                                        switch ($poll->status) {
                                            case 'active': echo '<span class="dashicons dashicons-yes-alt"></span> Hoạt động'; break;
                                            case 'closed': echo '<span class="dashicons dashicons-dismiss"></span> Đã đóng'; break;
                                            case 'draft': echo '<span class="dashicons dashicons-edit"></span> Nháp'; break;
                                            default: echo $poll->status;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?page=kata-seo-poll-management&edit=<?php echo $poll->id; ?>" 
                                           class="kata-button kata-button-small kata-button-edit" title="Chỉnh sửa">
                                            <span class="dashicons dashicons-edit"></span>
                                        </a>
                                        
                                        <form method="post" style="display: inline;">
                                            <?php wp_nonce_field('kata_poll_action'); ?>
                                            <input type="hidden" name="kata_poll_action" value="toggle_status">
                                            <input type="hidden" name="poll_id" value="<?php echo $poll->id; ?>">
                                            <input type="hidden" name="new_status" value="<?php echo $poll->status === 'active' ? 'closed' : 'active'; ?>">
                                            <button type="submit" class="kata-button kata-button-small kata-button-toggle" 
                                                    title="<?php echo $poll->status === 'active' ? 'Tạm dừng' : 'Kích hoạt'; ?>"
                                                    onclick="return confirm('Bạn có chắc muốn thay đổi trạng thái?')">
                                                <span class="dashicons dashicons-controls-<?php echo $poll->status === 'active' ? 'pause' : 'play'; ?>"></span>
                                            </button>
                                        </form>
                                        
                                        <form method="post" style="display: inline;">
                                            <?php wp_nonce_field('kata_poll_action'); ?>
                                            <input type="hidden" name="kata_poll_action" value="delete">
                                            <input type="hidden" name="poll_id" value="<?php echo $poll->id; ?>">
                                            <button type="submit" class="kata-button kata-button-small kata-button-delete" 
                                                    title="Xóa"
                                                    onclick="return confirm('Bạn có chắc muốn xóa poll này? Tất cả votes sẽ bị mất!')">
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Professional Senior UI Styles */
.kata-poll-management-wrap {
    background: #f5f7fa;
    margin: -20px -20px 0 -22px;
    padding: 0;
}

.kata-admin-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.kata-header-content {
    margin-bottom: 25px;
}

.kata-page-title {
    color: white;
    font-size: 28px;
    font-weight: 700;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.kata-page-title .dashicons {
    font-size: 32px;
    width: 32px;
    height: 32px;
}

.kata-page-subtitle {
    color: rgba(255,255,255,0.9);
    font-size: 15px;
    margin: 0;
}

.kata-header-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.stat-card {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 20px 25px;
    display: flex;
    align-items: center;
    gap: 15px;
    min-width: 180px;
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

.stat-card:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.stat-icon {
    font-size: 36px;
    line-height: 1;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: white;
    line-height: 1.2;
}

.stat-label {
    font-size: 12px;
    color: rgba(255,255,255,0.85);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.kata-poll-admin-container {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 25px;
    padding: 30px 40px;
    max-width: 1600px;
}

.kata-poll-form-card,
.kata-poll-list-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
    padding: 20px 25px;
    border-bottom: 2px solid #e1e8ed;
}

.card-title {
    font-size: 18px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-title .dashicons {
    color: #667eea;
}

.card-body {
    padding: 25px;
}

/* Form Styles */
.kata-poll-form .form-group {
    margin-bottom: 25px;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-label .dashicons {
    color: #667eea;
    font-size: 18px;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e1e8ed;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.form-control:focus {
    outline: none;
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.field-description {
    font-size: 12px;
    color: #6c757d;
    margin: 6px 0 0 0;
    font-style: italic;
}

.options-container {
    margin-bottom: 12px;
}

.poll-option-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.option-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
    flex-shrink: 0;
}

.option-input {
    flex: 1;
}

/* Buttons */
.kata-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    background: #e9ecef;
    color: #495057;
}

.kata-button:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.kata-button-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.kata-button-primary:hover {
    background: linear-gradient(135deg, #5568d3 0%, #63408b 100%);
    color: white;
}

.kata-button-secondary {
    background: #6c757d;
    color: white;
}

.kata-button-secondary:hover {
    background: #5a6268;
    color: white;
}

.kata-button-small {
    padding: 6px 12px;
    font-size: 12px;
}

.kata-button-icon {
    padding: 6px 8px;
}

.kata-button-edit {
    background: #17a2b8;
    color: white;
}

.kata-button-edit:hover {
    background: #138496;
}

.kata-button-toggle {
    background: #ffc107;
    color: #212529;
}

.kata-button-toggle:hover {
    background: #e0a800;
}

.kata-button-delete {
    background: #dc3545;
    color: white;
}

.kata-button-delete:hover {
    background: #c82333;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f1f3f5;
}

/* Table Styles */
.kata-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.kata-table thead {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.kata-table th {
    padding: 14px 12px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #dee2e6;
}

.kata-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #f1f3f5;
    font-size: 14px;
}

.kata-table tbody tr {
    transition: all 0.2s ease;
}

.kata-table tbody tr:hover {
    background: #f8f9fa;
}

.poll-id {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 13px;
    display: inline-block;
}

.poll-info {
    max-width: 400px;
}

.poll-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 4px;
    font-size: 15px;
}

.poll-question {
    color: #6c757d;
    font-size: 13px;
    margin-bottom: 8px;
}

.shortcode-display {
    margin: 8px 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.shortcode-display code {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    border: 1px solid #dee2e6;
    font-family: 'Courier New', monospace;
}

.copy-shortcode {
    background: none;
    border: none;
    cursor: pointer;
    color: #667eea;
    padding: 2px;
    display: flex;
    align-items: center;
    transition: all 0.2s ease;
}

.copy-shortcode:hover {
    color: #764ba2;
    transform: scale(1.1);
}

.poll-meta {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #adb5bd;
    margin-top: 6px;
}

.poll-meta .dashicons {
    font-size: 14px;
}

.option-badge {
    background: #e7f3ff;
    color: #0066cc;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.votes-info {
    text-align: center;
}

.votes-count {
    font-size: 20px;
    font-weight: 700;
    color: #28a745;
    margin-bottom: 4px;
}

.view-analytics {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #667eea;
    text-decoration: none;
    transition: all 0.2s ease;
}

.view-analytics:hover {
    color: #764ba2;
}

.poll-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.poll-status-active {
    background: #d4edda;
    color: #155724;
}

.poll-status-closed {
    background: #f8d7da;
    color: #721c24;
}

.poll-status-draft {
    background: #fff3cd;
    color: #856404;
}

.action-buttons {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state .dashicons {
    font-size: 64px;
    width: 64px;
    height: 64px;
    opacity: 0.3;
    margin-bottom: 16px;
}

.empty-state p {
    font-size: 16px;
    margin: 0;
}

/* Notification Styles */
.kata-notification {
    position: fixed;
    top: 32px;
    right: 20px;
    background: white;
    padding: 16px 24px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    z-index: 999999;
    font-size: 14px;
    font-weight: 500;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.3s ease;
}

.kata-notification.show {
    opacity: 1;
    transform: translateX(0);
}

.kata-notification-success {
    border-left: 4px solid #28a745;
    color: #155724;
}

.kata-notification-error {
    border-left: 4px solid #dc3545;
    color: #721c24;
}

/* Responsive */
@media (max-width: 1200px) {
    .kata-poll-admin-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .kata-admin-header {
        padding: 20px;
    }
    
    .kata-poll-admin-container {
        padding: 20px;
    }
    
    .kata-header-stats {
        flex-direction: column;
    }
    
    .stat-card {
        width: 100%;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Add option functionality
    $('#add-option').click(function() {
        var container = $('#poll-options-container');
        var currentCount = container.find('.poll-option-row').length;
        var newRow = $('<div class="poll-option-row">' +
            '<span class="option-number">' + (currentCount + 1) + '</span>' +
            '<input type="text" name="poll_options[]" value="" placeholder="Nhập tùy chọn ' + (currentCount + 1) + '" class="form-control option-input">' +
            '<button type="button" class="kata-button kata-button-icon kata-button-delete remove-option"><span class="dashicons dashicons-trash"></span></button>' +
            '</div>');
        container.append(newRow);
        updateOptionNumbers();
    });
    
    // Remove option functionality
    $(document).on('click', '.remove-option', function() {
        var container = $('#poll-options-container');
        var rows = container.find('.poll-option-row');
        if (rows.length > 2) {
            $(this).closest('.poll-option-row').remove();
            updateOptionNumbers();
        } else {
            alert('Cần ít nhất 2 tùy chọn!');
        }
    });
    
    // Update option numbers
    function updateOptionNumbers() {
        $('#poll-options-container .poll-option-row').each(function(index) {
            $(this).find('.option-number').text(index + 1);
            $(this).find('.option-input').attr('placeholder', 'Nhập tùy chọn ' + (index + 1));
        });
    }
    
    // Copy shortcode functionality with modern feedback
    $('.copy-shortcode').click(function(e) {
        e.preventDefault();
        var $btn = $(this);
        var shortcode = $(this).data('shortcode');
        
        navigator.clipboard.writeText(shortcode).then(function() {
            // Visual feedback
            var originalHTML = $btn.html();
            $btn.html('<span class="dashicons dashicons-yes"></span>');
            $btn.css('color', '#28a745');
            
            setTimeout(function() {
                $btn.html(originalHTML);
                $btn.css('color', '');
            }, 2000);
            
            // Modern notification
            showNotification('✅ Đã copy shortcode: ' + shortcode, 'success');
        }).catch(function() {
            // Fallback for older browsers
            var textArea = document.createElement('textarea');
            textArea.value = shortcode;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showNotification('✅ Đã copy shortcode: ' + shortcode, 'success');
        });
    });
    
    // Modern notification system
    function showNotification(message, type) {
        var $notification = $('<div class="kata-notification kata-notification-' + type + '">' + message + '</div>');
        $('body').append($notification);
        
        setTimeout(function() {
            $notification.addClass('show');
        }, 10);
        
        setTimeout(function() {
            $notification.removeClass('show');
            setTimeout(function() {
                $notification.remove();
            }, 300);
        }, 3000);
    }
});
</script>
