<?php
/**
 * KATA Wheel Analytics Page
 * Dashboard for wheel performance metrics, prize distribution, and user engagement
 *
 * @package KATA_SEO_Manager
 * @subpackage Admin
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get all wheels for dropdown
$wheels = $wpdb->get_results("SELECT id, wheel_title, total_spins, total_prizes_won, status FROM {$wpdb->prefix}kata_wheels ORDER BY created_at DESC");

// Get selected wheel
$selected_wheel_id = isset($_GET['wheel_id']) ? intval($_GET['wheel_id']) : 0;

// If no wheel selected, use first wheel
if ($selected_wheel_id === 0 && !empty($wheels)) {
    $selected_wheel_id = $wheels[0]->id;
}

// Get date range from GET parameters (default: last 30 days)
$date_from = isset($_GET['date_from']) ? sanitize_text_field($_GET['date_from']) : date('Y-m-d', strtotime('-30 days'));
$date_to = isset($_GET['date_to']) ? sanitize_text_field($_GET['date_to']) : date('Y-m-d');

// Get wheel details
$wheel = null;
$analytics_data = array();
$prize_stats = array();
$daily_stats = array();

if ($selected_wheel_id > 0) {
    // Get wheel info
    $wheel = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}kata_wheels WHERE id = %d",
        $selected_wheel_id
    ));
    
    // Get overall analytics for date range
    $analytics_data = $wpdb->get_row($wpdb->prepare(
        "SELECT 
            SUM(total_views) as total_views,
            SUM(total_spins) as total_spins,
            SUM(total_prizes_won) as total_prizes_won,
            SUM(total_emails_collected) as total_emails,
            SUM(total_phones_collected) as total_phones,
            SUM(unique_users) as unique_users,
            AVG(conversion_rate) as avg_conversion_rate,
            AVG(avg_spins_per_user) as avg_spins_per_user
        FROM {$wpdb->prefix}kata_wheel_analytics 
        WHERE wheel_id = %d AND date BETWEEN %s AND %s",
        $selected_wheel_id,
        $date_from,
        $date_to
    ));
    
    // Get prize distribution
    $prize_stats = $wpdb->get_results($wpdb->prepare(
        "SELECT 
            p.prize_text,
            p.prize_type,
            p.probability,
            p.color,
            p.total_won,
            p.total_available,
            COUNT(s.id) as actual_won
        FROM {$wpdb->prefix}kata_wheel_prizes p
        LEFT JOIN {$wpdb->prefix}kata_wheel_spins s ON p.id = s.prize_id AND s.spin_date BETWEEN %s AND %s
        WHERE p.wheel_id = %d AND p.is_active = 1
        GROUP BY p.id
        ORDER BY p.position_order",
        $date_from,
        $date_to,
        $selected_wheel_id
    ));
    
    // Get daily statistics for chart
    $daily_stats = $wpdb->get_results($wpdb->prepare(
        "SELECT 
            date,
            total_views,
            total_spins,
            total_prizes_won,
            conversion_rate,
            total_emails_collected,
            total_phones_collected
        FROM {$wpdb->prefix}kata_wheel_analytics 
        WHERE wheel_id = %d AND date BETWEEN %s AND %s
        ORDER BY date ASC",
        $selected_wheel_id,
        $date_from,
        $date_to
    ));
}

// Handle CSV export
if (isset($_GET['action']) && $_GET['action'] === 'export_csv' && $selected_wheel_id > 0) {
    $spins = $wpdb->get_results($wpdb->prepare(
        "SELECT 
            s.spin_date,
            s.user_email,
            s.user_phone,
            s.user_name,
            s.prize_text,
            s.prize_type,
            s.prize_value,
            s.is_claimed,
            s.user_ip,
            s.device_type,
            s.browser,
            s.utm_source,
            s.utm_medium,
            s.utm_campaign
        FROM {$wpdb->prefix}kata_wheel_spins 
        WHERE wheel_id = %d AND spin_date BETWEEN %s AND %s
        ORDER BY spin_date DESC",
        $selected_wheel_id,
        $date_from,
        $date_to
    ));
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=kata-wheel-spins-' . date('Y-m-d') . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Headers
    fputcsv($output, array('Ngày Quay', 'Email', 'Số Điện Thoại', 'Tên', 'Giải Thưởng', 'Loại', 'Giá Trị', 'Đã Nhận', 'IP', 'Thiết Bị', 'Trình Duyệt', 'UTM Source', 'UTM Medium', 'UTM Campaign'));
    
    // Data
    foreach ($spins as $spin) {
        fputcsv($output, array(
            $spin->spin_date,
            $spin->user_email,
            $spin->user_phone,
            $spin->user_name,
            $spin->prize_text,
            $spin->prize_type,
            $spin->prize_value,
            $spin->is_claimed ? 'Có' : 'Chưa',
            $spin->user_ip,
            $spin->device_type,
            $spin->browser,
            $spin->utm_source,
            $spin->utm_medium,
            $spin->utm_campaign
        ));
    }
    
    fclose($output);
    exit;
}
?>

<div class="wrap kata-wheel-analytics">
    <!-- Header with gradient background -->
    <div class="kata-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        <h1 style="color: white; margin: 0 0 20px 0; font-size: 28px; font-weight: 600;">
            📊 Phân Tích Vòng Quay May Mắn
        </h1>
        
        <!-- Wheel selector and filters -->
        <form method="GET" action="" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <input type="hidden" name="page" value="kata-seo-wheel-analytics">
            
            <div style="flex: 1; min-width: 200px;">
                <label style="color: rgba(255,255,255,0.9); font-size: 13px; display: block; margin-bottom: 5px;">Chọn Vòng Quay:</label>
                <select name="wheel_id" style="width: 100%; padding: 10px; border-radius: 6px; border: none; font-size: 14px;" onchange="this.form.submit()">
                    <option value="0">-- Chọn vòng quay --</option>
                    <?php foreach ($wheels as $w): ?>
                        <option value="<?php echo $w->id; ?>" <?php selected($selected_wheel_id, $w->id); ?>>
                            <?php echo esc_html($w->wheel_title); ?> (<?php echo $w->total_spins; ?> lượt quay)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="min-width: 150px;">
                <label style="color: rgba(255,255,255,0.9); font-size: 13px; display: block; margin-bottom: 5px;">Từ Ngày:</label>
                <input type="date" name="date_from" value="<?php echo esc_attr($date_from); ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: none; font-size: 14px;">
            </div>
            
            <div style="min-width: 150px;">
                <label style="color: rgba(255,255,255,0.9); font-size: 13px; display: block; margin-bottom: 5px;">Đến Ngày:</label>
                <input type="date" name="date_to" value="<?php echo esc_attr($date_to); ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: none; font-size: 14px;">
            </div>
            
            <button type="submit" class="button" style="padding: 10px 20px; height: 42px; background: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer;">
                🔍 Lọc
            </button>
            
            <?php if ($selected_wheel_id > 0): ?>
                <a href="?page=kata-seo-wheel-analytics&action=export_csv&wheel_id=<?php echo $selected_wheel_id; ?>&date_from=<?php echo $date_from; ?>&date_to=<?php echo $date_to; ?>" 
                   class="button" 
                   style="padding: 10px 20px; height: 42px; background: #10b981; color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-block; line-height: 22px;">
                    📥 Xuất CSV
                </a>
            <?php endif; ?>
        </form>
    </div>
    
    <?php if ($wheel): ?>
        <!-- Overview Statistics Cards -->
        <div class="kata-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            
            <!-- Total Views Card -->
            <div class="kata-stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">👁️ Tổng Lượt Xem</div>
                <div style="font-size: 36px; font-weight: 700; margin-bottom: 5px;">
                    <?php echo number_format($analytics_data->total_views ?? 0); ?>
                </div>
                <div style="font-size: 12px; opacity: 0.8;">Từ <?php echo date('d/m/Y', strtotime($date_from)); ?></div>
            </div>
            
            <!-- Total Spins Card -->
            <div class="kata-stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(240, 147, 251, 0.4);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">🎯 Tổng Lượt Quay</div>
                <div style="font-size: 36px; font-weight: 700; margin-bottom: 5px;">
                    <?php echo number_format($analytics_data->total_spins ?? 0); ?>
                </div>
                <div style="font-size: 12px; opacity: 0.8;">
                    Tỷ lệ: <?php echo number_format($analytics_data->avg_conversion_rate ?? 0, 1); ?>%
                </div>
            </div>
            
            <!-- Prizes Won Card -->
            <div class="kata-stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(79, 172, 254, 0.4);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">🎁 Giải Thưởng Đã Trao</div>
                <div style="font-size: 36px; font-weight: 700; margin-bottom: 5px;">
                    <?php echo number_format($analytics_data->total_prizes_won ?? 0); ?>
                </div>
                <div style="font-size: 12px; opacity: 0.8;">
                    <?php 
                    $total_spins = $analytics_data->total_spins ?? 0;
                    $win_rate = $total_spins > 0 ? ($analytics_data->total_prizes_won / $total_spins) * 100 : 0;
                    echo number_format($win_rate, 1); 
                    ?>% thắng
                </div>
            </div>
            
            <!-- Unique Users Card -->
            <div class="kata-stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(250, 112, 154, 0.4);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">👥 Người Chơi Duy Nhất</div>
                <div style="font-size: 36px; font-weight: 700; margin-bottom: 5px;">
                    <?php echo number_format($analytics_data->unique_users ?? 0); ?>
                </div>
                <div style="font-size: 12px; opacity: 0.8;">
                    TB: <?php echo number_format($analytics_data->avg_spins_per_user ?? 0, 1); ?> lượt/người
                </div>
            </div>
            
            <!-- Email Collection Card -->
            <div class="kata-stat-card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(48, 207, 208, 0.4);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">📧 Email Thu Thập</div>
                <div style="font-size: 36px; font-weight: 700; margin-bottom: 5px;">
                    <?php echo number_format($analytics_data->total_emails ?? 0); ?>
                </div>
                <div style="font-size: 12px; opacity: 0.8;">Lead marketing</div>
            </div>
            
            <!-- Phone Collection Card -->
            <div class="kata-stat-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); padding: 25px; border-radius: 12px; color: #333; box-shadow: 0 4px 15px rgba(168, 237, 234, 0.4);">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">📱 SĐT Thu Thập</div>
                <div style="font-size: 36px; font-weight: 700; margin-bottom: 5px;">
                    <?php echo number_format($analytics_data->total_phones ?? 0); ?>
                </div>
                <div style="font-size: 12px; opacity: 0.8;">Lead marketing</div>
            </div>
            
        </div>
        
        <!-- Prize Distribution Chart -->
        <div class="kata-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px 0; font-size: 20px; font-weight: 600; color: #333;">
                🎁 Phân Phối Giải Thưởng
            </h2>
            
            <div style="overflow-x: auto;">
                <table class="wp-list-table widefat fixed striped" style="border-radius: 8px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 15px; font-weight: 600;">Giải Thưởng</th>
                            <th style="padding: 15px; font-weight: 600;">Loại</th>
                            <th style="padding: 15px; font-weight: 600; text-align: center;">Xác Suất</th>
                            <th style="padding: 15px; font-weight: 600; text-align: center;">Đã Trao</th>
                            <th style="padding: 15px; font-weight: 600; text-align: center;">Tổng Trao</th>
                            <th style="padding: 15px; font-weight: 600; text-align: center;">Tỷ Lệ Thực</th>
                            <th style="padding: 15px; font-weight: 600;">Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($prize_stats)): ?>
                            <?php foreach ($prize_stats as $prize): 
                                $actual_rate = ($analytics_data->total_spins ?? 0) > 0 ? ($prize->actual_won / $analytics_data->total_spins) * 100 : 0;
                                $deviation = abs($actual_rate - $prize->probability);
                            ?>
                                <tr>
                                    <td style="padding: 15px;">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span style="width: 30px; height: 30px; border-radius: 6px; background: <?php echo esc_attr($prize->color); ?>; display: inline-block;"></span>
                                            <strong><?php echo esc_html($prize->prize_text); ?></strong>
                                        </div>
                                    </td>
                                    <td style="padding: 15px;">
                                        <?php
                                        $type_labels = array(
                                            'discount' => '💰 Giảm giá',
                                            'gift' => '🎁 Quà tặng',
                                            'service' => '🛠️ Dịch vụ',
                                            'retry' => '🔄 Quay lại',
                                            'nothing' => '😢 Chúc may mắn'
                                        );
                                        echo $type_labels[$prize->prize_type] ?? $prize->prize_type;
                                        ?>
                                    </td>
                                    <td style="padding: 15px; text-align: center;">
                                        <span style="background: #f0f0f0; padding: 5px 12px; border-radius: 20px; font-weight: 500;">
                                            <?php echo number_format($prize->probability, 1); ?>%
                                        </span>
                                    </td>
                                    <td style="padding: 15px; text-align: center; font-weight: 600; color: #667eea;">
                                        <?php echo number_format($prize->actual_won); ?>
                                    </td>
                                    <td style="padding: 15px; text-align: center; font-weight: 600;">
                                        <?php echo number_format($prize->total_won); ?>
                                    </td>
                                    <td style="padding: 15px; text-align: center;">
                                        <span style="background: <?php echo $deviation < 5 ? '#d4edda' : '#fff3cd'; ?>; color: <?php echo $deviation < 5 ? '#155724' : '#856404'; ?>; padding: 5px 12px; border-radius: 20px; font-weight: 500;">
                                            <?php echo number_format($actual_rate, 2); ?>%
                                        </span>
                                    </td>
                                    <td style="padding: 15px;">
                                        <div style="background: #f0f0f0; height: 20px; border-radius: 10px; overflow: hidden; position: relative;">
                                            <div style="background: <?php echo esc_attr($prize->color); ?>; height: 100%; width: <?php echo min($actual_rate * 5, 100); ?>%; border-radius: 10px;"></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="padding: 20px; text-align: center; color: #999;">
                                    Chưa có dữ liệu giải thưởng
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Daily Trends Chart -->
        <?php if (!empty($daily_stats)): ?>
        <div class="kata-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px 0; font-size: 20px; font-weight: 600; color: #333;">
                📈 Xu Hướng Theo Ngày
            </h2>
            
            <canvas id="dailyTrendsChart" width="100%" height="40"></canvas>
            
            <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
            <script>
                const ctx = document.getElementById('dailyTrendsChart').getContext('2d');
                const dailyData = <?php echo json_encode($daily_stats); ?>;
                
                const chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dailyData.map(d => {
                            const date = new Date(d.date);
                            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
                        }),
                        datasets: [
                            {
                                label: 'Lượt Xem',
                                data: dailyData.map(d => d.total_views),
                                borderColor: '#667eea',
                                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Lượt Quay',
                                data: dailyData.map(d => d.total_spins),
                                borderColor: '#f093fb',
                                backgroundColor: 'rgba(240, 147, 251, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Giải Thưởng',
                                data: dailyData.map(d => d.total_prizes_won),
                                borderColor: '#4facfe',
                                backgroundColor: 'rgba(79, 172, 254, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 15,
                                    font: {
                                        size: 13,
                                        weight: '500'
                                    }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                cornerRadius: 8,
                                titleFont: {
                                    size: 14,
                                    weight: '600'
                                },
                                bodyFont: {
                                    size: 13
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
        </div>
        <?php endif; ?>
        
        <!-- Recent Spins Table -->
        <div class="kata-card" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <h2 style="margin: 0 0 20px 0; font-size: 20px; font-weight: 600; color: #333;">
                🕐 Lượt Quay Gần Đây
            </h2>
            
            <?php
            $recent_spins = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}kata_wheel_spins 
                WHERE wheel_id = %d AND spin_date BETWEEN %s AND %s 
                ORDER BY created_at DESC 
                LIMIT 50",
                $selected_wheel_id,
                $date_from,
                $date_to
            ));
            ?>
            
            <div style="overflow-x: auto;">
                <table class="wp-list-table widefat fixed striped" style="border-radius: 8px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; font-weight: 600;">Thời Gian</th>
                            <th style="padding: 12px; font-weight: 600;">Người Chơi</th>
                            <th style="padding: 12px; font-weight: 600;">Giải Thưởng</th>
                            <th style="padding: 12px; font-weight: 600;">Loại</th>
                            <th style="padding: 12px; font-weight: 600;">Thiết Bị</th>
                            <th style="padding: 12px; font-weight: 600;">Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recent_spins)): ?>
                            <?php foreach ($recent_spins as $spin): ?>
                                <tr>
                                    <td style="padding: 12px; font-size: 13px;">
                                        <?php echo date('d/m/Y H:i', strtotime($spin->spin_date)); ?>
                                    </td>
                                    <td style="padding: 12px; font-size: 13px;">
                                        <?php if ($spin->user_name): ?>
                                            <strong><?php echo esc_html($spin->user_name); ?></strong><br>
                                        <?php endif; ?>
                                        <?php if ($spin->user_email): ?>
                                            📧 <?php echo esc_html($spin->user_email); ?><br>
                                        <?php endif; ?>
                                        <?php if ($spin->user_phone): ?>
                                            📱 <?php echo esc_html($spin->user_phone); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px; font-weight: 500;">
                                        <?php echo esc_html($spin->prize_text); ?>
                                        <?php if ($spin->prize_value): ?>
                                            <br><small style="color: #10b981;"><?php echo esc_html($spin->prize_value); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px; font-size: 13px;">
                                        <?php
                                        $type_icons = array(
                                            'discount' => '💰',
                                            'gift' => '🎁',
                                            'service' => '🛠️',
                                            'retry' => '🔄',
                                            'nothing' => '😢'
                                        );
                                        echo $type_icons[$spin->prize_type] ?? '❓';
                                        ?>
                                    </td>
                                    <td style="padding: 12px; font-size: 13px;">
                                        <?php echo esc_html($spin->device_type ?? 'Unknown'); ?><br>
                                        <small style="color: #999;"><?php echo esc_html($spin->browser ?? ''); ?></small>
                                    </td>
                                    <td style="padding: 12px;">
                                        <?php if ($spin->is_claimed): ?>
                                            <span style="background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                                ✅ Đã nhận
                                            </span>
                                        <?php else: ?>
                                            <span style="background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: 500;">
                                                ⏳ Chờ nhận
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="padding: 20px; text-align: center; color: #999;">
                                    Chưa có lượt quay nào trong khoảng thời gian này
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    <?php else: ?>
        <!-- No wheel selected -->
        <div style="background: white; padding: 60px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 64px; margin-bottom: 20px;">🎡</div>
            <h2 style="margin: 0 0 10px 0; color: #333;">Chưa Có Vòng Quay</h2>
            <p style="color: #666; margin: 0 0 20px 0;">Hãy tạo vòng quay đầu tiên để xem phân tích</p>
            <a href="?page=kata-seo-wheel-management" class="button button-primary" style="padding: 12px 24px; font-size: 14px;">
                ➕ Tạo Vòng Quay Mới
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
.kata-wheel-analytics .kata-stat-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.kata-wheel-analytics .kata-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.kata-wheel-analytics .kata-card {
    transition: box-shadow 0.3s ease;
}

.kata-wheel-analytics .kata-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
}

.kata-wheel-analytics table tr {
    transition: background-color 0.2s ease;
}

.kata-wheel-analytics table tbody tr:hover {
    background-color: #f8f9fa !important;
}
</style>
