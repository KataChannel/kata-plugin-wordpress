<?php
/**
 * Admin Dashboard Template
 * 
 * @package KataShareSocial
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$analytics = KataShareSocial_Analytics::get_instance();
$realtime_stats = $analytics->get_realtime_stats();
?>

<div class="wrap kata-sharesocial-dashboard">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-share" style="font-size: 28px; vertical-align: middle;"></span>
        <?php esc_html_e('Kata ShareSocial Dashboard', 'kata-sharesocial'); ?>
    </h1>
    
    <div class="kata-dashboard-header">
        <div class="kata-stats-cards">
            <div class="kata-stat-card">
                <div class="kata-stat-number"><?php echo esc_html(number_format($stats['total_shares'] ?? 0)); ?></div>
                <div class="kata-stat-label"><?php esc_html_e('Total Shares (30 days)', 'kata-sharesocial'); ?></div>
            </div>
            
            <div class="kata-stat-card">
                                <div class="kata-stat-number"><?php echo esc_html(number_format($realtime_stats['yesterday'] ?? 0)); ?></div>
                <div class="kata-stat-label"><?php esc_html_e('Shares Today', 'kata-sharesocial'); ?></div>
            </div>
            
            <div class="kata-stat-card">
                <div class="kata-stat-number"><?php echo esc_html(number_format($realtime_stats['last_hour'])); ?></div>
                <div class="kata-stat-label"><?php esc_html_e('Last Hour', 'kata-sharesocial'); ?></div>
            </div>
            
            <div class="kata-stat-card">
                <div class="kata-stat-number"><?php echo esc_html(count($stats['platform_stats'] ?? array())); ?></div>
                <div class="kata-stat-label"><?php esc_html_e('Active Platforms', 'kata-sharesocial'); ?></div>
            </div>
        </div>
    </div>
    
    <div class="kata-dashboard-content">
        <div class="kata-dashboard-row">
            <!-- Platform Performance -->
            <div class="kata-dashboard-col kata-col-60">
                <div class="kata-widget">
                    <div class="kata-widget-header">
                        <h3><?php esc_html_e('Platform Performance', 'kata-sharesocial'); ?></h3>
                        <span class="kata-widget-subtitle"><?php esc_html_e('Last 30 days', 'kata-sharesocial'); ?></span>
                    </div>
                    <div class="kata-widget-content">
                        <?php if (!empty($stats['platform_stats'] ?? [])): ?>
                            <div class="kata-platform-chart">
                                <?php foreach ($stats['platform_stats'] ?? [] as $platform_stat): ?>
                                    <?php 
                                    $platforms = KataShareSocial_Platforms::get_instance();
                                    $platform_info = $platforms->get_platform($platform_stat->platform);
                                    $percentage = $stats['total_shares'] > 0 ? (($platform_stat->total_shares ?? 0) / $stats['total_shares']) * 100 : 0;
                                    ?>
                                    <div class="kata-platform-row">
                                        <div class="kata-platform-info">
                                            <i class="<?php echo esc_attr($platform_info['icon'] ?? 'fas fa-share'); ?>" style="color: <?php echo esc_attr($platform_info['color'] ?? '#666'); ?>;"></i>
                                            <span class="kata-platform-name"><?php echo esc_html($platform_info['name'] ?? ucfirst($platform_stat->platform)); ?></span>
                                        </div>
                                        <div class="kata-platform-stats">
                                            <div class="kata-platform-bar">
                                                <div class="kata-platform-fill" style="width: <?php echo esc_attr($percentage); ?>%; background-color: <?php echo esc_attr($platform_info['color'] ?? '#666'); ?>;"></div>
                                            </div>
                                            <span class="kata-platform-count"><?php echo esc_html(number_format($platform_stat->total_shares ?? 0)); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="kata-empty-state">
                                <p><?php esc_html_e('No sharing data available yet. Start sharing your content!', 'kata-sharesocial'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="kata-dashboard-col kata-col-40">
                <div class="kata-widget">
                    <div class="kata-widget-header">
                        <h3><?php esc_html_e('Quick Actions', 'kata-sharesocial'); ?></h3>
                    </div>
                    <div class="kata-widget-content">
                        <div class="kata-quick-actions">
                            <a href="<?php echo admin_url('admin.php?page=kata-sharesocial-settings'); ?>" class="kata-action-button">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <?php esc_html_e('Settings', 'kata-sharesocial'); ?>
                            </a>
                            
                            <a href="<?php echo admin_url('admin.php?page=kata-sharesocial-analytics'); ?>" class="kata-action-button">
                                <span class="dashicons dashicons-chart-area"></span>
                                <?php esc_html_e('Analytics', 'kata-sharesocial'); ?>
                            </a>
                            
                            <a href="<?php echo admin_url('widgets.php'); ?>" class="kata-action-button">
                                <span class="dashicons dashicons-admin-appearance"></span>
                                <?php esc_html_e('Widgets', 'kata-sharesocial'); ?>
                            </a>
                        </div>
                        
                        <?php if (isset($realtime_stats['top_post_today']) && $realtime_stats['top_post_today']): ?>
                            <div class="kata-top-post">
                                <h4><?php esc_html_e('Top Post Today', 'kata-sharesocial'); ?></h4>
                                <div class="kata-post-info">
                                    <strong><?php echo esc_html($realtime_stats['top_post_today']->post_title ?? ''); ?></strong>
                                    <span><?php echo esc_html(sprintf(__('%d shares', 'kata-sharesocial'), $realtime_stats['top_post_today']->shares ?? 0)); ?></span>
                                </div>
                                <a href="<?php echo get_edit_post_link($realtime_stats['top_post_today']->post_id ?? 0); ?>" class="kata-edit-link">
                                    <?php esc_html_e('Edit Post', 'kata-sharesocial'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Performing Posts -->
        <?php if (!empty($stats['top_posts'] ?? [])): ?>
            <div class="kata-widget">
                <div class="kata-widget-header">
                    <h3><?php esc_html_e('Top Performing Posts', 'kata-sharesocial'); ?></h3>
                    <span class="kata-widget-subtitle"><?php esc_html_e('Last 30 days', 'kata-sharesocial'); ?></span>
                </div>
                <div class="kata-widget-content">
                    <div class="kata-top-posts">
                        <?php foreach ($stats['top_posts'] ?? [] as $index => $post): ?>
                            <div class="kata-post-row">
                                <div class="kata-post-rank"><?php echo esc_html($index + 1); ?></div>
                                <div class="kata-post-info">
                                    <div class="kata-post-title">
                                        <a href="<?php echo get_edit_post_link($post->post_id ?? 0); ?>">
                                            <?php echo esc_html($post->post_title ?? ''); ?>
                                        </a>
                                    </div>
                                    <div class="kata-post-meta">
                                        <span><?php echo esc_html(get_post_type($post->post_id ?? 0)); ?></span>
                                        <span><?php echo esc_html(get_the_date('M j, Y', $post->post_id ?? 0)); ?></span>
                                    </div>
                                </div>
                                <div class="kata-post-shares">
                                    <strong><?php echo esc_html(number_format($post->total_shares ?? 0)); ?></strong>
                                    <span><?php esc_html_e('shares', 'kata-sharesocial'); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.kata-sharesocial-dashboard {
    margin-top: 20px;
}

.kata-dashboard-header {
    margin: 20px 0;
}

.kata-stats-cards {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}

.kata-stat-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 6px;
    padding: 20px;
    flex: 1;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.kata-stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #1d2327;
    line-height: 1;
}

.kata-stat-label {
    color: #646970;
    font-size: 13px;
    margin-top: 5px;
}

.kata-dashboard-row {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.kata-dashboard-col {
    flex: 1;
}

.kata-col-60 {
    flex: 0 0 60%;
}

.kata-col-40 {
    flex: 0 0 40%;
}

.kata-widget {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.kata-widget-header {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f0f1;
}

.kata-widget-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.kata-widget-subtitle {
    color: #646970;
    font-size: 12px;
    margin-left: 10px;
}

.kata-widget-content {
    padding: 20px;
}

.kata-platform-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f1;
}

.kata-platform-row:last-child {
    border-bottom: none;
}

.kata-platform-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.kata-platform-info i {
    width: 20px;
    text-align: center;
}

.kata-platform-stats {
    display: flex;
    align-items: center;
    gap: 15px;
    min-width: 150px;
}

.kata-platform-bar {
    background: #f0f0f1;
    height: 8px;
    width: 100px;
    border-radius: 4px;
    overflow: hidden;
}

.kata-platform-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

.kata-quick-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 20px;
}

.kata-action-button {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #f0f0f1;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    text-decoration: none;
    color: #1d2327;
    transition: all 0.2s;
}

.kata-action-button:hover {
    background: #e8e8e9;
    color: #1d2327;
}

.kata-top-post {
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
}

.kata-top-post h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #646970;
}

.kata-post-info {
    margin-bottom: 10px;
}

.kata-post-info strong {
    display: block;
    margin-bottom: 5px;
}

.kata-edit-link {
    font-size: 12px;
    color: #0073aa;
}

.kata-post-row {
    display: flex;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #f0f0f1;
}

.kata-post-row:last-child {
    border-bottom: none;
}

.kata-post-rank {
    font-size: 18px;
    font-weight: bold;
    color: #646970;
    margin-right: 15px;
    width: 30px;
}

.kata-post-info {
    flex: 1;
}

.kata-post-title a {
    color: #1d2327;
    text-decoration: none;
    font-weight: 500;
}

.kata-post-title a:hover {
    color: #0073aa;
}

.kata-post-meta {
    color: #646970;
    font-size: 12px;
    margin-top: 5px;
}

.kata-post-meta span {
    margin-right: 15px;
}

.kata-post-shares {
    text-align: right;
}

.kata-post-shares strong {
    display: block;
    font-size: 16px;
    color: #1d2327;
}

.kata-post-shares span {
    color: #646970;
    font-size: 12px;
}

.kata-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #646970;
}

@media (max-width: 768px) {
    .kata-stats-cards {
        flex-direction: column;
    }
    
    .kata-dashboard-row {
        flex-direction: column;
    }
    
    .kata-col-60,
    .kata-col-40 {
        flex: 1;
    }
    
    .kata-platform-stats {
        min-width: auto;
    }
    
    .kata-post-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .kata-post-shares {
        text-align: left;
    }
}
</style>
