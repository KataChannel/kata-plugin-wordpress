<?php
if (!defined('ABSPATH')) {
    exit;
}

// Get statistics directly from database
global $wpdb;
$table_name = $wpdb->prefix . 'kata_form_submissions';
$form_id = isset($atts['form_id']) ? intval($atts['form_id']) : 0;
$type = isset($atts['type']) ? sanitize_text_field($atts['type']) : 'summary';

// Build query conditions
$where_condition = '';
if ($form_id > 0) {
    $where_condition = $wpdb->prepare(' WHERE form_id = %d', $form_id);
}

// Get basic stats
$total_submissions = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}{$where_condition}");
$today_submissions = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}{$where_condition}" . ($where_condition ? ' AND' : ' WHERE') . " DATE(created_at) = CURDATE()");
$month_submissions = $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}{$where_condition}" . ($where_condition ? ' AND' : ' WHERE') . " MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");

$stats = array(
    'total_submissions' => $total_submissions ?: 0,
    'today_submissions' => $today_submissions ?: 0,
    'month_submissions' => $month_submissions ?: 0
);

if ($type === 'summary') {
    ?>
    <div class="kata-form-stats-widget">
        <h3><?php _e('Thống Kê Form', 'kata-form'); ?></h3>
        <div class="kata-stats-summary">
            <div class="kata-stat-item">
                <span class="kata-stat-number"><?php echo number_format($stats['total_submissions']); ?></span>
                <span class="kata-stat-label"><?php _e('Tổng Dữ Liệu', 'kata-form'); ?></span>
            </div>
            <div class="kata-stat-item">
                <span class="kata-stat-number"><?php echo number_format($stats['today_submissions']); ?></span>
                <span class="kata-stat-label"><?php _e('Hôm Nay', 'kata-form'); ?></span>
            </div>
            <div class="kata-stat-item">
                <span class="kata-stat-number"><?php echo number_format($stats['month_submissions']); ?></span>
                <span class="kata-stat-label"><?php _e('Tháng Này', 'kata-form'); ?></span>
            </div>
        </div>
    </div>
    
    <style>
    .kata-form-stats-widget {
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .kata-stats-summary {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .kata-stat-item {
        text-align: center;
        flex: 1;
        min-width: 100px;
    }
    
    .kata-stat-number {
        display: block;
        font-size: 24px;
        font-weight: bold;
        color: #0073aa;
    }
    
    .kata-stat-label {
        display: block;
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
    </style>
    <?php
} elseif ($type === 'chart' && $form_id > 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'kata_form_submissions';
    
    $daily_stats = $wpdb->get_results($wpdb->prepare(
        "SELECT DATE(created_at) as date, COUNT(*) as count 
         FROM {$table_name} 
         WHERE form_id = %d AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
         GROUP BY DATE(created_at) 
         ORDER BY date ASC",
        $form_id
    ));
    
    $chart_id = 'kata-chart-' . uniqid();
    ?>
    <div class="kata-form-chart-widget">
        <canvas id="<?php echo esc_attr($chart_id); ?>" width="400" height="200"></canvas>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart !== 'undefined') {
            const ctx = document.getElementById('<?php echo esc_js($chart_id); ?>').getContext('2d');
            const chartData = <?php echo json_encode($daily_stats); ?>;
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => item.date),
                    datasets: [{
                        label: '<?php _e('Submissions', 'kata-form'); ?>',
                        data: chartData.map(item => parseInt(item.count)),
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
        }
    });
    </script>
    <?php
}
