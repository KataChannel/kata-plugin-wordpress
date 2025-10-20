<?php
/**
 * Admin Analytics Template
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get analytics data
$analytics = KataShareSocial_Analytics::get_instance();
$stats = $analytics->get_share_statistics();
$top_posts = $analytics->get_top_shared_posts();
$platform_stats = $analytics->get_platform_statistics();
?>

<div class="wrap">
    <h1><?php _e('Analytics & Reports', 'kata-sharesocial'); ?></h1>
    
    <div class="kata-sharesocial-analytics">
        <!-- Overview Cards -->
        <div class="analytics-overview">
            <div class="overview-cards">
                <div class="overview-card">
                    <div class="card-icon">
                        <span class="dashicons dashicons-share"></span>
                    </div>
                    <div class="card-content">
                        <h3><?php echo number_format($stats['total_shares'] ?? 0); ?></h3>
                        <p><?php _e('Total Shares', 'kata-sharesocial'); ?></p>
                    </div>
                </div>
                
                <div class="overview-card">
                    <div class="card-icon">
                        <span class="dashicons dashicons-admin-post"></span>
                    </div>
                    <div class="card-content">
                        <h3><?php echo number_format($stats['shared_posts'] ?? 0); ?></h3>
                        <p><?php _e('Shared Posts', 'kata-sharesocial'); ?></p>
                    </div>
                </div>
                
                <div class="overview-card">
                    <div class="card-icon">
                        <span class="dashicons dashicons-networking"></span>
                    </div>
                    <div class="card-content">
                        <h3><?php echo number_format($stats['active_platforms'] ?? 0); ?></h3>
                        <p><?php _e('Active Platforms', 'kata-sharesocial'); ?></p>
                    </div>
                </div>
                
                <div class="overview-card">
                    <div class="card-icon">
                        <span class="dashicons dashicons-chart-line"></span>
                    </div>
                    <div class="card-content">
                        <h3><?php echo number_format($stats['avg_shares_per_post'] ?? 0, 1); ?></h3>
                        <p><?php _e('Avg Shares/Post', 'kata-sharesocial'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="analytics-charts">
            <div class="chart-row">
                <div class="chart-container half-width">
                    <h3><?php _e('Platform Performance', 'kata-sharesocial'); ?></h3>
                    <div class="platform-chart">
                        <canvas id="platformChart" width="400" height="200"></canvas>
                    </div>
                </div>
                
                <div class="chart-container half-width">
                    <h3><?php _e('Shares Over Time', 'kata-sharesocial'); ?></h3>
                    <div class="time-chart">
                        <canvas id="timeChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Platform Statistics -->
        <div class="platform-statistics">
            <h3><?php _e('Platform Statistics', 'kata-sharesocial'); ?></h3>
            <div class="stats-table-wrapper">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Platform', 'kata-sharesocial'); ?></th>
                            <th><?php _e('Total Shares', 'kata-sharesocial'); ?></th>
                            <th><?php _e('Percentage', 'kata-sharesocial'); ?></th>
                            <th><?php _e('Avg. per Post', 'kata-sharesocial'); ?></th>
                            <th><?php _e('Status', 'kata-sharesocial'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($platform_stats)): ?>
                            <?php foreach ($platform_stats as $platform => $data): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html(ucfirst($platform)); ?></strong>
                                        <div class="platform-icon">
                                            <i class="kata-icon kata-icon-<?php echo esc_attr($platform); ?>"></i>
                                        </div>
                                    </td>
                                    <td><?php echo number_format($data['shares']); ?></td>
                                    <td>
                                        <div class="percentage-bar">
                                            <div class="percentage-fill" style="width: <?php echo esc_attr($data['percentage']); ?>%;"></div>
                                            <span><?php echo number_format($data['percentage'], 1); ?>%</span>
                                        </div>
                                    </td>
                                    <td><?php echo number_format($data['avg_per_post'], 1); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $data['active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $data['active'] ? __('Active', 'kata-sharesocial') : __('Inactive', 'kata-sharesocial'); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="no-data">
                                    <?php _e('No sharing data available yet.', 'kata-sharesocial'); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Top Shared Posts -->
        <div class="top-posts">
            <h3><?php _e('Top Shared Posts', 'kata-sharesocial'); ?></h3>
            <div class="top-posts-list">
                <?php if (!empty($top_posts)): ?>
                    <?php foreach ($top_posts as $post_data): ?>
                        <div class="top-post-item">
                            <div class="post-info">
                                <h4>
                                    <a href="<?php echo get_edit_post_link($post_data['post_id']); ?>">
                                        <?php echo esc_html(get_the_title($post_data['post_id'])); ?>
                                    </a>
                                </h4>
                                <p class="post-meta">
                                    <?php echo get_the_date('', $post_data['post_id']); ?> | 
                                    <?php echo get_post_type_object(get_post_type($post_data['post_id']))->labels->singular_name; ?>
                                </p>
                            </div>
                            <div class="post-shares">
                                <div class="share-count">
                                    <strong><?php echo number_format($post_data['total_shares']); ?></strong>
                                    <span><?php _e('shares', 'kata-sharesocial'); ?></span>
                                </div>
                                <div class="platform-breakdown">
                                    <?php foreach ($post_data['platforms'] as $platform => $count): ?>
                                        <span class="platform-share" title="<?php echo esc_attr(ucfirst($platform) . ': ' . $count); ?>">
                                            <i class="kata-icon kata-icon-<?php echo esc_attr($platform); ?>"></i>
                                            <?php echo $count; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data">
                        <p><?php _e('No posts have been shared yet.', 'kata-sharesocial'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Export Section -->
        <div class="analytics-export">
            <h3><?php _e('Export Data', 'kata-sharesocial'); ?></h3>
            <p><?php _e('Export your sharing analytics data for further analysis.', 'kata-sharesocial'); ?></p>
            
            <div class="export-options">
                <div class="export-option">
                    <label>
                        <input type="checkbox" id="export-overview" checked>
                        <?php _e('Overview Statistics', 'kata-sharesocial'); ?>
                    </label>
                </div>
                <div class="export-option">
                    <label>
                        <input type="checkbox" id="export-platforms" checked>
                        <?php _e('Platform Statistics', 'kata-sharesocial'); ?>
                    </label>
                </div>
                <div class="export-option">
                    <label>
                        <input type="checkbox" id="export-posts" checked>
                        <?php _e('Post-level Data', 'kata-sharesocial'); ?>
                    </label>
                </div>
                <div class="export-option">
                    <label>
                        <input type="checkbox" id="export-timeline">
                        <?php _e('Timeline Data', 'kata-sharesocial'); ?>
                    </label>
                </div>
            </div>
            
            <div class="export-actions">
                <button type="button" class="button button-primary" id="export-csv">
                    <span class="dashicons dashicons-download"></span>
                    <?php _e('Export as CSV', 'kata-sharesocial'); ?>
                </button>
                <button type="button" class="button" id="export-json">
                    <span class="dashicons dashicons-media-code"></span>
                    <?php _e('Export as JSON', 'kata-sharesocial'); ?>
                </button>
            </div>
        </div>
        
        <!-- Data Management -->
        <div class="data-management">
            <h3><?php _e('Data Management', 'kata-sharesocial'); ?></h3>
            <p><?php _e('Manage your sharing analytics data.', 'kata-sharesocial'); ?></p>
            
            <div class="management-actions">
                <button type="button" class="button" id="refresh-stats">
                    <span class="dashicons dashicons-update"></span>
                    <?php _e('Refresh Statistics', 'kata-sharesocial'); ?>
                </button>
                <button type="button" class="button button-secondary" id="clear-old-data">
                    <span class="dashicons dashicons-trash"></span>
                    <?php _e('Clear Old Data (90+ days)', 'kata-sharesocial'); ?>
                </button>
                <button type="button" class="button button-link-delete" id="reset-all-data">
                    <?php _e('Reset All Data', 'kata-sharesocial'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.kata-sharesocial-analytics {
    margin: 20px 0;
}

.analytics-overview {
    margin-bottom: 30px;
}

.overview-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.overview-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-icon {
    font-size: 48px;
    color: #0073aa;
}

.card-content h3 {
    margin: 0 0 5px 0;
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.card-content p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.analytics-charts {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.chart-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.chart-container h3 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #333;
}

.platform-statistics {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.stats-table-wrapper {
    overflow-x: auto;
}

.percentage-bar {
    position: relative;
    background: #f0f0f0;
    height: 20px;
    border-radius: 10px;
    overflow: hidden;
    min-width: 100px;
}

.percentage-fill {
    background: linear-gradient(90deg, #0073aa, #00a0d2);
    height: 100%;
    transition: width 0.3s ease;
}

.percentage-bar span {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12px;
    font-weight: bold;
    color: #333;
    text-shadow: 1px 1px 1px rgba(255,255,255,0.8);
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}

.top-posts {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.top-post-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f0;
}

.top-post-item:last-child {
    border-bottom: none;
}

.post-info h4 {
    margin: 0 0 5px 0;
}

.post-info h4 a {
    text-decoration: none;
    color: #0073aa;
}

.post-meta {
    margin: 0;
    color: #666;
    font-size: 13px;
}

.post-shares {
    text-align: right;
}

.share-count {
    margin-bottom: 5px;
}

.share-count strong {
    font-size: 18px;
    color: #333;
}

.share-count span {
    color: #666;
    font-size: 12px;
}

.platform-breakdown {
    display: flex;
    gap: 8px;
}

.platform-share {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 12px;
    color: #666;
}

.analytics-export,
.data-management {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.export-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    margin: 15px 0;
}

.export-actions,
.management-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.no-data {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

@media (max-width: 768px) {
    .chart-row {
        grid-template-columns: 1fr;
    }
    
    .top-post-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .post-shares {
        text-align: left;
        width: 100%;
    }
    
    .export-actions,
    .management-actions {
        flex-direction: column;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Initialize charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initPlatformChart();
        initTimeChart();
    }
    
    // Export functionality
    $('#export-csv').on('click', function() {
        exportData('csv');
    });
    
    $('#export-json').on('click', function() {
        exportData('json');
    });
    
    // Data management
    $('#refresh-stats').on('click', function() {
        refreshStatistics();
    });
    
    $('#clear-old-data').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to clear data older than 90 days?", "kata-sharesocial"); ?>')) {
            clearOldData();
        }
    });
    
    $('#reset-all-data').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to reset ALL analytics data? This action cannot be undone.", "kata-sharesocial"); ?>')) {
            resetAllData();
        }
    });
    
    function initPlatformChart() {
        var ctx = document.getElementById('platformChart').getContext('2d');
        var platformChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($platform_stats ?? array())); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($platform_stats ?? array(), 'shares')); ?>,
                    backgroundColor: [
                        '#3b5998', '#1da1f2', '#0077b5', '#0088cc', '#25d366'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    function initTimeChart() {
        var ctx = document.getElementById('timeChart').getContext('2d');
        var timeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [], // Will be populated with actual date data
                datasets: [{
                    label: 'Shares',
                    data: [],
                    borderColor: '#0073aa',
                    backgroundColor: 'rgba(0, 115, 170, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    function exportData(format) {
        var options = {
            overview: $('#export-overview').is(':checked'),
            platforms: $('#export-platforms').is(':checked'),
            posts: $('#export-posts').is(':checked'),
            timeline: $('#export-timeline').is(':checked')
        };
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_sharesocial_export_analytics',
                format: format,
                options: options,
                nonce: '<?php echo wp_create_nonce("kata_sharesocial_analytics"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    // Trigger download
                    var link = document.createElement('a');
                    link.href = 'data:text/' + format + ';charset=utf-8,' + encodeURIComponent(response.data);
                    link.download = 'kata-sharesocial-analytics.' + format;
                    link.click();
                } else {
                    alert('<?php _e("Export failed. Please try again.", "kata-sharesocial"); ?>');
                }
            }
        });
    }
    
    function refreshStatistics() {
        $('#refresh-stats').prop('disabled', true).text('<?php _e("Refreshing...", "kata-sharesocial"); ?>');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_sharesocial_refresh_stats',
                nonce: '<?php echo wp_create_nonce("kata_sharesocial_analytics"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('<?php _e("Failed to refresh statistics.", "kata-sharesocial"); ?>');
                }
            },
            complete: function() {
                $('#refresh-stats').prop('disabled', false).html('<span class="dashicons dashicons-update"></span> <?php _e("Refresh Statistics", "kata-sharesocial"); ?>');
            }
        });
    }
    
    function clearOldData() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_sharesocial_clear_old_data',
                nonce: '<?php echo wp_create_nonce("kata_sharesocial_analytics"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e("Old data cleared successfully.", "kata-sharesocial"); ?>');
                    location.reload();
                } else {
                    alert('<?php _e("Failed to clear old data.", "kata-sharesocial"); ?>');
                }
            }
        });
    }
    
    function resetAllData() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_sharesocial_reset_all_data',
                nonce: '<?php echo wp_create_nonce("kata_sharesocial_analytics"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    alert('<?php _e("All data reset successfully.", "kata-sharesocial"); ?>');
                    location.reload();
                } else {
                    alert('<?php _e("Failed to reset data.", "kata-sharesocial"); ?>');
                }
            }
        });
    }
});
</script>
