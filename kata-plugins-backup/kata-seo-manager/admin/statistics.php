<?php
/**
 * Statistics Page
 * 
 * Display SEO statistics and analytics
 * 
 * @package KATA_SEO_Manager
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get statistics data
$stats = new KATA_SEO_Statistics();
$all_stats = $stats->get_all_stats();

global $wpdb;
$table_name = $wpdb->prefix . 'kata_seo_schemas';
?>

<div class="wrap kata-seo-statistics">
    <h1><?php _e('KATA SEO Statistics', 'kata-seo-manager'); ?></h1>
    
    <div class="kata-seo-stats-grid">
        <!-- Total Schemas -->
        <div class="kata-stat-box">
            <div class="kata-stat-icon">
                <span class="dashicons dashicons-chart-area"></span>
            </div>
            <div class="kata-stat-content">
                <h3><?php echo esc_html($all_stats['total_schemas'] ?? 0); ?></h3>
                <p><?php _e('Total Schemas', 'kata-seo-manager'); ?></p>
            </div>
        </div>
        
        <!-- Schema Types Used -->
        <div class="kata-stat-box">
            <div class="kata-stat-icon">
                <span class="dashicons dashicons-editor-code"></span>
            </div>
            <div class="kata-stat-content">
                <h3><?php echo esc_html($all_stats['schema_types_count'] ?? 0); ?></h3>
                <p><?php _e('Schema Types Used', 'kata-seo-manager'); ?></p>
            </div>
        </div>
        
        <!-- Posts with Schema -->
        <div class="kata-stat-box">
            <div class="kata-stat-icon">
                <span class="dashicons dashicons-admin-post"></span>
            </div>
            <div class="kata-stat-content">
                <h3><?php echo esc_html($all_stats['posts_with_schema'] ?? 0); ?></h3>
                <p><?php _e('Posts with Schema', 'kata-seo-manager'); ?></p>
            </div>
        </div>
        
        <!-- Last Updated -->
        <div class="kata-stat-box">
            <div class="kata-stat-icon">
                <span class="dashicons dashicons-clock"></span>
            </div>
            <div class="kata-stat-content">
                <h3><?php echo esc_html($all_stats['last_updated'] ?? __('Never', 'kata-seo-manager')); ?></h3>
                <p><?php _e('Last Updated', 'kata-seo-manager'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Schema Distribution -->
    <div class="kata-seo-card">
        <h2><?php _e('Schema Type Distribution', 'kata-seo-manager'); ?></h2>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Schema Type', 'kata-seo-manager'); ?></th>
                    <th><?php _e('Count', 'kata-seo-manager'); ?></th>
                    <th><?php _e('Percentage', 'kata-seo-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $schema_distribution = $wpdb->get_results(
                    "SELECT schema_type, COUNT(*) as count 
                     FROM $table_name 
                     GROUP BY schema_type 
                     ORDER BY count DESC"
                );
                
                $total = $all_stats['total_schemas'] ?? 1;
                
                if ($schema_distribution) {
                    foreach ($schema_distribution as $row) {
                        $percentage = ($row->count / $total) * 100;
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html($row->schema_type); ?></strong></td>
                            <td><?php echo esc_html($row->count); ?></td>
                            <td>
                                <div class="kata-progress-bar">
                                    <div class="kata-progress-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                                </div>
                                <span><?php echo esc_html(number_format($percentage, 1)); ?>%</span>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="3" style="text-align: center;">
                            <?php _e('No schemas found', 'kata-seo-manager'); ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <!-- Recent Schemas -->
    <div class="kata-seo-card">
        <h2><?php _e('Recent Schemas', 'kata-seo-manager'); ?></h2>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'kata-seo-manager'); ?></th>
                    <th><?php _e('Type', 'kata-seo-manager'); ?></th>
                    <th><?php _e('Post', 'kata-seo-manager'); ?></th>
                    <th><?php _e('Created', 'kata-seo-manager'); ?></th>
                    <th><?php _e('Status', 'kata-seo-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent_schemas = $wpdb->get_results(
                    "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT 10"
                );
                
                if ($recent_schemas) {
                    foreach ($recent_schemas as $schema) {
                        $post = get_post($schema->post_id);
                        $post_title = $post ? $post->post_title : __('(No Post)', 'kata-seo-manager');
                        ?>
                        <tr>
                            <td><?php echo esc_html($schema->id); ?></td>
                            <td><span class="kata-badge"><?php echo esc_html($schema->schema_type); ?></span></td>
                            <td>
                                <?php if ($post) : ?>
                                    <a href="<?php echo get_edit_post_link($schema->post_id); ?>">
                                        <?php echo esc_html($post_title); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo esc_html($post_title); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html(mysql2date('Y-m-d H:i', $schema->created_at)); ?></td>
                            <td>
                                <span class="kata-status-active"><?php _e('Active', 'kata-seo-manager'); ?></span>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            <?php _e('No recent schemas found', 'kata-seo-manager'); ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.kata-seo-statistics {
    margin: 20px 20px 20px 0;
}

.kata-seo-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 30px 0;
}

.kata-stat-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.kata-stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kata-stat-icon .dashicons {
    font-size: 30px;
    width: 30px;
    height: 30px;
}

.kata-stat-content h3 {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    color: #333;
}

.kata-stat-content p {
    margin: 5px 0 0 0;
    color: #666;
    font-size: 14px;
}

.kata-seo-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.kata-seo-card h2 {
    margin: 0 0 20px 0;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.kata-progress-bar {
    display: inline-block;
    width: 200px;
    height: 10px;
    background: #f0f0f0;
    border-radius: 5px;
    overflow: hidden;
    margin-right: 10px;
}

.kata-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s ease;
}

.kata-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #667eea;
    color: #fff;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.kata-status-active {
    color: #46b450;
    font-weight: 600;
}
</style>
