<?php
/**
 * Wheel Management Admin Page - Senior UI/UX with Modal & Smooth Animations
 * 
 * Features:
 * - Modal for create/edit
 * - Collapsible list with toggle
 * - Search & filter
 * - Smooth animations
 * - Responsive design
 */

if (!defined('ABSPATH')) exit;

global $wpdb;
$wheels = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kata_wheels ORDER BY created_at DESC");
$current_wheel = null;
$edit_mode = false;

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $current_wheel = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}kata_wheels WHERE id = %d",
        $edit_id
    ));
    $edit_mode = true;
}
?>

<div class="wrap kata-wheel-management-wrap">
    <!-- Modern Header with Actions -->
    <div class="kata-admin-header">
        <div class="kata-header-left">
            <div class="kata-header-content">
                <h1 class="kata-page-title">
                    <span class="dashicons dashicons-update"></span>
                    Quản lý Vòng Quay May Mắn
                </h1>
                <p class="kata-page-subtitle">Tạo và quản lý vòng quay tương tác với người dùng</p>
            </div>
        </div>
        <div class="kata-header-actions">
            <button type="button" id="openCreateWheelModal" class="kata-button kata-button-primary kata-button-lg">
                <span class="dashicons dashicons-plus-alt"></span>
                Tạo Vòng Quay Mới
            </button>
            <button type="button" id="toggleWheelList" class="kata-button kata-button-secondary kata-button-lg">
                <span class="dashicons dashicons-list-view"></span>
                <span class="toggle-text">Ẩn</span> Danh Sách
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="kata-stats-container">
        <div class="stat-card stat-card-primary">
            <div class="stat-icon">🎡</div>
            <div class="stat-info">
                <div class="stat-value"><?php echo count($wheels); ?></div>
                <div class="stat-label">Tổng Wheels</div>
            </div>
            <div class="stat-trend">+<?php echo count($wheels); ?></div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-icon">✅</div>
            <div class="stat-info">
                <div class="stat-value"><?php echo count(array_filter($wheels, function($w) { return $w->status === 'active'; })); ?></div>
                <div class="stat-label">Đang hoạt động</div>
            </div>
            <div class="stat-trend positive">↗ Active</div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-icon">🎁</div>
            <div class="stat-info">
                <div class="stat-value"><?php echo array_sum(array_column($wheels, 'total_prizes_won')); ?></div>
                <div class="stat-label">Giải đã trao</div>
            </div>
            <div class="stat-trend positive">↗ Rewards</div>
        </div>
        <div class="stat-card stat-card-info">
            <div class="stat-icon">🎯</div>
            <div class="stat-info">
                <div class="stat-value"><?php echo array_sum(array_column($wheels, 'total_spins')); ?></div>
                <div class="stat-label">Tổng lượt quay</div>
            </div>
            <div class="stat-trend positive">↗ Spins</div>
        </div>
    </div>

    <!-- Wheels List (Collapsible) -->
    <div id="wheelsListContainer" class="kata-wheels-list-container" style="display: block;">
        <div class="kata-wheel-list-card">
            <div class="card-header">
                <h2 class="card-title">
                    <span class="dashicons dashicons-list-view"></span>
                    📋 Danh sách Vòng Quay (<?php echo count($wheels); ?>)
                </h2>
                <div class="card-header-actions">
                    <input type="text" id="searchWheels" class="kata-search-input" placeholder="🔍 Tìm kiếm vòng quay...">
                    <select id="filterStatus" class="kata-filter-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active">🟢 Hoạt động</option>
                        <option value="inactive">🔴 Tạm dừng</option>
                        <option value="scheduled">🟡 Đã lên lịch</option>
                        <option value="expired">⚫ Hết hạn</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
            <?php if (empty($wheels)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🎡</div>
                    <h3>Chưa có vòng quay nào</h3>
                    <p>Hãy tạo vòng quay đầu tiên của bạn!</p>
                    <button type="button" class="kata-button kata-button-primary open-modal-btn">
                        <span class="dashicons dashicons-plus-alt"></span>
                        Tạo Vòng Quay Ngay
                    </button>
                </div>
            <?php else: ?>
                <div class="wheels-grid">
                    <?php foreach ($wheels as $wheel): 
                        $prize_count = $wpdb->get_var($wpdb->prepare(
                            "SELECT COUNT(*) FROM {$wpdb->prefix}kata_wheel_prizes WHERE wheel_id = %d",
                            $wheel->id
                        ));
                    ?>
                    <div class="wheel-card" data-status="<?php echo $wheel->status; ?>" data-title="<?php echo esc_attr(strtolower($wheel->wheel_title)); ?>">
                        <div class="wheel-card-header">
                            <div class="wheel-id-badge">#<?php echo $wheel->id; ?></div>
                            <span class="wheel-status-badge wheel-status-<?php echo $wheel->status; ?>">
                                <?php
                                switch ($wheel->status) {
                                    case 'active': echo '<span class="status-dot"></span> Hoạt động'; break;
                                    case 'inactive': echo '<span class="status-dot"></span> Tạm dừng'; break;
                                    case 'scheduled': echo '<span class="status-dot"></span> Lên lịch'; break;
                                    case 'expired': echo '<span class="status-dot"></span> Hết hạn'; break;
                                }
                                ?>
                            </span>
                        </div>
                        
                        <div class="wheel-card-body">
                            <h3 class="wheel-title"><?php echo esc_html($wheel->wheel_title); ?></h3>
                            <p class="wheel-desc"><?php echo esc_html(wp_trim_words($wheel->wheel_description, 12)); ?></p>
                            
                            <div class="wheel-stats-mini">
                                <div class="stat-mini">
                                    <span class="stat-mini-icon">🎁</span>
                                    <span class="stat-mini-value"><?php echo $prize_count; ?></span>
                                    <span class="stat-mini-label">Giải</span>
                                </div>
                                <div class="stat-mini">
                                    <span class="stat-mini-icon">🎯</span>
                                    <span class="stat-mini-value"><?php echo $wheel->total_spins; ?></span>
                                    <span class="stat-mini-label">Lượt</span>
                                </div>
                                <div class="stat-mini">
                                    <span class="stat-mini-icon">🏆</span>
                                    <span class="stat-mini-value"><?php echo $wheel->total_prizes_won; ?></span>
                                    <span class="stat-mini-label">Trúng</span>
                                </div>
                            </div>
                            
                            <div class="shortcode-display">
                                <code class="shortcode-code">[kata_wheel id="<?php echo $wheel->id; ?>"]</code>
                                <button type="button" class="copy-shortcode-btn" data-shortcode='[kata_wheel id="<?php echo $wheel->id; ?>"]'>
                                    <span class="dashicons dashicons-clipboard"></span>
                                </button>
                            </div>
                        </div>
                        
                        <div class="wheel-card-footer">
                            <div class="wheel-meta">
                                <span class="dashicons dashicons-calendar"></span>
                                <?php echo date('d/m/Y', strtotime($wheel->created_at)); ?>
                            </div>
                            <div class="wheel-actions">
                                <button type="button" class="kata-btn-icon kata-btn-edit edit-wheel-btn" 
                                        data-wheel-id="<?php echo $wheel->id; ?>" 
                                        title="Chỉnh sửa">
                                    <span class="dashicons dashicons-edit"></span>
                                </button>
                                <a href="?page=kata-seo-wheel-analytics&wheel_id=<?php echo $wheel->id; ?>" 
                                   class="kata-btn-icon kata-btn-analytics" 
                                   title="Thống kê">
                                    <span class="dashicons dashicons-chart-line"></span>
                                </a>
                                <form method="post" style="display: inline;">
                                    <?php wp_nonce_field('kata_wheel_action'); ?>
                                    <input type="hidden" name="kata_wheel_action" value="delete">
                                    <input type="hidden" name="wheel_id" value="<?php echo $wheel->id; ?>">
                                    <button type="submit" class="kata-btn-icon kata-btn-delete" 
                                            onclick="return confirm('⚠️ Xóa vòng quay này?\n\nTất cả dữ liệu sẽ bị mất vĩnh viễn!')" 
                                            title="Xóa">
                                        <span class="dashicons dashicons-trash"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- CREATE/EDIT WHEEL MODAL -->
<div id="wheelFormModal" class="kata-modal <?php echo $edit_mode ? 'kata-modal-show' : ''; ?>">
    <div class="kata-modal-overlay"></div>
    <div class="kata-modal-container">
        <div class="kata-modal-header">
            <h2 class="kata-modal-title">
                <span class="dashicons dashicons-edit"></span>
                <span id="modalTitleText"><?php echo $current_wheel ? '✏️ Chỉnh Sửa Vòng Quay' : '➕ Tạo Vòng Quay Mới'; ?></span>
            </h2>
            <button type="button" class="kata-modal-close" id="closeWheelModal">
                <span class="dashicons dashicons-no-alt"></span>
            </button>
        </div>
        <div class="kata-modal-body">
            <form method="post" action="" class="kata-wheel-form" id="wheelForm">
                <?php wp_nonce_field('kata_wheel_action'); ?>
                <input type="hidden" name="kata_wheel_action" value="<?php echo $current_wheel ? 'update' : 'create'; ?>" id="formAction">
                <?php if ($current_wheel): ?>
                    <input type="hidden" name="wheel_id" value="<?php echo $current_wheel->id; ?>" id="wheelId">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group form-group-full">
                        <label for="wheel_title" class="form-label">
                            <span class="dashicons dashicons-admin-post"></span>
                            Tiêu đề Vòng Quay <span class="required">*</span>
                        </label>
                        <input type="text" id="wheel_title" name="wheel_title" 
                               value="<?php echo $current_wheel ? esc_attr($current_wheel->wheel_title) : ''; ?>" 
                               class="form-control" placeholder="VD: Vòng Quay Ưu Đãi Tháng 10" required>
                    </div>
                    
                    <div class="form-group form-group-full">
                        <label for="wheel_description" class="form-label">
                            <span class="dashicons dashicons-text"></span>
                            Mô tả
                        </label>
                        <textarea id="wheel_description" name="wheel_description" rows="2" class="form-control" 
                                  placeholder="Mô tả ngắn về vòng quay..."><?php echo $current_wheel ? esc_textarea($current_wheel->wheel_description) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="requirement" class="form-label">
                            <span class="dashicons dashicons-admin-users"></span>
                            Yêu cầu thông tin
                        </label>
                        <select id="requirement" name="requirement" class="form-control">
                            <option value="none" <?php selected($current_wheel ? $current_wheel->requirement : '', 'none'); ?>>Không yêu cầu</option>
                            <option value="email" <?php selected($current_wheel ? $current_wheel->requirement : 'email', 'email'); ?>>Email</option>
                            <option value="phone" <?php selected($current_wheel ? $current_wheel->requirement : '', 'phone'); ?>>Số điện thoại</option>
                            <option value="both" <?php selected($current_wheel ? $current_wheel->requirement : '', 'both'); ?>>Cả hai</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="max_spins_per_user" class="form-label">
                            <span class="dashicons dashicons-update"></span>
                            Giới hạn/User
                        </label>
                        <input type="number" id="max_spins_per_user" name="max_spins_per_user" 
                               value="<?php echo $current_wheel ? $current_wheel->max_spins_per_user : 1; ?>" 
                               class="form-control" min="1" max="100">
                    </div>
                    
                    <div class="form-group">
                        <label for="max_spins_per_day" class="form-label">
                            <span class="dashicons dashicons-calendar"></span>
                            Giới hạn/Ngày
                        </label>
                        <input type="number" id="max_spins_per_day" name="max_spins_per_day" 
                               value="<?php echo $current_wheel ? $current_wheel->max_spins_per_day : 1; ?>" 
                               class="form-control" min="1" max="100">
                    </div>
                    
                    <?php if ($current_wheel): ?>
                    <div class="form-group">
                        <label for="wheel_status" class="form-label">
                            <span class="dashicons dashicons-admin-settings"></span>
                            Trạng thái
                        </label>
                        <select id="wheel_status" name="wheel_status" class="form-control">
                            <option value="active" <?php selected($current_wheel->status, 'active'); ?>>🟢 Hoạt động</option>
                            <option value="inactive" <?php selected($current_wheel->status, 'inactive'); ?>>🔴 Tạm dừng</option>
                            <option value="scheduled" <?php selected($current_wheel->status, 'scheduled'); ?>>🟡 Đã lên lịch</option>
                            <option value="expired" <?php selected($current_wheel->status, 'expired'); ?>>⚫ Hết hạn</option>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group form-group-full">
                    <label class="form-label">
                        <span class="dashicons dashicons-awards"></span>
                        Giải thưởng <span class="required">*</span>
                    </label>
                    <div id="prizesContainer" class="prizes-container">
                        <?php
                        $prizes = array();
                        if ($current_wheel) {
                            $prizes = $wpdb->get_results($wpdb->prepare(
                                "SELECT * FROM {$wpdb->prefix}kata_wheel_prizes WHERE wheel_id = %d ORDER BY position_order",
                                $current_wheel->id
                            ));
                        }
                        
                        $min_prizes = max(3, count($prizes));
                        for ($i = 0; $i < max($min_prizes, 6); $i++):
                            $prize = isset($prizes[$i]) ? $prizes[$i] : null;
                        ?>
                        <div class="prize-row">
                            <span class="prize-number"><?php echo $i + 1; ?></span>
                            <input type="text" name="prize_texts[]" value="<?php echo $prize ? esc_attr($prize->prize_text) : ''; ?>" 
                                   placeholder="Tên giải" class="form-control prize-text">
                            <input type="text" name="prize_values[]" value="<?php echo $prize ? esc_attr($prize->prize_value) : ''; ?>" 
                                   placeholder="Giá trị" class="form-control prize-value">
                            <select name="prize_types[]" class="form-control prize-type">
                                <option value="discount" <?php selected($prize ? $prize->prize_type : '', 'discount'); ?>>💰 Giảm giá</option>
                                <option value="gift" <?php selected($prize ? $prize->prize_type : '', 'gift'); ?>>🎁 Quà tặng</option>
                                <option value="service" <?php selected($prize ? $prize->prize_type : '', 'service'); ?>>🛠️ Dịch vụ</option>
                                <option value="retry" <?php selected($prize ? $prize->prize_type : '', 'retry'); ?>>🔄 Thử lại</option>
                                <option value="nothing" <?php selected($prize ? $prize->prize_type : '', 'nothing'); ?>>❌ Chúc may mắn</option>
                            </select>
                            <input type="number" name="probabilities[]" value="<?php echo $prize ? $prize->probability : 10; ?>" 
                                   placeholder="%" class="form-control prize-prob" min="0" max="100" step="0.1">
                            <input type="color" name="colors[]" value="<?php echo $prize ? $prize->color : sprintf('#%06X', mt_rand(0, 0xFFFFFF)); ?>" 
                                   class="form-control prize-color">
                            <button type="button" class="kata-btn-icon kata-btn-delete remove-prize" <?php echo $i < 3 ? 'style="display:none;"' : ''; ?>>
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <button type="button" id="addPrizeBtn" class="kata-button kata-button-secondary kata-button-small">
                        <span class="dashicons dashicons-plus-alt"></span>
                        Thêm giải thưởng
                    </button>
                    <p class="field-hint">💡 Tổng xác suất nên = 100%. Tối thiểu 3 giải.</p>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="kata-button kata-button-secondary" id="cancelFormBtn">
                        <span class="dashicons dashicons-no"></span>
                        Hủy
                    </button>
                    <button type="submit" class="kata-button kata-button-primary">
                        <span class="dashicons dashicons-saved"></span>
                        <span id="submitBtnText"><?php echo $current_wheel ? 'Cập nhật Vòng Quay' : 'Tạo Vòng Quay'; ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/wheel-management-styles.php'; ?>
<?php include KATA_SEO_MANAGER_PLUGIN_DIR . 'admin/wheel-management-scripts.php'; ?>
