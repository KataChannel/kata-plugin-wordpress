<?php
if (!defined('ABSPATH')) {
    exit;
}

// Get statistics directly from database
global $wpdb;
$table_name = $wpdb->prefix . 'kata_form_submissions';

// Get basic stats
$total_submissions = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
$today_submissions = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE DATE(created_at) = CURDATE()");
$month_submissions = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
$popular_form = $wpdb->get_var("SELECT form_title FROM {$table_name} GROUP BY form_id ORDER BY COUNT(*) DESC LIMIT 1") ?: __('Chưa có', 'kata-form');

$stats = array(
    'total_submissions' => $total_submissions ?: 0,
    'today_submissions' => $today_submissions ?: 0,
    'month_submissions' => $month_submissions ?: 0,
    'popular_form' => $popular_form
);
?>

<div class="wrap">
    <h1><?php _e('Thống Kê Form', 'kata-form'); ?></h1>
    
    <!-- Statistics Cards -->
    <div class="kata-stats-grid">
        <div class="kata-stat-card">
            <div class="kata-stat-icon">
                <span class="dashicons dashicons-forms"></span>
            </div>
            <div class="kata-stat-content">
                <h3><?php echo number_format($stats['total_submissions']); ?></h3>
                <p><?php _e('Tổng Dữ Liệu', 'kata-form'); ?></p>
            </div>
        </div>
        
        <div class="kata-stat-card">
            <div class="kata-stat-icon">
                <span class="dashicons dashicons-calendar-alt"></span>
            </div>
            <div class="kata-stat-content">
                <h3><?php echo number_format($stats['today_submissions']); ?></h3>
                <p><?php _e('Hôm Nay', 'kata-form'); ?></p>
            </div>
        </div>
        
        <div class="kata-stat-card">
            <div class="kata-stat-icon">
                <span class="dashicons dashicons-chart-line"></span>
            </div>
            <div class="kata-stat-content">
                <h3><?php echo number_format($stats['month_submissions']); ?></h3>
                <p><?php _e('Tháng Này', 'kata-form'); ?></p>
            </div>
        </div>
        
        <div class="kata-stat-card">
            <div class="kata-stat-icon">
                <span class="dashicons dashicons-star-filled"></span>
            </div>
            <div class="kata-stat-content">
                <h3><?php echo esc_html($stats['popular_form']); ?></h3>
                <p><?php _e('Form Phổ Biến Nhất', 'kata-form'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Charts -->
    <div class="kata-charts-container">
        <!-- Daily Submissions Chart -->
        <div class="kata-chart-wrapper">
            <h2><?php _e('Dữ Liệu Theo Ngày (30 Ngày Gần Đây)', 'kata-form'); ?></h2>
            <canvas id="kata-daily-chart" width="400" height="200"></canvas>
        </div>
        
        <!-- Form Breakdown Chart -->
        <div class="kata-chart-wrapper">
            <h2><?php _e('Dữ Liệu Theo Form', 'kata-form'); ?></h2>
            <canvas id="kata-form-chart" width="400" height="200"></canvas>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="kata-recent-activity">
        <h2><?php _e('Hoạt Động Gần Đây', 'kata-form'); ?></h2>
        <div class="kata-activity-list">
            <?php
            global $wpdb;
            $table_name = $wpdb->prefix . 'kata_form_submissions';
            $recent = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY created_at DESC LIMIT 10");
            ?>
            
            <?php if ($recent): ?>
                <?php foreach ($recent as $activity): ?>
                    <div class="kata-activity-item">
                        <div class="kata-activity-icon">
                            <span class="dashicons dashicons-forms"></span>
                        </div>
                        <div class="kata-activity-content">
                            <strong><?php echo esc_html($activity->form_title); ?></strong>
                            <span class="kata-activity-time">
                                <?php echo human_time_diff(strtotime($activity->created_at), current_time('timestamp')) . ' trước'; ?>
                            </span>
                        </div>
                        <div class="kata-activity-status">
                            <span class="status-badge status-<?php echo esc_attr($activity->status); ?>">
                                <?php 
                                $status_text = array(
                                    'new' => 'Mới',
                                    'read' => 'Đã đọc', 
                                    'archived' => 'Lưu trữ'
                                );
                                echo esc_html($status_text[$activity->status] ?? ucfirst($activity->status)); 
                                ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p><?php _e('Không có hoạt động gần đây.', 'kata-form'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get chart data from database
    const chartData = <?php
    // Get daily data for last 30 days
    $daily_data = array();
    for ($i = 29; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_name} WHERE DATE(created_at) = %s", $date));
        $daily_data[] = array('date' => date('M j', strtotime($date)), 'count' => intval($count));
    }
    echo json_encode($daily_data);
    ?>;
    
    // Daily Submissions Chart
    const dailyCtx = document.getElementById('kata-daily-chart').getContext('2d');
    
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.date),
            datasets: [{
                label: '<?php _e('Dữ Liệu Gửi', 'kata-form'); ?>',
                data: chartData.map(item => item.count),
                borderColor: '#0073aa',
                backgroundColor: 'rgba(0, 115, 170, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    // Form Breakdown Chart
    const formCtx = document.getElementById('kata-form-chart').getContext('2d');
    const formData = <?php echo json_encode($stats['form_breakdown']); ?>;
    
    new Chart(formCtx, {
        type: 'doughnut',
        data: {
            labels: formData.map(item => item.form),
            datasets: [{
                data: formData.map(item => item.count),
                backgroundColor: [
                    '#0073aa',
                    '#00a32a',
                    '#dba617',
                    '#d63638',
                    '#8c8f94',
                    '#2271b1',
                    '#135e96'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
