<?php
/**
 * Admin Analytics Template
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
    
    <div class="kata-analytics-container">
        <!-- Time Period Filter -->
        <div class="kata-analytics-filters">
            <form method="get" class="kata-period-form">
                <input type="hidden" name="page" value="kata-chatbot-analytics">
                
                <select name="period" onchange="this.form.submit()">
                    <option value="7" <?php selected(isset($_GET['period']) ? $_GET['period'] : '30', '7'); ?>><?php _e('7 ngày qua', 'kata-chatbot'); ?></option>
                    <option value="30" <?php selected(isset($_GET['period']) ? $_GET['period'] : '30', '30'); ?>><?php _e('30 ngày qua', 'kata-chatbot'); ?></option>
                    <option value="90" <?php selected(isset($_GET['period']) ? $_GET['period'] : '30', '90'); ?>><?php _e('90 ngày qua', 'kata-chatbot'); ?></option>
                </select>
            </form>
        </div>
        
        <!-- Stats Overview -->
        <div class="kata-analytics-stats">
            <div class="kata-stat-card">
                <h3><?php echo esc_html(isset($stats['total_conversations']) ? number_format_i18n($stats['total_conversations']) : '0'); ?></h3>
                <p><?php _e('Tổng cuộc hội thoại', 'kata-chatbot'); ?></p>
            </div>
            
            <div class="kata-stat-card">
                <h3><?php echo esc_html(isset($stats['total_messages']) ? number_format_i18n($stats['total_messages']) : '0'); ?></h3>
                <p><?php _e('Tổng tin nhắn', 'kata-chatbot'); ?></p>
            </div>
            
            <div class="kata-stat-card">
                <h3><?php echo esc_html(isset($stats['active_users']) ? number_format_i18n($stats['active_users']) : '0'); ?></h3>
                <p><?php _e('Người dùng hoạt động', 'kata-chatbot'); ?></p>
            </div>
            
            <div class="kata-stat-card">
                <h3><?php echo isset($stats['avg_response_time']) && $stats['avg_response_time'] ? esc_html(number_format($stats['avg_response_time'], 2) . 's') : 'N/A'; ?></h3>
                <p><?php _e('Thời gian phản hồi TB', 'kata-chatbot'); ?></p>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="kata-charts-container">
            <div class="kata-chart-section">
                <h2><?php _e('Cuộc hội thoại hàng ngày', 'kata-chatbot'); ?></h2>
                <canvas id="conversationsChart" width="400" height="200"></canvas>
            </div>
            
            <div class="kata-chart-section">
                <h2><?php _e('Tin nhắn hàng ngày', 'kata-chatbot'); ?></h2>
                <canvas id="messagesChart" width="400" height="200"></canvas>
            </div>
        </div>
        
        <!-- Popular Keywords -->
        <?php if (isset($stats['popular_keywords']) && !empty($stats['popular_keywords'])) : ?>
            <div class="kata-keywords-section">
                <h2><?php _e('Từ khóa phổ biến', 'kata-chatbot'); ?></h2>
                <div class="kata-keywords-list">
                    <?php foreach ($stats['popular_keywords'] as $keyword => $count) : ?>
                        <span class="kata-keyword-tag">
                            <?php echo esc_html($keyword); ?>
                            <span class="count"><?php echo esc_html($count); ?></span>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.kata-analytics-container {
    background: #fff;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.kata-analytics-filters {
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.kata-analytics-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kata-stat-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #e9ecef;
}

.kata-stat-card h3 {
    margin: 0 0 10px 0;
    font-size: 28px;
    font-weight: bold;
    color: #0073aa;
}

.kata-stat-card p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.kata-charts-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

.kata-chart-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.kata-chart-section h2 {
    margin: 0 0 15px 0;
    font-size: 18px;
    color: #333;
}

.kata-keywords-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.kata-keywords-section h2 {
    margin: 0 0 15px 0;
    font-size: 18px;
    color: #333;
}

.kata-keywords-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.kata-keyword-tag {
    background: #0073aa;
    color: white;
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.kata-keyword-tag .count {
    background: rgba(255, 255, 255, 0.3);
    padding: 2px 6px;
    border-radius: 10px;
    font-weight: bold;
}

@media (max-width: 768px) {
    .kata-charts-container {
        grid-template-columns: 1fr;
    }
    
    .kata-analytics-stats {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 480px) {
    .kata-analytics-stats {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
    // Chart data
    var conversationsData = <?php echo json_encode(isset($analytics_data['daily_conversations']) ? $analytics_data['daily_conversations'] : []); ?>;
    var messagesData = <?php echo json_encode(isset($analytics_data['daily_messages']) ? $analytics_data['daily_messages'] : []); ?>;
    
    // Prepare chart labels and data
    var labels = [];
    var conversationCounts = [];
    var messageCounts = [];
    
    // Process conversations data
    conversationsData.forEach(function(item) {
        if (labels.indexOf(item.date) === -1) {
            labels.push(item.date);
        }
        conversationCounts.push(parseInt(item.count));
    });
    
    // Process messages data
    messagesData.forEach(function(item) {
        if (labels.indexOf(item.date) === -1) {
            labels.push(item.date);
        }
        messageCounts.push(parseInt(item.count));
    });
    
    // Sort labels and ensure data arrays match
    labels.sort();
    
    // Conversations Chart
    var ctx1 = document.getElementById('conversationsChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '<?php _e('Conversations', 'kata-chatbot'); ?>',
                    data: conversationCounts,
                    borderColor: '#0073aa',
                    backgroundColor: 'rgba(0, 115, 170, 0.1)',
                    tension: 0.4
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
    
    // Messages Chart
    var ctx2 = document.getElementById('messagesChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: '<?php _e('Messages', 'kata-chatbot'); ?>',
                    data: messageCounts,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4
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
