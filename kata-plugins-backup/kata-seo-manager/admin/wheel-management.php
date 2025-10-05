<?php
/**
 * Wheel Management Admin Page - Professional UI
 */

if (!defined('ABSPATH')) exit;

global $wpdb;
$wheels = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}kata_wheels ORDER BY created_at DESC");
$current_wheel = null;

if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $current_wheel = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}kata_wheels WHERE id = %d",
        $edit_id
    ));
}
?>

<div class="wrap kata-wheel-management-wrap">
    <div class="kata-admin-header">
        <div class="kata-header-content">
            <h1 class="kata-page-title">
                <span class="dashicons dashicons-update"></span>
                Quản lý Vòng Quay May Mắn
            </h1>
            <p class="kata-page-subtitle">Tạo và quản lý vòng quay tương tác với người dùng</p>
        </div>
        <div class="kata-header-stats">
            <div class="stat-card">
                <div class="stat-icon">🎡</div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count($wheels); ?></div>
                    <div class="stat-label">Tổng Wheels</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo count(array_filter($wheels, function($w) { return $w->status === 'active'; })); ?></div>
                    <div class="stat-label">Đang hoạt động</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🎁</div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo array_sum(array_column($wheels, 'total_prizes_won')); ?></div>
                    <div class="stat-label">Giải đã trao</div>
                </div>
            </div>
        </div>
    </div>

    <div class="kata-wheel-admin-container">
        <!-- Form Section -->
        <div class="kata-wheel-form-card">
            <div class="card-header">
                <h2 class="card-title">
                    <span class="dashicons dashicons-edit"></span>
                    <?php echo $current_wheel ? '✏️ Chỉnh Sửa Vòng Quay' : '➕ Tạo Vòng Quay Mới'; ?>
                </h2>
            </div>
            <div class="card-body">
                <form method="post" action="" class="kata-wheel-form">
                    <?php wp_nonce_field('kata_wheel_action'); ?>
                    <input type="hidden" name="kata_wheel_action" value="<?php echo $current_wheel ? 'update' : 'create'; ?>">
                    <?php if ($current_wheel): ?>
                        <input type="hidden" name="wheel_id" value="<?php echo $current_wheel->id; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="wheel_title" class="form-label">
                            <span class="dashicons dashicons-admin-post"></span>
                            Tiêu đề Vòng Quay
                        </label>
                        <input type="text" id="wheel_title" name="wheel_title" 
                               value="<?php echo $current_wheel ? esc_attr($current_wheel->wheel_title) : ''; ?>" 
                               class="form-control" placeholder="VD: Vòng Quay Ưu Đãi Tháng 10" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="wheel_description" class="form-label">
                            <span class="dashicons dashicons-text"></span>
                            Mô tả
                        </label>
                        <textarea id="wheel_description" name="wheel_description" rows="2" class="form-control" 
                                  placeholder="Mô tả ngắn về vòng quay..."><?php echo $current_wheel ? esc_textarea($current_wheel->wheel_description) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-row">
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
                    
                    <div class="form-group">
                        <label class="form-label">
                            <span class="dashicons dashicons-awards"></span>
                            Giải thưởng
                        </label>
                        <div id="prizes-container" class="prizes-container">
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
                                       placeholder="Tên giải (VD: Giảm 50%)" class="form-control prize-text">
                                <input type="text" name="prize_values[]" value="<?php echo $prize ? esc_attr($prize->prize_value) : ''; ?>" 
                                       placeholder="Giá trị (VD: 50)" class="form-control prize-value">
                                <select name="prize_types[]" class="form-control prize-type">
                                    <option value="discount" <?php selected($prize ? $prize->prize_type : '', 'discount'); ?>>💰 Giảm giá</option>
                                    <option value="gift" <?php selected($prize ? $prize->prize_type : '', 'gift'); ?>>🎁 Quà tặng</option>
                                    <option value="service" <?php selected($prize ? $prize->prize_type : '', 'service'); ?>>🛠️ Dịch vụ</option>
                                    <option value="retry" <?php selected($prize ? $prize->prize_type : '', 'retry'); ?>>🔄 Thử lại</option>
                                    <option value="nothing" <?php selected($prize ? $prize->prize_type : '', 'nothing'); ?>>❌ Chúc may mắn</option>
                                </select>
                                <input type="number" name="probabilities[]" value="<?php echo $prize ? $prize->probability : 10; ?>" 
                                       placeholder="%" class="form-control prize-prob" min="0" max="100" step="0.1">
                                <input type="color" name="colors[]" value="<?php echo $prize ? $prize->color : '#ff6b6b'; ?>" 
                                       class="form-control prize-color">
                                <button type="button" class="kata-button kata-button-icon remove-prize" <?php echo $i < 3 ? 'style="display:none;"' : ''; ?>>
                                    <span class="dashicons dashicons-trash"></span>
                                </button>
                            </div>
                            <?php endfor; ?>
                        </div>
                        <button type="button" id="add-prize" class="kata-button kata-button-secondary kata-button-small">
                            <span class="dashicons dashicons-plus-alt"></span>
                            Thêm giải thưởng
                        </button>
                        <p class="field-description">Tổng xác suất nên = 100%. Tối thiểu 3 giải.</p>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="kata-button kata-button-primary">
                            <span class="dashicons dashicons-saved"></span>
                            <?php echo $current_wheel ? 'Cập nhật Vòng Quay' : 'Tạo Vòng Quay'; ?>
                        </button>
                        <?php if ($current_wheel): ?>
                            <a href="?page=kata-seo-wheel-management" class="kata-button kata-button-secondary">
                                <span class="dashicons dashicons-no"></span>
                                Hủy
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- List Section -->
        <div class="kata-wheel-list-card">
            <div class="card-header">
                <h2 class="card-title">
                    <span class="dashicons dashicons-list-view"></span>
                    📋 Danh sách Vòng Quay
                </h2>
            </div>
            <div class="card-body">
            <?php if (empty($wheels)): ?>
                <div class="empty-state">
                    <span class="dashicons dashicons-info-outline"></span>
                    <p>Chưa có vòng quay nào. Hãy tạo vòng quay đầu tiên!</p>
                </div>
            <?php else: ?>
                <div class="wheels-table-wrapper">
                    <table class="kata-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Thông tin</th>
                                <th>Giải thưởng</th>
                                <th>Lượt quay</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wheels as $wheel): 
                                $prize_count = $wpdb->get_var($wpdb->prepare(
                                    "SELECT COUNT(*) FROM {$wpdb->prefix}kata_wheel_prizes WHERE wheel_id = %d",
                                    $wheel->id
                                ));
                            ?>
                            <tr>
                                <td><div class="wheel-id">#<?php echo $wheel->id; ?></div></td>
                                <td>
                                    <div class="wheel-info">
                                        <div class="wheel-title"><?php echo esc_html($wheel->wheel_title); ?></div>
                                        <div class="wheel-desc"><?php echo esc_html(wp_trim_words($wheel->wheel_description, 10)); ?></div>
                                        <div class="shortcode-display">
                                            <code>[kata_wheel id="<?php echo $wheel->id; ?>"]</code>
                                            <button type="button" class="copy-shortcode" data-shortcode='[kata_wheel id="<?php echo $wheel->id; ?>"]'>
                                                <span class="dashicons dashicons-clipboard"></span>
                                            </button>
                                        </div>
                                        <div class="wheel-meta">
                                            <span class="dashicons dashicons-calendar"></span>
                                            <?php echo date('d/m/Y H:i', strtotime($wheel->created_at)); ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="prize-badge"><?php echo $prize_count; ?> giải</div>
                                </td>
                                <td>
                                    <div class="spins-info">
                                        <div class="spins-count"><?php echo $wheel->total_spins; ?></div>
                                        <div class="prizes-won">🎁 <?php echo $wheel->total_prizes_won; ?> trúng</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="wheel-status-badge wheel-status-<?php echo $wheel->status; ?>">
                                        <?php
                                        switch ($wheel->status) {
                                            case 'active': echo '<span class="dashicons dashicons-yes-alt"></span> Hoạt động'; break;
                                            case 'inactive': echo '<span class="dashicons dashicons-dismiss"></span> Tạm dừng'; break;
                                            case 'scheduled': echo '<span class="dashicons dashicons-clock"></span> Đã lên lịch'; break;
                                            case 'expired': echo '<span class="dashicons dashicons-warning"></span> Hết hạn'; break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?page=kata-seo-wheel-management&edit=<?php echo $wheel->id; ?>" 
                                           class="kata-button kata-button-small kata-button-edit">
                                            <span class="dashicons dashicons-edit"></span>
                                        </a>
                                        
                                        <a href="?page=kata-seo-wheel-analytics&wheel_id=<?php echo $wheel->id; ?>" 
                                           class="kata-button kata-button-small kata-button-analytics">
                                            <span class="dashicons dashicons-chart-line"></span>
                                        </a>
                                        
                                        <form method="post" style="display: inline;">
                                            <?php wp_nonce_field('kata_wheel_action'); ?>
                                            <input type="hidden" name="kata_wheel_action" value="delete">
                                            <input type="hidden" name="wheel_id" value="<?php echo $wheel->id; ?>">
                                            <button type="submit" class="kata-button kata-button-small kata-button-delete" 
                                                    onclick="return confirm('Xóa vòng quay này? Tất cả dữ liệu sẽ mất!')">
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
/* Reuse poll-management.php styles with wheel-specific adjustments */
.kata-wheel-management-wrap { background: #f5f7fa; margin: -20px -20px 0 -22px; padding: 0; }
.kata-admin-header { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 30px 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
.kata-header-content { margin-bottom: 25px; }
.kata-page-title { color: white; font-size: 28px; font-weight: 700; margin: 0 0 8px 0; display: flex; align-items: center; gap: 12px; }
.kata-page-subtitle { color: rgba(255,255,255,0.9); font-size: 15px; margin: 0; }
.kata-header-stats { display: flex; gap: 20px; flex-wrap: wrap; }
.stat-card { background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); border-radius: 12px; padding: 20px 25px; display: flex; align-items: center; gap: 15px; min-width: 180px; border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s ease; }
.stat-card:hover { background: rgba(255,255,255,0.25); transform: translateY(-2px); }
.stat-icon { font-size: 36px; }
.stat-value { font-size: 28px; font-weight: 700; color: white; }
.stat-label { font-size: 12px; color: rgba(255,255,255,0.85); text-transform: uppercase; }
.kata-wheel-admin-container { display: grid; grid-template-columns: 1fr 2fr; gap: 25px; padding: 30px 40px; }
.kata-wheel-form-card, .kata-wheel-list-card { background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
.card-header { background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%); padding: 20px 25px; border-bottom: 2px solid #e1e8ed; }
.card-title { font-size: 18px; font-weight: 600; color: #2c3e50; margin: 0; display: flex; align-items: center; gap: 10px; }
.card-body { padding: 25px; }
.form-group { margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
.form-label { display: flex; align-items: center; gap: 8px; font-weight: 600; color: #2c3e50; margin-bottom: 8px; font-size: 14px; }
.form-control { width: 100%; padding: 10px 14px; border: 2px solid #e1e8ed; border-radius: 8px; font-size: 14px; transition: all 0.3s ease; background: #f8f9fa; }
.form-control:focus { outline: none; border-color: #f093fb; background: white; box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1); }
.field-description { font-size: 12px; color: #6c757d; margin: 6px 0 0 0; font-style: italic; }
.prizes-container { margin-bottom: 12px; }
.prize-row { display: grid; grid-template-columns: 40px 2fr 1fr 1.5fr 100px 60px 40px; gap: 10px; margin-bottom: 12px; align-items: center; }
.prize-number { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; }
.prize-color { width: 60px; height: 40px; border-radius: 6px; cursor: pointer; }
.kata-button { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
.kata-button:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.kata-button-primary { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
.kata-button-secondary { background: #6c757d; color: white; }
.kata-button-small { padding: 6px 12px; font-size: 12px; }
.kata-button-icon { padding: 6px 8px; }
.kata-button-edit { background: #17a2b8; color: white; }
.kata-button-analytics { background: #28a745; color: white; }
.kata-button-delete { background: #dc3545; color: white; }
.form-actions { display: flex; gap: 12px; margin-top: 30px; padding-top: 20px; border-top: 2px solid #f1f3f5; }
.kata-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.kata-table thead { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }
.kata-table th { padding: 14px 12px; text-align: left; font-weight: 600; color: #495057; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #dee2e6; }
.kata-table td { padding: 16px 12px; border-bottom: 1px solid #f1f3f5; font-size: 14px; }
.kata-table tbody tr:hover { background: #f8f9fa; }
.wheel-id { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 6px 12px; border-radius: 20px; font-weight: 700; font-size: 13px; display: inline-block; }
.wheel-title { font-weight: 600; color: #2c3e50; margin-bottom: 4px; }
.wheel-desc { color: #6c757d; font-size: 13px; margin-bottom: 8px; }
.shortcode-display { margin: 8px 0; display: flex; align-items: center; gap: 6px; }
.shortcode-display code { background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 4px 8px; border-radius: 6px; font-size: 12px; border: 1px solid #dee2e6; }
.copy-shortcode { background: none; border: none; cursor: pointer; color: #f093fb; padding: 2px; }
.copy-shortcode:hover { color: #f5576c; transform: scale(1.1); }
.wheel-meta { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #adb5bd; margin-top: 6px; }
.prize-badge { background: #fff3cd; color: #856404; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
.spins-info { text-align: center; }
.spins-count { font-size: 20px; font-weight: 700; color: #f093fb; }
.prizes-won { font-size: 11px; color: #6c757d; margin-top: 4px; }
.wheel-status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.wheel-status-active { background: #d4edda; color: #155724; }
.wheel-status-inactive { background: #f8d7da; color: #721c24; }
.wheel-status-scheduled { background: #fff3cd; color: #856404; }
.wheel-status-expired { background: #e2e3e5; color: #383d41; }
.action-buttons { display: flex; gap: 6px; }
.empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
.empty-state .dashicons { font-size: 64px; width: 64px; height: 64px; opacity: 0.3; }
@media (max-width: 1200px) { .kata-wheel-admin-container { grid-template-columns: 1fr; } }
</style>

<script>
jQuery(document).ready(function($) {
    // Add prize
    $('#add-prize').click(function() {
        var container = $('#prizes-container');
        var count = container.find('.prize-row').length;
        var row = $('<div class="prize-row">' +
            '<span class="prize-number">' + (count + 1) + '</span>' +
            '<input type="text" name="prize_texts[]" placeholder="Tên giải" class="form-control prize-text">' +
            '<input type="text" name="prize_values[]" placeholder="Giá trị" class="form-control prize-value">' +
            '<select name="prize_types[]" class="form-control prize-type">' +
            '<option value="discount">💰 Giảm giá</option><option value="gift">🎁 Quà tặng</option><option value="service">🛠️ Dịch vụ</option><option value="retry">🔄 Thử lại</option><option value="nothing">❌ Chúc may mắn</option>' +
            '</select>' +
            '<input type="number" name="probabilities[]" value="10" placeholder="%" class="form-control prize-prob" min="0" max="100" step="0.1">' +
            '<input type="color" name="colors[]" value="#ff6b6b" class="form-control prize-color">' +
            '<button type="button" class="kata-button kata-button-icon kata-button-delete remove-prize"><span class="dashicons dashicons-trash"></span></button>' +
            '</div>');
        container.append(row);
        updatePrizeNumbers();
    });
    
    // Remove prize
    $(document).on('click', '.remove-prize', function() {
        if ($('.prize-row').length > 3) {
            $(this).closest('.prize-row').remove();
            updatePrizeNumbers();
        } else {
            alert('Cần ít nhất 3 giải thưởng!');
        }
    });
    
    function updatePrizeNumbers() {
        $('.prize-row').each(function(i) {
            $(this).find('.prize-number').text(i + 1);
        });
    }
    
    // Copy shortcode
    $('.copy-shortcode').click(function(e) {
        e.preventDefault();
        var code = $(this).data('shortcode');
        navigator.clipboard.writeText(code).then(function() {
            alert('✅ Đã copy: ' + code);
        });
    });
});
</script>
