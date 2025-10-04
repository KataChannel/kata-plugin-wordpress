<?php
/**
 * Dashboard Page
 * 
 * @package KATA_SEO_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

$statistics = new KATA_SEO_Statistics();
$overview = $statistics->get_overview();
$validation_report = $statistics->get_validation_report();
?>

<div class="wrap kata-seo-dashboard">
    <h1><?php _e('KATA SEO Manager Dashboard', 'kata-seo-manager'); ?></h1>
    
    <div class="kata-dashboard-grid">
        <!-- Stats Cards -->
        <div class="kata-stats-cards">
            <div class="kata-stat-card">
                <div class="stat-icon dashicons dashicons-code-standards"></div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo esc_html($overview['total_schemas']); ?></div>
                    <div class="stat-label"><?php _e('Total Schemas', 'kata-seo-manager'); ?></div>
                </div>
            </div>
            
            <div class="kata-stat-card active">
                <div class="stat-icon dashicons dashicons-yes-alt"></div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo esc_html($overview['active_schemas']); ?></div>
                    <div class="stat-label"><?php _e('Active Schemas', 'kata-seo-manager'); ?></div>
                </div>
            </div>
            
            <div class="kata-stat-card validation">
                <div class="stat-icon dashicons dashicons-shield-alt"></div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo esc_html($overview['validation']['pass_rate']); ?>%</div>
                    <div class="stat-label"><?php _e('Validation Pass Rate', 'kata-seo-manager'); ?></div>
                </div>
            </div>
            
            <div class="kata-stat-card">
                <div class="stat-icon dashicons dashicons-chart-line"></div>
                <div class="stat-content">
                    <div class="stat-value"><?php echo count($overview['schemas_by_type']); ?></div>
                    <div class="stat-label"><?php _e('Schema Types Used', 'kata-seo-manager'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Schema Usage Chart -->
        <div class="kata-dashboard-card">
            <h2><?php _e('Schema Usage by Type', 'kata-seo-manager'); ?></h2>
            <div class="kata-chart-container">
                <canvas id="kata-schema-usage-chart"></canvas>
            </div>
            <script>
                var schemaUsageData = <?php echo json_encode($overview['schemas_by_type']); ?>;
            </script>
        </div>
        
        <!-- Recent Schemas -->
        <div class="kata-dashboard-card">
            <h2><?php _e('Recent Schemas', 'kata-seo-manager'); ?></h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Type', 'kata-seo-manager'); ?></th>
                        <th><?php _e('Post', 'kata-seo-manager'); ?></th>
                        <th><?php _e('Status', 'kata-seo-manager'); ?></th>
                        <th><?php _e('Created', 'kata-seo-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($overview['recent_schemas'])): ?>
                        <?php foreach ($overview['recent_schemas'] as $schema): ?>
                            <tr>
                                <td><strong><?php echo esc_html($schema['schema_type']); ?></strong></td>
                                <td>
                                    <a href="<?php echo get_edit_post_link($schema['post_id']); ?>">
                                        <?php echo esc_html($schema['post_title'] ?? __('Untitled', 'kata-seo-manager')); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($schema['is_active']): ?>
                                        <span class="kata-status-badge active"><?php _e('Active', 'kata-seo-manager'); ?></span>
                                    <?php else: ?>
                                        <span class="kata-status-badge inactive"><?php _e('Inactive', 'kata-seo-manager'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($schema['created_at']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4"><?php _e('No schemas found. Start by adding schema to your posts.', 'kata-seo-manager'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Validation Report -->
        <div class="kata-dashboard-card">
            <h2><?php _e('Validation Report', 'kata-seo-manager'); ?></h2>
            <div class="kata-validation-summary">
                <div class="validation-stat valid">
                    <span class="count"><?php echo esc_html($validation_report['valid']); ?></span>
                    <span class="label"><?php _e('Valid', 'kata-seo-manager'); ?></span>
                </div>
                <div class="validation-stat invalid">
                    <span class="count"><?php echo esc_html($validation_report['invalid']); ?></span>
                    <span class="label"><?php _e('Invalid', 'kata-seo-manager'); ?></span>
                </div>
                <div class="validation-stat warnings">
                    <span class="count"><?php echo esc_html($validation_report['with_warnings']); ?></span>
                    <span class="label"><?php _e('With Warnings', 'kata-seo-manager'); ?></span>
                </div>
            </div>
            
            <?php if (!empty($validation_report['recent_errors'])): ?>
                <h3><?php _e('Recent Errors', 'kata-seo-manager'); ?></h3>
                <ul class="kata-error-list">
                    <?php foreach (array_slice($validation_report['recent_errors'], 0, 5) as $error): ?>
                        <li>
                            <strong><?php echo esc_html($error['schema_type']); ?></strong>: 
                            <?php echo esc_html($error['post_title'] ?? __('Unknown post', 'kata-seo-manager')); ?>
                            <span class="error-time"><?php echo esc_html(human_time_diff(strtotime($error['validated_at']))); ?> ago</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="kata-dashboard-card">
            <h2><?php _e('Quick Actions', 'kata-seo-manager'); ?></h2>
            <div class="kata-quick-actions">
                <a href="<?php echo admin_url('admin.php?page=kata-seo-schema-types'); ?>" class="button button-primary button-hero">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php _e('Manage Schema Types', 'kata-seo-manager'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=kata-seo-statistics'); ?>" class="button button-secondary button-hero">
                    <span class="dashicons dashicons-chart-bar"></span>
                    <?php _e('View Statistics', 'kata-seo-manager'); ?>
                </a>
                <a href="<?php echo admin_url('admin.php?page=kata-seo-settings'); ?>" class="button button-secondary button-hero">
                    <span class="dashicons dashicons-admin-settings"></span>
                    <?php _e('Settings', 'kata-seo-manager'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.kata-seo-dashboard {
    margin: 20px 20px 0 0;
}
.kata-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.kata-stats-cards {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}
.kata-stat-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}
.kata-stat-card .stat-icon {
    font-size: 40px;
    color: #2271b1;
}
.kata-stat-card.active .stat-icon {
    color: #46b450;
}
.kata-stat-card.validation .stat-icon {
    color: #f0b849;
}
.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #1d2327;
}
.stat-label {
    font-size: 14px;
    color: #646970;
}
.kata-dashboard-card {
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
}
.kata-dashboard-card h2 {
    margin-top: 0;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}
.kata-chart-container {
    padding: 20px 0;
    min-height: 300px;
}
.kata-status-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}
.kata-status-badge.active {
    background: #d4edda;
    color: #155724;
}
.kata-status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}
.kata-validation-summary {
    display: flex;
    gap: 20px;
    padding: 20px 0;
}
.validation-stat {
    text-align: center;
    flex: 1;
    padding: 15px;
    border-radius: 4px;
}
.validation-stat.valid {
    background: #d4edda;
}
.validation-stat.invalid {
    background: #f8d7da;
}
.validation-stat.warnings {
    background: #fff3cd;
}
.validation-stat .count {
    display: block;
    font-size: 36px;
    font-weight: bold;
}
.validation-stat .label {
    display: block;
    font-size: 14px;
    margin-top: 5px;
}
.kata-error-list {
    list-style: none;
    padding: 0;
}
.kata-error-list li {
    padding: 10px;
    border-left: 3px solid #dc3232;
    background: #f8d7da;
    margin-bottom: 5px;
}
.error-time {
    float: right;
    color: #646970;
    font-size: 12px;
}
.kata-quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}
.kata-quick-actions .button-hero {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 15px 20px;
}
</style>
