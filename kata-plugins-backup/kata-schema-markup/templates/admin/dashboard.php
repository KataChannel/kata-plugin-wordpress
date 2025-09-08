<?php
/**
 * Admin Dashboard Template
 * 
 * @package KataSchemaMarkup
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Ensure stats array exists and has required keys
if (!isset($stats) || !is_array($stats)) {
    $stats = array(
        'posts_with_schema' => 0,
        'schema_types' => array(),
        'cached_schemas' => 0,
        'recent_posts' => array()
    );
}

// Set defaults for missing keys
$stats = wp_parse_args($stats, array(
    'posts_with_schema' => 0,
    'schema_types' => array(),
    'cached_schemas' => 0,
    'recent_posts' => array()
));

// Chart colors for schema types
$chart_colors = array(
    '#0073aa', '#00a32a', '#d63638', '#ffb900',
    '#826eb4', '#ea4335', '#fbbc04', '#34a853'
);

function get_chart_color_for_type($type, $index = 0) {
    global $chart_colors;
    return isset($chart_colors[$index]) ? $chart_colors[$index] : $chart_colors[0];
}
?>

<div class="wrap kata-schema-dashboard">
    <h1><?php _e('Schema Markup Dashboard', 'kata-schema-markup'); ?></h1>
    
    <!-- Stats Overview -->
    <div class="kata-schema-stats-grid">
        <div class="kata-schema-stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-admin-post"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format(intval($stats['posts_with_schema'])); ?></h3>
                <p><?php _e('Posts with Schema', 'kata-schema-markup'); ?></p>
            </div>
        </div>
        
        <div class="kata-schema-stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-category"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo count((array)$stats['schema_types']); ?></h3>
                <p><?php _e('Schema Types Used', 'kata-schema-markup'); ?></p>
            </div>
        </div>
        
        <div class="kata-schema-stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-performance"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo number_format(intval($stats['cached_schemas'])); ?></h3>
                <p><?php _e('Cached Schemas', 'kata-schema-markup'); ?></p>
            </div>
        </div>
        
        <div class="kata-schema-stat-card">
            <div class="stat-icon">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            <div class="stat-content">
                <h3><?php echo get_option('kata_schema_output_enabled', true) ? __('Active', 'kata-schema-markup') : __('Inactive', 'kata-schema-markup'); ?></h3>
                <p><?php _e('Schema Output', 'kata-schema-markup'); ?></p>
            </div>
        </div>
    </div>
    
    <div class="kata-schema-dashboard-content">
        <div class="kata-schema-main-content">
            <!-- Schema Types Usage Chart -->
            <div class="kata-schema-widget">
                <h2><?php _e('Schema Types Usage', 'kata-schema-markup'); ?></h2>
                
                <?php if (!empty($stats['schema_types'])): ?>
                    <div class="kata-schema-chart">
                        <canvas id="schemaTypesChart" width="400" height="200"></canvas>
                    </div>
                    
                    <div class="kata-schema-chart-legend">
                        <?php foreach ($stats['schema_types'] as $index => $type_data): ?>
                            <div class="legend-item">
                                <span class="legend-color" style="background-color: <?php echo get_chart_color_for_type($type_data->type, $index); ?>"></span>
                                <span class="legend-label"><?php echo esc_html(ucfirst($type_data->type)); ?></span>
                                <span class="legend-count"><?php echo number_format($type_data->count); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="kata-schema-empty-state">
                        <span class="dashicons dashicons-chart-pie"></span>
                        <p><?php _e('No schema data available yet. Start by enabling schema markup on your posts.', 'kata-schema-markup'); ?></p>
                        <a href="<?php echo admin_url('edit.php'); ?>" class="button button-primary">
                            <?php _e('Edit Posts', 'kata-schema-markup'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Recent Activity -->
            <div class="kata-schema-widget">
                <h2><?php _e('Recent Schema Activity', 'kata-schema-markup'); ?></h2>
                
                <?php if (!empty($stats['recent_posts'])): ?>
                    <div class="kata-schema-recent-list">
                        <?php foreach ($stats['recent_posts'] as $recent_post): ?>
                            <div class="recent-item">
                                <div class="recent-icon">
                                    <span class="dashicons dashicons-admin-<?php echo $recent_post->post_type === 'page' ? 'page' : 'post'; ?>"></span>
                                </div>
                                <div class="recent-content">
                                    <h4>
                                        <a href="<?php echo get_edit_post_link($recent_post->ID); ?>">
                                            <?php echo esc_html($recent_post->post_title); ?>
                                        </a>
                                    </h4>
                                    <p class="recent-meta">
                                        <span class="post-type"><?php echo esc_html(ucfirst($recent_post->post_type)); ?></span>
                                        <?php if ($recent_post->schema_type): ?>
                                            <span class="schema-type"><?php echo esc_html(ucfirst($recent_post->schema_type)); ?> Schema</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="recent-actions">
                                    <a href="<?php echo get_edit_post_link($recent_post->ID); ?>" class="button button-small">
                                        <?php _e('Edit', 'kata-schema-markup'); ?>
                                    </a>
                                    <a href="<?php echo get_permalink($recent_post->ID); ?>" class="button button-small" target="_blank">
                                        <?php _e('View', 'kata-schema-markup'); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="kata-schema-widget-footer">
                        <a href="<?php echo admin_url('edit.php?meta_key=_kata_schema_enabled&meta_value=1'); ?>" class="button">
                            <?php _e('View All Posts with Schema', 'kata-schema-markup'); ?>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="kata-schema-empty-state">
                        <span class="dashicons dashicons-clock"></span>
                        <p><?php _e('No recent schema activity found.', 'kata-schema-markup'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="kata-schema-sidebar">
            <!-- Quick Actions -->
            <div class="kata-schema-widget">
                <h3><?php _e('Quick Actions', 'kata-schema-markup'); ?></h3>
                
                <div class="kata-schema-quick-actions">
                    <a href="<?php echo admin_url('admin.php?page=kata-schema-templates'); ?>" class="quick-action-button">
                        <span class="dashicons dashicons-admin-appearance"></span>
                        <span><?php _e('Manage Templates', 'kata-schema-markup'); ?></span>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=kata-schema-validation'); ?>" class="quick-action-button">
                        <span class="dashicons dashicons-yes-alt"></span>
                        <span><?php _e('Validate Schemas', 'kata-schema-markup'); ?></span>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=kata-schema-settings'); ?>" class="quick-action-button">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <span><?php _e('Settings', 'kata-schema-markup'); ?></span>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=kata-schema-tools'); ?>" class="quick-action-button">
                        <span class="dashicons dashicons-admin-tools"></span>
                        <span><?php _e('Tools & Export', 'kata-schema-markup'); ?></span>
                    </a>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="kata-schema-widget">
                <h3><?php _e('System Status', 'kata-schema-markup'); ?></h3>
                
                <div class="kata-schema-status-list">
                    <div class="status-item">
                        <span class="status-label"><?php _e('Schema Output:', 'kata-schema-markup'); ?></span>
                        <span class="status-value <?php echo get_option('kata_schema_output_enabled', true) ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo get_option('kata_schema_output_enabled', true) ? __('Enabled', 'kata-schema-markup') : __('Disabled', 'kata-schema-markup'); ?>
                        </span>
                    </div>
                    
                    <div class="status-item">
                        <span class="status-label"><?php _e('Cache:', 'kata-schema-markup'); ?></span>
                        <span class="status-value <?php echo get_option('kata_schema_cache_enabled', true) ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo get_option('kata_schema_cache_enabled', true) ? __('Enabled', 'kata-schema-markup') : __('Disabled', 'kata-schema-markup'); ?>
                        </span>
                    </div>
                    
                    <div class="status-item">
                        <span class="status-label"><?php _e('Breadcrumbs:', 'kata-schema-markup'); ?></span>
                        <span class="status-value <?php echo get_option('kata_schema_breadcrumb_enabled', false) ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo get_option('kata_schema_breadcrumb_enabled', false) ? __('Enabled', 'kata-schema-markup') : __('Disabled', 'kata-schema-markup'); ?>
                        </span>
                    </div>
                    
                    <div class="status-item">
                        <span class="status-label"><?php _e('Cache Duration:', 'kata-schema-markup'); ?></span>
                        <span class="status-value">
                            <?php echo get_option('kata_schema_cache_duration', 24); ?> <?php _e('hours', 'kata-schema-markup'); ?>
                        </span>
                    </div>
                </div>
                
                <div class="kata-schema-widget-footer">
                    <button type="button" class="button" id="kata-clear-cache">
                        <?php _e('Clear Cache', 'kata-schema-markup'); ?>
                    </button>
                </div>
            </div>
            
            <!-- Latest News & Tips -->
            <div class="kata-schema-widget">
                <h3><?php _e('Schema Tips', 'kata-schema-markup'); ?></h3>
                
                <div class="kata-schema-tips">
                    <div class="tip-item">
                        <h4><?php _e('Use Structured Data Testing Tool', 'kata-schema-markup'); ?></h4>
                        <p><?php _e('Regularly test your schema markup with Google\'s Structured Data Testing Tool to ensure it\'s working correctly.', 'kata-schema-markup'); ?></p>
                    </div>
                    
                    <div class="tip-item">
                        <h4><?php _e('Keep Schema Updated', 'kata-schema-markup'); ?></h4>
                        <p><?php _e('Update your schema markup when you modify content to maintain accuracy and relevance.', 'kata-schema-markup'); ?></p>
                    </div>
                    
                    <div class="tip-item">
                        <h4><?php _e('Monitor Rich Snippets', 'kata-schema-markup'); ?></h4>
                        <p><?php _e('Check Google Search Console to see how your rich snippets are performing in search results.', 'kata-schema-markup'); ?></p>
                    </div>
                </div>
                
                <div class="kata-schema-widget-footer">
                    <a href="https://developers.google.com/search/docs/appearance/structured-data" target="_blank" class="button">
                        <?php _e('Schema.org Documentation', 'kata-schema-markup'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kata-schema-dashboard {
    margin: 20px 20px 0 0;
}

.kata-schema-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kata-schema-stat-card {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.kata-schema-stat-card .stat-icon {
    margin-right: 15px;
    font-size: 36px;
    color: #0073aa;
}

.kata-schema-stat-card .stat-content h3 {
    margin: 0 0 5px;
    font-size: 28px;
    font-weight: 600;
    color: #1d2327;
}

.kata-schema-stat-card .stat-content p {
    margin: 0;
    color: #646970;
    font-size: 14px;
}

.kata-schema-dashboard-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.kata-schema-widget {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.kata-schema-widget h2,
.kata-schema-widget h3 {
    margin: 0;
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f1;
    font-size: 16px;
    font-weight: 600;
    color: #1d2327;
}

.kata-schema-widget h3 {
    font-size: 14px;
    padding: 12px 15px;
}

.kata-schema-chart {
    padding: 20px;
}

.kata-schema-chart-legend {
    padding: 0 20px 20px;
    border-top: 1px solid #f0f0f1;
}

.legend-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f6f7f7;
}

.legend-item:last-child {
    border-bottom: none;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    margin-right: 12px;
}

.legend-label {
    flex: 1;
    font-weight: 500;
}

.legend-count {
    color: #646970;
    font-size: 13px;
}

.kata-schema-empty-state {
    padding: 40px 20px;
    text-align: center;
    color: #646970;
}

.kata-schema-empty-state .dashicons {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.kata-schema-empty-state p {
    margin-bottom: 20px;
    font-size: 15px;
}

.kata-schema-recent-list {
    padding: 0 20px;
}

.recent-item {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f6f7f7;
}

.recent-item:last-child {
    border-bottom: none;
}

.recent-icon {
    margin-right: 15px;
    font-size: 20px;
    color: #0073aa;
}

.recent-content {
    flex: 1;
}

.recent-content h4 {
    margin: 0 0 5px;
    font-size: 14px;
}

.recent-content h4 a {
    text-decoration: none;
    color: #0073aa;
}

.recent-content h4 a:hover {
    color: #005177;
}

.recent-meta {
    margin: 0;
    font-size: 12px;
    color: #646970;
}

.recent-meta .post-type,
.recent-meta .schema-type {
    margin-right: 10px;
    padding: 2px 6px;
    background: #f0f0f1;
    border-radius: 3px;
    font-size: 11px;
}

.recent-actions {
    display: flex;
    gap: 8px;
}

.kata-schema-widget-footer {
    padding: 15px 20px;
    border-top: 1px solid #f0f0f1;
    background: #f9f9f9;
}

.kata-schema-quick-actions {
    padding: 15px;
}

.quick-action-button {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 10px;
    background: #f6f7f7;
    border: 1px solid #dcdcde;
    border-radius: 4px;
    text-decoration: none;
    color: #1d2327;
    transition: all 0.2s ease;
}

.quick-action-button:hover {
    background: #f0f0f1;
    border-color: #c3c4c7;
    color: #1d2327;
}

.quick-action-button .dashicons {
    margin-right: 10px;
    font-size: 18px;
    color: #0073aa;
}

.kata-schema-status-list {
    padding: 15px;
}

.status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f6f7f7;
}

.status-item:last-child {
    border-bottom: none;
}

.status-label {
    font-weight: 500;
    color: #1d2327;
}

.status-value {
    font-size: 13px;
}

.status-active {
    color: #00a32a;
    font-weight: 600;
}

.status-inactive {
    color: #d63638;
    font-weight: 600;
}

.kata-schema-tips {
    padding: 15px;
}

.tip-item {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #f6f7f7;
}

.tip-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.tip-item h4 {
    margin: 0 0 8px;
    font-size: 13px;
    font-weight: 600;
    color: #1d2327;
}

.tip-item p {
    margin: 0;
    font-size: 12px;
    color: #646970;
    line-height: 1.5;
}

@media (max-width: 1200px) {
    .kata-schema-dashboard-content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 782px) {
    .kata-schema-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .kata-schema-stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .kata-schema-stat-card .stat-icon {
        margin-right: 0;
        margin-bottom: 10px;
    }
    
    .recent-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .recent-icon {
        margin-bottom: 10px;
    }
    
    .recent-actions {
        margin-top: 10px;
        width: 100%;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Chart colors
    var chartColors = [
        '#0073aa', '#00a32a', '#d63638', '#ffb900',
        '#826eb4', '#ea4335', '#fbbc04', '#34a853'
    ];
    
    // Initialize chart if data is available
    <?php if (!empty($stats['schema_types'])): ?>
    var ctx = document.getElementById('schemaTypesChart').getContext('2d');
    var chartData = {
        labels: [
            <?php foreach ($stats['schema_types'] as $type_data): ?>
                '<?php echo esc_js(ucfirst($type_data->type)); ?>',
            <?php endforeach; ?>
        ],
        datasets: [{
            data: [
                <?php foreach ($stats['schema_types'] as $type_data): ?>
                    <?php echo intval($type_data->count); ?>,
                <?php endforeach; ?>
            ],
            backgroundColor: chartColors.slice(0, <?php echo count($stats['schema_types']); ?>),
            borderWidth: 0
        }]
    };
    
    // Simple pie chart implementation (fallback if Chart.js not available)
    if (typeof Chart !== 'undefined') {
        new Chart(ctx, {
            type: 'pie',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    } else {
        // Fallback: simple canvas drawing
        drawSimplePieChart(ctx, chartData);
    }
    <?php endif; ?>
    
    // Clear cache button
    $('#kata-clear-cache').on('click', function() {
        var $button = $(this);
        var originalText = $button.text();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kata_clear_schema_cache',
                nonce: '<?php echo wp_create_nonce('kata_schema_admin_nonce'); ?>'
            },
            beforeSend: function() {
                $button.text('<?php _e('Clearing...', 'kata-schema-markup'); ?>').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {
                    $button.text('<?php _e('Cleared!', 'kata-schema-markup'); ?>');
                    // Update cache count
                    $('.kata-schema-stat-card').eq(2).find('h3').text('0');
                    
                    setTimeout(function() {
                        $button.text(originalText).prop('disabled', false);
                    }, 2000);
                } else {
                    alert('<?php _e('Failed to clear cache', 'kata-schema-markup'); ?>');
                    $button.text(originalText).prop('disabled', false);
                }
            },
            error: function() {
                alert('<?php _e('Error occurred', 'kata-schema-markup'); ?>');
                $button.text(originalText).prop('disabled', false);
            }
        });
    });
    
    // Simple pie chart drawing function
    function drawSimplePieChart(ctx, data) {
        var canvas = ctx.canvas;
        var centerX = canvas.width / 2;
        var centerY = canvas.height / 2;
        var radius = Math.min(centerX, centerY) - 10;
        
        var total = data.datasets[0].data.reduce(function(sum, value) {
            return sum + value;
        }, 0);
        
        var currentAngle = -Math.PI / 2; // Start from top
        
        data.datasets[0].data.forEach(function(value, index) {
            var sliceAngle = (value / total) * 2 * Math.PI;
            
            // Draw slice
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
            ctx.closePath();
            ctx.fillStyle = data.datasets[0].backgroundColor[index];
            ctx.fill();
            
            currentAngle += sliceAngle;
        });
    }
});
</script>
